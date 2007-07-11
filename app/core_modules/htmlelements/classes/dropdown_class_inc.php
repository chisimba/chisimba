<?php

/**
* Dropdown class for outputting dropdown menu.
*
*
* @package dropdown
* @category HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @author Wesley Nitsckie and Sholum
* @author Tohir Solomons
* @example $dd=&new dropdown('mydropdown');
* $dd->addOption()    will add a blank option
* $dd->addOption('1','Male')
* $dd->addOption('2','Female')
* $dd->show();
*
* OR use from a result array
*   $objElement = new dropdown('user_dropdown');
*	$objElement->addFromDB($this->objDBUser->getAll(),'username','userId',$this->objDBUser->userName());
*	$objElement->label='User list';
*	$objElement->show();
*/

// Include the HTML base class
require_once("abhtmlbase_class_inc.php");
// Include the HTML interface class
require_once("ifhtml_class_inc.php");

class dropdown extends abhtmlbase implements ifhtml
{

  /**
  *
  * @var array $options: holds the options for the combo box
  */
  public $options=array();

  /**
  *
  * @var array $extras: holds the extra attributes for the combo box
  */
  public $extras=array();
  
  /**
  *
  * @var boolean $multiple: When set allows multiple entries to be selected
  */
  public $multiple = FALSE;

   /**
  *
  * @var array $multipleselected: When set allows multiple entries marked as selected
  */
  public $multipleselected = array();
  
  /**
  *
  * @var string $size: Defines the number of visible items in the dropdown list
  */
  public $size = 1;
  
  /**
  *
  * @var string $selected: The value that selected
  */
  public $selected;
  
  /**
  *
  * @var string $cssClass: CSS Class for the drop down
  */
  public $cssClass = 'WCHhider';


  /**
  * Class Constructor
  * @param string $name : The name of the dropdown
  */
  public function dropdown($name=NULL){
  	$this->name=$name;
	$this->cssId = 'input_'.$name;
  }

  /*
  * Method that adds a options to
  * the radio group
  * @param string $label : The label that goes with the option
  * @param string $value : The value for a give option

  */
  public function addOption($value=null,$label=null,$extra='')
  {
    if ($label==null) {
        $label = $value;
    }
  	$this->options[$value] = $label;
	$this->extras[$value] = $extra;
  }

  /**
  * Method to set the selected value
  * @param $value string : The value that you want selected
  */
  public function setSelected($value)
  {
	if(isset($this->options[$value]))
	{
		$this->selected=$value;
	}
  }

  /**
  * Method to set multiple selected values
  * @param $valueArr Array : An Array of values that you want selected
  */
  public function setMultiSelected($valueArr)
  {
		$this->multipleselected=$valueArr;
  }


  /*
	* Method to set the cssId class
	* @param string $cssId
	*/
	public function setId($cssId)
	{
		$this->cssId = $cssId;
	}

  /**
  * Method to show the dropdown
  * @return $str string : The dropdown html
  */
  public function show()
  {
	/*
	ob_start();
	echo '<pre>';
	print_r($this->extras);
	echo '</pre>';
  	$str = ob_get_contents();
	ob_end_clean();
	return $str;
	*/
	//
  	if($this->multiple){
        $this->name = $this->name."[]";    
    }
    $str = '<select name="'.$this->name.'"';
	if($this->cssClass){
		$str.=' class="'.$this->cssClass.'" ';
	}
	if ($this->cssId) {
            $str .= ' id="' . $this->cssId . '" ';
    }
    if($this->multiple){
        $str .= ' multiple="multiple"';
    }
    if($this->size > 1){
        $str .= ' size="'.$this->size.'"';
    }
    if ($this->extra) {
        $str .= ' '.$this->extra;
    }
	$str.='>'."\n";
	foreach ($this->options as $opt => $lbl)
	{
		$str.='<option value="'.$opt.'"';
		
		if($this->multipleselected)
		{
			foreach($this->multipleselected as $mselect)
			{
				if($mselect==$opt)
				{
					$str.=' selected="selected"';
				}
			}
		}
		else
		{
			if($this->selected==$opt){
				$str.=' selected="selected"';
			}
		}
	    if ($this->extras[$opt] != '') {
	        $str .= ' '.$this->extras[$opt];
	    }
		$str.='>';
		$str.=$lbl;
		$str.='</option>'."\n";
	}
	$str .= '</select>'."\n";
	return $str;
  }

  /**
  * Method used to populate
  * the dropdown with a array
  * @param $resultset array : the result set
  * @param $labelField string : the value that will be displayed in the dropdown
  * @param $valueField string : the value the will go in the 'value' of the dropdown
  * @param @selectedValue string : the value that you want to have selected
  */
  public function addFromDB($array, $labelField=null, $valueField=null, $selectedValue=null)
  {
  	if ($array)
	{
		//loop through the array
		foreach($array as $line)
		{
			//add an option
			$this->addOption($line[$valueField],$line[$labelField]);
		}
		if (!is_null($selectedValue)) {
           	$this->setSelected($selectedValue);
		}
	}
  }
}
?>