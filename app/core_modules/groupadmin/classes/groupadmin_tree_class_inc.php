<?php
/**
 * groupadmin_tree class
 * 
 * PHP version 5
 *  
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * 
 * @category  Chisimba
 * @package   groupadmin
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}


/**
* @copyright  (c) 2000-2004, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package    groupadmin
* @subpackage view
* @version    0.1
* @since      22 November 2004
* @author     Jonathan Abrahams
* @filesource
*/
/**
* Uses classes for the groupadmin tree class.
* @package groupadmin
* @author  Jonathan Abrahams
*/
class groupadmin_tree extends object {
    /**
    * @var groupAdminModel an object reference.
    */
    var $_objGroupAdminModel;
    /**
    * @var treeMenu an object reference.
    */
    var $_objTreeMenu;
    /**
    * @var array extra tree options for display.
    */
    var $_extra;
    /**
    * @var string Group Id of root node.
    */
    var $_rootNode;
    
    /**
    * @var true|false Enable/Disabel context sensistive tree.
    */
    var $_contextSensitive;
    
    /**
    * @var string Location of tree icons.
    */
    var $_treeIcons='';
    /**
    * @var array Icons for the tree.
    */
    var $_arrTreeIcons=array();
    
    /**
    * @var string Module Link for Trees
    */
    var $treeTargetModule = 'groupadmin';
    
    /**
    * @var string Action Link for Trees
    */
    var $treeTargetAction = 'main';
    
    /**
    * @var string Additional Id Parameter for Trees
    */
    var $treeTargetId = NULL;
    
    /**
    * @var string Target Window for Trees
    */
    var $treeTargetWindow = NULL;

    /**
    * Method to initialize the object.
    */
    function init()
    {
        $this->loadClass('treemenu','tree');
        $this->loadClass('treenode','tree');
        $this->loadClass('dhtml','tree');
        $this->loadClass('listbox','tree');
        $this->loadClass('tree_dropdown','groupadmin');
        
        // Initialise icons
        $this->objSkin = $this->getObject( 'skin', 'skin' );
        $this->_treeIcons = 'icons/tree/';

        $this->_arrTreeIcons = array();
        $this->_arrTreeIcons['root'] = 'treebase';
        $this->_arrTreeIcons['empty']['selected'] = 'treefolder-selected_white';
        $this->_arrTreeIcons['empty']['open'] = 'treefolder-expanded_white';
        $this->_arrTreeIcons['empty']['closed'] = 'treefolder_white';
        $this->_arrTreeIcons['context']['selected'] = 'treefolder-selected_green';
        $this->_arrTreeIcons['context']['open'] = 'treefolder-expanded_green';
        $this->_arrTreeIcons['context']['closed'] = 'treefolder_green';
        $this->_arrTreeIcons['members']['selected'] = 'treefolder-selected_orange';
        $this->_arrTreeIcons['members']['open'] = 'treefolder-expanded_orange';
        $this->_arrTreeIcons['members']['closed'] = 'treefolder_orange';

        // Enable/Disable Context sensitive tree
        $this->_contextSensitive = false;
        
        $this->_objGroupAdminModel = $this->getObject( 'groupAdminModel', 'groupadmin' );
        
        // Load Language Class
        $this->objLanguage = $this->getObject('language', 'language');
        
        // Create an array of words to abstract
        $this->abstractionArray = array(
                'Lecturers'=>ucwords($this->objLanguage->code2Txt('word_lecturers')), 
                'Students'=>ucwords($this->objLanguage->code2Txt('word_students'))
            );
        
        
        $this->createTreeMenu();
    }

    /**
    * Method to get the tree menu object
    * @access public
    * @return object reference
    */
    function &getTreeMenu()
    {
        return $this->_objTreeMenu;
    }
    
    /**
    * Method to get the array of extra options
    * @access public
    * @return array  reference
    */
    function &getExtra()
    {
        return $this->_extra;
    }
    
    /**
    * Method to create a root node.
    * @access private
    * @return object  reference
    */
    function &createRootNode()
    {
        // Context Aware;
        $icons = $this->_arrTreeIcons;
        $objDBContext = $this->getObject('dbcontext','context');
        if( $this->_contextSensitive && $objDBContext->isInContext()){
            $contextCode = $objDBContext->getContextCode();
            $groupId = $this->_objGroupAdminModel->getLeafId(array($contextCode));
            $link    = $this->uri( array( 'action'=> $this->treeTargetAction, 'groupId' => $groupId ), $this->treeTargetModule );
            $this->_rootNode = $groupId;
            return new treenode( array (
                'text' => '<STRONG> '.$contextCode.'</STRONG>',
                'link'         => $link,
                'value'        => $groupId,
                'icon' => $icons['root'].'.gif' ));
        } else {
            $this->_rootNode = NULL;
            $treenode = new treenode( array ( 'text' => '<STRONG>Groups</STRONG>', 'icon' => $icons['root'].'.gif' ));
            return $treenode;
        }
    }

