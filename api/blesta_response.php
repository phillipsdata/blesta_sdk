<?php
/**
 * Blesta API response handler
 *
 * @copyright Copyright (c) 2013-2025, Phillips Data, Inc.
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @package blesta_sdk
 */
class BlestaResponse {
	/**
	 * @var string The raw response from the API
	 */
	private string $raw;
	/**
	 * @var int The HTTP response code from the API
	 */
	private int $response_code;

	/**
	 * Initializes the Blesta Response
	 *
	 * @param string $response The raw response data from an API request
	 * @param mixed $response_code The HTTP response code for the request
	 */
	public function __construct(string $response, mixed $response_code) {
		$this->raw = $response;
		$this->response_code = (int)$response_code;
	}
	
	/**
	 * Returns the response from the request
	 *
	 * @return mixed A stdClass object representing the response returned from the request, null if no response returned
	 */
	public function response(): mixed {
		$response = $this->formatResponse();
		if ($response && isset($response->response)) {
			return $response->response;
		}
		return null;
	}

	/**
	 * Returns the HTTP response code
	 *
	 * @return int The HTTP response code for the request
	 */
	public function responseCode(): int {
		return $this->response_code;
	}

	/**
	 * Returns the raw response
	 *
	 * @return string The raw response
	 */
	public function raw(): string {
		return $this->raw;
	}

	/**
	 * Returns all errors contained in the response
	 *
	 * @return object|false A stdClass object representing the errors in the response, false if no errors
	 */
	public function errors(): object|false {
		if ($this->response_code != 200) {
			$response = $this->formatResponse();

			if ($response && isset($response->errors)) {
				return $response->errors;
			} else {
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
	 * @return mixed A stdClass object representing the response, or null if JSON is invalid
	 */
	private function formatResponse(): mixed {
		return json_decode($this->raw);
	}
}
?>