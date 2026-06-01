import 'package:flutter/material.dart';
import 'dart:async';
import '../services/api_service.dart';

class VehicleData {
  final int id;
  final String licensePlate;
  final String type;
  final String? model;
  final String? color;
  final int totalWashes;
  final String? lastWashDate;
  final String? lastWashService;

  VehicleData({
    required this.id,
    required this.licensePlate,
    required this.type,
    this.model,
    this.color,
    required this.totalWashes,
    this.lastWashDate,
    this.lastWashService,
  });

  factory VehicleData.fromJson(Map<String, dynamic> json) {
    return VehicleData(
      id: json['id'],
      licensePlate: json['license_plate'],
      type: json['type'],
      model: json['model'],
      color: json['color'],
      totalWashes: json['total_washes'],
      lastWashDate: json['last_wash_date'],
      lastWashService: json['last_wash_service'],
    );
  }
}

class VehicleHistory {
  final VehicleData vehicle;
  final Map<String, dynamic> statistics;
  final List<Map<String, dynamic>> recentOrders;

  VehicleHistory({
    required this.vehicle,
    required this.statistics,
    required this.recentOrders,
  });

  factory VehicleHistory.fromJson(Map<String, dynamic> json) {
    return VehicleHistory(
      vehicle: VehicleData.fromJson(json['vehicle']),
      statistics: json['statistics'],
      recentOrders: List<Map<String, dynamic>>.from(json['recent_orders']),
    );
  }
}

class LicensePlateAutocomplete extends StatefulWidget {
  final TextEditingController controller;
  final Function(VehicleData) onVehicleSelected;
  final String? selectedVehicleType;

  const LicensePlateAutocomplete({
    super.key,
    required this.controller,
    required this.onVehicleSelected,
    this.selectedVehicleType,
  });

  @override
  State<LicensePlateAutocomplete> createState() => _LicensePlateAutocompleteState();
}

class _LicensePlateAutocompleteState extends State<LicensePlateAutocomplete> {
  List<VehicleData> _suggestions = [];
  bool _isLoading = false;
  Timer? _debounceTimer;
  final LayerLink _layerLink = LayerLink();
  OverlayEntry? _overlayEntry;
  final FocusNode _focusNode = FocusNode();
  final ApiService _apiService = ApiService();

  @override
  void initState() {
    super.initState();
    widget.controller.addListener(_onTextChanged);
    _focusNode.addListener(_onFocusChanged);
  }

  @override
  void dispose() {
    _debounceTimer?.cancel();
    _removeOverlay();
    widget.controller.removeListener(_onTextChanged);
    _focusNode.removeListener(_onFocusChanged);
    _focusNode.dispose();
    super.dispose();
  }

  void _onTextChanged() {
    _debounceTimer?.cancel();
    _debounceTimer = Timer(const Duration(milliseconds: 500), () {
      _searchVehicles(widget.controller.text);
    });
  }

  void _onFocusChanged() {
    if (!_focusNode.hasFocus) {
      _removeOverlay();
    }
  }

  Future<void> _searchVehicles(String query) async {
    if (query.length < 2) {
      setState(() {
        _suggestions = [];
      });
      _removeOverlay();
      return;
    }

    setState(() {
      _isLoading = true;
    });

    try {
      final vehicles = await _apiService.searchVehicles(query, widget.selectedVehicleType);
      setState(() {
        _suggestions = vehicles.map((json) => VehicleData.fromJson(json)).toList();
        _isLoading = false;
      });
      
      if (_suggestions.isNotEmpty && _focusNode.hasFocus) {
        _showOverlay();
      } else {
        _removeOverlay();
      }
    } catch (e) {
      setState(() {
        _isLoading = false;
        _suggestions = [];
      });
      _removeOverlay();
      print('Error searching vehicles: $e');
    }
  }

