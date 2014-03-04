<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* Display middle area personal blocks
*
* @author Derek Keats
*
*/
class block_pbmiddle extends object
{
    public $title;
    public $blockType;

    /**
    * Constructor for the class
    */
    public function init()
    {
        $this->title="Personal blocks: middle";
        $this->blockType = "none";
    }

    /**
    * Method to output block
    */
    public function show()
	{
        return $this->getWidget();
    }
    
    /**
    *  
    * Retrieve the block
    * @return string The rendered block
    * @access private
    * 
    */
    private function getWidget()
    {
        // Instantiate the rendering class
        $objPbrender = $this->getObject("pbrender", "personalblocks");
        return $objPbrender->renderMiddle();
    }
}
?>