# Deployment Guide - Congregation Management System

**Last Updated:** 2025-12-27  
**Version:** 1.0  
**Target Environment:** Production

---

## Overview

This guide provides step-by-step instructions for deploying the Congregation Management System to production environments. The system is containerized using Docker and can be deployed to various hosting platforms.

---

## Prerequisites

### System Requirements

**Minimum Server Specifications:**

- **CPU:** 2 cores
- **RAM:** 4GB
- **Storage:** 20GB SSD
- **OS:** Ubuntu 20.04+ or any Linux distribution with Docker support

**Software Requirements:**

- Docker 20.10+
- Docker Compose 2.0+
- Git
- SSL Certificate (for HTTPS)

### Access Requirements

- SSH access to production server
- Domain name configured
- Email service credentials (for notifications)
- Database backup storage (S3, local, etc.)

---

## Deployment Options

### Option 1: Docker Compose (Recommended)

**Best for:** VPS deployments, self-hosted environments

**Advantages:**

- Full control over infrastructure
- Easy to customize
- Cost-effective
- Data sovereignty

### Option 2: Laravel Sail (Development/Staging)

**Best for:** Development and staging environments

**Advantages:**

- Quick setup
- Consistent with local development
- Easy to tear down and rebuild

---

## Production Deployment (Docker Compose)

### Step 1: Server Preparation

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/latest/download/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Verify installation
docker --version
docker-compose --version
```

### Step 2: Clone Repository

```bash
# Create application directory
sudo mkdir -p /var/www/managing-congregation
cd /var/www/managing-congregation

# Clone repository
git clone https://github.com/your-org/managing-congregation.git .

# Set permissions
sudo chown -R $USER:$USER /var/www/managing-congregation
```

### Step 3: Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Edit environment variables
nano .env
```

**Critical Environment Variables:**

```env
# Application
APP_NAME="Congregation Management"
APP_ENV=production
APP_KEY=  # Generate with: php artisan key:generate
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=congregation_db
DB_USERNAME=congregation_user
DB_PASSWORD=STRONG_PASSWORD_HERE

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"

# Session & Cache
SESSION_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

# Backup
BACKUP_DISK=s3  # or 'local'
```

### Step 4: Docker Compose Configuration

Create `docker-compose.prod.yml`:

```yaml
version: "3.8"

services:
  app:
    build:
      context: .
      dockerfile: docker/Dockerfile.prod
    container_name: congregation-app
    restart: unless-stopped
    working_dir: /var/www/html
    volumes:
      - ./:/var/www/html
      - ./storage:/var/www/html/storage
    environment:
      - APP_ENV=production
    networks:
      - congregation-network
    depends_on:
      - db
      - redis

  nginx:
    image: nginx:alpine
    container_name: congregation-nginx
    restart: unless-stopped
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./:/var/www/html
      - ./docker/nginx/prod.conf:/etc/nginx/conf.d/default.conf
      - ./docker/ssl:/etc/nginx/ssl
    networks:
      - congregation-network
    depends_on:
      - app

  db:
    image: mysql:8.0
    container_name: congregation-db
    restart: unless-stopped
    environment:
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_USER: ${DB_USERNAME}
      MYSQL_PASSWORD: ${DB_PASSWORD}
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
    volumes:
      - db-data:/var/lib/mysql
    networks:
      - congregation-network

  redis:
    image: redis:alpine
    container_name: congregation-redis
    restart: unless-stopped
    networks:
      - congregation-network

networks:
  congregation-network:
    driver: bridge

volumes:
  db-data:
    driver: local
```

### Step 5: Build and Deploy

```bash
# Build containers
docker-compose -f docker-compose.prod.yml build

# Start services
docker-compose -f docker-compose.prod.yml up -d

# Install dependencies
docker-compose -f docker-compose.prod.yml exec app composer install --optimize-autoloader --no-dev

# Generate application key
docker-compose -f docker-compose.prod.yml exec app php artisan key:generate

# Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Seed permissions and roles
docker-compose -f docker-compose.prod.yml exec app php artisan db:seed --class=PermissionSeeder --force

# Build frontend assets
docker-compose -f docker-compose.prod.yml exec app npm ci
docker-compose -f docker-compose.prod.yml exec app npm run build

# Optimize Laravel
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache

# Set permissions
docker-compose -f docker-compose.prod.yml exec app chown -R www-data:www-data /var/www/html/storage
docker-compose -f docker-compose.prod.yml exec app chmod -R 775 /var/www/html/storage
```

### Step 6: SSL Certificate Setup

**Option A: Let's Encrypt (Free)**

```bash
# Install certbot
sudo apt install certbot python3-certbot-nginx

# Obtain certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal (already configured by certbot)
sudo certbot renew --dry-run
```

**Option B: Custom Certificate**

```bash
# Copy certificate files
cp your-cert.crt docker/ssl/
cp your-key.key docker/ssl/

# Update nginx configuration to use certificates
```

### Step 7: Create Super Admin User

```bash
# Access container
docker-compose -f docker-compose.prod.yml exec app bash

# Create user via tinker
php artisan tinker

# In tinker:
$user = new App\Models\User();
$user->name = 'Super Admin';
$user->email = 'admin@your-domain.com';
$user->password = Hash::make('secure-password');
$user->role = 'super_admin';
$user->save();
exit
```

---

## Post-Deployment Configuration

### 1. Configure Scheduled Tasks

```bash
# Add to crontab
crontab -e

# Add this line:
* * * * * cd /var/www/managing-congregation && docker-compose -f docker-compose.prod.yml exec -T app php artisan schedule:run >> /dev/null 2>&1
```

