<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* The class provides a hello world block to demonstrate
* how to use blockalicious
*
* @author Derek Keats
*
*/
class widget_errorwidget extends object
{
    /**
    * Constructor for the class
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
    * Method to output a Tweet block
    */
    public function show()
	{
        return "<span class=\"error\">The specified widget could not be found</span>";
    }

    private function getWidget()
    {
        return '';
    }
}
?>