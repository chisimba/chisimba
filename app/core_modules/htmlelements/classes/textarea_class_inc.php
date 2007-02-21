<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// Include the HTML base class
require_once("abhtmlbase_class_inc.php");
// Include the HTML interface class
require_once("ifhtml_class_inc.php");

/**
* textare class to use to make textarea inputs.
* 
* @package htmlTextarea
* @category HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version $Id: 
* @author Wesley Nitsckie 
* @author Megan Watson
* @author Tohir Solomons
* @example 
* @todo -c HTML Editor that will extend this object
*/
 class textarea extends abhtmlbase implements ifhtml
 {
 	/**
    * 
    * @var string $cols: The number of columns the textare will have
    */
	public $cols;
	/**
    * 
    * @var string $rows: The number of rows the textare will have
    */
	public $rows;
	
	
	/**
    * Method to establish the default values
    */
	public function textarea($name=null,$value='',$rows=4,$cols=50)
 	{
		$this->name=$name;
		$this->value=$value;
		$this->rows=$rows;
		$this->cols=$cols;
		$this->css='textarea';
		$this->cssId = 'input_'.$name;
	}
	
	/**
    * function to set the value of one of the properties of this class
    * 
    * @var string $name: The name of the textare
    */
	public function setName($name)
	{
		$this->name=$name;
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
    * function to set the amount of rows 
    * @var string $Rows: The number of rows of the textare
    * 
    */
	public function setRows($rows)
	{
		$this->rows=$rows;
	}
	/**
    * function to set the amount of cols 
    * @var string $cols: The number of cols of the textare
    * 
    */
	public function setColumns($cols)
	{
		$this->cols=$cols;
	}
	
	/**
    * function to set the content
    * @var string $content: The content of the textare
    */
	public function setContent($value)
	{
		$this->value=$value;
	}
 	/**
    * Method to show the textarea
    * @return string The formatted link
    */
	public function show()
	{
		$str = '<textarea name="'.$this->name.'"';
		if($this->cssClass){
			$str.=' class="'.$this->cssClass.'">';
		}
		if ($this->cssId) {
            $str .= ' id="' . $this->cssId . '"';
        }
		if($this->rows){
			$str.=' rows="'.$this->rows.'"';
		}
		if($this->cols){
			$str.=' cols="'.$this->cols.'"';
		}
		if ($this->extra) {
            $str .= ' '.$this->extra;
        }
		$str.='>';
		$str.=$this->value;
		$str.='</textarea>';
		
		return $str;
	}
 }

?>
