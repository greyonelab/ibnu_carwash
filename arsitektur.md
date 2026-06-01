# Arsitektur Sistem CarWash v2

## Gambaran Umum

Sistem ini terdiri dari dua lapisan utama yang berjalan di atas satu backend Laravel:

1. **Web App** — antarmuka berbasis browser (Blade + Bootstrap) untuk admin/manager
2. **Mobile App** — aplikasi Flutter yang berkomunikasi via REST API

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER                             │
│                                                                 │
│   ┌──────────────────────┐      ┌──────────────────────────┐    │
│   │   Web Browser        │      │   Flutter Mobile App     │    │
│   │   (Admin/Manager)    │      │   (Operator/Staff)       │    │
│   │                      │      │                          │    │
│   │  - Dashboard         │      │  - Login                 │    │
│   │  - Kelola Order      │      │  - Buat Order            │    │
│   │  - Kelola Staff      │      │  - Lihat Order           │    │
│   │  - Kelola Layanan    │      │  - Dashboard             │    │
│   │  - Laporan & Export  │      │  - Autocomplete Plat     │    │
│   │  - Komisi            │      │                          │    │
│   │  - Queue Display     │      │                          │    │
│   └──────────┬───────────┘      └────────────┬─────────────┘    │
└──────────────┼──────────────────────────────┼───────────────────┘
               │  HTTP (Blade/Form)            │  HTTP REST + JSON
               │                              │  Bearer Token (Sanctum)
               ▼                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                     LARAVEL BACKEND                             │
│                                                                 │
│  ┌─────────────────────────┐  ┌──────────────────────────────┐  │
│  │   Web Routes            │  │   API Routes                 │  │
│  │   /routes/web.php       │  │   /routes/api.php            │  │
│  │                         │  │                              │  │
│  │  Middleware: auth       │  │  Middleware: auth:sanctum    │  │
│  └────────────┬────────────┘  └──────────────┬───────────────┘  │
│               │                              │                  │
│               ▼                              ▼                  │
│  ┌─────────────────────────┐  ┌──────────────────────────────┐  │
│  │  Web Controllers        │  │  API Controllers             │  │
│  │  app/Http/Controllers/  │  │  app/Http/Controllers/Api/   │  │
│  │  Web/                   │  │                              │  │
│  │                         │  │  - AuthController            │  │
│  │  - AuthController       │  │  - DashboardController       │  │
│  │  - DashboardController  │  │  - ServiceController         │  │
│  │  - WashOrderController  │  │  - WashOrderController       │  │
│  │  - StaffController      │  │  - VehicleController         │  │
│  │  - ServiceController    │  │  - WashLaneController        │  │
│  │  - WashLaneController   │  │  - CommissionController      │  │
│  │  - CommissionController │  │                              │  │
│  │  - ReportsController    │  │                              │  │
│  │  - SearchController     │  │                              │  │
│  │  - ReceiptController    │  │                              │  │
│  │  - ExportController     │  │                              │  │
│  └────────────┬────────────┘  └──────────────┬───────────────┘  │
│               │                              │                  │
│               └──────────────┬───────────────┘                  │
│                              ▼                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │                    MODEL LAYER (Eloquent ORM)            │   │
│  │                                                          │   │
│  │   User          Staff         Service       Vehicle      │   │
│  │   WashOrder     WashLane      CommissionSetting          │   │
│  └──────────────────────────────┬───────────────────────────┘   │
│                                 │                               │
│  ┌──────────────────────────────┼───────────────────────────┐   │
│  │              EXPORT LAYER    │                           │   │
│  │   app/Exports/               │                           │   │
│  │   - OrdersExport.php         │                           │   │
│  │   - ReportsExport.php        │                           │   │
│  └──────────────────────────────┼───────────────────────────┘   │
└─────────────────────────────────┼───────────────────────────────┘
                                  │
                                  ▼
┌─────────────────────────────────────────────────────────────────┐
│                      DATABASE LAYER (MySQL)                     │
│                                                                 │
│   users              staff             services                 │
│   vehicles           wash_orders       wash_lanes               │
│   wash_order_staff   commission_settings                        │
│   personal_access_tokens   cache   jobs   notifications         │
└─────────────────────────────────────────────────────────────────┘
```

---

## Struktur Database (ERD)

```
users
  id, name, email, password, role (admin|manager), is_active

