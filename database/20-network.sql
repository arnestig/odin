\c odin;

create sequence sq_networks_id maxvalue 32700 start with 1;
alter sequence sq_networks_id owner to dbaodin;

create table networks (
        nw_id smallint primary key default nextval('sq_networks_id'),
        nw_base varchar(45) not null,
        nw_cidr numeric(2) not null,
        nw_description varchar(2000) not null
        );
alter table networks owner to dbaodin;

create table hosts (
        host_ip varchar(36) primary key,
        usr_id smallint not null references users default 0,
        nw_id smallint not null references networks,
        host_name varchar(255),
        host_data varchar(45),
        host_description varchar(2000),
        host_leased timestamp null,
        host_lease_expiry timestamp null,
        host_last_seen timestamp null,
        host_last_scanned timestamp null,
        host_last_notified timestamp null,
        token_usr smallint not null references users default 0,
        token_timestamp timestamp null
        );
alter table hosts owner to dbaodin;

-- 'Network' stored procedures
--
-- add_network
create or replace function add_network(
    ticket varchar(255),
    network_base varchar(45),
    cidr numeric(2),
    network_description varchar(2000),
    hosts varchar[] )
returns void as $$
declare
    new_nw_id smallint;
begin
    insert into networks( nw_base, nw_cidr, nw_description ) values( network_base, cidr, network_description );
    select into new_nw_id currval('sq_networks_id');  
    insert into hosts( host_ip, nw_id, usr_id, token_usr ) SELECT *, new_nw_id, 0, 0 FROM unnest(hosts);
end;
$$ language plpgsql;
alter function add_network(varchar,varchar,numeric,varchar,varchar[]) owner to dbaodin;

-- remove_network
create or replace function remove_network(
    ticket varchar(255),
    remove_nw_id smallint )
returns void as $$
begin
    delete from hosts where nw_id = remove_nw_id;
    delete from networks where nw_id = remove_nw_id;
end;
$$ language plpgsql;
alter function remove_network(varchar,smallint) owner to dbaodin;

-- get_networks
create or replace function get_networks(
    ticket varchar(255),
    get_nw_id smallint DEFAULT NULL )
returns SETOF refcursor AS $$
declare
ref1 refcursor;
begin
open ref1 for
    SELECT nw_id, nw_base, nw_cidr, nw_description FROM networks WHERE (get_nw_id IS NULL or nw_id = get_nw_id); 
return next ref1;
end;
$$ language plpgsql;
alter function get_networks(varchar,smallint) owner to dbaodin;

-- update_network
create or replace function update_network(
    ticket varchar(255), 
    networkid smallint,
    networkdescription varchar(2000) )
returns void as $$
begin
    UPDATE networks SET nw_description = networkdescription WHERE nw_id = networkid;
end;
$$ language plpgsql;
alter function update_network(varchar,smallint,varchar) owner to dbaodin;


-- get_hosts
-- mask values: 
-- free (1000), free but seen (0100), taken (0010), taken not seen (0001), show all (1111)
create or replace function get_hosts(
    ticket varchar(255),
    get_nw_id smallint DEFAULT NULL,
    page_offset integer default 0,
    items_per_page integer default 100,
    search_string varchar default NULL,
    search_bit_mask smallint default 0)
returns SETOF refcursor AS $$
declare
    host_not_seen_time_limit smallint;
    ref1 refcursor;
begin
    SELECT s_value from settings WHERE s_name = 'host_not_seen_time_limit' INTO host_not_seen_time_limit;
