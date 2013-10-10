<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Dummy Class for building the toolbar for Sanord menu.
*
* The class builds a css style menu from the list of modules based on which modules
* the user has premission to access.
*
* @author David Wafula
* @copyright (c)2008 UWC
* @package sanord-toolbar
* @version 0.1
*/

class menu extends object
{
    /**
    * @var $contextCode The current context code
    * @access private
    */
    private $contextCode = '';

    /**
    * Method to construct the class.
    **/
    function init()
    {
        
        
       
    }

   
    /**
    * Method to display the toolbar.
    * @return string $navbar The toolbar.
    */
    function show()
    {
           
               return $this->createToolbar();
          
        
        return '';
    }

   
    /**
    * Method to create the standard toolbar
    */
    function createToolbar()
    {
        $menu = FALSE;
        $iconList = array();

       //create dummy toolbar
        $menu = "";

        if(!$menu) {
            $menu='';
        }
        
        // get breadcrumbs
        $crumbs=array('');

      
        //removed id="menu" from the div because it looks crap
        $navbar = '<div  id="menu">'.$menu.'</div><div id="tooliconslist">'.$iconsStr.'</div><div id="breadcrumbs">'.$crumbs.'</div>';

        return $navbar;
    }

}
?>