staff
  id, name, phone, position, commission_rate, is_active

services
  id, name, description, price, duration_minutes, type, is_active

vehicles
  id, license_plate, type, model, color

wash_lanes
  id, name, type, is_active, max_queue, description

wash_orders
  id, order_number
  vehicle_id  ──FK──▶ vehicles.id
  service_id  ──FK──▶ services.id
  staff_id    ──FK──▶ staff.id (nullable, staff utama)
  user_id     ──FK──▶ users.id
  wash_lane_id──FK──▶ wash_lanes.id (nullable)
  staff_ids (JSON)
  base_price, additional_fee, total_price
  status (pending|in_progress|completed|cancelled)
  payment_status (unpaid|paid)
  payment_method (cash|qris|transfer)
  queue_position, queued_at, lane_started_at
  started_at, completed_at, notes

wash_order_staff  [pivot — many-to-many]
  wash_order_id ──FK──▶ wash_orders.id
  staff_id      ──FK──▶ staff.id
  commission_percentage, commission_amount

commission_settings
  id, name, percentage, description, is_active
```

---

## Alur Request

### Web (Browser → Blade)

```
Browser
  │
  ├─ GET /login          → AuthController@showLogin → view auth/login
  ├─ POST /login         → AuthController@login     → session auth
  │
  ├─ GET /dashboard      → DashboardController@index → view dashboard/index
  ├─ GET /orders         → WashOrderController@index → view orders/index
  ├─ POST /orders        → WashOrderController@store → redirect
  ├─ PATCH /orders/{id}/status → updateStatus
  ├─ PATCH /orders/{id}/payment → updatePayment
  │
  ├─ GET /queue-display  → QueueDisplayController   → view (public, no auth)
  └─ GET /reports        → ReportsController@index  → export Excel/PDF
```

### Mobile (Flutter → REST API)

```
Flutter App
  │
  ├─ POST /api/login     → Api\AuthController@login  → token Sanctum
  │
  ├─ GET  /api/dashboard → DashboardController       → JSON stats
  ├─ GET  /api/services  → ServiceController         → JSON list
  ├─ GET  /api/wash-orders → WashOrderController     → JSON list
  ├─ POST /api/wash-orders → WashOrderController@store → JSON order baru
  ├─ PATCH /api/wash-orders/{id}/status → updateStatus
  │
  ├─ GET  /api/vehicles/search?q=... → VehicleController@search
  └─ GET  /api/staff     → Staff::where(is_active, true)
```

---

## Komponen Flutter (Mobile)

```
lib/
├── main.dart                    ← entry point, Provider setup
├── services/
│   └── api_service.dart         ← HTTP client, token management
├── providers/
│   └── order_provider.dart      ← state management (Provider)
├── models/
│   ├── wash_order.dart
│   └── user.dart
├── screens/
│   ├── login_screen.dart
│   ├── dashboard_screen.dart
│   ├── orders_screen.dart
│   └── create_order_screen.dart
└── widgets/
    └── license_plate_autocomplete.dart  ← autocomplete plat nomor
```

---

## Autentikasi

| Klien  | Metode              | Middleware         |
|--------|---------------------|--------------------|
| Web    | Session (Laravel)   | `auth`             |
| Mobile | Bearer Token        | `auth:sanctum`     |

- Token dibuat saat login via `/api/login`, disimpan di Flutter local storage
- Web menggunakan session cookie standar Laravel

---

## Fitur Utama per Modul

| Modul            | Fitur                                                        |
|------------------|--------------------------------------------------------------|
| **Order**        | CRUD, update status, update payment, cetak struk, queue lane |
| **Staff**        | CRUD, komisi per order, multi-staff per order (pivot)        |
| **Layanan**      | CRUD, harga, durasi, tipe kendaraan                          |
| **Kendaraan**    | Auto-create saat order, search by plat, riwayat cuci         |
| **Wash Lane**    | Manajemen jalur, antrian otomatis, max queue per jalur       |
| **Komisi**       | Setting persentase staff vs owner, kalkulasi otomatis        |
| **Laporan**      | Filter tanggal, export Excel (maatwebsite/excel)             |
| **Queue Display**| Halaman publik real-time tanpa login                         |
