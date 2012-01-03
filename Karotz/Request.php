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
 * A Karotz REST API Request
 * 
 * @author Jeremy perret <jeremy@devster.org>
 */
class Request
{
    protected
        $parameters = array(),
        $command,
        $api_url,
        $rest_url
    ;
    
    /**
     * Constructor
     * 
     * @param string $api_url 
     * @param string $command one of Karotz::API_CMD_* values
     * @param array  $parameters 
     */
    public function __construct($api_url = null, $command = null, $parameters = null)
    {
        if (! is_null($api_url))
            $this->setApiUrl($api_url);
            
        if (! is_null($command))
            $this->setCommand($command);
            
        if (!is_null($parameters))
            $this->setParameters($parameters);
    }
    
    /**
     * Gets the REST API URL
     * 
     * @return string
     */
    public function getRestUrl()
    {
        // If the rest url is not set by hand, we create it
        if (is_null($this->rest_url)) {
            if (is_null($this->command))
                throw new LogicException("The command can not be empty.");
                
            if (is_null($this->api_url))
            throw new \LogicException("The api url can not be empty.");
        
            if (strpos($this->api_url, '{api_cmd}') === false || strpos($this->api_url, '{params}') === false)
                throw new \LogicException(sprintf("The api url is in the wrong format. It must contain the {api_cmd} and {params} tags."));
            
            $this->rest_url = $this->_makeRestUrl();
        }
        
        return $this->rest_url;
    }
    
    /**
     * Sets the REST API URL
     * 
     * @param string $rest_url
     * @return Request 
     */
    public function setRestUrl($rest_url)
    {
        $this->rest_url = $rest_url;
        
        return $this;
    }
    
    /**
     * Sets the base API URL
     * 
     * @param string $api_url
     * @return Request 
     */
    public function setApiUrl($api_url)
    {
        $this->api_url = $api_url;
        
        return $this;
    }
    
    /**
     * Sets the parameters
     * 
     * @param array $parameters
     * @return Request 
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
        
        return $this;
    }
    
    /**
     * Sets one parameter
     * 
     * @param string $key
     * @param mixed $value
     * @return Request 
     */
    public function setParameter($key, $value)
    {
        $this->parameters[$key] = $value;
        
        return $this;
    }
    
    /**
     * Sets the command
     * 
     * @param type $command one of Karotz::API_CMD_* values
     * @return Request 
     */
    public function setCommand($command)
    {   
        $this->command = $command;
        
        return $this;
    }
    
    /**
     * Gets the parameters
     * 
     * @return array 
     */
    public function getParameters()
    {
        return $this->parameters;
    }
    
    /**
     * Gets one parameter
     * 
     * @param string $key
     * @return mixed
     */
    public function getParameter($key)
    {
        if ($this->hasParameter($key))
            return $this->parameters[$key];
            
        return null;
    }
    
    /**
     * Tests if the request has this parameter
     * 
     * @param string $key
     * @return boolean 
     */
    public function hasParameter($key)
    {
        if (isset($this->parameters[$key]))
            return true;
        
        return false;
    }
    
    /**
     * Gets the command
     * 
     * @return string
     */
    public function getCommand()
    {
        return $this->command;
    }
    
    /**
     * Gets the base API URL
     * @return string
     */
    public function getApiUrl()
    {
        return $this->api_url;
    }
    
    /**
     * Makes the REST API URL from the base API URL
     * 
     * @return string
     */
    protected function _makeRestUrl()
    {
        $r = array(
            '{api_cmd}' => $this->command,
            '{params}' => http_build_query($this->parameters)
        );
        
        return str_replace(array_keys($r), array_values($r), $this->api_url);
    }
}