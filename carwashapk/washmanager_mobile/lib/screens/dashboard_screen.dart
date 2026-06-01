import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'dart:async';
import '../providers/auth_provider.dart';
import '../providers/order_provider.dart';
import 'orders_screen.dart';
import 'create_order_screen.dart';
import 'login_screen.dart';

class DashboardScreen extends StatefulWidget {
  const DashboardScreen({super.key});

  @override
  State<DashboardScreen> createState() => _DashboardScreenState();
}

class _DashboardScreenState extends State<DashboardScreen> {
  int _selectedIndex = 0;
  
  @override
  void initState() {
    super.initState();
    _loadData();
    // Auto refresh setiap 30 detik
    Timer.periodic(const Duration(seconds: 30), (timer) {
      if (mounted) {
        _loadData();
      } else {
        timer.cancel();
      }
    });
  }

  void _loadData() {
    final orderProvider = Provider.of<OrderProvider>(context, listen: false);
    print('🔄 Loading dashboard data...');
    orderProvider.loadOrders();
    orderProvider.loadServices();
    orderProvider.loadStaff();
  }

  void _onItemTapped(int index) {
    setState(() {
      _selectedIndex = index;
    });
  }

  Future<void> _logout() async {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    await authProvider.logout();
    
    if (mounted) {
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(builder: (context) => const LoginScreen()),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final List<Widget> pages = [
      const DashboardHome(),
      const OrdersScreen(),
      const CreateOrderScreen(),
    ];

    return Scaffold(
      extendBodyBehindAppBar: true,
      appBar: AppBar(
        title: const Text(
          'WashManager Pro',
          style: TextStyle(
            fontWeight: FontWeight.w800,
            fontSize: 22,
            letterSpacing: -0.5,
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
                Colors.white.withValues(alpha: 0.95),
                Colors.white.withValues(alpha: 0.9),
              ],
            ),
          ),
        ),
        actions: [
          Container(
            margin: const EdgeInsets.only(right: 8),
            decoration: BoxDecoration(
              gradient: const LinearGradient(
                colors: [Color(0xFF667EEA), Color(0xFF764BA2)],
              ),
              borderRadius: BorderRadius.circular(12),
              boxShadow: [
                BoxShadow(
                  color: const Color(0xFF667EEA).withValues(alpha: 0.3),
                  blurRadius: 8,
                  offset: const Offset(0, 4),
                ),
              ],
            ),
            child: IconButton(
              icon: const Icon(Icons.refresh_rounded, color: Colors.white),
              onPressed: _loadData,
            ),
          ),
          Container(
            margin: const EdgeInsets.only(right: 16),
            decoration: BoxDecoration(
              color: Colors.white.withValues(alpha: 0.9),
              borderRadius: BorderRadius.circular(12),
              border: Border.all(
                color: Colors.grey.withValues(alpha: 0.2),
              ),
            ),
            child: PopupMenuButton<String>(
              icon: const Icon(Icons.more_vert_rounded, color: Color(0xFF667EEA)),
              onSelected: (value) {
                if (value == 'logout') {
                  _logout();
                }
              },
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
              itemBuilder: (BuildContext context) => [
                const PopupMenuItem<String>(
                  value: 'logout',
                  child: Row(
                    children: [
                      Icon(Icons.logout_rounded, color: Color(0xFFEF4444)),
                      SizedBox(width: 12),
                      Text('Logout'),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [
              Color(0xFFF8FAFC),
              Color(0xFFF1F5F9),
              Color(0xFFE2E8F0),
            ],
            stops: [0.0, 0.6, 1.0],
          ),
        ),
        child: IndexedStack(
          index: _selectedIndex,
          children: pages,
        ),
      ),
      bottomNavigationBar: Container(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.topRight,
            colors: [
              Colors.white.withValues(alpha: 0.95),
              Colors.white.withValues(alpha: 0.9),
            ],
          ),
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
          child: BottomNavigationBar(
            items: const <BottomNavigationBarItem>[
              BottomNavigationBarItem(
                icon: Icon(Icons.dashboard_rounded),
                activeIcon: Icon(Icons.dashboard),
                label: 'Dashboard',
              ),
              BottomNavigationBarItem(
                icon: Icon(Icons.receipt_long_rounded),
                activeIcon: Icon(Icons.receipt_long),
                label: 'Orders',
              ),
              BottomNavigationBarItem(
                icon: Icon(Icons.add_circle_outline_rounded),
                activeIcon: Icon(Icons.add_circle_rounded),
                label: 'Create',
              ),
            ],
            currentIndex: _selectedIndex,
            selectedItemColor: const Color(0xFF667EEA),
            unselectedItemColor: const Color(0xFF9CA3AF),
            backgroundColor: Colors.transparent,
            elevation: 0,
            type: BottomNavigationBarType.fixed,
            selectedLabelStyle: const TextStyle(
              fontWeight: FontWeight.w700,
              fontSize: 12,
            ),
            unselectedLabelStyle: const TextStyle(
              fontWeight: FontWeight.w500,
              fontSize: 12,
            ),
            onTap: _onItemTapped,
          ),
        ),
      ),
    );
  }
}

class DashboardHome extends StatelessWidget {
  const DashboardHome({super.key});

  @override
  Widget build(BuildContext context) {
    return RefreshIndicator(
      onRefresh: () async {
        final orderProvider = Provider.of<OrderProvider>(context, listen: false);
        await orderProvider.loadOrders();
      },
      child: SingleChildScrollView(
        padding: const EdgeInsets.fromLTRB(20, 120, 20, 20),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Welcome Card with Modern Glassmorphism
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(32),
              decoration: BoxDecoration(
                gradient: const LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [
                    Color(0xFF667EEA),
                    Color(0xFF764BA2),
                    Color(0xFFF093FB),
                  ],
                  stops: [0.0, 0.5, 1.0],
                ),
                borderRadius: BorderRadius.circular(28),
                boxShadow: [
                  BoxShadow(
                    color: const Color(0xFF667EEA).withValues(alpha: 0.4),
                    blurRadius: 30,
                    offset: const Offset(0, 15),
                  ),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white.withValues(alpha: 0.25),
                          borderRadius: BorderRadius.circular(20),
                          border: Border.all(
                            color: Colors.white.withValues(alpha: 0.3),
                          ),
                        ),
                        child: const Icon(
                          Icons.waving_hand_rounded,
                          color: Colors.white,
                          size: 32,
                        ),
                      ),
                      const SizedBox(width: 20),
                      Expanded(
                        child: Consumer<AuthProvider>(
                          builder: (context, authProvider, child) {
                            return Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text(
                                  'Welcome back,',
                                  style: TextStyle(
                                    fontSize: 16,
                                    color: Colors.white.withValues(alpha: 0.8),
                                    fontWeight: FontWeight.w500,
                                  ),
                                ),
                                const SizedBox(height: 4),
                                Text(
                                  authProvider.user?.name ?? 'User',
                                  style: const TextStyle(
                                    fontSize: 26,
                                    fontWeight: FontWeight.w800,
                                    color: Colors.white,
                                    letterSpacing: -0.5,
                                  ),
                                ),
                              ],
                            );
                          },
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 24),
                  Text(
                    'Manage your car wash operations with ease and efficiency',
                    style: TextStyle(
                      fontSize: 16,
                      color: Colors.white.withValues(alpha: 0.9),
                      fontWeight: FontWeight.w500,
                      height: 1.5,
                    ),
                  ),
                  const SizedBox(height: 28),
                  Row(
                    children: [
                      Expanded(
                        child: Container(
                          padding: const EdgeInsets.all(20),
                          decoration: BoxDecoration(
                            gradient: LinearGradient(
                              colors: [
                                Colors.white.withValues(alpha: 0.2),
                                Colors.white.withValues(alpha: 0.1),
                              ],
                            ),
                            borderRadius: BorderRadius.circular(20),
                            border: Border.all(
                              color: Colors.white.withValues(alpha: 0.3),
                            ),
                          ),
                          child: Column(
                            children: [
                              const Icon(Icons.calendar_today_rounded, color: Colors.white, size: 28),
                              const SizedBox(height: 12),
                              Text(
                                DateTime.now().day.toString(),
                                style: const TextStyle(
                                  fontSize: 24,
                                  fontWeight: FontWeight.w800,
                                  color: Colors.white,
                                ),
                              ),
                              Text(
                                _getMonthName(DateTime.now().month),
                                style: TextStyle(
                                  fontSize: 13,
                                  color: Colors.white.withValues(alpha: 0.8),
                                  fontWeight: FontWeight.w600,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(width: 20),
                      Expanded(
                        flex: 2,
                        child: Container(
                          height: 64,
                          decoration: BoxDecoration(
                            color: Colors.white,
                            borderRadius: BorderRadius.circular(20),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withValues(alpha: 0.15),
                                blurRadius: 15,
                                offset: const Offset(0, 8),
                              ),
                            ],
                          ),
                          child: ElevatedButton.icon(
                            onPressed: () {
                              Navigator.of(context).push(
                                MaterialPageRoute(builder: (context) => const CreateOrderScreen()),
                              );
                            },
                            icon: const Icon(Icons.add_rounded, size: 24),
                            label: const Text(
                              'New Order',
                              style: TextStyle(
                                fontWeight: FontWeight.w700,
                                fontSize: 16,
                              ),
                            ),
                            style: ElevatedButton.styleFrom(
                              backgroundColor: Colors.white,
                              foregroundColor: const Color(0xFF667EEA),
                              elevation: 0,
                              shadowColor: Colors.transparent,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(20),
                              ),
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 32),
            
            // Modern Stats Section
            const Text(
              'Today\'s Overview',
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.w800,
                color: Color(0xFF1F2937),
                letterSpacing: -0.5,
              ),
            ),
            const SizedBox(height: 16),
            
            // Stats Cards
            Consumer<OrderProvider>(
              builder: (context, orderProvider, child) {
                final orders = orderProvider.orders;
                final todayOrders = orders.where((order) {
                  final today = DateTime.now();
                  final orderDate = DateTime.parse(order.createdAt);
                  return orderDate.year == today.year &&
                         orderDate.month == today.month &&
                         orderDate.day == today.day;
                }).toList();
                
                final completedToday = todayOrders.where((order) => order.status == 'completed').length;
                final pendingOrders = orders.where((order) => order.status == 'pending').length;
                final inProgressOrders = orders.where((order) => order.status == 'in_progress').length;
                
                final todayRevenue = todayOrders
                    .where((order) => order.paymentStatus == 'paid')
                    .fold(0, (sum, order) => sum + order.totalPrice);

                return Column(
                  children: [
                    Row(
                      children: [
                        Expanded(
                          child: _ModernStatCard(
                            title: 'Pesanan',
                            value: todayOrders.length.toString(),
                            icon: Icons.receipt_long_rounded,
                            color: const Color(0xFF3B82F6),
                            subtitle: 'Hari ini',
                          ),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: _ModernStatCard(
                            title: 'Selesai',
                            value: completedToday.toString(),
                            icon: Icons.check_circle_rounded,
                            color: const Color(0xFF10B981),
                            subtitle: 'Completed',
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    Row(
                      children: [
                        Expanded(
                          child: _ModernStatCard(
                            title: 'Pending',
                            value: pendingOrders.toString(),
                            icon: Icons.schedule_rounded,
                            color: const Color(0xFFF59E0B),
                            subtitle: 'Menunggu',
                          ),
                        ),
                        const SizedBox(width: 16),
                        Expanded(
                          child: _ModernStatCard(
                            title: 'Proses',
                            value: inProgressOrders.toString(),
                            icon: Icons.hourglass_empty_rounded,
                            color: const Color(0xFF8B5CF6),
                            subtitle: 'Dikerjakan',
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 16),
                    _ModernStatCard(
                      title: 'Pendapatan Hari Ini',
                      value: 'Rp ${_formatCurrency(todayRevenue)}',
                      icon: Icons.trending_up_rounded,
                      color: const Color(0xFF059669),
                      isWide: true,
                      subtitle: 'Total revenue',
                    ),
                  ],
                );
              },
            ),
            const SizedBox(height: 32),
            
            // Quick Actions with Modern Design
            const Text(
              'Quick Actions',
              style: TextStyle(
                fontSize: 24,
                fontWeight: FontWeight.w800,
                color: Color(0xFF1F2937),
                letterSpacing: -0.5,
              ),
            ),
            const SizedBox(height: 16),
            Row(
              children: [
                Expanded(
                  child: _ModernQuickActionCard(
                    title: 'New Order',
                    subtitle: 'Create wash order',
                    icon: Icons.add_circle_rounded,
                    gradient: const LinearGradient(
                      colors: [Color(0xFF667EEA), Color(0xFF764BA2)],
                    ),
                    onTap: () {
                      Navigator.of(context).push(
                        MaterialPageRoute(builder: (context) => const CreateOrderScreen()),
                      );
                    },
                  ),
                ),
                const SizedBox(width: 16),
                Expanded(
                  child: _ModernQuickActionCard(
                    title: 'View Orders',
                    subtitle: 'Manage orders',
                    icon: Icons.list_alt_rounded,
                    gradient: const LinearGradient(
                      colors: [Color(0xFF10B981), Color(0xFF059669)],
                    ),
                    onTap: () {
                      Navigator.of(context).push(
                        MaterialPageRoute(builder: (context) => const OrdersScreen()),
                      );
                    },
                  ),
                ),
              ],
            ),
            const SizedBox(height: 32),
            
            // Recent Orders
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                const Text(
                  'Recent Orders',
                  style: TextStyle(
                    fontSize: 24,
                    fontWeight: FontWeight.w800,
                    color: Color(0xFF1F2937),
                    letterSpacing: -0.5,
                  ),
                ),
                TextButton.icon(
                  onPressed: () {
                    Navigator.of(context).push(
                      MaterialPageRoute(builder: (context) => const OrdersScreen()),
                    );
                  },
                  icon: const Icon(Icons.arrow_forward_rounded, size: 18),
                  label: const Text(
                    'View All',
                    style: TextStyle(fontWeight: FontWeight.w600),
                  ),
                  style: TextButton.styleFrom(
                    foregroundColor: const Color(0xFF667EEA),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 16),
            Consumer<OrderProvider>(
              builder: (context, orderProvider, child) {
                if (orderProvider.isLoading) {
                  return const Center(
                    child: Padding(
                      padding: EdgeInsets.all(32.0),
                      child: CircularProgressIndicator(),
                    ),
                  );
                }
                
                final recentOrders = orderProvider.orders.take(5).toList();
                
                if (recentOrders.isEmpty) {
                  return Container(
                    padding: const EdgeInsets.all(40.0),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(20),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: 0.05),
                          blurRadius: 10,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: const Center(
                      child: Column(
                        children: [
                          Icon(Icons.inbox_rounded, size: 48, color: Color(0xFF9CA3AF)),
                          SizedBox(height: 16),
                          Text(
                            'No orders yet',
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.w600,
                              color: Color(0xFF6B7280),
                            ),
                          ),
                          SizedBox(height: 8),
                          Text(
                            'Create your first order',
                            style: TextStyle(
                              fontSize: 14,
                              color: Color(0xFF9CA3AF),
                            ),
                          ),
                        ],
                      ),
                    ),
                  );
                }
                
                return Column(
                  children: recentOrders.map((order) => _ModernOrderCard(order: order)).toList(),
                );
              },
            ),
          ],
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

  String _getMonthName(int month) {
    const months = [
      'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
      'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'
    ];
    return months[month - 1];
  }
}

class _ModernStatCard extends StatelessWidget {
  final String title;
  final String value;
  final IconData icon;
  final Color color;
  final bool isWide;
  final String? subtitle;

  const _ModernStatCard({
    required this.title,
    required this.value,
    required this.icon,
    required this.color,
    this.isWide = false,
    this.subtitle,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(24),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Colors.white,
            Colors.white.withValues(alpha: 0.8),
          ],
        ),
        borderRadius: BorderRadius.circular(24),
        border: Border.all(
          color: Colors.grey.withValues(alpha: 0.1),
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.08),
            blurRadius: 20,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [
                      color.withValues(alpha: 0.15),
                      color.withValues(alpha: 0.05),
                    ],
                  ),
                  borderRadius: BorderRadius.circular(16),
                  border: Border.all(
                    color: color.withValues(alpha: 0.2),
                  ),
                ),
                child: Icon(icon, color: color, size: 28),
              ),
              const Spacer(),
              if (!isWide)
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: Colors.green.withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(12),
                  ),
                  child: Icon(
                    Icons.trending_up_rounded, 
                    color: Colors.green.shade600, 
                    size: 20,
                  ),
                ),
            ],
          ),
          const SizedBox(height: 20),
          Text(
            value,
            style: TextStyle(
              fontSize: isWide ? 28 : 24,
              fontWeight: FontWeight.w800,
              color: color,
              letterSpacing: -0.5,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            title,
            style: const TextStyle(
              fontSize: 15,
              color: Color(0xFF6B7280),
              fontWeight: FontWeight.w600,
            ),
          ),
          if (subtitle != null) ...[
            const SizedBox(height: 4),
            Text(
              subtitle!,
              style: const TextStyle(
                fontSize: 13,
                color: Color(0xFF9CA3AF),
                fontWeight: FontWeight.w500,
              ),
            ),
          ],
        ],
      ),
    );
  }
}

class _ModernQuickActionCard extends StatelessWidget {
  final String title;
  final String? subtitle;
  final IconData icon;
  final Gradient gradient;
  final VoidCallback onTap;

  const _ModernQuickActionCard({
    required this.title,
    this.subtitle,
    required this.icon,
    required this.gradient,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        gradient: gradient,
        borderRadius: BorderRadius.circular(24),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.15),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: onTap,
          borderRadius: BorderRadius.circular(24),
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              children: [
                Container(
                  padding: const EdgeInsets.all(18),
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.25),
                    borderRadius: BorderRadius.circular(20),
                    border: Border.all(
                      color: Colors.white.withValues(alpha: 0.3),
                    ),
                  ),
                  child: Icon(icon, color: Colors.white, size: 36),
                ),
                const SizedBox(height: 20),
                Text(
                  title,
                  style: const TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w800,
                    color: Colors.white,
                    letterSpacing: -0.3,
                  ),
                  textAlign: TextAlign.center,
                ),
                if (subtitle != null) ...[
                  const SizedBox(height: 6),
                  Text(
                    subtitle!,
                    style: TextStyle(
                      fontSize: 13,
                      color: Colors.white.withValues(alpha: 0.8),
                      fontWeight: FontWeight.w500,
                    ),
                    textAlign: TextAlign.center,
                  ),
                ],
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _ModernOrderCard extends StatefulWidget {
  final dynamic order;

  const _ModernOrderCard({required this.order});

  @override
  State<_ModernOrderCard> createState() => _ModernOrderCardState();
}

class _ModernOrderCardState extends State<_ModernOrderCard> {
  bool _isUpdating = false;

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
        return 'Mulai';
      case 'in_progress':
        return 'Selesai';
      default:
        return '';
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

    setState(() => _isUpdating = true);

    final orderProvider = Provider.of<OrderProvider>(context, listen: false);
    final success = await orderProvider.updateOrderStatus(widget.order.id, nextStatus);

    if (!mounted) return;
    setState(() => _isUpdating = false);

    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(
          success
              ? (nextStatus == 'completed'
                  ? 'Order ${widget.order.orderNumber} selesai'
                  : 'Order ${widget.order.orderNumber} mulai diproses')
              : (orderProvider.errorMessage ?? 'Gagal update status'),
        ),
        backgroundColor: success ? _getNextStatusColor(widget.order.status) : Colors.red,
        behavior: SnackBarBehavior.floating,
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    Color statusColor;
    String statusText;

    switch (widget.order.status) {
      case 'pending':
        statusColor = const Color(0xFFF59E0B);
        statusText = 'Pending';
        break;
      case 'in_progress':
        statusColor = const Color(0xFF3B82F6);
        statusText = 'In Progress';
        break;
      case 'completed':
        statusColor = const Color(0xFF10B981);
        statusText = 'Completed';
        break;
      case 'cancelled':
        statusColor = const Color(0xFFEF4444);
        statusText = 'Cancelled';
        break;
      default:
        statusColor = const Color(0xFF6B7280);
        statusText = 'Unknown';
    }

    final nextStatus = _getNextStatus(widget.order.status);

    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(16),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                padding: const EdgeInsets.all(8),
                decoration: BoxDecoration(
                  color: statusColor.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(8),
                ),
                child: Icon(Icons.local_car_wash_rounded, color: statusColor, size: 20),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      widget.order.orderNumber,
                      style: const TextStyle(
                        fontWeight: FontWeight.w700,
                        fontSize: 16,
                        color: Color(0xFF1F2937),
                      ),
                    ),
                    Text(
                      widget.order.vehicle?.licensePlate ?? 'Unknown Vehicle',
                      style: const TextStyle(
                        color: Color(0xFF6B7280),
                        fontSize: 14,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                decoration: BoxDecoration(
                  color: statusColor.withValues(alpha: 0.1),
                  borderRadius: BorderRadius.circular(20),
                ),
                child: Text(
                  statusText,
                  style: TextStyle(
                    color: statusColor,
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Icon(Icons.cleaning_services_rounded, size: 16, color: Colors.grey.shade500),
              const SizedBox(width: 6),
              Text(
                widget.order.service?.name ?? 'Unknown Service',
                style: TextStyle(
                  color: Colors.grey.shade600,
                  fontSize: 14,
                  fontWeight: FontWeight.w500,
                ),
              ),
              const Spacer(),
              Text(
                'Rp ${_formatCurrency(widget.order.totalPrice)}',
                style: const TextStyle(
                  fontWeight: FontWeight.w700,
                  fontSize: 16,
                  color: Color(0xFF10B981),
                ),
              ),
            ],
          ),
          if (widget.order.paymentStatus == 'paid') ...[
            const SizedBox(height: 8),
            Row(
              children: [
                Icon(Icons.check_circle_rounded, size: 14, color: Colors.green.shade600),
                const SizedBox(width: 6),
                Text(
                  'Paid (${widget.order.paymentMethod?.toUpperCase() ?? 'Unknown'})',
                  style: TextStyle(
                    color: Colors.green.shade600,
                    fontSize: 12,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ],
            ),
          ],
          // Action button for non-completed/non-cancelled orders
          if (nextStatus != null) ...[
            const SizedBox(height: 12),
            SizedBox(
              width: double.infinity,
              child: ElevatedButton.icon(
                onPressed: _isUpdating ? null : () => _updateStatus(context),
                icon: _isUpdating
                    ? const SizedBox(
                        width: 16,
                        height: 16,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          color: Colors.white,
                        ),
                      )
                    : Icon(
                        nextStatus == 'completed'
                            ? Icons.check_circle_rounded
                            : Icons.play_arrow_rounded,
                        size: 18,
                      ),
                label: Text(
                  _isUpdating
                      ? 'Memproses...'
                      : _getNextStatusLabel(widget.order.status),
                  style: const TextStyle(
                    fontWeight: FontWeight.w700,
                    fontSize: 14,
                  ),
                ),
                style: ElevatedButton.styleFrom(
                  backgroundColor: _getNextStatusColor(widget.order.status),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 10),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10),
                  ),
                  elevation: 0,
                ),
              ),
            ),
          ],
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