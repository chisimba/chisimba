<?php

/**
 * Chisimba Web Present interface class
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
 * Chisimba Web Present XML-RPC Class
 * 
 * Class to provide Chisimba Presentation XML-RPC functionality
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
class webpresentapi extends object
{
	
	public $objDbTags;

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
        	$this->objDbTags = $this->getObject('dbwebpresenttags','webpresent');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	/**
	 * Method to return all tags in the system
	 * 
	 * Returns an array of structs of all the tags
	 * 
	 * @return array
	 * @access public
	 */
	public function getAllTagsAPI()
	{
		$data = $this->objDbTags->getAllTags();
		//log_debug($data);
		if(!empty($data))
		{
			foreach($data as $recs)
			{
				$tagstruct[] = new XML_RPC_Value(array(
    				"tag" => new XML_RPC_Value($recs['tag'], "string"),
    				"tagcount" => new XML_RPC_Value($recs['tagcount'], "string"),
    				), "struct");
			}
			
		}
		else {
			$tagstruct = new XML_RPC_Value(array(), "array");
		}
		return new XML_RPC_Response(new XML_RPC_Value($tagstruct, "array"));
	}
	
	/**
	 * Method to return a well formatted tag cloud in HTML
	 *
	 * @return unknown
	 */
	public function getTagCloudAPI()
	{
		$data = $this->objDbTags->getTagCloud();
		$val = new XML_RPC_Value($data, "string");
		return new XML_RPC_Response($val);
	}
	
	/**
	 * Method to return all tags associated with a particular file id
	 *
	 * @param object $params
	 * @return array
	 */
	public function getTagsPerFileAPI($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$fileid = $param->scalarval();
    	
    	$data = $this->objDbTags->getTags($fileid);
    	if(!empty($data))
    	{
    		foreach($data as $tags)
    		{
    			$tagarr[] = new XML_RPC_Value($tags['tag'], "string");
    		}
    	}
    	else {
    		$tagarr = new XML_RPC_Value(array(), "array");
    	}
    	
    	return new XML_RPC_Response(new XML_RPC_Value($tagarr, "array"));
	}
	
	/**
	 * Method to return a list (array) of files tagged with a certain keyword
	 *
	 * @param object $params
	 * @return array
	 */
	public function getFilesPerTagAPI($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$tag = $param->scalarval();
    	
    	$data = $this->objDbTags->getFilesWithTag($tag);
    	if(!empty($data))
    	{
    		foreach($data as $files)
    		{
    			$filearr[] = new XML_RPC_Value($files['id'], "string");
    		}
    	}
    	else {
    		$filearr = new XML_RPC_Value(array(), "array");
    	}
    	return new XML_RPC_Response(new XML_RPC_Value($filearr, "array"));
		
	}
	
	
}
?>