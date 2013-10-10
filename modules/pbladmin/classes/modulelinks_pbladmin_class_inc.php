<?php
/**
* Class modulelinks_pbladmin extends object.
* @package pbladmin
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die('You cannot view this page directly');
}
// end security check

 /**
 * Class providing links within the pbl admin module to all modules in the context
 *
 * @author Megan Watson
 * @copyright (c) 2007 UWC
 * @package pbladmin
 * @version 0.1
 */

class modulelinks_pbladmin extends object
{
    /**
    * Constructor method to initialise objects
    *
    * @access public
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->loadClass('treenode','tree');
    }
    
    /**
    * Method to display the links as a tree
    *
    * @access public
    */
    public function show()
    {
        $lbPbladmin = $this->objLanguage->languageText('mod_pbladmin_pbladmin', 'pbladmin');
        $rootNode = new treenode (array('link'=>$this->uri(NULL, 'pbladmin'), 'text'=>$lbPbladmin, 'preview'=>''));
        
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
        $lbPbladmin = $this->objLanguage->languageText('mod_pbladmin_pbladmin', 'pbladmin');
                
        $adminArr = array();
        $adminArr['menutext'] = $lbPbladmin;
        $adminArr['description'] = $lbPbladmin;
        $adminArr['itemid'] = '';
        $adminArr['moduleid'] = 'pbladmin';
        $adminArr['params'] = array();
        
        $returnArr = array();
        $returnArr[] = $adminArr;
        
        return $returnArr;
    }
}
?>