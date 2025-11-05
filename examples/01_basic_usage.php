<?php
/**
 * Example 1: Basic API Usage
 *
 * This example demonstrates the basic usage of the Blesta API SDK
 * with modern PHP 8.1+ features and authorization header authentication.
 */

require_once __DIR__ . '/../api/blesta_api.php';

// Configuration
$apiUrl = 'https://yourdomain.com/api/';
$apiUser = 'YOUR_API_USER';
$apiKey = 'YOUR_API_KEY';

// Initialize the API
$api = new BlestaApi($apiUrl, $apiUser, $apiKey);

// Example 1: Get a user by ID
echo "=== Example 1: Get User ===\n";
$response = $api->get('users', 'get', ['user_id' => 1]);

if ($response->errors()) {
    echo "Error occurred:\n";
    print_r($response->errors());
    echo "HTTP Status Code: " . $response->responseCode() . "\n";
} else {
    echo "Success! User data:\n";
    print_r($response->response());
}

echo "\n";

// Example 2: Get all clients
echo "=== Example 2: Get All Clients ===\n";
$response = $api->get('clients', 'getAll');

if ($response->errors()) {
    echo "Error occurred:\n";
    print_r($response->errors());
} else {
    $clients = $response->response();
    echo "Found " . count($clients) . " clients\n";
    foreach ($clients as $client) {
        echo "- Client ID: {$client->id}, Name: {$client->first_name} {$client->last_name}\n";
    }
}

echo "\n";

// Example 3: Get raw response
echo "=== Example 3: Raw Response ===\n";
$response = $api->get('users', 'get', ['user_id' => 1]);
echo "Raw JSON Response:\n";
echo $response->raw() . "\n";

echo "\n";

// Example 4: Check last request details
echo "=== Example 4: Last Request Details ===\n";
$lastRequest = $api->lastRequest();
echo "Last request URL: " . $lastRequest['url'] . "\n";
echo "Last request args:\n";
print_r($lastRequest['args']);
