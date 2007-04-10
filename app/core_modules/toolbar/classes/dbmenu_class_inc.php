<?php
/**
* Class dbmenu extends dbtable.
* @package toolbar
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Class to access the database table tbl_menu_category.
* The table contains a modules and their associated categories.
*
* Functions available are:
* getModules($access, $context) - returns a list of modules and their categories
* for the toolbar navigation.
* getSideMenus($menu, $access, $context) - returns a list of items for the side
* menu navigation.
* getPageItems($page, $context) - returns a list of items for the admin or
* lecturers page.
*
* @author Megan Watson
* @copyright (c)2004 UWC
* @package toolbar
* @version 0.9
*/

class dbmenu extends dbtable
{
    /**
    * Method to construct the class.
    */
    function init()
    {
        parent::init('tbl_menu_category');
        $this->table='tbl_menu_category';
    }

    /**
    * Method to get all modules in the table if the table exists.
    * The method ignores categories beginning with 'menu_', which go into
    * the side menus and 'page_' which refer to the admin and lecturer pages.
    * @param string $access The access level of the user. Default=2, all users.
    * @param bool $context TRUE if in a context, FALSE if not. Default=TRUE.
    * @return array $rows The modules and their categories.
    */
    function getModules($access=2, $context=TRUE)
    {
        $sqlFilter = "category NOT LIKE 'menu_%' AND category NOT LIKE 'page_%'";
        //$sql = 'show tables like \''.$this->table."'";
        if($access == 2){     // non-admin users
            $sqlFilter .= ' AND adminonly != 1';
        }

        if(!$context){
            $sqlFilter .= ' AND dependscontext != 1';
        }

        $ret = $this->listDbTables(); //$this->getArray($sql);
        if(in_array($this->table, $ret))
        {
        	$rows = TRUE;
        }
        else {
        	unset($rows);
        }
        if($rows){
            $sql = 'SELECT category, module, permissions, dependscontext FROM '.$this->table;
            $sql .= " WHERE $sqlFilter ";
            $sql .= 'ORDER BY category, module';
            $modules = $this->getArray($sql);
            return $modules;
        }
        return false;
    }

    /**
    * Method to get all modules in the table if the table exists.
    * The method finds categories beginning with 'flat_'
    *
    * @access public
    * @param string $access The access level of the user. Default=2, all users.
    * @param bool $context TRUE if in a context, FALSE if not. Default=TRUE.
    * @return array $rows The modules and their categories.
    */
    public function getFlatModules($access=2, $context=TRUE)
    {
        $sql = "SELECT * FROM {$this->table} WHERE category LIKE 'flat_%' ";
        
        if($access == 2){     // non-admin users
            $sql .= ' AND adminonly != 1 ';
        }

        if(!$context){
            $sql .= ' AND dependscontext != 1 ';
        }

        $sql .= 'ORDER BY category, module';
        $modules = $this->getArray($sql);
        return $modules;
    }

    /**
    * Method to get a list of modules to build the side menus.
    * The side menus consist of postlogin, user and context. The numbers after
    * the menu name indicate the position of the module in the menu: 1 = top,
    * 2 = middle, 3 = bottom.
    * @param string $menu Then side menu being built. Default=user menu.
    * @param string $access The access level of the user. Default=2, non-admin users.
    * @param bool $context TRUE if in a context, FALSE if not. Default=TRUE.
    * @return array $rows The modules.
    */
    function getSideMenus($menu='user', $access=2, $context=TRUE)
    {
        $sqlFilter = "category LIKE 'menu_$menu%'";
        //$sql = 'show tables like \''.$this->table."'";
        if($access == 2){     // lecturer
            $sqlFilter .= ' AND adminonly != 1';
        }

        if(!$context){
            $sqlFilter .= ' AND dependscontext != 1';
        }

        $ret = $this->listDbTables(); //$this->getArray($sql);
        if(in_array($this->table, $ret))
        {
        	$rows = TRUE;
        }
        else {
        	unset($rows);
        }

        //$rows = $this->getArray($sql);
        if($rows){
            $sql = 'SELECT category,module,permissions, dependscontext FROM '.$this->table;
            $sql .= " WHERE $sqlFilter ";
            $sql .= 'ORDER BY category, module';
            $modules = $this->getArray($sql);
            return $modules;
        }
        return false;
    }

    /**
    * Method to get a list of the modules to build the admin and lecturers pages.
    * @param string $page lecturer or admin. Default=lecturer.
    * @param bool $context TRUE if in a context, FALSE if not. Default=TRUE.
    * @return array $rows The modules.
    */
    function getPageItems($page='lecturer', $context=TRUE)
    {
        $sqlFilter = "category LIKE 'page_$page%'";
        //$sql = 'show tables like \''.$this->table."'";
        $page = strtolower($page);

        if(!$context){
            $sqlFilter .= ' AND dependscontext != 1';
        }

        //$rows = $this->getArray($sql);
        //if($rows){
            $sql = 'SELECT category, module, permissions, dependscontext FROM '.$this->table;
            $sql .= " WHERE $sqlFilter ";
            $sql .= 'ORDER BY category, module';
            $modules = $this->getArray($sql);
            return $modules;
        //}
        //return false;
    }

    /**
    * Method to get a list of links for a module.
    * @return $data The modules links.
    */
    function getModuleLinks($filter)
    {
        $sql = 'SELECT * FROM '.$this->table;
        $sql .= " WHERE $filter";
        $data = $this->getArray($sql);

        if(!empty($data)){
            return $data;
        }
        return false;
    }

    /**
    * Method to save new or editted links.
    */
    function saveLinks($fields, $id = NULL)
    {
        if(!$id){
            $this->insert($fields);
        }else{
            $this->update('id', $id, $fields);
        }
    }
}
?>