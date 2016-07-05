\c odin;

create extension pgcrypto;

create sequence sq_users_id maxvalue 32700 start with 1;
alter sequence sq_users_id owner to dbaodin;


create table users (
        usr_id smallint primary key default nextval('sq_users_id'),
        usr_usern varchar(45) not null,
        usr_lastn varchar(45) null,
        usr_firstn varchar(45) null,
        usr_pwd varchar(100) not null,
        server_gen_pwd smallint default 0,
        usr_email varchar(128),
        usr_privileges smallint default 0,
        usr_session_key varchar(255),
        usr_last_touch timestamp
        );
create unique index uni_users_usern on users(usr_usern);
create unique index uni_usr_session_key on users(usr_session_key);
alter table users owner to dbaodin;

-- add the user nobody (owner of available hosts)
insert into users (usr_id,usr_usern,usr_pwd)values(0,'nobody','null');

-- 'User' stored procedures
--
-- add_user
create or replace function add_user(
    ticket varchar(255), 
    username varchar(45), 
    password varchar(100),
    serverpwd smallint,
    firstname varchar(45), 
    lastname varchar(45), 
    email varchar(128) )
returns void as $$
begin
    --perform isSessionValid(ticket);
    insert into users( usr_usern, usr_pwd, server_gen_pwd, usr_firstn, usr_lastn, usr_email ) values( username, crypt( password, gen_salt('md5') ), serverpwd, firstname, lastname, email );
end;
$$ language plpgsql;
alter function add_user(varchar,varchar,varchar,smallint,varchar,varchar,varchar) owner to dbaodin;

-- get_users
create or replace function get_users(
    ticket varchar(255), 
    get_usr_id smallint DEFAULT NULL )
returns SETOF refcursor AS $$
declare
ref1 refcursor;
begin
open ref1 for
    SELECT usr_id, usr_usern, usr_lastn, usr_firstn, usr_email, server_gen_pwd FROM users WHERE usr_id > 0 and (get_usr_id IS NULL or usr_id = get_usr_id); 
return next ref1;
end;
$$ language plpgsql;
alter function get_users(varchar,smallint) owner to dbaodin;
    
-- update_user
create or replace function update_user(
    ticket varchar(255), 
    userid smallint,
    username varchar(45),
    password varchar(100),
    serverpwd smallint,
    firstname varchar(45),
    lastname varchar(45),
    email varchar(128) )
returns void as $$
begin
    UPDATE users SET usr_usern = username, usr_pwd = crypt( password, gen_salt( 'md5' ) ), server_gen_pwd = serverpwd, usr_firstn = firstname, usr_lastn = lastname, usr_email = email WHERE usr_id = userid;
end;
$$ language plpgsql;
alter function update_user(varchar,smallint,varchar,varchar,smallint,varchar,varchar,varchar) owner to dbaodin;

-- ADMIN update_user
create or replace function admin_update_user(
    ticket varchar(255), 
    userid smallint,
    username varchar(45),
    firstname varchar(45),
    lastname varchar(45),
    email varchar(128),
    privileges smallint )
returns void as $$
begin
    UPDATE users SET usr_usern = username, usr_firstn = firstname, usr_lastn = lastname, usr_email = email, usr_privileges = privileges WHERE usr_id = userid;
end;
$$ language plpgsql;
alter function admin_update_user(varchar,smallint,varchar,varchar,varchar,varchar,smallint) owner to dbaodin;


-- remove_user
create or replace function remove_user(
    ticket varchar(255), 
    userid smallint )
returns void as $$
begin
    UPDATE hosts 
    SET 
    usr_id = 0, 
    host_name = '', 
    host_data = '', 
    host_description = '',
    host_last_seen = null,
    host_lease_expiry = null,
    host_last_notified = null
    WHERE usr_id = userid;
    
    DELETE FROM users WHERE usr_id = userid;
end;
$$ language plpgsql;
alter function remove_user(varchar,smallint) owner to dbaodin;

-- Create our administrator
insert into users( usr_usern, usr_pwd, usr_firstn, usr_lastn, usr_email, usr_privileges ) values( 'admin', crypt( '', gen_salt('md5') ), '', '', '', 2 );
