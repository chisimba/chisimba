<?php
/**
* Class expandmenu extends object.
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class to build an expandable menu
* @author Wesley Nitsckie
* @copyright (c) 2004 University of the Western Cape
* @package toolbar
* @version 1
*/

class expandmenu extends object
{
    var $headings=array();

    /**
    * Method to construct the class
    */
    function init()
    {
    }

    /**
    * Method to build the html for displaying the menu.
    * @return string $str The menu.
    */
    function show()
    {
        $javascriptFile=$this->getJavascriptFile('slide.js','toolbar');
        $str=$javascriptFile;
        $str.='<ul id="menu">';
        foreach(array_keys($this->headings) as $heading )
        {
            $str.='<li>'.$heading;
            $str.='<ol>';
            foreach($this->headings[$heading] as $item => $v){
                $str.='<li>'.$v.'</li>';
            }
            $str.='</ol>';
            $str.='</li>';
        }
        $str.='</ul>';

        return $str;
    }

    /**
    * Method to add a heading to the menu.
    * @return
    */
    function addHeading($str=null)
    {
        $this->headings[$str]=array();
    }

    /**
    * Method to add a menu item under a heading on the menu.
    * @return
    */
    function addMenuItem($heading,$str='')
    {
        if(!empty($str)){
            if (array_key_exists($heading, $this->headings)){
                array_push ($this->headings[$heading],$str);
            }
        }
    }
}
?>