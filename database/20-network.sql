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
        hostid varchar(36) primary key, -- ip?
        usr_id smallint not null references users default 1,
        nw_id smallint not null references networks,
        host_name varchar(255),
        host_data varchar(45),
        -- host_address varchar(15) not null,
        host_description varchar(128),
        host_lease_expiry timestamp null,
        host_last_seen timestamp null,
        host_last_scanned timestamp null
        );
alter table hosts owner to dbaodin;

-- 'Network' stored procedures
--
-- add_network
create or replace function add_network(
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
    insert into hosts( hostid, nw_id, usr_id ) SELECT *, new_nw_id, admin_id FROM unnest(hosts);

end;
$$ language plpgsql;
alter function add_network(varchar,numeric,varchar[]) owner to dbaodin;

-- get_networks
create or replace function get_networks(
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
alter function get_networks(smallint) owner to dbaodin;

-- get_hosts
create or replace function get_hosts(
    get_host_id varchar(36) DEFAULT NULL )
returns SETOF refcursor AS $$
declare
ref1 refcursor;
begin
open ref1 for
    SELECT hostid, usr_id, host_name, host_data, host_description, host_lease_expiry, host_last_seen, host_last_scanned FROM hosts WHERE (get_host_id IS NULL or hostid = get_host_id); 
return next ref1;
end;
$$ language plpgsql;
alter function get_hosts(varchar) owner to dbaodin;

