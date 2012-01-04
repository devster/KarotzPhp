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
 * Interface for transports
 * 
 * @author Jeremy perret <jeremy@devster.org>
 */
interface TransportInterface
{
    /**
     * Returns the raw Karotz REST API response
     * 
     * @param Request $request
     * @return string
     */
    public function sendRequest(Request $request);
}