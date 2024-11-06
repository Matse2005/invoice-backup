# syntax=docker/dockerfile:1
FROM php:8.2.10-apache

# Install Cron
RUN apt-get update && \
    apt-get -y install cron && \
    rm -rf /var/lib/apt/lists/*

# Copy app files from the app directory
COPY ./src /var/www/html

# Set proper permissions for the web directory
RUN chown -R www-data:www-data /var/www/html

# Setup PHP configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Add crontab file and set permissions
COPY crontab /etc/cron.d/scheduler-cron
RUN chmod 0644 /etc/cron.d/scheduler-cron && \
    # Install crontab for root user
    crontab /etc/cron.d/scheduler-cron

# Create the log file if it doesn't exist and set permissions for root
RUN if [ ! -f /var/log/cron.log ]; then \
        touch /var/log/cron.log; \
    fi && \
    chmod 777 /var/log/cron.log

# Create directories for backups with root permissions
RUN mkdir -p /var/www/html/backups && \
    chmod 777 /var/www/html/backups

# Create a script to run both cron and apache
COPY <<'EOF' /usr/local/bin/docker-entrypoint.sh
#!/bin/bash
echo "Starting cron service..." >> /var/log/cron.log
service cron start
echo "Starting Apache..." >> /var/log/cron.log
apache2-foreground
EOF

RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Use the entrypoint script
CMD ["/usr/local/bin/docker-entrypoint.sh"]