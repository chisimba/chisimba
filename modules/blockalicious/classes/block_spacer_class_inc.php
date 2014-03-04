<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* Spacer widget block. There are four spacers named Spacer-Spacer4
*
* @author Derek Keats
*
*/
class block_spacer extends object
{
    public $title;
    public $blockType;

    /**
    * Constructor for the class
    */
    public function init()
    {
        $this->title="Spacer";
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
    *  Thinking about blocks as widgets. The
    *  show method fetches the widget.
    *
    */
    private function getWidget()
    {
        return  '<br />&nbsp;<br />';
    }
}
?>