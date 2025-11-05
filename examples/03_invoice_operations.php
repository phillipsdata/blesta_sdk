<?php
/**
 * Example 3: Invoice Operations
 *
 * This example demonstrates how to retrieve and work with invoices.
 *
 * NOTE: Invoice creation requires specific configurations and custom fields
 * that vary by Blesta installation. This example focuses on reliable read
 * and status operations.
 */

require_once __DIR__ . '/../api/blesta_api.php';

// Configuration
$apiUrl = 'https://yourdomain.com/api/';
$apiUser = 'YOUR_API_USER';
$apiKey = 'YOUR_API_KEY';

// Initialize the API
$api = new BlestaApi($apiUrl, $apiUser, $apiKey);

// Example 1: Get all invoices for a client
echo "=== Get Client Invoices ===\n";
$clientId = 1; // Replace with actual client ID

$response = $api->get('invoices', 'getAll', [
    'client_id' => $clientId,
    'status' => 'open'
]);

if ($response->errors()) {
    echo "Failed to retrieve invoices:\n";
    print_r($response->errors());
} else {
    $invoices = $response->response();
    echo "Found " . count($invoices) . " open invoice(s)\n";
    foreach ($invoices as $invoice) {
        echo sprintf(
            "- Invoice #%d: %s (Due: %s, Total: %s %s)\n",
            $invoice->id,
            $invoice->id_code,
            $invoice->date_due,
            $invoice->total,
            $invoice->currency
        );
    }
}

echo "\n";

// Example 2: Get specific invoice details
echo "=== Get Invoice Details ===\n";
$invoiceId = 1; // Replace with actual invoice ID

$response = $api->get('invoices', 'get', ['invoice_id' => $invoiceId]);

if ($response->errors()) {
    echo "Failed to retrieve invoice:\n";
    print_r($response->errors());
} else {
    $invoice = $response->response();
    echo "Invoice #{$invoice->id_code}\n";
    echo "Client ID: {$invoice->client_id}\n";
    echo "Date Created: " . ($invoice->date_created ?? 'N/A') . "\n";
    echo "Date Due: {$invoice->date_due}\n";
    echo "Status: {$invoice->status}\n";
    echo "Currency: {$invoice->currency}\n";
    echo "Subtotal: {$invoice->subtotal}\n";
    echo "Total: {$invoice->total}\n";

    if (!empty($invoice->line_items)) {
        echo "\nLine Items:\n";
        foreach ($invoice->line_items as $item) {
            $dateRange = '';
            if (isset($item->service_date_added) && isset($item->service_date_renews)) {
                $dateRange = " ({$item->service_date_added} - {$item->service_date_renews})";
            }
            echo "  - {$item->description}{$dateRange}: {$item->amount}\n";
        }
    }
}

echo "\n";

// Example 3: Get all invoices (with pagination)
echo "=== Get All Invoices (Paginated) ===\n";
$response = $api->get('invoices', 'getAll', [
    'page' => 1,
    'order_by' => ['date_due' => 'desc']
]);

if (!$response->errors()) {
    $invoices = $response->response();
    echo "Retrieved " . count($invoices) . " invoice(s)\n";

    $statusCounts = [];
    foreach ($invoices as $invoice) {
        $statusCounts[$invoice->status] = ($statusCounts[$invoice->status] ?? 0) + 1;
    }

    echo "\nInvoices by status:\n";
    foreach ($statusCounts as $status => $count) {
        echo "  - {$status}: {$count}\n";
    }
}

echo "\n";

// Example 4: Get recurring invoices
echo "=== Get Recurring Invoices ===\n";
$response = $api->get('invoices', 'getAllRecurring');

if ($response->errors()) {
    echo "Failed to retrieve recurring invoices:\n";
    print_r($response->errors());
} else {
    $recurringInvoices = $response->response();
    if (empty($recurringInvoices)) {
        echo "No recurring invoices found.\n";
    } else {
        echo "Found " . count($recurringInvoices) . " recurring invoice(s):\n";
        foreach ($recurringInvoices as $recurring) {
            echo sprintf(
                "  - ID: %d, Client: %d, Term: %s, Next Date: %s\n",
                $recurring->id,
                $recurring->client_id,
                $recurring->term ?? 'N/A',
                $recurring->date_renews ?? 'N/A'
            );
        }
    }
}

echo "\n";

// Example 5: Invoice status operations (that work reliably)
echo "=== Invoice Status Operations ===\n";
echo "Available status operations:\n";
echo "  - invoices/setClosed: Close an invoice\n";
echo "  - invoices/setDraft: Set invoice to draft status\n";
echo "\nExample usage:\n";
echo "\$api->post('invoices', 'setClosed', ['invoice_id' => \$invoiceId]);\n";

echo "\n\n";

echo "=== TODO: Advanced Operations ===\n";
echo "Creating invoices requires:\n";
echo "  - Understanding your currency and tax settings\n";
echo "  - Proper line item formatting for your setup\n";
echo "  - Knowledge of required custom fields\n";
echo "  - Valid client and service configurations\n\n";
echo "Recommendation: Use the Blesta admin panel to create an invoice,\n";
echo "then retrieve it via API to see the exact structure your system uses.\n";

