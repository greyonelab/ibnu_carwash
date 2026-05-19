import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';
import 'package:provider/provider.dart';
import '../providers/order_provider.dart';
import '../models/service.dart';
import '../models/staff.dart';
import '../models/wash_lane.dart';
import '../widgets/license_plate_autocomplete.dart';

class CreateOrderScreen extends StatefulWidget {
  const CreateOrderScreen({super.key});

  @override
  State<CreateOrderScreen> createState() => _CreateOrderScreenState();
}

class _CreateOrderScreenState extends State<CreateOrderScreen> {
  final PageController _pageController = PageController();
  int _currentStep = 0;
  
  // Step 1: Vehicle Type
  String? _selectedVehicleType;
  
  // Step 2: Vehicle Details
  final _formKey = GlobalKey<FormState>();
  final _licensePlateController = TextEditingController();
  final _vehicleModelController = TextEditingController();
  final _vehicleColorController = TextEditingController();
  final _notesController = TextEditingController();
  final _additionalFeeController = TextEditingController();
  Service? _selectedService;
  List<Staff> _selectedStaff = [];
  VehicleData? _selectedVehicle;
  WashLane? _selectedWashLane;
  bool _autoAssignLane = true;
  
  // Step 3: Payment
  String? _paymentMethod;
  bool _autoComplete = false;

  final List<Map<String, dynamic>> _vehicleTypes = [
    {
      'type': 'Motor',
      'icon': Icons.motorcycle,
      'color': Colors.orange,
      'description': 'Sepeda motor, scooter, dll'
    },
    {
      'type': 'Mobil',
      'icon': Icons.directions_car,
      'color': Colors.blue,
      'description': 'Sedan, hatchback, SUV, MPV, dll'
    },
  ];

  final List<String> _paymentMethods = [
    'cash',
    'qris',
    'transfer',
  ];

  @override
  void initState() {
    super.initState();
    // Load services dan staff saat screen dibuka
    WidgetsBinding.instance.addPostFrameCallback((_) {
      final orderProvider = Provider.of<OrderProvider>(context, listen: false);
      print('🔄 Loading services, staff, and wash lanes for create order...');
      orderProvider.loadServices();
      orderProvider.loadWashLanes();
      orderProvider.loadStaff().then((_) {
        // Set default: select all staff
        setState(() {
          _selectedStaff = List.from(orderProvider.staff);
        });
        print('✅ Default: All ${_selectedStaff.length} staff selected');
      });
    });
  }

  @override
  void dispose() {
    _pageController.dispose();
    _licensePlateController.dispose();
    _vehicleModelController.dispose();
    _vehicleColorController.dispose();
    _notesController.dispose();
    _additionalFeeController.dispose();
    super.dispose();
  }

  void _nextStep() {
    if (_currentStep == 0) {
      // Validasi step 1: harus pilih jenis kendaraan
      if (_selectedVehicleType == null) {
        _showSnackBar('Pilih jenis kendaraan terlebih dahulu', Colors.red);
        return;
      }
    } else if (_currentStep == 1) {
      // Validasi step 2: form harus valid dan service/staff dipilih
      if (!_formKey.currentState!.validate()) {
        _showSnackBar('Mohon lengkapi semua field yang wajib diisi', Colors.red);
        return;
      }
      if (_selectedService == null) {
        _showSnackBar('Pilih layanan terlebih dahulu', Colors.red);
        return;
      }
      if (_selectedStaff.isEmpty) {
        _showSnackBar('Pilih minimal satu staff terlebih dahulu', Colors.red);
        return;
      }
    }
    
    if (_currentStep < 2) {
      setState(() {
        _currentStep++;
      });
      _pageController.nextPage(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeInOut,
      );
    }
  }

  void _previousStep() {
    if (_currentStep > 0) {
      setState(() {
        _currentStep--;
      });
      _pageController.previousPage(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeInOut,
      );
    }
  }

  Future<void> _createOrder() async {
    print('🔄 Starting create order process...');
    print('📋 Current form state:');
    print('   - Vehicle type: $_selectedVehicleType');
    print('   - License plate: ${_licensePlateController.text}');
    print('   - Selected service: ${_selectedService?.name}');
    print('   - Selected staff: ${_selectedStaff.map((s) => s.name).join(', ')}');
    print('   - Payment method: $_paymentMethod');
    print('   - Auto complete: $_autoComplete');
    
    // Validasi manual tanpa form key karena step 3 tidak punya form
    if (_selectedVehicleType == null) {
      print('❌ No vehicle type selected');
      _showSnackBar('Pilih jenis kendaraan terlebih dahulu', Colors.red);
      return;
    }
    print('✅ Vehicle type selected: $_selectedVehicleType');

    if (_licensePlateController.text.trim().isEmpty) {
      print('❌ License plate is empty');
      _showSnackBar('Nomor plat harus diisi', Colors.red);
      return;
    }
    print('✅ License plate: ${_licensePlateController.text}');
    
    if (_selectedService == null) {
      print('❌ No service selected');
      _showSnackBar('Pilih layanan terlebih dahulu', Colors.red);
      return;
    }
    print('✅ Service selected: ${_selectedService!.name}');

    if (_selectedStaff.isEmpty) {
      print('❌ No staff selected');
      _showSnackBar('Pilih minimal satu staff terlebih dahulu', Colors.red);
      return;
    }
    print('✅ Staff selected: ${_selectedStaff.map((s) => s.name).join(', ')}');

    print('🔄 Calling OrderProvider.createOrder...');
    final orderProvider = Provider.of<OrderProvider>(context, listen: false);
    
    final success = await orderProvider.createOrder(
      licensePlate: _licensePlateController.text.trim().toUpperCase(),
      vehicleType: _selectedVehicleType!,
      vehicleModel: _vehicleModelController.text.trim().isNotEmpty 
          ? _vehicleModelController.text.trim() 
          : null,
      vehicleColor: _vehicleColorController.text.trim().isNotEmpty 
          ? _vehicleColorController.text.trim() 
          : null,
      serviceId: _selectedService!.id,
      staffIds: _selectedStaff.map((s) => s.id).toList(),
      additionalFee: _additionalFeeController.text.isNotEmpty 
          ? int.tryParse(_additionalFeeController.text) 
          : null,
      notes: _notesController.text.trim().isNotEmpty 
          ? _notesController.text.trim() 
          : null,
      paymentMethod: _paymentMethod,
      autoComplete: _autoComplete,
    );

    print('📡 Create order result: $success');

    if (success && mounted) {
      print('✅ Order created successfully');
      _showSnackBar('Pesanan berhasil dibuat!', Colors.green);
      Navigator.of(context).pop();
    } else if (mounted) {
      print('❌ Order creation failed: ${orderProvider.errorMessage}');
      _showSnackBar(
        orderProvider.errorMessage ?? 'Gagal membuat pesanan',
        Colors.red,
      );
    }
  }

