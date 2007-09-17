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
    public $objDbFiles;
    public $objDbSlides;
    
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
        	$this->objDbFiles = $this->getObject('dbwebpresentfiles','webpresent');
        	$this->objDbSlides = $this->getObject('dbwebpresentslides','webpresent');
        	
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
    	//log_debug($filearr);
    	return new XML_RPC_Response(new XML_RPC_Value($filearr, "array"));
		
	}
	
	public function getFileAPI($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$fileid = $param->scalarval();
    	
    	$files = $this->objDbFiles->getFile($fileid);
    	// log_debug($files);
    	if(!empty($files))
    	{
    			$fStruct[] = new XML_RPC_Value(array(
    			"id" => new XML_RPC_Value($files['id'], "string"),
    			"processstage" => new XML_RPC_Value($files['processstage'], "string"),
    			"inprocess" => new XML_RPC_Value($files['inprocess'], "string"),
    			"filename" => new XML_RPC_Value($files['filename'], "string"),
    			"mimetype" => new XML_RPC_Value($files['mimetype'], "string"),
    			"title" => new XML_RPC_Value($files['title'], "string"),
    			"description" => new XML_RPC_Value($files['description'], "string"),
    			"filetype" => new XML_RPC_Value($files['filetype'], "string"),
    			"cclicense" => new XML_RPC_Value($files['cclicense'], "string"),
    			"creatorid" => new XML_RPC_Value($files['creatorid'], "string"),
    			"dateuploaded" => new XML_RPC_Value($files['dateuploaded'], "string"),
    			), "struct");
	    	
    		//$arrofStructs = new XML_RPC_Value(array($myStruct), "array");
    		//log_debug($catStruct);
    		return new XML_RPC_Response(new XML_RPC_Value($fStruct, "struct"));
    	}
    	else {
    		$filearr = new XML_RPC_Value(array(), "struct");
    		//log_debug($filearr);
    		return new XML_RPC_Response(new XML_RPC_Value($filearr, "array"));
    	}
	}
	
	
	public function getLatestAPI()
	{
		$data = $this->objDbFiles->getLatestPresentations();
		
		if(!empty($data))
    	{
    		foreach($data as $files)
    		{
    			$fStruct[] = new XML_RPC_Value(array(
    			"id" => new XML_RPC_Value($files['id'], "string"),
    			"processstage" => new XML_RPC_Value($files['processstage'], "string"),
    			"inprocess" => new XML_RPC_Value($files['inprocess'], "string"),
    			"filename" => new XML_RPC_Value($files['filename'], "string"),
    			"mimetype" => new XML_RPC_Value($files['mimetype'], "string"),
    			"title" => new XML_RPC_Value($files['title'], "string"),
    			"description" => new XML_RPC_Value($files['description'], "string"),
    			"filetype" => new XML_RPC_Value($files['filetype'], "string"),
    			"cclicense" => new XML_RPC_Value($files['cclicense'], "string"),
    			"creatorid" => new XML_RPC_Value($files['creatorid'], "string"),
    			"dateuploaded" => new XML_RPC_Value($files['dateuploaded'], "string"),
    			), "struct");
    		}
    		return new XML_RPC_Response(new XML_RPC_Value($fStruct, "struct"));
    	}
    	else {
    		$filearr = new XML_RPC_Value(array(), "struct");
    		return new XML_RPC_Response(new XML_RPC_Value($filearr, "array"));
    	}
	}
	
	public function getByUserAPI($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$userid = $param->scalarval();
    	
    	$data = $this->objDbFiles->getByUser($userid);
    	if(!empty($data))
    	{
    		foreach($data as $files)
    		{
    			$fStruct[] = new XML_RPC_Value(array(
    			"id" => new XML_RPC_Value($files['id'], "string"),
    			"processstage" => new XML_RPC_Value($files['processstage'], "string"),
    			"inprocess" => new XML_RPC_Value($files['inprocess'], "string"),
    			"filename" => new XML_RPC_Value($files['filename'], "string"),
    			"mimetype" => new XML_RPC_Value($files['mimetype'], "string"),
    			"title" => new XML_RPC_Value($files['title'], "string"),
    			"description" => new XML_RPC_Value($files['description'], "string"),
    			"filetype" => new XML_RPC_Value($files['filetype'], "string"),
    			"cclicense" => new XML_RPC_Value($files['cclicense'], "string"),
    			"creatorid" => new XML_RPC_Value($files['creatorid'], "string"),
    			"dateuploaded" => new XML_RPC_Value($files['dateuploaded'], "string"),
    			), "struct");
    		}
    		return new XML_RPC_Response(new XML_RPC_Value($fStruct, "struct"));
    	}
    	else {
    		$filearr = new XML_RPC_Value(array(), "struct");
    		return new XML_RPC_Response(new XML_RPC_Value($filearr, "array"));
    	}
	}
	
	public function getThumbnailAPI($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
    	
    	$data = $this->objDbFiles->getPresentationThumbnail($id);
    	if(!empty($data))
    	{
    		return new XML_RPC_Response(new XML_RPC_Value($data, 'string'));
    	}
    	else {
    		return new XML_RPC_Response(new XML_RPC_Value("no data", 'string'));
    	}
		
	}
	
	public function getNumSlidesAPI($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
		
    	$data = $this->objDbSlides->getNumSlides($id);
    	
    	return new XML_RPC_Response(new XML_RPC_Value($data, 'int'));
	}
	
	public function getSlidesAPI($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
		
    	$data = $this->objDbSlides->getSlides($id);
    	log_debug($data);
    	return new XML_RPC_Response(new XML_RPC_Value("not yet implemented - sorry!", 'string'));
	}
	
	public function getSlideThumbnailAPI($params)
	{
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
    	
    	$data = $this->objDbSlides->getSlideThumbnail($id);
    	
		return new XML_RPC_Response(new XML_RPC_Value($data, 'string'));
	}
}
?>