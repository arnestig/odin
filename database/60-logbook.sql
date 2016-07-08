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
    SELECT
        lb_usr_id,
        to_char(lb_time, 'YYYY-MM-DD HH24:MI:SS') as lb_time,
        lb_text
    FROM logbook
    WHERE
        lb_host_ip = host_ip
    ORDER BY lb_time;
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

