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
class block_amazonkindle extends object
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
        return '<div style="clear: both"><iframe src="http://rcm.amazon.com/e/cm?t=dkeatscom-20&' .
                'o=1&p=8&l=as1&asins=B000FI73MA&fc1=000000&IS2=1&lt1=' .
                '_blank&lc1=0000FF&bc1=000000&bg1=FFFFFF&f=ifr" ' .
                'style="width:120px;height:240px;" scrolling="no" ' .
                'marginwidth="0" marginheight="0" frameborder="0">' .
                '</iframe></div><br />';
    }
}
?>