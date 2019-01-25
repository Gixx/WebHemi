#!/bin/sh
dir="$(dirname "$0")"
cd $dir

rm -f ../build/webhemi_schema.sqlite3
cat ../build/webhemi_schema.sqlite.sql | sqlite3 ../build/webhemi_schema.sqlite3
