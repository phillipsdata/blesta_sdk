# Blesta SDK #

This development kit includes the following:

* An API processor to make interfacing with the Blesta API super simple
* Sample Merchant and Non-merchant Gateway implementations

## Requirements ##

* PHP 5.2.0 or greater
* Blesta 3.0.0 or greater

## Using the API ##

Documentation on the API can be found in the [API](http://docs.blesta.com/display/dev/API) section of the [Developer Manual](http://docs.blesta.com/display/dev/)

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

## Working with Gateways ##

Documentation on gateways can be found in the [Payment Gateways](http://docs.blesta.com/display/dev/Payment+Gateways) section of the [Developer Manual](http://docs.blesta.com/display/dev/)

Included in this SKD are two example gateways:

* Merchant Credit Card Gateway _/components/gateways/merchant/merchant_demo_cc/_
* Non-merchant Gateway _/components/gateways/nonmerchant/nonmerchant_demo/_