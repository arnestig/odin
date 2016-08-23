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
