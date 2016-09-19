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

CREATE TYPE setting_type AS ENUM ('text', 'bool', 'choice', 'number', 'email', 'checkbox', 'file');

create sequence sq_settings_id maxvalue 32700 start with 1;
alter sequence sq_settings_id owner to dbaodin;

create table settings_group (
        sg_name varchar not null primary key,
        sg_value varchar not null,
        sg_description varchar not null
        );
alter table settings_group owner to dbaodin;

create table settings (
        s_name varchar not null primary key,
        s_id smallint default nextval('sq_settings_id'),
        sg_name varchar not null references settings_group,
        s_value varchar not null,
        s_type setting_type not null,
        s_fullname varchar not null,
        s_description varchar not null
        );
alter table settings owner to dbaodin;

-- 'Settings' stored procedures
--
-- update_setting
create or replace function update_setting(
    ticket varchar(255),
    setting_name varchar(45),
    setting_value varchar)
returns void as $$
declare
begin
    update settings SET s_value = setting_value WHERE s_name = setting_name;
end;
$$ language plpgsql;
alter function update_setting(varchar,varchar,varchar) owner to dbaodin;

-- get_setting_value
create or replace function get_setting_value(
    ticket varchar(255),
    setting_name varchar(45) )
returns varchar AS $$
declare
    retval varchar;
begin
    SELECT s_value FROM settings WHERE s_name = setting_name INTO retval;
    return retval;
end;
$$ language plpgsql;
alter function get_setting_value(varchar,varchar) owner to dbaodin;

-- get_settings
create or replace function get_settings(
    ticket varchar(255),
    settingsgroup varchar)
returns SETOF refcursor AS $$
declare
ref1 refcursor;
begin
open ref1 for
    SELECT s_id, s_name, s_type, s_value, s_fullname, s_description FROM settings WHERE sg_name = settingsgroup ORDER BY s_id;
return next ref1;
end;
$$ language plpgsql;
alter function get_settings(varchar,varchar) owner to dbaodin;

-- get_setting_groups
create or replace function get_setting_groups(
    ticket varchar(255))
returns SETOF refcursor AS $$
declare
ref1 refcursor;
begin
open ref1 for
    SELECT * FROM settings_group;
return next ref1;
end;
$$ language plpgsql;
alter function get_setting_groups(varchar) owner to dbaodin;

-- add every odin settings_group here
insert into settings_group( sg_name, sg_value, sg_description )
values( 'odin_generic', 'Odin', 'Odin base configuration settings' );
insert into settings_group( sg_name, sg_value, sg_description )
values( 'notifications', 'Notifications', 'Notification (e-mail) configuration of Odin.' );
insert into settings_group( sg_name, sg_value, sg_description )
values( 'hosts', 'Hosts', 'Settings for hosts, how long the hosts are leased, when expiry emails are sent, host scan interval (slave setting),  etc.' );


-- add all odin settings here
--- Notification settings
insert into settings( sg_name, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'notifications', 'email_notification', 'checkbox', '1', 'Enable notification mails', '' );
insert into settings( sg_name, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'notifications', 'email_notification_type', 'text', 'smtp', 'Mail server type', 'Only SMTP supported for now' );
insert into settings( sg_name, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'notifications', 'email_hostname', 'text', '', 'Mail server hostname', 'Hostname or IP-address' );
insert into settings( sg_name, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'notifications', 'email_port', 'number', '25', 'Mail server port', '' );
insert into settings( sg_name, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'notifications', 'email_sender', 'email', 'no-reply@odin.valhalla', 'Sender email address', 'Most servers will atleast require the domain to be valid' );

--- User signup
insert into settings( sg_name, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'odin_generic', 'allow_user_registration', 'checkbox', '1', 'Allow user registration', 'Allows users to register on the Odin login page. If disabled only administrators will be able to add new users.' );

