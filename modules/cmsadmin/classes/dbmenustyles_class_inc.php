<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* Data access class for the cmsadmin module. Used to access data in the menu styles table. 
*
* @package cmsadmin
* @category chisimba
* @copyright AVOIR 
* @license GNU GPL
* @author Megan Watson
*/

class dbMenuStyles extends dbTable
{
    /**
    * @var string $table The name of the table being accessed
    * @access private
    */
    private $table;

    /**
	* Class Constructor
	*
	* @access public
	* @return void
	*/
    public function init()
    {
        try {
            parent::init('tbl_cms_menustyles');
            $this->table = 'tbl_cms_menustyles';
        } catch (Exception $e){
            throw customException($e->getMessage());
            exit();
        }
    }
    
    /**
    * Method to get the current style
    *
    * @access public
    * return string The style
    */
    public function getActive()
    {
        $sql = "SELECT menu_style FROM {$this->table}
        WHERE is_active = '1'";
        
        $data = $this->getArray($sql);
        if(!empty($data)){
            return $data[0]['menu_style'];
        }
        return 'tree'; // Return the default style = the tree menu
    }
    
    /**
    * Method to get the different styles
    *
    * @access public
    * return array The styles
    */
    public function getStyles()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY menu_style";
        
        $data = $this->getArray($sql);
        return $data;
    }
    
    /**
    * Method to update the currently active style
    *
    * @access public
    * @param string $id The id of the new style
    * @return void
    */
    public function updateActive($id)
    {
        $current = $this->getActive();
        $this->update('menu_style', $current, array('is_active' => 0));
        $this->update('id', $id, array('is_active' => 1));
    }
}
?>