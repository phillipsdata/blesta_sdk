<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "blesta_response.php";

/**
 * Blesta API processor
 *
 * Provides a simple interface to interact with the Blesta API using the
 * BLESTA-API-USER and BLESTA-API-KEY headers instead of legacy HTTP basic auth.
 *
 * @copyright Copyright (c) 2013-2025, Phillips Data, Inc.
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @package blesta_sdk
 */
class BlestaApi {

	/**
	 * @var string The URL to the Blesta API
	 */
	private string $url;
	/**
	 * @var string The API user
	 */
	private string $user;
	/**
	 * @var string The API key
	 */
	private string $key;
	/**
	 * @var array The details of the last request made
	 */
	private array $last_request = [];
	/**
	 * @var string API response format
	 */
	private static string $format = "json";
	
	/**
	 * Initializes the API
	 *
	 * @param string $url The URL to the Blesta API (e.g. https://yourdomain.com/api/)
	 * @param string $user The API user
	 * @param string $key The API key
	 */
	public function __construct(string $url, string $user, string $key) {
		$this->url = $url;
		$this->user = $user;
		$this->key = $key;
	}

	/**
	 * Submit an API request via GET
	 *
	 * @param string $model The model to request (e.g. users)
	 * @param string $method The method to request (e.g. add)
	 * @param array $args An array of arguments to pass to the method
	 * @return BlestaResponse The response object
	 */
	public function get(string $model, string $method, array $args = []): BlestaResponse {
		return $this->submit($model, $method, $args, "GET");
	}

	/**
	 * Submit an API request via POST
	 *
	 * @param string $model The model to request (e.g. users)
	 * @param string $method The method to request (e.g. add)
	 * @param array $args An array of arguments to pass to the method
	 * @return BlestaResponse The response object
	 */
	public function post(string $model, string $method, array $args = []): BlestaResponse {
		return $this->submit($model, $method, $args, "POST");
	}

	/**
	 * Submit an API request via PUT
	 *
	 * @param string $model The model to request (e.g. users)
	 * @param string $method The method to request (e.g. add)
	 * @param array $args An array of arguments to pass to the method
	 * @return BlestaResponse The response object
	 */
	public function put(string $model, string $method, array $args = []): BlestaResponse {
		return $this->submit($model, $method, $args, "PUT");
	}

	/**
	 * Submit an API request via DELETE
	 *
	 * @param string $model The model to request (e.g. users)
	 * @param string $method The method to request (e.g. add)
	 * @param array $args An array of arguments to pass to the method
	 * @return BlestaResponse The response object
	 */
	public function delete(string $model, string $method, array $args = []): BlestaResponse {
		return $this->submit($model, $method, $args, "DELETE");
	}
	
	/**
	 * Submits a request to the API
	 *
	 * @param string $model The model to request
	 * @param string $method The method to request
	 * @param array $args An array of key/value pair arguments to submit to the given API command
	 * @param string $action The HTTP method (GET, POST, PUT, DELETE)
	 * @return BlestaResponse The response object
	 */
	private function submit(string $model, string $method, array $args = [], string $action = "POST"): BlestaResponse {

		$url = $this->url . $model . "/" . $method . "." . self::$format;

		$this->last_request = array(
			'url' => $url,
			'args' => $args
		);

		if ($action == "GET") {
			$url .= "?" . http_build_query($args);
			$args = null;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $action);
		curl_setopt($ch, CURLOPT_URL, $url);

		// Set Blesta API headers with user and key
		$headers = array(
			'BLESTA-API-USER: ' . $this->user,
			'BLESTA-API-KEY: ' . $this->key
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		if ($args) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
		}

		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$response = curl_exec($ch);
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		// Handle curl_exec failure
		if ($response === false) {
			$response = '';
		}

		return new BlestaResponse($response, $response_code);
	}
	
	/**
	 * Returns the details of the last request made
	 *
	 * @return array An array containing:
	 *	- url The URL of the last request
	 *	- args The parameters passed to the URL
	 */
	public function lastRequest(): array {
		return $this->last_request;
	}
}
?>