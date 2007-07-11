<?php

/**
 * Text Input class controls buttons 
 * @author Wesley Nitsckie
 * @version $Id$
 * @copyright 2003 
 **/
 
// Include the HTML interface class
require_once("ifhtml_class_inc.php");

class calendar implements ifhtml {
	public $name;
	public $css;
	public $value;
	public $windowName;
	public $location;
	public $width;
	public $height;
    
    /**
     * Initialization method to set default values
	 * @param string $name optional :sets the name of the text input
     */
	public function caledar($name=null,$value=null){
		$this->name=$name;
		$this->value=$value;
		$this->css='textdisabled';
	}
	
	/**
	* Method to set the css class
	* @param string $css
	*/
	public function setCss($css)
	{
		$this->css=$css;
	}
    
	/*function to set the date for calendar
	* @param int $mth :the month
	* @param int $day :the day
	* @param int $year :the year
	*/
	public function setDate($mth,$day,$year)
	{
		if(checkdate($mth,$day,$year))
		{
			$this->value=$mth.'/'.$day.'/'.$year;
		}
	}
	
	/**
	* Method to show the button
	*/
	public function show(){
		$this->windowName = "win";
		$str='<input type="text" value="'.$this->value.'" id="caltext"';
		$str.=' name="'.$this->name.'"';
		$str.=' class="'.$this->css.'"';
		$str.=' />';
		//$str.="<a href=\"#\" onclick=\"window.open('modules/htmlelements/classes/cal.php','win','width=350,height=200'); return false\"><img src=\"modules/htmlelements/resources/images/schedule_ico.gif\"></a>";
		$str.="<a href=\"#\" onclick=\"window.open('core_modules/htmlelements/classes/cal.php','win','width=350,height=200'); return false\"><img src=\"core_modules/htmlelements/resources/images/schedule_ico.gif\" /></a>";
		return $str;
	}

 }

?>