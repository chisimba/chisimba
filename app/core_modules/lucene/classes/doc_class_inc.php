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

    public function init()
    {

    }

	/**
      * Method to generate a URL
      *
      * @access private
      * @param void
      * @return url
      */
     private function generateUrl()
     {

     }

     /**
      * Method to gather the document properties
      * Will this be stored in a db? In metadata? in DC tbl?
      *
      * @param $prop
      * @return mixed $docproperties
      */
     private function getProperty($prop)
     {
     	switch ($prop)
     	{
     		case '':
     			return NULL;

     		case 'createdBy':
     			return $createdBy;

     		case 'body':
     			$body = file_get_contents($file);
     	}

     }
}
?>