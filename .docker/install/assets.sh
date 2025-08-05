#!/bin/sh
set -e

echo "> Building assets if needed...."

if [ ! -f /var/www/html/admin-dev/themes/default/public/theme.css ]; then
    /var/www/html/tools/assets/build.sh admin-default
else
    echo "> Admin default theme already exists"
fi

if [ ! -f /var/www/html/admin-dev/themes/new-theme/public/theme.css ]; then
    /var/www/html/tools/assets/build.sh admin-new-theme
else
    echo "> Admin new theme already exists"
fi

if [ ! -f /var/www/html/themes/core.js ]; then
    /var/www/html/tools/assets/build.sh front-core
else
    echo "> Front core already exists"
fi

if [ ! -f /var/www/html/themes/classic/assets/css/theme.css ]; then
    /var/www/html/tools/assets/build.sh front-classic
else
    echo "> Front classic already exists"
fi

if [ ! -f /var/www/html/themes/hummingbird/assets/css/theme.css ]; then
    /var/www/html/tools/assets/build.sh front-hummingbird
else
    echo "> Front hummingbird already exists"
fi
