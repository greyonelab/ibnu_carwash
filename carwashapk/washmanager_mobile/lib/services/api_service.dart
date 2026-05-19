import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../models/user.dart';
import '../models/service.dart';
import '../models/staff.dart';
import '../models/wash_order.dart';
import '../models/wash_lane.dart';

class ApiService {
  static const String baseUrl = 'http://127.0.0.1:8000/api';
  late Dio _dio;
  final FlutterSecureStorage _storage = const FlutterSecureStorage();

  ApiService() {
    _dio = Dio(BaseOptions(
      baseUrl: baseUrl,
      connectTimeout: const Duration(seconds: 30),
      receiveTimeout: const Duration(seconds: 30),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    ));

    // Add interceptor for authentication
    _dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await _storage.read(key: 'auth_token');
        if (token != null) {
          options.headers['Authorization'] = 'Bearer $token';
        }
        handler.next(options);
      },
      onError: (error, handler) async {
        if (error.response?.statusCode == 401) {
          // Token expired, clear storage
          await _storage.delete(key: 'auth_token');
          await _storage.delete(key: 'user_data');
        }
        handler.next(error);
      },
    ));
  }

  // Authentication
  Future<Map<String, dynamic>> login(String email, String password) async {
    try {
      print('🔄 Attempting login to: $baseUrl/login');
      print('📧 Email: $email');
      
      final response = await _dio.post('/login', data: {
        'email': email,
        'password': password,
      });

      print('📡 Response status: ${response.statusCode}');
      print('📦 Response data: ${response.data}');

      if (response.statusCode == 200) {
        final data = response.data;
        
        // Check if response has expected structure
        if (data['success'] != true || data['data'] == null) {
          print('❌ Invalid response structure: ${data}');
          return {
            'success': false, 
            'message': 'Invalid response from server'
          };
        }
        
        final responseData = data['data'];
        
        if (responseData['token'] == null || responseData['user'] == null) {
          print('❌ Missing token or user in response data');
          return {
            'success': false, 
            'message': 'Invalid response from server - missing token or user'
          };
        }
        
        // Store token and user data
        await _storage.write(key: 'auth_token', value: responseData['token']);
        await _storage.write(key: 'user_data', value: responseData['user'].toString());
        
        print('✅ Login successful, token stored');
        
        return {
          'success': true,
          'token': responseData['token'],
          'user': User.fromJson(responseData['user']),
        };
      }
      
      return {'success': false, 'message': 'Login failed with status: ${response.statusCode}'};
    } on DioException catch (e) {
      print('❌ DioException: ${e.type}');
      print('❌ Error message: ${e.message}');
      print('❌ Response: ${e.response?.data}');
      
      String errorMessage = 'Network error';
      
      if (e.response != null) {
        if (e.response!.data is Map && e.response!.data['message'] != null) {
          errorMessage = e.response!.data['message'];
        } else {
          errorMessage = 'Server error: ${e.response!.statusCode}';
        }
      } else if (e.type == DioExceptionType.connectionTimeout) {
        errorMessage = 'Connection timeout - check if Laravel server is running';
      } else if (e.type == DioExceptionType.connectionError) {
        errorMessage = 'Connection error - check API URL and network';
      }
      
      return {
        'success': false,
        'message': errorMessage
      };
    } catch (e) {
      print('❌ Unexpected error: $e');
      return {
        'success': false,
        'message': 'Unexpected error: $e'
      };
    }
  }

  Future<bool> logout() async {
    try {
      await _dio.post('/logout');
      await _storage.delete(key: 'auth_token');
      await _storage.delete(key: 'user_data');
      return true;
    } catch (e) {
      // Clear local storage even if API call fails
      await _storage.delete(key: 'auth_token');
      await _storage.delete(key: 'user_data');
      return false;
    }
  }

  Future<String?> getToken() async {
    return await _storage.read(key: 'auth_token');
  }

  Future<bool> isLoggedIn() async {
    final token = await getToken();
    return token != null;
  }

  // Dashboard
  Future<Map<String, dynamic>> getDashboardData() async {
    try {
      print('🔄 Loading dashboard data...');
      final response = await _dio.get('/dashboard');
      print('📡 Dashboard response: ${response.statusCode}');
      print('📦 Dashboard data: ${response.data}');
      
      if (response.statusCode == 200 && response.data['success'] == true) {
        return {
          'success': true,
          'data': response.data['data'],
        };
      }
      return {'success': false, 'message': 'Failed to load dashboard'};
    } on DioException catch (e) {
      print('❌ Dashboard error: ${e.response?.data}');
      return {
        'success': false,
        'message': e.response?.data['message'] ?? 'Network error'
      };
    }
  }

  // Services
  Future<List<Service>> getServices() async {
    try {
      print('🔄 API: Loading services from /services');
      final response = await _dio.get('/services');
      print('📡 Services response status: ${response.statusCode}');
      print('📦 Services response data: ${response.data}');
      
      if (response.statusCode == 200 && response.data['success'] == true) {
        final List<dynamic> data = response.data['data'];
        print('✅ API: Found ${data.length} services');
        
        final services = data.map((json) {
          try {
            return Service.fromJson(json);
          } catch (e) {
            print('❌ Error parsing service: $json');
            print('❌ Parse error: $e');
            return null;
          }
        }).where((service) => service != null).cast<Service>().toList();
        
        print('✅ API: Successfully parsed ${services.length} services');
        return services;
      }
      
      print('❌ API: Invalid response structure for services');
      return [];
    } catch (e) {
      print('❌ Error loading services: $e');
      
      // Fallback data untuk testing
      print('🔄 Using fallback services data');
      return [
        Service(
          id: 1,
          name: 'Cuci Standar',
          type: 'standard',
          description: 'Cuci luar & vakum dasar',
          price: 50000,
          durationMinutes: 30,
          isActive: true,
          createdAt: DateTime.now().toIso8601String(),
          updatedAt: DateTime.now().toIso8601String(),
        ),
        Service(
          id: 2,
          name: 'Cuci Premium',
          type: 'premium',
          description: 'Cuci luar, dalam & wax',
          price: 75000,
          durationMinutes: 45,
          isActive: true,
          createdAt: DateTime.now().toIso8601String(),
          updatedAt: DateTime.now().toIso8601String(),
        ),
      ];
    }
  }

  // Staff
  Future<List<Staff>> getStaff() async {
    try {
      print('🔄 API: Loading staff from /staff');
      final response = await _dio.get('/staff');
      print('📡 Staff response status: ${response.statusCode}');
      print('📦 Staff response data: ${response.data}');
      
      if (response.statusCode == 200 && response.data['success'] == true) {
        final List<dynamic> data = response.data['data'];
        print('✅ API: Found ${data.length} staff members');
        
        final staff = data.map((json) {
          try {
            return Staff.fromJson(json);
          } catch (e) {
            print('❌ Error parsing staff: $json');
            print('❌ Parse error: $e');
            return null;
          }
        }).where((staff) => staff != null).cast<Staff>().toList();
        
        print('✅ API: Successfully parsed ${staff.length} staff members');
        return staff;
      }
      
      print('❌ API: Invalid response structure for staff');
      return [];
    } catch (e) {
      print('❌ Error loading staff: $e');
      
      // Fallback data untuk testing
      print('🔄 Using fallback staff data');
      return [
        Staff(
          id: 1,
          name: 'Ahmad',
          position: 'Cuci Motor',
          phone: '081234567890',
          commissionRate: 15.0,
          isActive: true,
          createdAt: DateTime.now().toIso8601String(),
          updatedAt: DateTime.now().toIso8601String(),
        ),
        Staff(
          id: 2,
          name: 'Budi',
          position: 'Cuci Mobil',
          phone: '081234567891',
          commissionRate: 12.0,
          isActive: true,
          createdAt: DateTime.now().toIso8601String(),
          updatedAt: DateTime.now().toIso8601String(),
        ),
      ];
    }
  }

  // Wash Orders
  Future<List<WashOrder>> getOrders({
    String? status,
    String? date,
    String? search,
    int page = 1,
  }) async {
    try {
      print('🔄 API: Loading orders from /wash-orders');
      final queryParams = <String, dynamic>{
        'page': page,
      };
      
      if (status != null) queryParams['status'] = status;
      if (date != null) queryParams['date'] = date;
      if (search != null) queryParams['search'] = search;

      final response = await _dio.get('/wash-orders', queryParameters: queryParams);
      print('📡 Orders response status: ${response.statusCode}');
      print('📦 Orders response data type: ${response.data.runtimeType}');
      
      if (response.statusCode == 200 && response.data['success'] == true) {
        final responseData = response.data['data'];
        print('📦 Orders data type: ${responseData.runtimeType}');
        
        // Handle paginated response
        List<dynamic> ordersData;
        if (responseData is Map && responseData.containsKey('data')) {
          // Paginated response
          ordersData = responseData['data'] as List<dynamic>;
          print('✅ API: Found ${ordersData.length} orders (paginated)');
        } else if (responseData is List) {
          // Direct array response
          ordersData = responseData;
          print('✅ API: Found ${ordersData.length} orders (direct)');
        } else {
          print('❌ API: Unexpected orders data structure: $responseData');
          return [];
        }
        
        final orders = ordersData.map((json) {
          try {
            return WashOrder.fromJson(json);
          } catch (e) {
            print('❌ Error parsing order: $json');
            print('❌ Parse error: $e');
            return null;
          }
        }).where((order) => order != null).cast<WashOrder>().toList();
        
        print('✅ API: Successfully parsed ${orders.length} orders');
        return orders;
      }
      
      print('❌ API: Invalid response structure for orders');
      return [];
    } catch (e) {
      print('❌ Error loading orders: $e');
      return [];
    }
  }

  Future<WashOrder?> getOrder(int id) async {
    try {
      final response = await _dio.get('/wash-orders/$id');
      if (response.statusCode == 200 && response.data['success'] == true) {
        return WashOrder.fromJson(response.data['data']);
      }
      return null;
    } catch (e) {
      print('❌ Error loading order: $e');
      return null;
    }
  }

  // Wash Lanes
  Future<List<WashLane>> getWashLanes() async {
    try {
      print('🔄 API: Loading wash lanes from /wash-lanes');
      final response = await _dio.get('/wash-lanes');
      print('📡 Wash lanes response status: ${response.statusCode}');
      print('📦 Wash lanes response data: ${response.data}');
      
      if (response.statusCode == 200 && response.data['success'] == true) {
        final List<dynamic> data = response.data['data'];
        print('✅ API: Found ${data.length} wash lanes');
        
        final lanes = data.map((json) {
          try {
            return WashLane.fromJson(json);
          } catch (e) {
            print('❌ Error parsing wash lane: $json');
            print('❌ Parse error: $e');
            return null;
          }
        }).where((lane) => lane != null).cast<WashLane>().toList();
        
        print('✅ API: Successfully parsed ${lanes.length} wash lanes');
        return lanes;
      }
      
      print('❌ API: Invalid response structure for wash lanes');
      return [];
    } catch (e) {
      print('❌ Error loading wash lanes: $e');
      
      // Fallback data untuk testing
      print('🔄 Using fallback wash lanes data');
      return [
        WashLane(
          id: 1,
          name: 'Jalur A',
          type: 'general',
          isActive: true,
          maxQueue: 5,
          description: 'Jalur umum untuk semua jenis kendaraan',
          currentQueue: 2,
          createdAt: DateTime.now(),
          updatedAt: DateTime.now(),
        ),
        WashLane(
          id: 2,
          name: 'Jalur B',
          type: 'motor',
          isActive: true,
          maxQueue: 8,
          description: 'Jalur khusus motor',
          currentQueue: 1,
          createdAt: DateTime.now(),
          updatedAt: DateTime.now(),
        ),
      ];
    }
  }

  Future<WashLane?> getAvailableLane(String vehicleType) async {
    try {
      print('🔄 API: Getting available lane for vehicle type: $vehicleType');
      final response = await _dio.get('/wash-lanes/available', queryParameters: {
        'vehicle_type': vehicleType,
      });
      
      if (response.statusCode == 200 && response.data['success'] == true) {
        final data = response.data['data'];
        if (data != null) {
          return WashLane.fromJson(data);
        }
      }
      
      return null;
    } catch (e) {
      print('❌ Error getting available lane: $e');
      return null;
    }
  }

  // Vehicle Search
  Future<List<Map<String, dynamic>>> searchVehicles(String query, String? vehicleType) async {
    try {
      print('🔄 API: Searching vehicles with query: $query, type: $vehicleType');
      final response = await _dio.get('/vehicles/search', queryParameters: {
        'q': query,
        if (vehicleType != null) 'type': vehicleType,
      });
      
      if (response.statusCode == 200 && response.data['success'] == true) {
        final List<dynamic> data = response.data['data'];
        print('✅ API: Found ${data.length} vehicles');
        return data.cast<Map<String, dynamic>>();
      }
      
      return [];
    } catch (e) {
      print('❌ Error searching vehicles: $e');
      return [];
    }
  }

  Future<Map<String, dynamic>> createOrder({
    required String licensePlate,
    required String vehicleType,
    String? vehicleModel,
    String? vehicleColor,
    required int serviceId,
    required List<int> staffIds,
    int? washLaneId,
    int? additionalFee,
    String? notes,
    String? paymentMethod,
    bool autoComplete = false,
  }) async {
    try {
      print('🔄 Creating order with data:');
      print('📋 License: $licensePlate, Type: $vehicleType');
      print('🔧 Service: $serviceId, Staff IDs: $staffIds, Lane: $washLaneId');
      
      final response = await _dio.post('/wash-orders', data: {
        'license_plate': licensePlate,
        'vehicle_type': vehicleType,
        'vehicle_model': vehicleModel,
        'vehicle_color': vehicleColor,
        'service_id': serviceId,
        'staff_ids': staffIds,
        'wash_lane_id': washLaneId,
        'additional_fee': additionalFee ?? 0,
        'notes': notes,
        'payment_method': paymentMethod,
        'auto_complete': autoComplete,
      });

      print('📡 Create order response: ${response.statusCode}');
      print('📦 Response data: ${response.data}');

      if (response.statusCode == 201 && response.data['success'] == true) {
        return {
          'success': true,
          'order': WashOrder.fromJson(response.data['data']),
        };
      }
      
      return {'success': false, 'message': 'Failed to create order'};
    } on DioException catch (e) {
      print('❌ Create order error: ${e.response?.data}');
      return {
        'success': false,
        'message': e.response?.data['message'] ?? 'Network error',
        'errors': e.response?.data['errors'],
      };
    }
  }

  Future<Map<String, dynamic>> updateOrderStatus(int orderId, String status) async {
    try {
      final response = await _dio.patch('/wash-orders/$orderId/status', data: {
        'status': status,
      });

      if (response.statusCode == 200 && response.data['success'] == true) {
        return {
          'success': true,
          'order': WashOrder.fromJson(response.data['data']),
        };
      }
      
      return {'success': false, 'message': 'Failed to update status'};
    } on DioException catch (e) {
      return {
        'success': false,
        'message': e.response?.data['message'] ?? 'Network error'
      };
    }
  }

  Future<Map<String, dynamic>> updateOrderPayment(
    int orderId, 
    String paymentStatus, 
    String? paymentMethod
  ) async {
    try {
      final response = await _dio.patch('/wash-orders/$orderId/payment', data: {
        'payment_status': paymentStatus,
        'payment_method': paymentMethod,
      });

      if (response.statusCode == 200 && response.data['success'] == true) {
        return {
          'success': true,
          'order': WashOrder.fromJson(response.data['data']),
        };
      }
      
      return {'success': false, 'message': 'Failed to update payment'};
    } on DioException catch (e) {
      return {
        'success': false,
        'message': e.response?.data['message'] ?? 'Network error'
      };
    }
  }
}