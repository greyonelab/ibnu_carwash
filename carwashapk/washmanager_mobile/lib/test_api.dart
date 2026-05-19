import 'package:dio/dio.dart';

void main() async {
  final dio = Dio(BaseOptions(
    baseUrl: 'http://127.0.0.1:8000/api',
    connectTimeout: const Duration(seconds: 10),
    receiveTimeout: const Duration(seconds: 10),
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
  ));

  try {
    print('🔄 Testing API connection...');
    
    // Test login
    final response = await dio.post('/login', data: {
      'email': 'admin@carwash.com',
      'password': 'password',
    });
    
    print('✅ API Response Status: ${response.statusCode}');
    print('📦 API Response Data: ${response.data}');
    
  } catch (e) {
    print('❌ API Test Failed: $e');
    if (e is DioException) {
      print('❌ DioException Type: ${e.type}');
      print('❌ Response: ${e.response?.data}');
    }
  }
}