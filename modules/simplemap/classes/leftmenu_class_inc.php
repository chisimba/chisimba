<?php
/**
*
* Class for providing the left menu for the timeline module
*
* @package timeline
* @author Derek Keats
*
*/
class leftmenu extends object {
    
    /**
    * @var $objLanguage String object property for holding the 
    * language object
    * @access public
    */
    public $objLanguage;


    /**
    * @var $objH String object property for holding the 
    * heading object from htmlelements
    */
    public $objH;
    
    /**
    * @var $objGetIcon String Object A string to hold the geticon object from
    *   htmlelements
    * @access private
    */
    private $objGetIcon;
    

    /**
    *
    * Constructor method to define the table
    *
    */
    function init()
    {
        $this->objLanguage =& $this->getObject('language', 'language');
        $this->objH =& $this->getObject('htmlheading', 'htmlelements');
        $this->objGetIcon = $this->newObject('geticon', 'htmlelements');
    }
    
    /**
     * 
     * Show method to build the menu return it for rendering
     * @access public
     * @return The formatted menu
     * 
     */
    public function show()
    {
 
        return  $this->__getMenuText();
    }
    
    private function __getMenuText() {
    	$this->objH->str = $this->objLanguage->languageText("mod_simplemap_lmen_title", "simplemap");
    	$ret = $this->objH->show() . "\n<ul>";
    	$objHref = $this->newObject("href", "htmlelements");
    	$action = $this->getParam("action", "viewall");
    	//---------- The simplemap home link
    	if ($action == "viewall") {
    	    $ret .= "<li class=\"smmenuleft\">" 
    	      . $this->objLanguage->languageText("mod_simplemap_title_viewall", "simplemap")
 			  . "</li>";  
    	} else {
	    	$demLink = $this->uri(array("action" => "viewall"), "simplemap");
	    	$objHref->text = $this->objLanguage->languageText("mod_simplemap_title_viewall", "simplemap");
	    	$objHref->link = $demLink;
	    	unset($demLink);
	    	$ret .= "<li class=\"smmenuleft\">" . $objHref->show() . "</li>";  
    	}
    	//---------- The demo link
    	if ($action == "viewdemo") {
    	    $ret .= "<li class=\"tlmenuleft\">" 
    	      . $this->objLanguage->languageText("mod_simplemap_lmen_vdem", "simplemap")
 			  . "</li>";  
    	} else {
	    	$demLink = $this->uri(array("action" => "viewdemo"), "simplemap");
	    	$objHref->text = $this->objLanguage->languageText("mod_simplemap_lmen_vdem", "simplemap");
	    	$objHref->link = $demLink;
	    	unset($demLink);
	    	$ret .= "<li class=\"smmenuleft\">" . $objHref->show() . "</li>";  
    	}

    	$ret .= "</ul>";
        return $ret;
    }

  
}  #end of class
?>