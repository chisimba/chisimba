<?php
require_once('feedrss_class_inc.php');
require_once('feedatom_class_inc.php');
require_once('feedentryrss_class_inc.php');
require_once('feedentryatom_class_inc.php');
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

class feeds extends object
{
	/**
	 * The feed class will make use of a number of abstract classes that will
	 * be able to parse and consume a number of RSS type formats,
	 * including:
	 * <pre>
	 * <li>RSS</li>
	 * <li>Atom</li>
	 * <li>Derivitives of the above</li>
	 * </pre>
	 *
	 * Feed provides functionality for consuming RSS and Atom feeds.
	 * It provides a natural syntax for accessing elements of feeds, feed attributes, and entry attributes.
	 * Feed also has extensive support for modifying feed and entry structure with the same natural syntax, and turning the result back into XML.
	 * In the future, this modification support could provide support for the Atom Publishing Protocol.
	 * Programmatically, Feed consists of a base Feed class, abstract abFeedAbstract and abFeedEntryAbstract base classes for representing
	 * Feeds and Entries, specific implementations of feeds and entries for RSS and Atom, and a behind-the-scenes helper for making the natural
	 * syntax magic work.
	 *
	 * This module will depend on another module called httpclient heavily to fetch the feeds
	 *
	 * @access public
	 * @copyright AVOIR
	 * @author Paul Scott
	 * @filesource
	 */

	/**
	 * The language object
	 *
	 * @var object $objLanguage
	 * @access public
	 */
	public $objLanguage;

	/**
     * HTTP client object to use for retrieving feeds
     *
     * @var abHttpClient
     * @access protected
     */
    protected static $_httpClient = null;

    /**
     * Override HTTP PUT and DELETE request methods?
     *
     * @var boolean
     * @access protected
     */
    protected static $_httpMethodOverride = false;

    /**
     * Namespaces array that we will be using.
     *
     * @var array
     * @access protected
     */
    protected static $_namespaces = array(
        'opensearch' => 'http://a9.com/-/spec/opensearchrss/1.0/',
        'atom' => 'http://www.w3.org/2005/Atom',
        'rss' => 'http://blogs.law.harvard.edu/tech/rss',
    );

	/**
	 * Standard engine init method to get the necessary objects for the module to function correctly
	 *
	 * @access public
	 * @param void
	 * @return void
	 */
	public function init()
	{
		//Load the language object to the public property $objLanguage
		$this->objLanguage = $this->getObject('language', 'language');
		//self::$_httpClient = $this->getObject('httpclients','httpclient');
		//$this->getObject('abfeed');
		//$this->getObject('feedrss');
	}

    /**
     * Set the HTTP client instance
     *
     * Sets the HTTP client object to use for retrieving the feeds.  If none
     * is set, the default httpclient will be used.
     *
     * @param abhttpclient $httpClient
     */
    public static function setHttpClient(abhttpclient $httpClient)
    {
        self::$_httpClient = $httpClient;
    }


    /**
     * Gets the HTTP client object.
     *
     * @return abhttpclient
     */
    public static function getHttpClient()
    {
    	if (!self::$_httpClient instanceof abhttpclient ) {
            self::$_httpClient = new httpclients();
        }
//var_dump(self::$_httpClient);
        return self::$_httpClient;
    }


    /**
     * Toggle using POST instead of PUT and DELETE HTTP methods
     *
     * Some feed implementations do not accept PUT and DELETE HTTP
     * methods, or they can't be used because of proxies or other
     * measures. This allows turning on using POST where PUT and
     * DELETE would normally be used; in addition, an
     * X-Method-Override header will be sent with a value of PUT or
     * DELETE as appropriate.
     *
     * @param boolean $override Whether to override PUT and DELETE.
     */
    public static function setHttpMethodOverride($override = true)
    {
        self::$_httpMethodOverride = $override;
    }


    /**
     * Get the HTTP override state
     *
     * @return boolean
     */
    public static function getHttpMethodOverride()
    {
        return self::$_httpMethodOverride;
    }


    /**
     * Get the full version of a namespace prefix
     *
     * Looks up a prefix (atom:, etc.) in the list of registered
     * namespaces and returns the full namespace URI if
     * available. Returns the prefix, unmodified, if it's not
     * registered.
     *
     * @return string
     */
    public static function lookupNamespace($prefix)
    {
        return isset(self::$_namespaces[$prefix]) ?
            self::$_namespaces[$prefix] :
            $prefix;
    }


