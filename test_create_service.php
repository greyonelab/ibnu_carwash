<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Service;

echo "=== Test Create Service with Category ===\n\n";

// Create service for motor
$motorService = Service::create([
    'name' => 'Cuci Motor Standard',
    'description' => 'Cuci eksterior motor dengan shampo khusus',
    'price' => 15000,
    'duration_minutes' => 20,
    'type' => 'standard',
    'category' => 'motor',
    'is_active' => true,
]);

echo "✅ Created motor service:\n";
echo "   ID: {$motorService->id}\n";
echo "   Name: {$motorService->name}\n";
echo "   Type: {$motorService->type}\n";
echo "   Category: {$motorService->category}\n";
echo "   Price: Rp " . number_format($motorService->price, 0, ',', '.') . "\n\n";

// Create service for lainnya
$trukService = Service::create([
    'name' => 'Cuci Truk Standard',
    'description' => 'Cuci eksterior truk/pickup',
    'price' => 75000,
    'duration_minutes' => 60,
    'type' => 'standard',
    'category' => 'lainnya',
    'is_active' => true,
]);

echo "✅ Created truk service:\n";
echo "   ID: {$trukService->id}\n";
echo "   Name: {$trukService->name}\n";
echo "   Type: {$trukService->type}\n";
echo "   Category: {$trukService->category}\n";
echo "   Price: Rp " . number_format($trukService->price, 0, ',', '.') . "\n\n";

// Show all services grouped by category
echo "=== All Services by Category ===\n\n";

$categories = ['mobil', 'motor', 'lainnya'];

foreach ($categories as $category) {
    $services = Service::where('category', $category)->get();
    echo "Category: " . strtoupper($category) . " ({$services->count()} services)\n";
    foreach ($services as $service) {
        echo "  - {$service->name} ({$service->type}) - Rp " . number_format($service->price, 0, ',', '.') . "\n";
    }
    echo "\n";
}

echo "✅ Test completed successfully!\n";
