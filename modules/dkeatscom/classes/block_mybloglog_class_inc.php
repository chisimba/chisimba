<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* Mybloglog widget
*
* @author Derek Keats
*
*/
class block_mybloglog extends object
{
    public $title;
    public $blockType;

    /**
    * Constructor for the class
    */
    public function init()
    {
        $this->title="My blog log";
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
        return '<script src="http://pub.mybloglog.com/comm3.php?mblID=2008061712422423&amp;r=widget&amp;is=normal&amp;o=r&amp;ro=10&amp;cs=blue&amp;ww=175&amp;wc=single"></script>';
    }
}
?>