<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* Class to hold request information
* @author Nic Appleby 
* $Id: request_class_inc.php, v 1.0 2006/01/05 09:42:39
*/

class request
{
	
	/**
	* @var string $reqId The unique identifier for the request
	*/
	
	var $reqId;

	/**
	* @var string $modName The name of the assosciated module
	*/
	
	var $modName;
	
	/**
	* @var char $priority The priority of the icon either (y)esterday, (h)igh or (n)ormal
	*/
	
	var $priority;
	
	/**
	* @var char $type The type of icon, either (m)odule or (c)ommon
	*/
	
	var $type;
	
	/**
	* @var string $icon Php type A short description of the Php version icon
	*/
	
	var $Phpversion;
	
	/**
	* @var string $iconName The name of the icon
	*/
	
	var $iconName;
		
	/**
	* @var string $description A short description of the required icon
	*/
	
	var $description;
	
	/**
	* @var string $image A reference to an example image (if provided)
	*/
	
	var $uri1;
	
	/**
	* @var string $uri2 Another link to an example image online
	*/
	
	var $uri2;
	
	/**
	* @var int $complete The progress of the icon in percentage complete
	*/
	
	var $complete;
	
	/**
	* @var string $uploaded The name of the user the request belongs to
	*/
	
	var $uploaded;

	/**
	*constructor to initialise variables
	*/

	function request($reqId,$moduleName,$priority,$type,$Phpversion,$iconName,$description,$user,$url1,$url2)
	{
		$this->reqid = $reqId;
		$this->modname = $moduleName;
		$this->priority = $priority;
		$this->type = $type;
		$this->Phpversion = $Phpversion;
		$this->iconname = $iconName;
		$this->description = $description;
		$this->uri1 = $url1;
		$this->uri2 = $url2;
		$this->complete = 0;		// 0% complete upon creation
		$this->uploaded = $user;
	}
}
?>
