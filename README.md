# Blesta SDK

A modern PHP SDK for interfacing with the Blesta API.

This development kit includes the following:

* An API processor to make interfacing with the Blesta API super simple
* Sample Merchant and Non-merchant Gateway implementations

## Requirements

* **PHP 8.1.0 or greater**
* **Blesta 3.0.0 or greater**
* cURL extension

## Features

* Modern PHP 8.1+ support with type declarations
* Blesta API header authentication (BLESTA-API-USER and BLESTA-API-KEY)
* Support for all HTTP methods (GET, POST, PUT, DELETE)
* Simple, fluent interface
* Full error handling

## Installation

### Via Composer (Recommended)

```bash
composer require phillipsdata/blesta_sdk
```

### Manual Installation

Clone or download this repository and include the API files:

```php
<?php
require_once "api/blesta_api.php";
```

## Using the API

Documentation on the API can be found in the [API Documentation](https://docs.blesta.com/developers/api).

### Basic Usage

```php
<?php
require_once "api/blesta_api.php";

$user = "YOUR_API_USER";
$key = "YOUR_API_KEY";
$url = "https://yourdomain.com/api/";

$api = new BlestaApi($url, $user, $key);

// GET request example
$response = $api->get("users", "get", ['user_id' => 1]);

if ($response->errors()) {
    print_r($response->errors());
} else {
    print_r($response->response());
}
```

### Creating a New Client

```php
<?php
require_once "api/blesta_api.php";

$api = new BlestaApi(
    "https://yourdomain.com/api/",
    "your_api_user",
    "your_api_key"
);

// POST request to create a client
$response = $api->post("clients", "add", [
    'user_id' => 1,
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'john.doe@example.com',
    'company' => 'Example Corp'
]);

if ($response->errors()) {
    echo "Error: " . print_r($response->errors(), true);
} else {
    $client = $response->response();
    echo "Client created with ID: " . $client->id;
}
```

### Accessing Plugin Models

Plugin models are accessible using dot notation:

```php
<?php
// Close a support manager ticket
$response = $api->post(
    "support_manager.support_manager_tickets",
    "close",
    ['ticket_id' => 1]
);

if (!$response->errors()) {
    echo "Ticket closed successfully!";
}
```

### Using Different HTTP Methods

```php
<?php
// GET request
$response = $api->get("clients", "get", ['client_id' => 1]);

// POST request
$response = $api->post("invoices", "add", $invoice_data);

// PUT request
$response = $api->put("clients", "edit", $updated_data);

// DELETE request
$response = $api->delete("services", "delete", ['service_id' => 5]);
```

### Error Handling

```php
<?php
$response = $api->get("clients", "get", ['client_id' => 999]);

// Check for errors
if ($errors = $response->errors()) {
    echo "HTTP Status Code: " . $response->responseCode() . "\n";
    print_r($errors);
} else {
    $client = $response->response();
    echo "Client Name: " . $client->first_name . " " . $client->last_name;
}
```

### Advanced Usage

#### Retrieve Last Request Details

```php
<?php
$response = $api->get("clients", "getAll");
$lastRequest = $api->lastRequest();

echo "URL: " . $lastRequest['url'] . "\n";
print_r($lastRequest['args']);
```

#### Working with Raw Response

```php
<?php
$response = $api->get("clients", "get", ['client_id' => 1]);

// Get raw JSON response
$rawJson = $response->raw();
echo $rawJson;

// Get parsed response
$data = $response->response();
```

## Authentication

All examples use the modern Blesta API header authentication method:

```php
// The SDK automatically sets the Blesta API headers
$api = new BlestaApi($apiUrl, $apiUser, $apiKey);
// Headers sent:
// BLESTA-API-USER: {user}
// BLESTA-API-KEY: {key}
```

This replaces the legacy HTTP Basic Auth method and provides better security and compatibility with modern PHP versions.

## Working with Gateways ##

Documentation on gateways can be found in the [Payment Gateways](http://docs.blesta.com/display/dev/Payment+Gateways) section of the [Developer Manual](http://docs.blesta.com/display/dev/)

Included in this SKD are two example gateways:

* Merchant Credit Card Gateway _/components/gateways/merchant/merchant_demo_cc/_
* Non-merchant Gateway _/components/gateways/nonmerchant/nonmerchant_demo/_