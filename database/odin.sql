-- WARNING
 DROP DATABASE odin;
 DROP USER dbaodin;
-- WARNING


create user dbaodin with password 'gresen';
create database odin owner dbaodin;

\c odin;

create sequence sq_users_id maxvalue 32700 start with 1;
create table users (
        usr_id smallint primary key default nextval('sq_users_id'),
        usr_usern varchar(45) not null,
        usr_lastn varchar(45) null,
        usr_firstn varchar(45) null,
        usr_pwd varchar(100) not null,
        usr_email varchar(128)),
        usr_session_key varchar(255),
        usr_last_touch timestamp
        );
create unique index uni_users_usern on users(usr_usern);

-- 'User' stored procedures
--
-- add_user
create or replace function add_user(
    username varchar(45), 
    password varchar(100), 
    firstname varchar(45), 
    lastname varchar(45), 
    email varchar(128) )
returns void as $$
begin
    insert into users( usr_usern, usr_pwd, usr_firstn, usr_lastn, usr_email ) values( username, password, firstname, lastname, email );
end;
$$ language plpgsql;

select add_user( 'admin', '', '', '', '' );

create sequence sq_networks_id maxvalue 32700 start with 1;
create table networks (
        nw_id smallint primary key default nextval('sq_networks_id'),
        nw_base varchar(45) not null,
        nw_cidr numeric(2) not null
        );

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

grant all privileges on all tables in schema public to dbaodin;
grant usage, select on sequence sq_networks_id to dbaodin;
grant usage, select on sequence sq_users_id to dbaodin;
