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
 * A basic transport for the Karotz REST API
 * 
 * @author Jeremy perret <jeremy@devster.org>
 */
class FileTransport implements TransportInterface
{
    /**
     * {@inheritdoc}
     */
    public function sendRequest(Request $request)
    {
        $url = $request->getRestUrl();
        $ret = file_get_contents($url);
        
        if ($ret === false)
            throw new \Exception(sprintf("Unable to join this host with this URL: `%s`", $url));
            
        return $ret;
    }
}