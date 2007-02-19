<?php
/* -------------------- interface class for stories module ----------------*/

/**
*
* Class for providing interface elements to the stories module
*
* @author Derek Keats
*
*/
class structureinterface extends object {

    /**
    * @var $objConfig String object property for holding the 
    * configuration object
    * @access public
    */
    public $objConfig;
    
    /**
    * @var $objLanguage String object property for holding the 
    * language object
    * @access public
    */
    public $objLanguage;
    
    /**
    * @var $objLog String object property for holding the 
    * logger object for logging user activity
    * @access private
    */
    private $objLog;

    /**
    * @var $objH String object property for holding the 
    * heading object from htmlelements
    */
    public $objH;

    /**
    * @var $fields String array An array of the fields to display
    * @access private
    */
    private $fields;
    
    /**
    * @var $viewTable String The table used to display the output
    * @access private
    */
    private $viewTable;
    
    /**
    * @var $allowAdmin Boolean Whether the user viewing can admin the timelines
    * @access private
    */
    private $allowAdmin;
    
    /**
    * @var $dataAr String Array An array of the data as returned by the DB class
    * @access private
    */
    private $dataAr;
    
    /**
    * @var $objGetIcon String Object A string to hold the geticon object from
    *   htmlelements
    * @access private
    */
    private $objGetIcon;
    
    /**
    * @var $objGetIcon String Object A string to hold the geticon object from
    *   htmlelements
    * @access private
    */
    private $objConfirm;

    /**
    *
    * Constructor method to define the table
    *
    */
    function init()
    {
        $this->objUser =& $this->getObject('user', 'security');
        $this->objLanguage =& $this->getObject('language', 'language');
        $this->objDbStr = & $this->getObject('dbstructure', 'timeline');
        $this->objH =& $this->getObject('htmlheading', 'htmlelements');
        $this->objGetIcon = $this->newObject('geticon', 'htmlelements');
        //Get the confrim object & use it to make a popup
		$this->objConfirm = $this->newObject('confirm','utilities');
        $this->allowAdmin = TRUE;
    }
    
    /**
     * 
     * Show method to build the table and return it for rendering
     * @access public
     * @return The formatted table of data
     * 
     */
    public function show()
    {
    	$this->setFields();
    	$this->makeViewTable();
    	$this->addHeaderRowToTable();
    	$this->getData();
    	$this->addDataRowsToTable();
        return $this->viewTable;
    }
    
    /**
     * 
     * Method to set the standard fields to display
     * @access private
     * 
     */
    private function setFields()
    {
        $this->fields = array("id", "title", "description", "url", "focusdate", 
  		  "intervalpixels", "intervalunit", "tlheight", "theme", "bgcolor",
		  "created");
    }
    
    /**
     * 
     * Method to create the skeleton of the table
     * @access Private
     * 
     */
    private function makeViewTable()
    {
        $this->viewTable = "<table width=\"100%\" class=\"viewsimple\" id=\"viewdatasimple\">{TABLECONTENTS}</table>";
    }
    
    /**
     * 
     * Method to add the field names to the table heading
     * @access private
     * 
     */
    private function addHeaderRowToTable()
    {
        try {
        	//Add a row to the table
        	$tmp = "<tr>{HEADERROW}</tr>{TABLECONTENTS}";
        	$this->viewTable = str_replace ("{TABLECONTENTS}", $tmp, $this->viewTable);
        	//Loop over the fields and add them as cells with as sort link
        	$n = 0;
        	$tmp="";
        	foreach ($this->fields as $field) {
        	    $paramArray = array('action' => 'viewall', 
 				  'order' => $field);
		        $tmp .= "<td class=\"heading\"><a href=\""
		         . $this->uri($paramArray, "timeline")
		         . "\">" . $this->objLanguage->languageText("mod_timeline_fieldname_$field", "timeline")
		         . "</a></td>\n";
        	}
        	//Add the ADD icon to the last column if they can add
	        $objGetIcon = $this->newObject('geticon', 'htmlelements');
	        if ($this->allowAdmin) {
	            $paramArray = array(
 			      'action' => 'addstructure',
 			      'mode' => 'add');
	            $tmp .= "<td class=\"heading\" align=\"right\" width=\"30\">"
	             . $objGetIcon->getAddIcon($this->uri($paramArray, "timeline"))
	             . "</td>\n";
	           
	        }
	        $this->viewTable = str_replace ("{HEADERROW}", $tmp, $this->viewTable);
	        //Clean up place holder
	        $this->viewTable = str_replace ("{HEADERROW}", "", $this->viewTable);
        } catch(customException $e) {
            //something went wrong, print it out and log it
            echo customException::cleanUp();
            //kill everything because we are dead anyway
            die();
        }
    }
    
