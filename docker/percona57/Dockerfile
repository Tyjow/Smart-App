# percona57-macosx
# First docker test, so this is a all in one docker file
FROM percona:5.7

MAINTAINER Yann Nave <ynave@group-dis.com>
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