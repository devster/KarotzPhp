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

use Karotz\Transport\TransportInterface;
use Karotz\Transport\FileTransport;

/**
 * A class to interact with the Karotz REST API
 * 
 * @author Jeremy perret <jeremy@devster.org>
 */
class Karotz
{
    protected
        $api_base_url            = 'http://api.karotz.com/api/karotz/{api_cmd}?{params}',
        $default_lang            = 'EN',
        $allowed_lang            = array('FR', 'EN', 'ES', 'DE')
    ;    
    
    CONST API_CMD_START          = 'start';
    CONST API_CMD_INTERACTIVMODE = 'interactivemode';
    CONST API_CMD_EARS           = 'ears';
    CONST API_CMD_ASR            = 'asr';
    CONST API_CMD_LED            = 'led';
    CONST API_CMD_TTS            = 'tts';
    CONST API_CMD_MULTIMEDIA     = 'multimedia';
    CONST API_CMD_WEBCAM         = 'webcam';
    CONST API_CMD_CONFIG         = 'config';
    
    protected
        $interactive_id,
        $install_id,
        $api_key,
        $secret_key,
        $transport,
        $last_request
    ;
    
    /**
     * Constructor
     * 
     * @param string $interactive_id
     * @param TransportInterface $transport
     * @param string $lang Default lang
     */
    public function __construct($interactive_id = null, TransportInterface $transport = null, $lang = null)
    {   
        if (! is_null($lang))
            $this->setLang($lang);
            
        if (! is_null($interactive_id))
            $this->interactive_id = $interactive_id;
            
        if (! is_null($transport))
            $this->transport = $transport;
    }
    
    /**
     * Start a session with the Karotz
     * 
     * Used to get the interactiveID
     * 
     * @param  string $install_id
     * @param  string $api_key
     * @param  string $secret_key
     * @return Response
     */
    public function start($install_id = null, $api_key = null, $secret_key = null)
    {
        if (! is_null($this->interactive_id))
            throw \Exception(sprintf("An interactiveID is already set (%s), you don't need to start a new session", $this->interactive_id));
        
        if (! is_null($install_id)) $this->install_id = $install_id;
        if (! is_null($api_key)) $this->api_key = $api_key;
        if (! is_null($secret_key)) $this->secret_key = $secret_key;
        
        if (is_null($this->install_id) || is_null($this->api_key) || is_null($this->secret_key))
            throw \Exception("The start method requires install_id, api_key and secret_key");
        
        $params = array(
            'apikey' => $this->api_key,
            'once' => uniqid(),
            'timestamp' => time(),
            'installid' => $this->install_id,
        );
        ksort($params);
        
        $signature = base64_encode(hash_hmac('sha1', http_build_query($params), $this->secret_key, true));
        $params['signature'] = $signature;
        
        return $this->sendCmd(self::API_CMD_START, $params, true);
    }
    
    /**
     * Stop the session with the Karotz
     * 
     * @return Response
     */
    public function stop()
    {
        $response = $this->sendCmd(self::API_CMD_INTERACTIVMODE, array('action' => 'stop'));
        $this->interactive_id = null;
        
        return $response;
    }
    
    /**
     * Move the ears of the Karotz
     * 
     * @param integer $left
     * @param integer $right
     * @param boolean $relative
     * @return Response 
     */
    public function ears($left = null, $right = null, $relative = null)
    {
        $params = array(
            'left' => $left,
            'right' => $right,
            'relative' => $relative ? 'true' : 'false',
        );
        return $this->sendCmd(self::API_CMD_EARS, $params);
    }
    
    /**
     * Reset the ears position of the Karotz
     * 
     * @return Response
     */
    public function earsReset()
    {
        return $this->sendCmd(self::API_CMD_EARS, array('reset' => 'true'));
    }
    
    /**
     * Set the grammar for the Automatic Speech Recognition (ASR)
     * 
     * @param mixed $grammar
     * @param string $lang
     * @return Response
     */
    public function asr($grammar, $lang = null)
    {
        //grammar terms must be separed by a comma
        if (! is_array($grammar))
            $grammar = array($grammar);
        
        $grammar = array_map(function ($str) {
            return str_replace(',', ' ', $str);
        }, $grammar);
        
        $params = array(
            'grammar' => implode(',', $grammar),
            'lang' => $this->getLang($lang),
        );
        
        return $this->sendCmd(self::API_CMD_ASR, $params);
    }
    
