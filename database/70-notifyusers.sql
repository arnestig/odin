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

create sequence sq_notify_id;
alter sequence sq_notify_id owner to dbaodin;

create table notifyusers (
        nu_id integer primary key default nextval('sq_notify_id'),
        nu_sent_by_id smallint not null references users default 0,
        nu_usr_id smallint not null references users default 0,
        nu_notification_sent boolean default false,
        nu_subject text,
        nu_message text
        );
alter table notifyusers owner to dbaodin;

-- 'Notifyusers' stored procedures
--
-- notify_user
create or replace function notify_user(
    ticket varchar(255),
    usr_id smallint,
    subject text,
    message text,
    from_usr_id smallint default 0)
returns void AS $$
begin
    IF usr_id > 1 THEN
        INSERT INTO notifyusers( nu_usr_id, nu_sent_by_id, nu_subject, nu_message ) VALUES ( usr_id, from_usr_id, subject, message );
    END IF;
end;
$$ language plpgsql;
alter function notify_user(varchar,smallint,text,text,smallint) owner to dbaodin;



-- get_notifications
create or replace function get_notifications()
returns SETOF refcursor AS $$
declare
ref1 refcursor;
begin
open ref1 for
    SELECT
        su.usr_usrn,
        su.usr_firstn,
        su.usr_lastn,
        su.usr_email,
        n.nu_id,
        n.nu_message,
        n.nu_subject,
        ru.usr_usrn,
        ru.usr_firstn,
        ru.usr_lastn,
        ru.usr_email
    FROM
        users su 
    LEFT OUTER JOIN
        notifyusers n
    ON
        (su.usr_id = n.nu_sent_by_id)
    FROM
        users ru
    LEFT OUTER JOIN
        notifyusers n
    ON
        (ru.usr_id = n.nu_usr_id)
    WHERE
        (n.nu_notification_sent = false);

return next ref1;
end;
$$ language plpgsql;
alter function get_notifications() owner to dbaodin;



-- notification_sent
create or replace function notification_sent(
    notification_id integer)
returns void AS $$
begin
    UPDATE notifyusers SET nu_notification_sent = true WHERE nu_id = notification_id;
end;
$$ language plpgsql;
alter function notification_sent(integer) owner to dbaodin; 
