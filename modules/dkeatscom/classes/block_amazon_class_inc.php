<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* Amazon widget block
*
* @author Derek Keats
*
*/
class block_amazon extends object
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
        return '<div style="clear: both"><SCRIPT charset="utf-8" type="text/javascript" ' .
                'src="http://ws.amazon.com/widgets/q?ServiceVersion=' .
                '20070822&MarketPlace=US&ID=V20070822/US/dkeatscom-20/8006/' .
                '54263f20-de60-40eb-9f12-dbe38a4a8b1c"> </SCRIPT> <NOSCRIPT>' .
                '<A HREF="http://ws.amazon.com/widgets/q?ServiceVersion=' .
                '20070822&MarketPlace=US&ID=V20070822%2FUS%2Fdkeatscom-' .
                '20%2F8006%2F54263f20-de60-40eb-9f12-dbe38a4a8b1c&Operation=' .
                'NoScript">Amazon.com Widgets</A></NOSCRIPT></div>';
    }
}
?>