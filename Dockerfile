FROM php:7.2-apache-stretch
ARG ARG_APACHE_LISTEN_PORT=8080
ENV APACHE_LISTEN_PORT=${ARG_APACHE_LISTEN_PORT}
RUN sed -s -i -e "s/80/${APACHE_LISTEN_PORT}/" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf
COPY vinotec /var/www/html
USER www-data
EXPOSE ${APACHE_LISTEN_PORT}