  void _showOverlay() {
    _removeOverlay();
    
    _overlayEntry = OverlayEntry(
      builder: (context) => Positioned(
        width: MediaQuery.of(context).size.width - 32,
        child: CompositedTransformFollower(
          link: _layerLink,
          showWhenUnlinked: false,
          offset: const Offset(0, 60),
          child: Material(
            elevation: 8,
            borderRadius: BorderRadius.circular(12),
            child: Container(
              constraints: const BoxConstraints(maxHeight: 300),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.grey.shade300),
              ),
              child: ListView.separated(
                padding: EdgeInsets.zero,
                shrinkWrap: true,
                itemCount: _suggestions.length,
                separatorBuilder: (context, index) => Divider(
                  height: 1,
                  color: Colors.grey.shade200,
                ),
                itemBuilder: (context, index) {
                  final vehicle = _suggestions[index];
                  return _buildVehicleSuggestion(vehicle);
                },
              ),
            ),
          ),
        ),
      ),
    );

    Overlay.of(context).insert(_overlayEntry!);
  }

  void _removeOverlay() {
    _overlayEntry?.remove();
    _overlayEntry = null;
  }

  Widget _buildVehicleSuggestion(VehicleData vehicle) {
    return InkWell(
      onTap: () => _selectVehicle(vehicle),
      borderRadius: BorderRadius.circular(12),
      child: Container(
        padding: const EdgeInsets.all(16),
        child: Row(
          children: [
            // Vehicle Icon
            Container(
              width: 48,
              height: 48,
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  colors: [
                    vehicle.type == 'Motor' ? Colors.orange.shade400 : Colors.blue.shade400,
                    vehicle.type == 'Motor' ? Colors.orange.shade600 : Colors.blue.shade600,
                  ],
                ),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(
                vehicle.type == 'Motor' ? Icons.motorcycle : Icons.directions_car,
                color: Colors.white,
                size: 24,
              ),
            ),
            const SizedBox(width: 16),
            
            // Vehicle Info
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Text(
                        vehicle.licensePlate,
                        style: const TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 16,
                          color: Color(0xFF1F2937),
                        ),
                      ),
                      const SizedBox(width: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                        decoration: BoxDecoration(
                          color: vehicle.type == 'Motor' 
                              ? Colors.orange.shade100 
                              : Colors.blue.shade100,
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Text(
                          vehicle.type,
                          style: TextStyle(
                            fontSize: 12,
                            color: vehicle.type == 'Motor' 
                                ? Colors.orange.shade700 
                                : Colors.blue.shade700,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 4),
                  
                  if (vehicle.model != null || vehicle.color != null)
                    Text(
                      [vehicle.model, vehicle.color].where((e) => e != null).join(' • '),
                      style: TextStyle(
                        fontSize: 14,
                        color: Colors.grey.shade600,
                      ),
                    ),
                  
                  const SizedBox(height: 8),
                  
                  // Wash History
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: Colors.green.shade100,
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                              Icons.local_car_wash,
                              size: 14,
                              color: Colors.green.shade700,
                            ),
                            const SizedBox(width: 4),
                            Text(
                              '${vehicle.totalWashes}x cuci',
                              style: TextStyle(
                                fontSize: 12,
                                color: Colors.green.shade700,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),
                      
                      if (vehicle.lastWashDate != null) ...[
                        const SizedBox(width: 8),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                          decoration: BoxDecoration(
                            color: Colors.purple.shade100,
                            borderRadius: BorderRadius.circular(8),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(
                                Icons.schedule,
                                size: 14,
                                color: Colors.purple.shade700,
                              ),
                              const SizedBox(width: 4),
                              Text(
                                _formatDate(vehicle.lastWashDate!),
                                style: TextStyle(
                                  fontSize: 12,
                                  color: Colors.purple.shade700,
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ],
                  ),
                  
                  if (vehicle.lastWashService != null) ...[
                    const SizedBox(height: 4),
                    Text(
                      'Terakhir: ${vehicle.lastWashService}',
                      style: TextStyle(
                        fontSize: 12,
                        color: Colors.grey.shade500,
                        fontStyle: FontStyle.italic,
                      ),
                    ),
                  ],
                ],
              ),
            ),
            
            // Arrow Icon
            Icon(
              Icons.arrow_forward_ios,
              size: 16,
              color: Colors.grey.shade400,
            ),
          ],
        ),
      ),
    );
  }

  void _selectVehicle(VehicleData vehicle) {
    widget.controller.text = vehicle.licensePlate;
    widget.onVehicleSelected(vehicle);
    _removeOverlay();
    _focusNode.unfocus();
    
    // Show vehicle history dialog
    _showVehicleHistoryDialog(vehicle);
  }

  void _showVehicleHistoryDialog(VehicleData vehicle) {
    showDialog(
      context: context,
      builder: (context) => VehicleHistoryDialog(vehicleId: vehicle.id),
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

  @override
  Widget build(BuildContext context) {
    return CompositedTransformTarget(
      link: _layerLink,
      child: TextFormField(
        controller: widget.controller,
        focusNode: _focusNode,
        decoration: InputDecoration(
          labelText: 'Nomor Plat *',
          hintText: 'B 1234 ABC',
          border: const OutlineInputBorder(),
          prefixIcon: const Icon(Icons.confirmation_number),
          suffixIcon: _isLoading
              ? const Padding(
                  padding: EdgeInsets.all(12),
                  child: SizedBox(
                    width: 20,
                    height: 20,
                    child: CircularProgressIndicator(strokeWidth: 2),
                  ),
                )
              : _suggestions.isNotEmpty
                  ? const Icon(Icons.expand_more)
                  : null,
        ),
        textCapitalization: TextCapitalization.characters,
        validator: (value) {
          if (value == null || value.trim().isEmpty) {
            return 'Nomor plat harus diisi';
          }
          return null;
        },
      ),
    );
  }
}

class VehicleHistoryDialog extends StatefulWidget {
  final int vehicleId;

  const VehicleHistoryDialog({super.key, required this.vehicleId});

  @override
  State<VehicleHistoryDialog> createState() => _VehicleHistoryDialogState();
}

class _VehicleHistoryDialogState extends State<VehicleHistoryDialog> {
  VehicleHistory? _history;
  bool _isLoading = true;
  final ApiService _apiService = ApiService();

  @override
  void initState() {
    super.initState();
    _loadVehicleHistory();
  }

  Future<void> _loadVehicleHistory() async {
    try {
      final response = await _apiService.getVehicleHistory(widget.vehicleId);
      
      if (response['success']) {
        setState(() {
          _history = VehicleHistory.fromJson(response['data']);
          _isLoading = false;
        });
      } else {
        setState(() {
          _isLoading = false;
        });
      }
    } catch (e) {
      setState(() {
        _isLoading = false;
      });
      print('Error loading vehicle history: $e');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Dialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: Container(
        constraints: const BoxConstraints(maxHeight: 600, maxWidth: 400),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Header
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  colors: [Color(0xFF667EEA), Color(0xFF764BA2)],
                ),
                borderRadius: const BorderRadius.only(
                  topLeft: Radius.circular(16),
                  topRight: Radius.circular(16),
                ),
              ),
              child: Row(
                children: [
                  const Icon(Icons.history, color: Colors.white),
                  const SizedBox(width: 12),
                  const Expanded(
                    child: Text(
                      'Riwayat Kendaraan',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                  IconButton(
                    onPressed: () => Navigator.of(context).pop(),
                    icon: const Icon(Icons.close, color: Colors.white),
                  ),
                ],
              ),
            ),
            
            // Content
            Expanded(
              child: _isLoading
                  ? const Center(child: CircularProgressIndicator())
                  : _history == null
                      ? const Center(child: Text('Gagal memuat data'))
                      : SingleChildScrollView(
                          padding: const EdgeInsets.all(20),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              // Vehicle Info
                              _buildVehicleInfo(),
                              const SizedBox(height: 20),
                              
                              // Statistics
                              _buildStatistics(),
                              const SizedBox(height: 20),
                              
                              // Recent Orders
                              _buildRecentOrders(),
                            ],
                          ),
                        ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildVehicleInfo() {
    final vehicle = _history!.vehicle;
    
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.grey.shade50,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Row(
        children: [
          Container(
            width: 60,
            height: 60,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: [
                  vehicle.type == 'Motor' ? Colors.orange.shade400 : Colors.blue.shade400,
                  vehicle.type == 'Motor' ? Colors.orange.shade600 : Colors.blue.shade600,
                ],
              ),
              borderRadius: BorderRadius.circular(12),
            ),
            child: Icon(
              vehicle.type == 'Motor' ? Icons.motorcycle : Icons.directions_car,
              color: Colors.white,
              size: 30,
            ),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  vehicle.licensePlate,
                  style: const TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: Color(0xFF1F2937),
                  ),
                ),
                Text(
                  vehicle.type,
                  style: TextStyle(
                    fontSize: 14,
                    color: Colors.grey.shade600,
                  ),
                ),
                if (vehicle.model != null || vehicle.color != null)
                  Text(
                    [vehicle.model, vehicle.color].where((e) => e != null).join(' • '),
                    style: TextStyle(
                      fontSize: 14,
                      color: Colors.grey.shade600,
                    ),
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatistics() {
    final stats = _history!.statistics;
    
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Statistik',
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: Color(0xFF1F2937),
          ),
        ),
        const SizedBox(height: 12),
        
        Row(
          children: [
            Expanded(
              child: _buildStatCard(
                'Total Cuci',
                '${stats['total_washes']}x',
                Icons.local_car_wash,
                Colors.blue,
              ),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: _buildStatCard(
                'Total Biaya',
                'Rp ${_formatCurrency(stats['total_spent'])}',
                Icons.payments,
                Colors.green,
              ),
            ),
          ],
        ),
        
        const SizedBox(height: 12),
        
        if (stats['last_wash_date'] != null)
          _buildInfoRow('Cuci Terakhir', _formatDate(stats['last_wash_date'])),
        if (stats['last_wash_service'] != null)
          _buildInfoRow('Layanan Terakhir', stats['last_wash_service']),
        _buildInfoRow('Pelanggan Sejak', _formatDate(stats['customer_since'])),
      ],
    );
  }

  Widget _buildStatCard(String title, String value, IconData icon, Color color) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: color.withOpacity(0.1),
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: color.withOpacity(0.3)),
      ),
      child: Column(
        children: [
          Icon(icon, color: color, size: 24),
          const SizedBox(height: 8),
          Text(
            value,
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: color,
            ),
          ),
          Text(
            title,
            style: TextStyle(
              fontSize: 12,
              color: Colors.grey.shade600,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            label,
            style: TextStyle(
              fontSize: 14,
              color: Colors.grey.shade600,
            ),
          ),
          Text(
            value,
            style: const TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w600,
              color: Color(0xFF1F2937),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildRecentOrders() {
    final orders = _history!.recentOrders;
    
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const Text(
          'Riwayat Cuci Terbaru',
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: Color(0xFF1F2937),
          ),
        ),
        const SizedBox(height: 12),
        
        if (orders.isEmpty)
          const Center(
            child: Text(
              'Belum ada riwayat cuci',
              style: TextStyle(color: Colors.grey),
            ),
          )
        else
          ...orders.take(5).map((order) => _buildOrderItem(order)),
      ],
    );
  }

  Widget _buildOrderItem(Map<String, dynamic> order) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.grey.shade200),
      ),
      child: Row(
        children: [
          Container(
            width: 40,
            height: 40,
            decoration: BoxDecoration(
              color: _getStatusColor(order['status']).withOpacity(0.1),
              borderRadius: BorderRadius.circular(8),
            ),
            child: Icon(
              Icons.local_car_wash,
              color: _getStatusColor(order['status']),
              size: 20,
            ),
          ),
          const SizedBox(width: 12),
          
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  order['service_name'],
                  style: const TextStyle(
                    fontWeight: FontWeight.w600,
                    fontSize: 14,
                  ),
                ),
                Text(
                  _formatDate(order['created_at']),
                  style: TextStyle(
                    fontSize: 12,
                    color: Colors.grey.shade600,
                  ),
                ),
              ],
            ),
          ),
          
          Column(
            crossAxisAlignment: CrossAxisAlignment.end,
            children: [
              Text(
                'Rp ${_formatCurrency(order['total_price'])}',
                style: const TextStyle(
                  fontWeight: FontWeight.bold,
                  fontSize: 14,
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                decoration: BoxDecoration(
                  color: _getStatusColor(order['status']).withOpacity(0.1),
                  borderRadius: BorderRadius.circular(12),
                ),
                child: Text(
                  _getStatusText(order['status']),
                  style: TextStyle(
                    fontSize: 10,
                    color: _getStatusColor(order['status']),
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }

  Color _getStatusColor(String status) {
    switch (status) {
      case 'completed':
        return Colors.green;
      case 'in_progress':
        return Colors.blue;
      case 'cancelled':
        return Colors.red;
      default:
        return Colors.orange;
    }
  }

  String _getStatusText(String status) {
    switch (status) {
      case 'completed':
        return 'Selesai';
      case 'in_progress':
        return 'Proses';
      case 'cancelled':
        return 'Batal';
      default:
        return 'Pending';
    }
  }

  String _formatCurrency(dynamic amount) {
    if (amount == null) return '0';
    return amount.toString().replaceAllMapped(
      RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
      (Match m) => '${m[1]}.',
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
      } else {
        return '${date.day}/${date.month}/${date.year}';
      }
    } catch (e) {
      return dateStr;
    }
  }
}