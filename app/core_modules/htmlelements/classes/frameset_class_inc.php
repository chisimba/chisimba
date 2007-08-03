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

// Include the HTML base class

/**
 * Description for require_once
 */
require_once("abhtmlbase_class_inc.php");
// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");

/**
 * HTML control class to create layers (<DIV>) tags
 * @package   iframe
 * @category  HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license   GNU GPL
* @version   1
* @author    Wesley Nitsckie
* @example  
*/
class frameset extends abhtmlbase implements ifhtml
{
    /**
    * @var array $frameList
    */
    public $frameList = array();
    
    /**
    * Constructor
    */
    public function init(){
    
    
    }
    
    /**
    * Method to show the frameset
    */
    public function show(){
        $ret="<iframe width=\"".$this->width."\" height=\"".$this->height."\" ";        
        if ($this->align) {
            $ret .= " align=\"".$this->align."\" ";
        }
        if (isset($this->frameborder)) {
            $ret .= " frameborder=\"".$this->frameborder."\" ";
        }
        if ($this->align) {
            $ret .= " align=\"".$this->align."\" ";
        }
        if ($this->marginheight) {
            $ret .= " marginheight=\"".$this->marginheight."\" ";
        }
        if ($this->marginwidth) {
            $ret .= " marginwidth=\"".$this->marginwidth."\" ";
        }
        if ($this->name) {
            $ret .= " name=\"".$this->name."\" ";
        }
        if ($this->id) {
            $ret .= " id=\"".$this->id."\" ";
        }
        if ($this->scrolling) {
            $ret .= " scrolling=\"".$this->scrolling."\" ";
        }
        $ret .= ">";
        
        foreach($this->frameList as $list){
            $list[]->show();
        }
        
        $ret = "</iframe>";
        return $ret;
    }
    
    /**
    * Method to  add an frame
    * @param object $frame The frame object
    */
    public function addFrame($objFrame){
       array_ push($this->frameList, $objFrame);
    }
    

?>