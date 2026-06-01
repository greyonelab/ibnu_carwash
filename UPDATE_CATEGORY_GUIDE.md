# Panduan Update Fitur Kategori Layanan

## Perubahan yang Dilakukan

Fitur layanan service sekarang memiliki 3 kategori:
- **Mobil** - untuk layanan cuci mobil
- **Motor** - untuk layanan cuci motor  
- **Lainnya** - untuk layanan cuci kendaraan lainnya

Layanan akan muncul sesuai dengan kategori yang dipilih.

## File yang Diubah

### Backend (Laravel)

1. **Migration**
   - `database/migrations/2026_06_01_045848_add_category_to_services_table.php`
   - Menambahkan kolom `category` dengan enum('mobil', 'motor', 'lainnya')

2. **Model**
   - `app/Models/Service.php`
   - Menambahkan 'category' ke $fillable

3. **Controllers**
   - `app/Http/Controllers/Web/ServiceController.php`
     - Update validasi untuk menambahkan field 'type' dan 'category'
     - 'type' = standard/premium/detail (tipe layanan)
     - 'category' = mobil/motor/lainnya (kategori kendaraan)
   
   - `app/Http/Controllers/Api/ServiceController.php`
     - Menambahkan filter berdasarkan category di method index()
     - Update validasi untuk menambahkan field 'category'

4. **Views**
   - `resources/views/services/create.blade.php`
     - Menambahkan dropdown "Kategori Kendaraan" (mobil/motor/lainnya)
     - Menambahkan dropdown "Tipe Layanan" (standard/premium/detail)
   
   - `resources/views/services/edit.blade.php`
     - Menambahkan dropdown "Kategori Kendaraan" (mobil/motor/lainnya)
     - Menambahkan dropdown "Tipe Layanan" (standard/premium/detail)
   
   - `resources/views/services/index.blade.php`
     - Menampilkan kategori pada card layanan
   
   - `resources/views/orders/create.blade.php`
     - Menambahkan dropdown "Kategori Kendaraan" di form kendaraan
     - Menambahkan filter otomatis untuk layanan berdasarkan kategori yang dipilih
     - Layanan akan di-filter secara real-time saat kategori dipilih

### Frontend (Flutter)

1. **Model**
   - `carwashapk/washmanager_mobile/lib/models/service.dart`
   - Menambahkan field `category` (nullable)

2. **Screen**
   - `carwashapk/washmanager_mobile/lib/screens/create_order_screen.dart`
   - Menambahkan filter otomatis layanan berdasarkan jenis kendaraan yang dipilih
   - Menampilkan badge kategori pada setiap layanan
   - Menampilkan pesan jika tidak ada layanan untuk kategori yang dipilih

## Cara Menjalankan Update

### 1. Jalankan Migration

Pastikan database MySQL sudah berjalan (XAMPP), kemudian jalankan:

```bash
cd c:\xampp\htdocs\carwashv2
php artisan migrate
```

### 2. Update Data Layanan yang Ada

Setelah migration berhasil, update data layanan yang sudah ada untuk menambahkan kategori:

```sql
-- Update layanan yang ada dengan kategori default 'mobil'
UPDATE services SET category = 'mobil' WHERE category IS NULL;

-- Atau update manual sesuai kebutuhan
UPDATE services SET category = 'motor' WHERE name LIKE '%Motor%';
UPDATE services SET category = 'lainnya' WHERE name LIKE '%Truk%' OR name LIKE '%Bus%';
```

### 3. Update Flutter Model

Regenerate file service.g.dart:

```bash
cd c:\xampp\htdocs\carwashv2\carwashapk\washmanager_mobile
flutter pub run build_runner build --delete-conflicting-outputs
```

### 4. Test Aplikasi

#### Web Interface:
1. Buka http://localhost/carwashv2/public/services
2. Klik "Tambah Layanan"
3. Pastikan ada dropdown "Kategori Kendaraan" dan "Tipe Layanan"
4. Buat layanan baru dengan kategori yang berbeda

