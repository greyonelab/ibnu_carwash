// GENERATED CODE - DO NOT MODIFY BY HAND

part of 'vehicle.dart';

// **************************************************************************
// JsonSerializableGenerator
// **************************************************************************

Vehicle _$VehicleFromJson(Map<String, dynamic> json) => Vehicle(
  id: (json['id'] as num).toInt(),
  licensePlate: json['license_plate'] as String,
  type: json['type'] as String,
  model: json['model'] as String?,
  color: json['color'] as String?,
  createdAt: json['created_at'] as String,
  updatedAt: json['updated_at'] as String,
);

Map<String, dynamic> _$VehicleToJson(Vehicle instance) => <String, dynamic>{
  'id': instance.id,
  'license_plate': instance.licensePlate,
  'type': instance.type,
  'model': instance.model,
  'color': instance.color,
  'created_at': instance.createdAt,
  'updated_at': instance.updatedAt,
};
