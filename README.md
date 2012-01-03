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
'''json
{
    "require": {
        "karotz/karotz": ">=1.0.0-alpha"
    }
}
'''

Usage
=====

'''php
use Karotz\Karotz;

$kz = new karotz('interactive ID');

// Makes flash your Karotz LED in red
$response = $kz->ledPulse('FF0000', 500, 500);

// test the response
if ($reponse->getStatus()) {
    echo "
}
'''