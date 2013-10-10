<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 * httpClient provides an easy interface with which to perform HTTP requests.
 * httpClient is able to perform GET, POST, PUT and DELETE requests.
 * httpClient follows up to 5 HTTP redirections by default.
 * To change this behavior, pass the maximum number of allowed redirections to the get() method.
 *
 * @access public
 * @copyright AVOIR
 * @author Paul Scott
 */
require_once('aburi_class_inc.php');

abstract class abhttpclient
{
	/**
     * Socket Connection Timeout
     *
     * @var int Time in Seconds
     */
    protected $_timeout = 10;

    /**
     * The URI we are accessing.
     *
     * @var Zend_Uri
     */
    protected $_uri = null;

    /**
     * Additional HTTP headers to send.
     *
     * @var array
     */
    protected $_headers = array();

    /**
     * Validates that $headers is an array of strings, where each string
     * is formed like "Field: value".  An exception is thrown on failure.
     * An empty $headers array is valid and will not throw an exception.
     *
     * @param array $headers
     * @throws customException
     * @return void
     */
    final static public function validateHeaders($headers = array()) {
        // Validate headers
        if (!is_array($headers)) {
            throw new customException('Headers must be supplied as an array');
        } else {
            foreach ($headers as $header) {
                if (!is_string($header)) {
                    throw new customException('Illegal header supplied; header must be a string');
                } else if (!strpos($header, ': ')) {
                	/**
                	 * @todo should protect against injections by making sure one and only one header is here
                	 */
                    throw new customException('Bad header.  Headers must be formatted as "Field: value"');
                }
            }
        }
    }


    /**
     * Class Constructor, create and validate URI object
     *
     * @param  string|Uri|null $uri
     * @param  array $headers
     * @return void
     */
    abstract public function __construct($uri = null, $headers = array());


    /**
     * Sets the URI of the remote site.  Setting a new URI will automatically
     * clear the response properties.
     *
     * @param string|Uri $uri
     * @return void
     */
    final public function setUri($uri) {
        // Accept a Uri object or decompose a URI string into a Uri.
        if ($uri instanceof aburi) {
            $this->_uri = $uri;
        } else {
            // $uri string will be validated automatically by Uri.
            $this->_uri = aburi::factory($uri);
        }

        // Explicitly set the port if it's not already.
        if (!$this->_uri->getPort() && $this->_uri->getScheme() == 'https') {
            $this->_uri->setPort(443);
        } else if (!$this->_uri->getPort()) {
            $this->_uri->setPort(80);
        }
    }


    /**
     * Get the Uri for this URI.
     *
     * @throws customException
     * @return Uri
     */
    final public function getUri() {
        if (!$this->_uri instanceof aburi) {
            throw new customException('URI was never set with setUri()');
        }
        return $this->_uri;
    }


    /**
     * Set the $headers to send.  Headers are supplied as an array of strings,
     * where each string is a header formatted like "Field: value".
     *
     * @param array $headers
     * @return void
     */
    final public function setHeaders($headers=array()) {
        self::validateHeaders($headers);
        $this->_headers = $headers;
    }


    /**
     * Set Connection Timeout
     *
     * @param int $seconds Timeout in seconds
     * @return void
     */
    final public function setTimeout($seconds)
    {
        if (ctype_digit((string) $seconds)) {
            $this->_timeout = $seconds;
        } else {
            throw new customException("Invalid Timeout. The timeout should be a numerical value in seconds");
        }
    }


    /**
     * Send a GET HTTP Request
     *
     * @return Http_Response
     */
    abstract public function get();


    /**
     * Send a POST HTTP Request
     *
     * @param string $data Data to send in the request
     * @return Http_Response
     */
    abstract public function post($data);


    /**
     * Send a PUT HTTP Request
     *
     * @param string $data Data to send in the request
     * @return Http_Response
     */
    abstract public function put($data);


    /**
     * Send a DELETE HTTP Request
     *
     * @return Http_Response
     */
    abstract public function delete();
}
?>