// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'staff.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

Staff _$StaffFromJson(Map<String, dynamic> json) => Staff(
  id: (json['id'] as num).toInt(),
  name: json['name'] as String,
  position: json['position'] as String,
  phone: json['phone'] as String?,
  commissionRate: Staff._commissionFromJson(json['commission_rate']),
  isActive: json['is_active'] as bool,
  createdAt: json['created_at'] as String,
  updatedAt: json['updated_at'] as String,
);

Map<String, dynamic> _$StaffToJson(Staff instance) => <String, dynamic>{
  'id': instance.id,
  'name': instance.name,
  'position': instance.position,
  'phone': instance.phone,
  'commission_rate': instance.commissionRate,
  'is_active': instance.isActive,
  'created_at': instance.createdAt,
  'updated_at': instance.updatedAt,
};
