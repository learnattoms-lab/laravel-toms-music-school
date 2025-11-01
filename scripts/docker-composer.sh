#!/bin/bash
# Execute Composer commands in Docker container
docker compose exec app composer "$@"

