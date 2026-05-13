import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../providers/order_provider.dart';
import '../models/wash_order.dart';

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
                  Icon(Icons.person, size: 16, color: Colors.grey.shade600),
                  const SizedBox(width: 4),
                  Text(
                    order.staff?.name ?? 'Unknown Staff',
                    style: TextStyle(color: Colors.grey.shade600),
                  ),
                  const Spacer(),
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
}

class _OrderDetailsSheet extends StatelessWidget {
  final WashOrder order;

  const _OrderDetailsSheet({required this.order});

  @override
  Widget build(BuildContext context) {
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
                          _DetailRow('Order Number', order.orderNumber),
                          _DetailRow('Status', order.status.toUpperCase()),
                          _DetailRow('Payment Status', order.paymentStatus.toUpperCase()),
                          if (order.paymentMethod != null)
                            _DetailRow('Payment Method', order.paymentMethod!.toUpperCase()),
                        ],
                      ),
                      const SizedBox(height: 16),
                      
                      _DetailSection(
                        title: 'Vehicle Information',
                        children: [
                          _DetailRow('License Plate', order.vehicle?.licensePlate ?? 'Unknown'),
                          _DetailRow('Type', order.vehicle?.type ?? 'Unknown'),
                          if (order.vehicle?.model != null)
                            _DetailRow('Model', order.vehicle!.model!),
                          if (order.vehicle?.color != null)
                            _DetailRow('Color', order.vehicle!.color!),
                        ],
                      ),
                      const SizedBox(height: 16),
                      
                      _DetailSection(
                        title: 'Service Information',
                        children: [
                          _DetailRow('Service', order.service?.name ?? 'Unknown'),
                          _DetailRow('Type', order.service?.type ?? 'Unknown'),
                          _DetailRow('Duration', '${order.service?.durationMinutes ?? 0} minutes'),
                          _DetailRow('Staff', order.staff?.name ?? 'Unknown'),
                        ],
                      ),
                      const SizedBox(height: 16),
                      
                      _DetailSection(
                        title: 'Pricing',
                        children: [
                          _DetailRow('Base Price', 'Rp ${_formatCurrency(order.basePrice)}'),
                          if (order.additionalFee > 0)
                            _DetailRow('Additional Fee', 'Rp ${_formatCurrency(order.additionalFee)}'),
                          _DetailRow(
                            'Total Price', 
                            'Rp ${_formatCurrency(order.totalPrice)}',
                            isTotal: true,
                          ),
                        ],
                      ),
                      
                      if (order.notes != null) ...[
                        const SizedBox(height: 16),
                        _DetailSection(
                          title: 'Notes',
                          children: [
                            Text(order.notes!),
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
                      child: const Text('Close'),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: () {
                        // TODO: Implement status update
                        Navigator.pop(context);
                      },
                      child: const Text('Update Status'),
                    ),
                  ),
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