<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* Facebook Chisimba group widget block
*
* @author Derek Keats
*
*/
class block_fbchisimba extends object
{
    public $title;
    public $blockType;

    /**
    * Constructor for the class
    */
    public function init()
    {
        $this->title="Chisimba Facebook group";
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
        $icon = $this->getResourceUri("fb-group-icon.gif", "dkeatscom");
        $icon = "<img src=\"$icon\" alt=\"Chisimba Facebook group\" border=\"0\" style=\"vertical-align:middle\">";
        $objLink = $this->getObject("link", "htmlelements");
        $objLink->href = "http://www.facebook.com/group.php?gid=14068945606";
        $objLink->title = "Chisimba Facebook group";
        $objLink->link = $icon . " <span class=\"minute\">Chisimba Facebook group</span>";
        return $objLink->show() . '<br />';
    }
}
?>