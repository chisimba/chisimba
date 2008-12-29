<?php
/**
* Class menu extends object.
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class for showing the breadcrumbs on a page.
*
* The class is an alternative for developers who only want to show the breadcrumbs,
* not the entire toolbar on a page.
*
* @author Tohir Solomons
* @copyright (c)2006 UWC
* @package toolbar
* @version 0.1
*/

class breadcrumbs extends object
{
    /**
    * Method to construct the class
    * @access public
    */
    public function init()
    {
        $this->tools = $this->getObject('tools');
    }
    
    /**
    * Method to display the breadcrumbs
    * @access public
    * @return string
    */
    public function show()
    {
        return $this->tools->navigation();
    }
}
?>