#!/bin/sh
dir="$(dirname "$0")"

cd $dir

rm -f ../build/webhemi_schema.sqlite3 && ./mysql2sqlite.sh --host=localhost -u root -pdevmysql webhemi | sqlite3 ../build/webhemi_schema.sqlite3
