# Car Wash Management System - Deployment Guide

Panduan lengkap untuk deploy aplikasi Car Wash Management System ke production.

## 🚀 Production Deployment

### **1. Server Requirements**

#### **Minimum Specifications**
- **CPU**: 2 vCPU
- **RAM**: 4GB
- **Storage**: 20GB SSD
- **OS**: Ubuntu 20.04+ / CentOS 8+
- **PHP**: 8.2+
- **Database**: MySQL 8.0+ / PostgreSQL 13+
- **Web Server**: Nginx / Apache

#### **Recommended Specifications**
- **CPU**: 4 vCPU
- **RAM**: 8GB
- **Storage**: 50GB SSD
- **Load Balancer**: For high availability
- **CDN**: For static assets

### **2. Environment Setup**

#### **Install Dependencies**
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo add-apt-repository ppa:ondrej/php
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl php8.2-zip php8.2-mbstring php8.2-gd

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js (for asset compilation)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs

# Install MySQL
sudo apt install mysql-server
sudo mysql_secure_installation
```

#### **Setup Database**
```sql
CREATE DATABASE carwash_production;
CREATE USER 'carwash_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON carwash_production.* TO 'carwash_user'@'localhost';
FLUSH PRIVILEGES;
```

### **3. Application Deployment**

#### **Clone and Setup**
```bash
# Clone repository
git clone https://github.com/your-repo/carwash-management.git
cd carwash-management

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies and build assets
npm install
npm run build

# Set permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### **Environment Configuration**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure .env file
nano .env
```

#### **Production .env Configuration**
```env
APP_NAME="Car Wash Management"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=carwash_production
DB_USERNAME=carwash_user
DB_PASSWORD=secure_password_here

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

#### **Database Migration**
```bash
# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed --force

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **4. Web Server Configuration**

#### **Nginx Configuration**
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    root /var/www/carwash-management/public;

    # SSL Configuration
    ssl_certificate /path/to/ssl/certificate.crt;
    ssl_certificate_key /path/to/ssl/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    index index.php;

    charset utf-8;

    # Handle Laravel routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM Configuration
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Static assets caching
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Security
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Logs
    access_log /var/log/nginx/carwash_access.log;
    error_log /var/log/nginx/carwash_error.log;
}
```

#### **PHP-FPM Optimization**
```ini
# /etc/php/8.2/fpm/pool.d/www.conf
[www]
user = www-data
group = www-data
listen = /var/run/php/php8.2-fpm.sock
listen.owner = www-data
listen.group = www-data
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.process_idle_timeout = 10s
pm.max_requests = 500
```

### **5. SSL Certificate Setup**

#### **Using Let's Encrypt (Certbot)**
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### **6. Performance Optimization**

#### **Redis Setup**
```bash
# Install Redis
sudo apt install redis-server

# Configure Redis
sudo nano /etc/redis/redis.conf
# Set: maxmemory 256mb
# Set: maxmemory-policy allkeys-lru

# Restart Redis
sudo systemctl restart redis-server
```

#### **Queue Worker Setup**
```bash
# Create systemd service
sudo nano /etc/systemd/system/carwash-worker.service
```

```ini
[Unit]
Description=Car Wash Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/carwash-management
ExecStart=/usr/bin/php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

```bash
# Enable and start service
sudo systemctl enable carwash-worker
sudo systemctl start carwash-worker
```

#### **Cron Jobs Setup**
```bash
# Edit crontab
sudo crontab -e

# Add Laravel scheduler
* * * * * cd /var/www/carwash-management && php artisan schedule:run >> /dev/null 2>&1
```

### **7. Monitoring & Logging**

#### **Log Rotation**
```bash
# Create logrotate config
sudo nano /etc/logrotate.d/carwash
```

```
/var/www/carwash-management/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    notifempty
    create 0644 www-data www-data
}
```

#### **Health Check Endpoint**
```bash
# Test application health
curl https://your-domain.com/up
```

### **8. Backup Strategy**

#### **Database Backup Script**
```bash
#!/bin/bash
# /usr/local/bin/backup-carwash.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/carwash"
DB_NAME="carwash_production"
DB_USER="carwash_user"
DB_PASS="secure_password_here"

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_$DATE.sql

