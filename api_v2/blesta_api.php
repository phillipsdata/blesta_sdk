<?php
/**
 * Blesta API processor v2.0
 *
 * @copyright Copyright (c) 2013, Phillips Data, Inc.
 * @license http://opensource.org/licenses/mit-license.php MIT License
 * @package blesta_sdk
 */

require_once "config.php";

/**
 * Blesta API response handler
 *
 * Handles the response from the Blesta API.
 */
class BlestaResponse
{
    private string $raw;
    private int $response_code;

    public function __construct(string $response, int $response_code)
    {
        $this->raw = $response;
        $this->response_code = $response_code;
    }

    public function response(): mixed
    {
        $response = $this->formatResponse();
        return $response->response ?? null;
    }

    public function responseCode(): int
    {
        return $this->response_code;
    }

    public function raw(): string
    {
        return $this->raw;
    }

    public function errors(): stdClass|false
    {
        if ($this->response_code !== 200) {
            $response = $this->formatResponse();

            if (isset($response->errors)) {
                return $response->errors;
            }

            $error = new stdClass();
            $error->error = $response;
            return $error;
        }

        return false;
    }

    private function formatResponse(): ?stdClass
    {
        return json_decode($this->raw);
    }
}

/**
 * Blesta API processor
 *
 * Handles communication with the Blesta API.
 */
class BlestaApi
{
    private string $url;
    private string $user;
    private string $key;
    private bool $ssl_verify;
    private bool $debug;
    private ?array $last_request = null;
    private const FORMAT = "json";

    /**
     * Initializes the API with configuration.
     *
     * @param array $config Configuration array containing:
     *  - url: The URL to the Blesta API
     *  - user: The API user
     *  - key: The API key
     *  - ssl_verify: Whether to enforce SSL verification (default: true)
     *  - debug: Whether to enable debug mode (default: false)
     */
    public function __construct(array $config)
    {
        $this->url = $config['url'] ?? throw new InvalidArgumentException("API URL is required.");
        $this->user = $config['user'] ?? throw new InvalidArgumentException("API user is required.");
        $this->key = $config['key'] ?? throw new InvalidArgumentException("API key is required.");
        $this->ssl_verify = $config['ssl_verify'] ?? true;
        $this->debug = $config['debug'] ?? false;
    }

    public function get(string $model, string $method, array $args = []): BlestaResponse
    {
        return $this->submit($model, $method, $args, "GET");
    }

    public function post(string $model, string $method, array $args = []): BlestaResponse
    {
        return $this->submit($model, $method, $args, "POST");
    }

    public function put(string $model, string $method, array $args = []): BlestaResponse
    {
        return $this->submit($model, $method, $args, "PUT");
    }

    public function delete(string $model, string $method, array $args = []): BlestaResponse
    {
        return $this->submit($model, $method, $args, "DELETE");
    }

    private function submit(string $model, string $method, array $args = [], string $action = "POST"): BlestaResponse
    {
        $url = $this->url . $model . "/" . $method . "." . self::FORMAT;

        $this->last_request = [
            'url' => $url,
            'args' => $args
        ];

        if ($action === "GET") {
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
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->user}:{$this->key}");

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl_verify);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->ssl_verify ? 2 : 0);

        $response = curl_exec($ch);
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException("cURL error: $error");
        }

        curl_close($ch);

        if ($this->debug) {
            echo "Debug Info:\n";
            echo "URL: $url\n";
            echo "Action: $action\n";
            echo "Response Code: $response_code\n";
            echo "Response: $response\n";
        }

        return new BlestaResponse($response, $response_code);
    }

    public function lastRequest(): ?array
    {
        return $this->last_request;
    }
}
?>