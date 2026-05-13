import 'package:flutter/foundation.dart';
import '../models/wash_order.dart';
import '../models/service.dart';
import '../models/staff.dart';
import '../services/api_service.dart';

class OrderProvider with ChangeNotifier {
  final ApiService _apiService = ApiService();
  
  List<WashOrder> _orders = [];
  List<Service> _services = [];
  List<Staff> _staff = [];
  bool _isLoading = false;
  String? _errorMessage;

  List<WashOrder> get orders => _orders;
  List<Service> get services => _services;
  List<Staff> get staff => _staff;
  bool get isLoading => _isLoading;
  String? get errorMessage => _errorMessage;

  Future<void> loadOrders({
    String? status,
    String? date,
    String? search,
  }) async {
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      _orders = await _apiService.getOrders(
        status: status,
        date: date,
        search: search,
      );
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Failed to load orders: $e';
      _isLoading = false;
      notifyListeners();
    }
  }

  Future<void> loadServices() async {
    print('🔄 OrderProvider: Loading services...');
    try {
      _services = await _apiService.getServices();
      print('✅ OrderProvider: Loaded ${_services.length} services');
      _services.forEach((service) {
        print('   - ${service.name}: Rp ${service.price}');
      });
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Failed to load services: $e';
      print('❌ OrderProvider: Error loading services - $e');
      notifyListeners();
    }
  }

  Future<void> loadStaff() async {
    print('🔄 OrderProvider: Loading staff...');
    try {
      _staff = await _apiService.getStaff();
      print('✅ OrderProvider: Loaded ${_staff.length} staff members');
      _staff.forEach((staff) {
        print('   - ${staff.name} (${staff.position})');
      });
      notifyListeners();
    } catch (e) {
      _errorMessage = 'Failed to load staff: $e';
      print('❌ OrderProvider: Error loading staff - $e');
      notifyListeners();
    }
  }

  Future<bool> createOrder({
    required String licensePlate,
    required String vehicleType,
    String? vehicleModel,
    String? vehicleColor,
    required int serviceId,
    required List<int> staffIds,
    int? additionalFee,
    String? notes,
    String? paymentMethod,
    bool autoComplete = false,
  }) async {
    print('🔄 OrderProvider: Starting createOrder...');
    print('📋 Order data:');
    print('   - License: $licensePlate');
    print('   - Type: $vehicleType');
    print('   - Service ID: $serviceId');
    print('   - Staff IDs: $staffIds');
    print('   - Payment: $paymentMethod');
    print('   - Auto complete: $autoComplete');
    
    _isLoading = true;
    _errorMessage = null;
    notifyListeners();

    try {
      final result = await _apiService.createOrder(
        licensePlate: licensePlate,
        vehicleType: vehicleType,
        vehicleModel: vehicleModel,
        vehicleColor: vehicleColor,
        serviceId: serviceId,
        staffIds: staffIds,
        additionalFee: additionalFee,
        notes: notes,
        paymentMethod: paymentMethod,
        autoComplete: autoComplete,
      );

      print('📡 OrderProvider: API result - ${result['success']}');

      if (result['success']) {
        // Add new order to the beginning of the list
        _orders.insert(0, result['order']);
        _isLoading = false;
        print('✅ OrderProvider: Order created and added to list');
        notifyListeners();
        return true;
      } else {
        _errorMessage = result['message'];
        _isLoading = false;
        print('❌ OrderProvider: Create failed - ${result['message']}');
        notifyListeners();
        return false;
      }
    } catch (e) {
      _errorMessage = 'Failed to create order: $e';
      _isLoading = false;
      print('❌ OrderProvider: Exception - $e');
      notifyListeners();
      return false;
    }
  }

  Future<bool> updateOrderStatus(int orderId, String status) async {
    try {
      final result = await _apiService.updateOrderStatus(orderId, status);
      
      if (result['success']) {
        // Update order in the list
        final index = _orders.indexWhere((order) => order.id == orderId);
        if (index != -1) {
          _orders[index] = result['order'];
          notifyListeners();
        }
        return true;
      } else {
        _errorMessage = result['message'];
        notifyListeners();
        return false;
      }
    } catch (e) {
      _errorMessage = 'Failed to update status: $e';
      notifyListeners();
      return false;
    }
  }

  Future<bool> updateOrderPayment(
    int orderId, 
    String paymentStatus, 
    String? paymentMethod
  ) async {
    try {
      final result = await _apiService.updateOrderPayment(
        orderId, 
        paymentStatus, 
        paymentMethod
      );
      
      if (result['success']) {
        // Update order in the list
        final index = _orders.indexWhere((order) => order.id == orderId);
        if (index != -1) {
          _orders[index] = result['order'];
          notifyListeners();
        }
        return true;
      } else {
        _errorMessage = result['message'];
        notifyListeners();
        return false;
      }
    } catch (e) {
      _errorMessage = 'Failed to update payment: $e';
      notifyListeners();
      return false;
    }
  }

  void clearError() {
    _errorMessage = null;
    notifyListeners();
  }
}