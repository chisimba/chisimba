<?php
/* -------------------- helloworld class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* Controller for translate module. This controller is for
* testing purpose only. It does not provide a functional module.
*
* @author derek Keats
* @author Jameel Sauls
* $Id: controller.php 5301 2007-01-10 08:30:51Z wnitsckie $
*
*/
class dictionary extends controller
{

    /**
    *
    * @var object $objLanguage String to hold the language object
    *
    */
    public $objLanguage;

    /**
    *
    * @var object $objBab String to hold the dictionary lookup object
    *
    */
    public $objDict;


    /**
    *
    * @var string $action The action parameter from the querystring
    *
    */
  //  pubic $action;

    /**
    *
    * Standard init class to instantiate the core objects and grab
    * the action parameter.
    *
    */
    public function init()
    {
        //Retrieve the action parameter from the querystring
        $this->action = $this->getParam('action', Null);
        //Create an instance of the language object
        $this->objLanguage = &$this->getObject("language", "language");
        //Create an instance of the bablefish object
        $this->objDict = & $this->getObject('diclookup');
    }

    /**
    *
    * Standard dispatch method of controller
    *
    */
    public function dispatch($action)
    {
        $this->setLayoutTemplate('layout_tpl.php');
        switch ($this->action) {
            //Default to view and display view template
            case null:
            case 'lookup':
                $word = $this->getParam('word', NULL);
                if ($word!=NULL) {
                    $str = "<table width=\"600\"><tr><td valign=\"top\"><b>" . $word
                      . "</b>:&nbsp;</td><td>"
                      . $this->objDict->lookup($word)
                      . "</td></tr></table>";
                } else {
                    $objDicInt = & $this->getObject('dicinterface');
                    $objDicInt->set('format', 'horizontal');
                    $str = "<br /><br />" . $objDicInt->makeSearch() . "<br /><br />";
                }
                $this->setVarByRef('str', $str);
                return 'dump_tpl.php';
                break;
            default:
                $this->setVar('str', $this->objLanguage->languageText("phrase_actionunknown").": ".$this->action);
                return 'dump_tpl.php';
                break;
        }
    }

}
?>
