<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Indexer class extends object
 * This class gets and creates document properties that are used in the index.
 * It is used for the filesystem stored documents not dbTable derived actions
 *
 * @author Paul Scott
 * @copyright AVOIR UWC
 * @package lucene
 */

class doc extends object
{
	/**
	 * The config object
	 *
	 * @var object configuration
	 */
	public $objConfig;

    /**
     * Init function
     * Standard initialisation method for the Chisimba framework
     *
     * @param void
     * @return void
     * @access public
     */
	public function init()
    {
		//load up the config object
    	$this->objConfig = $this->getObject('altconfig','config');
    }

	/**
      * Method to generate a URL
      *
      * @access public
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
      * @access public
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