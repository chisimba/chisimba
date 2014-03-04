<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* Amazon search widget block
*
* @author Derek Keats
*
*/
class block_amazonsearch extends object
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
        return '<div style="clear: both"><iframe src="http://rcm.amazon.com/e/cm?t=dkeatscom-20' .
                '&o=1&p=27&l=qs1&f=ifr" width="180" height="150" ' .
                'frameborder="0" scrolling="no"></iframe><br /></div><br />';
    }
}
?>