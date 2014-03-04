<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* Icecast wrapper.
* @author Jeremy O'Connor , Jameel Sauls
* $Id: controller.php 4398 2006-10-16 07:50:36Z jameel $
*/
class icecast extends controller
{
    public $objUser;
    public $objHelp;

   public function init()
    {
        $this->objUser =& $this->getObject('user', 'security');
        $this->objHelp=& $this->getObject('helplink','help');
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
        $objHelp->rootModule="helloworld";
    }
    
    public function dispatch($action=Null)
    {
        // 1. ignore action at moment as we only do one thing - say hello
        // 2. load the data object (calls the magical getObject which finds the
        //    appropriate file, includes it, and either instantiates the object,
        //    or returns the existing instance if there is one. In this case we
        //    are not actually getting a data object, just a helper to the 
        //    controller.
        // 3. Pass variables to the template
        $this->setVarByRef('objUser', $this->objUser);
        $this->setVarByRef('objHelp', $this->objHelp);
        //$this->setVar('suppressFooter', True); # uncomment this line to suppress footer
        // return the name of the template to use  because it is a page content template
        // the file must live in the templates/content subdir of the module directory
        return "main_tpl.php";
    }
}    
?>
