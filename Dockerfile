FROM alpine:3.18

# Install required packages
RUN apk add --no-cache \
    nginx \
    php81 \
    php81-fpm \
    php81-sqlite3 \
    php81-pdo \
    php81-pdo_sqlite \
    php81-session \
    php81-json \
    php81-mbstring \
    php81-curl \
    php81-xml \
    php81-simplexml \
    php81-dom \
    php81-fileinfo \
    php81-zip \
    php81-opcache \
    supervisor \
    bash

# Create web root directory
RUN mkdir -p /var/www/vinotec

# Set up nginx configuration
RUN rm -f /etc/nginx/http.d/default.conf
COPY nginx.conf /etc/nginx/nginx.conf
COPY site.conf /etc/nginx/http.d/default.conf

# Set up PHP-FPM configuration
RUN sed -i 's/;listen.owner = nobody/listen.owner = nginx/' /etc/php81/php-fpm.d/www.conf && \
    sed -i 's/;listen.group = nobody/listen.group = nginx/' /etc/php81/php-fpm.d/www.conf && \
    sed -i 's/;listen.mode = 0660/listen.mode = 0666/' /etc/php81/php-fpm.d/www.conf && \
    sed -i 's/listen = 127.0.0.1:9000/listen = \/run\/php-fpm.sock/' /etc/php81/php-fpm.d/www.conf && \
    sed -i 's/user = nobody/user = nginx/' /etc/php81/php-fpm.d/www.conf && \
    sed -i 's/group = nobody/group = nginx/' /etc/php81/php-fpm.d/www.conf

# Create supervisor configuration
COPY supervisord.conf /etc/supervisord.conf

# Set proper permissions
RUN chown -R nginx:nginx /var/www/vinotec && \
    chmod -R 755 /var/www/vinotec

# Create necessary directories
RUN mkdir -p /run/nginx /run/php-fpm /var/log/supervisor

# Copy your application files (if any)
# COPY vinotec/ /var/www/vinotec/

# Create a simple index.php for testing
RUN echo '<?php phpinfo(); ?>' > /var/www/vinotec/index.php && \
    echo '<?php echo "PHP works!"; ?>' > /var/www/vinotec/test.php && \
    echo '<h1>Static HTML works!</h1>' > /var/www/vinotec/index.html

# Copy run script
COPY run.sh /
RUN chmod a+x /run.sh

# Expose port 80
EXPOSE 8080

# Start with run.sh
CMD ["/run.sh"]