insert into settings( sg_name, s_name, s_type, s_value, s_fullname, s_description )
values( 'odin_generic', 'logo', 'file', 'iVBORw0KGgoAAAANSUhEUgAAAHYAAAAyCAYAAACJbi9rAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAABCNJREFUeNrsXI1xmzAUVnIZwBuEThA2MJmgeILSCeJMEDIByQROJ7A7AWQC4wnsTmA6QYpy4qpyPElPSDKo+u50tvGPnr7v6b0nGSAkwEtcBQoGEbctZc+btu3admKvU/Y+YcfeAl3Tx6JtZds+BlretuPA8SMndMBEsQdElbXz1MS90vTqhA3kjr3u452FqYoLYTrhsOurA9QfDxo6Dxr9Z23bjOCShutV71jSe6S4bVuE+F0TXApBjdtqePOekYbBWnPmfAChslAgc2ugL56rs8Ex8GPJFZxbeeaUhoxKFPssLZDywWZkZLHPDrkl+/nQvx4jambBqOKCwnakZDMXlnfUSYiqalDpgJTCcPgvLyAsyOUNQGyqQH7NEvov9pywMEfbUhJ2M/a9HOls99z68QTUAd3jV0mlumY2vLDXdD36gCxseDwrfqbh+BriNO6lwTumx0LA5YEbh7DqPUs8U6W0j5hziLwtRs5YLFRsSHpE6hQ9/dCeG7Kf1ySXpBdpQSUiQidhpwKySsvCqthwHCgWzyNEtSGsim25zMNNisoTqzJjbAmLJUVVXGgZZ0tYUS1wFH2pQM4sDKDf3joSFkuKTFzR2tymsATY2hSlNvALiQFjRLl74UhY0RhThLiyDRfbwq4xUTUW7ByZwkaRVJvCZsglWF9clV0028JGmDGgvMBwri0cCks0clQs2Ni4hLAEkzI32Lht0SDbwtr+fRfClsCy5xPXvekNLZpNogZmhEu8C/7omDMWqsLWFjpvRAY5Qk3mD+EYZMI2npLaIGZszsJe1zYXcMQh/Ba9eTMhgyip1cRmBU0RTwPHdfa5neKaBCjlrLkhCOspgrBB2IAgbEAQNiAIGxCEDQjCBmEDgrB6OAU55i3scsLC1j4KWw28n3jq0NC4Gh+FhQYVOSC1cjzuW9/TAS/sAendOognQmgCzFYvha2QOVEHKXD84HDMMRCFKuIRVIQVXRCExTfg+M7hmB+A4z99roqHCKaimjgFNSPweVUnh7M1m4BzORf2FfjcExl3JiF1DuiC5x+OxkptgE4Kf/OpIh4SthKs5UpNcbtb7CyAguXNkaiQ/dSGZ+IZhjYoHiXkpMjqcy9wiGcHM0Vmwyv5j3a9oCvj+Ot51gBZXR6T3W5AdAXf2DP1I2bffoQNnVOgr0Ulbq4EEPYBnX76SP7eywkqQsbkXBruVxrfKxVnqE0bZgHRecX0fg9bYn5bsWa/rROCTdlCK+DvvhVMshzLFxX3hguL3QhRTaBh0Wjls6gyYflY/mVk9VoxQVUJNV3MnJiD0nG8IB1hlsDeSzFiVfGSwFtzHSE1283ZaQjVbYosuX4jpJC0vbP+x/wdV/TqiW7WnyQbIcVACno0vNkC9mHifsUL8u/9e10tHYbErkjAJ/4IMAABVYOjANw6RQAAAABJRU5ErkJggg', 'Custom Odin logo', 'Upload a new logo to be used by Odin. Optimal format width: x px, height: x px.' );

--- Hosts leasing
insert into settings( sg_name, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'hosts', 'host_max_lease_time', 'number', '365', 'Host maximum lease time (days)', 'Number of days a user can lease a host without having to renew the lease.' );
insert into settings( sg_name, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'hosts', 'host_expiry_warning_time', 'number', '30', 'Host expiry warning time (days)', 'Defines when the expiration email is sent to a user.' );
insert into settings( sg_name, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'hosts', 'host_steal_not_seen', 'checkbox', '', 'Allow stealing not seen', 'Allow reservation of addresses that are taken but not seen.' );
insert into settings( sg_name, s_name, s_type, s_value, s_fullname, s_description )
values( 'hosts', 'host_not_seen_time_limit', 'number', '30', 'Host not seen time limit (days)', 'Defines when hosts are considered gone or not seen by the system any more.' );
insert into settings( sg_name, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'hosts', 'host_scan_interval', 'number', '5', 'Host scan interval (minutes)', 'Time between host scans in minutes.' );
