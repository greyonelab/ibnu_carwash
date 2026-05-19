# Flutter Command Guide - WashManager Pro Mobile App

Panduan lengkap perintah dan penjelasan untuk mengembangkan aplikasi mobile Flutter WashManager Pro.

## 📋 Daftar Isi
- [Setup Awal Flutter](#setup-awal-flutter)
- [Project Creation & Setup](#project-creation--setup)
- [Development Commands](#development-commands)
- [Build & Release Commands](#build--release-commands)
- [Debugging & Testing](#debugging--testing)
- [Package Management](#package-management)
- [Platform Specific Commands](#platform-specific-commands)
- [Performance & Analysis](#performance--analysis)
- [Deployment Commands](#deployment-commands)

---

## 🚀 Setup Awal Flutter

### 1. **Instalasi Flutter SDK**
```bash
# Download Flutter SDK dari https://flutter.dev/docs/get-started/install

# Tambahkan Flutter ke PATH (Linux/Mac)
export PATH="$PATH:`pwd`/flutter/bin"

# Tambahkan Flutter ke PATH (Windows)
# Tambahkan C:\flutter\bin ke System PATH

# Verifikasi instalasi
flutter --version
flutter doctor
```
**Penjelasan:** Menginstall Flutter SDK dan memverifikasi setup environment.

### 2. **Setup Development Environment**
```bash
# Check semua requirements
flutter doctor

# Accept Android licenses
flutter doctor --android-licenses

# Install missing dependencies
# - Android Studio
# - VS Code dengan Flutter extension
# - Xcode (untuk iOS development di Mac)
```
**Penjelasan:** Memastikan semua tools development sudah terinstall dengan benar.

### 3. **Device Setup**
```bash
# List available devices
flutter devices

# Enable developer mode di Android device
# Enable USB debugging

# iOS Simulator (Mac only)
open -a Simulator

# Android Emulator
flutter emulators
flutter emulators --launch <emulator_id>
```
**Penjelasan:** Setup device untuk testing dan development.

---

## 📱 Project Creation & Setup

### 1. **Create New Flutter Project**
```bash
# Create new Flutter project
flutter create washmanager_mobile

# Create with specific package name
flutter create --org com.washmanager washmanager_mobile

# Create with specific platforms
flutter create --platforms android,ios washmanager_mobile

# Navigate to project directory
cd washmanager_mobile
```
**Penjelasan:** Membuat project Flutter baru untuk aplikasi WashManager Pro.

### 2. **Project Structure Setup**
```bash
# Create folder structure
mkdir lib/models
mkdir lib/services
mkdir lib/screens
mkdir lib/widgets
mkdir lib/utils
mkdir lib/providers
mkdir assets/images
mkdir assets/fonts

# Create main files
touch lib/models/wash_order.dart
touch lib/models/service.dart
touch lib/models/vehicle.dart
touch lib/services/api_service.dart
touch lib/screens/login_screen.dart
touch lib/screens/dashboard_screen.dart
touch lib/screens/order_screen.dart
```
**Penjelasan:** Membuat struktur folder yang terorganisir untuk project.

### 3. **Dependencies Setup**
```bash
# Add common dependencies to pubspec.yaml
flutter pub add http
flutter pub add provider
flutter pub add shared_preferences
flutter pub add flutter_secure_storage
flutter pub add dio
flutter pub add json_annotation
flutter pub add intl
flutter pub add flutter_launcher_icons
flutter pub add flutter_native_splash

# Add dev dependencies
flutter pub add --dev build_runner
flutter pub add --dev json_serializable
flutter pub add --dev flutter_test
```
**Penjelasan:** Menambahkan package yang diperlukan untuk aplikasi car wash.

---

## 💻 Development Commands

### 1. **Running the App**
```bash
# Run in debug mode
flutter run

# Run on specific device
flutter run -d <device_id>

# Run with hot reload enabled (default)
flutter run --hot

# Run in release mode
flutter run --release

# Run in profile mode (for performance testing)
flutter run --profile
```
**Penjelasan:** Menjalankan aplikasi dalam berbagai mode development.

### 2. **Hot Reload & Hot Restart**
```bash
# Hot reload (dalam flutter run session)
# Press 'r' in terminal

# Hot restart (dalam flutter run session)
# Press 'R' in terminal

# Quit app (dalam flutter run session)
# Press 'q' in terminal
```
**Penjelasan:** Menggunakan fitur hot reload untuk development yang cepat.

### 3. **Code Generation**
```bash
# Generate code untuk JSON serialization
flutter packages pub run build_runner build

# Watch for changes dan auto-generate
flutter packages pub run build_runner watch

# Clean generated files
flutter packages pub run build_runner clean
```
**Penjelasan:** Generate code otomatis untuk model classes dan serialization.

---

## 🔨 Build & Release Commands

### 1. **Android Build**
```bash
# Build APK (debug)
flutter build apk

# Build APK (release)
flutter build apk --release

# Build App Bundle (recommended for Play Store)
flutter build appbundle --release

# Build APK for specific architecture
flutter build apk --target-platform android-arm64

# Build split APKs
flutter build apk --split-per-abi
```
**Penjelasan:** Build aplikasi untuk platform Android dalam berbagai format.

### 2. **iOS Build (Mac only)**
```bash
# Build iOS app
flutter build ios

# Build iOS app for release
flutter build ios --release

# Build IPA file
flutter build ipa

# Build for simulator
flutter build ios --simulator
```
**Penjelasan:** Build aplikasi untuk platform iOS.

### 3. **Web Build**
```bash
# Build for web
flutter build web

# Build for web with specific renderer
flutter build web --web-renderer canvaskit
flutter build web --web-renderer html

# Serve web build locally
flutter build web && cd build/web && python -m http.server 8080
```
**Penjelasan:** Build aplikasi untuk platform web.

---

## 🐛 Debugging & Testing

### 1. **Debugging Commands**
```bash
# Run with debugging enabled
flutter run --debug

# Attach debugger to running app
flutter attach

# Enable debugging in VS Code
# F5 atau Run > Start Debugging

# Flutter Inspector
# Available in VS Code Flutter extension
# Atau buka di browser: http://localhost:9100
```
**Penjelasan:** Tools untuk debugging aplikasi Flutter.

### 2. **Logging & Analysis**
```bash
# View logs
flutter logs

# Analyze code
flutter analyze

# Format code
flutter format .
flutter format lib/

# Check for outdated packages
flutter pub outdated
```
**Penjelasan:** Analisis kode dan monitoring logs.

### 3. **Testing Commands**
```bash
# Run all tests
flutter test

# Run specific test file
flutter test test/widget_test.dart

# Run tests with coverage
flutter test --coverage

# Integration tests
flutter drive --target=test_driver/app.dart

# Generate test coverage report
genhtml coverage/lcov.info -o coverage/html
```
**Penjelasan:** Menjalankan berbagai jenis testing.

---

## 📦 Package Management

### 1. **Dependency Management**
```bash
# Get dependencies
flutter pub get

# Upgrade dependencies
flutter pub upgrade

# Add new dependency
flutter pub add package_name

# Add dev dependency
flutter pub add --dev package_name

# Remove dependency
flutter pub remove package_name

# Show dependency tree
flutter pub deps
```
**Penjelasan:** Mengelola dependencies dan packages.

### 2. **Specific Packages untuk Car Wash App**
```bash
# HTTP client untuk API calls
flutter pub add dio
flutter pub add http

# State management
flutter pub add provider
flutter pub add bloc
flutter pub add riverpod

# Local storage
flutter pub add shared_preferences
flutter pub add hive
flutter pub add sqflite

# UI components
flutter pub add flutter_svg
flutter pub add cached_network_image
flutter pub add shimmer

# Utilities
flutter pub add intl
flutter pub add url_launcher
flutter pub add permission_handler
```
**Penjelasan:** Package yang berguna untuk aplikasi car wash management.

---

## 🎯 Platform Specific Commands

### 1. **Android Specific**
```bash
# Clean Android build
flutter clean && cd android && ./gradlew clean && cd ..

# Build Android with specific key
flutter build apk --release --build-name=1.0.0 --build-number=1

# Install APK to device
flutter install

# Generate Android signing key
keytool -genkey -v -keystore ~/washmanager-key.jks -keyalg RSA -keysize 2048 -validity 10000 -alias washmanager

# Check Android dependencies
cd android && ./gradlew dependencies
```
**Penjelasan:** Perintah khusus untuk development Android.

### 2. **iOS Specific (Mac only)**
```bash
# Clean iOS build
flutter clean && cd ios && rm -rf Pods Podfile.lock && pod install && cd ..

# Update iOS pods
cd ios && pod update && cd ..

# Open iOS project in Xcode
open ios/Runner.xcworkspace

# Build for iOS device
flutter build ios --release --no-codesign

# Archive for App Store
flutter build ipa --release
```
**Penjelasan:** Perintah khusus untuk development iOS.

---

## 📊 Performance & Analysis

### 1. **Performance Profiling**
```bash
# Run in profile mode
flutter run --profile

# Performance overlay
# Press 'P' dalam flutter run session

# Memory usage
# Press 'M' dalam flutter run session

# Widget inspector
# Press 'I' dalam flutter run session

# Timeline trace
flutter run --trace-startup --profile
```
**Penjelasan:** Tools untuk menganalisis performa aplikasi.

### 2. **Code Analysis**
```bash
# Static analysis
flutter analyze

# Custom analysis options (analysis_options.yaml)
# include: package:flutter_lints/flutter.yaml

# Dart code metrics
flutter pub global activate dart_code_metrics
flutter pub global run dart_code_metrics:metrics analyze lib

# Check unused files
flutter pub global activate dart_dependency_validator
flutter pub global run dart_dependency_validator
```
**Penjelasan:** Analisis kualitas kode dan optimasi.

---

## 🚀 Deployment Commands

### 1. **Android Deployment**
```bash
# Build release APK
flutter build apk --release --build-name=1.0.0 --build-number=1

# Build App Bundle untuk Play Store
flutter build appbundle --release --build-name=1.0.0 --build-number=1

# Upload ke Play Store (menggunakan fastlane)
cd android && fastlane deploy

# Manual upload ke Play Console
# Upload file build/app/outputs/bundle/release/app-release.aab
```
**Penjelasan:** Deploy aplikasi ke Google Play Store.

### 2. **iOS Deployment (Mac only)**
```bash
# Build IPA untuk App Store
flutter build ipa --release --build-name=1.0.0 --build-number=1

# Upload ke App Store (menggunakan Xcode)
open build/ios/archive/Runner.xcarchive

# Upload menggunakan Transporter app
# Atau langsung dari Xcode Organizer

# TestFlight deployment
# Upload melalui App Store Connect
```
**Penjelasan:** Deploy aplikasi ke Apple App Store.

### 3. **Web Deployment**
```bash
# Build untuk web
flutter build web --release

# Deploy ke Firebase Hosting
firebase init hosting
firebase deploy

# Deploy ke GitHub Pages
# Copy build/web/* ke gh-pages branch

# Deploy ke Netlify
# Drag & drop build/web folder ke Netlify
```
**Penjelasan:** Deploy aplikasi web ke berbagai hosting platform.

---

## 🛠️ Car Wash App Specific Commands

### 1. **API Integration Setup**
```bash
# Generate model classes dari JSON
# Buat file JSON sample di assets/json/
# Gunakan https://app.quicktype.io/ untuk generate Dart classes

# Test API connection
flutter test test/api_test.dart

# Mock API untuk development
flutter pub add mockito
flutter pub add --dev build_runner
```
**Penjelasan:** Setup khusus untuk integrasi dengan Laravel API.

### 2. **Asset Management**
```bash
# Add assets ke pubspec.yaml
# assets:
#   - assets/images/
#   - assets/icons/

# Generate launcher icons
flutter pub run flutter_launcher_icons:main

# Generate splash screen
flutter pub run flutter_native_splash:create

# Optimize images
# Gunakan tools seperti TinyPNG atau ImageOptim
```
**Penjelasan:** Mengelola assets untuk aplikasi car wash.

### 3. **Local Database Setup**
```bash
# Setup Hive (NoSQL local database)
flutter pub add hive
flutter pub add hive_flutter
flutter pub add --dev hive_generator

# Setup SQLite
flutter pub add sqflite
flutter pub add path

# Generate Hive adapters
flutter packages pub run build_runner build
```
**Penjelasan:** Setup database lokal untuk offline functionality.

---

## 🔧 Development Workflow

### 1. **Daily Development**
```bash
# Start development session
flutter clean
flutter pub get
flutter run

# Code, test, repeat
# Hot reload dengan 'r'
# Hot restart dengan 'R'

# End of day
flutter analyze
flutter test
git add .
git commit -m "feat: implement order creation screen"
```
**Penjelasan:** Workflow harian untuk development.

### 2. **Feature Development**
```bash
# Create feature branch
git checkout -b feature/order-management

# Implement feature
flutter create --template=package packages/order_management

# Test feature
flutter test packages/order_management/test/

# Integration test
flutter drive --target=test_driver/order_test.dart
```
**Penjelasan:** Workflow untuk pengembangan fitur baru.

### 3. **Release Preparation**
```bash
# Update version di pubspec.yaml
# version: 1.0.0+1

# Update changelog
# CHANGELOG.md

# Build release
flutter build apk --release
flutter build appbundle --release

# Test release build
flutter install --release

# Tag release
git tag v1.0.0
git push origin v1.0.0
```
**Penjelasan:** Persiapan untuk release aplikasi.

---

## 🚨 Troubleshooting Commands

### 1. **Common Issues**
```bash
# Flutter doctor issues
flutter doctor -v
flutter doctor --android-licenses

# Gradle issues (Android)
cd android && ./gradlew clean && cd ..
flutter clean && flutter pub get

# Pod issues (iOS)
cd ios && rm -rf Pods Podfile.lock && pod install && cd ..

# Cache issues
flutter clean
flutter pub cache repair
```
**Penjelasan:** Mengatasi masalah umum dalam development Flutter.

### 2. **Build Issues**
```bash
# Clear all caches
flutter clean
rm -rf build/
flutter pub get

# Reset to fresh state
flutter create --project-name washmanager_mobile .
# (Hati-hati: akan overwrite files)

# Check for conflicts
flutter pub deps
flutter analyze
```
**Penjelasan:** Mengatasi masalah build dan dependency conflicts.

---

## 📱 Car Wash App Features Implementation

### 1. **Authentication Flow**
```bash
# Implement login screen
# lib/screens/auth/login_screen.dart

# API service untuk auth
# lib/services/auth_service.dart

# Secure storage untuk token
flutter pub add flutter_secure_storage

# Test auth flow
flutter test test/auth_test.dart
```

### 2. **Order Management**
```bash
# Order models
# lib/models/wash_order.dart
# lib/models/service.dart
# lib/models/vehicle.dart

# Order screens
# lib/screens/orders/order_list_screen.dart
# lib/screens/orders/create_order_screen.dart
# lib/screens/orders/order_detail_screen.dart

# State management
flutter pub add provider
# lib/providers/order_provider.dart
```

### 3. **Offline Functionality**
```bash
# Local database
flutter pub add hive
flutter pub add connectivity_plus

# Sync service
# lib/services/sync_service.dart

# Offline indicator
# lib/widgets/offline_indicator.dart
```

---

## ✅ Quick Checklist

### **Setup Baru:**
- [ ] `flutter doctor` (semua ✓)
- [ ] `flutter create washmanager_mobile`
- [ ] Setup folder structure
- [ ] Add dependencies di `pubspec.yaml`
- [ ] `flutter pub get`
- [ ] `flutter run`

### **Daily Development:**
- [ ] `flutter run` untuk start development
- [ ] Gunakan hot reload (`r`) untuk perubahan cepat
- [ ] `flutter analyze` sebelum commit
- [ ] `flutter test` untuk run tests
- [ ] Commit changes ke git

### **Before Release:**
- [ ] Update version di `pubspec.yaml`
- [ ] `flutter analyze` (no issues)
- [ ] `flutter test` (all pass)
- [ ] `flutter build apk --release`
- [ ] Test di real device
- [ ] Update store listings

---

## 🔗 Useful Resources

### **Documentation:**
- [Flutter.dev](https://flutter.dev/docs)
- [Dart.dev](https://dart.dev/guides)
- [Pub.dev](https://pub.dev/) - Package repository

### **Tools:**
- [Flutter Inspector](https://flutter.dev/docs/development/tools/flutter-inspector)
- [Dart DevTools](https://dart.dev/tools/dart-devtools)
- [VS Code Flutter Extension](https://marketplace.visualstudio.com/items?itemName=Dart-Code.flutter)

### **Car Wash App Specific:**
- Laravel API documentation: `API_DOCUMENTATION.md`
- Design mockups: `prototipe_aplikasi/`
- Backend setup: `DEPLOYMENT_GUIDE.md`

---

**💡 Tips:** Selalu gunakan `flutter doctor` untuk check environment dan `flutter clean` jika ada masalah build yang aneh!