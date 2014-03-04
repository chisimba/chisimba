<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

class feed extends object
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
	}

}//end class
?>