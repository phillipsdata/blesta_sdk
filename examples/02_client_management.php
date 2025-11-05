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

// Note: Creating clients requires specific data. This example shows the structure.
// You may need to adjust these values based on your Blesta configuration.
$newClientData = [
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john.doe@example.com',
    'company' => 'Example Corporation',
    'address1' => '123 Main Street',
    'city' => 'New York',
    'state' => 'NY',
    'zip' => '10001',
    'country' => 'US',
    'username' => 'johndoe' . time(), // Unique username
    'new_password' => bin2hex(random_bytes(8)), // Random password
    'confirm_password' => null, // Set to same as new_password if required
];

// Set confirm_password same as new_password
$newClientData['confirm_password'] = $newClientData['new_password'];

$response = $api->post('clients', 'add', $newClientData);

if ($response->errors()) {
    echo "Failed to create client:\n";
    print_r($response->errors());
    echo "\nNote: Client creation requires specific fields based on your Blesta settings.\n";
    echo "If this fails, try getting an existing client instead (see Example 2).\n\n";

    // Use a default client ID for remaining examples
    $clientId = 1;
} else {
    $client = $response->response();
    $clientId = $client->id;
    echo "Client created successfully!\n";
    echo "Client ID: {$clientId}\n";
    echo "Name: {$client->first_name} {$client->last_name}\n\n";
}

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

// Note: Updating requires the client's current data plus changes
// It's safer to get the client first, modify, then update
$response = $api->get('clients', 'get', ['client_id' => $clientId]);

if (!$response->errors() && $response->response()) {
    $currentClient = $response->response();

    // Prepare update with required fields
    $updateData = [
        'client_id' => $clientId,
        'first_name' => $currentClient->first_name,
        'last_name' => $currentClient->last_name,
        'email' => $currentClient->email,
        'company' => 'Updated Corporation Name', // The change we want to make
        'address1' => '456 Updated Avenue', // Another change
        'city' => $currentClient->city,
        'state' => $currentClient->state,
        'zip' => $currentClient->zip,
        'country' => $currentClient->country
    ];

    $response = $api->post('clients', 'edit', $updateData);

    if ($response->errors()) {
        echo "Failed to update client:\n";
        print_r($response->errors());
    } else {
        echo "Client updated successfully!\n";
        echo "New company name: Updated Corporation Name\n";
    }
} else {
    echo "Could not retrieve client for updating\n";
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
