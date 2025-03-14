#!/bin/bash

# Load configurations from an external file
CONFIG_FILE="./bin/config.sh"
if [ ! -f "$CONFIG_FILE" ]; then
    echo "Configuration file not found: $CONFIG_FILE"
    exit 1
fi
source "$CONFIG_FILE"

# Validate backup directory
# @see config.sh
if [ ! -d "$BACKUP_DIR" ]; then
    echo "Backup directory does not exist: $BACKUP_DIR"
    exit 1
fi

# Check if required commands are available
for cmd in mysqldump tar find gzip php; do
    if ! command -v $cmd &>/dev/null; then
        echo "Error: $cmd is not installed. Exiting."
        exit 1
    fi
done

# Function to get .env variable securely
get_env_var() {
    local value=$(grep "^$1=" "$BASE_DIR/.env" | cut -d '=' -f2-)
    echo ${value//\"/} # Removes quotes if any
}

# Load database configurations from .env
DB_HOST=$(get_env_var DB_HOST)
DB_USERNAME=$(get_env_var DB_USERNAME)
DB_PASSWORD=$(get_env_var DB_PASSWORD)
DB_DATABASE=$(get_env_var DB_DATABASE)

# Check for empty variables
if [ -z "$DB_HOST" ] || [ -z "$DB_USERNAME" ] || [ -z "$DB_DATABASE" ]; then
    echo "Database configuration variables cannot be empty. Check .env file."
    exit 1
fi

if [ "$DB_USERNAME" != "root" ] && [ -z "$DB_PASSWORD" ]; then
    echo "DB_PASSWORD cannot be empty if DB_USERNAME is not root. Check .env file."
    exit 1
fi

echo "Starting backup tasks for project..."
BACKUP_FILE="$BACKUP_DIR/db_backup_$(date +"%Y-%m-%d_%H-%M-%S").sql.gz"

# Database Backup
{
    echo "Creating database backup..."
    if [ "$DB_USERNAME" = "root" ] && [ -z "$DB_PASSWORD" ]; then
        mysqldump -h "$DB_HOST" -u "$DB_USERNAME" "$DB_DATABASE" | gzip >"$BACKUP_FILE"
    else
        mysqldump -h "$DB_HOST" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" | gzip >"$BACKUP_FILE"
    fi
    echo "Database backup completed."
} || {
    echo "Error: Database backup failed."
}

# Clear Old Backups
{
    echo "Deleting old backups..."
    find "$BACKUP_DIR" -type f -mtime +$BACKUP_RETENTION_DAYS -name 'db_backup_*.sql.gz' -exec rm -f {} \;
    echo "Old backup cleanup completed."
} || {
    echo "Error: Old backup cleanup failed."
}

# At the end of the database backup process
echo "Backup created: $BACKUP_FILE"
