# Car Wash Management System

Sistem manajemen car wash lengkap dengan API Laravel dan Web Interface untuk mengelola pesanan cuci, karyawan, layanan, dan analytics.

## 🚀 Features

### **API Backend**
- 🔐 **Authentication & Authorization** - Login/logout dengan Laravel Sanctum
- 📊 **Dashboard Analytics** - Revenue tracking, statistik harian, status lajur
- 🚗 **Vehicle Management** - Manajemen data kendaraan pelanggan
- 🧽 **Service Management** - Kelola jenis layanan cuci (Standard, Premium, Detail)
- 👥 **Staff Management** - Manajemen karyawan dan sistem komisi
- 📋 **Order Management** - Proses pesanan dari pending hingga completed
- 💰 **Payment Tracking** - Tracking pembayaran (Cash, QRIS, Transfer)

### **Web Interface**
- 🖥️ **Modern Dashboard** - Interface web responsive dengan Tailwind CSS
- 📱 **Mobile Responsive** - Optimized untuk desktop, tablet, dan mobile
- 🎨 **Material Design** - Menggunakan Material Symbols dan design system
- ⚡ **Real-time Updates** - Status updates dan notifications
- 📈 **Analytics & Reports** - Charts dan laporan performa bisnis
- 🔍 **Advanced Search** - Filter dan search untuk semua data

## 🛠 Tech Stack

- **Backend**: Laravel 11 + PHP 8.2
- **Frontend**: Laravel Blade + Tailwind CSS + Material Symbols
- **Database**: SQLite (development) / MySQL (production)
- **Authentication**: Laravel Sanctum (API) + Session (Web)
- **API**: RESTful API with JSON responses

## 📦 Installation

### **1. Clone & Install Dependencies**
```bash
git clone <repository-url>
cd carwashv2
composer install
```

### **2. Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

### **3. Database Setup**
```bash
php artisan migrate:fresh --seed
```

### **4. Run Development Server**
```bash
php artisan serve
```

**Web Interface**: http://localhost:8000  
**API Endpoint**: http://localhost:8000/api

## 👤 Default Users

- **Admin**: admin@carwash.com / password
- **Manager**: manager@carwash.com / password

## 🌐 Web Interface

### **Dashboard**
- Revenue analytics dengan perbandingan harian
- Statistik mobil terlayani dan antrian
- Tracking komisi karyawan dengan progress
- Aktivitas terkini dengan status real-time
- Status lajur cuci (occupied/available)

### **Order Management**
- List pesanan dengan filter dan search
- Form wizard untuk pesanan baru (3 langkah)
- Detail pesanan dengan timeline
- Update status dan pembayaran
- Print receipt functionality

### **Service & Staff Management**
- Grid view untuk layanan dan karyawan
- Performance analytics dan komisi tracking
- CRUD operations dengan modal forms

### **Reports & Analytics**
- Revenue charts dan service distribution
- Top performers ranking
- Payment method analysis
- Export ke PDF dan Excel

## 📡 API Documentation

Lihat file `API_DOCUMENTATION.md` untuk dokumentasi lengkap endpoint API.

### **Quick Start - API Test**

```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@carwash.com","password":"password"}'

# Get Dashboard (with token)
curl -X GET http://localhost:8000/api/dashboard \
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 🗄 Database Schema

### **Tables**
- `users` - Admin/Manager accounts dengan role-based access
- `services` - Jenis layanan cuci (Standard, Premium, Detail)
- `vehicles` - Data kendaraan pelangaan dengan license plate
- `staff` - Data karyawan dengan sistem komisi
- `wash_orders` - Pesanan cuci dengan status dan payment tracking
- `personal_access_tokens` - Sanctum API tokens

### **Key Relationships**
```
WashOrder belongsTo Vehicle, Service, Staff, User
Staff hasMany WashOrders (untuk komisi calculation)
Service hasMany WashOrders (untuk analytics)
User hasMany WashOrders (untuk audit trail)
```

## 🔗 API Endpoints Overview

```
# Authentication
POST   /api/login              # Login
POST   /api/logout             # Logout
GET    /api/me                 # User profile

