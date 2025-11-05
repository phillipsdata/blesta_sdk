<?php
/**
 * Example 2: Client Management
 *
 * This example demonstrates how to manage clients using the Blesta API.
 * Includes creating, updating, and retrieving client information.
 */

require_once __DIR__ . '/../api/blesta_api.php';

// Configuration
$apiUrl = 'https://yourdomain.com/api/';
$apiUser = 'YOUR_API_USER';
$apiKey = 'YOUR_API_KEY';

// Initialize the API
$api = new BlestaApi($apiUrl, $apiUser, $apiKey);

// Example 1: Create a new client
echo "=== Creating a New Client ===\n";
$newClientData = [
    'user_id' => 1,
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john.doe@example.com',
    'company' => 'Example Corporation',
    'address1' => '123 Main Street',
    'city' => 'New York',
    'state' => 'NY',
    'zip' => '10001',
    'country' => 'US'
];

$response = $api->post('clients', 'add', $newClientData);

if ($response->errors()) {
    echo "Failed to create client:\n";
    print_r($response->errors());
    exit;
}

$client = $response->response();
$clientId = $client->id;
echo "Client created successfully!\n";
echo "Client ID: {$clientId}\n";
echo "Name: {$client->first_name} {$client->last_name}\n\n";

// Example 2: Get client details
echo "=== Retrieving Client Details ===\n";
$response = $api->get('clients', 'get', ['client_id' => $clientId]);

if ($response->errors()) {
    echo "Failed to retrieve client:\n";
    print_r($response->errors());
} else {
    $client = $response->response();
    echo "Client Information:\n";
    echo "ID: {$client->id}\n";
    echo "Name: {$client->first_name} {$client->last_name}\n";
    echo "Email: {$client->email}\n";
    echo "Company: {$client->company}\n";
}

echo "\n";

// Example 3: Update client information
echo "=== Updating Client Information ===\n";
$updateData = [
    'client_id' => $clientId,
    'company' => 'Updated Corporation Name',
    'address1' => '456 Updated Avenue'
];

$response = $api->put('clients', 'edit', $updateData);

if ($response->errors()) {
    echo "Failed to update client:\n";
    print_r($response->errors());
} else {
    echo "Client updated successfully!\n";
    $updatedClient = $response->response();
    echo "New company name: {$updatedClient->company}\n";
}

echo "\n";

// Example 4: List all clients with pagination
echo "=== Listing Clients with Pagination ===\n";
$response = $api->get('clients', 'getAll', [
    'page' => 1,
    'order_by' => ['date_added' => 'desc']
]);

if (!$response->errors()) {
    $clients = $response->response();
    echo "Retrieved " . count($clients) . " clients\n";
    foreach ($clients as $client) {
        echo sprintf(
            "- [%d] %s %s (%s)\n",
            $client->id,
            $client->first_name,
            $client->last_name,
            $client->email
        );
    }
}
