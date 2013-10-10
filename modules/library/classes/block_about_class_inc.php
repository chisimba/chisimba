<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* The class that demonstrates how to use blocks
*
* @author Derek Keats

* 
* $Id: block_about_class_inc.php 11428 2008-11-10 21:31:08Z charlvn $
*
*/
class block_about extends object
{
    var $title;
    
    function init()
    {
        $this->objLanguage = & $this->getObject('language', 'language');
        $this->title=$this->objLanguage->languageText("mod_library_expltitle",'library');
    }
    
    function show()
	{
		return $this->objLanguage->languageText("mod_library_explain",'library');
    }
}