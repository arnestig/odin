/*
   Odin - IP plan management and tracker
   Copyright (C) 2015-2016  Tobias Eliasson <arnestig@gmail.com>
                            Jonas Berglund <jonas.jberglund@gmail.com>
                            Martin Rydin <martin.rydin@gmail.com>

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License along
   with this program; if not, write to the Free Software Foundation, Inc.,
   51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.

*/

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
        server_gen_pwd boolean default false,
        usr_email varchar(128),
        usr_privileges smallint default 0,
        usr_session_key varchar(255),
        usr_last_touch timestamp,
        usr_is_deleted boolean default false
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
    serverpwd boolean,
    firstname varchar(45), 
    lastname varchar(45), 
    email varchar(128),
    OUT status boolean,
    OUT errmsg varchar,
    OUT new_usr_id smallint )
returns record AS $$
begin
    status = true;
    --perform isSessionValid(ticket);
    insert into users( usr_usern, usr_pwd, server_gen_pwd, usr_firstn, usr_lastn, usr_email ) values( username, crypt( password, gen_salt('md5') ), serverpwd, firstname, lastname, email ) RETURNING usr_id into new_usr_id;
    EXCEPTION WHEN unique_violation THEN
        RAISE NOTICE 'Username already in use';
        errmsg = 'Username already in use';
        status = false;
        new_usr_id = 0;
end;
$$ language plpgsql;
alter function add_user(varchar,varchar,varchar,boolean,varchar,varchar,varchar) owner to dbaodin;

-- get_users
-- Returns all users if get_usr_id is NULL or specified user otherwise
create or replace function get_users(
    ticket varchar(255), 
    get_usr_id smallint DEFAULT NULL )
returns SETOF refcursor AS $$
declare
ref1 refcursor;
begin
open ref1 for
    SELECT usr_id, usr_usern, usr_lastn, usr_firstn, usr_email, usr_privileges, server_gen_pwd FROM users WHERE usr_id > 0 and (get_usr_id IS NULL or usr_id = get_usr_id) AND usr_is_deleted = false ORDER BY usr_lastn, usr_firstn, usr_usern;
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
    serverpwd boolean,
    firstname varchar(45),
    lastname varchar(45),
    email varchar(128),
    OUT status boolean,
    OUT errmsg varchar)
returns record as $$
begin
    status = true;
    UPDATE users SET usr_usern = username, usr_pwd = crypt( password, gen_salt( 'md5' ) ), server_gen_pwd = serverpwd, usr_firstn = firstname, usr_lastn = lastname, usr_email = email WHERE usr_id = userid;
    EXCEPTION WHEN unique_violation THEN
        RAISE NOTICE 'Username already in use';
        errmsg = 'Username already in use';
        status = false;
end;
$$ language plpgsql;
alter function update_user(varchar,smallint,varchar,varchar,boolean,varchar,varchar,varchar) owner to dbaodin;

-- ADMIN update_user
create or replace function admin_update_user(
    ticket varchar(255), 
    userid smallint,
    username varchar(45),
    firstname varchar(45),
    lastname varchar(45),
    email varchar(128),
    privileges smallint,
    OUT status boolean,
    OUT errmsg varchar)
returns record as $$
begin
    status = true;
    UPDATE users SET usr_usern = username, usr_firstn = firstname, usr_lastn = lastname, usr_email = email, usr_privileges = privileges WHERE usr_id = userid;
    EXCEPTION WHEN unique_violation THEN
        RAISE NOTICE 'Username already in use';
        errmsg = 'Username already in use';
        status = false;
end;
$$ language plpgsql;
alter function admin_update_user(varchar,smallint,varchar,varchar,varchar,varchar,smallint) owner to dbaodin;


-- remove_user
create or replace function remove_user(
    ticket varchar(255), 
    userid smallint )
returns void as $$
declare
    host_result record;
begin
    FOR host_result IN select host_ip FROM hosts WHERE usr_id = userid LOOP
        PERFORM add_log_entry( ticket, userid, host_result.host_ip, 'User deleted, host lease terminated' );
    END LOOP;
    UPDATE hosts 
    SET 
    usr_id = 0, 
    host_name = '', 
    host_data = '', 
    host_description = '',
    host_lease_expiry = null,
    host_last_notified = null
    WHERE usr_id = userid;
    
    UPDATE users SET usr_is_deleted = true WHERE usr_id = userid;
end;
$$ language plpgsql;
alter function remove_user(varchar,smallint) owner to dbaodin;

-- Create our administrator
insert into users( usr_usern, usr_pwd, usr_firstn, usr_lastn, usr_email, usr_privileges, server_gen_pwd ) values( 'admin', crypt( 'admin', gen_salt('md5') ), 'Odin', 'Administrator', '', 2, true );
