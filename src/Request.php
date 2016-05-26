<?php

namespace LawnGnome\PhpdbgFakeRequest;

class Request {
	protected $action;
	protected $cookies = [];
	protected $headers = [];
	protected $method;
	protected $post = [];
	protected $query = [];
	protected $script;

	public function addCookie(string $name, string $value) {
		$this->cookies[$name] = $value;
	}

	public function addHeader(string $name, string $value) {
		// Only the last header wins anyway in PHP, so this is at least
		// consistent.
		$this->headers[$name] = $value;
	}

	public function addPost(string $name, string $value) {
		$this->post[] = [$name, $value];
	}

	public function addQuery(string $name, string $value) {
		$this->query[] = [$name, $value];
	}

	public function buildServer(array $get): array {
		return [
			'GATEWAY_INTERFACE' => 'CGI/1.1',
			'PHP_SELF'          => $this->script ?: '/index.php',
			'QUERY_STRING'      => http_build_query($get),
			'REQUEST_METHOD'    => $this->method ?: 'GET',
			'REQUEST_URI'       => ($this->action ?: '/').(count($get) ? '?'.http_build_query($get) : ''),
			'SCRIPT_FILENAME'   => $this->script ?: getcwd().'/index.php',
			'SERVER_NAME'       => 'localhost',
			'SERVER_PROTOCOL'   => 'HTTP/1.1',
		] + convertHeadersToServer($this->headers) + [
			// Default headers.
			'HTTP_HOST' => 'localhost',
		];
	}

	public function getSuperglobals(string $order = null): array {
		$order = $order ?: 'EGPCS';
		$sg = [
			'_GET'    => createSuperglobal($this->query),
			'_POST'   => createSuperglobal($this->post),
			'_COOKIE' => $this->cookies,
		];

		$sg['_SERVER'] = $this->buildServer($sg['_GET']);
		$sg['_REQUEST'] = buildRequest($order, $sg);

		return $sg;
	}

	public function setAction(string $action) {
		$this->action = $action;
	}

	public function setMethod(string $method) {
		$this->method = $method;
	}

	public function setScript(string $script) {
		$this->script = $script;
	}
}

// vim: set noet ts=4 sw=4:
