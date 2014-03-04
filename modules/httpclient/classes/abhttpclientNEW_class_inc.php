<?php
require_once('aburi_class_inc.php');

abstract class abhttpclient
{
    /**
     * HTTP protocol versions
     */
    const HTTP_VER_1 = 1.1;
    const HTTP_VER_0 = 1.0;

    /**
     * HTTP request methods
     */
    const METHOD_GET     = 'GET';
    const METHOD_POST    = 'POST';
    const METHOD_PUT     = 'PUT';
    const METHOD_HEAD    = 'HEAD';
    const METHOD_DELETE  = 'DELETE';
    const METHOD_TRACE   = 'TRACE';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_CONNECT = 'CONNECT';

    /**
     * POST data encoding methods
     */
    const ENC_URLENCODED = 'application/x-www-form-urlencoded';
    const ENC_FORMDATA   = 'multipart/form-data';

    /**
     * The user agent string that identifies the HTTP client
     *
     * @var string
     */
    protected $user_agent = null;

    /**
     * Request URI
     *
     * @var urihttp
     */
    protected $uri;

    /**
     * Request timeout in seconds
     *
     * @var int
     */
    protected $timeout = 10;

    /**
     * Associative array of request headers
     *
     * @var array
     */
    protected $headers = array();

    /**
     * Request HTTP version (1.0 or 1.1)
     *
     * @var float
     */
    protected $http_version = self::HTTP_VER_1;

    /**
     * HTTP request method
     *
     * @var string
     */
    protected $method = self::METHOD_GET;

    /**
     * Associative array of GET parameters
     *
     * @var array
     */
    protected $paramsGet = array();

    /**
     * Assiciative array of POST parameters
     *
     * @var array
     */
    protected $paramsPost = array();

    /**
     * Request body content type (for POST requests)
     *
     * @var string
     */
    protected $enctype = null;

    /**
     * The raw post data to send. Could be set by setRawPostData($data, $enctype).
     *
     * @var string
     */
    protected $raw_post_data = null;

    /**
     * The last HTTP request sent by the client, as string
     *
     * @var string
     */
    protected $last_request = null;

    /**
     * Contructor method. Will create a new HTTP client. Accepts the target
     * URL and optionally and array of headers.
     *
     * @param uriHttp|string $uri
     * @param array $headers Optional request headers to set
     */
    public function __construct($uri = null, $headers = null)
    {
        if (! is_null($uri)) $this->setUri($uri);
        if (! is_null($headers)) $this->setHeaders($headers);
        $this->user_agent = 'PHP/' . PHP_VERSION . ' Chisimba Framework/1.0.0';
    }

    /**
     * Set the URI for the next request
     *
     * @param Uri_Http|string $uri
     */
    public function setUri($uri)
    {
        if (is_string($uri) && urihttp::check($uri)) {
            $uri = urihttp::factory($uri);
        }

        if ($uri instanceof urihttp) {
            // We have no ports, set the defaults
            if (! $uri->getPort()) {
                $uri->setPort(($uri->getScheme() == 'https' ? 443 : 80));
            }

            $this->uri = $uri;
        } else {
            throw new customException('Passed parameter is not a valid HTTP URI.');
        }
    }

    /**
     * Get the URI for the next request
     *
     * @param boolean $as_string If true, will return the URI as a string
     * @return urihttp|string
     */
    public function getUri($as_string = false)
    {
        if ($as_string && $this->uri instanceof urihttp) {
            return $this->uri->__toString();
        } else {
            return $this->uri;
        }
    }

    /**
     * Set the user agent identification string
     *
     * @param string $ua
     */
    public function setUserAgent($ua) {
        $this->user_agent = $ua;
    }

    /**
     * Set the client's connection timeout in seconds, 0 for none
     *
     * @param int $timeout
     */
    public function setTimeout($timeout = 10)
    {
        $this->timeout = $timeout;
    }

    /**
     * Set the next request's method
     *
     * Validated the passed method and sets it. If we have files set for
     * POST requests, and the new method is not POST, the files are silently
     * dropped.
     *
     * @param string $method
     */
    public function setMethod($method = self::METHOD_GET)
    {
        $method = strtoupper($method);

        if (! defined('self::METHOD_' . $method)) {
            throw new customException("'{$method}' is not a valid HTTP request method.");
        }

        if ($method == self::METHOD_POST && is_null($this->enctype)) {
            $this->setEncType(self::ENC_URLENCODED);
        }

        $this->method = $method;
    }

