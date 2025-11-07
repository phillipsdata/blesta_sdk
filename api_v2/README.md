# Blesta SDK V2 (Beta)

This repository contains a simple SDK for interacting with the Blesta API. The SDK is written in PHP and provides an easy way to make API requests and handle responses.

---

## Files Overview

### `blesta_api.php`
This is the main SDK file that handles communication with the Blesta API. It includes two classes:

1. **BlestaApi**:
   - Handles API requests (`GET`, `POST`, `PUT`, `DELETE`) to the Blesta API.
   - Supports SSL verification and debug mode.
   - Provides methods to retrieve the last request details and parse API responses.

2. **BlestaResponse**:
   - Handles the response from the Blesta API.
   - Provides methods to access the raw response, parsed response, and any errors.

#### Key Features:
- **API Request Methods**:
  - `get($model, $method, $args = [])`
  - `post($model, $method, $args = [])`
  - `put($model, $method, $args = [])`
  - `delete($model, $method, $args = [])`
- **Response Handling**:
  - `response()` - Returns the parsed response.
  - `errors()` - Returns any errors in the response.
  - `raw()` - Returns the raw response as a string.
- **Debug Mode**:
  - When enabled, prints detailed request and response information.

---

### `config.php`
This file contains the configuration for the Blesta API. It is used to store API credentials and settings.

#### Configuration Options:
- **`url`**: The base URL of the Blesta API (e.g., `https://yourdomain.com/api/`).
- **`user`**: The API username.
- **`key`**: The API key.
- **`ssl_verify`**: A boolean value to enable or disable SSL verification (`true` by default).
- **`debug`**: A boolean value to enable or disable debug mode (`false` by default).

#### Example Configuration:
```php
return [
    'url' => 'https://yourdomain.com/api/',
    'user' => 'YOUR_API_USER',
    'key' => 'YOUR_API_KEY',
    'ssl_verify' => true,
    'debug' => false,
];