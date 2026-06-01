<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Update Service Categories ===\n\n";

// Get all services
$services = DB::table('services')->get();

echo "Total services: " . $services->count() . "\n\n";

if ($services->isEmpty()) {
    echo "No services found. You can now create new services with categories.\n";
    exit(0);
}

echo "Current services:\n";
foreach ($services as $service) {
    echo "- ID: {$service->id}, Name: {$service->name}, Type: {$service->type}, Category: " . ($service->category ?? 'NULL') . "\n";
}

echo "\n=== Updating categories ===\n\n";

// Update services without category to 'mobil' as default
$updated = DB::table('services')
    ->whereNull('category')
    ->update(['category' => 'mobil']);

echo "Updated {$updated} services to default category 'mobil'\n\n";

// Update services with 'motor' in name to category 'motor'
$motorUpdated = DB::table('services')
    ->where('name', 'like', '%motor%')
    ->orWhere('name', 'like', '%Motor%')
    ->update(['category' => 'motor']);

echo "Updated {$motorUpdated} services to category 'motor' (based on name)\n\n";

// Update services with 'truk' or 'bus' in name to category 'lainnya'
$lainnyaUpdated = DB::table('services')
    ->where(function($query) {
        $query->where('name', 'like', '%truk%')
              ->orWhere('name', 'like', '%Truk%')
              ->orWhere('name', 'like', '%bus%')
              ->orWhere('name', 'like', '%Bus%');
    })
    ->update(['category' => 'lainnya']);

echo "Updated {$lainnyaUpdated} services to category 'lainnya' (based on name)\n\n";

// Show final result
echo "=== Final Result ===\n\n";
$services = DB::table('services')->get();

foreach ($services as $service) {
    echo "- ID: {$service->id}, Name: {$service->name}, Type: {$service->type}, Category: {$service->category}\n";
}

echo "\n=== Statistics ===\n\n";
$stats = DB::table('services')
    ->select('category', DB::raw('COUNT(*) as total'))
    ->groupBy('category')
    ->get();

foreach ($stats as $stat) {
    echo "Category '{$stat->category}': {$stat->total} services\n";
}

echo "\n✅ Update completed!\n";
