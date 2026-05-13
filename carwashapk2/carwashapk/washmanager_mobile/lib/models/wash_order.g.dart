// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'wash_order.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

WashOrder _$WashOrderFromJson(Map<String, dynamic> json) => WashOrder(
  id: (json['id'] as num).toInt(),
  vehicleId: (json['vehicle_id'] as num).toInt(),
  serviceId: (json['service_id'] as num).toInt(),
  staffId: (json['staff_id'] as num).toInt(),
  userId: (json['user_id'] as num).toInt(),
  orderNumber: json['order_number'] as String,
  basePrice: WashOrder._priceFromJson(json['base_price']),
  additionalFee: WashOrder._priceFromJson(json['additional_fee']),
  totalPrice: WashOrder._priceFromJson(json['total_price']),
  status: json['status'] as String,
  paymentStatus: json['payment_status'] as String,
  paymentMethod: json['payment_method'] as String?,
  startedAt: json['started_at'] as String?,
  completedAt: json['completed_at'] as String?,
  notes: json['notes'] as String?,
  createdAt: json['created_at'] as String,
  updatedAt: json['updated_at'] as String,
  vehicle: json['vehicle'] == null
      ? null
      : Vehicle.fromJson(json['vehicle'] as Map<String, dynamic>),
  service: json['service'] == null
      ? null
      : Service.fromJson(json['service'] as Map<String, dynamic>),
  staff: json['staff'] == null
      ? null
      : Staff.fromJson(json['staff'] as Map<String, dynamic>),
  user: json['user'] == null
      ? null
      : User.fromJson(json['user'] as Map<String, dynamic>),
);

Map<String, dynamic> _$WashOrderToJson(WashOrder instance) => <String, dynamic>{
  'id': instance.id,
  'vehicle_id': instance.vehicleId,
  'service_id': instance.serviceId,
  'staff_id': instance.staffId,
  'user_id': instance.userId,
  'order_number': instance.orderNumber,
  'base_price': instance.basePrice,
  'additional_fee': instance.additionalFee,
  'total_price': instance.totalPrice,
  'status': instance.status,
  'payment_status': instance.paymentStatus,
  'payment_method': instance.paymentMethod,
  'started_at': instance.startedAt,
  'completed_at': instance.completedAt,
  'notes': instance.notes,
  'created_at': instance.createdAt,
  'updated_at': instance.updatedAt,
  'vehicle': instance.vehicle,
  'service': instance.service,
  'staff': instance.staff,
  'user': instance.user,
};
