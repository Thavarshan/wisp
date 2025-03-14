#!/bin/bash
# Configuration File for Backup and Maintenance Script

# Laravel project directory
BASE_DIR="./"

# Backup directory
BACKUP_DIR=$1

# Log directory in Laravel
LOG_DIR="$BASE_DIR/storage/logs"

# Number of days to retain log files
LOG_RETENTION_DAYS=30

# Number of days to retain backup files
BACKUP_RETENTION_DAYS=30