open ref1 for
    WITH CTE AS (
        SELECT
            hosts.*,
            CASE
                WHEN usr_id <> 0 AND (host_last_seen < NOW() - host_not_seen_time_limit * interval '1 days' OR host_last_seen IS NULL) THEN 8 -- taken but not seen
                WHEN usr_id <> 0 AND host_last_seen > NOW() - host_not_seen_time_limit * interval '1 days' THEN 4 -- red, taken and seen
                WHEN usr_id = 0 AND host_last_seen > NOW() - host_not_seen_time_limit * interval '1 days' THEN 2 -- not taken but seen
                WHEN usr_id = 0 AND (host_last_seen < NOW() - host_not_seen_time_limit * interval '1 days' OR host_last_seen IS NULL) THEN 1 -- not taken, not seen
            END as status FROM hosts )
    SELECT
        h.host_ip,
        h.usr_id,
        h.nw_id,
        h.host_name,
        h.host_data,
        h.host_description,
        h.host_leased,
        h.host_last_seen,
        h.host_last_scanned,
        h.host_last_notified,
        h.host_leased,
        h.host_lease_expiry,
        u.usr_usern,
        u.usr_firstn,
        u.usr_lastn,
        u.usr_email,
        h.status,
        count(*) OVER() as total_rows,
        greatest(0,(count(*) OVER()) - (items_per_page * (page_offset+1))) as remaining_rows,
        ceil(count(*) OVER()::float/items_per_page) as total_pages
    FROM CTE h LEFT OUTER JOIN users u ON (h.usr_id = u.usr_id)
    WHERE
        (get_nw_id IS NULL or nw_id = get_nw_id) AND
        (search_string is NULL or (
            (lower(host_description) ~ ('^' || lower(search_string))) OR
            (lower(host_name) ~ ('^' || lower(search_string))) OR
            (lower(host_data) ~ ('^' || lower(search_string))) OR
            (host_ip ~ ('^' || search_string)))
        ) AND
        (search_bit_mask = 0 OR 0 <> (h.status & search_bit_mask))
    ORDER BY inet(host_ip) LIMIT items_per_page offset(items_per_page * page_offset);
return next ref1;
end;
$$ language plpgsql;
alter function get_hosts(varchar,smallint,integer,integer,varchar,smallint) owner to dbaodin;

-- reserve_host
-- This function will be used to preliminary book hosts for reservation
-- Input:  session key, hosts.host_ip, current users.usr_id
-- Output: false if booking failed, true if successful 
create or replace function reserve_host(
    ticket varchar(255),
    host_to_reserve varchar(36),
    cur_usr_id smallint )
returns boolean as $$
declare
    host_already_reserved text;
begin
    select host_ip into host_already_reserved from hosts where host_ip = host_to_reserve AND token_timestamp > NOW() - interval '10 minutes' AND token_usr != cur_usr_id LIMIT 1;
    IF host_already_reserved <> '' THEN
        RAISE 'Host % already reserved', host_already_reserved USING ERRCODE = '28001';
        RETURN false;
    ELSE
        update hosts SET token_usr=cur_usr_id WHERE host_ip = host_to_reserve;
        update hosts SET token_timestamp = NOW() WHERE token_usr=cur_usr_id;
    END IF;
    RETURN true;
end;
$$ language plpgsql;
alter function reserve_host(varchar,varchar,smallint) owner to dbaodin;

-- lease_host
-- This function leases the host and is called after reserving the host
-- Input: session key, hosts.host_ip, current users.usr_id, new host description
-- Output: false if no reserved host was found, true if successful 
create or replace function lease_host(
    ticket varchar(255),
    host_to_lease varchar(36),
    cur_usr_id smallint,
    host_desc varchar(2000) default NULL)
returns boolean as $$
declare
    host_already_reserved text;
    max_lease_time smallint;
begin
    SELECT s_value from settings WHERE s_name = 'host_max_lease_time' INTO max_lease_time;
    select host_ip into host_already_reserved from hosts where host_ip = host_to_lease AND token_timestamp > NOW() - interval '10 minutes' AND token_usr != cur_usr_id LIMIT 1;
    IF host_already_reserved <> '' THEN
        RAISE 'Host % already reserved by ...', host_already_reserved USING ERRCODE = '28001';
        RETURN false;
    ELSE
        UPDATE hosts
        SET
            usr_id = cur_usr_id,
            host_leased = now(),
            host_lease_expiry = NOW() + max_lease_time * interval '1 days',
            host_description = host_desc,
            token_timestamp = NULL,
            token_usr = DEFAULT
        WHERE
            host_ip = host_to_lease AND
            token_usr = cur_usr_id AND
            token_timestamp > NOW() - interval '10 minutes';
    END IF;
    RETURN true;
end;
$$ language plpgsql;
alter function lease_host(varchar,varchar,smallint,varchar) owner to dbaodin;

