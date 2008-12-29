<?php
/**
* Class postloginmenu extends object.
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Postlogin Menu Class
* This class generates a postlogin lefthand side navigation menu.
* The menu is created dynamically using table tbl_menu_category. Modules set to
* go into the postlogin menu are given the menu category menu_postlogin-num. Where
* num indicates the position of the item in the menu: 1 = top, 2 = middle,
* 3 = bottom.
* @author Tohir Solomons
* @author Megan Watson
* @copyright (c) 2004 University of the Western Cape
* @package toolbar
* @version 1.1
*/
class postloginmenu extends object
{
    /**
    * Method to construct the class.
    */
    function init()
    {
        $this->objSideMenu = $this->getObject('sidemenu','toolbar');
    }

    /**
    * Method for getting and displaying the postlogin side menu.
    * @return string menu
    */
    function show()
    {
        //split the side menu into the menu items and the context login and user image
        $menu = '';
        
        $menu .= $this->objSideMenu->userDetails();
        $menu .= $this->objSideMenu->getPostLoginMenuItems().'<br />';
        //$menu .= $this->objSideMenu->contextDetails();
        
        return $menu;
    }

}
?>