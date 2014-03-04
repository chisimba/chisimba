<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* The class provides a shelfari block
*
* @author Derek Keats
*
*/
class block_shelfari extends object
{
    public $title;
    public $blockType;

    /**
    * Constructor for the class
    */
    public function init()
    {
        $this->title="";
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
        return '<div style="clear: both"><embed width="206" height="300" ' .
                'src="http://www.shelfari.com/ws/shelf.swf" ' .
                'wmode="transparent" FlashVars="UserName=dkeats&ShelfType=' .
                'list&verE=s1.5&ListType=isread&booksize=small&' .
                'AmazonAssociate=dkeatscom-20&Alpha=0&BGColor=FFFFFF">' .
                '</embed> <a href="http://www.amazon.com?%5Fencoding=UTF8&tag=dkeatscom-20">' .
                'Purchase books<br />from Amazon</a></div>';
    }
}
?>