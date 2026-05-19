# Command Guide - WashManager Pro

Panduan lengkap perintah dan penjelasan untuk menjalankan aplikasi WashManager Pro.

## 📋 Daftar Isi
- [Setup Awal](#setup-awal)
- [Perintah Development](#perintah-development)
- [Database Management](#database-management)
- [Cache Management](#cache-management)
- [Debugging & Troubleshooting](#debugging--troubleshooting)
- [Production Commands](#production-commands)
- [Maintenance Commands](#maintenance-commands)

---

## 🚀 Setup Awal

### 1. **Instalasi Dependencies**
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies (jika ada)
npm install
```
**Penjelasan:** Menginstall semua package yang diperlukan aplikasi Laravel.

### 2. **Environment Setup**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```
**Penjelasan:** Menyiapkan file konfigurasi dan generate key unik untuk aplikasi.

### 3. **Database Setup**
```bash
# Run migrations
php artisan migrate

# Seed database with sample data
php artisan db:seed
```
**Penjelasan:** Membuat tabel database dan mengisi data awal (services, staff, users).

---

## 💻 Perintah Development

### 1. **Menjalankan Server**
```bash
# Start development server
php artisan serve

# Start with custom host and port
php artisan serve --host=127.0.0.1 --port=8000
```
**Penjelasan:** Menjalankan server development Laravel di `http://127.0.0.1:8000`.

### 2. **Monitoring Logs**
```bash
# Watch Laravel logs in real-time
tail -f storage/logs/laravel.log

# Clear log file
> storage/logs/laravel.log
```
**Penjelasan:** Memantau log aplikasi untuk debugging dan monitoring error.

### 3. **Asset Compilation**
```bash
# Compile assets for development
npm run dev

# Watch for changes and auto-compile
npm run watch

# Compile for production
npm run build
```
**Penjelasan:** Compile CSS/JS assets menggunakan Vite atau Laravel Mix.

---

## 🗄️ Database Management

### 1. **Migration Commands**
```bash
# Check migration status
php artisan migrate:status

# Run pending migrations
php artisan migrate

# Rollback last migration batch
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Fresh migration (drop all tables and re-migrate)
php artisan migrate:fresh

# Fresh migration with seeding
php artisan migrate:fresh --seed
```
**Penjelasan:** Mengelola struktur database dan perubahan skema.

### 2. **Seeding Commands**
```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=ServiceSeeder
php artisan db:seed --class=StaffSeeder
php artisan db:seed --class=SampleDataSeeder

# Seed with fresh migration
php artisan migrate:fresh --seed
```
**Penjelasan:** Mengisi database dengan data awal atau sample data.

### 3. **Database Inspection**
```bash
# Open Tinker (Laravel REPL)
php artisan tinker

# Check data counts
php artisan tinker --execute="echo 'Users: ' . App\Models\User::count() . PHP_EOL;"
php artisan tinker --execute="echo 'Services: ' . App\Models\Service::count() . PHP_EOL;"
php artisan tinker --execute="echo 'Staff: ' . App\Models\Staff::count() . PHP_EOL;"
php artisan tinker --execute="echo 'Orders: ' . App\Models\WashOrder::count() . PHP_EOL;"
```
**Penjelasan:** Memeriksa data dalam database menggunakan Eloquent ORM.

---

## 🧹 Cache Management

### 1. **Clear All Caches**
```bash
# Clear all caches at once
php artisan optimize:clear
```
**Penjelasan:** Membersihkan semua cache sekaligus (application, config, route, view).

### 2. **Individual Cache Commands**
```bash
# Clear application cache
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Clear route cache
php artisan route:clear

# Clear compiled views
php artisan view:clear

# Clear compiled classes
php artisan clear-compiled
```
**Penjelasan:** Membersihkan cache secara individual untuk debugging spesifik.

### 3. **Optimize for Production**
```bash
# Cache configurations
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```
**Penjelasan:** Mengoptimalkan aplikasi untuk performa production.

---

## 🐛 Debugging & Troubleshooting

### 1. **Route Debugging**
```bash
# List all routes
php artisan route:list

# Filter routes by name
php artisan route:list --name=orders

# Filter routes by method
php artisan route:list --method=POST
```
**Penjelasan:** Memeriksa routing aplikasi untuk debugging URL issues.

### 2. **Model & Database Debugging**
```bash
# Check model relationships
php artisan tinker
>>> $order = App\Models\WashOrder::with(['vehicle', 'service', 'staff'])->first()
>>> $order->vehicle->license_plate
>>> $order->service->name

# Test order creation
>>> $user = App\Models\User::first()
>>> $service = App\Models\Service::first()
>>> $staff = App\Models\Staff::first()
```
**Penjelasan:** Testing model relationships dan data integrity.

### 3. **Permission & Storage Issues**
```bash
# Fix storage permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache

# Fix storage permissions (Windows - run as Administrator)
icacls storage /grant Everyone:F /T
icacls bootstrap/cache /grant Everyone:F /T

# Create storage link for public files
php artisan storage:link
```
**Penjelasan:** Memperbaiki permission issues yang sering terjadi.

### 4. **Queue & Job Debugging**
```bash
# Process queue jobs
php artisan queue:work

# List failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```
**Penjelasan:** Mengelola background jobs dan queue processing.

---

## 🚀 Production Commands

### 1. **Deployment Preparation**
```bash
# Install production dependencies only
composer install --no-dev --optimize-autoloader

# Cache everything for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Compile assets for production
npm run build
```
**Penjelasan:** Menyiapkan aplikasi untuk deployment production.

### 2. **Environment Setup**
```bash
# Set application to production mode in .env
APP_ENV=production
APP_DEBUG=false

# Generate new application key (if needed)
php artisan key:generate --force
```
**Penjelasan:** Konfigurasi environment untuk production.

### 3. **Database Migration (Production)**
```bash
# Run migrations in production (with confirmation)
php artisan migrate --force

# Seed production data
php artisan db:seed --class=ProductionSeeder
```
**Penjelasan:** Menjalankan migration di production environment.

---

## 🔧 Maintenance Commands

### 1. **Application Maintenance**
```bash
# Put application in maintenance mode
php artisan down

# Put application in maintenance mode with custom message
php artisan down --message="Sistem sedang maintenance, akan kembali dalam 30 menit"

# Bring application back online
php artisan up
```
**Penjelasan:** Mengaktifkan/menonaktifkan maintenance mode.

### 2. **Log Management**
```bash
# View recent logs
tail -n 100 storage/logs/laravel.log

# Clear logs
> storage/logs/laravel.log

# Archive old logs (manual)
mv storage/logs/laravel.log storage/logs/laravel-$(date +%Y%m%d).log
touch storage/logs/laravel.log
```
**Penjelasan:** Mengelola file log aplikasi.

### 3. **Performance Monitoring**
```bash
# Check application performance
php artisan optimize

# Monitor memory usage
php -d memory_limit=512M artisan your:command

# Check PHP configuration
php --ini
php -m | grep -i extension_name
```
**Penjelasan:** Monitoring performa dan konfigurasi PHP.

---

## 🎯 Workflow Specific Commands

### 1. **Order Management Testing**
```bash
# Test order creation
php artisan tinker
>>> $order = App\Models\WashOrder::factory()->create()
>>> $order->load(['vehicle', 'service', 'staff'])

# Check recent orders
>>> App\Models\WashOrder::latest()->take(5)->get(['order_number', 'status', 'total_price'])
```
**Penjelasan:** Testing fitur order management.

### 2. **User & Authentication**
```bash
# Create admin user
php artisan tinker
>>> App\Models\User::create(['name' => 'Admin', 'email' => 'admin@carwash.com', 'password' => bcrypt('password')])

# Reset user password
>>> $user = App\Models\User::where('email', 'admin@carwash.com')->first()
>>> $user->update(['password' => bcrypt('newpassword')])
```
**Penjelasan:** Mengelola user dan authentication.

### 3. **Service & Staff Management**
```bash
# Add new service
php artisan tinker
>>> App\Models\Service::create(['name' => 'Cuci Motor', 'type' => 'standard', 'price' => 15000, 'duration_minutes' => 30, 'description' => 'Cuci motor standar'])

# Add new staff
>>> App\Models\Staff::create(['name' => 'Budi Santoso', 'position' => 'Cuci', 'phone' => '081234567890', 'is_active' => true])
```
**Penjelasan:** Menambah data service dan staff baru.

---

## 🚨 Emergency Commands

### 1. **Quick Fix Commands**
```bash
# Emergency cache clear
php artisan optimize:clear && php artisan config:clear && php artisan view:clear

# Reset application (DANGER: will lose data)
php artisan migrate:fresh --seed

# Fix common issues
composer dump-autoload
php artisan package:discover
```
**Penjelasan:** Perintah darurat untuk memperbaiki masalah umum.

### 2. **Backup Commands**
```bash
# Backup database (MySQL)
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup files
tar -czf backup_files_$(date +%Y%m%d_%H%M%S).tar.gz storage/ public/uploads/

# Restore database
mysql -u username -p database_name < backup_file.sql
```
**Penjelasan:** Backup dan restore data penting.

---

## 📝 Development Tips

### 1. **Useful Aliases (Optional)**
```bash
# Add to ~/.bashrc or ~/.zshrc
alias pa="php artisan"
alias pas="php artisan serve"
alias pam="php artisan migrate"
alias pac="php artisan cache:clear"
alias pat="php artisan tinker"
```
**Penjelasan:** Shortcut untuk perintah yang sering digunakan.

### 2. **VS Code Extensions (Recommended)**
- Laravel Extension Pack
- PHP Intelephense
- Laravel Blade Snippets
- Laravel goto view
- Laravel Extra Intellisense

### 3. **Browser Tools**
- Laravel Debugbar (development)
- Browser Developer Tools (F12)
- Network tab untuk monitoring AJAX requests
- Console tab untuk JavaScript errors

---

## ✅ Quick Checklist

### **Sebelum Development:**
- [ ] `composer install`
- [ ] Copy `.env.example` to `.env`
- [ ] `php artisan key:generate`
- [ ] Setup database di `.env`
- [ ] `php artisan migrate --seed`
- [ ] `php artisan serve`

### **Setelah Code Changes:**
- [ ] `php artisan cache:clear` (jika ada config changes)
- [ ] `php artisan view:clear` (jika ada view changes)
- [ ] `composer dump-autoload` (jika ada class baru)

### **Sebelum Production:**
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] Set `APP_DEBUG=false` di `.env`
- [ ] Test semua fitur utama

---

**💡 Tips:** Selalu backup database sebelum menjalankan perintah yang mengubah struktur data!