<?php
/**
 * Blesta API response handler
 *
 * @copyright Copyright (c) 2013, Phillips Data, Inc.
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @package blesta_sdk
 */
class BlestaResponse {
	/**
	 * @var string The raw response from the API
	 */	
	private $raw;
	/**
	 * @var int The HTTP response code from the API
	 */
	private $response_code;
	
	/**
	 * Initializes the Blesta Response
	 *
	 * @param string $response The raw response data from an API request
	 * @param int $response_code The HTTP response code for the request
	 */
	public function __construct($response, $response_code) {
		$this->raw = $response;
		$this->response_code = $response_code;
	}
	
	/**
	 * Returns the response from the request
	 *
	 * @return mixed A stdClass object representing the response returned from the request, null if no response returned
	 */
	public function response() {
		$response = $this->formatResponse();
		if (isset($response->response))
			return $response->response;
		return null;
	}
	
	/**
	 * Returns the HTTP response code
	 *
	 * @return int The HTTP response code for the request
	 */
	public function responseCode() {
		return $this->response_code;
	}
	
	/**
	 * Returns the raw response
	 *
	 * @return string The raw response
	 */
	public function raw() {
		return $this->raw;
	}
	
	/**
	 * Returns all errors contained in the response
	 *
	 * @return stdClass A stdClass object representing the errors in the response, false if invalid response
	 */
	public function errors() {
		if ($this->response_code != 200) {
			$response = $this->formatResponse();

			if (isset($response->errors))
				return $response->errors;
			else {
				$error = new stdClass();
				$error->error = $response;
				return $error;
			}
		}
		return false;
	}
	
	/**
	 * Formats the raw response into a stdClass object
	 *
	 * @return stdClass A stdClass object representing the resposne
	 */
	private function formatResponse() {
		return json_decode($this->raw);
	}
}
?>