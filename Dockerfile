# Dockerfile
FROM python:3.11-slim

# Install required packages
RUN apt-get update && \
    apt-get install -y cron openssl build-essential python3-dev && \
    rm -rf /var/lib/apt/lists/*
    
# Copy application files
COPY ./src /app

# Set working directory
WORKDIR /app

# Install Python dependencies
RUN pip install -r requirements.txt

# Add crontab file
COPY crontab /etc/cron.d/scheduler-cron
RUN chmod 0644 /etc/cron.d/scheduler-cron && crontab /etc/cron.d/scheduler-cron

# Create the log file if it doesn't exist and set permissions
RUN touch /var/log/cron.log && chmod 777 /var/log/cron.log

# Create directories for backups with proper permissions
RUN mkdir -p /app/backups && chmod 777 /app/backups

# Generate a random string for SECRET_KEY and replace it in app.py
RUN SECRET_KEY=$(openssl rand -hex 16) && \
    sed -i "s|app.config\['SECRET_KEY'\] = os.getenv('SECRET_KEY', 'supersecretkey')|app.config['SECRET_KEY'] = os.getenv('SECRET_KEY', '${SECRET_KEY}')|g" /app/app.py

# Generate a random string for ACCESS_KEY and replace it in app.py
RUN ACCESS_KEY=$(openssl rand -hex 16) && \
sed -i "s|ACCESS_KEY = os.getenv('ACCESS_KEY', 'supersecretkey')|ACCESS_KEY = os.getenv('ACCESS_KEY', '${ACCESS_KEY}')|g" /app/app.py

# Create a script to run both cron and Flask
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Use the entrypoint script
CMD ["/usr/local/bin/docker-entrypoint.sh"]