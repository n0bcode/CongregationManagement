#!/bin/bash

# Ensure the container is running
echo "Checking if containers are up..."
if ! docker compose ps | grep -q "Up"; then
    echo "Starting containers..."
    docker compose up -d
fi

# Run tests
echo "Running tests inside the container..."
docker compose exec -it laravel.test php artisan test
