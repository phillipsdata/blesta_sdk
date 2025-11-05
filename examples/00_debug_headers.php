<?php
/**
 * Debug Script: Verify API Headers and Configuration
 *
 * This script helps debug authentication issues by showing exactly what
 * headers and requests are being sent to the Blesta API.
 */

require_once __DIR__ . '/../api/blesta_api.php';

// Configuration - REPLACE THESE WITH YOUR ACTUAL VALUES
$apiUrl = 'https://yourdomain.com/api/';  // Your actual Blesta API URL
$apiUser = 'YOUR_API_USER';                // Your actual API username
$apiKey = 'YOUR_API_KEY';                  // Your actual API key

echo "=== Blesta API Debug Information ===\n\n";

// Check configuration
echo "1. Configuration Check:\n";
echo "   API URL: {$apiUrl}\n";
echo "   API User: {$apiUser}\n";
echo "   API Key: " . (strlen($apiKey) > 0 ? str_repeat('*', strlen($apiKey)) : '(empty)') . "\n\n";

// Verify placeholders have been replaced
$configErrors = [];
if ($apiUrl === 'https://yourdomain.com/api/') {
    $configErrors[] = "API URL is still the placeholder value";
}
if ($apiUser === 'YOUR_API_USER') {
    $configErrors[] = "API User is still the placeholder value";
}
if ($apiKey === 'YOUR_API_KEY') {
    $configErrors[] = "API Key is still the placeholder value";
}

if (!empty($configErrors)) {
    echo "⚠ CONFIGURATION ERRORS DETECTED:\n";
    foreach ($configErrors as $error) {
        echo "   ✗ {$error}\n";
    }
    echo "\nPlease update the values at the top of this script with your actual Blesta API credentials.\n";
    echo "You can find these in your Blesta admin panel under Settings > System > API Access\n\n";
    exit(1);
}

echo "✓ Configuration values appear to be set\n\n";

// Test the connection with verbose curl output
echo "2. Testing API Connection:\n";
echo "   Attempting to call: {$apiUrl}users/get.json\n\n";

// Manual curl test to show headers
$ch = curl_init();
$testUrl = $apiUrl . 'users/get.json?user_id=1';

curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

$headers = [
    'BLESTA-API-USER: ' . $apiUser,
    'BLESTA-API-KEY: ' . $apiKey
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// Enable verbose mode to see request details
curl_setopt($ch, CURLOPT_VERBOSE, true);
$verbose = fopen('php://temp', 'w+');
curl_setopt($ch, CURLOPT_STDERR, $verbose);

echo "   Sending headers:\n";
echo "   - BLESTA-API-USER: {$apiUser}\n";
echo "   - BLESTA-API-KEY: " . str_repeat('*', min(strlen($apiKey), 10)) . "\n\n";

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

rewind($verbose);
$verboseLog = stream_get_contents($verbose);

echo "3. Response Information:\n";
echo "   HTTP Status Code: {$httpCode}\n";

if ($httpCode === 200) {
    echo "   ✓ SUCCESS! Authentication working correctly.\n\n";
    echo "   Response:\n";
    $decoded = json_decode($response);
    if ($decoded) {
        print_r($decoded);
    } else {
        echo "   " . $response . "\n";
    }
} elseif ($httpCode === 403) {
    echo "   ✗ 403 FORBIDDEN - Authentication failed\n\n";
    echo "   Possible causes:\n";
    echo "   1. API User or API Key is incorrect\n";
    echo "   2. API Access is not enabled in Blesta\n";
    echo "   3. The API user doesn't have proper permissions\n";
    echo "   4. IP restrictions are blocking your request\n\n";
    echo "   Raw response:\n";
    echo "   " . $response . "\n\n";
} elseif ($httpCode === 404) {
    echo "   ✗ 404 NOT FOUND - API endpoint not found\n";
    echo "   Check that your API URL is correct: {$apiUrl}\n\n";
} elseif ($httpCode === 0) {
    echo "   ✗ CONNECTION FAILED - Could not connect to server\n";
    echo "   Check that your API URL is correct and accessible: {$apiUrl}\n";
    echo "   Error: " . curl_error($ch) . "\n\n";
} else {
    echo "   ✗ HTTP {$httpCode}\n";
    echo "   Response: {$response}\n\n";
}

curl_close($ch);

// Show verbose output if there's an error
if ($httpCode !== 200) {
    echo "4. Verbose cURL Output:\n";
    echo str_repeat('-', 70) . "\n";
    echo $verboseLog;
    echo str_repeat('-', 70) . "\n\n";
}

echo "5. Next Steps:\n";
if ($httpCode === 200) {
    echo "   Your API configuration is working! You can now run the other examples.\n";
} else {
    echo "   - Verify your credentials in Blesta admin: Settings > System > API Access\n";
    echo "   - Ensure API access is enabled for your user\n";
    echo "   - Check that there are no IP restrictions\n";
    echo "   - Verify the API URL ends with /api/\n";
}

echo "\n";
