<?php

/**
* Class usermenu extends object.
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* User Menu Class
* This class generates a user lefthand side navigation menu.
* The menu is created dynamically using table tbl_menu_category. Modules set to
* go into the user menu are given the menu category menu_user-num. Where
* num indicates the position of the item in the menu: 1 = top, 2 = middle,
* 3 = bottom.
* @author Tohir Solomons
* @author Megan Watson
* @copyright (c) 2004 University of the Western Cape
* @package toolbar
* @version 1
*/
class usermenu extends object
{
    /**
    * Constructor method to instantiate objects and get variables
    */
    function init()
    {
        $this->objSideMenu =& $this->getObject('sidemenu');
    }

    /**
    * Method to get and display the user side menu.
    * @return string $menu The finished menu
    */
    function show()
    {
        $menu = $this->objSideMenu->menuUser();
        return $menu;
    }
}
?>