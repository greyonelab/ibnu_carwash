# Car Wash Web Interface Guide

Interface web untuk sistem manajemen car wash yang dibangun dengan Laravel Blade dan Tailwind CSS.

## 🚀 Fitur Web Interface

### 1. **Authentication System**
- Login page dengan design modern
- Session-based authentication
- Role-based access control
- Auto-redirect setelah login

### 2. **Dashboard**
- **Revenue Analytics**: Pendapatan harian dengan perbandingan kemarin
- **Car Statistics**: Jumlah mobil terlayani dan antrian
- **Commission Tracking**: Total komisi karyawan dengan progress bar
- **Recent Activities**: Tabel aktivitas terkini dengan status real-time
- **Bay Status**: Status lajur cuci (occupied/available)
- **Quick Actions**: Tombol aksi cepat untuk operasi umum

### 3. **Order Management**
- **List Orders**: Tabel pesanan dengan filter dan search
- **Create Order**: Form wizard 3 langkah (Kendaraan → Detail → Konfirmasi)
- **Order Detail**: View lengkap dengan timeline dan update status
- **Status Updates**: Update status pesanan dan pembayaran
- **Payment Tracking**: Tracking metode pembayaran (Cash, QRIS, Transfer)

### 4. **Service Management**
- **Service Grid**: Tampilan card untuk setiap layanan
- **Service Types**: Standard, Premium, Detail dengan icon berbeda
- **Price Management**: Kelola harga dan durasi layanan
- **Active/Inactive**: Toggle status layanan

### 5. **Staff Management**
- **Staff Cards**: Profil karyawan dengan foto dan info
- **Commission Tracking**: Persentase komisi dan total earning
- **Performance Stats**: Statistik pesanan dan komisi bulanan
- **Contact Info**: Nomor telepon dan posisi

### 6. **Reports & Analytics**
- **Revenue Charts**: Grafik pendapatan harian
- **Service Distribution**: Pie chart distribusi layanan
- **Top Performers**: Ranking karyawan terbaik
- **Payment Methods**: Analisis metode pembayaran
- **Export Options**: PDF dan Excel export

## 🎨 Design System

