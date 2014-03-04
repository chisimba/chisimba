<?php
/**
* The DBBS postlogin displays the list of project groups / contexts available to users on the site.
* Additionally links to the digital library and conferences are provided.
* 
* @author Megan Watson
* @copyright 2007 University of the Western Cape & AVOIR Project
* @license GNU GPL
* @package dbbspostlogin
*/

// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
    die( "You cannot view this page directly" );
} 
// end security check

/**
 * The DBBS postlogin displays the list of project groups / contexts available to users on the site.
 * Additionally links to the digital library and conferences are provided.
 * 
 * @author Megan Watson
 * @copyright 2007 University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package dbbspostlogin
 */

class dbbspostlogin extends controller 
{
    
    /**
	* Constructor
    */
    public function init()
    {
        $this->dbbsTools = $this->getObject('dbbstools', 'dbbspostlogin');
        
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
    }
    
    /**
     * The standard dispatch function
     */
    public function dispatch($action)
    {
        switch ($action){
            
            default:
                $this->setLayoutTemplate('main_layout_tpl.php');
                $display = $this->dbbsTools->showGroups();
                $this->setVarByRef('display', $display);
                return 'main_tpl.php';
        }
    }
}
?>