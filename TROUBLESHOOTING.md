# Car Wash Management System - Troubleshooting Guide

## 🚨 Common Issues & Solutions

### **1. "Cannot redeclare method" Error**

**Problem**: `Cannot redeclare App\Http\Controllers\Web\WashOrderController::receipt()`

**Solution**:
```bash
# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Regenerate autoload files
composer dump-autoload
```

**Root Cause**: Duplicate method declarations in controller files.

### **2. Internal Server Error (500)**

**Symptoms**: White screen or "Internal Server Error" message

**Debugging Steps**:
```bash
# 1. Check Laravel logs
tail -f storage/logs/laravel.log

# 2. Enable debug mode temporarily
# In .env file: APP_DEBUG=true

# 3. Check web server error logs
# Nginx: tail -f /var/log/nginx/error.log
# Apache: tail -f /var/log/apache2/error.log

# 4. Check PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

**Common Fixes**:
```bash
# Fix permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Clear and rebuild caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
```

### **3. Database Connection Issues**

**Error**: `SQLSTATE[HY000] [2002] Connection refused`

**Solutions**:
```bash
# 1. Check database service
sudo systemctl status mysql
sudo systemctl start mysql

# 2. Verify database credentials in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=carwash_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

# 3. Test connection
php artisan tinker
DB::connection()->getPdo();
```

### **4. Migration Issues**

**Error**: `Base table or view not found`

**Solutions**:
```bash
# 1. Run migrations
php artisan migrate

# 2. Fresh migration (WARNING: This will delete all data)
php artisan migrate:fresh --seed

# 3. Check migration status
php artisan migrate:status
```

### **5. Composer/Autoload Issues**

**Error**: `Class not found` or autoload errors

**Solutions**:
```bash
# 1. Update composer
composer self-update

# 2. Install dependencies
composer install --optimize-autoloader

# 3. Regenerate autoload
composer dump-autoload

# 4. Clear composer cache
composer clear-cache
```

### **6. Permission Issues**

**Error**: `Permission denied` or file write errors

**Solutions**:
```bash
# Set correct ownership
sudo chown -R www-data:www-data /path/to/carwash-app

# Set correct permissions
sudo chmod -R 755 /path/to/carwash-app
sudo chmod -R 775 storage bootstrap/cache

# For development (less secure)
sudo chmod -R 777 storage bootstrap/cache
```

### **7. Route Not Found (404)**

**Error**: `Route [route.name] not defined`

**Solutions**:
```bash
# 1. Clear route cache
php artisan route:clear

# 2. List all routes
php artisan route:list

# 3. Check route definitions in routes/web.php
```

### **8. Session/Authentication Issues**

**Error**: Login redirects or session problems

**Solutions**:
```bash
# 1. Clear sessions
php artisan session:flush

# 2. Check session configuration
# In .env: SESSION_DRIVER=file (or redis/database)

# 3. Generate new app key
php artisan key:generate
```

### **9. Asset/CSS Not Loading**

**Problem**: Styles not applied or 404 on assets

**Solutions**:
```bash
# 1. Check public directory permissions
sudo chmod -R 755 public

# 2. For development with Vite
npm install
npm run dev

# 3. For production
npm run build

# 4. Check web server configuration for static files
```

### **10. Excel Export Issues**

**Error**: Excel export not working

**Solutions**:
```bash
# 1. Install maatwebsite/excel
composer require maatwebsite/excel

# 2. Check PHP extensions
php -m | grep zip
php -m | grep xml

# 3. Install missing extensions
sudo apt install php8.2-zip php8.2-xml
```

## 🔧 Diagnostic Commands

### **Health Check Script**
```bash
#!/bin/bash
echo "=== Car Wash App Health Check ==="

echo "1. Checking PHP version..."
php -v

echo "2. Checking Laravel version..."
php artisan --version

echo "3. Checking database connection..."
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database OK';"

echo "4. Checking storage permissions..."
ls -la storage/

echo "5. Checking routes..."
php artisan route:list | head -10

echo "6. Checking config..."
php artisan config:show app.name

echo "=== Health Check Complete ==="
```

### **Performance Check**
```bash
# Check memory usage
php -i | grep memory_limit

# Check execution time
php -i | grep max_execution_time

# Check opcache status
php -i | grep opcache

# Check disk space
df -h
```

### **Log Analysis**
```bash
# Laravel logs
tail -n 50 storage/logs/laravel.log

# Web server logs
sudo tail -n 50 /var/log/nginx/error.log

# System logs
sudo tail -n 50 /var/log/syslog
```

## 🚀 Quick Fixes

### **Emergency Reset**
```bash
# Complete application reset (USE WITH CAUTION)
php artisan down
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
composer dump-autoload
php artisan migrate:fresh --seed
php artisan config:cache
php artisan route:cache
php artisan up
```

### **Development Mode**
```bash
# Enable debug mode
# In .env: APP_DEBUG=true
# In .env: APP_ENV=local

# Disable caching
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### **Production Mode**
```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# In .env: APP_DEBUG=false
# In .env: APP_ENV=production
```

## 📞 Getting Help

### **Log Files to Check**
1. `storage/logs/laravel.log` - Application logs
2. `/var/log/nginx/error.log` - Web server logs
3. `/var/log/php8.2-fpm.log` - PHP-FPM logs
4. `/var/log/mysql/error.log` - Database logs

### **Information to Provide**
When reporting issues, include:
- Error message (exact text)
- Steps to reproduce
- Browser/environment details
- Relevant log entries
- Laravel version: `php artisan --version`
- PHP version: `php -v`

### **Useful Commands for Debugging**
```bash
# Check application status
php artisan about

# Check environment
php artisan env

# Check database
php artisan db:show

# Check queue status
php artisan queue:work --once

# Check scheduled tasks
php artisan schedule:list
```

## 🔍 Advanced Debugging

### **Enable Query Logging**
```php
// Add to AppServiceProvider boot() method
DB::listen(function ($query) {
    Log::info('Query: ' . $query->sql);
    Log::info('Bindings: ' . json_encode($query->bindings));
    Log::info('Time: ' . $query->time);
});
```

### **Debug Blade Views**
```php
// Add to config/app.php
'debug_blacklist' => [
    '_ENV' => [
        'APP_KEY',
        'DB_PASSWORD',
    ],
],
```

### **Memory Usage Monitoring**
```php
// Add to routes for memory debugging
Route::get('/debug/memory', function () {
    return [
        'memory_usage' => memory_get_usage(true),
        'memory_peak' => memory_get_peak_usage(true),
        'memory_limit' => ini_get('memory_limit'),
    ];
});
```

---

**Remember**: Always backup your database before making major changes!