### 2. Configure Queue Worker

```bash
# Start queue worker
docker-compose -f docker-compose.prod.yml exec -d app php artisan queue:work --tries=3
```

### 3. Setup Automated Backups

```bash
# Create backup script
nano /usr/local/bin/backup-congregation.sh
```

```bash
#!/bin/bash
cd /var/www/managing-congregation
docker-compose -f docker-compose.prod.yml exec -T app php artisan backup:run
```

```bash
# Make executable
chmod +x /usr/local/bin/backup-congregation.sh

# Add to crontab (daily at 2 AM)
0 2 * * * /usr/local/bin/backup-congregation.sh
```

---

## Monitoring & Maintenance

### Health Checks

```bash
# Check container status
docker-compose -f docker-compose.prod.yml ps

# View logs
docker-compose -f docker-compose.prod.yml logs -f app

# Check database connection
docker-compose -f docker-compose.prod.yml exec app php artisan tinker
>>> DB::connection()->getPdo();
```

### Performance Monitoring

```bash
# Monitor resource usage
docker stats

# Check Laravel logs
docker-compose -f docker-compose.prod.yml exec app tail -f storage/logs/laravel.log
```

### Database Maintenance

```bash
# Backup database
docker-compose -f docker-compose.prod.yml exec db mysqldump -u root -p congregation_db > backup-$(date +%Y%m%d).sql

# Restore database
docker-compose -f docker-compose.prod.yml exec -T db mysql -u root -p congregation_db < backup-20251227.sql
```

---

## Updates & Rollbacks

### Deploying Updates

```bash
# Pull latest code
git pull origin main

# Rebuild containers
docker-compose -f docker-compose.prod.yml build

# Update dependencies
docker-compose -f docker-compose.prod.yml exec app composer install --optimize-autoloader --no-dev
docker-compose -f docker-compose.prod.yml exec app npm ci
docker-compose -f docker-compose.prod.yml exec app npm run build

# Run migrations
docker-compose -f docker-compose.prod.yml exec app php artisan migrate --force

# Clear and rebuild cache
docker-compose -f docker-compose.prod.yml exec app php artisan config:clear
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker-compose -f docker-compose.prod.yml exec app php artisan config:cache
docker-compose -f docker-compose.prod.yml exec app php artisan route:cache
docker-compose -f docker-compose.prod.yml exec app php artisan view:cache

# Restart services
docker-compose -f docker-compose.prod.yml restart
```

### Rollback Procedure

```bash
# Checkout previous version
git log --oneline  # Find commit hash
git checkout <previous-commit-hash>

# Rebuild and restart
docker-compose -f docker-compose.prod.yml build
docker-compose -f docker-compose.prod.yml up -d

# Rollback migrations if needed
docker-compose -f docker-compose.prod.yml exec app php artisan migrate:rollback --step=1
```

---

## Troubleshooting

### Common Issues

**Issue 1: Permission Denied on Storage**

```bash
docker-compose -f docker-compose.prod.yml exec app chown -R www-data:www-data storage
docker-compose -f docker-compose.prod.yml exec app chmod -R 775 storage
```

**Issue 2: Database Connection Failed**

```bash
# Check database container
docker-compose -f docker-compose.prod.yml logs db

# Verify credentials in .env
# Restart database
docker-compose -f docker-compose.prod.yml restart db
```

**Issue 3: 500 Internal Server Error**

```bash
# Check logs
docker-compose -f docker-compose.prod.yml logs app

# Clear all caches
docker-compose -f docker-compose.prod.yml exec app php artisan cache:clear
docker-compose -f docker-compose.prod.yml exec app php artisan config:clear
docker-compose -f docker-compose.prod.yml exec app php artisan view:clear
```

**Issue 4: Queue Jobs Not Processing**

```bash
# Restart queue worker
docker-compose -f docker-compose.prod.yml exec app php artisan queue:restart
```

---

## Security Checklist

- [ ] APP_DEBUG=false in production
- [ ] Strong database passwords
- [ ] SSL certificate installed and configured
- [ ] Firewall configured (allow only 80, 443, 22)
- [ ] Regular automated backups
- [ ] File permissions properly set
- [ ] Environment variables secured
- [ ] Database access restricted
- [ ] Regular security updates applied

---

## Performance Optimization

### 1. Enable OPcache

Add to `docker/php/php.ini`:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
```

### 2. Configure Redis Cache

```bash
# Already configured in .env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

### 3. Database Optimization

```sql
-- Run in MySQL
OPTIMIZE TABLE members;
OPTIMIZE TABLE communities;
OPTIMIZE TABLE assignments;
```

---

## Backup Strategy

### Automated Daily Backups

The system uses `spatie/laravel-backup` for automated backups.

**Configuration:** `config/backup.php`

**Manual Backup:**

```bash
docker-compose -f docker-compose.prod.yml exec app php artisan backup:run
```

**Restore from Backup:**

```bash
# Database restore
docker-compose -f docker-compose.prod.yml exec -T db mysql -u root -p congregation_db < backup.sql

# Files restore
tar -xzf storage-backup.tar.gz -C storage/
```

---

## Support & Maintenance

**Regular Maintenance Tasks:**

- Daily: Automated backups
- Weekly: Review logs for errors
- Monthly: Security updates
- Quarterly: Performance review and optimization

**Emergency Contacts:**

- System Administrator: [contact info]
- Database Administrator: [contact info]
- Development Team: [contact info]

---

**Document Status:** ✅ Complete  
**Maintained By:** DevOps Team  
**Review Frequency:** Quarterly or when deployment process changes
