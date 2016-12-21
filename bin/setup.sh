#!/bin/sh
dir="$(dirname "$0")"
parentdir="$(dirname "$dir")"
publicIp=$(curl ipinfo.io/ip)

cd $dir

if [ -n "$(type -t mysql)" ] ; then
    echo "Initialize database"
    mysql --host=$publicIp -u root -pwebhemi < ../build/webhemi_schema.sql

    if [ -n "$(type -t mysqldump)" ] && [ -n "$(type -t sqlite3)" ]; then
        echo "Create SQLite copy for the unit test"
        file="../build/webhemi_schema.sqlite3"

        if [ -f $file ] ; then
            rm $file
        fi

        ./mysql2sqlite.sh --host=$publicIp -u root -px webhemi | sqlite3 $file
    fi
fi


if [ -n "$(type -t php)" ] ; then
    cd $parentdir

    echo "Install composer"
    curl -L -o /usr/bin/composer https://getcomposer.org/composer.phar
    chmod +x /usr/bin/composer
    
    echo "Install composer packages"
    if [ -f "composer.lock" ] ; then
        rm composer.lock
    fi
    composer install --prefer-source --no-interaction
fi
