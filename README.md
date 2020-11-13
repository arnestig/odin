# odin
IP plan management and tracker

# Prerequisites
For running and developing Odin you will need the following packages:
 - php
 - postgresql
 - perl

Installing on a Debian-based system you can:
```
apt-get install php php-pgsql php-gd perl libdbd-pg-perl libdbi-perl libhttp-date-perl postgresql postgresql-contrib
```

Update the file /etc/postgresql/11/main/pg_hba.conf and change 'peer' to 'md5' on the following line:
```
"local   all             all                                peer"
```

Restart the service via /etc/init.d/postgresql restart

Connecting to the database using the following command:
```
psql odin dbaodin
```

# Development
start a tiny php server for development:
```
php -S 127.0.0.1:8080 -t www/
```

run requests via curl for example:
```
curl http://127.0.0.1:8080
```

purge the database and re-populate:
```
for sql in $(ls database/*.sql); do sudo -u postgres psql -f $sql; done
```

# Installation

