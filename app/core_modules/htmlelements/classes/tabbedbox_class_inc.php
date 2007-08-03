<?php
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");

/**
* HTML control class to create tabbed boxes using the layers class.
* The style sheet class is >box<.
* 
* Note: relies on the skin CSS containing the correct style definitions
* 
* @abstract 
* @package   htmlTable
* @category  HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license   GNU GPL
* @version   $Id$;
* @author    Derek Keats
*            
*/
class tabbedbox implements ifhtml
{
    /**
    * Define all vars
    */
    public $tabLabel=array(); // JCA - Allow for many labels


    /**
     * Description for public
     * @var    string
     * @access public
     */
    public $boxContent;

    /**
     * Description for public
     * @var    string
     * @access public
     */
    public $box;

    /**
     * Description for public
     * @var    string
     * @access public
     */
    public $extra = '';

    /**
    * Initialization method to set default values
    */
    public function tabbedbox()
    {
        $this->tabLabel=NULL;
        $this->boxContent=NULL;
    }
    
    /**
    * method to add a tab label
    * @var string $str: the string to add
    *             
    */
    public function addTabLabel($str)
    {
        $this->tabLabel[]=$str;
    }
    
    /**
    * Method to add content to the body of
    * the box
    * @var string $str: The string to add to the body
    */
    public function addBoxContent($str)
    {
        $this->boxContent=$str;
    }
    
    /**
    * method to build a tabbed box from the label text and the body
    * content
    * @param string tablabel:   The label for the tab
    * @param string boxcontent: the content for the box
    */
    public function buildTabbedBox($tablabel, $boxcontent)
    {
        $this->tabLabel[]=$tablabel;
        $this->boxContent=$boxcontent;
        $this->makeTabbedBox();
        return $this->box;
    }
    
    /**
    * Method to assemble the box (uses the parent class
    * to assemble the box)
    */
    public function makeTabbedBox()
    {
	$this->box='<fieldset class="tabbox" '.$this->extra.'>';
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
    public function show()
    {
        $this->makeTabbedBox();
        return $this->box;
    }
    
} 

?>