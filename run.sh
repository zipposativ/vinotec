#!/usr/bin/with-contenv bashio

# ==============================================================================
# Home Assistant Add-on: Vinotec Web App
# Starts nginx and php-fpm services
# ==============================================================================

bashio::log.info "Starting Vinotec Web App..."

# Check if config directory exists and set permissions
if bashio::fs.directory_exists "/share/vinotec"; then
    bashio::log.info "Using existing /share/vinotec directory"
    rm -rf /var/www/vinotec
    ln -sf /share/vinotec /var/www/vinotec
else
    bashio::log.info "Creating default vinotec directory"
    mkdir -p /share/vinotec
    cp -r /var/www/vinotec/* /share/vinotec/ 2>/dev/null || true
    rm -rf /var/www/vinotec
    ln -sf /share/vinotec /var/www/vinotec
fi

# Set proper permissions
chown -R nginx:nginx /share/vinotec
chmod -R 755 /share/vinotec

# Create necessary runtime directories
mkdir -p /run/nginx /run/php-fpm /var/log/supervisor

bashio::log.info "Starting services..."

# Start supervisor which manages nginx and php-fpm
exec /usr/bin/supervisord -c /etc/supervisord.conf
