#!/bin/bash
export WWWUSER=${WWWUSER:-$UID}
export WWWGROUP=${WWWGROUP:-$(id -g)}

set -e

echo "Setting up Task Management System..."

# Create directories
mkdir -p backend frontend

# Scaffold Laravel in backend
echo "Scaffolding Laravel backend..."
docker-compose run --rm backend bash -lc "rm -rf * && composer create-project laravel/laravel ."

# Generate Laravel key
echo "Generating Laravel key..."
docker-compose run --rm backend bash -lc "php artisan key:generate"

# Copy environment variables
if ! [ -f .env ]; then
    cp ./backend/.env.example ./backend/.env;
fi

# Update APP_KEY in .env
docker-compose run --rm backend bash -lc "php artisan key:generate --show" | tr -d '\n' > temp_key.txt
sed -i "s/APP_KEY=/APP_KEY=$(cat temp_key.txt)/" backend/.env
rm temp_key.txt

# Build docker image
docker-compose build

# Start services
./vendor/bin/sail down
./vendor/bin/sail up -d

# Run migrations
echo "Running migrations..."
docker-compose run --rm backend $1 ${WWWUSER}:${WWWGROUP} composer install
docker-compose run --rm backend bash -lc "php artisan migrate"

# Install dependencies
echo "Installing frontend dependencies..."
docker-compose run --rm frontend sh -lc "npm install"
docker-compose run --rm frontend sh -lc "npm install -D tailwindcss postcss autoprefixer"
docker-compose run --rm frontend sh -lc "npx tailwindcss init -p"

echo "Backend: http://localhost:8080"
echo "Frontend: http://localhost:5173"
