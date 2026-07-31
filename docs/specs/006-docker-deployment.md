# Docker Deployment Specification

## Overview
This document specifies the Docker deployment configuration for NandoRAG to enable easy deployment in containerized environments with high port isolation.

## Architecture

### Services
1. **app** - Main Laravel application running on Apache
2. **db** - PostgreSQL 15 database
3. **redis** - Redis 7 for queueing and caching
4. **horizon** - Laravel Horizon worker for processing jobs

### Networking
- App exposed on port 9090 (host) → 80 (container)
- Database exposed on port 5433 (host) → 5432 (container) 
- Redis exposed on port 6380 (host) → 6379 (container)
- Internal service communication via Docker network

### Volumes
- Persistent storage for database data
- Bind mounts for application storage and cache

## Environment Variables
Uses `.env.docker` file with production-optimized settings:
- APP_ENV=production
- APP_DEBUG=false
- Database connection to service hostnames
- Ollama accessible via host.docker.internal for Mac/Windows

## Deployment Instructions

### Prerequisites
- Docker Engine v20.10+
- Docker Compose v2+
- Ollama running on host machine (accessible via host.docker.internal)

### Steps
1. Copy `.env.example` to `.env` if not present
2. Update `.env` with production values if needed
3. Run: `docker compose up -d --build`
4. Wait for containers to initialize (~30-60 seconds)
5. Access application at: http://localhost:9090
6. Laravel Horizon dashboard available at: http://localhost:9090/horizon

### Management Commands
```bash
# View logs
docker compose logs -f

# Stop containers
docker compose down

# Rebuild after code changes
docker compose up -d --build

# Execute artisan commands
docker compose exec app php artisan <command>

# Run migrations
docker compose exec app php artisan migrate

# Seed database
docker compose exec app php artisan db:seed
```

## Configuration Notes
- Apache configured with increased upload limits for TUS (LimitRequestBody 0)
- Storage and cache directories mounted for persistence
- Ollama connection uses host.docker.internal for cross-platform compatibility
- Horizon runs as separate service for proper process isolation