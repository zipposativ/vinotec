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
    && sed -i 's|DocumentRoot "/var/www/localhost/htdocs"|DocumentRoot "/var/www/localhost/htdocs/vinotec"|g' /etc/apache2/httpd.conf \
    && sed -i 's|<Directory "/var/www/localhost/htdocs">|<Directory "/var/www/localhost/htdocs/vinotec">|g' /etc/apache2/httpd.conf \
    && sed -i 's|DirectoryIndex index.html|DirectoryIndex index.php index.html|g' /etc/apache2/httpd.conf

# PHP-Modul aktivieren
RUN sed -i 's|#LoadModule rewrite_module|LoadModule rewrite_module|g' /etc/apache2/httpd.conf

# Arbeitsverzeichnis setzen
WORKDIR /var/www/localhost/htdocs

# Projektdateien kopieren
COPY vinotec/ /var/www/localhost/htdocs/

# Berechtigungen setzen
RUN chown -R apache:apache /var/www/localhost/htdocs \
    && chmod -R 755 /var/www/localhost/htdocs

# SQLite Datenbank-Verzeichnis vorbereiten (falls benötigt)
RUN mkdir -p /data \
    && chown apache:apache /data \
    && chmod 755 /data

# Port freigeben
EXPOSE 8080

# Healthcheck
HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8080/vinotec/ || exit 1

# Apache im Vordergrund starten
CMD ["httpd", "-D", "FOREGROUND"]
