\c odin;

create sequence sq_networks_id maxvalue 32700 start with 1;
alter sequence sq_networks_id owner to dbaodin;

create table networks (
        nw_id smallint primary key default nextval('sq_networks_id'),
        nw_base varchar(45) not null,
        nw_cidr numeric(2) not null
        );
alter table networks owner to dbaodin;

create table hosts (
        host_ip varchar(36) primary key,
        usr_id smallint not null references users default 0,
        nw_id smallint not null references networks,
        host_name varchar(255),
        host_data varchar(45),
        host_description varchar(128),
        host_lease_expiry timestamp null,
        host_last_seen timestamp null,
        host_last_scanned timestamp null,
        host_last_notified timestamp null
        );
alter table hosts owner to dbaodin;

-- 'Network' stored procedures
--
-- add_network
create or replace function add_network(
    ticket varchar(255),
    network_base varchar(45),
    cidr numeric(2),
    hosts varchar[] )
returns void as $$
declare
    new_nw_id smallint;
    admin_id smallint;
begin
    insert into networks( nw_base, nw_cidr ) values( network_base, cidr );
    select into new_nw_id currval('sq_networks_id');  
    select usr_id from users where usr_usern = 'admin' into admin_id;
    insert into hosts( host_ip, nw_id, usr_id ) SELECT *, new_nw_id, admin_id FROM unnest(hosts);
end;
$$ language plpgsql;
alter function add_network(varchar,varchar,numeric,varchar[]) owner to dbaodin;

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
    SELECT nw_id, nw_base, nw_cidr FROM networks WHERE (get_nw_id IS NULL or nw_id = get_nw_id); 
return next ref1;
end;
$$ language plpgsql;
alter function get_networks(varchar,smallint) owner to dbaodin;

-- get_hosts
create or replace function get_hosts(
    ticket varchar(255),
    get_nw_id smallint DEFAULT NULL,
    page_offset integer default 0,
    items_per_page integer default 100,
    search_string varchar default NULL)
returns SETOF refcursor AS $$
declare
ref1 refcursor;
begin
open ref1 for
    SELECT
        host_ip,
        usr_id,
        nw_id,
        host_name,
        host_data,
        host_description,
        host_lease_expiry,
        host_last_seen,
        host_last_scanned,
        count(*) OVER() as total_rows,
        greatest(0,(count(*) OVER()) - (items_per_page * (page_offset+1))) as remaining_rows,
        ceil(count(*) OVER()::float/items_per_page) as total_pages
    FROM hosts
    WHERE
        (get_nw_id IS NULL or nw_id = get_nw_id) AND
        (search_string is NULL or (
            (lower(host_description) ~ ('^' || lower(search_string))) OR
            (lower(host_name) ~ ('^' || lower(search_string))) OR
            (lower(host_data) ~ ('^' || lower(search_string))) OR
            (host_ip ~ ('^' || search_string)))
        )
    ORDER BY inet(host_ip) LIMIT items_per_page offset(items_per_page * page_offset);
return next ref1;
end;
$$ language plpgsql;
alter function get_hosts(varchar,smallint,integer,integer,varchar) owner to dbaodin;
