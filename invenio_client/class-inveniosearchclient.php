<?php

class InvenioSearchClient extends GuzzleHttp\Client {

	var $uri;
	var $timeout;

	function __construct( $uri, $timeout = 2.0 ) {
		parent::__construct( [
			'base_uri' => $uri,
			'timeout'  => $timeout,
		]);
		$this->uri     = $uri;
		$this->timeout = $timeout;
	}

	function set_uri( $uri ) {
		$this->uri = $uri;
	}

	function get_uri() {
		return $this->uri;
	}

	function set_timeout( $timeout ) {
		$this->timeout = $timeout;
	}

	function get_timeout() {
		return $this->timeout;
	}

	function search( $args ) {
		try {
			$response = $this->request( 'GET', 'search', $args );
			if ( $response->getStatusCode() === 200 ) {
				return json_decode( $response->getBody(), true );
			} else {
				return 'Error retreiving records: ' . $response->getReasonPhrase() . "\nError code: " . $response->getStatusCode() . "\n";
			}
		} catch ( Exception $e ) {
			return 'Error retreiving records: ' . $e->getMessage() . "\n";
		}

	}
};
