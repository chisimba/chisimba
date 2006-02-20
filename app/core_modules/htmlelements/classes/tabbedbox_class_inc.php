<?php
/**
* HTML control class to create tabbed boxes using the layers class.
* The style sheet class is >box<.
* 
* Note: relies on the skin CSS containing the correct style definitions
* 
* @abstract 
* @package htmlTable
* @category HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version $Id$;
* @author Derek Keats 
* 
*/
class tabbedbox {
    /**
    * Define all vars
    */
    var $tabLabel=array(); // JCA - Allow for many labels
    var $boxContent;
    var $box;

    /**
    * Initialization method to set default values
    */
    function tabbedbox()
    {
        $this->tabLabel=NULL;
        $this->boxContent=NULL;
    }
    
    /**
    * method to add a tab label
    * @var string $str: the string to add
    * 
    */
    function addTabLabel($str)
    {
        $this->tabLabel[]=$str;
    }
    
    /**
    * Method to add content to the body of
    * the box
    * @var string $str: The string to add to the body
    */
    function addBoxContent($str)
    {
        $this->boxContent=$str;
    }
    
    /**
    * method to build a tabbed box from the label text and the body
    * content
    * @param string tablabel: The label for the tab
    * @param string boxcontent: the content for the box
    */
    function buildTabbedBox($tablabel, $boxcontent)
    {
        $this->tabLabel=$tablabel;
        $this->boxcontent=$boxcontent;
        $this->makeTabbedBox();
        return $this->box;
    }
    
    /**
    * Method to assemble the box (uses the parent class
    * to assemble the box)
    */
    function makeTabbedBox()
    {
	$this->box='<fieldset class="tabbox">';
	// JCA begin - allows for 0:M tabLabels
	if (isset($this->tabLabel)) {
    	    foreach($this->tabLabel as $tabLabel){
    		$this->box.='<legend class="tabbox">'. $tabLabel.'</legend>';
	    }
	}
	// JCA end - allow for 0:M tabLabels.
        $this->box.=$this->boxContent.'</fieldset>';
    }
    
    /**
    * Method to return the tabbed box complete for display
    */
    function show()
    {
        $this->makeTabbedBox();
        return $this->box;
    }
    
} 

?>