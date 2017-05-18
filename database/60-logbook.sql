/*
   Odin - IP plan management and tracker
   Copyright (C) 2015-2017  Tobias Eliasson <arnestig@gmail.com>
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

create sequence sq_logbook_id;
alter sequence sq_logbook_id owner to dbaodin;

create table logbook (
        lb_id integer primary key default nextval('sq_logbook_id'),
        lb_host_ip varchar(36),
        lb_usr_id smallint not null references users default 0,
        lb_time timestamp DEFAULT NOW(),
        lb_text text
        );
alter table logbook owner to dbaodin;

-- 'Logbook' stored procedures
--
-- get_entry_by_host
create or replace function get_entry_by_host(
    ticket varchar(255),
    host_ip varchar(36) )
returns SETOF refcursor AS $$
declare
    ref1 refcursor;
begin
open ref1 for
    SELECT
        u.usr_usern,
        to_char(l.lb_time, 'YYYY-MM-DD HH24:MI:SS') as lb_time,
        l.lb_text
    FROM
        logbook l
    LEFT OUTER JOIN
        users u
    ON
        l.lb_usr_id = u.usr_id
    WHERE
        l.lb_host_ip = host_ip
    ORDER BY
        l.lb_time
    DESC;
--    SELECT
--        lb_usr_id,
--        to_char(lb_time, 'YYYY-MM-DD HH24:MI:SS') as lb_time,
--        lb_text
--    FROM logbook
--    WHERE
--        lb_host_ip = host_ip
--    ORDER BY lb_time;
return next ref1;
end;
$$ language plpgsql;
alter function get_entry_by_host(varchar,varchar) owner to dbaodin;

-- get_entry_by_user
create or replace function get_entry_by_user(
    ticket varchar(255),
    usr_id smallint )
returns SETOF refcursor AS $$
declare
    ref1 refcursor;
begin
    SELECT 
        lb_host_ip,
        to_char(lb_time, 'YYYY-MM-DD HH24:MI:SS') as lb_time,
        lb_text
    FROM logbook
    WHERE
        lb_usr_id = usr_id
    ORDER BY lb_time;
return next ref1;
end;
$$ language plpgsql;
alter function get_entry_by_user(varchar,smallint) owner to dbaodin;

-- add_log_entry
create or replace function add_log_entry(
    ticket varchar(255),
    usr_id smallint,
    host_ip varchar(36),
    log_details text )
returns void AS $$
begin
    INSERT INTO logbook( lb_host_ip, lb_usr_id, lb_text ) VALUES ( host_ip, usr_id, log_details );
end;
$$ language plpgsql;
alter function add_log_entry(varchar,smallint,varchar,text) owner to dbaodin;

