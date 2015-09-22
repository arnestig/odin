\c odin;

create extension "uuid-ossp";

-- authenticate
create or replace function authenticate (
    username varchar(45),
    password varchar(100) )
returns varchar as $$
declare
    skey varchar(74);
begin
    perform usr_id from users WHERE usr_usern = username and usr_pwd = crypt( password, usr_pwd );
    if not found then
        -- failed login must not delete usr_session_key, i.e. valid session stays valid
        return null;
    else
        skey := cast(uuid_generate_v4() as varchar) || '-' || cast(uuid_generate_v4() as varchar);
        update users
           set usr_session_key = skey,
               usr_last_touch = now()
         where usr_usern = username;
        return skey;
    end if;
end;
$$ language plpgsql;
alter function authenticate(varchar,varchar) owner to dbaodin;

-- isSessionValid
create or replace function isSessionValid (
    ticket varchar(74))
returns boolean as $$
begin
    if ticket is null then
        return false;
    else
        perform usr_id
           from users
          where usr_session_key = ticket
            and usr_session_key is not null
            and usr_last_touch > now() - '30 minutes'::interval;
            --TODO: Change harcoded interval
        if found then
            update users
               set usr_last_touch = now()
             where usr_usern = username;
        end if;
        return found;
    end if;
end;
$$ language plpgsql;
alter function isSessionValid(varchar) owner to dbaodin;

-- Logout
create or replace function logOut (
    ticket varchar(74))
returns boolean as $$
begin
    if ticket is null then
        return false;
    else
        update users
           set usr_session_key = null
         where usr_session_key = ticket;
        return found;
    end if;
end;
$$ language plpgsql;
alter function logOut(varchar) owner to dbaodin;
