import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/order_provider.dart';
import '../models/wash_order.dart';
import '../services/api_service.dart';

class OrdersScreen extends StatefulWidget {
  const OrdersScreen({super.key});

  @override
  State<OrdersScreen> createState() => _OrdersScreenState();
}

class _OrdersScreenState extends State<OrdersScreen> {
  String? _selectedStatus;
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _loadOrders();
  }

  void _loadOrders() {
    final orderProvider = Provider.of<OrderProvider>(context, listen: false);
    orderProvider.loadOrders(
      status: _selectedStatus,
      search: _searchController.text.isNotEmpty ? _searchController.text : null,
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          // Filters
          Container(
            padding: const EdgeInsets.all(16),
            child: Column(
              children: [
                // Search
                TextField(
                  controller: _searchController,
                  decoration: InputDecoration(
                    hintText: 'Search by order number or license plate...',
                    prefixIcon: const Icon(Icons.search),
                    border: OutlineInputBorder(
                      borderRadius: BorderRadius.circular(12),
                    ),
                    suffixIcon: IconButton(
                      icon: const Icon(Icons.clear),
                      onPressed: () {
                        _searchController.clear();
                        _loadOrders();
                      },
                    ),
                  ),
                  onSubmitted: (_) => _loadOrders(),
                ),
                const SizedBox(height: 12),
                
                // Status Filter
                SingleChildScrollView(
                  scrollDirection: Axis.horizontal,
                  child: Row(
                    children: [
                      _FilterChip(
                        label: 'All',
                        isSelected: _selectedStatus == null,
                        onSelected: () {
                          setState(() {
                            _selectedStatus = null;
                          });
                          _loadOrders();
                        },
                      ),
                      const SizedBox(width: 8),
                      _FilterChip(
                        label: 'Pending',
                        isSelected: _selectedStatus == 'pending',
                        onSelected: () {
                          setState(() {
                            _selectedStatus = 'pending';
                          });
                          _loadOrders();
                        },
                      ),
                      const SizedBox(width: 8),
                      _FilterChip(
                        label: 'In Progress',
                        isSelected: _selectedStatus == 'in_progress',
                        onSelected: () {
                          setState(() {
                            _selectedStatus = 'in_progress';
                          });
                          _loadOrders();
                        },
                      ),
                      const SizedBox(width: 8),
                      _FilterChip(
                        label: 'Completed',
                        isSelected: _selectedStatus == 'completed',
                        onSelected: () {
                          setState(() {
                            _selectedStatus = 'completed';
                          });
                          _loadOrders();
                        },
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
          
          // Orders List
          Expanded(
            child: Consumer<OrderProvider>(
              builder: (context, orderProvider, child) {
                if (orderProvider.isLoading) {
                  return const Center(child: CircularProgressIndicator());
                }

                if (orderProvider.errorMessage != null) {
                  return Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.error, size: 48, color: Colors.red.shade400),
                        const SizedBox(height: 16),
                        Text(
                          orderProvider.errorMessage!,
                          textAlign: TextAlign.center,
                          style: TextStyle(color: Colors.red.shade600),
                        ),
                        const SizedBox(height: 16),
                        ElevatedButton(
                          onPressed: _loadOrders,
                          child: const Text('Retry'),
                        ),
                      ],
                    ),
                  );
                }

                final orders = orderProvider.orders;

                if (orders.isEmpty) {
                  return const Center(
                    child: Column(
                      mainAxisAlignment: MainAxisAlignment.center,
                      children: [
                        Icon(Icons.inbox, size: 48, color: Colors.grey),
                        SizedBox(height: 16),
                        Text('No orders found'),
                      ],
                    ),
                  );
                }

                return RefreshIndicator(
                  onRefresh: () async => _loadOrders(),
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: orders.length,
                    itemBuilder: (context, index) {
                      final order = orders[index];
                      return _OrderCard(
                        order: order,
                        onTap: () => _showOrderDetails(order),
                      );
                    },
                  ),
                );
              },
            ),
          ),
        ],
      ),
    );
  }

  void _showOrderDetails(WashOrder order) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(20)),
      ),
      builder: (context) => _OrderDetailsSheet(order: order),
    );
  }
}