# Application files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/carwash-management/storage

# Remove old backups (keep 7 days)
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
```

```bash
# Make executable and add to cron
chmod +x /usr/local/bin/backup-carwash.sh

# Add to crontab (daily at 2 AM)
0 2 * * * /usr/local/bin/backup-carwash.sh
```

### **9. Security Hardening**

#### **Firewall Configuration**
```bash
# Install UFW
sudo apt install ufw

# Configure firewall
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

#### **Fail2Ban Setup**
```bash
# Install Fail2Ban
sudo apt install fail2ban

# Configure for Nginx
sudo nano /etc/fail2ban/jail.local
```

```ini
[nginx-http-auth]
enabled = true
port = http,https
logpath = /var/log/nginx/carwash_error.log

[nginx-limit-req]
enabled = true
port = http,https
logpath = /var/log/nginx/carwash_error.log
maxretry = 10
```

### **10. Deployment Automation**

#### **Deploy Script**
```bash
#!/bin/bash
# deploy.sh

set -e

echo "🚀 Starting deployment..."

# Pull latest code
git pull origin main

# Install/update dependencies
composer install --optimize-autoloader --no-dev
npm ci && npm run build

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Run migrations
php artisan migrate --force

# Cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx
sudo systemctl restart carwash-worker

echo "✅ Deployment completed successfully!"
```

### **11. Monitoring Setup**

#### **Application Monitoring**
```bash
# Install monitoring tools
sudo apt install htop iotop nethogs

# Setup log monitoring
tail -f /var/log/nginx/carwash_error.log
tail -f /var/www/carwash-management/storage/logs/laravel.log
```

#### **Performance Monitoring**
- **New Relic**: Application performance monitoring
- **DataDog**: Infrastructure monitoring
- **Sentry**: Error tracking and monitoring

### **12. Maintenance Tasks**

#### **Regular Maintenance Checklist**
- [ ] Monitor disk space usage
- [ ] Check application logs for errors
- [ ] Verify backup integrity
- [ ] Update dependencies (security patches)
- [ ] Monitor database performance
- [ ] Check SSL certificate expiry
- [ ] Review server resource usage

#### **Update Process**
```bash
# 1. Backup current version
./backup-carwash.sh

# 2. Deploy new version
./deploy.sh

# 3. Test functionality
curl -f https://your-domain.com/up

# 4. Monitor logs
tail -f storage/logs/laravel.log
```

## 🔧 Troubleshooting

### **Common Issues**

#### **Permission Issues**
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

#### **Cache Issues**
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

#### **Database Connection Issues**
```bash
# Test database connection
php artisan tinker
DB::connection()->getPdo();
```

#### **Queue Worker Issues**
```bash
# Restart queue worker
sudo systemctl restart carwash-worker

# Check worker status
sudo systemctl status carwash-worker
```

## 📊 Performance Benchmarks

### **Expected Performance**
- **Response Time**: < 200ms (95th percentile)
- **Throughput**: 1000+ requests/minute
- **Database**: < 50ms query time
- **Memory Usage**: < 512MB per process

### **Load Testing**
```bash
# Install Apache Bench
sudo apt install apache2-utils

# Test performance
ab -n 1000 -c 10 https://your-domain.com/
```

## 🚨 Emergency Procedures

### **Rollback Process**
```bash
# 1. Switch to previous version
git checkout previous-stable-tag

# 2. Restore database backup
mysql -u carwash_user -p carwash_production < /var/backups/carwash/db_backup.sql

# 3. Clear caches and restart services
php artisan config:clear && sudo systemctl reload nginx
```

### **Emergency Contacts**
- **System Admin**: admin@your-company.com
- **Developer**: dev@your-company.com
- **Hosting Provider**: support@hosting-provider.com

---

**Deployment completed successfully! 🎉**

Monitor the application closely for the first 24 hours after deployment.