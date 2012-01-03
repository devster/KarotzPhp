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
    /**
     * {@inheritdoc}
     */
    public function sendRequest(Request $request)
    {
        // TODO
    }
}