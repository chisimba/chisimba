<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* Facebook friend me widget
*
* @author Derek Keats
*
*/
class block_fbaddme extends object
{
    public $title;
    public $blockType;

    /**
    * Constructor for the class
    */
    public function init()
    {
        $this->title="Friend me on Facebook";
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
        $icon = $this->getResourceUri("fb-friend-icon.gif", "dkeatscom");
        $icon = "<img src=\"$icon\" alt=\"Friend me on Facebook\" border=\"0\" style=\"vertical-align:middle\">";
        $objLink = $this->getObject("link", "htmlelements");
        $objLink->href = "http://www.facebook.com/addfriend.php?id=812410106";
        $objLink->title = "Friend me on Facebook";
        $objLink->link = $icon . " <span class=\"minute\">Add me on Facebook</span>";
        return $objLink->show() . '<br />';
    }
}
?>