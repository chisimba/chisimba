<?php
/* -------------------- security class extends module ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// // Include the HTML base class
require_once("abhtmlbase_class_inc.php");
// Include the HTML interface class
require_once("ifhtml_class_inc.php");
require_once("label_class_inc.php");
require_once('htmltable_class_inc.php');

/**
* Radio class for outputting Radio Buttons. 
* 
* 
* @abstract 
* @package multitabbed Box
* @category HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @author Wesley Nitsckie
* @author Tohir Solomons
* @example 
* //Radio button Group
*	$objElement = new radio('sex_radio');
*	$objElement->addOption('m','Male');
*	$objElement->addOption('f','Female');
*	$objElement->addOption('n','Seaweed');
*	$objElement->setSelected('f');
*	echo $objElement->show().'<br>';
*
*/
class radio extends abhtmlbase implements ifhtml
{
  
    public $options = array();
    public $selected;
    public $breakSpace = '';
    public $tableColumns = 3;
    
    /**
    * Class Constructor
    * @param string $name : The name of the radio group
    */
    public function radio($name){
        $this->name=$name;
        $this->cssClass='transparentbgnb';
    }
  
    /**
    * Method to set the break/space between input options
    * @param string $break : The name of the radio group
    */
    public function setBreakSpace($breakSpace)
    {
        $this->breakSpace=$breakSpace;
    }
    
    /**
    * Method to set the cssId class 
    * @param string $cssId
    */
    public function setTableColumns($columns)
    {
        $this->tableColumns = $columns;
    } 
  
    /**
    * Method to set the cssId class 
    * @param string $cssId
    */
    public function setId($cssId)
    {
        $this->cssId = $cssId;
    } 
  
    /*
    * Method that adds a options to 
    * the radio group
    * @param string $label : The label that goes with the option
    * @param string $value : The value for a give option
    
    */
    public function addOption($value,$label)
    {
        $this->options[$value] = $label;
    }
  
    /*Method to set the an
    * option to be selected
    * @param string $value : Sets the option to 'checked'
    */
    public function setSelected($value)
    {
        if(isset($this->options[$value]))
        {
            $this->selected=$value;
        }	
    }
  
    /*
    * Method to show the option group
    */
    public function show()
    {
        if (strtolower($this->breakSpace) == 'table') {
            return $this->showTable();
        } else {
            return $this->showNormal();
        }
    }

    /*
    * Method to show the option group with the given breakspace (not table)
    */
    public function showNormal()
    {
        $str='';
        
        $breakSpace = '';
        
        foreach ($this->options as $opt => $lbl)
        {
            $str.= $breakSpace;
            
            $str.='<input type="radio" name="'.$this->name.'"';
            $str.=' value="'.$opt.'"';
            
            if ($this->cssClass) {
                $str.=' class="'.$this->cssClass.'"';    
            }
            
            // If no CSS Id is given, it takes the default value of input_$opt for accessibility
            // If CSS Id is given, it takes the default value of input_$opt for accessibility, as well as CSS one
            if ($this->cssId) {
                $str .= ' id="input_' .$this->name. $opt. ' ' . $this->cssId . '"';
            } else {
                $str .= ' id="input_' .$this->name. $opt. '"';
            }
    
            if($this->selected==$opt){
                $str.=' checked="checked"';
            }
            if($this->extra){
                $str.=' '.$this->extra;
            }
            $str.=' />' ;
            
            $label = new label($lbl, 'input_'.$this->name.$opt);
            $str.=$label->show();
            
            $breakSpace = $this->breakSpace;
        }
        return $str;
    }

    /**
    * Method to show the option group for a table
    */
    public function showTable()
    {
        $table =& new htmltable();
        $table->startRow();
        $table->cellpadding  = 1;
        
        $counter = 0;
        
        $equalColumns = (100 - (100 % $this->tableColumns)) / $this->tableColumns;
        
        foreach ($this->options as $opt => $lbl) 
        {
            $counter++;
            
            $str ='<input type="radio" name="'.$this->name.'"';
            $str.=' value="'.$opt.'"';
            
            if ($this->cssClass) {
                $str.=' class="'.$this->cssClass.'"';    
            }
            
            // If no CSS Id is given, it takes the default value of input_$opt for accessibility
            // If CSS Id is given, it takes the default value of input_$opt for accessibility, as well as CSS one
            if ($this->cssId) {
                $str .= ' id="input_' .$this->name. $opt. ' ' . $this->cssId . '"';
            } else {
                $str .= ' id="input_' .$this->name. $opt. '"';
            }
            
            if ($this->selected == $opt){
                $str.=' checked="checked" ';
            }
            
            if ($this->extra){
                $str.=' '.$this->extra;
            }
            $str.=' />' ;
            
            $label = new label($lbl, 'input_'.$this->name.$opt);
            $str.=$label->show();
            
            $table->addCell($str, $equalColumns.'%');
            
            if ($counter % $this->tableColumns == 0) {
                $table->endRow();
                $table->startRow();
            }
        }
        
        
        if ($counter % $this->tableColumns == 0) {
            $table->endRow();
        } else {
            for ($i = 1; $i < ($counter % $this->tableColumns); $i++)
            {
                $table->addCell('&nbsp;', $equalColumns.'%');
            }
            $table->endRow();
        }
        
        return $table->show();
    
    }
}
?>