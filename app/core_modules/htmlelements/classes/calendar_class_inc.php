<?php

/**
 * Text Input class controls buttons 
 * @author Wesley Nitsckie
 * @version $Id$
 * @copyright 2003 
 **/


class calendar {
	var $name;
	var $css;
	var $value;
	var $windowName;
	var $location;
	var $width;
	var $height;
    
    /**
     * Initialization method to set default values
	 * @param string $name optional :sets the name of the text input
     */
	function caledar($name=null,$value=null){
		$this->name=$name;
		$this->value=$value;
		$this->css='textdisabled';
	}
	/**
	* Method to set the css class
	* @param string $css
	*/
	function setCss($css)
	{
		$this->css=$css;
	}
	
        
    
	/*function to set the date for calendar
	* @param int $mth :the month
	* @param int $day :the day
	* @param int $year :the year
	*/
	function setDate($mth,$day,$year)
	{
		if(checkdate($mth,$day,$year))
		{
			$this->value=$mth.'/'.$day.'/'.$year;
		}
	}
	
	/**
	* Method to show the button
	*/
	function show(){
		$this->windowName = "win";
		$str='<input type="text" value="'.$this->value.'" id="caltext"';
		$str.=' name="'.$this->name.'"';
		$str.=' value="'.$this->value.'"';
		$str.=' class="'.$this->css.'"';
		$str.=' />';
		//$str.="<a href=\"#\" onclick=\"window.open('modules/htmlelements/classes/cal.php','win','width=350,height=200'); return false\"><img src=\"modules/htmlelements/resources/images/schedule_ico.gif\"></a>";
		$str.="<a href=\"#\" onclick=\"window.open('modules/htmlelements/classes/cal.php','win','width=350,height=200'); return false\"><img src=\"modules/htmlelements/resources/images/schedule_ico.gif\" /></a>";
		return $str;
	}


 }

?>
