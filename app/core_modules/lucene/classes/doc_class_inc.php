<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Indexer class extends object
 */

class doc extends object
{
	public $objConfig;

    public function init()
    {
		$this->objConfig = $this->getObject('altconfig','config');
    }

	/**
      * Method to generate a URL
      *
      * @access private
      * @param void
      * @return url
      */
     public function generateUrl($filename)
     {
     	//generate a url for the document/file
     	$sroot = $this->objConfig->getSiteRoot();
     	$spath = $this->objConfig->getSiteRootPath();

     	//so we have sroot as the url up to the place where index.php is and
     	//the siterootpath which is /var/www/whatever
     	//now we replace the siterootpath bit with the sroot to form a url
     	$url = str_replace($spath, $sroot, $filename);
     	return $url;

     }

     /**
      * Method to gather the document properties
      * Will this be stored in a db? In metadata? in DC tbl?
      *
      * @param $prop
      * @return mixed $docproperties
      */
     public function getProperty($prop, $filename)
     {
     	switch ($prop)
     	{
     		case '':
     			return NULL;

     		case 'createdBy':
     			return $createdBy;

     		case 'body':
     			$body = file_get_contents($filename);
     			return $body;
     	}

     }
}
?>