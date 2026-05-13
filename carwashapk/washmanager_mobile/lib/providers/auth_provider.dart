import 'package:flutter/foundation.dart';
import '../models/user.dart';
import '../services/api_service.dart';

class AuthProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();
  
  User? _user;
  bool _isLoading = false;
  String? _errorMessage;

  User? get user => _user;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;
  bool get isLoggedIn => _user != null;

  Future<bool> login(String email, String password) async {
    print('🔐 AuthProvider: Starting login process');
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final result = await _apiService.login(email, password);
      print('🔐 AuthProvider: Login result - ${result['success']}');
      
      if (result['success']) {
        _user = result['user'];
        _isLoading = false;
        print('✅ AuthProvider: Login successful, user set');
        notifyListeners();
        return true;
      } else {
        _errorMessage = result['message'];
        _isLoading = false;
        print('❌ AuthProvider: Login failed - ${result['message']}');
        notifyListeners();
        return false;
      }
    } catch (e) {
      _errorMessage = 'Login failed: $e';
      _isLoading = false;
      print('❌ AuthProvider: Exception - $e');
      notifyListeners();
      return false;
    }
  }

  Future<void> logout() async {
    _isLoading = true;
    notifyListeners();

    await _apiService.logout();
    
    _user = null;
    _isLoading = false;
    _errorMessage = null;
    notifyListeners();
  }

  Future<bool> checkAuthStatus() async {
    final isLoggedIn = await _apiService.isLoggedIn();
    if (isLoggedIn) {
      // Try to get user data from storage or API
      // For now, we'll just set a flag
      _user = User(
        id: 1,
        name: 'User',
        email: 'user@example.com',
        createdAt: DateTime.now().toIso8601String(),
        updatedAt: DateTime.now().toIso8601String(),
      );
    }
    notifyListeners();
    return isLoggedIn;
  }

  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}