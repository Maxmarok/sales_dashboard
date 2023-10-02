FROM node:lts-alpine

WORKDIR /var/www

### Install Laravel Echo Server and dependencies
RUN set -eux; \
    apk add --update --no-cache \
        sqlite \
        openssl \
    ; \
    apk add --update --no-cache \
        python3 make g++ \
    ; \
    apk add --update --no-cache --virtual .build-deps \
        build-base \
    ; \
    yarn global add --prod --no-lockfile laravel-echo-server ; \
    apk del .build-deps ; \
    yarn cache clean ; \
    mkdir -p /app/database ; \
    rm /usr/local/bin/docker-entrypoint.sh

COPY bin/* /usr/local/bin/
COPY src/* /usr/local/src/

VOLUME /var/www
EXPOSE 6001

ENTRYPOINT ["docker-entrypoint"]
CMD ["start"]
