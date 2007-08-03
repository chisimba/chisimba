<?php

/**
 * Class to show the File Manager Navigation as a tree
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
 * @category  Chisimba
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       
 */


/**
 * Class to show the File Manager Navigation as a tree
 * 
 * @category  Chisimba
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       
 */
class treefilter extends object
{

    
    /**
    * Constructor
    */
    public function init()
    {
        $this->objFile =& $this->getObject('dbfile');
        $this->objMediaFileInfo =& $this->getObject('dbmediafileinfo');
        $this->objUser =& $this->getObject('user', 'security');
		$this->loadClass('treemenu','tree');
		$this->loadClass('treenode','tree');
        $this->loadClass('htmllist','tree');
        $this->objLanguage =& $this->getObject('language', 'language');
        $this->loadClass('dropdown', 'htmlelements');
    }
    
    /**
    * Method to show the Navigation
    */
    public function show()
    {
        $icon         = 'folder.gif';
		$expandedIcon = 'folder-expanded.gif';
        
        $userId = $this->objUser->userId();
        $fullname = $this->objUser->fullname();
        
		//Create a new tree
		$menu  = new treemenu();
        
        $numFiles = $this->objFile->getNumUniqueFiles($userId);
		
		$allFilesNode = new treenode(array('text' => 'All Files ('.$numFiles.')', 'link' => $this->uri(NULL)));
        
        $categories = $this->objFile->getUserCategories($userId);
        
        foreach ($categories as $category)
        {
            $node =& new treenode(array('text' => ucfirst($category['category']), 'link' => $this->uri(array('category'=>$category['category']))));
            
            $allFilesNode->addItem($node);
        }
        
        $menu->addItem($allFilesNode);
        
        $uploadItem = new treenode(array('text' => 'Upload File', 'link' => $this->uri(array('action'=>'uploadfiles'))));
        
        $menu->addItem($uploadItem);
        
        $indexItem = new treenode(array('text' => 'File Indexer', 'link' => $this->uri(array('action'=>'indexfiles'))));
        
        $menu->addItem($indexItem);
        
        $treeMenu = &new htmllist($menu);
        
        //$this->appendArrayVar('headerParams', '<script src="modules/tree/resources/TreeMenu.js" language="JavaScript" type="text/javascript"></script>');
        
        // Check if there are any temporary files
        $objCheckOverwrite = $this->getObject('checkoverwrite');
        
        $title = '<h1>'.htmlentities($fullname."'s Files").'</h1>';
        
        return $title.'<p>'.$objCheckOverwrite->showLink().'</p>'.$treeMenu->getMenu();
    }
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return object Return description (if any) ...
     * @access public
     */
    function showDropDown()
    {
        $dropDown = new dropdown('asfas');
        $dropDown->addOption('asfsa', 'Fix Me - List of FIlters');
        return $dropDown->show();
    }

}

?>