<?php
/**
* Class tablemenu extends object.
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check

/**
 * This object creates an expanded menu 
 * @package toolbar
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @version
 * @author Wesley Nitsckie 
 */
class tablemenu extends object{
    /**
    *@var array $headings
    */
    var $headings=array();    
    
    /**
    *Init method
    */
    function init(){
    
    }
    
    /**
    *Method to show the expanded menu
    *access public
    *return string
    */
    function show()
    {    
        $str='<table id="tmenu" >';
        foreach(array_keys($this->headings) as $heading )
        {            
            $str.='<tr><td class="theading">'.$heading.'</td></tr>';
            foreach($this->headings[$heading] as $item => $v){
                $str.='<tr><td class="tdata">'.$v.'</td></tr>';
            }            
        }
        $str.='</table>'; 
        return $str;
    }
    
    /**
    *Method used to add a heading
    *@access public
    *@param string $str The heading 
    *@return null
    */
    function addHeading($str=null){
        $this->headings[$str]=array();    
    }
    
    /**
    *@access public
    *@param string $heading The heading under which you want to add the item
    *@param string $str The item text 
    *@return null
    */
    function addMenuItem($heading,$str=''){    
        if(!empty($str)){
            if (array_key_exists($heading, $this->headings)) 
            {
                array_push ($this->headings[$heading],$str);
            }        
        }
    }
}
?>