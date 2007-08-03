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
* Class frameset class to group items
* 
* Used to create frameset that helps
* grouped items
* 
* @package   frameset
* @category  HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license   GNU GPL
* @author    Wesley Nitsckie updated by Derek Keats 2004 03 16
* @example   :
*/
class fieldsetex extends object implements ifhtml
{
	/**
	*@var $legend The heading of the frameset
	*/
	public $legend;
	/**
	*@var $legendalign The alignment for the legend
	*/
	public $legendalign;

	/**
	*@var $content The contents of the frameset
	*/
	public $contents;
    /**
	*@var $width The width attribute
	*/
	public $width;
    /**
	*@var $extra Any other extra items that needs to be added 
	*/
	public $extra;
    
        /**
        * @var $align how the table is aligned - added 2005 03 31 by James Scoble
        */
        var $align;

	/**
	*Initialize
	*/
	public function init()
	{
		$this->contents="";
	}
	
	/**
	*The show Method
    * @return null  
    * @access public
	*/
	public function show()
	{
            $str="";
            //Add the width if it exists !added by derek
            $align='';
            if (isset($this->align)){
                $align="align='".$this->align."'";
            }
            if (isset($this->width)) {
                $str.= "<table $align width=\"" . $this->width . "\"><tr><td>";
            }   
	    $str .= '<fieldset';
        
        if (isset($this->extra)) {
            $str.=' '.$this->extra;
        }
        $str .= '>';
        
        if (isset($this->legend)) {
            $str .= '<legend';
            if (isset($this->legendalign)) {
                $str .= ' align="' . $this->legendalign . '"';
            }
            $str .= '>'.$this->legend.'</legend>';
        }
		$str.="<table>";
		$str.=$this->contents;
		$str.="</table>";
		$str.='</fieldset>';
        //End the width if it exists !added by derek
        if (isset($this->width)) {
            $str.= "</td></tr></table>";
        }
		return $str;
	}
	
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  object $label Parameter description (if any) ...
     * @return void  
     * @access public
     */
	public function addLabel($label){
        if (is_object($label)) {
            $str = $label->show();
        } else {
            $str = $label;
        } 
		$this->contents.='<tr><td align="left" colspan="2">'.$str.'</td></tr>';
	}
	
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  object $label Parameter description (if any) ...
     * @param  object $field Parameter description (if any) ...
     * @return void  
     * @access public
     */
	public function addLabelledField($label,$field){
        if (is_object($label)) {
            $str1 = $label->show();
        } else {
            $str1 = $label;
        } 
        if (is_object($field)) {
            $str2 = $field->show();
        } else {
            $str2 = $field;
        } 
		$this->contents.='<tr><td align="right">'.$str1.'</td><td align="left">'.$str2.'</td></tr>';
	}

	/**
	*Method to reset the fields
    * @return null  
    * @access public
	*/
	public function reset(){
		$this->contents=null;
		$this->legend=null;
	}
	
	/**
	*Method to add the legend
	*@param $legend string  The legend to be added to the fieldset
    * @return null  
    * @access public
	*/
	public function setLegend($legend){
		$this->legend=$legend;
	}
    
    /**
	*Method to add extra parameters
	*@param $parameters string  String of parameters that can be added
    * @return null  
    * @access public
	*/
	public function setExtra($parameters){
		$this->extra=$parameters;
	}
}
?>