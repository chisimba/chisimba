<?php

/**
 * This class contains utilities for doing common functions in apo
 *  PHP version 5
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
 * @package   apo (document management system)
 * @author    Nguni Phakela, david wafula
 * @copyright 2010
  =
 */
if (!
        /**
         * Description for $GLOBALS
         * @global string $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

class userutils extends object {

    var $heading = "Document Management System";

    /*
     * Constructor
     *
     */
    public function init() {
        //instantiate the language object
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('treemenu', 'tree');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('treenode', 'tree');
        $this->loadClass('htmllist', 'tree');
        $this->loadClass('htmldropdown', 'tree');
        $this->loadClass('dhtml', 'tree');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objIcon = $this->getObject('geticon', 'htmlelements');
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $this->resourcePath = $this->objAltConfig->getModulePath();
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->rootTitle = $this->objSysConfig->getValue('ROOT_TITLE', 'apo');
        $this->baseDir = $this->objSysConfig->getValue('FILES_DIR', 'apo');
        $this->modeLabel = $this->objSysConfig->getValue('MODE_LABEL', 'apo');
    }

    /*
     * This method is used to display the heading on a page, by making the first
     * letter a capital letter
     * @param string $page The string that contains the current page in view
     * @access public
     * @return string $heading. This is the heading of the page, with the first letter
     * being upper case.
     */
     public function showPageHeading($page=null) {

        if ($page != null) {
            return $this->heading . " - " . ucfirst($page);
        } else {
            return $this->heading;
        }
    }

    /*
     * This method is used to construct the tree for the side navigation and also for select
     * input type navigation
     * @param $treeType
     * @param $selected The currently selected tree node, when navigating.
     * @param $treemode
     * @param $action 
     * @access public
     * @return The tree that is being used for navigation
     */
    public function getTree($treeType='dhtml', $selected='', $treeMode='side', $action='') {
        $baseFolder = $this->objSysConfig->getValue('FILES_DIR', 'apo');
        
        $rolesArray = array('apo'=>'APO','subsidy'=>'Subsidy Office', 'library'=>'Library', 'faculty'=>'Faculty Registrar', 'legal' => 'Legal Office');
        $baseFolderId = "0";
        
        $icon = '';
        $expandedIcon = '';
        $cssClass = '';

        if ($treeType == 'htmldropdown') {
            $allFilesNode = new treenode(array('text' => $this->modeLabel, 'link' => $baseFolderId));
        } else {
            $allFilesNode = new treenode(array('text' => $this->modeLabel, 'link' => "#", 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
        }
        $documents = $this->getObject('dbdocuments');
        $count = $documents->getUnapprovedDocsCount();
        $faculty = $this->getObject('dbfaculties');

        $this->objUsers = $this->getObject('dbapousers');
        $userCount = $this->objUsers->getNumUsers();

        $facultyCount = count($faculty->getFaculties());
        $faculties = $faculty->getFaculties();
        if ($treeMode == 'side') {
            $unapprovedDocs = "$count New Course Proposals";
            if ($selected == 'unapproved') {
                $unapprovedDocs = '<strong>' . $unapprovedDocs . '</strong>';
                $cssClass = 'confirm';
            } else {
                $cssClass = '';
            }

            $userManagement = "$userCount Users";
            if ($selected == 'usermanagement') {
                $userManagement = '<strong>' . $userManagement . '</strong>';
                $cssClass = 'confirm';
            } else {
                $cssClass = '';
            }


            $newDocsNode = new treenode(array('text' => $unapprovedDocs, 'link' => $this->uri(array('action' => 'unapproveddocs')), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
            $newUserNode = new treenode(array('text' => $userManagement, 'link' => $this->uri(array('action' => 'usermanagement')), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));

            foreach($rolesArray as $key=>$value) {
                $roleNode = &new treenode(array(
                                           'text' => $value,
                                           'link' => $this->uri(array('action' => 'usermanagement', 'role' => $key)),
                                           'icon' => $icon,
                                           'expandedIcon' => $expandedIcon,
                                           'cssClass' => $cssClass));
                $newUserNode->addItem($roleNode);
            }

            $facultyManagement = "$facultyCount Faculty Management";
            if ($selected == 'facultymanagement') {
                $facultyManagement = '<strong>' . $facultyManagement . '</strong>';
                $cssClass = 'confirm';
            } else {
                $cssClass = '';
            }

            if ($treeType == 'htmldropdown') {
                $facultyManagementNode = new treenode(array('text' => $facultyManagement, 'link' => '-1'));
            }
            else {
                $facultyManagementNode = new treenode(array('text' => $facultyManagement, 'link' => $this->uri(array('action' => 'facultymanagement')), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
            }

            if ($treeType != 'htmldropdown') {
                $allFilesNode->addItem($newDocsNode);
                $allFilesNode->addItem($newUserNode);
            }
            $allFilesNode->addItem($facultyManagementNode);
        }

        //Create a new tree
        $menu = new treemenu();

        $icon = 'folder.gif';
        $expandedIcon = 'folder-expanded.gif';

        $refArray = array();
        $refArray[$this->rootTitle] = & $facultyManagementNode;
        
        if (count($faculties) > 0) {
            foreach ($faculties as $row) {
                $folderText = $row['name'];


                $folderShortText = substr($row['name'], 0, 200) . '...';
                if ($row['name'] == $selected) {
                    $folderText = '<strong>' . $folderText . '</strong>';
                    $cssClass = 'confirm';
                } else {
                    $cssClass = '';
                }
                if ($treeType == 'htmldropdown') {
                    $node = & new treenode(array('title' => $folderText, 'text' => $folderShortText, 'link' => $row['id'], 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
                } else {
                    $node = & new treenode(array('title' => $folderText, 'text' => $folderShortText, 'link' => $this->uri(array('action' => 'home', 'facultyid' => $row['id'], 'facultyname' => $row['name'])), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
                }

                $parent = $this->getParent($row['path']);
                if (array_key_exists($parent, $refArray)) {
                    $refArray[$parent]->addItem($node);
                }

                $refArray[$row['path']] = & $node;
            }
        }

        $menu->addItem($allFilesNode);
        if ($treeType == 'htmldropdown') {
            $treeMenu = &new htmldropdown($menu, array('inputName' => 'parentfolder', 'id' => 'input_parentfolder', 'selected' => $selected));
        } else {
            $this->appendArrayVar('headerParams', $this->getJavascriptFile('TreeMenu.js', 'tree'));
            $this->setVar('pageSuppressXML', TRUE);
            $objSkin = & $this->getObject('skin', 'skin');
            $treeMenu = &new dhtml($menu, array('images' => 'skins/_common/icons/tree', 'defaultClass' => 'treeMenuDefault'));
        }

        return $treeMenu->getMenu();
    }

    public function showCreateFolderForm($folderPath, $selected) {
        if ($folderPath == FALSE) {
            return '';
        }

        $folderParts = explode('/', $folderPath);

        $form = new form('createfolder', $this->uri(array('action' => 'createfolder')));

        $label = new label('Create a ' . $this->modeLabel . ' in: ', 'input_parentfolder');


        $form->addToForm($label->show() . $this->getTree('htmldropdown', $selected));

        // $objInputMasks = $this->getObject('inputmasks', 'htmlelements');
        // echo $objInputMasks->show();

        $textinput = new textinput('foldername');
        //$textinput->setCss('text input_mask anytext');

        $label = new label('Name of ' . $this->modeLabel . ': ', 'input_foldername');

        $form->addToForm(' &nbsp; ' . $label->show() . $textinput->show());

        $button = new button('create', 'Create ' . $this->modeLabel);
        $button->setToSubmit();

        $form->addToForm(' ' . $button->show());

        return $form->show();
    }

    /*
     * This is a helper method that is used to determine the parent of the current
     * node in a tree.
     * @param $path The expanded path of the current node
     * @access public
     * @return the parent of the deepest child node.
     */
    public function getParent($path) {

        $parent = "";
        $parts = explode("/", $path);
        $count = count($parts);
        for ($i = 0; $i < $count - 1; $i++) {
            if ($parent == '') {
                $parent.= $parts[$i];
            } else {
                $parent.="/" . $parts[$i];
            }
        }
        if ($parent == '') {
            $parent = $this->rootTitle;
        }
        return $parent;
    }

}

?>