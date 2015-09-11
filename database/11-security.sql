-- authenticate
create or replace function authenticate (
    username varchar(45),
    password varchar(100) )
returns varchar as $$
declare
    skey users.usr_session_key%type;
begin
    perform usr_id from users WHERE usr_usern = username and usr_pwd = crypt( password, usr_pwd );
    if not found then
        -- failed login must not delete usr_session_key, i.e. valid session stays valid
        return null;
    else
        skey := cast(gen_random_uuid() as varchar) || '-' || cast(gen_random_uuid() as varchar);
        update users
           set usr_session_key = skey,
               usr_last_touch = now()
         where usr_usern = username;
        --commit;
        return skey;
    end if;
end;
$$ language plpgsql;
        --usr_session_key varchar(255),
        --usr_last_touch timestamp

-- isSessionValid
create or replace function isSessionValid (
    username varchar(45),
    session varchar(74))
returns boolean as $$
begin
    if session is null or username is null then
        return false;
    else
        perform usr_id
           from users
          where usr_usern = username
            and usr_session_key = session
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

