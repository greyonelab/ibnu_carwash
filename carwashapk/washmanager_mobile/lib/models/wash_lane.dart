class WashLane {
  final int id;
  final String name;
  final String type;
  final bool isActive;
  final int maxQueue;
  final String? description;
  final int currentQueue;
  final DateTime createdAt;
  final DateTime updatedAt;

  WashLane({
    required this.id,
    required this.name,
    required this.type,
    required this.isActive,
    required this.maxQueue,
    this.description,
    required this.currentQueue,
    required this.createdAt,
    required this.updatedAt,
  });

  factory WashLane.fromJson(Map<String, dynamic> json) {
    return WashLane(
      id: json['id'] ?? 0,
      name: json['name'] ?? '',
      type: json['type'] ?? 'general',
      isActive: json['is_active'] ?? true,
      maxQueue: json['max_queue'] ?? 10,
      description: json['description'],
      currentQueue: json['current_queue'] ?? 0,
      createdAt: json['created_at'] != null
          ? DateTime.parse(json['created_at'])
          : DateTime.now(),
      updatedAt: json['updated_at'] != null
          ? DateTime.parse(json['updated_at'])
          : DateTime.now(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'type': type,
      'is_active': isActive,
      'max_queue': maxQueue,
      'description': description,
      'current_queue': currentQueue,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt.toIso8601String(),
    };
  }

  bool get canAcceptOrder => isActive && currentQueue < maxQueue;
  
  double get occupancyRate => maxQueue > 0 ? (currentQueue / maxQueue) * 100 : 0;
  
  String get statusText {
    if (!isActive) return 'Nonaktif';
    if (currentQueue >= maxQueue) return 'Penuh';
    if (currentQueue > 0) return 'Ada antrian';
    return 'Kosong';
  }

  @override
  String toString() {
    return 'WashLane{id: $id, name: $name, type: $type, isActive: $isActive, currentQueue: $currentQueue/$maxQueue}';
  }
}