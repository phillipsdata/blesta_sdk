<?php
/**
 * Example 2: Client Management
 *
 * This example demonstrates how to retrieve and list client information.
 *
 * NOTE: Client creation and updating require specific field configurations
 * that vary by Blesta installation. These examples focus on reliable read
 * operations that work with any Blesta setup.
 */

require_once __DIR__ . '/../api/blesta_api.php';

// Configuration
$apiUrl = 'https://yourdomain.com/api/';
$apiUser = 'YOUR_API_USER';
$apiKey = 'YOUR_API_KEY';

// Initialize the API
$api = new BlestaApi($apiUrl, $apiUser, $apiKey);

// Example 1: Get a specific client
echo "=== Retrieving Client Details ===\n";
$clientId = 1; // Change to an existing client ID in your system

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
    echo "Company: " . ($client->company ?? '(none)') . "\n";
    echo "Status: {$client->status}\n";
    if (isset($client->date_added)) {
        echo "Date Added: {$client->date_added}\n";
    }
}

echo "\n";

// Example 2: List all clients with pagination
echo "=== Listing All Clients ===\n";
$response = $api->get('clients', 'getAll', [
    'page' => 1,
    'order_by' => ['date_added' => 'desc']
]);

if ($response->errors()) {
    echo "Failed to retrieve clients:\n";
    print_r($response->errors());
} else {
    $clients = $response->response();
    echo "Retrieved " . count($clients) . " clients\n\n";

    foreach ($clients as $client) {
        echo sprintf(
            "- [%d] %s %s (%s) - Status: %s\n",
            $client->id,
            $client->first_name,
            $client->last_name,
            $client->email,
            $client->status
        );
    }
}

echo "\n";

// Example 3: Search for clients by email
echo "=== Search Clients by Email ===\n";
$searchEmail = 'example.com'; // Domain to search for

$response = $api->get('clients', 'getAll');
if (!$response->errors()) {
    $allClients = $response->response();
    $matches = array_filter($allClients, function($client) use ($searchEmail) {
        return stripos($client->email, $searchEmail) !== false;
    });

    echo "Found " . count($matches) . " clients with '{$searchEmail}' in email:\n";
    foreach ($matches as $client) {
        echo "  - {$client->first_name} {$client->last_name} ({$client->email})\n";
    }
}

echo "\n";

// Example 3: Get client statistics
echo "=== Client Statistics ===\n";
$response = $api->get('clients', 'getAll');

if (!$response->errors()) {
    $allClients = $response->response();

    $statusCounts = [];
    foreach ($allClients as $client) {
        $status = $client->status;
        $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
    }

    echo "Client counts by status:\n";
    foreach ($statusCounts as $status => $count) {
        echo "  - {$status}: {$count}\n";
    }
}

echo "\n\n";

echo "=== TODO: Advanced Operations ===\n";
echo "Creating and updating clients requires specific field configurations\n";
echo "that depend on your Blesta settings (custom fields, required fields, etc.)\n";
echo "For these operations, consult the Blesta API documentation and test with\n";
echo "your specific setup.\n\n";
echo "Recommended workflow:\n";
echo "1. Get an existing client to see the data structure\n";
echo "2. Use that structure as a template for new clients\n";
echo "3. Test with your required/custom fields\n\n";

echo "Other useful client API methods:\n";
echo "  - clients/getNotes: Get notes for a client\n";
echo "  - services/getAll: Get all services (filter by client_id if needed)\n";
echo "  - invoices/getAll: Get invoices for a client\n";
echo "  - transactions/getAll: Get transactions for a client\n";