    /**
     * Makes flash the led of the Karotz
     * 
     * @param string $color
     * @param integer $period
     * @param integer $pulse
     * @return Response 
     */
    public function ledPulse($color, $period = 500, $pulse = 500)
    {
        $this->_validateColorString($color);
        
        $params = array(
            'action' => 'pulse',
            'color' => strtoupper($color),
            'period' => (int) $period,
            'pulse' => (int) $pulse,
        );
        
        return $this->sendCmd(self::API_CMD_LED, $params);
    }
    
    /**
     * Switch on the led with fading effect
     * 
     * @param string $color
     * @param integer $period
     * @return Response 
     */
    public function ledFade($color, $period = 500)
    {
        $this->_validateColorString($color);
        
        $params = array(
            'action' => 'fade',
            'color' => strtoupper($color),
            'period' => (int) $period,
        );
        
        return $this->sendCmd(self::API_CMD_LED, $params);
    }
    
    /**
     * Switch on the led
     * 
     * @param string $color
     * @return Response
     */
    public function ledLight($color)
    {
        $this->_validateColorString($color);
        
        $params = array(
            'action' => 'light',
            'color' => strtoupper($color),
        );
        
        return $this->sendCmd(self::API_CMD_LED, $params);
    }
    
    /**
     * Switch off the led
     * 
     * @return Response
     */
    public function ledOff()
    {
        return $this->sendCmd(self::API_CMD_LED, array('action' => 'light'));
    }
    
    /**
     * Text to read to the Karotz
     * 
     * @param string $text
     * @param string $lang
     * @return Response
     */
    public function tts($text, $lang = null)
    {       
        $params = array(
            'action' => 'speak',
            'lang' => $this->getLang($lang),
            'text' => $text,
        );
        
        return $this->sendCmd(self::API_CMD_TTS, $params);
    }
    
    /**
     * Stop the speaking
     * 
     * @return Response
     */
    public function ttsStop()
    {
        return $this->sendCmd(self::API_CMD_TTS, array('action' => 'stop'));
    }
    
    /**
     * Takes a photo with the webcam Karotz
     * 
     * And POST it to the callback url
     * 
     * @param string $callback_url
     * @return Response
     */
    public function photo($callback_url)
    {
        if (! filter_var($callback_url, FILTER_VALIDATE_URL))
            throw \Exception(sprintf("Callback URL for the photo command has wrong format (%s)", $callback_url));
            
        $params = array(
            'action' => 'photo',
            'url' => $callback_url,
        );
        
        return $this->sendCmd(self::API_CMD_WEBCAM, $params);
    }
    
    /**
     * Gets the video stream as an URL
     * 
     * No request to the API is performed
     * 
     * @return string
     */
    public function getVideoStream()
    {
        if (is_null($this->interactive_id))
            throw new \Exception("The interactiveID can not be empty");
        
        $params = array(
            'action' => 'video',
            'interactiveid' => $this->interactive_id
        );
        
        $request = new Request($this->api_base_url, self::API_CMD_WEBCAM, $params);
        return $request->getRestUrl();
    }
    
    /**
     * Gets the Karotz configuration
     * 
     * @return Response
     */
    public function config()
    {
        return $this->sendCmd(self::API_CMD_CONFIG, array());
    }
    
    /**
     * Sets the secret key
     * 
     * @param string $secret_key
     * @return Karotz 
     */
    public function setSecretKey($secret_key)
    {
        $this->secret_key = $secret_key;
        
        return $this;
    }
    
    /**
     * Gets the secret key
     * 
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secret_key;
    }
    
    /**
     * Sets the API key
     * 
     * @param string $api_key
     * @return Karotz 
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;
        
        return $this;
    }
    
    /**
     * Gets the API key
     * 
     * @return string
     */
    public function getApiKey()
    {
        $this->api_key;
    }
    
    /**
     * Sets the install ID
     * 
     * @param string $install_id
     * @return Karotz 
     */
    public function setInstallId($install_id)
    {
        $this->install_id = $install_id;
        
        return $this;
    }
    
