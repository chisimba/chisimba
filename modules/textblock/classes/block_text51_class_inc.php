<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* The class provides a hello world block to demonstrate
* how to use blockalicious
*
* @author Derek Keats
*
*/

$this->loadClass("textblockbase", "textblock");

class block_text51 extends textblockbase
{
    
    /**
    * Constructor for the class
    */
    function init()
    {
    	parent::init();
		$this->setData("text51");
    }
    
    /**
    * Method to output a block with text content
    */
    function show()
	{
        return $this->blockContents;
    }
}
?>