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
*	$objElement->show()."<br>";
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
  * @var string $selected: The value that selected
  */
  public $selected;
  
 
  /**
  * Class Constructor
  * @param string $name : The name of the dropdown
  */
  public function dropdown($name){
  	$this->name=$name;
	$this->cssId = 'input_'.$name;
  }
  
  /*
  * Method that adds a options to 
  * the radio group
  * @param string $label : The label that goes with the option
  * @param string $value : The value for a give option
  
  */
  public function addOption($value=null,$label=null)
  {
    if ($label==null) {
        $label = $value;
    }
  	$this->options[$value] = $label;
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
  	$str = '<select name="'.$this->name.'"';
	if($this->cssClass){
		$str.=' class="'.$this->cssClass.'" ';
	}	
	if ($this->cssId) {
            $str .= ' id="' . $this->cssId . '" ';
        }
    if ($this->extra) {
        $str .= $this->extra;
    }
	$str.='>'."\n";
	foreach ($this->options as $opt => $lbl)
	{
		$str.='<option value="'.$opt.'"';
		if($this->selected==$opt){
			$str.=' selected="1"';
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
			//set the selected value
			if ($line[$valueField]==$selectedValue) 
            {
            	$this->setSelected($selectedValue);
            }			
		}		 
	}  
  }
}
?>
