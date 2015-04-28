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
        usr_email varchar(128)
        );
create unique index uni_users_usern on users(usr_usern);
insert into users (usr_id,usr_usern,usr_pwd,usr_email)values(0,'admin','','');

create sequence sq_networks_id maxvalue 32700 start with 1;
create table networks (
        nw_id smallint primary key default nextval('sq_networks_id'),
        nw_base varchar(45) not null,
        nw_cidr numeric(2) not null
        );

create table hosts (
        hostid varchar(36) primary key, -- ip?
        usr_id smallint not null references users default 0,
        nw_id smallint not null references networks,
        host_name varchar(255),
        host_data varchar(45),
        -- host_address varchar(15) not null,
        host_description varchar(128),
        host_lease_expiry timestamp null,
        host_last_seen timestamp null,
        host_last_scanned timestamp null
        );

grant all privileges on all tables in schema public to dbaodin;
grant usage, select on sequence sq_networks_id to dbaodin;
grant usage, select on sequence sq_users_id to dbaodin;