    /**
     * Add a namespace and prefix to the registered list
     *
     * Takes a prefix and a full namespace URI and adds them to the
     * list of registered namespaces for use by
     * feed::lookupNamespace().
     *
     * @param string $prefix The namespace prefix
     * @param string $namespaceURI The full namespace URI
     */
    public static function registerNamespace($prefix, $namespaceURI)
    {
        self::$_namespaces[$prefix] = $namespaceURI;
    }


    /**
     * Imports a feed located at $uri.
     *
     * @param string $uri
     * @throws customException
     * @return abfeed
     */
    public static function import($uri)
    {
        $client = self::getHttpClient();
        $client->setUri($uri);
        $response = $client->get();
        if ($response->getStatus() !== 200) {
            throw new customException('Feed failed to load, got response code ' . $response->getStatus());
        }
        $feed = $response->getBody();
        return self::importString($feed);
    }


    /**
     * Imports a feed represented by $string.
     *
     * @param string $string
     * @throws customException
     * @return abfeed
     */
    public static function importString($string)
    {
    	// Load the feed as an XML DOMDocument object
        @ini_set('track_errors', 1);
        $doc = new DOMDocument();
        $success = @$doc->loadXML($string);
        @ini_restore('track_errors');

        if (!$success) {
            throw new customException("DOMDocument cannot parse XML: $php_errormsg");
        }

        // Try to find the base feed element or a single <entry> of an Atom feed
        if ($doc->getElementsByTagName('feed')->item(0) ||
            $doc->getElementsByTagName('entry')->item(0)) {
            //echo "Atom feed";
            	// return a newly created FeedAtom object
            return new feedatom(null, $string);
        }

        // Try to find the base feed element of an RSS feed
        if ($doc->getElementsByTagName('channel')->item(0)) {
            // return a newly created feedrss object
            return new feedrss(null, $string);
        }

        // $string does not appear to be a valid feed of the supported types
        throw new customException('Invalid or unsupported feed format');
    }


    /**
     * Imports a feed from a file located at $filename.
     *
     * @param string $uri
     * @throws customException
     * @return abfeed
     */
    public static function importFile($filename)
    {
        @ini_set('track_errors', 1);
        $feed = @file_get_contents($filename);
        @ini_restore('track_errors');
        if ($feed === false) {
            throw new customException("File could not be loaded: $php_errormsg");
        }
        return self::importString($feed);
    }


    /**
     * Attempts to find feeds at $uri referenced by <link ... /> tags. Returns an
     * array of the feeds referenced at $uri.
     *
     * @todo Allow findFeeds() to follow one, but only one, code 302.
     *
     * @param string $uri
     * @throws customException
     * @return array
     */
    public static function findFeeds($uri)
    {
        // Get the HTTP response from $uri and save the contents
        $client = self::getHttpClient();
        $client->setUri($uri);
        $response = $client->get();
        if ($response->getStatus() !== 200) {
            throw new customException("Failed to access $uri, got response code " . $response->getStatus());
        }
        $contents = $response->getBody();

        // Parse the contents for appropriate <link ... /> tags
        @ini_set('track_errors', 1);
        $pattern = '~(<link[^>]+)/?>~i';
        $result = @preg_match_all($pattern, $contents, $matches);
        @ini_restore('track_errors');
        if ($result === false) {
            throw new customException("Internal error: $php_errormsg");
        }

        // Try to fetch a feed for each link tag that appears to refer to a feed
        $feeds = array();
        if (isset($matches[1]) && count($matches[1]) > 0) {
            foreach ($matches[1] as $link) {
                $xml = @simplexml_load_string(rtrim($link, ' /') . ' />');
                if ($xml === false) {
                    continue;
                }
                $attributes = $xml->attributes();
                if (!isset($attributes['rel']) || !@preg_match('~^(?:alternate|service\.feed)~i', $attributes['rel'])) {
                    continue;
                }
                if (!isset($attributes['type']) ||
                        !@preg_match('~^application/(?:atom|rss|rdf)\+xml~', $attributes['type'])) {
                    continue;
                }
                if (!isset($attributes['href'])) {
                    continue;
                }
                try {
                    $feed = self::import($attributes['href']);
                } catch (Exception $e) {
                    continue;
                }
                $feeds[] = $feed;
            }
        }

        // Return the fetched feeds
        return $feeds;
    }


}//end class
?>