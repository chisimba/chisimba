<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* Follow me on Twitter widget block
*
* @author Derek Keats
*
*/
class block_twitterfollowme extends object
{
    public $title;
    public $blockType;

    /**
    * Constructor for the class
    */
    public function init()
    {
        $this->title="Follow me on Twitter";
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
        $icon = $this->getResourceUri("twitter-icon.gif", "dkeatscom");
        $icon = "<img src=\"$icon\" alt=\"Follow me on Twitter\" border=\"0\" style=\"vertical-align:middle\">";
        $objLink = $this->getObject("link", "htmlelements");
        $objLink->href = "http://www.twitter.com/dkeats";
        $objLink->title = "Follow me on Twitter";
        $objLink->link = $icon . " <span class=\"minute\">Follow me on Twitter</span>";
        return $objLink->show() . '<br />';
    }
}
?>