class _FilterChip extends StatelessWidget {
  final String label;
  final bool isSelected;
  final VoidCallback onSelected;

  const _FilterChip({
    required this.label,
    required this.isSelected,
    required this.onSelected,
  });

  @override
  Widget build(BuildContext context) {
    return FilterChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (_) => onSelected(),
      selectedColor: Colors.blue.shade100,
      checkmarkColor: Colors.blue.shade600,
    );
  }
}

class _OrderCard extends StatelessWidget {
  final WashOrder order;
  final VoidCallback onTap;

  const _OrderCard({
    required this.order,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    Color statusColor;
    String statusText;
    IconData statusIcon;
    
    switch (order.status) {
      case 'pending':
        statusColor = Colors.orange;
        statusText = 'Pending';
        statusIcon = Icons.pending;
        break;
      case 'in_progress':
        statusColor = Colors.blue;
        statusText = 'In Progress';
        statusIcon = Icons.hourglass_empty;
        break;
      case 'completed':
        statusColor = Colors.green;
        statusText = 'Completed';
        statusIcon = Icons.check_circle;
        break;
      case 'cancelled':
        statusColor = Colors.red;
        statusText = 'Cancelled';
        statusIcon = Icons.cancel;
        break;
      default:
        statusColor = Colors.grey;
        statusText = 'Unknown';
        statusIcon = Icons.help;
    }

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(8),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Expanded(
                    child: Text(
                      order.orderNumber,
                      style: const TextStyle(
                        fontWeight: FontWeight.bold,
                        fontSize: 16,
                      ),
                    ),
                  ),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: statusColor.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(statusIcon, size: 14, color: statusColor),
                        const SizedBox(width: 4),
                        Text(
                          statusText,
                          style: TextStyle(
                            color: statusColor,
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  Icon(Icons.directions_car, size: 16, color: Colors.grey.shade600),
                  const SizedBox(width: 4),
                  Text(
                    order.vehicle?.licensePlate ?? 'Unknown Vehicle',
                    style: TextStyle(color: Colors.grey.shade600),
                  ),
                  const Spacer(),
                  Icon(Icons.cleaning_services, size: 16, color: Colors.grey.shade600),
                  const SizedBox(width: 4),
                  Text(
                    order.service?.name ?? 'Unknown Service',
                    style: TextStyle(color: Colors.grey.shade600),
                  ),
                ],
              ),
              const SizedBox(height: 8),
              Row(
                children: [
                  Icon(Icons.group, size: 16, color: Colors.grey.shade600),
                  const SizedBox(width: 4),
                  Expanded(
                    child: Text(
                      _getStaffDisplay(order),
                      style: TextStyle(color: Colors.grey.shade600),
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                  const SizedBox(width: 8),
                  Text(
                    'Rp ${_formatCurrency(order.totalPrice)}',
                    style: const TextStyle(
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                      color: Colors.green,
                    ),
                  ),
                ],
              ),
              if (order.paymentStatus == 'paid') ...[
                const SizedBox(height: 4),
                Row(
                  children: [
                    Icon(Icons.payment, size: 14, color: Colors.green.shade600),
                    const SizedBox(width: 4),
                    Text(
                      'Paid (${order.paymentMethod?.toUpperCase() ?? 'Unknown'})',
                      style: TextStyle(
                        color: Colors.green.shade600,
                        fontSize: 12,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  String _formatCurrency(int amount) {
    return amount.toString().replaceAllMapped(
      RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
      (Match m) => '${m[1]},',
    );
  }

  String _getStaffDisplay(WashOrder order) {
    // Check if order has multiple staff (from API response)
    if (order.allStaff != null && order.allStaff!.isNotEmpty) {
      if (order.allStaff!.length == 1) {
        return order.allStaff!.first.name;
      } else {
        return '${order.allStaff!.length} staff: ${order.allStaff!.map((s) => s.name).join(', ')}';
      }
    }
    
    // Fallback to single staff
    if (order.staff != null) {
      return order.staff!.name;
    }
    
    return 'Unknown Staff';
  }
}

class _OrderDetailsSheet extends StatefulWidget {
  final WashOrder order;

  const _OrderDetailsSheet({required this.order});

  @override
  State<_OrderDetailsSheet> createState() => _OrderDetailsSheetState();
}

class _OrderDetailsSheetState extends State<_OrderDetailsSheet> {
  bool _isUpdating = false;

  // Status flow: pending → in_progress → completed
  String? _getNextStatus(String currentStatus) {
    switch (currentStatus) {
      case 'pending':
        return 'in_progress';
      case 'in_progress':
        return 'completed';
      default:
        return null;
    }
  }

  String _getNextStatusLabel(String currentStatus) {
    switch (currentStatus) {
      case 'pending':
        return 'Mulai Proses';
      case 'in_progress':
        return 'Selesai';
      default:
        return '';
    }
  }

  IconData _getNextStatusIcon(String currentStatus) {
    switch (currentStatus) {
      case 'pending':
        return Icons.play_arrow_rounded;
      case 'in_progress':
        return Icons.check_circle_rounded;
      default:
        return Icons.check;
    }
  }

  Color _getNextStatusColor(String currentStatus) {
    switch (currentStatus) {
      case 'pending':
        return const Color(0xFF3B82F6);
      case 'in_progress':
        return const Color(0xFF10B981);
      default:
        return Colors.grey;
    }
  }

  Future<void> _updateStatus(BuildContext context) async {
    final nextStatus = _getNextStatus(widget.order.status);
    if (nextStatus == null) return;

    final confirmed = await showDialog<bool>(
      context: context,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: const Text('Konfirmasi'),
        content: Text(
          nextStatus == 'completed'
              ? 'Tandai order ${widget.order.orderNumber} sebagai selesai?'
              : 'Mulai proses order ${widget.order.orderNumber}?',
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(ctx, false),
            child: const Text('Batal'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.pop(ctx, true),
            style: ElevatedButton.styleFrom(
              backgroundColor: _getNextStatusColor(widget.order.status),
              foregroundColor: Colors.white,
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
            ),
            child: Text(_getNextStatusLabel(widget.order.status)),
          ),
        ],
      ),
    );

    if (confirmed != true || !mounted) return;

    setState(() => _isUpdating = true);

    final orderProvider = Provider.of<OrderProvider>(context, listen: false);
    final success = await orderProvider.updateOrderStatus(widget.order.id, nextStatus);

    if (!mounted) return;
    setState(() => _isUpdating = false);

    if (success) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(
            nextStatus == 'completed'
                ? 'Order berhasil diselesaikan'
                : 'Order mulai diproses',
          ),
          backgroundColor: _getNextStatusColor(widget.order.status),
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        ),
      );
      Navigator.pop(context);
    } else {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(orderProvider.errorMessage ?? 'Gagal update status'),
          backgroundColor: Colors.red,
          behavior: SnackBarBehavior.floating,
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final nextStatus = _getNextStatus(widget.order.status);

    return DraggableScrollableSheet(
      initialChildSize: 0.7,
      maxChildSize: 0.9,
      minChildSize: 0.5,
      expand: false,
      builder: (context, scrollController) {
        return Container(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Handle
              Center(
                child: Container(
                  width: 40,
                  height: 4,
                  decoration: BoxDecoration(
                    color: Colors.grey.shade300,
                    borderRadius: BorderRadius.circular(2),
                  ),
                ),
              ),
              const SizedBox(height: 16),
              
              // Title
              Text(
                'Order Details',
                style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 16),
              
              // Content
              Expanded(
                child: SingleChildScrollView(
                  controller: scrollController,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _DetailSection(
                        title: 'Order Information',
                        children: [
                          _DetailRow('Order Number', widget.order.orderNumber),
                          _DetailRow('Status', widget.order.status.toUpperCase()),
                          _DetailRow('Payment Status', widget.order.paymentStatus.toUpperCase()),
                          if (widget.order.paymentMethod != null)
                            _DetailRow('Payment Method', widget.order.paymentMethod!.toUpperCase()),
                        ],
                      ),
                      const SizedBox(height: 16),
                      
                      _DetailSection(
                        title: 'Vehicle Information',
                        children: [
                          _DetailRow('License Plate', widget.order.vehicle?.licensePlate ?? 'Unknown'),
                          _DetailRow('Type', widget.order.vehicle?.type ?? 'Unknown'),
                          if (widget.order.vehicle?.model != null)
                            _DetailRow('Model', widget.order.vehicle!.model!),
                          if (widget.order.vehicle?.color != null)
                            _DetailRow('Color', widget.order.vehicle!.color!),
                        ],
                      ),
                      const SizedBox(height: 16),
                      
                      _DetailSection(
                        title: 'Service Information',
                        children: [
                          _DetailRow('Service', widget.order.service?.name ?? 'Unknown'),
                          _DetailRow('Type', widget.order.service?.type ?? 'Unknown'),
                          _DetailRow('Duration', '${widget.order.service?.durationMinutes ?? 0} minutes'),
                          _DetailRow('Staff', widget.order.staff?.name ?? 'Unknown'),
                        ],
                      ),
                      const SizedBox(height: 16),
                      
                      _DetailSection(
                        title: 'Pricing',
                        children: [
                          _DetailRow('Base Price', 'Rp ${_formatCurrency(widget.order.basePrice)}'),
                          if (widget.order.additionalFee > 0)
                            _DetailRow('Additional Fee', 'Rp ${_formatCurrency(widget.order.additionalFee)}'),
                          _DetailRow(
                            'Total Price', 
                            'Rp ${_formatCurrency(widget.order.totalPrice)}',
                            isTotal: true,
                          ),
                        ],
                      ),
                      
                      if (widget.order.notes != null) ...[
                        const SizedBox(height: 16),
                        _DetailSection(
                          title: 'Notes',
                          children: [
                            Text(widget.order.notes!),
                          ],
                        ),
                      ],
                    ],
                  ),
                ),
              ),
              
              // Actions
              const SizedBox(height: 16),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => Navigator.pop(context),
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                      ),
                      child: const Text('Tutup'),
                    ),
                  ),
                  if (nextStatus != null) ...[
                    const SizedBox(width: 12),
                    Expanded(
                      flex: 2,
                      child: ElevatedButton.icon(
                        onPressed: _isUpdating ? null : () => _updateStatus(context),
                        icon: _isUpdating
                            ? const SizedBox(
                                width: 18,
                                height: 18,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2,
                                  color: Colors.white,
                                ),
                              )
                            : Icon(_getNextStatusIcon(widget.order.status), size: 20),
                        label: Text(
                          _isUpdating ? 'Memproses...' : _getNextStatusLabel(widget.order.status),
                          style: const TextStyle(fontWeight: FontWeight.w700),
                        ),
                        style: ElevatedButton.styleFrom(
                          backgroundColor: _getNextStatusColor(widget.order.status),
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(12),
                          ),
                        ),
                      ),
                    ),
                  ],
                ],
              ),
            ],
          ),
        );
      },
    );
  }

  String _formatCurrency(int amount) {
    return amount.toString().replaceAllMapped(
      RegExp(r'(\d{1,3})(?=(\d{3})+(?!\d))'),
      (Match m) => '${m[1]},',
    );
  }
}

class _DetailSection extends StatelessWidget {
  final String title;
  final List<Widget> children;

  const _DetailSection({
    required this.title,
    required this.children,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          title,
          style: Theme.of(context).textTheme.titleMedium?.copyWith(
            fontWeight: FontWeight.bold,
          ),
        ),
        const SizedBox(height: 8),
        ...children,
      ],
    );
  }
}

class _DetailRow extends StatelessWidget {
  final String label;
  final String value;
  final bool isTotal;

  const _DetailRow(
    this.label,
    this.value, {
    this.isTotal = false,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            flex: 2,
            child: Text(
              label,
              style: TextStyle(
                color: Colors.grey.shade600,
                fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
              ),
            ),
          ),
          Expanded(
            flex: 3,
            child: Text(
              value,
              style: TextStyle(
                fontWeight: isTotal ? FontWeight.bold : FontWeight.normal,
                color: isTotal ? Colors.green.shade600 : null,
              ),
            ),
          ),
        ],
      ),
    );
  }
}