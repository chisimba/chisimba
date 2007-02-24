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
    	$this->objH->str = $this->objLanguage->languageText("mod_timeline_lmen_title", "timeline");
    	$ret = $this->objH->show() . "\n<ul>";
    	$objHref = $this->newObject("href", "htmlelements");
    	$action = $this->getParam("action", "viewall");
    	//---------- The timeline home link
    	if ($action == "viewall") {
    	    $ret .= "<li class=\"tlmenuleft\">" 
    	      . $this->objLanguage->languageText("mod_timeline_title_viewall", "timeline")
 			  . "</li>";  
    	} else {
	    	$demLink = $this->uri(array("action" => "viewall"), "timeline");
	    	$objHref->text = $this->objLanguage->languageText("mod_timeline_title_viewall", "timeline");
	    	$objHref->link = $demLink;
	    	unset($demLink);
	    	$ret .= "<li class=\"tlmenuleft\">" . $objHref->show() . "</li>";  
    	}
    	//---------- The demo link
    	if ($action == "viewdemo") {
    	    $ret .= "<li class=\"tlmenuleft\">" 
    	      . $this->objLanguage->languageText("mod_timeline_lmen_vdem", "timeline")
 			  . "</li>";  
    	} else {
	    	$demLink = $this->uri(array("action" => "viewdemo"), "timeline");
	    	$objHref->text = $this->objLanguage->languageText("mod_timeline_lmen_vdem", "timeline");
	    	$objHref->link = $demLink;
	    	unset($demLink);
	    	$ret .= "<li class=\"tlmenuleft\">" . $objHref->show() . "</li>";  
    	}

    	//----------- The link for making timelines
    	if ($action == "makesingle") {
    	    $ret .= "<li class=\"tlmenuleft\">" 
    	      . $this->objLanguage->languageText("mod_timeline_lmen_csingle", "timeline")
 		      . "</li>";
    	} else {
	    	$mkSingLink = $this->uri(array("action" => "makesingle"), "timeline");
	    	$objHref->text = $this->objLanguage->languageText("mod_timeline_lmen_csingle", "timeline");
	    	$objHref->link = $mkSingLink;
	    	unset($demLink);
	    	$ret .= "<li class=\"tlmenuleft\">" . $objHref->show() . "</li>";
    	}

    	$ret .= "</ul>";
        return $ret;
    }

  
}  #end of class
?>