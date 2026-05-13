# WashManager Pro - Mobile App

A Flutter mobile application for car wash management system that integrates with the Laravel backend API.

## Features

- **Authentication**: Secure login with token-based authentication
- **Dashboard**: Overview of orders, revenue, and statistics
- **Order Management**: Create, view, and manage wash orders
- **Service Selection**: Choose from available car wash services
- **Staff Assignment**: Assign staff members to orders
- **Payment Tracking**: Track payment status and methods
- **Real-time Updates**: Live data synchronization with backend
- **Responsive Design**: Material Design 3 with modern UI

## Screenshots

The app includes:
- Splash screen with app branding
- Login screen with demo credentials
- Dashboard with statistics and quick actions
- Orders list with filtering and search
- Create order form with service selection
- Order details with status tracking

## Technical Stack

- **Framework**: Flutter 3.9.2+
- **State Management**: Provider pattern
- **HTTP Client**: Dio for API communication
- **Storage**: Flutter Secure Storage for tokens
- **UI**: Material Design 3
- **Architecture**: Clean architecture with providers and services

## Project Structure

```
lib/
├── main.dart                 # App entry point
├── models/                   # Data models
│   ├── user.dart
│   ├── wash_order.dart
│   ├── service.dart
│   ├── staff.dart
│   └── vehicle.dart
├── providers/                # State management
│   ├── auth_provider.dart
│   └── order_provider.dart
├── services/                 # API services
│   └── api_service.dart
└── screens/                  # UI screens
    ├── login_screen.dart
    ├── dashboard_screen.dart
    ├── orders_screen.dart
    └── create_order_screen.dart
```

## API Integration

The app connects to the Laravel backend at `http://127.0.0.1:8000/api` with the following endpoints:

- `POST /login` - User authentication
- `POST /logout` - User logout
- `GET /dashboard` - Dashboard statistics
- `GET /services` - Available services
- `GET /staff` - Staff members
- `GET /orders` - Order list with filters
- `POST /orders` - Create new order
- `PATCH /orders/{id}/status` - Update order status
- `PATCH /orders/{id}/payment` - Update payment status

## Setup Instructions

### Prerequisites

1. **Flutter SDK**: Install Flutter 3.9.2 or later
2. **Android Studio**: For Android development
3. **VS Code**: Recommended editor with Flutter extensions
4. **Laravel Backend**: Ensure the backend API is running

### Installation

1. **Navigate to the Flutter project**:
   ```bash
   cd carwashapk/washmanager_mobile
   ```

2. **Install dependencies**:
   ```bash
   flutter pub get
   ```

3. **Generate JSON serialization code**:
   ```bash
   flutter packages pub run build_runner build
   ```

4. **Configure API endpoint** (if needed):
   Edit `lib/services/api_service.dart` and update the `baseUrl`:
   ```dart
   static const String baseUrl = 'http://YOUR_API_URL/api';
   ```

### Running the App

1. **Start the Laravel backend** (in the main project directory):
   ```bash
   php artisan serve
   ```

2. **Run the Flutter app**:
   ```bash
   flutter run
   ```

   Or for specific platforms:
   ```bash
   flutter run -d android    # Android
   flutter run -d chrome     # Web (for testing)
   ```

### Building for Production

1. **Android APK**:
   ```bash
   flutter build apk --release
   ```

2. **Android App Bundle**:
   ```bash
   flutter build appbundle --release
   ```

## Demo Credentials

Use these credentials to test the app:
- **Email**: admin@carwash.com
- **Password**: password

## Configuration

### Network Configuration

For Android, ensure network security config allows HTTP connections to your local server. The app is configured to connect to `http://127.0.0.1:8000` by default.

### Permissions

The app requires the following permissions:
- Internet access for API communication
- Storage access for secure token storage

## Development

### Code Generation

When modifying model classes, regenerate the JSON serialization code:

```bash
flutter packages pub run build_runner build --delete-conflicting-outputs
```

### State Management

The app uses the Provider pattern for state management:
- `AuthProvider`: Handles authentication state
- `OrderProvider`: Manages orders, services, and staff data

### API Service

The `ApiService` class handles all HTTP communication with automatic token management and error handling.

## Troubleshooting

### Common Issues

1. **Build Errors**: Run `flutter clean` and `flutter pub get`
2. **API Connection**: Ensure the Laravel backend is running and accessible
3. **Token Issues**: Clear app data or reinstall to reset stored tokens
4. **NDK Issues**: Delete and re-download Android NDK if build fails

### Debug Mode

Run the app in debug mode for detailed logging:
```bash
flutter run --debug
```

### Network Debugging

Use Flutter Inspector and network logs to debug API communication issues.

## Contributing

1. Follow Flutter coding conventions
2. Use meaningful commit messages
3. Test on both Android and iOS (if available)
4. Update documentation for new features

## License

This project is part of the WashManager Pro car wash management system.