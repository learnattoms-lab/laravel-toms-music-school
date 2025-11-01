#!/bin/bash
# Execute Laravel Artisan commands in Docker container
docker compose exec app php artisan "$@"