### **Color Palette**
- **Primary**: Blue (#0051d5) - Untuk aksi utama dan navigasi
- **Secondary**: Slate - Untuk teks dan border
- **Success**: Green - Untuk status completed dan positive metrics
- **Warning**: Yellow - Untuk status pending
- **Error**: Red - Untuk status cancelled dan negative metrics

### **Typography**
- **Font**: Inter (Google Fonts)
- **Headings**: Bold weights dengan letter-spacing optimized
- **Body**: Regular weight dengan line-height 1.5-1.6
- **Labels**: Semibold dengan uppercase dan tracking

### **Components**
- **Cards**: Rounded corners dengan subtle shadows
- **Buttons**: Consistent padding dan hover states
- **Forms**: Focus states dengan blue accent
- **Tables**: Zebra striping dengan hover effects
- **Status Badges**: Color-coded dengan rounded pills

## 📱 Responsive Design

### **Breakpoints**
- **Mobile**: < 768px - Stack layout, collapsible sidebar
- **Tablet**: 768px - 1024px - Adjusted grid columns
- **Desktop**: > 1024px - Full layout dengan sidebar

### **Mobile Features**
- Hamburger menu untuk sidebar
- Touch-friendly button sizes
- Optimized table scrolling
- Responsive grid layouts

## 🔧 Technical Implementation

### **Frontend Stack**
- **Laravel Blade**: Server-side templating
- **Tailwind CSS**: Utility-first CSS framework
- **Material Symbols**: Google's icon system
- **Vanilla JavaScript**: Untuk interaktivity

### **Key Features**
- **Real-time Updates**: Auto-refresh untuk status changes
- **Form Validation**: Client dan server-side validation
- **Search & Filter**: Advanced filtering untuk semua list
- **Pagination**: Laravel pagination dengan Tailwind styling
- **Flash Messages**: Success/error notifications

## 🚦 Navigation Structure

```
Dashboard (/)
├── Pesanan Cuci (/orders)
│   ├── List Pesanan (/orders)
│   ├── Buat Pesanan (/orders/create)
│   └── Detail Pesanan (/orders/{id})
├── Layanan (/services)
├── Karyawan (/staff)
└── Laporan (/reports)
```

## 📊 Dashboard Widgets

### **Revenue Card**
- Total penjualan hari ini
- Persentase perubahan vs kemarin
- Indikator kas tersedia

### **Cars Served Card**
- Jumlah mobil terlayani hari ini
- Jumlah mobil dalam antrian
- Visual indicator untuk queue

### **Commission Card**
- Total komisi karyawan hari ini
- Progress bar target harian
- Persentase pencapaian

### **Recent Activities Table**
- 10 aktivitas terkini
- Status real-time dengan color coding
- Link ke detail pesanan

### **Bay Status Panel**
- Status 3 lajur cuci
- Estimasi waktu selesai
- Visual indicator (red/green dots)

## 🔄 Workflow Pesanan

### **1. Create Order**
```
Step 1: Vehicle Info
├── License Plate (required)
├── Vehicle Type (dropdown)
├── Model/Brand (optional)
└── Color (optional)

Step 2: Service Selection
├── Service Cards dengan pricing
├── Staff Assignment
├── Additional Fees
└── Notes

Step 3: Confirmation
├── Price Breakdown
├── Summary Review
└── Submit Order
```

### **2. Order Status Flow**
```
Pending → In Progress → Completed
    ↓         ↓           ↓
  Cancel   Cancel    Payment
```

### **3. Payment Flow**
```
Unpaid → Paid
  ↓       ↓
Method  Receipt
```

## 🎯 User Experience Features

### **Quick Actions**
- Floating action buttons untuk aksi umum
- Keyboard shortcuts untuk power users
- Bulk operations untuk multiple items

### **Smart Defaults**
- Auto-fill vehicle info dari database
- Default staff assignment
- Suggested pricing berdasarkan vehicle type

### **Visual Feedback**
- Loading states untuk async operations
- Success/error animations
- Progress indicators untuk multi-step forms

## 🔐 Security Features

### **Authentication**
- Session-based login dengan remember me
- CSRF protection pada semua forms
- Auto-logout setelah inactivity

### **Authorization**
- Role-based access control
- Route protection dengan middleware
- Feature-level permissions

### **Data Validation**
- Server-side validation untuk semua inputs
- Client-side validation untuk UX
- Sanitization untuk XSS prevention

## 📈 Performance Optimizations

### **Frontend**
- Lazy loading untuk images
- Minified CSS dan JavaScript
- Optimized font loading

### **Backend**
- Database query optimization
- Eager loading untuk relationships
- Caching untuk static data

### **Assets**
- CDN untuk Tailwind CSS
- Compressed images
- Optimized icon fonts

## 🛠 Development Setup

### **Requirements**
- PHP 8.2+
- Laravel 11
- Node.js (untuk asset compilation)
- Modern browser dengan ES6 support

### **Installation**
```bash
# Install dependencies
composer install

# Setup database
php artisan migrate:fresh --seed

# Start development server
php artisan serve
```

### **Default Login**
- **Admin**: admin@carwash.com / password
- **Manager**: manager@carwash.com / password

## 🎨 Customization

### **Branding**
- Logo: Update di layout header
- Colors: Modify Tailwind config
- Typography: Change font imports

### **Features**
- Add new menu items di sidebar
- Create new pages dengan extending layout
- Customize dashboard widgets

### **Styling**
- Override Tailwind classes
- Add custom CSS di app.css
- Modify component templates

## 📱 Mobile Optimization

### **Touch Interactions**
- Minimum 44px touch targets
- Swipe gestures untuk navigation
- Pull-to-refresh untuk data updates

### **Performance**
- Optimized images untuk mobile
- Reduced JavaScript bundle size
- Fast loading dengan skeleton screens

### **UX Adaptations**
- Bottom navigation untuk mobile
- Simplified forms dengan fewer fields
- Modal dialogs untuk detail views