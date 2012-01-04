KarotzPhp - PHP library for the Karotz REST API
===============================================

Installation
============

Old school
----------

Download the latest version of KarotzPhp and add the `Karotz` namespace 
to your PSR-0 autoloading system, or simply require the autoload.php

Composer
--------

```json
{
    "require": {
        "karotz/karotz": ">=1.0.0-alpha"
    }
}
```

Usage
=====

```php
use Karotz\Karotz;

$kz = new Karotz('Interactive ID');

try {
	// Make flash your Karotz LED in red
	$response = $kz->ledPulse('FF0000', 500, 500);
	
	// Test the response
	if ($response->getStatus())
	    echo "Rabbit flashs red light!";
	else
	    echo $response->getCode().": ".$response->getDescription();

} catch(\Exception $e) {
	echo $e->getMessage();
}
```

Get the Interactive ID
----------------------

To run, the Karotz REST API needs an Interactive ID, which is reset every 15 minutes.
Check the official documentation to know how retrieve this ID. http://dev.karotz.com/api/interactiveid.html
KarotzPhp is not involved in managing the life cycle of the Interactive ID, but provides 
a way to get it with the signed START method:

```php
use Karotz\Karotz;

$kz = new Karotz();

// Open a session and save the Interactive ID in the Karotz object
$response = $kz->start('install ID', 'API key', 'secret key');
```

Features
========

Work in progress... waiting for some test

About
=====

Requirements
------------

- Any flavor of PHP 5.3 should do

Author
------

- Jeremy Perret <jeremy@devster.org>
- Thierry Geindre <karotz@nostalgeek.org>

License
-------

KarotzPhp is licensed under the MIT License - see the LICENSE file for details