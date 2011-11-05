<?php
/**
 * Topsy API Class.
 *
 * @author Rowan Manning <info@rowanmanning.co.uk>
 * @copyright Copyright 2011, Rowan Manning
 * @license Dual licensed under the MIT or GPL Version 2 licenses.
 * @filesource
 */

//============================================================
// CLASS DEFINITION
//============================================================

/**
 * Topsy API Class.
 */
class Topsy {
	
	//------------------------------------------------------------
	// VARIABLES
	//------------------------------------------------------------
	
	/**
	 * The API key to send with all API requests.
	 *
	 * @var string
	 */
	protected $api_key = null;
	
	/**
	 * The HTTP User-Agent header to send with all API requests.
	 *
	 * @var string
	 */
	protected $user_agent = null;
	
	/**
	 * The API rate limit.
	 *
	 * @var integer
	 */
	protected $api_rate_limit = null;
	
	/**
	 * The API rate limit remaining.
	 *
	 * @var integer
	 */
	protected $api_rate_limit_remaining = null;
	
	/**
	 * The API rate limit reset time, as a UNIX time-stamp.
	 *
	 * @var integer
	 */
	protected $api_rate_limit_reset = null;
	
	//------------------------------------------------------------
	// GET/SET FUNCTIONS
	//------------------------------------------------------------
	
	/**
	 * Get/set the API key that is sent with all API requests.
	 *
	 * @param string $value The API key to set. Default value is `null`.
	 * @return string|Topsy Returns the API key if `$value` is `null`, otherwise the calling `Topsy` object is returned for chaining.
	 */
	public function api_key($value = null) {
		
		// get
		if ($value === null) {
			return $this->api_key;
		}
		
		// set
		$this->api_key = (string) $value;
		return $this;
		
	}
	
	/**
	 * Get/set the HTTP User-Agent header that is sent with all API requests.
	 *
	 * @param string $value The HTTP User-Agent to set. Topsy specifies that this should be your application or service URL. Default value is `null`.
	 * @return string|Topsy Returns the User-Agent if `$value` is `null`, otherwise the calling `Topsy` object is returned for chaining.
	 */
	public function user_agent($value = null) {
		
		// get
		if ($value === null) {
			return $this->user_agent;
		}
		
		// set
		$this->user_agent = (string) $value;
		return $this;
		
	}
	
	/**
	 * Get the API rate limit.
	 *
	 * @return integer Returns the API rate limit.
	 */
	public function api_rate_limit() {
		
		// ensure we have rate limit data
		if ($this->api_rate_limit === null) {
			$this->get('credit');
		}
		
		// get
		return $this->api_rate_limit;
		
	}
	
	/**
	 * Get the API rate limit remaining.
	 *
	 * @return integer Returns the API rate limit remaining.
	 */
	public function api_rate_limit_remaining() {
		
		// ensure we have rate limit data
		if ($this->api_rate_limit === null) {
			$this->get('credit');
		}
		
		// get
		return $this->api_rate_limit_remaining;
		
	}
	
	/**
	 * Get the API rate limit reset time, as a UNIX time-stamp.
	 *
	 * @return integer Returns the API rate limit reset time.
	 */
	public function api_rate_limit_reset() {
		
		// ensure we have rate limit data
		if ($this->api_rate_limit === null) {
			$this->get('credit');
		}
		
		// get
		return $this->api_rate_limit_reset;
		
	}
	
	//------------------------------------------------------------
	// FUNCTIONS
	//------------------------------------------------------------
	
	/**
	 * Class constructor.
	 * 
	 * @param string $api_key The API key to send with all API requests. Default value is `null`.
	 * @param string $user_agent The HTTP User-Agent header to send with all API requests. Topsy specifies that this should be your application or service URL. Default value is `null`.
	 */
	public function __construct($api_key = null, $user_agent = null) {
		
		// sanitize and store arguments
		$this->api_key = (string) $api_key;
		$this->user_agent = (string) $user_agent;
		
	}
	
	/**
	 * Calculate an API URL from an endpoint.
	 *
	 * @param string $value The API key to set. Default value is `null`.
	 * @return string Returns the calculated URL.
	 */
	protected function calculate_url($endpoint) {
		
		// sanitize arguments
		$endpoint = trim((string) $endpoint, '/');
		
		// return the URL
		return 'http://otter.topsy.com/' . $endpoint . '.json';
		
	}
	
	/**
	 * Make a request to the API.
	 *
	 * @param string $method The HTTP method to use.
	 * @param string $endpoint The API endpoint to request.
	 * @param array $params An array of parameters to send with the request. Default value is `null`.
	 * @return array Returns an array of response data.
	 */
	protected function request($method, $endpoint, $params = null) {
		
		// sanitize arguments
		$method = strtoupper((string) $method);
		$params = ($params === null ? array() : (array) $params);
		
		// check for cURL
		if (!function_exists('curl_init')) {
			throw new RuntimeException('The PHP cURL extension is required to make API requests.');
		}
		
		// calculate the URL
		$url = $this->calculate_url($endpoint);
		
		// store API key and build the query
		if ($this->api_key !== null) {
			$params['apikey'] = $this->api_key;
		}
		$query = http_build_query($params);
		
		// initialize a cURL handle and set some basic options
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, true);
		
		// set headers
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'User-Agent: ' . $this->user_agent,
		));
		
		// set method and query/body
		if ($method == 'GET') {
			$url = $url . '?' . $query;
		} else {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		}
		
		// set the cURL URL
		curl_setopt($ch, CURLOPT_URL, $url);
		
		// execute the request
		$raw_response = curl_exec($ch);
		$curl_info = curl_getinfo($ch);
		curl_close($ch);
		
		// get the response headers/body
		@list($raw_response_headers, $response_body) = preg_split('/(\r\n|\n){2}/', $raw_response, 2);
		$raw_response_headers = preg_split('/(\r\n|\n)/', $raw_response_headers);
		array_shift($raw_response_headers);
		$response_headers = array();
		foreach ($raw_response_headers as $response_header) {
			$response_header_name = $response_header_value = '';
			@list($response_header_name, $response_header_value) = explode(':', $response_header, 2);
			$response_headers[trim($response_header_name)] = trim($response_header_value);
		}
		
		// store the new rate limit data
		if (isset($response_headers['X-RateLimit-Limit'])) {
			$this->api_rate_limit = (integer) $response_headers['X-RateLimit-Limit'];
			$this->api_rate_limit_remaining = (integer) $response_headers['X-RateLimit-Remaining'];
			$this->api_rate_limit_reset = (integer) $response_headers['X-RateLimit-Reset'];
		}
		
		// package up and return the response
		$response = (object) array(
			'status' => $curl_info['http_code'],
			'headers' => $response_headers,
			'body' => json_decode($response_body),
		);
		return $response;
		
	}
	
	/**
	 * Make a GET request to the API.
	 *
	 * @param string $endpoint The API endpoint to request.
	 * @param array $params An array of parameters to send with the request. Default value is `null`.
	 * @return array Returns an array of response data.
	 */
	public function get($endpoint, $params = null) {
		
		return $this->request('GET', $endpoint, $params);
		
	}
	
	//------------------------------------------------------------
	
}

//============================================================
// end of file
