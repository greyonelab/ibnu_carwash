<?php

// Simple test script to check if the application is working
echo "Testing Car Wash Application...\n\n";

// Test 1: Check if we can connect to the application
echo "1. Testing application connection...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ Application is running successfully!\n";
} else {
    echo "❌ Application connection failed (HTTP $httpCode)\n";
}

// Test 2: Check if login page is accessible
echo "\n2. Testing login page...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200 && strpos($response, 'WashManager Pro') !== false) {
    echo "✅ Login page is accessible!\n";
} else {
    echo "❌ Login page failed (HTTP $httpCode)\n";
}

// Test 3: Check API endpoint
echo "\n3. Testing API endpoint...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/services');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 401) {
    echo "✅ API endpoint is working (requires authentication)!\n";
} else {
    echo "⚠️  API endpoint response: HTTP $httpCode\n";
}

echo "\n🎉 Application test completed!\n";
echo "\nNext steps:\n";
echo "1. Open http://localhost:8000 in your browser\n";
echo "2. Login with: admin@carwash.com / password\n";
echo "3. Test the dashboard and order management\n";

?>