<?php
/**
* Class modulelinks_pbl extends object.
* @package pbl
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die('You cannot view this page directly');
}
// end security check

 /**
 * Class providing links within the pbl module to all modules in the context
 *
 * @author Megan Watson
 * @copyright (c) 2007 UWC
 * @package pbladmin
 * @version 0.1
 */

class modulelinks_pbl extends object
{
    /**
    * Constructor method to initialise objects
    *
    * @access public
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objUser = $this->getObject('user', 'security');
        $this->loadClass('treenode','tree');
        
        $this->userId = $this->objUser->userId();
    }
    
    /**
    * Method to display the links as a tree
    *
    * @access public
    */
    public function show()
    {
        $lbPbl = $this->objLanguage->languageText('mod_pbl_pbl', 'pbl');
        $rootNode = new treenode (array('link'=>$this->uri(NULL, 'pbl'), 'text'=>$lbPbl, 'preview'=>''));
        
        return $rootNode;
    }
    
    /**
    * Method to get a set of links for a context
    *
    * @access public
    * @param string $contextCode
    * @return array
    */
    public function getContextLinks($contextCode)
    { 
        $lbPbl = $this->objLanguage->languageText('mod_pbl_pbl', 'pbl');
                
        $adminArr = array();
        $adminArr['menutext'] = $lbPbl;
        $adminArr['description'] = $lbPbl;
        $adminArr['itemid'] = '';
        $adminArr['moduleid'] = 'pbl';
        $adminArr['params'] = array();
        
        $returnArr = array();
        $returnArr[] = $adminArr;
        
        return $returnArr;
    }
}
?>