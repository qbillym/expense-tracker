#!/bin/bash

# Deployment script for Render
echo "Starting deployment..."

# Check if we're in production
if [ "$APP_ENV" = "production" ]; then
    echo "Running in production mode"
    
    # Clear cache
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    # Run database migrations
    echo "Running database migrations..."
    php artisan migrate --force
    
    # Seed database if needed
    # php artisan db:seed --force
    
    # Optimize for production
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    echo "Deployment completed successfully!"
else
    echo "Not in production mode, skipping optimization"
fi
