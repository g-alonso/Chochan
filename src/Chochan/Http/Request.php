<?php

/**
* This file is part of Chochan framework
* 
* @author    Gabriel Alonso <gbr.alonso@gmail.com>
* @copyright 2014
* @license   WTFPL - http://www.wtfpl.net/txt/copying/
* 
*/

namespace Chochan\Http;

/**
 * Request class
 * 
 * Represents a HTTP request
*/
class Request
{
    /**
     * The URI which was given in order to access this page;
     * 
     * @var string
     * 
     */
    protected $uri;

    /**
     * Contains the current script's path. 
     * This is useful for pages which need to point to themselves. 
     * The __FILE__ constant contains the 
     * full path and filename of the current (i.e. included) file.
     * 
     * @var string
     * 
     */
    protected $base;

    /**
     * Request Method. GET,POST,PUT,DELETE
     * 
     * @var string
     * 
     */
    protected $method;

    /**
     * The address of the page (if any) which referred the user agent to the current page. 
     * This is set by the user agent. Not all user agents will set this, 
     * and some provide the ability to modify HTTP_REFERER as a feature. 
     * 
     * In short, it cannot really be trusted.
     * 
     * @var string
     * 
     */
    protected $referrer;

    /**
     * The IP address from which the user is viewing the current page.
     * 
     * @var string
     * 
     */
    protected $ip;

    /**
     * If the request is an ajax request
     * 
     * @var bool
     * 
     */
    protected $ajax;

    /**
     * Name and revision of the information protocol via which the page was requested. 
     * Default 'HTTP/1.1';
     * 
     * @var string
     * 
     */
    protected $scheme;

    /**
     * Contents of the User-Agent: header from the current request, 
     * if there is one. This is a string denoting the user agent being which is accessing the page. 
     * A typical example is: Mozilla/4.5 [en] (X11; U; Linux 2.2.9 i586). 
     * Among other things, you can use this value with get_browser() to 
     * tailor your page's output to the capabilities of the user agent.
     * 
     * @var string
     * 
     */
    protected $user_agent;

    /**
     * Has the raw data from the request body
     * 
     * @var string
     * 
     */
    protected $body;

    /**
     * Content type
     * 
     * @todo investigate
     * @var string
     * 
     */
    protected $type;

    /**
     * Content length
     * 
     * @todo investigate
     * @var int
     * 
     */
    protected $length;

    /**
     * An associative array of variables passed to the current script via the URL parameters.
     * 
     * @var array
     * 
     */
    protected $query;

    /**
     * An associative array of variables passed to the current script via the HTTP POST method.
     * 
     * @var array
     * 
     */
    protected $data;

    /**
     * An associative array of variables passed to the current script via HTTP Cookies.
     * 
     * @var array
     * 
     */
    protected $cookies;

    /**
     * An associative array of items uploaded to the current script via the HTTP POST method.
     * 
     * @var array
     * 
     */
    protected $files;

    /**
     * HTTPS?
     * 
     * @var bool
     * 
     */
    protected $secure;

    /**
     * Contents of the Accept: header from the current request, if there is one.
     * 
     * @var string
     * 
     */
    protected $accept;

    /**
     * Has proxy ip
     * 
     * @var string
     * 
     */
    protected $proxy_ip;

    /**
     * Constructor
     * 
     * @param array config optional config
     * 
     */
    public function __construct($config = array())
    {
        if (count($config) == 0) {
            
            $config = array(
                'uri' => $this->getServerValue('REQUEST_URI', '/'),
                'base' => str_replace(array('\\',' '), array('/','%20'), dirname($this->getServerValue('SCRIPT_NAME'))),
                'method' => $this->resolveMethod(),
                'referrer' => $this->getServerValue('HTTP_REFERER'),
                'ip' => $this->getServerValue('REMOTE_ADDR'),
                'ajax' => $this->getServerValue('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest',
                'scheme' => $this->getServerValue('SERVER_PROTOCOL', 'HTTP/1.1'),
                'user_agent' => $this->getServerValue('HTTP_USER_AGENT'),
                'body' => file_get_contents('php://input'),
                'type' => $this->getServerValue('CONTENT_TYPE'),
                'length' => $this->getServerValue('CONTENT_LENGTH', 0),
                'query' => $_GET,
                'data' => $_POST,
                'cookies' => $_COOKIE,
                'files' => $_FILES,
                'secure' => $this->getServerValue('HTTPS', 'off') != 'off',
                'accept' => $this->getServerValue('HTTP_ACCEPT'),
                'proxy_ip' => $this->getProxyIpAddress()
            );
        }

        $this->init($config);
    }

    /**
     * Gets a variable from $_SERVER using $default if not provided.
     *
     * @param string $var variable name
     * @param string $default default value to substitute
     * @return string Server variable value
     */
    private function getServerValue($var, $default = '')
    {
        return isset($_SERVER[$var]) ? $_SERVER[$var] : $default;
    }

    /**
     * Gets the request method.
     *
     * @return string
     */
    private function resolveMethod()
    {
        if (isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
            return $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'];
        } elseif (isset($_REQUEST['_method'])) {
            return $_REQUEST['_method'];
        }

        return $this->getServerValue('REQUEST_METHOD', 'GET');
    }

    /**
     * Returns if the requests is ajax
     * 
     * @return bool
     */
    public function isAjax()
    {
        return $this->ajax;
    }


    /**
     * Returns if the requests is ajax
     * 
     * @return bool
    */
    public function getMethod()
    {
        return $this->method;
    }

    /**
	 * Get the uri request 
	 * 
	 * @return string
	*/
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Gets the real IP address.
     *
     * @return string IP address
     */
    public function getProxyIpAddress()
    {
        static $forwarded = array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED'
        );

        $flags = \FILTER_FLAG_NO_PRIV_RANGE | \FILTER_FLAG_NO_RES_RANGE;

        foreach ($forwarded as $key) {
            if (array_key_exists($key, $_SERVER)) {
                sscanf($_SERVER[$key], '%[^,]', $ip);
                if (filter_var($ip, \FILTER_VALIDATE_IP, $flags) !== false) {
                    return $ip;
                }
            }
        }

        return null;
    }

    /**
     * Initialize request properties.
     *
     * @param array $properties array of request properties
     */
    public function init($properties = array())
    {
        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
    }
}
