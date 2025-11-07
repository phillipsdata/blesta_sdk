<?php
require_once "blesta_api.php";
$config = require "config.php";

// Initialize the Blesta API
$api = new BlestaApi($config);

try {
    // Example API call: Fetch user details
    $response = $api->get("users", "get", ['user_id' => 1]);

    // Parse and format the response
    $data = $response->response();
    $errors = $response->errors();

    if ($data) {
        echo "Parsed Response:\n";
        echo "ID: " . ($data->id ?? "N/A") . "\n";
        echo "Username: " . ($data->username ?? "N/A") . "\n";
        // echo "Password (hashed): " . ($data->password ?? "N/A") . "\n";
        echo "Recovery Email: " . (!empty($data->recovery_email) ? $data->recovery_email : "Not set") . "\n";
        echo "Two-Factor Mode: " . ($data->two_factor_mode ?? "N/A") . "\n";
        // echo "Two-Factor Key: " . ($data->two_factor_key ?? "N/A") . "\n";
        // echo "Two-Factor PIN: " . (!empty($data->two_factor_pin) ? $data->two_factor_pin : "Not set") . "\n";
        echo "Date Added: " . ($data->date_added ?? "N/A") . "\n";
    } else {
        echo "No response data available.\n";
    }

    // Display errors if any
    if ($errors) {
        echo "\nErrors:\n";
        print_r($errors);
    }
} catch (Exception $e) {
    echo "An error occurred: " . $e->getMessage();
}
?>