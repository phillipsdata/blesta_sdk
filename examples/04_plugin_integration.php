<?php
/**
 * Example 4: Plugin Integration
 *
 * This example demonstrates how to retrieve data from Blesta plugins.
 * Focuses on read operations that work reliably.
 *
 * NOTE: Plugin operations require the respective plugins to be installed.
 * Create/update operations require specific configurations.
 */

require_once __DIR__ . '/../api/blesta_api.php';

// Configuration
$apiUrl = 'https://yourdomain.com/api/';
$apiUser = 'YOUR_API_USER';
$apiKey = 'YOUR_API_KEY';

// Initialize the API
$api = new BlestaApi($apiUrl, $apiUser, $apiKey);

echo "=== Support Manager Plugin Examples ===\n\n";

// Example 1: Get specific ticket details
echo "Example 1: Get Ticket Details\n";
$ticketId = 1; // Replace with actual ticket ID

$response = $api->get(
    'support_manager.support_manager_tickets',
    'get',
    ['ticket_id' => $ticketId]
);

if ($response->errors()) {
    echo "Failed to retrieve ticket:\n";
    print_r($response->errors());
    echo "Note: Support Manager plugin must be installed.\n";
} else {
    $ticket = $response->response();
    echo "Ticket #{$ticket->id}\n";
    echo "Summary: {$ticket->summary}\n";
    echo "Status: {$ticket->status}\n";
    echo "Priority: {$ticket->priority}\n";
    echo "Department ID: {$ticket->department_id}\n";
    echo "Created: {$ticket->date_added}\n";

    if (!empty($ticket->replies)) {
        echo "\nReplies (" . count($ticket->replies) . "):\n";
        foreach ($ticket->replies as $reply) {
            $replyType = isset($reply->staff_id) ? 'Staff' : 'Client';
            echo "  - [{$replyType}] " . substr($reply->details, 0, 50) . "...\n";
        }
    }
}

echo "\n";

// Example 2: Get all departments
echo "Example 2: Get Support Departments\n";
$response = $api->get(
    'support_manager.support_manager_departments',
    'getAll',
    ['company_id' => 1]
);

if ($response->errors()) {
    echo "Failed to retrieve departments:\n";
    print_r($response->errors());
} else {
    $departments = $response->response();
    if (empty($departments)) {
        echo "No departments found.\n";
    } else {
        echo "Found " . count($departments) . " department(s):\n";
        foreach ($departments as $dept) {
            echo sprintf(
                "  - [%d] %s (Email: %s)\n",
                $dept->id,
                $dept->name,
                $dept->email ?? 'N/A'
            );
        }
    }
}

echo "\n";

// Example 3: Get ticket count by status
echo "Example 3: Ticket Statistics\n";
$statuses = ['open', 'awaiting_reply', 'in_progress', 'closed'];
$ticketCounts = [];

foreach ($statuses as $status) {
    $response = $api->get(
        'support_manager.support_manager_tickets',
        'getList',
        [
            'status' => $status,
            'page' => 1
        ]
    );

    if (!$response->errors() && $response->response()) {
        $tickets = $response->response();
        $ticketCounts[$status] = is_array($tickets) ? count($tickets) : 0;
    } else {
        $ticketCounts[$status] = 0;
    }
}

echo "Ticket counts by status:\n";
foreach ($ticketCounts as $status => $count) {
    echo "  - {$status}: {$count}\n";
}

echo "\n";

// Example 4: Get ticket attachments (if any)
echo "Example 4: Get Ticket Attachments\n";
$response = $api->get(
    'support_manager.support_manager_tickets',
    'get',
    ['ticket_id' => $ticketId]
);

if (!$response->errors() && $response->response()) {
    $ticket = $response->response();
    if (!empty($ticket->attachments)) {
        echo "Ticket has " . count($ticket->attachments) . " attachment(s):\n";
        foreach ($ticket->attachments as $attachment) {
            echo sprintf(
                "  - %s (%s bytes)\n",
                $attachment->name,
                $attachment->size ?? 'unknown'
            );
        }
    } else {
        echo "No attachments found for this ticket.\n";
    }
} else {
    echo "Could not retrieve ticket attachments.\n";
}

echo "\n\n";

echo "=== TODO: Advanced Plugin Operations ===\n\n";

echo "Creating Tickets:\n";
echo "  Requires: department_id, summary, details, client_id or email\n";
echo "  May require: custom fields specific to your setup\n";
echo "  Example structure:\n";
echo "    ['department_id' => 1, 'summary' => '...', 'details' => '...', 'client_id' => 1]\n\n";

echo "Replying to Tickets:\n";
echo "  Requires: ticket_id, details, staff_id (for staff) or contact_id (for clients)\n";
echo "  May require: type ('reply' or 'note')\n";
echo "  Note: Requires appropriate permissions\n\n";

echo "Closing Tickets:\n";
echo "  Method: support_manager_tickets/close\n";
echo "  Requires: ticket_id, details (closing message)\n";
echo "  Example:\n";
echo "    \$api->post('support_manager.support_manager_tickets', 'close',\n";
echo "              ['ticket_id' => 1, 'details' => 'Issue resolved']);\n\n";

echo "Other Plugins:\n";
echo "  - Order Plugin: order.orders (requires Order plugin installed)\n";
echo "  - Download Plugin: download_manager.* (requires Download Manager)\n";
echo "  - CMS Plugin: cms.* (requires CMS plugin)\n";
echo "  Check plugin documentation for available API methods.\n";

