#!/bin/sh
dir="$(dirname "$0")"
parentdir="$(dirname "$dir")"

cd $parentdir

echo "Install composer"
curl -L -o /usr/bin/composer https://getcomposer.org/composer.phar
chmod +x /usr/bin/composer

echo "Install composer packages"
if [ -f "composer.lock" ] ; then
    rm composer.lock
fi
composer install --prefer-source --no-interaction
