#!/usr/bin/env bash

# get the user that owns our app here
APP_USER=www-multisite;

# make sure we own it
chown -R $APP_USER:$APP_USER $PWD*;

export COMPOSER_ALLOW_SUPERUSER=1;

# update all packages
composer update;

# dump the composer autoloader and force it to regenerate
composer dumpautoload -o -n;

# just inn case php is caching
service php8.3-fpm restart && service nginx reload

# clear out our redis cache
redis-cli flushall

# make sure we own it one last time
chown -R $APP_USER:$APP_USER $PWD*;

# reset permissions
find $PWD -type d -exec chmod 755 {} \;
find $PWD -type f -exec chmod 644 {} \;
chmod +x refresh.sh
