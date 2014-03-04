<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* The class provides a block that displays a google ad in
* a block in the manner of normal blocks.
*
* @author Derek Keats
*
*/
class block_googlead extends object
{
    public $title;

    /**
    * Constructor for the class
    */
    public function init()
    {
        //Set the title
        $objLanguage = $this->getObject('language', 'language');
        $this->title = $objLanguage->languageText('mod_googleadsense_advert', 'googleadsense');
    }

    /**
    * Method to output a block with the Google ad in it
    */
    public function show()
	{
        $objGad = $this->getObject('buildad', 'googleadsense');
        $objGad->setupByType("banner");
        return $objGad->show();
    }
}
?>