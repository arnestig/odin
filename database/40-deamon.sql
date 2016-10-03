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

-- get_hosts_to_scan
create or replace function get_hosts_to_scan()
returns SETOF refcursor AS $$
declare
    host_scan_interval smallint;
    ref1 refcursor;
begin
    SELECT s_value FROM settings WHERE s_name = 'host_scan_interval' INTO host_scan_interval;
open ref1 for
    SELECT host_ip FROM hosts WHERE host_last_scanned IS NULL or host_last_scanned < (NOW() - host_scan_interval * interval '1 minute') ORDER BY random();
return next ref1;
end;
$$ language plpgsql;
alter function get_hosts_to_scan() owner to dbaodin;

-- update_host_status
create or replace function update_host_status(
   update_host_ip varchar(36),
   update_host_online boolean,
   update_host_timestamp timestamp )
returns void as $$
declare
begin
    UPDATE hosts SET 
        host_last_scanned = update_host_timestamp, 
        host_last_seen = ( 
            CASE WHEN update_host_online = true THEN 
                update_host_timestamp
            ELSE
                host_last_seen
            END
        ) WHERE host_ip = update_host_ip;
end;
$$ language plpgsql;
alter function update_host_status(varchar,boolean,timestamp) owner to dbaodin;

-- get_emails_to_send
create or replace function get_emails_to_send()
returns SETOF refcursor AS $$
declare
    ref1 refcursor;
begin
open ref1 for
    SELECT nu.nu_id, nu.nu_message, u.usr_email, u.usr_firstn, u.usr_lastn, u.usr_usern
    FROM notifyusers nu LEFT OUTER JOIN users u ON(u.usr_id = nu.nu_usr_id)
    WHERE
        nu.nu_notification_sent = false;
return next ref1;
end;
$$ language plpgsql;
alter function get_emails_to_send() owner to dbaodin;

-- remove_nu_message
create or replace function remove_notifyuser_message(
    nu_msg_id integer )
returns void as $$
begin
    UPDATE notifyusers SET nu_notification_sent = true WHERE nu_id = nu_msg_id;
end;
$$ language plpgsql;
alter function remove_notifyuser_message(integer) owner to dbaodin;
