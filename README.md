KarotzPhp - PHP library for the Karotz REST API
===============================================

Installation
============

Old school
----------

Download the last version of KarotzPhp and add the `Karotz` namespace 
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

$kz = new karotz('interactive ID');

// Makes flash your Karotz LED in red
$response = $kz->ledPulse('FF0000', 500, 500);

// test the response
if ($reponse->getStatus()) {
    echo "
}
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

Jeremy Perret <jeremy@devster.org>
Thierry Geindre <karotz@nostalgeek.org>

License
-------

KarotzPhp is licensed under the MIT License - see the LICENSE file for details