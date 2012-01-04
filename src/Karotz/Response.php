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

namespace Karotz;

/**
 * A Karotz REST API Response
 * 
 * @author Jeremy perret <jeremy@devster.org>
 */
class Response
{
    protected
        $request,
        $voomsg_raw,
        $voomsg
    ;
    
    /**
     * Constructor
     * 
     * @param string $vooMsg
     * @param Request $request 
     */
    public function __construct($vooMsg, Request $request)
    {
        $this->voomsg_raw = $vooMsg;
        $this->request = $request;
        $this->voomsg = $this->_parseXml($vooMsg);
    }
    
    /**
     * Gets the status of the response
     * 
     * @return boolean
     */
    public function getStatus()
    {
        // Experimental, waiting for some test with real Karotz responses
        if ($code = $this->getCode()) {
            if ('OK' == $code)
                return true;
            return false;
        }
        
        return true;
    }
    
    /**
     * Gets the id
     * 
     * @return string
     */
    public function getId()
    {
        if (isset($this->voomsg->id))
            return (string) $this->voomsg->id;
    }
    
    /**
     * Gets the correlation id
     * 
     * @return string
     */
    public function getCorrelationId()
    {
        if (isset($this->voomsg->correlationid))
            return (string) $this->voomsg->correlationid;
    }
    
    /**
     * Gets the interactive id
     * @return string
     */
    public function getInteractiveId()
    {
        if (isset($this->voomsg->interactiveid))
            return (string) $this->voomsg->interactiveid;
    }
    
    /**
     * Gets the response code
     * 
     * @return string 
     */
    public function getCode()
    {
        if (isset($this->voomsg->response->code))
            return (string) $this->voomsg->response->code;
    }
    
    /**
     * Gets the response description
     * 
     * @return string
     */
    public function getDescription()
    {
        if (isset($this->voomsg->response->description))
            return (string) $this->voomsg->response->description;
    }
    
    /**
     * Gets the voomsg XML object
     * 
     * @return \SimpleXMLElement
     */
    public function getVooMsg()
    {
        return $this->voomsg;
    }
    
    /**
     * Gets the request that is causing this response
     * 
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * Gets the original string response returns by the Karotz REST API
     * 
     * @return string
     */
    public function getRawVooMsg()
    {
        return $this->voomsg_raw;
    }
    
    /**
     * Parse some XML string
     * 
     * @param string $xml_string
     * @return \SimpleXMLElement
     */
    protected function _parseXml($xml_string)
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xml_string);
        if (! $xml) {
            $xml_errors = array_map(function ($e) {
                return $e->message;
            }, libxml_get_errors());
            
            throw new \RuntimeException(sprintf("Error parsing the XML response:\n %s in `%s`",
                                            implode("\n", $xml_errors), $xml_string));
            libxml_clear_errors();
        }
        libxml_use_internal_errors(false);
        
        return $xml;
    }
}