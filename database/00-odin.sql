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

-- WARNING
-- DROP DATABASE odin;
-- DROP USER dbaodin;
-- WARNING

create user dbaodin with password 'gresen';
create database odin owner dbaodin;

\c odin;

create TYPE sp_result AS (
    result boolean,
    errormsg varchar
);

-- get_odin_status
-- This function returns the current running status of odin, any issues with the setup, parameters, running state
-- Input: session key
-- Output: false if any errors/issues were detected
create or replace function get_odin_status(
    ticket varchar(255), 
    OUT status boolean,
    OUT errmsg varchar )
returns record AS $$
declare
    tStatus boolean;
    errmsgs varchar[];
begin
    status = true;
    -- query for checking when the scanner daemon was last connected
    SELECT CASE WHEN to_timestamp(si_value,'YYYY-MM-DD HH24:MI:SS') + interval '1 hour' < NOW() THEN false ELSE true END as status FROM scanner_info WHERE si_name = 'last_slave_activity' into tStatus;
    IF tStatus = false THEN
        RAISE NOTICE 'Last status update too old, check scanner status';
        errmsgs = array_append(errmsgs,'Last status update too old, check scanner status');
        status = false;
    END IF;

    -- query for checking when the scanner daemon was last connected
    SELECT CASE WHEN a.s_value = 'checked' AND b.s_value = '' THEN false ELSE true END as status FROM settings a, settings b WHERE a.s_name = 'email_notification' AND b.s_name = 'email_hostname' into tStatus;
    IF tStatus = false THEN
        RAISE NOTICE 'Incorrect notification configuration, Odin will not be able to send email to users';
        errmsgs = array_append(errmsgs,'Incorrect notification configuration, Odin will not be able to send email to users');
        status = false;
    END IF;

   errmsg = array_to_json(errmsgs);
end;
$$ language plpgsql;
alter function get_odin_status(varchar) owner to dbaodin;

