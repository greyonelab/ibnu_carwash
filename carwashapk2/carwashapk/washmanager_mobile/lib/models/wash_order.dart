import 'package:json_annotation/json_annotation.dart';
import 'user.dart';
import 'service.dart';
import 'vehicle.dart';
import 'staff.dart';

part 'wash_order.g.dart';

@JsonSerializable()
class WashOrder {
  final int id;
  @JsonKey(name: 'vehicle_id')
  final int vehicleId;
  @JsonKey(name: 'service_id')
  final int serviceId;
  @JsonKey(name: 'staff_id')
  final int staffId;
  @JsonKey(name: 'user_id')
  final int userId;
  @JsonKey(name: 'order_number')
  final String orderNumber;
  @JsonKey(name: 'base_price', fromJson: _priceFromJson)
  final int basePrice;
  @JsonKey(name: 'additional_fee', fromJson: _priceFromJson)
  final int additionalFee;
  @JsonKey(name: 'total_price', fromJson: _priceFromJson)
  final int totalPrice;
  final String status;
  @JsonKey(name: 'payment_status')
  final String paymentStatus;
  @JsonKey(name: 'payment_method')
  final String? paymentMethod;
  @JsonKey(name: 'started_at')
  final String? startedAt;
  @JsonKey(name: 'completed_at')
  final String? completedAt;
  final String? notes;
  @JsonKey(name: 'created_at')
  final String createdAt;
  @JsonKey(name: 'updated_at')
  final String updatedAt;

  // Relationships
  final Vehicle? vehicle;
  final Service? service;
  final Staff? staff;
  final User? user;

  WashOrder({
    required this.id,
    required this.vehicleId,
    required this.serviceId,
    required this.staffId,
    required this.userId,
    required this.orderNumber,
    required this.basePrice,
    required this.additionalFee,
    required this.totalPrice,
    required this.status,
    required this.paymentStatus,
    this.paymentMethod,
    this.startedAt,
    this.completedAt,
    this.notes,
    required this.createdAt,
    required this.updatedAt,
    this.vehicle,
    this.service,
    this.staff,
    this.user,
  });

  static int _priceFromJson(dynamic price) {
    if (price is int) return price;
    if (price is double) return price.round();
    if (price is String) return double.parse(price).round();
    return 0;
  }

  factory WashOrder.fromJson(Map<String, dynamic> json) => _$WashOrderFromJson(json);
  Map<String, dynamic> toJson() => _$WashOrderToJson(this);
}