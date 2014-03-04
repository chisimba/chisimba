<?php
/**
* Tools class for the DBBS project providing various functions
*/

// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
} 
// end security check

/**
 * Tools class for the DBBS project providing various functions
 * 
 * @author Megan Watson
 * @copyright 2007, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package dbbspostlogin
 */

class dbbstools extends object 
{
    
    /**
    * The constructor
    */
    public function init()
    {
        $this->objBlocks = $this->getObject('blocks', 'blocks');
    }
    
    /**
    * Method to display the left menu items
    *
    * @access public
    * @return string html
    */
    public function leftMenu()
    {
        if(isset($this->leftMenu) && !empty($this->leftMenu)){
            return $this->leftMenu;
        }
        
        $dbbsLibTools = $this->getObject('dbbslibtools', 'dbbslibrary');
        $access = $dbbsLibTools->setGroupPermissions();
        
        $blBrowse = $this->objBlocks->showBlock('browselibrary', 'dbbslibrary', '','','', FALSE);
        
        if(isset($access) && is_array($access)){
            if(in_array('manager', $access) || in_array('editor', $access)){
                $blBrowse .= $this->objBlocks->showBlock('managelibrary', 'dbbslibrary', '','','', FALSE);
            }
        }
        
        $str = $blBrowse;
        $str .= '<br />';
        return $str;
    }
    
    /**
    * Method to replace the left menu
    *
    * @access public
    * @param string $leftSide The replacement menu items
    * @return void
    */
    public function setLeftSide($leftSide)
    {
        $this->leftMenu = $leftSide;
    }
    
    /**
    * Method to display the groups box for the default front page of the module
    *
    * @access public
    * @return string html
    */
    public function showGroups()
    {
        $objContextUtils = $this->getObject('utils', 'contextpostlogin');
        $objContextUtils->setModule('dbbspostlogin');
        $filter = $this->getParam('filter');
        return $objContextUtils->showBox($filter);
    } 
}	
?>