    /**
     * Get the currently-set request method (GET, POST, etc.)
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set or unset a request header field
     *
     * The function validates the header, and sets it. If $override is false,
     * and the header already exists, another value will be added to the same
     * header.
     *
     * If $value is null, name is considered a string of the format
     * "Header: value", which will be split to get the header name
     * and value.
     *
     * If the value is still null or false after trying to split $name on
     * the ':' character (IE if $name does not contain ':'), the header will
     * be unset.
     *
     * @param string $name Header name or entire header string
     * @param string $value Header value or null
     * @param boolean $override Whether to rewrite the header if it is already
     *        set, or add another similar header
     */
    public function setHeader($name, $value = null, $override = true)
    {
        // Check if $name needs to be split
        if (is_null($value) && (strpos($name, ':') > 0))
            list($name, $value) = explode(':', $name, 2);

        // Make sure name is valid
        if (! preg_match('/^[A-Za-z0-9-]+$/', $name)) {
            throw new customException("{$name} is not a valid HTTP header name");
        }

        // Header names are storred lowercase internally.
        $name = strtolower($name);

        // If $value is null or false, unset the header
        if (is_null($value) || $value === false) {
        	unset($this->headers[$name]);

        // Else, set the header
        } else {
        	$value = trim($value);

            // If override is set, set the header as is
            if ($override || ! isset($this->headers[$name])) {
                $this->headers[$name] = $value;

            // Else, if the header already exists, add a new value
            } else {
                if (! is_array($this->headers[$name]))
                        $this->headers[$name] = array($this->headers[$name]);

                $this->headers[$name][] = $value;
            }
        }
    }

    /**
     * Get the value of a specific header
     *
     * Note that if the header has more than one value, an array
     * will be returned.
     *
     * @param unknown_type $key
     * @return string|array|null The header value or null if it is not set
     */
    public function getHeader($key)
    {
        if (isset($this->headers[$key])) {
            return $this->headers[$key];
        } else {
            return null;
        }
    }

    /**
     * Set a GET parameter for the request. Wrapper around _setParameter
     *
     * @param string $name
     * @param string $value
     * @param boolean $override Whether to overwrite the parameter's value
     */
    public function setParameterGet($name, $value, $override = true)
    {
        $this->_setParameter('GET', $name, $value, $override);
    }

    /**
     * Set a POST parameter for the request. Wrapper around _setParameter
     *
     * @param string $name
     * @param string $value
     * @param boolean $override Whether to overwrite the parameter's value
     */
    public function setParameterPost($name, $value, $override = true)
    {
        $this->_setParameter('POST', $name, $value, $override);
    }

    /**
     * Clear all GET and POST parameters
     *
     * Should be used to reset the request parameters if the client is
     * used for several concurrent requests.
     */
    public function resetParameters()
    {
        // Reset parameter data
        $this->paramsGet = array();
        $this->paramsPost = array();
        $this->raw_post_data = null;

        // Clear outdated headers
        if (isset($this->headers['content-type'])) unset($this->headers['content-type']);
        if (isset($this->headers['content-length'])) unset($this->headers['content-length']);
    }

    /**
     * Set a cookie parameter
     *
     * @param Zend_Http_Cookie|string $name
     * @param string|null $value If "cookie" is a string, this is the cookie value.
     */
    public function setCookie($cookie, $value = null)
    {
        if ($cookie instanceof Zend_Http_Cookie) {
            $cookie = $cookie->getName();
            $value = $cookie->getValue();
        }

        if (preg_match("/[=,; \t\r\n\013\014]/", $cookie))
            throw new customException("Cookie name cannot contain these characters: =,; \t\r\n\013\014 ({$name})");

        $value = urlencode($value);

        if (isset($this->headers['cookie'])) {
            $this->headers['cookie'] .= "; {$cookie}={$value}";
        } else {
            $this->setHeader('cookie', "{$cookie}={$value}");
        }
    }

    /**
     * Set the encoding type for POST data
     *
     * @param string $enctype
     */
    public function setEncType($enctype = self::ENC_URLENCODED)
    {
        $this->enctype = $enctype;
    }

    /**
     * Set the raw (already encoded) POST data.
     *
     * This function is here for two reasons:
     * 1. For advanced user who would like to set their own data, already encoded
     * 2. For backwards compatibilty: If someone uses the old post($data) method.
     *    this method will be used to set the encoded data.
     *
     * @param string $data
     * @param string $enctype
     */
    public function setRawData($data, $enctype = null)
    {
        $this->raw_post_data = $data;
        $this->setEncType($enctype);
    }

    /**
     * Set the next request's headers.
     *
     * Receives an array of headers, which can be eithr an associative array of
     * the form "Header" => "value" (eg. "Host" => "www.example.com") or a
     * numbered array of string, each of the format "Header: value".
     *
     * @param array $headers
     */
    public function setHeaders($headers=array())
    {
        // Make sure we got the proper data
        if (is_array($headers)) {
            foreach ($headers as $name => $value) {
                if (is_string($name)) {
                    $this->setHeader($name, $value);
                } else {
                    $this->setHeader($value);
                }
            }
        }
        else
            throw new customException("Parameter must be an array of header lines");
    }

