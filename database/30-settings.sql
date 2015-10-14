\c odin;

-- To be done:
-- add a variable data type so that we can add different data values for different settings
-- For example:
-- email_notification should be a boolean (true/false)
-- email_notification_type could be an enum of a different selected types (SMTP,IMAP)
-- email_hostname should be a varchar
-- email_port should be an int
-- 
-- also, settings could be added to groups...
--
-- perhaps this is not needed, investigation pending

create table settings (
        s_name varchar not null primary key,
        s_value varchar not null
        );
alter table settings owner to dbaodin;

-- 'Settings' stored procedures
--
-- update_setting
create or replace function update_setting(
   setting_name varchar(45),
   setting_value varchar)
returns void as $$
declare
begin
    update settings SET s_value = setting_value WHERE s_name = setting_name;
end;
$$ language plpgsql;
alter function update_setting(varchar,varchar) owner to dbaodin;

-- get_setting_value
create or replace function get_setting_value(
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
alter function get_setting_value(varchar) owner to dbaodin;

-- get_settings
create or replace function get_settings()
returns SETOF refcursor AS $$
declare
ref1 refcursor;
begin
open ref1 for
    SELECT s_name, s_value FROM settings;
return next ref1;
end;
$$ language plpgsql;
alter function get_settings() owner to dbaodin;

-- add all odin settings here
insert into settings( s_name, s_value ) values( 'email_notification', '1' );
insert into settings( s_name, s_value ) values( 'email_notification_type', 'smtp' );
insert into settings( s_name, s_value ) values( 'email_hostname', '' );
insert into settings( s_name, s_value ) values( 'email_port', '25' );
