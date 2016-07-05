\c odin;

CREATE TYPE setting_type AS ENUM ('text', 'bool', 'choice', 'number', 'email', 'checkbox');

create sequence sq_settings_id maxvalue 32700 start with 1;
alter sequence sq_settings_id owner to dbaodin;

create table settings (
        s_name varchar not null primary key,
        s_id smallint default nextval('sq_settings_id'),
        s_group_name varchar not null,
        s_group_value varchar not null,
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
returns SETOF refcursor AS $$
declare
ref1 refcursor;
begin
open ref1 for
    SELECT s_value FROM settings WHERE s_name = setting_name; 
return next ref1;
end;
$$ language plpgsql;
alter function get_setting_value(varchar,varchar) owner to dbaodin;

-- get_settings
create or replace function get_settings(
    ticket varchar(255))
returns SETOF refcursor AS $$
declare
ref1 refcursor;
begin
open ref1 for
    SELECT s_group_name, s_group_value, s_name, s_type, s_value, s_fullname, s_description FROM settings ORDER BY s_id;
return next ref1;
end;
$$ language plpgsql;
alter function get_settings(varchar) owner to dbaodin;

-- add all odin settings here
--- Notification settings
insert into settings( s_group_name, s_group_value, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'notifications', 'Notifications', 'email_notification', 'checkbox', '1', 'Enable notification mails', '' );
insert into settings( s_group_name, s_group_value, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'notifications', 'Notifications', 'email_notification_type', 'text', 'smtp', 'Mail server type', 'Only SMTP supported for now' );
insert into settings( s_group_name, s_group_value, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'notifications','Notifications', 'email_hostname', 'text', '', 'Mail server hostname', 'Hostname or IP-address' );
insert into settings( s_group_name, s_group_value, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'notifications', 'Notifications', 'email_port', 'number', '25', 'Mail server port', '' );
insert into settings( s_group_name, s_group_value, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'notifications', 'Notifications', 'email_sender', 'email', 'no-reply@odin.valhalla', 'Sender email address', 'Most servers will atleast require the domain to be valid' );

--- User signup
insert into settings( s_group_name, s_group_value, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'user_registration', 'User registration', 'allow_user_registration', 'checkbox', '1', 'Allow user registration', 'Allows users to register on the Odin login page. If disabled only administrators will be able to add new users.' );

--- Hosts leasing
insert into settings( s_group_name, s_group_value, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'hosts', 'Hosts', 'host_max_lease_time', 'number', '365', 'Host maximum lease time (days)', 'Number of days a user can lease a host without having to renew the lease.' );
insert into settings( s_group_name, s_group_value, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'hosts', 'Hosts', 'host_expiry_warning_time', 'number', '30', 'Host expiry warning time (days)', 'Defines when the expiration email is sent to a user.' );
insert into settings( s_group_name, s_group_value, s_name, s_type, s_value, s_fullname, s_description ) 
values( 'hosts', 'Hosts', 'host_steal_not_seen', 'checkbox', '', 'Allow stealing not seen', 'Allow reservation of addresses that are taken but not seen.' );
insert into settings( s_group_name, s_group_value, s_name, s_type, s_value, s_fullname, s_description )
values( 'hosts', 'Hosts', 'host_not_seen_time_limit', 'number', '30', 'Host not seen time limit (days)', 'Defines when hosts are considered gone or not seen by the system any more.' );
