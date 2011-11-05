# Topsy API PHP Library #

This is a simple PHP library for using the Topsy API. The library requires PHP 5.3, and is easy to use.

Read the [API Documentation](http://code.google.com/p/otterapi/wiki/Resources) for more info on the API itself, or see examples below for using the library.

The Topsy API allows anonymous access, but it's best to use an API key; you can [get an API key here](http://manage.topsy.com/app/) if you need one. Topsy also asks that you send a User-Agent containing the URL of your app or service, this can be done in the constructor.


## Basic Usage ##

The `Topsy::get()` method is used to send a `GET` request to the Topsy API. The first argument is the endpoint to get, the second is an array of query parameters. All class methods are well documented in the source if you require further detail.

```php
<?php

// config
$api_key = 'ABC123';
$user_agent = 'http://yourapp.com/';

// create a new `Topsy` instance
$topsy = new Topsy($api_key, $user_agent);

// make a call to the '/trackbacks' endpoint, getting trackbacks for the GitHub website
$response = $topsy->get('trackbacks', array(
	'url' => 'http://github.com/',
));

?>
```

All requests return a simple object, so the `$response` variable above will contain something like this:

```
stdClass Object
(
	
	// the status property contains the HTTP status code of
	// the response
	[status] => 200
	
	// the headers property contains an array of all headers sent
	// as part of the response
	[headers] => Array
		(
			[Content-Type] => application/json; charset=utf-8
			[X-RateLimit-Limit] => 10000
			[X-RateLimit-Remaining] => 10000
			[X-RateLimit-Reset] => 1234567890
			// etc...
		)
	
	// the body property contains the decoded JSON response body
	// which differs depending on the requested endpoint. see
	// http://code.google.com/p/otterapi/wiki/ResponseFormats#JSON
	// for more detail
	[body] => stdClass Object
		(
			
			// details the request that lead to this response
			[request] => stdClass Object
				(
					// etc...
				)
			
			// the actual response
			[response] => stdClass Object
				(
					// properties depend on the endpoint requested
				)

		)

)
```


## Testing ##

This library is tested using [PHPUnit](https://github.com/sebastianbergmann/phpunit). To run tests, navigate to the repository via the command line and run the following:

```sh
$ phpunit
```


## License ##

Copyright 2011, Rowan Manning  
Dual licensed under the [MIT](http://opensource.org/licenses/mit-license.php) or [GPL Version 2](http://opensource.org/licenses/gpl-2.0.php) licenses.
