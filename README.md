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

$kz = new Karotz('interactive ID');

// Make flash your Karotz LED in red
$response = $kz->ledPulse('FF0000', 500, 500);

// Test the response
if ($response->getStatus())
    echo "Rabbit flashs red light!";
else
    echo "Error: ".$response->getCode()." ".$response->getDescription();
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