<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* 
* Controller class for Chisimba for the module googleanalytics
*
* @author Derek Keats
* @package googleanalytics
*
*/
class googleanalytics extends controller
{    
    /**
    * @var $objLanguage String object property for holding the 
    * language object
    */
    var $objLanguage;

    /**
     * Intialiser for the stories controller
     *
     * @param byref $ string $engine the engine object
     */
    public function init()
    {
    }
    
    
    /**
     * 
     * The standard dispatch method for the googleanalytics module.
     * 
     */
    public function dispatch()
    {
    	$objCr = $this->getObject("createanalytic", "googleanalytics");
    	$str = $objCr->show();
    	$str=nl2br(htmlspecialchars($str));
        $this->setVarByRef("str", $str);
        /*
        * Return the template determined by the method resulting 
        * from action
        */
        return "dump_tpl.php";
    }
}
?>