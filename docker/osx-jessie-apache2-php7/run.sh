#!/bin/bash


# CONF APACHE
sed -i -e "s/export APACHE_RUN_GROUP=www-data/export APACHE_RUN_GROUP=staff/" /etc/apache2/envvars
sed -i -e "s/##ServerName/ServerName ${APACHE_VHOST_SERVERNAME}/" /etc/apache2/sites-available/000-default.conf
sed -i -e "s/^#AddDefaultCharset UTF-8$/AddDefaultCharset UTF-8/" /etc/apache2/conf-enabled/charset.conf

# Tweaks to give Apache/PHP write permissions to the app
chown -R www-data:staff /var/www
chown -R www-data:staff /app

/setup-app.sh

exec supervisord -n