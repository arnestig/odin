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