    /**
     * Gets the install ID
     * @return string
     */
    public function getInstallId()
    {
        return $this->install_id;
    }
    
    /**
     * Sets the interactive ID
     * 
     * @param string $interactive_id
     * @return Karotz 
     */
    public function setInteractiveId($interactive_id)
    {
        $this->interactive_id = $interactive_id;
        
        return $this;
    }
    
    /**
     * Gets the interactive ID
     * 
     * @return string
     */
    public function getInteractiveId()
    {
        return $this->interactive_id;
    }
    
    /**
     * Sets the default language
     * 
     * @param string $lang
     * @return Karotz 
     */
    public function setLang($lang)
    {
        $this->_validateLanguage($lang);
        $this->default_lang = strtoupper($lang);
        
        return $this;
    }
    
    /**
     * Gets the validated language passed in parameter or the default one
     * 
     * @param string $lang
     * @return string
     */
    public function getLang($lang = null)
    {
        if (! is_null($lang))
            $lang = $this->_validateLanguage($lang) ? strtoupper($lang) : $this->default_lang;
        else
            $lang = $this->default_lang;
            
        return $lang;
    }
    
    /**
     * Gets the allowed languages by the API
     * 
     * @return array
     */
    public function getAllowedLang()
    {
        return $this->allowed_lang;
    }
    
    /**
     * Sets the API url
     * 
     * Normally, the default is very suitable
     * 
     * @param string $api_url
     * @return Karotz 
     */
    public function setApiUrl($api_url)
    {
        $this->api_base_url = $api_url;
        
        return $this;
    }
    
    /**
     * Gets the API url
     * @return string
     */
    public function getApiUrl()
    {
        return $this->api_base_url;
    }
    
    /**
     * Sets the transport
     * 
     * The transport is the way to query the Karotz REST API
     * 
     * @param TransportInterface $transport
     * @return Karotz 
     */
    public function setTransport(TransportInterface $transport)
    {
        $this->transport = $transport;
        
        return $this;
    }
    
    /**
     * Gets the transport
     * 
     * @return TransportInterface
     */
    public function getTransport()
    {
        return $this->transport;
    }
    
    /**
     * Performs the last request made to the API
     * 
     * @return Response
     */
    public function replay()
    {
        if (is_null($this->last_request))
            throw new \Exception("A command should be performed at least once before you can use the replay function. noob.");
        
        return $this->sendRequest($this->last_request);
    }
    
    /**
     * Makes a request and sends it
     * 
     * @param string $api_cmd one of self::API_CMD_* values
     * @param array $params
     * @param boolean $no_interactive_id
     * @return Response
     */
    public function sendCmd($api_cmd, array $params, $no_interactive_id = false)
    {
        if (is_null($this->interactive_id) && ! $no_interactive_id)
            throw new \Exception("The interactiveID can not be empty");
        
        $request = new Request($this->api_base_url, $api_cmd, $params);
        
        if (! $no_interactive_id)
            $request->setParameter('interactiveid', $this->interactive_id);
        
        return $this->sendRequest($request);
    }
    
    /**
     * Sends a request object
     * 
     * @param Request $request
     * @return Response 
     */
    public function sendRequest(Request $request)
    {
        if (is_null($this->transport))
            $this->transport = new FileTransport();
        
        $ret = $this->transport->sendRequest($request);
        
        $this->last_request = $request;
        
        $response = new Response($ret, $request);
       
        if (! is_null($response->getInteractiveId()))
            $this->interactive_id = $response->getInteractiveId();
       
       return $response;
    }
    
    /**
     * Validates a 6 Hexa string
     * 
     * @param string $color 
     */
    protected function _validateColorString($color)
    {
        if (! preg_match('/^[a-f0-9]{6}$/i', $color))
            throw new \LogicException(sprintf("Error color format `%s`, color must be a 6 Hexa string. ex: FF0000", $color));
    }
    
    /**
     * Validates language
     * 
     * @param string $lang
     * @return true 
     */
    protected function _validateLanguage($lang)
    {
        $lang = strtoupper($lang);
        
        if (! in_array($lang, $this->allowed_lang))
            throw new \LogicException(sprintf("The language `%s` is not allowed, only %s", $lang, implode(', ', $this->allowed_lang)));
        
        return true;
    }
}