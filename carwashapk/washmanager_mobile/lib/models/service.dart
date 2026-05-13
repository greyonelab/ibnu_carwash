import 'package:json_annotation/json_annotation.dart';

part 'service.g.dart';

@JsonSerializable()
class Service {
  final int id;
  final String name;
  final String type;
  final String description;
  @JsonKey(fromJson: _priceFromJson)
  final int price;
  @JsonKey(name: 'duration_minutes')
  final int durationMinutes;
  @JsonKey(name: 'is_active')
  final bool isActive;
  @JsonKey(name: 'created_at')
  final String createdAt;
  @JsonKey(name: 'updated_at')
  final String updatedAt;

  Service({
    required this.id,
    required this.name,
    required this.type,
    required this.description,
    required this.price,
    required this.durationMinutes,
    required this.isActive,
    required this.createdAt,
    required this.updatedAt,
  });

  static int _priceFromJson(dynamic price) {
    if (price is int) return price;
    if (price is double) return price.round();
    if (price is String) return double.parse(price).round();
    return 0;
  }

  factory Service.fromJson(Map<String, dynamic> json) => _$ServiceFromJson(json);
  Map<String, dynamic> toJson() => _$ServiceToJson(this);
}