# Dashboard & Analytics
GET    /api/dashboard          # Dashboard data

# Services
GET    /api/services           # List services
POST   /api/services           # Create service
GET    /api/services/{id}      # Show service
PUT    /api/services/{id}      # Update service
DELETE /api/services/{id}      # Delete service

# Wash Orders
GET    /api/wash-orders        # List orders
POST   /api/wash-orders        # Create order
GET    /api/wash-orders/{id}   # Show order
PATCH  /api/wash-orders/{id}/status   # Update status
PATCH  /api/wash-orders/{id}/payment  # Update payment

# Staff
GET    /api/staff              # List staff
```

## 🌐 Web Routes Overview

```
# Authentication
GET    /login                  # Login page
POST   /login                  # Process login
POST   /logout                 # Logout

# Dashboard
GET    /dashboard              # Main dashboard

# Orders
GET    /orders                 # List orders
GET    /orders/create          # Create order form
POST   /orders                 # Store order
GET    /orders/{id}            # Show order detail
PATCH  /orders/{id}/status     # Update order status
PATCH  /orders/{id}/payment    # Update payment

# Management
GET    /services               # Services management
GET    /staff                  # Staff management
GET    /reports                # Reports & analytics
```

## 🎨 Design System

### **Colors**
- **Primary**: Blue (#0051d5) - Actions dan navigation
- **Secondary**: Slate - Text dan borders
- **Success**: Green - Completed status
- **Warning**: Yellow - Pending status
- **Error**: Red - Cancelled status

### **Typography**
- **Font**: Inter (Google Fonts)
- **Headings**: Bold dengan optimized letter-spacing
- **Body**: Regular dengan line-height 1.5-1.6

### **Components**
- **Cards**: Rounded corners dengan subtle shadows
- **Buttons**: Consistent hover states
- **Forms**: Focus states dengan blue accent
- **Tables**: Hover effects dengan zebra striping

## 📱 Mobile Responsive

- **Breakpoints**: Mobile (<768px), Tablet (768-1024px), Desktop (>1024px)
- **Navigation**: Collapsible sidebar dengan hamburger menu
- **Touch**: Optimized button sizes dan touch targets
- **Performance**: Lazy loading dan optimized assets

## 🔧 Development

### **Running Tests**
```bash
php artisan test
```

### **Code Style**
```bash
./vendor/bin/pint
```

### **Asset Compilation**
```bash
npm install
npm run dev        # Development
npm run build      # Production
```

## 🚀 Deployment

### **Production Setup**
1. Set `APP_ENV=production` di `.env`
2. Configure database credentials
3. Run optimizations:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
4. Setup web server (Nginx/Apache)
5. Configure SSL certificate

### **Environment Variables**
```env
APP_NAME="Car Wash Management"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=carwash_db
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 📱 Flutter Integration

API ini dirancang untuk digunakan dengan aplikasi Flutter mobile:

```dart
// Login example
final response = await http.post(
  Uri.parse('$baseUrl/login'),
  headers: {'Content-Type': 'application/json'},
  body: json.encode({
    'email': 'admin@carwash.com',
    'password': 'password'
  })
);

// Authenticated requests
final token = loginResponse['data']['token'];
final dashboardResponse = await http.get(
  Uri.parse('$baseUrl/dashboard'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json'
  }
);
```

## 📚 Documentation

- `API_DOCUMENTATION.md` - Complete API reference
- `WEB_INTERFACE_GUIDE.md` - Web interface guide
- `database/` - Database schema dan seeders
- `tests/` - Test cases dan examples

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License.

---

**Built with ❤️ using Laravel, Tailwind CSS, and Material Design**
