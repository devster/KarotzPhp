<?php
/*
* This file is part of the Karotz package.
*
* (c) Jeremy Perret <jeremy@devster.org>
* (c) Thierry Geindre <karotz@nostalgeek.org>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Karotz\Transport;

use Karotz\Request;

/**
 * A CURL transport for the Karotz REST API
 * 
 * @author Jeremy perret <jeremy@devster.org>
 */
class CurlTransport implements TransportInterface
{
    public $ch;

    /**
     * Constructor
     */
    public function __construct()
    {
    	$this->ch = curl_init();
    }

    /**
     * {@inheritdoc}
     */
    public function sendRequest(Request $request)
    {
        curl_setopt_array($this->ch, array(
        	CURLOPT_URL => $request->getRestUrl(),
        	CURLOPT_RETURNTRANSFER => true,
	    ));

	    if (false === $ret = curl_exec($this->ch))
	    	throw new \Exception(curl_error($this->ch), curl_errno($this->ch));

	    $info = curl_getinfo($this->ch);
	    if (200 != $info['http_code'])
	    	throw new \Exception(sprintf("Karotz REST API is unreachable (returned http code: %s)", $info['http_code']), 1);

	    return $ret;
    }
}