import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';
import 'package:provider/provider.dart';
import '../providers/order_provider.dart';
import '../models/service.dart';
import '../models/staff.dart';

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
  Staff? _selectedStaff;
  
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
      print('🔄 Loading services and staff for create order...');
      orderProvider.loadServices();
      orderProvider.loadStaff();
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
      if (_selectedStaff == null) {
        _showSnackBar('Pilih staff terlebih dahulu', Colors.red);
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
    print('   - Selected staff: ${_selectedStaff?.name}');
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

    if (_selectedStaff == null) {
      print('❌ No staff selected');
      _showSnackBar('Pilih staff terlebih dahulu', Colors.red);
      return;
    }
    print('✅ Staff selected: ${_selectedStaff!.name}');

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
      staffId: _selectedStaff!.id,
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
      appBar: AppBar(
        title: const Text('Buat Pesanan Baru'),
        backgroundColor: Colors.blue.shade600,
        foregroundColor: Colors.white,
        elevation: 0,
      ),
      body: Column(
        children: [
          // Progress Indicator
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.blue.shade600,
              borderRadius: const BorderRadius.only(
                bottomLeft: Radius.circular(20),
                bottomRight: Radius.circular(20),
              ),
            ),
            child: Row(
              children: [
                _buildStepIndicator(0, 'Kendaraan', Icons.directions_car),
                _buildStepConnector(0),
                _buildStepIndicator(1, 'Detail', Icons.edit),
                _buildStepConnector(1),
                _buildStepIndicator(2, 'Bayar', Icons.payment),
              ],
            ),
          ),
          
          // Content
          Expanded(
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
          
          // Navigation Buttons
          Container(
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: Colors.white,
              boxShadow: [
                BoxShadow(
                  color: Colors.grey.withValues(alpha: 0.2),
                  blurRadius: 10,
                  offset: const Offset(0, -2),
                ),
              ],
            ),
            child: Row(
              children: [
                if (_currentStep > 0)
                  Expanded(
                    child: OutlinedButton(
                      onPressed: _previousStep,
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      child: const Text('Kembali'),
                    ),
                  ),
                if (_currentStep > 0) const SizedBox(width: 16),
                Expanded(
                  child: Consumer<OrderProvider>(
                    builder: (context, orderProvider, child) {
                      return ElevatedButton(
                        onPressed: orderProvider.isLoading 
                            ? null 
                            : (_currentStep == 0 && _selectedVehicleType == null)
                                ? null
                            : (_currentStep == 1 && (_selectedService == null || _selectedStaff == null))
                                ? null
                            : _currentStep == 2 
                                ? () async {
                                    print('🔘 Button pressed in step 3');
                                    try {
                                      await _createOrder();
                                    } catch (e) {
                                      print('❌ Error in button handler: $e');
                                      _showSnackBar('Terjadi kesalahan: $e', Colors.red);
                                    }
                                  }
                                : _nextStep,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.blue.shade600,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 16),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                        child: orderProvider.isLoading
                            ? Row(
                                mainAxisAlignment: MainAxisAlignment.center,
                                children: const [
                                  SizedBox(
                                    height: 20,
                                    width: 20,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 2,
                                      valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                    ),
                                  ),
                                  SizedBox(width: 8),
                                  Text('Memproses...'),
                                ],
                              )
                            : Text(
                                _currentStep == 2 ? 'Buat Pesanan' : 'Lanjut',
                                style: const TextStyle(
                                  fontSize: 16,
                                  fontWeight: FontWeight.bold,
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
    );
  }

  Widget _buildStepIndicator(int step, String title, IconData icon) {
    final isActive = step <= _currentStep;
    final isCompleted = step < _currentStep;
    
    return Column(
      children: [
        Container(
          width: 40,
          height: 40,
          decoration: BoxDecoration(
            color: isActive ? Colors.white : Colors.white.withValues(alpha: 0.3),
            shape: BoxShape.circle,
          ),
          child: Icon(
            isCompleted ? Icons.check : icon,
            color: isActive ? Colors.blue.shade600 : Colors.white,
            size: 20,
          ),
        ),
        const SizedBox(height: 8),
        Text(
          title,
          style: TextStyle(
            color: Colors.white,
            fontSize: 12,
            fontWeight: isActive ? FontWeight.bold : FontWeight.normal,
          ),
        ),
      ],
    );
  }

  Widget _buildStepConnector(int step) {
    final isCompleted = step < _currentStep;
    
    return Expanded(
      child: Container(
        height: 2,
        margin: const EdgeInsets.only(bottom: 24),
        color: isCompleted ? Colors.white : Colors.white.withValues(alpha: 0.3),
      ),
    );
  }

  Widget _buildStep1VehicleType() {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Pilih Jenis Kendaraan',
            style: TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 8),
          const Text(
            'Pilih jenis kendaraan yang akan dicuci',
            style: TextStyle(
              fontSize: 16,
              color: Colors.grey,
            ),
          ),
          const SizedBox(height: 32),
          
          Expanded(
            child: GridView.builder(
              gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                crossAxisCount: 2,
                crossAxisSpacing: 16,
                mainAxisSpacing: 16,
                childAspectRatio: 1.2,
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
                    duration: const Duration(milliseconds: 200),
                    decoration: BoxDecoration(
                      color: isSelected 
                          ? vehicleType['color'].withValues(alpha: 0.1)
                          : Colors.white,
                      border: Border.all(
                        color: isSelected 
                            ? vehicleType['color']
                            : Colors.grey.shade300,
                        width: isSelected ? 2 : 1,
                      ),
                      borderRadius: BorderRadius.circular(16),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.grey.withValues(alpha: 0.1),
                          blurRadius: 8,
                          offset: const Offset(0, 2),
                        ),
                      ],
                    ),
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(
                          vehicleType['icon'],
                          size: 48,
                          color: isSelected 
                              ? vehicleType['color']
                              : Colors.grey.shade600,
                        ),
                        const SizedBox(height: 12),
                        Text(
                          vehicleType['type'],
                          style: TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                            color: isSelected 
                                ? vehicleType['color']
                                : Colors.grey.shade800,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          vehicleType['description'],
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            fontSize: 12,
                            color: Colors.grey.shade600,
                          ),
                        ),
                      ],
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
                    TextFormField(
                      controller: _licensePlateController,
                      decoration: const InputDecoration(
                        labelText: 'Nomor Plat *',
                        hintText: 'B 1234 ABC',
                        border: OutlineInputBorder(),
                        prefixIcon: Icon(Icons.confirmation_number),
                      ),
                      textCapitalization: TextCapitalization.characters,
                      validator: (value) {
                        if (value == null || value.trim().isEmpty) {
                          return 'Nomor plat harus diisi';
                        }
                        return null;
                      },
                    ),
                    const SizedBox(height: 16),
                    
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
                      DropdownButtonFormField<Staff>(
                        initialValue: _selectedStaff,
                        decoration: const InputDecoration(
                          labelText: 'Pilih Staff *',
                          border: OutlineInputBorder(),
                          prefixIcon: Icon(Icons.person),
                        ),
                        items: orderProvider.staff.map((staff) {
                          return DropdownMenuItem(
                            value: staff,
                            child: Text('${staff.name} (${staff.position})'),
                          );
                        }).toList(),
                        onChanged: (value) {
                          setState(() {
                            _selectedStaff = value;
                          });
                        },
                      ),
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
                    _buildSummaryRow('Staff:', _selectedStaff?.name ?? '-'),
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
                      Text('Staff: ${_selectedStaff?.name ?? 'None'}'),
                      Text('Payment: $_paymentMethod'),
                      const SizedBox(height: 8),
                      ElevatedButton(
                        onPressed: () async {
                          print('🔘 Debug button pressed - bypassing validation');
                          if (_selectedVehicleType != null && 
                              _licensePlateController.text.isNotEmpty &&
                              _selectedService != null && 
                              _selectedStaff != null) {
                            
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
                              staffId: _selectedStaff!.id,
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

  Widget _buildSummaryRow(String label, String value, {bool isTotal = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(
              fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
              fontSize: isTotal ? 16 : 14,
            ),
          ),
          Text(
            value,
            style: TextStyle(
              fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
              fontSize: isTotal ? 16 : 14,
              color: isTotal ? Colors.green.shade600 : null,
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
}