<?php
/**
 * Example 5: Error Handling and Best Practices
 *
 * This example demonstrates proper error handling techniques
 * and best practices when using the Blesta API SDK.
 */

require_once __DIR__ . '/../api/blesta_api.php';

// Configuration
$apiUrl = 'https://yourdomain.com/api/';
$apiUser = 'YOUR_API_USER';
$apiKey = 'YOUR_API_KEY';

// Initialize the API
$api = new BlestaApi($apiUrl, $apiUser, $apiKey);

// Example 1: Basic error checking
echo "=== Example 1: Basic Error Checking ===\n";
$response = $api->get('clients', 'get', ['client_id' => 99999]);

if ($errors = $response->errors()) {
    echo "Error detected!\n";
    echo "HTTP Status Code: " . $response->responseCode() . "\n";
    echo "Error details:\n";
    print_r($errors);
} else {
    echo "Success!\n";
    print_r($response->response());
}

echo "\n";

// Example 2: Handling validation errors
echo "=== Example 2: Handling Validation Errors ===\n";
$invalidData = [
    'client_id' => 1,
    'email' => 'invalid-email' // Invalid email format
];

$response = $api->put('clients', 'edit', $invalidData);

if ($errors = $response->errors()) {
    echo "Validation failed:\n";

    // Blesta API returns errors in a structured format
    if (is_object($errors)) {
        foreach ($errors as $field => $errorMessages) {
            if (is_array($errorMessages)) {
                foreach ($errorMessages as $message) {
                    echo "- {$field}: {$message}\n";
                }
            } else {
                echo "- {$field}: {$errorMessages}\n";
            }
        }
    }
}

echo "\n";

// Example 3: Using try-catch for critical operations
echo "=== Example 3: Try-Catch Pattern ===\n";
try {
    $response = $api->post('invoices', 'add', [
        'client_id' => 1,
        'date_due' => date('Y-m-d', strtotime('+30 days')),
        'currency' => 'USD',
        'lines' => []
    ]);

    if ($errors = $response->errors()) {
        throw new Exception(
            "API Error: " . print_r($errors, true),
            $response->responseCode()
        );
    }

    $invoice = $response->response();
    echo "Invoice created: #{$invoice->id}\n";

} catch (Exception $e) {
    echo "Error caught: " . $e->getMessage() . "\n";
    echo "Error code: " . $e->getCode() . "\n";

    // Log error, send notification, etc.
    error_log("Blesta API Error: " . $e->getMessage());
}

echo "\n";

// Example 4: Graceful degradation
echo "=== Example 4: Graceful Degradation ===\n";
function getClientSafely(BlestaApi $api, int $clientId): ?object {
    $response = $api->get('clients', 'get', ['client_id' => $clientId]);

    if ($response->errors()) {
        // Log error
        error_log("Failed to retrieve client {$clientId}: " .
                 print_r($response->errors(), true));
        return null;
    }

    return $response->response();
}

$client = getClientSafely($api, 1);
if ($client !== null) {
    echo "Client found: {$client->first_name} {$client->last_name}\n";
} else {
    echo "Client not found, using default values\n";
    // Use cached data or default values
}

echo "\n";

// Example 5: Batch operations with error tracking
echo "=== Example 5: Batch Operations with Error Tracking ===\n";
$clientIds = [1, 2, 999, 4, 5]; // 999 doesn't exist
$successCount = 0;
$errorCount = 0;
$errors = [];

foreach ($clientIds as $clientId) {
    $response = $api->get('clients', 'get', ['client_id' => $clientId]);

    if ($response->errors()) {
        $errorCount++;
        $errors[] = [
            'client_id' => $clientId,
            'error' => $response->errors(),
            'status_code' => $response->responseCode()
        ];
    } else {
        $successCount++;
        $client = $response->response();
        echo "✓ Processed client: {$client->first_name} {$client->last_name}\n";
    }
}

echo "\nBatch Results:\n";
echo "Successful: {$successCount}\n";
echo "Failed: {$errorCount}\n";

if (!empty($errors)) {
    echo "\nErrors encountered:\n";
    foreach ($errors as $error) {
        echo "- Client ID {$error['client_id']}: ";
        echo "HTTP {$error['status_code']}\n";
    }
}

echo "\n";

// Example 6: Handling network errors
echo "=== Example 6: Network Error Handling ===\n";
$invalidApi = new BlestaApi('https://invalid-domain-that-does-not-exist.com/api/', 'user', 'key');
$response = $invalidApi->get('clients', 'getAll');

if ($response->errors()) {
    $statusCode = $response->responseCode();

    if ($statusCode === 0) {
        echo "Network error: Could not connect to API server\n";
        echo "Check your connection and API URL\n";
    } elseif ($statusCode === 401 || $statusCode === 403) {
        echo "Authentication error: Invalid API credentials\n";
    } elseif ($statusCode >= 500) {
        echo "Server error: API server is experiencing issues\n";
    }

    echo "Raw response: " . $response->raw() . "\n";
}
