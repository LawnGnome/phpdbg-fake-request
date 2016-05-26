<?php

namespace LawnGnome\PhpdbgFakeRequest;

function buildRequest(string $order, array $sg): array {
	static $types = [
		'C' => '_COOKIE',
		'G' => '_GET',
		'P' => '_POST',
		'S' => '_SERVER',
	];

	$request = [];

	foreach (str_split($order) as $c) {
		if (isset($types[$c]) && isset($sg[$types[$c]])) {
			$request = array_merge($request, $sg[$types[$c]]);
		}
	}

	return $request;
}

function convertHeadersToServer(array $headers): array {
	$server = [];

	array_walk($headers, function (string $value, string $name) use (&$server) {
		$server['HTTP_'.mungeHeaderName($name)] = $value;
	});

	return $server;
}

function createSuperglobal(array $source): array {
	$global = [];

	array_walk($source, function (array $element) use (&$global) {
		list($name, $value) = $element;
		$name = mungeElementName($name);
		if (isset($global[$name])) {
			if (is_array($global[$name])) {
				$global[$name][] = $value;
			} else {
				$orig = $global[$name];
				$global[$name] = [$orig, $value];
			}
		} else {
			$global[$name] = $value;
		}
	});

	return $global;
}

function mungeElementName(string $name): string {
	return str_replace(['.', ' '], '_', $name);
}

function mungeHeaderName(string $name): string {
	return str_replace('-', '_', strtoupper($name));
}


// vim: set noet ts=4 sw=4:
