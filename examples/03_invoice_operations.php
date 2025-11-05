<?php
/**
 * Example 3: Invoice Operations
 *
 * This example demonstrates how to work with invoices using the Blesta API.
 * Includes creating invoices, retrieving invoice data, and managing invoice status.
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
    echo "Found " . count($invoices) . " open invoices\n";
    foreach ($invoices as $invoice) {
        echo sprintf(
            "- Invoice #%d: %s (Due: %s, Total: %s)\n",
            $invoice->id,
            $invoice->id_code,
            $invoice->date_due,
            $invoice->total
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
    echo "Date Created: {$invoice->date_created}\n";
    echo "Date Due: {$invoice->date_due}\n";
    echo "Status: {$invoice->status}\n";
    echo "Subtotal: {$invoice->subtotal}\n";
    echo "Total: {$invoice->total}\n";

    if (!empty($invoice->line_items)) {
        echo "\nLine Items:\n";
        foreach ($invoice->line_items as $item) {
            echo "  - {$item->description}: {$item->amount}\n";
        }
    }
}

echo "\n";

// Example 3: Create a new invoice
echo "=== Create New Invoice ===\n";
$newInvoiceData = [
    'client_id' => $clientId,
    'date_due' => date('Y-m-d', strtotime('+30 days')),
    'currency' => 'USD',
    'lines' => [
        [
            'description' => 'Web Hosting - Monthly',
            'qty' => 1,
            'amount' => 19.99,
            'tax' => false
        ],
        [
            'description' => 'Domain Registration',
            'qty' => 1,
            'amount' => 14.99,
            'tax' => false
        ]
    ]
];

$response = $api->post('invoices', 'add', $newInvoiceData);

if ($response->errors()) {
    echo "Failed to create invoice:\n";
    print_r($response->errors());
} else {
    $invoice = $response->response();
    echo "Invoice created successfully!\n";
    echo "Invoice ID: {$invoice->id}\n";
    echo "Invoice Number: {$invoice->id_code}\n";
    echo "Total: {$invoice->total}\n";
}

echo "\n";

// Example 4: Update invoice status
echo "=== Update Invoice Status ===\n";
$response = $api->put('invoices', 'edit', [
    'invoice_id' => $invoiceId,
    'status' => 'active'
]);

if ($response->errors()) {
    echo "Failed to update invoice:\n";
    print_r($response->errors());
} else {
    echo "Invoice status updated successfully!\n";
}

echo "\n";

// Example 5: Get invoice PDF (returns base64 encoded PDF)
echo "=== Get Invoice PDF ===\n";
$response = $api->get('invoices', 'getPdf', ['invoice_id' => $invoiceId]);

if (!$response->errors()) {
    $pdf = $response->response();
    // Save or display PDF
    echo "PDF retrieved successfully (base64 encoded)\n";
    // Example: file_put_contents("invoice_{$invoiceId}.pdf", base64_decode($pdf));
}
