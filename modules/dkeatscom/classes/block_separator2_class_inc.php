<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* Separator widget block
*
* @author Derek Keats
*
*/
class block_separator2 extends object
{
    public $title;
    public $blockType;

    /**
    * Constructor for the class
    */
    public function init()
    {
        $this->title="Separator2";
        $this->blockType = "none";
    }

    /**
    * Method to output block
    */
    public function show()
	{
        return $this->getWidget();
    }

    private function getWidget()
    {
        return  '<br /><br />';
    }
}
?>