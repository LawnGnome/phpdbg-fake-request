<?php

namespace LawnGnome\PhpdbgFakeRequest;

use PHPUnit_Framework_TestCase;

class FunctionTest extends PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider buildRequestProvider
	 */
	public function testBuildRequest(string $order, array $sg, array $expected) {
		$this->assertSame($expected, buildRequest($order, $sg));
	}

	/**
	 * @dataProvider headerProvider
	 */
	public function testConvertHeadersToServer(array $headers, array $expected) {
		$this->assertSame($expected, convertHeadersToServer($headers));
	}

	/**
	 * @dataProvider superglobalProvider
	 */
	public function testCreateSuperglobal(array $source, array $expected) {
		$this->assertSame($expected, createSuperglobal($source));
	}

	/**
	 * @dataProvider elementNameProvider
	 */
	public function testMungeElementName(string $input, string $expected) {
		$this->assertSame($expected, mungeElementName($input));
	}

	/**
	 * @dataProvider headerNameProvider
	 */
	public function testMungeHeaderName(string $input, string $expected) {
		$this->assertSame($expected, mungeHeaderName($input));
	}

	public function buildRequestProvider(): array {
		return [
			'empty input' => ['CGPS', [], []],
			'partial input' => ['CGPS',
				[
					'_GET' => [
						'foo' => 'bar',
						'bar' => 'quux',
					],
					'_POST' => [
						'bar' => 'overridden',
						'array' => ['1', '2'],
					],
				],
				[
					'foo' => 'bar',
					'bar' => 'overridden',
					'array' => ['1', '2'],
				],
			],
			'extra order letters' => ['XCY',
				[
					'_COOKIE' => [
						'foo' => 'bar',
					],
					'_GET' => [
						'bar' => 'quux',
					],
				],
				[
					'foo' => 'bar',
				],
			],
		];
	}

	public function elementNameProvider(): array {
		return [
			['', ''],
			['foo', 'foo'],
			['foo.bar', 'foo_bar'],
			['foo bar', 'foo_bar'],
			['foo. bar', 'foo__bar'],
		];
	}

	public function headerNameProvider(): array {
		return [
			['', ''],
			['Foo', 'FOO'],
			['Foo-Bar', 'FOO_BAR'],
		];
	}

	public function headerProvider(): array {
		return [
			'simple' => [
				[
					'Foo' => 'bar',
					'Foo-Bar' => 'quux',
				],
				[
					'HTTP_FOO' => 'bar',
					'HTTP_FOO_BAR' => 'quux',
				],
			],
			'duplicates' => [
				[
					'Foo' => 'bar',
					'Foo-Bar' => 'quux',
					'Foo-Bar' => 'override',
				],
				[
					'HTTP_FOO' => 'bar',
					'HTTP_FOO_BAR' => 'override',
				],
			],
		];
	}

	public function superglobalProvider(): array {
		return [
			[
				[
					['foo', 'bar'],
					['bar', 'quux'],
					['bar', '123'],
					['bar', '234'],
				],
				[
					'foo' => 'bar',
					'bar' => ['quux', '123', '234'],
				],
			],
		];
	}
}

// vim: set noet ts=4 sw=4:
