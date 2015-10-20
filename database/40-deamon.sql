\c odin;

-- get_hosts_to_scan
create or replace function get_hosts_to_scan()
returns SETOF refcursor AS $$
declare
ref1 refcursor;
begin
open ref1 for
    SELECT host_ip FROM hosts WHERE host_last_scanned IS NULL or host_last_scanned < (NOW() - interval '1 hour'); 
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
