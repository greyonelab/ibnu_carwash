import 'package:json_annotation/json_annotation.dart';

part 'staff.g.dart';

@JsonSerializable()
class Staff {
  final int id;
  final String name;
  final String position;
  final String? phone;
  @JsonKey(name: 'commission_rate', fromJson: _commissionFromJson)
  final double? commissionRate;
  @JsonKey(name: 'is_active')
  final bool isActive;
  @JsonKey(name: 'created_at')
  final String createdAt;
  @JsonKey(name: 'updated_at')
  final String updatedAt;

  Staff({
    required this.id,
    required this.name,
    required this.position,
    this.phone,
    this.commissionRate,
    required this.isActive,
    required this.createdAt,
    required this.updatedAt,
  });

  static double? _commissionFromJson(dynamic commission) {
    if (commission == null) return null;
    if (commission is double) return commission;
    if (commission is int) return commission.toDouble();
    if (commission is String) return double.tryParse(commission);
    return null;
  }

  factory Staff.fromJson(Map<String, dynamic> json) => _$StaffFromJson(json);
  Map<String, dynamic> toJson() => _$StaffToJson(this);
}