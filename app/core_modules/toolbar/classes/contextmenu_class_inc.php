<?php
/**
* Class contextmenu extends object.
*
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Context Menu Class
* This class generates a context lefthand side context sensitive navigation menu.
* The menu is created dynamically using table tbl_menu_category. Modules set to
* go into the postlogin menu are given the menu category menu_postlogin-num. Where
* num indicates the position of the item in the menu: 1 = top, 2 = middle,
* 3 = bottom.
*
* @author Tohir Solomons
* @author Megan Watson
* @author Paul Scott <pscott@uwc.ac.za>
* @copyright (c) 2004 University of the Western Cape
* @package toolbar
* @version 1
*/
class contextmenu extends object
{
    /**
    * Constructor method to instantiate objects and get variables
    */
    public function init()
    {
        $this->objSideMenu = $this->getObject('sidemenu');
    }

    /**
    * Method to get and display the context side menu.
    *
    * @return string $menu The finished menu
    */
    public function show()
    {
        $menu = $this->objSideMenu->menuContext();

        return $menu;
    }
}
?>