<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
/**
 * HTML control class to create layers (<DIV>) tags
 * @package iframe
 * @category HTML Controls
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version 1
* @author Wesley Nitsckie
* @example
*/
class frameset extends htmlbase{
    /**
    * @var array $frameList
    */
    var $frameList = array();
    
    /**
    * Constructor
    */
    function init(){
    
    
    }
    
    /**
    * Method to show the frameset
    */
    function show(){
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
    function addFrame($objFrame){
       array_ push($this->frameList, $objFrame);
    }
    

?>