    /**
    * Method to create a tree menu object.
    * @access private
    * @return nothing
    */
    function createTreeMenu()
    {
        $this->_objTreeMenu =& new treemenu();
        
        $rootMenu = &$this->createRootNode();
        $this->_objTreeMenu->addItem( $rootMenu );
        
        $this->recureTree( $this->_rootNode, $rootMenu );
    }
    
    /**
    * Method to create a tree node.
    * @param  array   contains the current group row.
    * @access private
    * @return object  reference
    */
    function &createTreeNode( &$row )
    {
        // Initialize locals
        $icons = $this->_arrTreeIcons;
        $groupId   = $row['id'];
        
        $groupName = $row['name'];
        
        foreach ($this->abstractionArray as $name=>$value)
        {
            $groupName = str_replace($name, $value, $groupName);
        }
        
        $model =& $this->_objGroupAdminModel;

        // Create clickable links on the tree
        $link      = $this->uri( array( 'action'=> $this->treeTargetAction, 'groupId' => $groupId, 'id'=>$this->treeTargetId ), $this->treeTargetModule );
        
        // Get the list of group members
        $users = $model->getSubGroupUsers( $groupId );
        
        // Check if an empty group
        $isEmptyFolder = (count( $users )) == 0;

        // Selected node
        $objGA = &$this->_objGroupAdminModel;
        $isSelected = $this->getParam('groupId') == $groupId;
        
        // Check if it is a context
        $objContext = &$this->getObject('dbcontext','context');
        $isContext = $objContext->valueExists('contextCode', $groupName );
        if( $isContext ) {
            $icon   = $icons['context']['closed'].'.gif'; // closed folder
            $expand = $icons['context']['open'].'.gif'; // expanded folder
            $groupName = $objContext->getTitle( $groupName );
        } else {
            // empty folder
            if( $isEmptyFolder ){
                $icon   = $isSelected ? $icons['empty']['selected'].'.gif': $icons['empty']['closed'].'.gif';// Empty closed folder
                $expand = $icons['empty']['open'].'.gif';// Empty expanded folder
            } else {
                $icon   = $isSelected ? $icons['members']['selected'].'.gif' : $icons['members']['closed'].'.gif' ;// Empty closed folder
                $expand = $icons['members']['open'].'.gif';// Empty expanded folder
            }
        }

   $treenode = new treenode(  array (
                    'text'         => $groupName,
                    'link'         => $link,
                    'value'        => $groupId,
                    'icon'         => $icon,
                    'expandedIcon' => $expand,
                    'cssClass'     => '',
                    'linkTarget'   => $this->treeTargetWindow
                ));
             return $treenode;
    }

    /**
    * Method to show the tree as a list box.
    * @access public
    * @return string the HTML output
    */
    function showListbox()
    {
        $this->_extra = array(
            'images' => $this->objSkin->getCommonSkinURL().$this->_treeIcons,
            'defaultClass' => 'treeMenuDefault' );
        $listboxMenu = new listbox( $this->_objTreeMenu, $this->_extra );
        return $listboxMenu->getMenu();
    }

    /**
    * Method to show the tree as a drop down.
    * @access public
    * @return string the HTML output
    */
    function showDropdown()
    {
        $this->_extra = array(
            'images' => $this->objSkin->getCommonSkinURL().$this->_treeIcons,
            'defaultClass' => 'treeMenuDefault' );

        $dropdownMenu = new tree_dropdown( $this->_objTreeMenu, $this->_extra );
        return $dropdownMenu->getMenu();
    }

    /**
    * Method to show the tree as a tree like structure.
    * @access public
    * @return string the HTML output
    */
    function showDHTML()
    {
        $this->_extra = array(
            'images' => $this->objSkin->getCommonSkinURL().$this->_treeIcons,
            'defaultClass' => 'treeMenuDefault' );

        $dhtmlMenu = new dhtml( $this->_objTreeMenu, $this->_extra );
        
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('TreeMenu.js','tree'));
        
        return $dhtmlMenu->getMenu();
    }
    /**
    * Method to build the tree structure.
    * @access private
    * @return nothing
    */
    function recureTree( &$node, &$parentNode )
    {
        $objGA = &$this->_objGroupAdminModel;
        $menu  = &$this->_objTreeMenu;

        $isRoot = is_null( $node );
        $result = ( $isRoot  ) ? $objGA->getRoot( ) : $objGA->getChildren( $node );

        if ( $result ) {
            foreach ( $result as $row ) {
                if( $this->isVisible( $row ) ) {
                    $newNode = &$this->createTreeNode( $row );
                    if  ( is_null( $parentNode ) ) {
                        $menu->addItem( $newNode );
                    } else {
                        $parentNode->addItem( $newNode );
                    }
                    $this->recureTree( $row['id'], $newNode );
                }
            }
        }
    }
    
    /**
    * Method to test if the group row is visible.
    * @access private   
    * @return TRUE|FALSE
    */
    function isVisible( $row )
    {
        return TRUE;
    }
}
?>