5. Buka http://localhost/carwashv2/public/orders/create
6. Pilih "Kategori Kendaraan"
7. Pastikan layanan yang muncul sesuai dengan kategori yang dipilih

#### Mobile App:
1. Jalankan aplikasi Flutter
2. Buat order baru
3. Pilih jenis kendaraan (Motor/Mobil)
4. Pastikan layanan yang muncul sesuai dengan jenis kendaraan
5. Pastikan ada badge kategori pada setiap layanan

## API Endpoint

### Get Services dengan Filter Kategori

```
GET /api/services?category=mobil
GET /api/services?category=motor
GET /api/services?category=lainnya
```

Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Cuci Mobil Standard",
      "description": "Cuci eksterior mobil",
      "price": 25000,
      "duration_minutes": 30,
      "type": "standard",
      "category": "mobil",
      "is_active": true
    }
  ]
}
```

## Struktur Database

### Tabel: services

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar(255) | Nama layanan |
| description | text | Deskripsi layanan |
| price | decimal(10,2) | Harga layanan |
| duration_minutes | int | Durasi dalam menit |
| type | enum | standard/premium/detail |
| **category** | **enum** | **mobil/motor/lainnya** (NEW) |
| is_active | boolean | Status aktif |
| created_at | timestamp | Waktu dibuat |
| updated_at | timestamp | Waktu diupdate |

## Mapping Kategori

### Web Form (orders/create)
- Kategori "mobil" → Menampilkan layanan dengan category='mobil'
- Kategori "motor" → Menampilkan layanan dengan category='motor'
- Kategori "lainnya" → Menampilkan layanan dengan category='lainnya'

### Flutter App
- Jenis kendaraan "Mobil" → Filter layanan category='mobil'
- Jenis kendaraan "Motor" → Filter layanan category='motor'
- Jenis kendaraan lainnya → Filter layanan category='lainnya'

## Troubleshooting

### Layanan tidak muncul setelah pilih kategori
- Pastikan layanan sudah memiliki kategori yang sesuai
- Cek di database: `SELECT * FROM services WHERE category IS NULL`
- Update kategori yang NULL

### Error saat create/update service
- Pastikan migration sudah dijalankan
- Cek kolom 'category' sudah ada di tabel services
- Pastikan validasi di controller sudah benar

### Flutter error setelah update model
- Jalankan: `flutter pub run build_runner build --delete-conflicting-outputs`
- Restart aplikasi Flutter
- Clear cache: `flutter clean && flutter pub get`

## Catatan Penting

1. **Backward Compatibility**: Layanan lama yang belum memiliki kategori akan default ke 'mobil'
2. **Validasi**: Kategori wajib diisi saat create/update layanan
3. **Filter Otomatis**: Layanan akan otomatis di-filter berdasarkan kategori kendaraan yang dipilih
4. **API**: API mendukung filter kategori melalui query parameter `?category=`

## Contoh Data Seeder

Jika ingin menambahkan data sample, update DatabaseSeeder.php:

```php
Service::create([
    'name' => 'Cuci Motor Standard',
    'description' => 'Cuci eksterior motor',
    'price' => 15000,
    'duration_minutes' => 20,
    'type' => 'standard',
    'category' => 'motor',
    'is_active' => true,
]);

Service::create([
    'name' => 'Cuci Mobil Premium',
    'description' => 'Cuci eksterior + interior mobil',
    'price' => 50000,
    'duration_minutes' => 60,
    'type' => 'premium',
    'category' => 'mobil',
    'is_active' => true,
]);

Service::create([
    'name' => 'Cuci Truk',
    'description' => 'Cuci eksterior truk/bus',
    'price' => 100000,
    'duration_minutes' => 90,
    'type' => 'standard',
    'category' => 'lainnya',
    'is_active' => true,
]);
```

Kemudian jalankan:
```bash
php artisan db:seed
```
