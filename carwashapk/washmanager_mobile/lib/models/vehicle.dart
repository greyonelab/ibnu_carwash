import 'package:json_annotation/json_annotation.dart';

part 'vehicle.g.dart';

@JsonSerializable()
class Vehicle {
  final int id;
  @JsonKey(name: 'license_plate')
  final String licensePlate;
  final String type;
  final String? model;
  final String? color;
  @JsonKey(name: 'created_at')
  final String createdAt;
  @JsonKey(name: 'updated_at')
  final String updatedAt;

  Vehicle({
    required this.id,
    required this.licensePlate,
    required this.type,
    this.model,
    this.color,
    required this.createdAt,
    required this.updatedAt,
  });

  factory Vehicle.fromJson(Map<String, dynamic> json) => _$VehicleFromJson(json);
  Map<String, dynamic> toJson() => _$VehicleToJson(this);
}