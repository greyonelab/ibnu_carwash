# Car Wash API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication
API menggunakan Laravel Sanctum untuk authentication. Setelah login, gunakan token yang diterima di header:
```
Authorization: Bearer {your-token}
```

## Endpoints

### 1. Authentication

#### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "admin@carwash.com",
    "password": "password"
}
```

Response:
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Admin Car Wash",
            "email": "admin@carwash.com",
            "role": "admin",
            "is_active": true
        },
        "token": "1|abc123..."
    }
}
```

#### Register
```http
POST /api/register
Content-Type: application/json

{
    "name": "New User",
    "email": "user@example.com",
    "password": "password",
    "password_confirmation": "password",
    "role": "admin"
}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

#### Get User Profile
```http
GET /api/me
Authorization: Bearer {token}
```

### 2. Dashboard

#### Get Dashboard Data
```http
GET /api/dashboard
Authorization: Bearer {token}
```

Response:
```json
{
    "success": true,
    "data": {
        "revenue": {
            "today": 500000,
            "yesterday": 450000,
            "change_percentage": 11.1,
            "cash_indicator": 420.00
        },
        "cars": {
            "served_today": 15,
            "in_queue": 3
        },
        "commission": {
            "today_total": 75000,
            "target_percentage": 65
        },
        "recent_activities": [...],
        "bay_status": [...],
        "shift_progress": {
            "current_hours": 6,
            "total_hours": 8,
            "percentage": 75
        }
    }
}
```

### 3. Services

#### Get All Services
```http
GET /api/services
Authorization: Bearer {token}
```

#### Create Service
```http
POST /api/services
Authorization: Bearer {token}
Content-Type: application/json

{
    "name": "Cuci Express",
    "description": "Cuci cepat 15 menit",
    "price": 25000,
    "duration_minutes": 15,
    "type": "standard"
}
```

### 4. Wash Orders

#### Get All Orders
```http
GET /api/wash-orders
Authorization: Bearer {token}
```

Query parameters:
- `status`: pending, in_progress, completed, cancelled
- `date`: YYYY-MM-DD format

#### Create New Order
```http
POST /api/wash-orders
Authorization: Bearer {token}
Content-Type: application/json

{
    "license_plate": "B 1234 ABC",
    "vehicle_type": "SUV",
    "vehicle_model": "Honda CR-V",
    "vehicle_color": "White",
    "service_id": 2,
    "staff_id": 1,
    "additional_fee": 15000,
    "notes": "Mobil sangat kotor"
}
```

#### Update Order Status
```http
PATCH /api/wash-orders/{id}/status
Authorization: Bearer {token}
Content-Type: application/json

{
    "status": "in_progress"
}
```

#### Update Payment Status
```http
PATCH /api/wash-orders/{id}/payment
Authorization: Bearer {token}
Content-Type: application/json

{
    "payment_status": "paid",
    "payment_method": "cash"
}
```

### 5. Staff

#### Get All Staff
```http
GET /api/staff
Authorization: Bearer {token}
```

## Default Users
- **Admin**: admin@carwash.com / password
- **Manager**: manager@carwash.com / password

## Error Responses
```json
{
    "success": false,
    "message": "Error message",
    "errors": {
        "field": ["Validation error message"]
    }
}
```

## Status Codes
- 200: Success
- 201: Created
- 400: Bad Request
- 401: Unauthorized
- 404: Not Found
- 422: Validation Error
- 500: Server Error