#!/bin/bash
# Execute npm commands in Docker container
docker compose exec node npm "$@"

