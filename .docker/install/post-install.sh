#!/bin/sh
set -e

# TODO: This whole file should be reworked (use dotenv variables)

if [ "${PS_DEV_MODE:-0}" -eq 1 ]; then
  echo "\n* Enabling DEV mode ...";
  sed -i -e "s/define('_PS_MODE_DEV_', false);/define('_PS_MODE_DEV_',\ true);/g" /var/www/html/config/defines.inc.php
  echo "\n* Define PHP error logs ...";
  echo "error_log=/var/www/html/var/logs/php.log" >> /usr/local/etc/php/php.ini
else
  echo "\n* Disabling DEV mode ...";
  sed -i -e "s/define('_PS_MODE_DEV_', true);/define('_PS_MODE_DEV_',\ false);/g" /var/www/html/config/defines.inc.php
fi

if [ "${PS_DEMO_MODE:-0}" -eq 1 ]; then
  echo "\n* Enabling DEMO mode ...";
  sed -i -e "s/define('_PS_MODE_DEMO_', false);/define('_PS_MODE_DEMO_',\ true);/g" /var/www/html/config/defines.inc.php
else
  echo "\n* Disabling DEMO mode ...";
  sed -i -e "s/define('_PS_MODE_DEMO_', true);/define('_PS_MODE_DEMO_',\ false);/g" /var/www/html/config/defines.inc.php
fi

# if [ "${PS_USE_DOCKER_MAILDEV:-1}" -eq 1 ]; then
#   echo "\n* Configuring emails to use maildev ..."
#   _DB_PREFIX="ps_" bin/console prestashop:config set PS_MAIL_METHOD --value "2"
#   _DB_PREFIX="ps_" bin/console prestashop:config set PS_MAIL_SERVER --value "maildev"
#   _DB_PREFIX="ps_" bin/console prestashop:config set PS_MAIL_SMTP_PORT --value "1025"
# else
#   echo "\n* Disabling maildev ..."
#   _DB_PREFIX="ps_" bin/console prestashop:config set PS_MAIL_METHOD --value "1"
# fi

if [ "${BLACKFIRE_ENABLE:-0}" -eq 1 ]; then
  if [ "$BLACKFIRE_SERVER_ID" = "0" ] || [ "$BLACKFIRE_SERVER_TOKEN" = "0" ]; then
    echo "\n* BLACKFIRE_SERVER_ID and BLACKFIRE_SERVER_TOKEN environment variables missing."
    echo "\n* Skipping blackfire install..."
  else
    echo "\n* Installing Blackfire..."
    wget -q -O - https://packages.blackfire.io/gpg.key | dd of=/usr/share/keyrings/blackfire-archive-keyring.asc
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/blackfire-archive-keyring.asc] http://packages.blackfire.io/debian any main" | tee /etc/apt/sources.list.d/blackfire.list
    apt-get update
    apt-get install -y blackfire
    blackfire agent:config --server-id=$BLACKFIRE_SERVER_ID --server-token=$BLACKFIRE_SERVER_TOKEN
    service blackfire-agent restart
    apt-get install -y blackfire-php
  fi
fi
