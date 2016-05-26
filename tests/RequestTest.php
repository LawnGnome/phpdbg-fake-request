<?php

namespace LawnGnome\PhpdbgFakeRequest;

use PHPUnit_Framework_TestCase;

class RequestTest extends PHPUnit_Framework_TestCase {
	public function testAddCookie() {
		$request = new Request;

		$request->addCookie('foo', 'bar');
		$this->assertAttributeSame(['foo' => 'bar'], 'cookies', $request);

		$request->addCookie('bar', 'quux');
		$this->assertAttributeSame(['foo' => 'bar', 'bar' => 'quux'], 'cookies', $request);

		$request->addCookie('bar', 'override');
		$this->assertAttributeSame(['foo' => 'bar', 'bar' => 'override'], 'cookies', $request);
	}

	public function testAddHeader() {
		$request = new Request;

		$request->addHeader('foo', 'bar');
		$this->assertAttributeSame(['foo' => 'bar'], 'headers', $request);

		$request->addHeader('bar', 'quux');
		$this->assertAttributeSame(['foo' => 'bar', 'bar' => 'quux'], 'headers', $request);

		$request->addHeader('bar', 'override');
		$this->assertAttributeSame(['foo' => 'bar', 'bar' => 'override'], 'headers', $request);
	}

	public function testAddPost() {
		$request = new Request;

		$request->addPost('foo', 'bar');
		$request->addPost('foo', 'quux');

		$this->assertAttributeSame([
			['foo', 'bar'],
			['foo', 'quux'],
		], 'post', $request);
	}

	public function testAddQuery() {
		$request = new Request;

		$request->addQuery('foo', 'bar');
		$request->addQuery('foo', 'quux');

		$this->assertAttributeSame([
			['foo', 'bar'],
			['foo', 'quux'],
		], 'query', $request);
	}

	public function testBuildServerEmpty() {
		$request = new Request;

		$this->assertSame([
			'GATEWAY_INTERFACE' => 'CGI/1.1',
			'PHP_SELF'          => '/index.php',
			'QUERY_STRING'      => '',
			'REQUEST_METHOD'    => 'GET',
			'REQUEST_URI'       => '/',
			'SCRIPT_FILENAME'   => getcwd().'/index.php',
			'SERVER_NAME'       => 'localhost',
			'SERVER_PROTOCOL'   => 'HTTP/1.1',
			'HTTP_HOST'         => 'localhost',
		], $request->buildServer([]));
	}

	public function testBuildServerSet() {
		$request = new Request;

		$request->setAction('/index.php');
		$request->setMethod('DELETE');
		$request->setScript('index.php');

		$request->addHeader('foo', 'bar');
		$request->addHeader('bar', 'quux');
		$request->addHeader('host', 'junk');

		$get = [
			['a', 'b'],
			['a', 'c'],
			['b', 'd'],
		];

		$this->assertSame([
			'GATEWAY_INTERFACE' => 'CGI/1.1',
			'PHP_SELF'          => 'index.php',
			'QUERY_STRING'      => 'a%5B0%5D=b&a%5B1%5D=c&b=d',
			'REQUEST_METHOD'    => 'DELETE',
			'REQUEST_URI'       => '/index.php?a%5B0%5D=b&a%5B1%5D=c&b=d',
			'SCRIPT_FILENAME'   => 'index.php',
			'SERVER_NAME'       => 'localhost',
			'SERVER_PROTOCOL'   => 'HTTP/1.1',
			'HTTP_FOO'          => 'bar',
			'HTTP_BAR'          => 'quux',
			'HTTP_HOST'         => 'junk',
		], $request->buildServer(createSuperglobal($get)));
	}

	public function testGetSuperglobalsEmpty() {
		$request = new Request;

		$this->assertSame([
			'_GET' => [],
			'_POST' => [],
			'_COOKIE' => [],
			'_SERVER' => [
				'GATEWAY_INTERFACE' => 'CGI/1.1',
				'PHP_SELF'          => '/index.php',
				'QUERY_STRING'      => '',
				'REQUEST_METHOD'    => 'GET',
				'REQUEST_URI'       => '/',
				'SCRIPT_FILENAME'   => getcwd().'/index.php',
				'SERVER_NAME'       => 'localhost',
				'SERVER_PROTOCOL'   => 'HTTP/1.1',
				'HTTP_HOST'         => 'localhost',
			],
			'_REQUEST' => [
				'GATEWAY_INTERFACE' => 'CGI/1.1',
				'PHP_SELF'          => '/index.php',
				'QUERY_STRING'      => '',
				'REQUEST_METHOD'    => 'GET',
				'REQUEST_URI'       => '/',
				'SCRIPT_FILENAME'   => getcwd().'/index.php',
				'SERVER_NAME'       => 'localhost',
				'SERVER_PROTOCOL'   => 'HTTP/1.1',
				'HTTP_HOST'         => 'localhost',
			],
		], $request->getSuperglobals());
	}

	public function testGetSuperglobalsSet() {
		$request = new Request;

		$request->setAction('/index.php');
		$request->setMethod('DELETE');
		$request->setScript('index.php');

		$request->addCookie('e', 'f');
		$request->addCookie('e', 'g');
		$request->addCookie('f', 'h');

		$request->addHeader('foo', 'bar');
		$request->addHeader('bar', 'quux');
		$request->addHeader('host', 'junk');

		$request->addPost('c', 'e');
		$request->addPost('c', 'f');
		$request->addPost('d', 'g');

		$request->addQuery('a', 'b');
		$request->addQuery('a', 'c');
		$request->addQuery('b', 'd');

		$this->assertSame([
			'_GET' => [
				'a' => ['b', 'c'],
				'b' => 'd',
			],
			'_POST' => [
				'c' => ['e', 'f'],
				'd' => 'g',
			],
			'_COOKIE' => [
				'e' => 'g',
				'f' => 'h',
			],
			'_SERVER' => [
				'GATEWAY_INTERFACE' => 'CGI/1.1',
				'PHP_SELF'          => 'index.php',
				'QUERY_STRING'      => 'a%5B0%5D=b&a%5B1%5D=c&b=d',
				'REQUEST_METHOD'    => 'DELETE',
				'REQUEST_URI'       => '/index.php?a%5B0%5D=b&a%5B1%5D=c&b=d',
				'SCRIPT_FILENAME'   => 'index.php',
				'SERVER_NAME'       => 'localhost',
				'SERVER_PROTOCOL'   => 'HTTP/1.1',
				'HTTP_FOO'          => 'bar',
				'HTTP_BAR'          => 'quux',
				'HTTP_HOST'         => 'junk',
			],
			'_REQUEST' => [
				'a' => ['b', 'c'],
				'b' => 'd',
				'c' => ['e', 'f'],
				'd' => 'g',
			],
		], $request->getSuperglobals('GP'));
	}

	public function testSetters() {
		$request = new Request;

		$request->setAction('/');
		$this->assertAttributeSame('/', 'action', $request);

		$request->setMethod('GET');
		$this->assertAttributeSame('GET', 'method', $request);

		$request->setScript('index.php');
		$this->assertAttributeSame('index.php', 'script', $request);
	}
}

// vim: set noet ts=4 sw=4:
