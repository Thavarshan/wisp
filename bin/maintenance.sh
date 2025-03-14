#!/bin/bash

# Load configurations from an external file
CONFIG_FILE="/path/to/config_file.sh"
if [ ! -f "$CONFIG_FILE" ]; then
    echo "Configuration file not found: $CONFIG_FILE"
    exit 1
fi
source "$CONFIG_FILE"

# Check if required commands are available
for cmd in find php; do
    if ! command -v $cmd &>/dev/null; then
        echo "Error: $cmd is not installed. Exiting."
        exit 1
    fi
done

# Clear Old Log Files
{
    echo "Cleaning up old log files..."
    find "$LOG_DIR" -type f -mtime +$LOG_RETENTION_DAYS -name '*.log' -exec rm -f {} \;
    echo "Log cleanup completed."
} || {
    echo "Error: Log cleanup failed."
}

# App Maintenance
{
    echo "Clearing App cache..."
    cd "$BASE_DIR"
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    echo "App cache cleared."
} || {
    echo "Error: App maintenance tasks failed."
}

echo "Backup and maintenance tasks completed."
