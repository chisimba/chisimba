<?php
require_once('urihttp_class_inc.php');
abstract class aburi
{
	/**
	 * Scheme of this URI (http, ftp, etc.)
	 * @var string
	 */
	protected $_scheme = '';

    /**
     * Return a string representation of this URI.
     *
     * @see     getUri()
     * @return  string
     */
    public function __toString()
    {
        return $this->getUri();
    }

    /**
     * Convenience function, checks that a $uri string is well-formed
     * by validating it but not returning an object.  Returns TRUE if
     * $uri is a well-formed URI, or FALSE otherwise.
     *
     * @param string $uri
     * @return boolean
     */
    static public function check($uri)
    {
        try {
            $uri = self::factory($uri);
        } catch (Exception $e) {
            return false;
        }

        return $uri->valid();
    }


    /**
     * Create a new uri object for a URI.  If building a new URI, then $uri should contain
     * only the scheme (http, ftp, etc).  Otherwise, supply $uri with the complete URI.
     *
     * @param string $uri
     * @throws customException
     * @return uri
     */
    static public function factory($uri = 'http')
    {
        /**
         * Separate the scheme from the scheme-specific parts
         * @link http://www.faqs.org/rfcs/rfc2396.html
         */
        $uri = explode(':', $uri, 2);
        $scheme = strtolower($uri[0]);
        $schemeSpecific = isset($uri[1]) ? $uri[1] : '';

        if (!strlen($scheme)) {
            throw new customException('An empty string was supplied for the scheme');
        }

        // Security check: $scheme is used to load a class file, so only alphanumerics are allowed.
        if (!ctype_alnum($scheme)) {
            throw new customException('Illegal scheme supplied, only alphanumeric characters are permitted');
        }


        /**
         * Create a new uri object for the $uri. If a subclass of uri exists for the
         * scheme, return an instance of that class. Otherwise, a customException is thrown.
         */
        switch ($scheme) {

            case 'mailto':
                // fall through to next case
            case 'http':
                // fall through to next case
            case 'https':
                if ($scheme == 'https' or $scheme == 'http') {
                    $className = 'urihttp';
                } else {
                    $className = 'uri' . ucfirst($scheme);
                }
                return new $className($scheme, $schemeSpecific);
            default:
				throw new customException("Scheme \"$scheme\" is not supported");
        }

    }

    public function getScheme()
    {
    	if (!empty($this->_scheme)) {
    		return $this->_scheme;
    	} else {
    		return false;
    	}
    }


    /******************************************************************************
     * Abstract Methods
     *****************************************************************************/


    /**
     * uri and its subclasses cannot be instantiated directly.
     * Use uri::factory() to return a new uri object.
     */
    abstract protected function __construct($scheme, $schemeSpecific = '');


    /**
     * Return a string representation of this URI.
     *
     * @return string
     */
    abstract public function getUri();


    /**
     * Returns TRUE if this URI is valid, or FALSE otherwise.
     *
     * @return boolean
     */
    abstract public function valid();

}
?>