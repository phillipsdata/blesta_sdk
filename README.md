# Blesta API SDK #

This development kit includes an API processor to make interfacing with the Blesta API super simple.

## Requirements ##

* PHP 5.2.0 or greater
* Blesta 3.0.0 or greater

### Using the API ###

```php
<?php
require_once "blesta_api.php";

$user = "YOUR_API_USER";
$key = "YOUR_API_KEY";
$url = "http://yourdomain.com/installpath/api/";

$api = new BlestaApi($url, $user, $key);

$response = $api->get("users", "get", array('user_id' => 1));

print_r($response->response());
print_r($response->errors());

?>
```

Plugin models are accessible as well:

```php
<?php
require_once "blesta_api.php";

$user = "YOUR_API_USER";
$key = "YOUR_API_KEY";
$url = "http://yourdomain.com/installpath/api/";

$api = new BlestaApi($url, $user, $key);

$response = $api->post("support_manager.support_manager_tickets", "close", array('ticket_id' => 1));

print_r($response->response());
print_r($response->errors());

?>
```