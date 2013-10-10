<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* 
* Controller class for Chisimba for the modulegravatar
*
* @author _AUTHORNAME
* @package _MODULECODE
*
*/
class gravatar extends controller
{
    
    /**
    * @var $objConfig String object property for holding the 
    * configuration object
    */
    public $objConfig;
    
    /**
    * @var $objLanguage String object property for holding the 
    * language object
    */
    public $objLanguage;
    /**
    * @var $objLog String object property for holding the 
    * logger object for logging user activity
    */
    public $objLog;

    /**
     * Intialiser for the stories controller
     *
     * @param byref $ string $engine the engine object
     */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
		
    }
    
    
    /**
     * 
     * The standard dispatch method for the _MODULECODE module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch()
    {
    	$objavatar = $this->getObject("getgravatar", "gravatar");
    	//Test with working data
    	$str = "Testing with known user: <br />" 
    	  . $objavatar->show("dkeats@uwc.ac.za") 
    	  . "<br />for dkeats@uwc.ac.za<br />" 
    	  . $objavatar->gravatarLink
    	  . "<br />Testing with user who does not exist: <br />" 
    	  . $objavatar->show("nopossibility@nothing.noplace") 
    	  . "<br />for nopossibility@nothing.noplace <br />"
    	  . $objavatar->gravatarLink;
    	//Test with a size setting
    	$objavatar->avatarSize="30";
    	$str .= "<br /><br />Testing with avatar size setting: <br />" 
    	  . $objavatar->show("dkeats@uwc.ac.za") 
    	  . "<br />for dkeats@uwc.ac.za<br />"
    	  . $objavatar->gravatarLink;
		$this->setVarByRef("str", $str);
        /*
        * Return the template determined by the method resulting 
        * from action
        */
        return "dump_tpl.php";
    }
    
}
?>
