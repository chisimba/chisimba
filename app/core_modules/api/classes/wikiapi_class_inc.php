<?php

/**
 * Wiki interface class
 * 
 * XML-RPC (Remote Procedure call) class
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * Wiki XML-RPC Class
 * 
 * Class to provide Chisimba Packages XML-RPC functionality
 * 
 * @category  Chisimba
 * @package   api
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class wikiapi extends object
{
	
	public $objDbWiki;

	/**
     * init method
     * 
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init()
	{
		try {
			$this->objConfig = $this->getObject('altconfig', 'config');
			$this->objLanguage = $this->getObject('language', 'language');
        	$this->objUser = $this->getObject('user', 'security');
        	$this->objDbWiki = $this->getObject('dbwiki','wiki');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	/**
	 * Method to return the RPC Version supported
	 * 
	 * Returns 1 with this version of the JSPWiki API
	 * 
	 * @return integer
	 * @access public
	 */
	public function getRPCVersionSupported()
	{
		$val = new XML_RPC_Value(1, 'int');
		return new XML_RPC_Response($val);
	}
	
	/**
	 * Get list of changed pages since timestamp, which should be in UTC. 
	 * 
	 * The result is an array, where each element is a struct:
     * name (string) : Name of the page. The name is UTF-8 with URL encoding to make it ASCII.
     * lastModified (date) : Date of last modification, in UTC.
     * author (string) : Name of the author (if available). Again, name is UTF-8 with URL encoding.
     * version (int) : Current version. 
     * A page MAY be specified multiple times. A page MAY NOT be specified multiple times with the same modification date. 
     * 
     * @access public
     * @param xmlrpc params
     * @return object
	 */
	public function getRecentChanges($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$timestamp = $param->scalarval();
    	
    	$date = date('Y-m-d H:i:s', $timestamp);
    	// grab the data from the wiki database
    	$ret = $this->objDbWiki->getRecentlyUpdatedAPI($date);
    	if(!empty($ret))
    	{
    		foreach($ret as $recs)
    		{
    			$pgstruct[] = new XML_RPC_Value(array(
    			"name" => new XML_RPC_Value($recs['page_name'], "string"),
    			"lastModified" => new XML_RPC_Value($recs['date_created'], "string"),
    			"author" => new XML_RPC_Value($this->objUser->userName($recs['page_author_id']), "string"),
    			"version" => new XML_RPC_Value($recs['page_version'], "string"),), "struct");
    		}
    	}
    	// log_debug($pgstruct); die();
    	return new XML_RPC_Response($pgstruct);
	}
	
	/**
	 * Get the raw Wiki text of page, latest version. 
	 * 
	 * Page name must be UTF-8, with URL encoding. 
	 * Returned value is a binary object, with UTF-8 encoded page data.
	 *
	 * @param string $params
	 * @return string
	 */
	public function getPage($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pagename = $param->scalarval();
    	
    	$data = $this->objDbWiki->getPage($pagename);
    	$val = $data[0]['page_content'];
    	// send it back as a base64 encoded value
    	$val2send = new XML_RPC_Value($val, "base64");
    	return new XML_RPC_Response($val2send);
	}
	
	/**
	 * Get the raw Wiki text of page, any version. 
	 * 
	 * Page name must be UTF-8, with URL encoding. 
	 * Returned value is a binary object, with UTF-8 encoded page data.
	 *
	 * @param string $params
	 * @return string
	 */
	public function getPageVersion($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pagename = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$ver = $param->scalarval();
    	
    	$data = $this->objDbWiki->getPage($pagename, $ver);
    	
    	$val = $data[0]['page_content'];
    	// send it back as a base64 encoded value
    	$val2send = new XML_RPC_Value($val, "base64");
    	return new XML_RPC_Response($val2send);
	}
	
	/**
	 * Return page in rendered HTML. Returns UTF-8, expects UTF-8 with URL encoding.
	 *
	 * @param string $params
	 * @return string
	 */
	public function getPageHTML($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pagename = $param->scalarval();
    	
    	$data = $this->objDbWiki->getPage($pagename);
    	$val = $data[0]['page_content'];
    	// load up the wiki parser
    	$objParser = $this->getObject('wikitextparser', 'wiki');
    	$val = $objParser->transform($val);
    	// send it back as a base64 encoded value
    	$val2send = new XML_RPC_Value($val, "base64");
    	return new XML_RPC_Response($val2send);
	}
	
	/**
	 * Return page in rendered HTML as per a supplied version. Returns UTF-8, expects UTF-8 with URL encoding.
	 *
	 * @param string $params
	 * @return string
	 */
	public function getPageHTMLVersion($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pagename = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$ver = $param->scalarval();
    	
    	$data = $this->objDbWiki->getPage($pagename, $ver);
    	$val = $data[0]['page_content'];
    	// load up the wiki parser
    	$objParser = $this->getObject('wikitextparser', 'wiki');
    	$val = $objParser->transform($val);
    	// send it back as a base64 encoded value
    	$val2send = new XML_RPC_Value($val, "base64");
    	return new XML_RPC_Response($val2send);
	}
	
	/**
	 * Returns a list of all pages. 
	 * 
	 * The result is an array of strings, again UTF-8 in URL encoding.
	 *
	 * @return string
	 */
	public function getAllPages()
	{    	
    	$data = $this->objDbWiki->getAllPages();
    	$val = new XML_RPC_Value($data, "array");
    	return new XML_RPC_Response($val);
	}
	
	/**
	 * return all the info about a page
	 * 
	 * returns a struct with elements
     * name (string): the canonical page name, URL-encoded UTF-8.
     * lastModified (date): Last modification date, UTC.
     * author (string): author name, URL-encoded UTF-8.
     * version (int): current version 
	 *
	 * @param string $params
	 * @return string
	 */
	public function getPageInfo($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pagename = $param->scalarval();
    	
    	$data = $this->objDbWiki->getPage($pagename);
    	
    	$val = new XML_RPC_Value(array(
    			"name" => new XML_RPC_Value($data['page_name'], "string"),
    			"lastModified" => new XML_RPC_Value($data['date_created'], "string"),
    			"author" => new XML_RPC_Value($this->objUser->userName($data['page_author_id']), "string"),
    			"version" => new XML_RPC_Value($data['page_version'], "string"),), "struct");
    	// send it back as a struct encoded value
    	return new XML_RPC_Response($val);
	}
	
	/**
	 * return all the info about a page with version
	 * 
	 * returns a struct with elements
     * name (string): the canonical page name, URL-encoded UTF-8.
     * lastModified (date): Last modification date, UTC.
     * author (string): author name, URL-encoded UTF-8.
     * version (int): current version 
	 *
	 * @param string $params
	 * @return string
	 */
	public function getPageInfoVersion($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pagename = $param->scalarval();
    	
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$ver = $param->scalarval();
    	
    	$data = $this->objDbWiki->getPage($pagename, $ver);
    	
    	$val = new XML_RPC_Value(array(
    			"name" => new XML_RPC_Value($data['page_name'], "string"),
    			"lastModified" => new XML_RPC_Value($data['date_created'], "string"),
    			"author" => new XML_RPC_Value($this->objUser->userName($data['page_author_id']), "string"),
    			"version" => new XML_RPC_Value($data['page_version'], "string"),), "struct");
    	// send it back as a struct encoded value
    	return new XML_RPC_Response($val);
	}
	
	/**
	 * Lists all links for a given page. The returned array contains structs, with the following elements:
	 * 
     * page (string) : The page name or URL the link is to.
     * type (string) : The link type. This is a string, with the following possible values:
          o &amp;amp;amp;amp;amp;amp;quot;external&amp;amp;amp;amp;amp;amp;quot; : The link is an external hyperlink
          o &amp;amp;amp;amp;amp;amp;quot;local&amp;amp;amp;amp;amp;amp;quot; : This is a local Wiki name for hyperlinking 
     * href (string) : The HREF the link points to. Useful for finding this link within the HTML of this page. 
	 *
	 * @param string $params
	 * @return string
	 */
	public function listLinks($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pagename = $param->scalarval();
    	    	
    	$data = $this->objDbWiki->getLinks();
    	
    	$val = new XML_RPC_Value($data, "array");
    	// send it back as a struct encoded value
    	return new XML_RPC_Response($val);
	}
}
?>