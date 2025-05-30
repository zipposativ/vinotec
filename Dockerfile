# Home Assistant Add-on Dockerfile für PHP-Projekt mit SQLite
ARG BUILD_FROM
FROM $BUILD_FROM

# Umgebungsvariablen
ENV LANG C.UTF-8

# Installiere Apache, PHP und SQLite
RUN apk add --no-cache \
    apache2 \
    php82 \
    php82-apache2 \
    php82-sqlite3 \
    php82-pdo \
    php82-pdo_sqlite \
    php82-session \
    php82-json \
    php82-mbstring \
    php82-curl \
    php82-xml \
    php82-openssl \
    && rm -rf /var/cache/apk/*

# Apache Konfiguration
RUN sed -i 's|#ServerName www.example.com:80|ServerName localhost:8080|g' /etc/apache2/httpd.conf \
    && sed -i 's|Listen 80|Listen 8080|g' /etc/apache2/httpd.conf \
    && sed -i 's|DirectoryIndex index.html|DirectoryIndex index.php index.html|g' /etc/apache2/httpd.conf

# PHP-Modul aktivieren
RUN sed -i 's|#LoadModule rewrite_module|LoadModule rewrite_module|g' /etc/apache2/httpd.conf

# Arbeitsverzeichnis setzen
WORKDIR /var/www/html

# Projektdateien kopieren
COPY vinotec/ /var/www/html

# Berechtigungen setzen
RUN chown -R apache:apache /var/www/html \
    && chmod -R 755 /var/www/html



# Port freigeben
EXPOSE 8080


# Apache im Vordergrund starten
CMD ["httpd", "-D", "FOREGROUND"]
