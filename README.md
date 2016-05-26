# phpdbg request faker

This package provides an executable script that can be used with
[phpdbg](http://phpdbg.com) to fake a web request.

[![Build Status](https://travis-ci.org/LawnGnome/phpdbg-fake-request.svg?branch=master)](https://travis-ci.org/LawnGnome/phpdbg-fake-request)

## Installation

```sh
composer require lawngnome/phpdbg-fake-request
```

## Usage

To fake a `GET` request to `/` with `public/index.php` as the entry point:

```sh
./vendor/bin/fake-request GET / public/index.php
```

This will launch phpdbg. The script can then be run with phpdbg's `run`
command:

```
run
```

### Populating `$_GET` and `$_POST`

The `-g` and `-p` options allow for GET and POST variables to be sent:

```sh
./vendor/bin/fake-request GET / public/index.php -g 'page=2'
./vendor/bin/fake-request POST / public/index.php -p 'page=2'
```

Values do not need to be URL encoded.

### Headers

Similarly, the `-H` option allows for HTTP headers to be sent:

```sh
./vendor/bin/fake-request GET / public/index.php -H 'X-Foo: bar'
```

### Cookies

The `-c` option allows cookies to be sent:

```sh
./vendor/bin/fake-request GET / public/index.php -c 'PHPSESSID=foo'
```

## Known issues

### Arbitrary POST data is unsupported

It is impossible at present to set the `php://input` stream up at runtime, so
non-form POST data is not supported.

## Feedback

Please send issues and pull requests through
[GitHub](https://github.com/LawnGnome/phpdbg-fake-request).

## Acknowledgements

This builds on top of the excellent
[documentation](http://phpdbg.com/docs/mocking-webserver) on the phpdbg site
and Symfony's equally excellent
[console component](http://symfony.com/doc/current/components/console/index.html).

You can also e-mail me at aharvey@php.net, if you feel so inclined.
