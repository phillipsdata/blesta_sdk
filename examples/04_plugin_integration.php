<?php
/**
 * Example 4: Plugin Integration
 *
 * This example demonstrates how to interact with Blesta plugins
 * using the API. Includes Support Manager plugin examples.
 */

require_once __DIR__ . '/../api/blesta_api.php';

// Configuration
$apiUrl = 'https://yourdomain.com/api/';
$apiUser = 'YOUR_API_USER';
$apiKey = 'YOUR_API_KEY';

// Initialize the API
$api = new BlestaApi($apiUrl, $apiUser, $apiKey);

// Example 1: Get all support tickets
echo "=== Get All Support Tickets ===\n";
$response = $api->get(
    'support_manager.support_manager_tickets',
    'getAll',
    ['status' => 'open']
);

if ($response->errors()) {
    echo "Failed to retrieve tickets:\n";
    print_r($response->errors());
} else {
    $tickets = $response->response();
    echo "Found " . count($tickets) . " open tickets\n";
    foreach ($tickets as $ticket) {
        echo sprintf(
            "- Ticket #%d: %s (Priority: %s)\n",
            $ticket->id,
            $ticket->summary,
            $ticket->priority
        );
    }
}

echo "\n";

// Example 2: Get specific ticket details
echo "=== Get Ticket Details ===\n";
$ticketId = 1; // Replace with actual ticket ID

$response = $api->get(
    'support_manager.support_manager_tickets',
    'get',
    ['ticket_id' => $ticketId]
);

if ($response->errors()) {
    echo "Failed to retrieve ticket:\n";
    print_r($response->errors());
} else {
    $ticket = $response->response();
    echo "Ticket #{$ticket->id}\n";
    echo "Summary: {$ticket->summary}\n";
    echo "Status: {$ticket->status}\n";
    echo "Priority: {$ticket->priority}\n";
    echo "Department: {$ticket->department_id}\n";
    echo "Created: {$ticket->date_added}\n";
}

echo "\n";

// Example 3: Create a new support ticket
echo "=== Create New Support Ticket ===\n";
$newTicketData = [
    'department_id' => 1,
    'client_id' => 1,
    'summary' => 'Website Performance Issues',
    'priority' => 'high',
    'message' => 'The website has been loading slowly for the past few hours. Please investigate.',
    'type' => 'issue'
];

$response = $api->post(
    'support_manager.support_manager_tickets',
    'add',
    $newTicketData
);

if ($response->errors()) {
    echo "Failed to create ticket:\n";
    print_r($response->errors());
} else {
    $ticket = $response->response();
    echo "Ticket created successfully!\n";
    echo "Ticket ID: {$ticket->id}\n";
    echo "Summary: {$ticket->summary}\n";
}

echo "\n";

// Example 4: Reply to a ticket
echo "=== Reply to Support Ticket ===\n";
$replyData = [
    'ticket_id' => $ticketId,
    'staff_id' => 1, // Or use client_id for client replies
    'message' => 'We have identified the issue and are working on a fix. Expected resolution time is 2 hours.',
    'type' => 'reply'
];

$response = $api->post(
    'support_manager.support_manager_tickets',
    'addReply',
    $replyData
);

if ($response->errors()) {
    echo "Failed to add reply:\n";
    print_r($response->errors());
} else {
    echo "Reply added successfully!\n";
}

echo "\n";

// Example 5: Close a ticket
echo "=== Close Support Ticket ===\n";
$response = $api->post(
    'support_manager.support_manager_tickets',
    'close',
    ['ticket_id' => $ticketId]
);

if ($response->errors()) {
    echo "Failed to close ticket:\n";
    print_r($response->errors());
} else {
    echo "Ticket closed successfully!\n";
}

echo "\n";

// Example 6: Working with Order plugin
echo "=== Order Plugin Example ===\n";
$response = $api->get(
    'order.orders',
    'getAll',
    ['status' => 'pending']
);

if ($response->errors()) {
    echo "Failed to retrieve orders:\n";
    print_r($response->errors());
} else {
    $orders = $response->response();
    echo "Found " . count($orders) . " pending orders\n";
}
