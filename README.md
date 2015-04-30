# odin
IP plan management and tracker

# Development
start a tiny php server for development:
php -S 127.0.0.1:8080 -t www/

run requests via curl for example:
curl http://127.0.0.1:8080

purge the database and re-populate:
for sql in $(ls database/*.sql); do sudo -u postgres psql -f $sql; done
