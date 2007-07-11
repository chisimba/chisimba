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
* $Id$
*
*/
class block_gettinghelp extends object
{
    var $title;
    
    /**
    * Constructor for the class
    */
    function init()
    {
        //Create an instance of the help object
        $this->objHelp=& $this->getObject('helplink','help');
 		//Create an instance of the language object
        $this->objLanguage =& $this->getObject('language','language');
        //Set the title
        $this->title=$this->objLanguage->languageText("mod_postlogin_helptitle",'postlogin');
    }
    
    /**
    * Method to output a block with information on how help works
    */
    function show()
	{
        //Add the text tot he output
        $ret = $this->objLanguage->languageText("mod_postlogin_helphowto",'postlogin');
        //Create an instance of the help object
        $objHelp = & $this->getObject('helplink','help');
        //Add the help link to the output
        $ret .= "&nbsp;".$this->objHelp->show('mod_postlogin_helphowto','postlogin');
        return $ret;
    }
}
?>