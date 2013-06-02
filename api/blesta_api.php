<?php
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "blesta_response.php";

/**
 * Blesta API processor
 *
 * @copyright Copyright (c) 2013, Phillips Data, Inc.
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @package blesta_sdk
 */
class BlestaApi {
	
	/**
	 * @var string The URL to the Blesta API
	 */
	private $url;
	/**
	 * @var string The API user
	 */
	private $user;
	/**
	 * @var string The API key
	 */
	private $key;
	/**
	 * @var string API response format
	 */
	private static $format = "json";
	
	/**
	 * Initializes the API
	 *
	 * @param string $url The URL to the Blesta API
	 * @param string $user The API user
	 * @param string $key The API key
	 */
	public function __construct($url, $user, $key) {
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
	public function get($model, $method, array $args = array()) {
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
	public function post($model, $method, array $args = array()) {
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
	public function put($model, $method, array $args = array()) {
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
	public function delete($model, $method, array $args = array()) {
		return $this->submit($model, $method, $args, "DELETE");
	}
	
	/**
	 * Submits a request to the API
	 *
	 * @param string $uri The URI to submit to
	 * @param array $args An array of key/value pair arguments to submit to the given API command
	 * @return BlestaResponse The response object
	 */
	private function submit($model, $method, array $args = array(), $action = "POST") {
		
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
		if ($args) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
		}

		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, $this->user . ":" . $this->key);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$response = curl_exec($ch);
		$response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		return new BlestaResponse($response, $response_code);
	}
	
	/**
	 * Returns the details of the last request made
	 *
	 * @return array An array containg:
	 *	- url The URL of the last request
	 *	- args The paramters passed to the URL
	*/
	public function lastRequest() {
		return $this->last_request;
	}
}
?>