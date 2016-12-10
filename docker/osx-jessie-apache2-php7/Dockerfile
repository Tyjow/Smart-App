# osx-jessie-apache2-php7
# First docker test, so this is a all in one docker file
FROM buildpack-deps:jessie
MAINTAINER Yann Nave <ynave@group-dis.com>

# ENV
ENV DEBIAN_FRONTEND noninteractive
ENV INITRD No

ENV APACHE_VHOST_SERVERNAME localhost
ENV PHP_UPLOAD_MAX_FILESIZE 10M
ENV PHP_POST_MAX_SIZE 10M

ENV DOCKER_USER_ID 501
ENV DOCKER_USER_GID 20

ENV BOOT2DOCKER_ID 1000
ENV BOOT2DOCKER_GID 50

# Tweaks to give Apache/PHP write permissions to the app
RUN usermod -u ${BOOT2DOCKER_ID} www-data && \
    usermod -G staff www-data && \
    useradd -r mysql && \
    usermod -G staff mysql && \
    groupmod -g $(($BOOT2DOCKER_GID + 10000)) $(getent group $BOOT2DOCKER_GID | cut -d: -f1) && \
    groupmod -g ${BOOT2DOCKER_GID} staff

# add dotdeb to apt sources list and pin the versions
COPY conf/apt/php.pin /etc/apt/preferences.d/php
RUN echo 'deb http://packages.dotdeb.org jessie all' > /etc/apt/sources.list.d/dotdeb.list && \
    echo 'deb-src http://packages.dotdeb.org jessie all' >> /etc/apt/sources.list.d/dotdeb.list && \
    curl http://www.dotdeb.org/dotdeb.gpg | apt-key add -

ENV TIMEZONE Europe/Paris

# Change locale
RUN apt-get update -y && apt-get install -y locales && \
    echo 'fr_FR.UTF-8 UTF-8' >> /etc/locale.gen && locale-gen

ENV LC_ALL fr_FR.UTF-8
ENV LANG fr_FR.UTF-8
ENV LANGUAGE fr_FR.UTF-8

# Timezone
RUN echo $TIMEZONE > /etc/timezone && dpkg-reconfigure tzdata

# update, install and clean up to minimize the image size
RUN apt-get install -f --no-install-recommends -y \
        supervisor sudo wget git nano pwgen zip unzip \
        debconf-utils bzip2 \
        apache2 libapache2-mod-php7.0 \
        php7.0-mysql \
        php7.0-curl \
        php7.0-apcu \
        php7.0-xdebug \
        php7.0-redis \
        php7.0-imagick  \
        php7.0-mbstring php7.0-zip php7.0-xml && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && a2enmod php7.0 && a2enmod rewrite

# Supervicor
COPY conf/supervisord.conf /etc/supervisord.conf

## APACHE & PHP
COPY start-apache2.sh /start-apache2.sh
COPY conf/apache/apache_default /etc/apache2/sites-available/000-default.conf
# COPY conf/php/xdebug.ini /etc/php5/apache2/conf.d/xdebug.ini

# Configure /app folder with sample app
RUN mkdir -p /app
ADD app/ /app
COPY conf/app/env /tmp/env
COPY setup-app.sh /setup-app.sh

# ALL & SYSTE%
COPY run.sh /run.sh
RUN chmod 755 /*.sh

# VOLUMES
VOLUME  ["/app" ]

EXPOSE 80

CMD ["/run.sh"]