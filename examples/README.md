# Blesta SDK Examples

This directory contains comprehensive examples demonstrating how to use the Blesta SDK with PHP 8.1+ and modern authorization header authentication.

## Prerequisites

Before running these examples, ensure you have:

1. PHP 8.1.0 or greater installed
2. cURL extension enabled
3. Valid Blesta API credentials (API user and key)
4. A working Blesta installation

## Configuration

Before running any example, update the following variables in each file:

```php
$apiUrl = 'https://yourdomain.com/api/';  // Your Blesta API URL
$apiUser = 'YOUR_API_USER';                // Your API username
$apiKey = 'YOUR_API_KEY';                  // Your API key
```

## Examples Overview

### 01_basic_usage.php
**Basic API Usage**
- Making GET requests
- Retrieving user and client data
- Working with raw responses
- Checking last request details

```bash
php examples/01_basic_usage.php
```

### 02_client_management.php
**Client Management**
- Creating new clients
- Retrieving client details
- Updating client information
- Listing clients with pagination

```bash
php examples/02_client_management.php
```

### 03_invoice_operations.php
**Invoice Operations**
- Retrieving invoices
- Creating new invoices
- Managing invoice line items
- Updating invoice status
- Generating invoice PDFs

```bash
php examples/03_invoice_operations.php
```

### 04_plugin_integration.php
**Plugin Integration**
- Working with Support Manager plugin
- Creating and managing support tickets
- Adding ticket replies
- Closing tickets
- Accessing other plugins (Order plugin example)

```bash
php examples/04_plugin_integration.php
```

### 05_error_handling.php
**Error Handling and Best Practices**
- Basic error checking
- Handling validation errors
- Using try-catch patterns
- Graceful degradation
- Batch operations with error tracking
- Network error handling

```bash
php examples/05_error_handling.php
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

## HTTP Methods

The SDK supports all standard HTTP methods:

- **GET**: Retrieve data (e.g., `$api->get('clients', 'get', $params)`)
- **POST**: Create new resources (e.g., `$api->post('clients', 'add', $data)`)
- **PUT**: Update existing resources (e.g., `$api->put('clients', 'edit', $data)`)
- **DELETE**: Remove resources (e.g., `$api->delete('services', 'delete', $params)`)

## Response Handling

Every API call returns a `BlestaResponse` object with the following methods:

```php
$response = $api->get('clients', 'get', ['client_id' => 1]);

// Get the response data
$data = $response->response();

// Check for errors
$errors = $response->errors();

// Get HTTP status code
$statusCode = $response->responseCode();

// Get raw JSON response
$raw = $response->raw();
```

## Error Handling

Always check for errors before processing the response:

```php
if ($errors = $response->errors()) {
    // Handle error
    echo "Error: HTTP " . $response->responseCode() . "\n";
    print_r($errors);
} else {
    // Process successful response
    $data = $response->response();
}
```

## Common HTTP Status Codes

- **200**: Success
- **400**: Bad Request (validation errors)
- **401**: Unauthorized (invalid credentials)
- **403**: Forbidden (insufficient permissions)
- **404**: Not Found
- **500**: Internal Server Error

## Tips

1. **Enable Error Reporting During Development**
   ```php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

2. **Use HTTPS in Production**
   Always use HTTPS for API requests in production environments to ensure secure transmission of credentials.

3. **Handle Rate Limiting**
   Be mindful of API rate limits and implement appropriate delays between requests when performing batch operations.

4. **Log Errors**
   Always log API errors for debugging and monitoring:
   ```php
   if ($errors = $response->errors()) {
       error_log("Blesta API Error: " . print_r($errors, true));
   }
   ```

5. **Cache Responses**
   Cache frequently accessed data to reduce API calls and improve performance.

## Additional Resources

- [Blesta API Documentation](https://docs.blesta.com/developers/api)
- [Blesta Developer Documentation](https://docs.blesta.com/developers)
- [GitHub Repository](https://github.com/phillipsdata/blesta_sdk)

## Support

For issues or questions:
- Check the [API Documentation](https://docs.blesta.com/developers/api)
- Open an issue on [GitHub](https://github.com/phillipsdata/blesta_sdk/issues)
- Contact Blesta Support

## License

MIT License - See LICENSE file for details