  void _showSnackBar(String message, Color color) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: color,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text(
          'Create New Order',
          style: TextStyle(
            fontWeight: FontWeight.w800,
            fontSize: 20,
            letterSpacing: -0.3,
          ),
        ),
        backgroundColor: Colors.transparent,
        elevation: 0,
        flexibleSpace: Container(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.topRight,
              colors: [
                const Color(0xFF667EEA).withValues(alpha: 0.95),
                const Color(0xFF764BA2).withValues(alpha: 0.9),
              ],
            ),
          ),
        ),
        foregroundColor: Colors.white,
      ),
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [
              Color(0xFF667EEA),
              Color(0xFF764BA2),
              Color(0xFFF8FAFC),
            ],
            stops: [0.0, 0.3, 1.0],
          ),
        ),
        child: Column(
          children: [
            // Modern Progress Indicator
            Container(
              margin: const EdgeInsets.fromLTRB(20, 120, 20, 0),
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [
                    Colors.white.withValues(alpha: 0.25),
                    Colors.white.withValues(alpha: 0.1),
                  ],
                ),
                borderRadius: BorderRadius.circular(24),
                border: Border.all(
                  color: Colors.white.withValues(alpha: 0.3),
                ),
              ),
              child: Row(
                children: [
                  _buildModernStepIndicator(0, 'Vehicle', Icons.directions_car_rounded),
                  _buildModernStepConnector(0),
                  _buildModernStepIndicator(1, 'Details', Icons.edit_rounded),
                  _buildModernStepConnector(1),
                  _buildModernStepIndicator(2, 'Payment', Icons.payment_rounded),
                ],
              ),
            ),
            
            // Content
            Expanded(
              child: Container(
                margin: const EdgeInsets.fromLTRB(20, 20, 20, 0),
                decoration: BoxDecoration(
                  color: Colors.white,
                  borderRadius: const BorderRadius.only(
                    topLeft: Radius.circular(28),
                    topRight: Radius.circular(28),
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: Colors.black.withValues(alpha: 0.1),
                      blurRadius: 30,
                      offset: const Offset(0, -10),
                    ),
                  ],
                ),
                child: ClipRRect(
                  borderRadius: const BorderRadius.only(
                    topLeft: Radius.circular(28),
                    topRight: Radius.circular(28),
                  ),
                  child: PageView(
                    controller: _pageController,
                    physics: const NeverScrollableScrollPhysics(),
                    children: [
                      _buildStep1VehicleType(),
                      _buildStep2Details(),
                      _buildStep3Payment(),
                    ],
                  ),
                ),
              ),
            ),
            
            // Modern Navigation Buttons
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.white,
                boxShadow: [
                  BoxShadow(
                    color: Colors.black.withValues(alpha: 0.1),
                    blurRadius: 20,
                    offset: const Offset(0, -5),
                  ),
                ],
              ),
              child: Row(
                children: [
                  if (_currentStep > 0)
                    Expanded(
                      child: Container(
                        height: 56,
                        decoration: BoxDecoration(
                          border: Border.all(
                            color: const Color(0xFF667EEA).withValues(alpha: 0.3),
                          ),
                          borderRadius: BorderRadius.circular(16),
                        ),
                        child: OutlinedButton.icon(
                          onPressed: _previousStep,
                          icon: const Icon(Icons.arrow_back_rounded),
                          label: const Text('Back'),
                          style: OutlinedButton.styleFrom(
                            foregroundColor: const Color(0xFF667EEA),
                            side: BorderSide.none,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(16),
                            ),
                          ),
                        ),
                      ),
                    ),
                  if (_currentStep > 0) const SizedBox(width: 16),
                  Expanded(
                    child: Consumer<OrderProvider>(
                      builder: (context, orderProvider, child) {
                        return Container(
                          height: 56,
                          decoration: BoxDecoration(
                            gradient: const LinearGradient(
                              colors: [Color(0xFF667EEA), Color(0xFF764BA2)],
                            ),
                            borderRadius: BorderRadius.circular(16),
                            boxShadow: [
                              BoxShadow(
                                color: const Color(0xFF667EEA).withValues(alpha: 0.4),
                                blurRadius: 15,
                                offset: const Offset(0, 8),
                              ),
                            ],
                          ),
                          child: ElevatedButton.icon(
                            onPressed: orderProvider.isLoading 
                                ? null 
                                : (_currentStep == 0 && _selectedVehicleType == null)
                                    ? null
                                : (_currentStep == 1 && (_selectedService == null || _selectedStaff.isEmpty))
                                    ? null
                                : _currentStep == 2 
                                    ? () async {
                                        try {
                                          await _createOrder();
                                        } catch (e) {
                                          _showSnackBar('Error occurred: $e', Colors.red);
                                        }
                                      }
                                    : _nextStep,
                            icon: orderProvider.isLoading
                                ? const SizedBox(
                                    height: 20,
                                    width: 20,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 2,
                                      valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                    ),
                                  )
                                : Icon(
                                    _currentStep == 2 
                                        ? Icons.check_rounded 
                                        : Icons.arrow_forward_rounded,
                                  ),
                            label: Text(
                              orderProvider.isLoading
                                  ? 'Processing...'
                                  : _currentStep == 2 
                                      ? 'Create Order' 
                                      : 'Continue',
                              style: const TextStyle(
                                fontSize: 16,
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.transparent,
                              shadowColor: Colors.transparent,
                              foregroundColor: Colors.white,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(16),
                              ),
                            ),
                          ),
                        );
                      },
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildModernStepIndicator(int step, String title, IconData icon) {
    final isActive = step <= _currentStep;
    final isCompleted = step < _currentStep;
    
    return Column(
      children: [
        Container(
          width: 48,
          height: 48,
          decoration: BoxDecoration(
            gradient: isActive 
                ? const LinearGradient(
                    colors: [Colors.white, Colors.white],
                  )
                : LinearGradient(
                    colors: [
                      Colors.white.withValues(alpha: 0.3),
                      Colors.white.withValues(alpha: 0.2),
                    ],
                  ),
            shape: BoxShape.circle,
            border: Border.all(
              color: isActive 
                  ? Colors.white 
                  : Colors.white.withValues(alpha: 0.4),
              width: 2,
            ),
            boxShadow: isActive ? [
              BoxShadow(
                color: Colors.white.withValues(alpha: 0.3),
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ] : null,
          ),
          child: Icon(
            isCompleted ? Icons.check_rounded : icon,
            color: isActive ? const Color(0xFF667EEA) : Colors.white,
            size: 24,
          ),
        ),
        const SizedBox(height: 12),
        Text(
          title,
          style: TextStyle(
            color: Colors.white,
            fontSize: 13,
            fontWeight: isActive ? FontWeight.w700 : FontWeight.w500,
          ),
        ),
      ],
    );
  }

  Widget _buildModernStepConnector(int step) {
    final isCompleted = step < _currentStep;
    
    return Expanded(
      child: Container(
        height: 3,
        margin: const EdgeInsets.only(bottom: 32, left: 8, right: 8),
        decoration: BoxDecoration(
          color: isCompleted 
              ? Colors.white 
              : Colors.white.withValues(alpha: 0.3),
          borderRadius: BorderRadius.circular(2),
        ),
      ),
    );
  }

  Widget _buildStep1VehicleType() {
    return Padding(
      padding: const EdgeInsets.all(24),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Choose Vehicle Type',
            style: TextStyle(
              fontSize: 28,
              fontWeight: FontWeight.w800,
              color: Color(0xFF1F2937),
              letterSpacing: -0.5,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'Select the type of vehicle to be washed',
            style: TextStyle(
              fontSize: 16,
              color: Colors.grey.shade600,
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(height: 40),
          
          Expanded(
            child: GridView.builder(
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 1,
                crossAxisSpacing: 16,
                mainAxisSpacing: 20,
                childAspectRatio: 3.5,
              ),
              itemCount: _vehicleTypes.length,
              itemBuilder: (context, index) {
                final vehicleType = _vehicleTypes[index];
                final isSelected = _selectedVehicleType == vehicleType['type'];
                
                return GestureDetector(
                  onTap: () {
                    setState(() {
                      _selectedVehicleType = vehicleType['type'];
                    });
                  },
                  child: AnimatedContainer(
                    duration: const Duration(milliseconds: 300),
                    curve: Curves.easeInOut,
                    decoration: BoxDecoration(
                      gradient: isSelected 
                          ? LinearGradient(
                              begin: Alignment.topLeft,
                              end: Alignment.bottomRight,
                              colors: [
                                vehicleType['color'].withValues(alpha: 0.15),
                                vehicleType['color'].withValues(alpha: 0.05),
                              ],
                            )
                          : LinearGradient(
                              colors: [
                                Colors.grey.shade50,
                                Colors.white,
                              ],
                            ),
                      border: Border.all(
                        color: isSelected 
                            ? vehicleType['color']
                            : Colors.grey.shade200,
                        width: isSelected ? 2.5 : 1.5,
                      ),
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [
                        BoxShadow(
                          color: isSelected 
                              ? vehicleType['color'].withValues(alpha: 0.2)
                              : Colors.black.withValues(alpha: 0.05),
                          blurRadius: isSelected ? 15 : 8,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: Padding(
                      padding: const EdgeInsets.all(24),
                      child: Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              gradient: isSelected
                                  ? LinearGradient(
                                      colors: [
                                        vehicleType['color'],
                                        vehicleType['color'].withValues(alpha: 0.8),
                                      ],
                                    )
                                  : LinearGradient(
                                      colors: [
                                        Colors.grey.shade100,
                                        Colors.grey.shade50,
                                      ],
                                    ),
                              borderRadius: BorderRadius.circular(16),
                            ),
                            child: Icon(
                              vehicleType['icon'],
                              size: 32,
                              color: isSelected 
                                  ? Colors.white
                                  : Colors.grey.shade600,
                            ),
                          ),
                          const SizedBox(width: 20),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Text(
                                  vehicleType['type'],
                                  style: TextStyle(
                                    fontSize: 20,
                                    fontWeight: FontWeight.w800,
                                    color: isSelected 
                                        ? vehicleType['color']
                                        : const Color(0xFF1F2937),
                                    letterSpacing: -0.3,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  vehicleType['description'],
                                  style: TextStyle(
                                    fontSize: 14,
                                    color: Colors.grey.shade600,
                                    fontWeight: FontWeight.w500,
                                  ),
                                ),
                              ],
                            ),
                          ),
                          if (isSelected)
                            Container(
                              padding: const EdgeInsets.all(8),
                              decoration: BoxDecoration(
                                color: vehicleType['color'],
                                shape: BoxShape.circle,
                              ),
                              child: const Icon(
                                Icons.check_rounded,
                                color: Colors.white,
                                size: 20,
                              ),
                            ),
                        ],
                      ),
                    ),
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStep2Details() {
    return Consumer<OrderProvider>(
      builder: (context, orderProvider, child) {
        return Form(
          key: _formKey,
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Detail Kendaraan & Layanan',
                  style: TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 8),
                const Text(
                  'Isi detail kendaraan dan pilih layanan',
                  style: TextStyle(
                    fontSize: 16,
                    color: Colors.grey,
                  ),
                ),
                const SizedBox(height: 24),
                
                // Vehicle Information
                _buildSectionCard(
                  title: 'Informasi Kendaraan',
                  icon: Icons.directions_car,
                  children: [
                    LicensePlateAutocomplete(
                      controller: _licensePlateController,
                      selectedVehicleType: _selectedVehicleType,
                      onVehicleSelected: (vehicle) {
                        setState(() {
                          _selectedVehicle = vehicle;
                          // Auto-fill vehicle details if available
                          if (vehicle.model != null) {
                            _vehicleModelController.text = vehicle.model!;
                          }
                          if (vehicle.color != null) {
                            _vehicleColorController.text = vehicle.color!;
                          }
                          // Auto-select vehicle type if different
                          if (_selectedVehicleType != vehicle.type) {
                            _selectedVehicleType = vehicle.type;
                          }
                        });
                      },
                    ),
                    const SizedBox(height: 16),
                    
                    // Display selected vehicle info
                    if (_selectedVehicle != null)
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          gradient: LinearGradient(
                            colors: [
                              Colors.green.shade50,
                              Colors.blue.shade50,
                            ],
                          ),
                          borderRadius: BorderRadius.circular(12),
                          border: Border.all(color: Colors.green.shade200),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(8),
                                  decoration: BoxDecoration(
                                    color: Colors.green.shade600,
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                  child: const Icon(
                                    Icons.check_circle,
                                    color: Colors.white,
                                    size: 20,
                                  ),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      const Text(
                                        'Kendaraan Ditemukan!',
                                        style: TextStyle(
                                          fontWeight: FontWeight.w700,
                                          fontSize: 16,
                                          color: Color(0xFF1F2937),
                                        ),
                                      ),
                                      Text(
                                        'Sudah ${_selectedVehicle!.totalWashes}x cuci di sini',
                                        style: TextStyle(
                                          fontSize: 14,
                                          color: Colors.green.shade700,
                                          fontWeight: FontWeight.w500,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                            if (_selectedVehicle!.lastWashDate != null) ...[
                              const SizedBox(height: 12),
                              Container(
                                padding: const EdgeInsets.all(12),
                                decoration: BoxDecoration(
                                  color: Colors.white.withOpacity(0.7),
                                  borderRadius: BorderRadius.circular(8),
                                ),
                                child: Row(
                                  children: [
                                    Icon(
                                      Icons.history,
                                      size: 16,
                                      color: Colors.grey.shade600,
                                    ),
                                    const SizedBox(width: 8),
                                    Text(
                                      'Terakhir cuci: ${_formatDate(_selectedVehicle!.lastWashDate!)}',
                                      style: TextStyle(
                                        fontSize: 12,
                                        color: Colors.grey.shade700,
                                      ),
                                    ),
                                    if (_selectedVehicle!.lastWashService != null) ...[
                                      const SizedBox(width: 8),
                                      Text(
                                        '(${_selectedVehicle!.lastWashService})',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey.shade600,
                                          fontStyle: FontStyle.italic,
                                        ),
                                      ),
                                    ],
                                  ],
                                ),
                              ),
                            ],
                          ],
                        ),
                      ),
                    
                    if (_selectedVehicle != null) const SizedBox(height: 16),
                    
                    TextFormField(
                      controller: _vehicleModelController,
                      decoration: const InputDecoration(
                        labelText: 'Model/Merek (Opsional)',
                        hintText: 'Honda Beat, Toyota Avanza',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.car_rental),
                      ),
                    ),
                    const SizedBox(height: 16),
                    
                    TextFormField(
                      controller: _vehicleColorController,
                      decoration: const InputDecoration(
                        labelText: 'Warna (Opsional)',
                        hintText: 'Putih, Hitam, Merah',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.palette),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),
                
                // Service Selection
                _buildSectionCard(
                  title: 'Pilih Layanan',
                  icon: Icons.cleaning_services,
                  children: [
                    if (orderProvider.isLoading)
                      const Center(
                        child: Padding(
                          padding: EdgeInsets.all(16.0),
                          child: CircularProgressIndicator(),
                        ),
                      )
                    else if (orderProvider.services.isEmpty)
                      Center(
                        child: Padding(
                          padding: const EdgeInsets.all(16.0),
                          child: Column(
                            children: [
                              const Text('Tidak ada layanan tersedia'),
                              const SizedBox(height: 8),
                              ElevatedButton.icon(
                                onPressed: () {
                                  orderProvider.loadServices();
                                },
                                icon: const Icon(Icons.refresh),
                                label: const Text('Muat Ulang'),
                              ),
                            ],
                          ),
                        ),
                      )
                    else
                      ...orderProvider.services.map((service) {
                        final isSelected = _selectedService?.id == service.id;
                        return Container(
                          margin: const EdgeInsets.only(bottom: 8),
                          child: InkWell(
                            onTap: () {
                              setState(() {
                                _selectedService = service;
                              });
                            },
                            borderRadius: BorderRadius.circular(12),
                            child: Container(
                              padding: const EdgeInsets.all(16),
                              decoration: BoxDecoration(
                                border: Border.all(
                                  color: isSelected 
                                      ? Colors.blue.shade600 
                                      : Colors.grey.shade300,
                                  width: isSelected ? 2 : 1,
                                ),
                                borderRadius: BorderRadius.circular(12),
                                color: isSelected 
                                    ? Colors.blue.shade50 
                                    : Colors.white,
                              ),
                              child: Row(
                                children: [
                                  Radio<Service>(
                                    value: service,
                                    groupValue: _selectedService,
                                    onChanged: (value) {
                                      setState(() {
                                        _selectedService = value;
                                      });
                                    },
                                  ),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Text(
                                          service.name,
                                          style: const TextStyle(
                                            fontWeight: FontWeight.bold,
                                            fontSize: 16,
                                          ),
                                        ),
                                        Text(
                                          service.description,
                                          style: TextStyle(
                                            color: Colors.grey.shade600,
                                          ),
                                        ),
                                        const SizedBox(height: 4),
                                        Row(
                                          children: [
                                            Text(
                                              'Rp ${_formatCurrency(service.price)}',
                                              style: TextStyle(
                                                color: Colors.green.shade600,
                                                fontWeight: FontWeight.bold,
                                                fontSize: 16,
                                              ),
                                            ),
                                            const SizedBox(width: 16),
                                            Text(
                                              '${service.durationMinutes} menit',
                                              style: TextStyle(
                                                color: Colors.grey.shade600,
                                                fontSize: 14,
                                              ),
                                            ),
                                          ],
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        );
                      }),
                  ],
                ),
                const SizedBox(height: 16),
                
                // Staff Selection
                _buildSectionCard(
                  title: 'Pilih Staff',
                  icon: Icons.person,
                  children: [
                    if (orderProvider.isLoading)
                      const Center(
                        child: Padding(
                          padding: EdgeInsets.all(16.0),
                          child: CircularProgressIndicator(),
                        ),
                      )
                    else if (orderProvider.staff.isEmpty)
                      Center(
                        child: Padding(
                          padding: const EdgeInsets.all(16.0),
                          child: Column(
                            children: [
                              const Text('Tidak ada staff tersedia'),
                              const SizedBox(height: 8),
                              ElevatedButton.icon(
                                onPressed: () {
                                  orderProvider.loadStaff();
                                },
                                icon: const Icon(Icons.refresh),
                                label: const Text('Muat Ulang'),
                              ),
                            ],
                          ),
                        ),
                      )
                    else
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          // Header dengan Select All
                          Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              color: Colors.blue.shade50,
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: Colors.blue.shade200),
                            ),
                            child: Row(
                              children: [
                                Container(
                                  decoration: BoxDecoration(
                                    color: Colors.blue.shade600,
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                  child: Checkbox(
                                    value: _selectedStaff.length == orderProvider.staff.length && orderProvider.staff.isNotEmpty,
                                    tristate: _selectedStaff.isNotEmpty && _selectedStaff.length < orderProvider.staff.length,
                                    onChanged: (value) {
                                      setState(() {
                                        if (value == true) {
                                          _selectedStaff = List.from(orderProvider.staff);
                                        } else {
                                          _selectedStaff.clear();
                                        }
                                      });
                                    },
                                    activeColor: Colors.white,
                                    checkColor: Colors.blue.shade600,
                                    side: const BorderSide(color: Colors.white, width: 2),
                                  ),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      const Text(
                                        'Pilih Staff Cuci',
                                        style: TextStyle(
                                          fontWeight: FontWeight.w700,
                                          fontSize: 16,
                                          color: Color(0xFF1F2937),
                                        ),
                                      ),
                                      Text(
                                        '${_selectedStaff.length} dari ${orderProvider.staff.length} staff dipilih',
                                        style: TextStyle(
                                          fontSize: 14,
                                          color: Colors.blue.shade700,
                                          fontWeight: FontWeight.w500,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                Container(
                                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                                  decoration: BoxDecoration(
                                    color: Colors.blue.shade600,
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                  child: Text(
                                    _selectedStaff.length == orderProvider.staff.length 
                                        ? 'Semua' 
                                        : '${_selectedStaff.length}',
                                    style: const TextStyle(
                                      color: Colors.white,
                                      fontWeight: FontWeight.w600,
                                      fontSize: 12,
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 16),
                          
                          // Staff List
                          Container(
                            decoration: BoxDecoration(
                              border: Border.all(color: Colors.grey.shade300),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Column(
                              children: orderProvider.staff.asMap().entries.map((entry) {
                                final index = entry.key;
                                final staff = entry.value;
                                final isSelected = _selectedStaff.contains(staff);
                                final isLast = index == orderProvider.staff.length - 1;
                                
                                return Container(
                                  decoration: BoxDecoration(
                                    border: isLast ? null : Border(
                                      bottom: BorderSide(color: Colors.grey.shade200),
                                    ),
                                  ),
                                  child: Material(
                                    color: isSelected ? Colors.blue.shade50 : Colors.white,
                                    borderRadius: BorderRadius.vertical(
                                      top: index == 0 ? const Radius.circular(12) : Radius.zero,
                                      bottom: isLast ? const Radius.circular(12) : Radius.zero,
                                    ),
                                    child: InkWell(
                                      onTap: () {
                                        setState(() {
                                          if (isSelected) {
                                            _selectedStaff.remove(staff);
                                          } else {
                                            _selectedStaff.add(staff);
                                          }
                                        });
                                      },
                                      borderRadius: BorderRadius.vertical(
                                        top: index == 0 ? const Radius.circular(12) : Radius.zero,
                                        bottom: isLast ? const Radius.circular(12) : Radius.zero,
                                      ),
                                      child: Padding(
                                        padding: const EdgeInsets.all(16),
                                        child: Row(
                                          children: [
                                            Container(
                                              width: 24,
                                              height: 24,
                                              decoration: BoxDecoration(
                                                color: isSelected ? Colors.blue.shade600 : Colors.transparent,
                                                border: Border.all(
                                                  color: isSelected ? Colors.blue.shade600 : Colors.grey.shade400,
                                                  width: 2,
                                                ),
                                                borderRadius: BorderRadius.circular(6),
                                              ),
                                              child: isSelected
                                                  ? const Icon(
                                                      Icons.check_rounded,
                                                      color: Colors.white,
                                                      size: 16,
                                                    )
                                                  : null,
                                            ),
                                            const SizedBox(width: 16),
                                            Container(
                                              width: 48,
                                              height: 48,
                                              decoration: BoxDecoration(
                                                gradient: LinearGradient(
                                                  colors: [
                                                    Colors.blue.shade400,
                                                    Colors.blue.shade600,
                                                  ],
                                                ),
                                                borderRadius: BorderRadius.circular(12),
                                              ),
                                              child: const Icon(
                                                Icons.person_rounded,
                                                color: Colors.white,
                                                size: 24,
                                              ),
                                            ),
                                            const SizedBox(width: 16),
                                            Expanded(
                                              child: Column(
                                                crossAxisAlignment: CrossAxisAlignment.start,
                                                children: [
                                                  Text(
                                                    staff.name,
                                                    style: TextStyle(
                                                      fontWeight: FontWeight.w700,
                                                      fontSize: 16,
                                                      color: isSelected ? Colors.blue.shade800 : const Color(0xFF1F2937),
                                                    ),
                                                  ),
                                                  const SizedBox(height: 4),
                                                  Row(
                                                    children: [
                                                      Container(
                                                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                                        decoration: BoxDecoration(
                                                          color: Colors.grey.shade100,
                                                          borderRadius: BorderRadius.circular(12),
                                                        ),
                                                        child: Text(
                                                          staff.position,
                                                          style: TextStyle(
                                                            fontSize: 12,
                                                            color: Colors.grey.shade700,
                                                            fontWeight: FontWeight.w500,
                                                          ),
                                                        ),
                                                      ),
                                                      const SizedBox(width: 8),
                                                      Container(
                                                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                                        decoration: BoxDecoration(
                                                          color: Colors.green.shade100,
                                                          borderRadius: BorderRadius.circular(12),
                                                        ),
                                                        child: Text(
                                                          'Komisi ${(staff.commissionRate ?? 15.0).toStringAsFixed(0)}%',
                                                          style: TextStyle(
                                                            fontSize: 12,
                                                            color: Colors.green.shade700,
                                                            fontWeight: FontWeight.w600,
                                                          ),
                                                        ),
                                                      ),
                                                    ],
                                                  ),
                                                ],
                                              ),
                                            ),
                                            if (isSelected)
                                              Container(
                                                padding: const EdgeInsets.all(8),
                                                decoration: BoxDecoration(
                                                  color: Colors.blue.shade600,
                                                  borderRadius: BorderRadius.circular(20),
                                                ),
                                                child: const Icon(
                                                  Icons.check_rounded,
                                                  color: Colors.white,
                                                  size: 16,
                                                ),
                                              ),
                                          ],
                                        ),
                                      ),
                                    ),
                                  ),
                                );
                              }).toList(),
                            ),
                          ),
                          const SizedBox(height: 16),
                          
                          // Commission Info
                          Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              gradient: LinearGradient(
                                colors: [
                                  Colors.green.shade50,
                                  Colors.blue.shade50,
                                ],
                              ),
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: Colors.green.shade200),
                            ),
                            child: Row(
                              children: [
                                Container(
                                  padding: const EdgeInsets.all(8),
                                  decoration: BoxDecoration(
                                    color: Colors.green.shade600,
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                  child: const Icon(
                                    Icons.info_outline_rounded,
                                    color: Colors.white,
                                    size: 20,
                                  ),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      const Text(
                                        'Pembagian Komisi',
                                        style: TextStyle(
                                          fontWeight: FontWeight.w700,
                                          fontSize: 14,
                                          color: Color(0xFF1F2937),
                                        ),
                                      ),
                                      Text(
                                        _selectedStaff.isEmpty 
                                            ? 'Pilih staff untuk melihat pembagian komisi'
                                            : 'Komisi akan dibagi rata antara ${_selectedStaff.length} staff yang dipilih',
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.grey.shade600,
                                          fontWeight: FontWeight.w500,
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                  ],
                ),
                const SizedBox(height: 16),
                
                // Wash Lane Selection
                _buildSectionCard(
                  title: 'Pilih Jalur Cuci',
                  icon: Icons.route,
                  children: [
                    // Auto assign toggle
                    Container(
                      padding: const EdgeInsets.all(16),
                      decoration: BoxDecoration(
                        color: Colors.blue.shade50,
                        borderRadius: BorderRadius.circular(12),
                        border: Border.all(color: Colors.blue.shade200),
                      ),
                      child: Row(
                        children: [
                          Container(
                            decoration: BoxDecoration(
                              color: Colors.blue.shade600,
                              borderRadius: BorderRadius.circular(8),
                            ),
                            child: Checkbox(
                              value: _autoAssignLane,
                              onChanged: (value) {
                                setState(() {
                                  _autoAssignLane = value ?? true;
                                  if (_autoAssignLane) {
                                    _selectedWashLane = null;
                                  }
                                });
                              },
                              activeColor: Colors.white,
                              checkColor: Colors.blue.shade600,
                              side: const BorderSide(color: Colors.white, width: 2),
                            ),
                          ),
                          const SizedBox(width: 12),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                const Text(
                                  'Otomatis Pilih Jalur',
                                  style: TextStyle(
                                    fontWeight: FontWeight.w700,
                                    fontSize: 16,
                                    color: Color(0xFF1F2937),
                                  ),
                                ),
                                Text(
                                  'Sistem akan memilih jalur terbaik berdasarkan jenis kendaraan',
                                  style: TextStyle(
                                    fontSize: 14,
                                    color: Colors.blue.shade700,
                                    fontWeight: FontWeight.w500,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      ),
                    ),
                    
                    if (!_autoAssignLane) ...[
                      const SizedBox(height: 16),
                      
                      // Manual lane selection
                      if (orderProvider.isLoading)
                        const Center(
                          child: Padding(
                            padding: EdgeInsets.all(16.0),
                            child: CircularProgressIndicator(),
                          ),
                        )
                      else if (orderProvider.washLanes.isEmpty)
                        Center(
                          child: Padding(
                            padding: const EdgeInsets.all(16.0),
                            child: Column(
                              children: [
                                const Text('Tidak ada jalur cuci tersedia'),
                                const SizedBox(height: 8),
                                ElevatedButton.icon(
                                  onPressed: () {
                                    orderProvider.loadWashLanes();
                                  },
                                  icon: const Icon(Icons.refresh),
                                  label: const Text('Muat Ulang'),
                                ),
                              ],
                            ),
                          ),
                        )
                      else
                        Container(
                          decoration: BoxDecoration(
                            border: Border.all(color: Colors.grey.shade300),
                            borderRadius: BorderRadius.circular(12),
                          ),
                          child: Column(
                            children: orderProvider.washLanes.asMap().entries.map((entry) {
                              final index = entry.key;
                              final lane = entry.value;
                              final isSelected = _selectedWashLane?.id == lane.id;
                              final isLast = index == orderProvider.washLanes.length - 1;
                              final canAccept = lane.canAcceptOrder;
                              
                              return Container(
                                decoration: BoxDecoration(
                                  border: isLast ? null : Border(
                                    bottom: BorderSide(color: Colors.grey.shade200),
                                  ),
                                ),
                                child: Material(
                                  color: isSelected 
                                      ? Colors.blue.shade50 
                                      : canAccept 
                                          ? Colors.white 
                                          : Colors.grey.shade50,
                                  borderRadius: BorderRadius.vertical(
                                    top: index == 0 ? const Radius.circular(12) : Radius.zero,
                                    bottom: isLast ? const Radius.circular(12) : Radius.zero,
                                  ),
                                  child: InkWell(
                                    onTap: canAccept ? () {
                                      setState(() {
                                        _selectedWashLane = lane;
                                      });
                                    } : null,
                                    borderRadius: BorderRadius.vertical(
                                      top: index == 0 ? const Radius.circular(12) : Radius.zero,
                                      bottom: isLast ? const Radius.circular(12) : Radius.zero,
                                    ),
                                    child: Padding(
                                      padding: const EdgeInsets.all(16),
                                      child: Row(
                                        children: [
                                          Container(
                                            width: 24,
                                            height: 24,
                                            decoration: BoxDecoration(
                                              color: isSelected 
                                                  ? Colors.blue.shade600 
                                                  : Colors.transparent,
                                              border: Border.all(
                                                color: isSelected 
                                                    ? Colors.blue.shade600 
                                                    : canAccept 
                                                        ? Colors.grey.shade400 
                                                        : Colors.grey.shade300,
                                                width: 2,
                                              ),
                                              borderRadius: BorderRadius.circular(6),
                                            ),
                                            child: isSelected
                                                ? const Icon(
                                                    Icons.check_rounded,
                                                    color: Colors.white,
                                                    size: 16,
                                                  )
                                                : null,
                                          ),
                                          const SizedBox(width: 16),
                                          Container(
                                            width: 48,
                                            height: 48,
                                            decoration: BoxDecoration(
                                              gradient: LinearGradient(
                                                colors: canAccept ? [
                                                  Colors.green.shade400,
                                                  Colors.green.shade600,
                                                ] : [
                                                  Colors.grey.shade400,
                                                  Colors.grey.shade600,
                                                ],
                                              ),
                                              borderRadius: BorderRadius.circular(12),
                                            ),
                                            child: Icon(
                                              Icons.route_rounded,
                                              color: Colors.white,
                                              size: 24,
                                            ),
                                          ),
                                          const SizedBox(width: 16),
                                          Expanded(
                                            child: Column(
                                              crossAxisAlignment: CrossAxisAlignment.start,
                                              children: [
                                                Row(
                                                  children: [
                                                    Text(
                                                      lane.name,
                                                      style: TextStyle(
                                                        fontWeight: FontWeight.w700,
                                                        fontSize: 16,
                                                        color: canAccept 
                                                            ? (isSelected ? Colors.blue.shade800 : const Color(0xFF1F2937))
                                                            : Colors.grey.shade600,
                                                      ),
                                                    ),
                                                    const SizedBox(width: 8),
                                                    Container(
                                                      padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                                                      decoration: BoxDecoration(
                                                        color: lane.type == 'general' 
                                                            ? Colors.blue.shade100
                                                            : lane.type == 'motor'
                                                                ? Colors.orange.shade100
                                                                : Colors.purple.shade100,
                                                        borderRadius: BorderRadius.circular(8),
                                                      ),
                                                      child: Text(
                                                        lane.type.toUpperCase(),
                                                        style: TextStyle(
                                                          fontSize: 10,
                                                          fontWeight: FontWeight.w600,
                                                          color: lane.type == 'general' 
                                                              ? Colors.blue.shade700
                                                              : lane.type == 'motor'
                                                                  ? Colors.orange.shade700
                                                                  : Colors.purple.shade700,
                                                        ),
                                                      ),
                                                    ),
                                                  ],
                                                ),
                                                const SizedBox(height: 4),
                                                Row(
                                                  children: [
                                                    Container(
                                                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                                                      decoration: BoxDecoration(
                                                        color: canAccept 
                                                            ? Colors.green.shade100
                                                            : Colors.red.shade100,
                                                        borderRadius: BorderRadius.circular(12),
                                                      ),
                                                      child: Text(
                                                        '${lane.currentQueue}/${lane.maxQueue}',
                                                        style: TextStyle(
                                                          fontSize: 12,
                                                          color: canAccept 
                                                              ? Colors.green.shade700
                                                              : Colors.red.shade700,
                                                          fontWeight: FontWeight.w600,
                                                        ),
                                                      ),
                                                    ),
                                                    const SizedBox(width: 8),
                                                    Text(
                                                      canAccept ? 'Tersedia' : 'Penuh',
                                                      style: TextStyle(
                                                        fontSize: 12,
                                                        color: canAccept 
                                                            ? Colors.green.shade600
                                                            : Colors.red.shade600,
                                                        fontWeight: FontWeight.w500,
                                                      ),
                                                    ),
                                                  ],
                                                ),
                                              ],
                                            ),
                                          ),
                                          if (isSelected)
                                            Container(
                                              padding: const EdgeInsets.all(8),
                                              decoration: BoxDecoration(
                                                color: Colors.blue.shade600,
                                                borderRadius: BorderRadius.circular(20),
                                              ),
                                              child: const Icon(
                                                Icons.check_rounded,
                                                color: Colors.white,
                                                size: 16,
                                              ),
                                            ),
                                        ],
                                      ),
                                    ),
                                  ),
                                ),
                              );
                            }).toList(),
                          ),
                        ),
                    ],
                  ],
                ),
                const SizedBox(height: 16),
                
                // Additional Details
                _buildSectionCard(
                  title: 'Detail Tambahan',
                  icon: Icons.note_add,
                  children: [
                    TextFormField(
                      controller: _additionalFeeController,
                      decoration: const InputDecoration(
                        labelText: 'Biaya Tambahan (Opsional)',
                        hintText: '0',
                        border: OutlineInputBorder(),
                        prefixText: 'Rp ',
                        prefixIcon: Icon(Icons.attach_money),
                      ),
                      keyboardType: TextInputType.number,
                      validator: (value) {
                        if (value != null && value.isNotEmpty) {
                          final fee = int.tryParse(value);
                          if (fee == null || fee < 0) {
                            return 'Masukkan jumlah yang valid';
                          }
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    
                    TextFormField(
                      controller: _notesController,
                      decoration: const InputDecoration(
                        labelText: 'Catatan (Opsional)',
                        hintText: 'Instruksi khusus...',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.note),
                      ),
                      maxLines: 3,
                    ),
                  ],
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  Widget _buildStep3Payment() {
    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Pembayaran & Konfirmasi',
            style: TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 8),
          const Text(
            'Pilih metode pembayaran dan konfirmasi pesanan',
            style: TextStyle(
              fontSize: 16,
              color: Colors.grey,
            ),
          ),
          const SizedBox(height: 24),
          
          // Order Summary
          if (_selectedService != null)
            Card(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      children: [
                        Icon(Icons.receipt, color: Colors.blue.shade600),
                        const SizedBox(width: 8),
                        const Text(
                          'Ringkasan Pesanan',
                          style: TextStyle(
                            fontWeight: FontWeight.bold,
                            fontSize: 18,
                          ),
                        ),
                      ],
                    ),
                    const Divider(),
                    _buildSummaryRow('Kendaraan:', _selectedVehicleType ?? '-'),
                    _buildSummaryRow('Plat Nomor:', _licensePlateController.text.toUpperCase()),
                    _buildSummaryRow('Layanan:', _selectedService?.name ?? '-'),
                    _buildSummaryRow('Staff:', _selectedStaff.isEmpty ? '-' : '${_selectedStaff.length} staff'),
                    if (_selectedStaff.isNotEmpty) ...[
                      const SizedBox(height: 4),
                      ...(_selectedStaff.map((staff) => Padding(
                        padding: const EdgeInsets.only(left: 16),
                        child: Text(
                          '• ${staff.name}',
                          style: const TextStyle(fontSize: 12, color: Colors.grey),
                        ),
                      ))),
                    ],
                    const Divider(),
                    _buildSummaryRow('Harga Layanan:', 'Rp ${_formatCurrency(_selectedService!.price)}'),
                    if (_additionalFeeController.text.isNotEmpty)
                      _buildSummaryRow(
                        'Biaya Tambahan:', 
                        'Rp ${_formatCurrency(int.tryParse(_additionalFeeController.text) ?? 0)}'
                      ),
                    const Divider(),
                    _buildSummaryRow(
                      'Total:',
                      'Rp ${_formatCurrency(_selectedService!.price + (int.tryParse(_additionalFeeController.text) ?? 0))}',
                      isTotal: true,
                    ),
                    if (_selectedStaff.isNotEmpty) ...[
                      const Divider(),
                      const Text(
                        'Pembagian Komisi:',
                        style: TextStyle(fontWeight: FontWeight.w600, fontSize: 14),
                      ),
                      const SizedBox(height: 8),
                      _buildCommissionBreakdown(),
                    ],
                  ],
                ),
              ),
            ),
          const SizedBox(height: 24),
          
          // Payment Method
          _buildSectionCard(
            title: 'Metode Pembayaran',
            icon: Icons.payment,
            children: [
              const Text(
                'Pilih metode pembayaran (opsional):',
                style: TextStyle(fontWeight: FontWeight.w500),
              ),
              const SizedBox(height: 12),
              Wrap(
                spacing: 8,
                runSpacing: 8,
                children: _paymentMethods.map((method) {
                  final isSelected = _paymentMethod == method;
                  return FilterChip(
                    label: Text(method.toUpperCase()),
                    selected: isSelected,
                    onSelected: (selected) {
                      setState(() {
                        _paymentMethod = selected ? method : null;
                      });
                    },
                    selectedColor: Colors.blue.shade100,
                    checkmarkColor: Colors.blue.shade600,
                  );
                }).toList(),
              ),
              const SizedBox(height: 16),
              
              CheckboxListTile(
                title: const Text('Tandai sebagai selesai'),
                subtitle: const Text('Untuk cuci express yang langsung selesai'),
                value: _autoComplete,
                onChanged: (value) {
                  setState(() {
                    _autoComplete = value ?? false;
                  });
                },
                contentPadding: EdgeInsets.zero,
              ),
              
              // Debug section (hanya untuk development)
              if (kDebugMode) ...[
                const SizedBox(height: 16),
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.orange.shade50,
                    border: Border.all(color: Colors.orange.shade200),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const Text(
                        'Debug Info:',
                        style: TextStyle(fontWeight: FontWeight.bold, color: Colors.orange),
                      ),
                      Text('Vehicle: $_selectedVehicleType'),
                      Text('License: ${_licensePlateController.text}'),
                      Text('Service: ${_selectedService?.name ?? 'None'}'),
                      Text('Staff: ${_selectedStaff.isEmpty ? 'None' : _selectedStaff.map((s) => s.name).join(', ')} (${_selectedStaff.length})'),
                      Text('Wash Lane: ${_autoAssignLane ? 'Auto-assign' : _selectedWashLane?.name ?? 'None'}'),
                      Text('Payment: $_paymentMethod'),
                      const SizedBox(height: 8),
                      ElevatedButton(
                        onPressed: () async {
                          print('🔘 Debug button pressed - bypassing validation');
                          if (_selectedVehicleType != null && 
                              _licensePlateController.text.isNotEmpty &&
                              _selectedService != null && 
                              _selectedStaff.isNotEmpty) {
                            
                            final orderProvider = Provider.of<OrderProvider>(context, listen: false);
                            
                            final success = await orderProvider.createOrder(
                              licensePlate: _licensePlateController.text.trim().toUpperCase(),
                              vehicleType: _selectedVehicleType!,
                              vehicleModel: _vehicleModelController.text.trim().isNotEmpty 
                                  ? _vehicleModelController.text.trim() 
                                  : null,
                              vehicleColor: _vehicleColorController.text.trim().isNotEmpty 
                                  ? _vehicleColorController.text.trim() 
                                  : null,
                              serviceId: _selectedService!.id,
                              staffIds: _selectedStaff.map((s) => s.id).toList(),
                              washLaneId: _autoAssignLane ? null : _selectedWashLane?.id,
                              additionalFee: _additionalFeeController.text.isNotEmpty 
                                  ? int.tryParse(_additionalFeeController.text) 
                                  : null,
                              notes: _notesController.text.trim().isNotEmpty 
                                  ? _notesController.text.trim() 
                                  : null,
                              paymentMethod: _paymentMethod,
                              autoComplete: _autoComplete,
                            );
                            
                            if (success && mounted) {
                              _showSnackBar('Debug: Pesanan berhasil dibuat!', Colors.green);
                              Navigator.of(context).pop();
                            } else if (mounted) {
                              _showSnackBar('Debug: ${orderProvider.errorMessage}', Colors.red);
                            }
                          } else {
                            _showSnackBar('Debug: Data belum lengkap', Colors.orange);
                          }
                        },
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.orange,
                          foregroundColor: Colors.white,
                        ),
                        child: const Text('Debug: Force Create Order'),
                      ),
                    ],
                  ),
                ),
              ],
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildSectionCard({
    required String title,
    required IconData icon,
    required List<Widget> children,
  }) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(icon, color: Colors.blue.shade600),
                const SizedBox(width: 8),
                Text(
                  title,
                  style: const TextStyle(
                    fontWeight: FontWeight.bold,
                    fontSize: 16,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            ...children,
          ],
        ),
      ),
    );
  }

  Widget _buildSummaryRow(String label, String value, {bool isTotal = false, bool isSmall = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(
              fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
              fontSize: isTotal ? 16 : (isSmall ? 12 : 14),
              color: isSmall ? Colors.grey.shade600 : null,
            ),
          ),
          Text(
            value,
            style: TextStyle(
              fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
              fontSize: isTotal ? 16 : (isSmall ? 12 : 14),
              color: isTotal ? Colors.green.shade600 : (isSmall ? Colors.grey.shade600 : null),
            ),
          ),
        ],
      ),
    );
  }

  String _formatCurrency(int amount) {
    return amount.toString().replaceAllMapped(
      RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
      (Match m) => '${m[1]},',
    );
  }

  String _formatDate(String dateStr) {
    try {
      final date = DateTime.parse(dateStr);
      final now = DateTime.now();
      final difference = now.difference(date).inDays;
      
      if (difference == 0) {
        return 'Hari ini';
      } else if (difference == 1) {
        return 'Kemarin';
      } else if (difference < 7) {
        return '$difference hari lalu';
      } else if (difference < 30) {
        final weeks = (difference / 7).floor();
        return '$weeks minggu lalu';
      } else {
        final months = (difference / 30).floor();
        return '$months bulan lalu';
      }
    } catch (e) {
      return dateStr;
    }
  }

  Widget _buildCommissionBreakdown() {
    if (_selectedService == null || _selectedStaff.isEmpty) {
      return const SizedBox.shrink();
    }

    final totalPrice = _selectedService!.price + (int.tryParse(_additionalFeeController.text) ?? 0);
    const staffCommissionRate = 15.0; // Default 15%, should be fetched from API
    const ownerCommissionRate = 85.0; // Default 85%
    
    final totalStaffCommission = totalPrice * (staffCommissionRate / 100);
    final commissionPerStaff = totalStaffCommission / _selectedStaff.length;
    final ownerCommission = totalPrice * (ownerCommissionRate / 100);

    return Column(
      children: [
        _buildSummaryRow(
          'Komisi per Staff:', 
          'Rp ${_formatCurrency(commissionPerStaff.round())}',
          isSmall: true,
        ),
        _buildSummaryRow(
          'Total Komisi Staff:', 
          'Rp ${_formatCurrency(totalStaffCommission.round())}',
          isSmall: true,
        ),
        _buildSummaryRow(
          'Komisi Owner:', 
          'Rp ${_formatCurrency(ownerCommission.round())}',
          isSmall: true,
        ),
        const SizedBox(height: 4),
        Text(
          '${_selectedStaff.length} staff • Staff: ${staffCommissionRate.toInt()}% • Owner: ${ownerCommissionRate.toInt()}%',
          style: const TextStyle(fontSize: 10, color: Colors.grey),
        ),
      ],
    );
  }
}