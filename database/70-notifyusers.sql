\c odin;

create sequence sq_notify_id;
alter sequence sq_notify_id owner to dbaodin;

create table notifyusers (
        nu_id integer primary key default nextval('sq_notify_id'),
        nu_usr_id smallint not null references users default 0,
        nu_message text
        );
alter table notifyusers owner to dbaodin;

-- 'Notifyusers' stored procedures
--
-- notify_user
create or replace function notify_user(
    ticket varchar(255),
    usr_id smallint,
    message text )
returns void AS $$
begin
    INSERT INTO notifyusers( nu_usr_id, nu_message ) VALUES ( usr_id, message );
end;
$$ language plpgsql;
alter function notify_user(varchar,smallint,text) owner to dbaodin;