    /**
     * 
     * Method to use the DB table class to return an array of the 
     * data to display 
     * @access private
     * 
     */
    private function getData()
    {
        //Create list of fields for select
        $ix = 1; 
        $flStr = "";
        foreach ($this->fields as $field) {
            if ($ix < count($this->fields)) {
                $flStr .= $field . ",";
            } else {
                $flStr .= $field;
            }
            $ix++;
        }
        $sql = "SELECT " . $flStr . " FROM tbl_timeline_structure WHERE isdeleted != 1";
        $filter=NULL;
        $order = $this->getParam("order", NULL);
        if ($order) {
            $filter=" ORDER BY ".$order;
        }
        $this->dataAr = $this->objDbStr->getArray($sql.$filter);
    }
    
    /**
     * 
     * Method to insert the data as rows in the table skeleton
     * @access private
     * 
     */
    private function addDataRowsToTable()
    {
        	//Add a row to the table
        	$tmp = "{DATARROW}";
        	$this->viewTable = str_replace ("{TABLECONTENTS}", $tmp, $this->viewTable);
        	//Loop over the fields and add them as data cells for the current row
        	$str ="";
        	$rowcount=0;
        	//
        	
        	if (count($this->dataAr) > 0) {
	        	foreach ($this->dataAr as $row) {
	        	    $oddOrEven = ($rowcount == 0) ? "odd" : "even";
	        	    foreach ($this->fields as $field) {
	        	        $str .= "<td class=\"" . $oddOrEven . "\">" . $row[$field] . "</td>\n";
	        	    }
	        	    $str .= $this->getAddEditCell($row['id'], $oddOrEven);
	        		// Set rowcount for bitwise determination of odd or even
	            	$rowcount = ($rowcount == 0) ? 1 : 0;
	            	$this->viewTable = str_replace ("{DATARROW}", "<tr>" . $str . "</tr>{DATARROW}", $this->viewTable);
	            	$str = "";
	        	}
	        	//Clean up place holder
		        $this->viewTable = str_replace ("{DATARROW}", "", $this->viewTable);
		        $this->viewTable = str_replace ("{TABLECONTENTS}", "", $this->viewTable);
        	} else {
        	    $this->viewTable = str_replace ("{DATARROW}", "", $this->viewTable);
		        $this->viewTable = str_replace ("{TABLECONTENTS}", "", $this->viewTable);
        	    $this->viewTable .= "<span class=\"noRecordsMessage\">" .
        	    		$this->objLanguage->languageText("mod_timeline_norecords", "timeline")
        	    		. "</span>";
        	}
    }
    
    /**
     * 
     * Method to add the edit/add icons to the table
     * @access private
     * @param string $id A string containing the Id primary key
     * @param string $oddOrEven Whether the class for the cell is odd or even
     * @return String The table cell to add to the table 
     * 
     */
    function getAddEditCell($id, $oddOrEven)
    {
		if ($this->allowAdmin) {
		    //add the edit/add icons
		    $ret = "<td class=\"" . $oddOrEven . "\"><nobr>";
		    $editArray = array('action' => 'editstructure',
		        'mode' => 'edit',
		        'id' => $id);
		    $deleteArray = array('action' => 'deletestructure',
		        'confirm' => 'yes',
		        'id' => $id);
		    $ret .= $this->objGetIcon->getEditIcon($this->uri($editArray, "timeline"));
		    
		    
		    //Go for the delete icon
		    $delLink = $this->uri($deleteArray, "timeline");
		    // The delete icon with link uses confirm delete utility
            $this->objGetIcon->setIcon("delete");
            $this->objGetIcon->alt=$this->objLanguage->languageText('mod_timeline_delstructure','timeline');
		    
		    
		    $rep = array('ITEM', $id);
            $this->objConfirm->setConfirm($this->objGetIcon->show(),
              $delLink, $this->objLanguage->code2Txt("mod_timeline_conf",'timeline', $rep));
		    $ret .=  $this->objConfirm->show() . "</nobr></td>\n";
		    return $ret;
		}
    }
    
}  #end of class
?>