    /**
     * Get the last HTTP request as string
     *
     * @return string
     */
    public function getLastRequest() {
        return $this->last_request;
    }

    /**
     * Send the HTTP request and return a response
     *
     * @param string $method
     * @return Zend_Http_Response
     */
    public function request($method = null)
    {
        if (! $this->uri instanceof urihttp)
            throw new customException("No valid URI has been passed to the client");

        if ($method) $this->setMethod($method);

        // Prepare the request string
        $body = $this->_prepare_body();
        $headers = $this->_prepare_headers();
        $request = $headers . "\r\n" . $body;

        // Open the connection, send the request and read the response
        $sock = $this->_connect();
        $this->_write($sock, $request);
        $response = $this->_read($sock);

        $this->last_request = $request;

        return httpresponse::factory($response);
    }

    /**
     * Validate an array of headers.
     *
     * Accepts either an associative array of Header name => Header value
     * format, or a numbered array where all elements are string of the
     * format "Header: value".
     *
     * @param array $headers
     * @return bool
     * @throws Zend_Http_Client_Exception
     */
    static public function validateHeaders($headers = array())
    {
        // Make sure we got the proper data
        if (is_array($headers)) {
            foreach ($headers as $name => $value) {
                // If this is not an associative array, split the string
                if (! is_string($name))
                    list($name, $value) = explode(":", $value, 1);

                $value = trim($value);

                // Make sure the header is valid
                if (! preg_match("/^[a-zA-Z-]+$/", $name))
                    return false;
            }
        }
        else
            throw new customException("Parameter must be an array of header lines.");

        return true;
    }

    /**
     * Set a GET or POST parameter - used by SetParameterGet and SetParameterPost
     *
     * @param string $type GET or POST
     * @param string $name
     * @param string $value
     * @param boolean $override Whether to replace old value, or add it as an array of values
     */
    protected function _setParameter($type, $name, $value, $override = true)
    {
        $type = strtolower($type);
        switch ($type) {
            case 'get':
                $parray = &$this->paramsGet;
                break;
            case 'post':
                $parray = &$this->paramsPost;
                break;
            default:
                throw new customException("Trying to set unknown parameter type: '{$type}'");
        }

        if ($override || (! isset($parray[$name]))) {
            $parray[$name] = $value;
        } elseif (isset($parray[$name])) {
            if (! is_array($parray[$name])) {
                $parray[$name] = array($parray[$name]);
            }
            $parray[$name][] = $value;
        }
    }

    /**
     * Prepare the request headers
     *
     * @abstract
     * @return string
     */
    abstract protected function _prepare_headers();

    /**
     * Prepare the request body (for POST and PUT requests)
     *
     * @abstract
     * @return string
     */
    abstract protected function _prepare_body();

    /**
     * Open a connection to the remote server
     *
     * @abstract
     * @return resource Socket
     */
    abstract protected function _connect();

    /**
     * Send request to the remote server
     *
     * @abstract
     * @param resource $socket Socket (returned by _connect())
     * @param string $request Request to send
     */
    abstract protected function _write($socket, $request);

    /**
     * Read response from remote server
     *
     * @abstract
     * @param resource $socket Socket (returned by _connect())
     * @return string
     */
    abstract protected function _read($socket);

    // ------------------------------------------------------------------------
    // Deprecated methods
    // ------------------------------------------------------------------------

    /**
     * Send a GET request
     *
     * @return Zend_Http_Response
     * @deprecated Please use request('GET') instead
     */
    public function get()
    {
        $this->setMethod(self::METHOD_GET);
        return $this->request();
    }

    /**
     * Send a POST request
     *
     * @param string $data Data to send
     * @return Zend_Http_Response
     * @deprecated Please use request('POST') instead
     */
    public function post($data = null)
    {
        $this->setMethod(self::METHOD_POST);
        $this->setRawData($data);
        return $this->request();
    }

    /**
     * Send a PUT request
     *
     * @param string $data Data to send
     * @return Zend_Http_Response
     * @deprecated Please use request('PUT') instead
     */
    public function put($data = null)
    {
        $this->setMethod(self::METHOD_PUT);
        $this->setRawData($data);
        return $this->request();
    }

    /**
     * Send a DELETE request
     *
     * @return Zend_Http_Response
     * @deprecated Please use request('DELETE') instead
     */
    public function delete()
    {
        $this->setMethod(self::METHOD_DELETE);
        return $this->request();
    }
}
?>