<?php

/**
 * Class to handle interaction with table tbl_files_folders
 *
 * This table lists all folders that are created on the system
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
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see
 */

/**
 * Class to handle interaction with table tbl_files_folders
 *
 * This table lists all folders that are created on the system
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
class dbfolder extends dbTable {

    /**
     * Constructor
     */
    public function init() {
        parent::init('tbl_files_folders');

        $this->objFiles = $this->getObject('dbfile');
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objCleanUrl = $this->getObject('cleanurl');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->objContext->getContextCode();
        $this->objLanguage = $this->getObject('language', 'language');
        $this->loadClass('treemenu', 'tree');
        $this->loadClass('treenode', 'tree');
        $this->loadClass('htmllist', 'tree');
        $this->loadClass('htmldropdown', 'tree');
        $this->loadClass('dhtml', 'tree');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
    }

    /**
     * Method to check whether a folder is in the database record or not.
     * If it is not, add it to the database
     * @param  string  $folder     Path to Folder
     * @param  boolean $isFullPath Is it the full path of the folder, or just the part of usrfiles/
     * @return string  Record Id
     */
    public function indexFolder($folder, $isFullPath = TRUE) {
        // Convert all backslashes to forward slashes
        // Convert multiple forward slashes to single
        $folder = preg_replace('/(\/|\\\)+/', '/', $folder);

        // If it is the full path to the file
        if ($isFullPath) {
            // Remove the path upto userfiles
            // Eg. removes /htdocs/chisima_framework/app/usrfiles/
            $folder = preg_replace('/\\A(.)*?usrfiles\//', '', $folder);
        }

        // Remove the Slash at the end if there is one
        $folder = preg_replace('/\/$/', '', $folder);

        if (!$this->valueExists('folderpath', $folder)) {
            return $this->addFolder($folder);
        } else {
            return $folder['id'];
        }
    }

    /**
     * Method to check whether a folder is in the database record or not.
     * If it is, update it to the database
     * @param  string  $folder     Path to Folder
     * @param  boolean $isFullPath Is it the full path of the folder, or just the part of usrfiles/
     * @return string  Record Id
     */
    public function updateFolderIndex($oldPath, $newPath, $isFullPath = TRUE) {
        // Convert all backslashes to forward slashes
        // Convert multiple forward slashes to single
        $oldPath = preg_replace('/(\/|\\\)+/', '/', $oldPath);
        $newPath = preg_replace('/(\/|\\\)+/', '/', $newPath);

        // If it is the full path to the file
        if ($isFullPath) {
            // Remove the path upto userfiles
            // Eg. removes /htdocs/chisima_framework/app/usrfiles/
            $oldPath = preg_replace('/\\A(.)*?usrfiles\//', '', $oldPath);
        }


        // If it is the full path to the file
        if ($isFullPath) {
            // Remove the path upto userfiles
            // Eg. removes /htdocs/chisima_framework/app/usrfiles/
            $newPath = preg_replace('/\\A(.)*?usrfiles\//', '', $newPath);
        }

        // Remove the Slash at the end if there is one
        $oldPath = preg_replace('/\/$/', '', $oldPath);
        $newPath = preg_replace('/\/$/', '', $newPath);
        if ($this->valueExists('folderpath', $oldPath)) {
            return $this->updateFolderDetails($oldPath, $newPath);
        }
    }

    /**
     * Method to add a folder to the database records
     * @param  string $folder Path to the folder
     * @return string Record Id
     */
    private function updateFolderDetails($oldPath, $newPath) {
        return $this->update('folderpath', $oldPath, array('folderpath' => $newPath, 'folderlevel' => count(explode('/', $newPath))));
    }

    /**
     * Method to add a folder to the database records
     * @param  string $folder Path to the folder
     * @return string Record Id
     */
    private function addFolder($folder) {
        return $this->insert(array('folderpath' => $folder, 'folderlevel' => count(explode('/', $folder))));
    }

    /**
     * Method to override the uri function to include automatic inclusion
     * of mode and restriction
     *
     * @access  public
     * @param   array  $params         Associative array of parameter values
     * @param   string $module         Name of module to point to (blank for core actions)
     * @param   string $mode           The URI mode to use, must be one of 'push', 'pop', or 'preserve'
     * @param   string $omitServerName flag to produce relative URLs
     * @param   bool   $javascriptCompatibility flag to produce javascript compatible URLs
     * @returns string $uri the URL
     */
    public function uri($params = array(), $module = '', $mode = '', $omitServerName = FALSE, $javascriptCompatibility = FALSE) {
        $objFileManagerObject = $this->getObject('filemanagerobject');
        return $objFileManagerObject->uri($params, $module, $mode, $omitServerName, $javascriptCompatibility);
    }

    /**
     *
     *
     */
    function getFolders($type, $id) {
        return $this->getAll(' WHERE folderpath LIKE \'' . $type . '/' . $id . '/%\' ORDER BY folderlevel, folderpath');
    }

    /**
     *
     *
     */
    function getAllFolders($type, $id, $userId) {
        return $this->getAll(' WHERE folderpath LIKE \'users/' . $userId . '/%\' OR folderpath LIKE \'' . $type . '/' . $id . '/%\' ORDER BY folderlevel, folderpath');
    }

    /**
     * Method to generate a folder tree
     * @param string $folderType Type of Folders - either users, context, workgroup, or group
     * @param string $id Either User Id of Context Code
     * @param string $treeType Type of Tree - Either dhtml or htmldropdown
     * @param string $selected Record Id of default selected node
     */
    function getTree($folderType = 'users', $id, $treeType = 'dhtml', $selected = '') {
        //Create a new tree
        //Create the variable to contain the link's title text and assign it the value [ Contains folder(s) and file(s) ]
        $titleAttr; // = $this->objLanguage->languageText("mod_filemanager_contentsindicator","filemanager");
        $icon = 'folder.gif';
        $menu = new treemenu();
        $objFile = $this->getObject("dbfile");
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objFiles = $this->getObject('dbfile', 'filemanager');
        $expandedIcon = 'folder-expanded.gif';
        $baseFolder = $folderType . '/' . $id;
        $baseFolderId = $this->getFolderId($baseFolder);

        if ($baseFolderId == $selected) {
            $folderText = '<strong>' . $this->getFolderType($folderType, $id) . '</strong>';
            $cssClass = 'confirm';
        } else {
            $folderText = $this->getFolderType($folderType, $id);
            $cssClass = '';
        }

        $nmbrOfFiles = count($objFile->getUserFiles($id));
        $nmbrOfFolders = count($this->getUserFolders($id));

        //TODO: make this a reusable function
        //For parent folder
        if ($nmbrOfFiles == 0 && $nmbrOfFolders == 0) {
            $titleAttr = $this->objLanguage->languageText("mod_filemanager_emptyfolderindicator", "filemanager");
        } else {
            $titleAttr = str_replace(" ", "_", $this->objLanguage->languageText("mod_filemanager_contentsindicator", "filemanager"));
            $titleAttr = substr($titleAttr, 0, 9) . $nmbrOfFolders . "_" . substr($titleAttr, 9, 9) . "_" . $nmbrOfFiles . "_" . substr($titleAttr, 19, 11);
        }

        if ($treeType == 'htmldropdown') {
            $allFilesNode = new treenode(array('text' => strip_tags($folderText), 'link' => $baseFolderId));
        } else {
            $allFilesNode = new treenode(array('text' => $folderText, 'link' => $this->uri(array('action' => 'viewfolder', 'folder' => $baseFolderId)), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass, 'titleattr' => $titleAttr));
        }

        $refArray = array();
        $refArray[$baseFolder] = & $allFilesNode;
        $folders = $this->getFolders($folderType, $id);

        if (count($folders) > 0) {
            foreach ($folders as $folder) {
                //TODO: make this a reusable function
                $nmbrOfFiles = count($objFile->getFolderFiles($folder['folderpath']));
                $nmbrOfFolders = count($this->getSubFolders($folder['id']));
                if ($nmbrOfFiles == 0 && $nmbrOfFolders == 0) {
                    $titleAttr = $this->objLanguage->languageText("mod_filemanager_emptyfolderindicator", "filemanager");
                } else {
                    $titleAttr = str_replace(" ", "_", $this->objLanguage->languageText("mod_filemanager_contentsindicator", "filemanager"));
                    $titleAttr = substr($titleAttr, 0, 9) . $nmbrOfFolders . "_" . substr($titleAttr, 9, 9) . "_" . $nmbrOfFiles . "_" . substr($titleAttr, 19, 11);
                }

                $extTitle = '';
                $access = null;

                if (key_exists("access", $folder)) {
                    $access = $folder['access'];
                }
                if ($access == 'private_all') {
                    $objIcon->setIcon('info');
                    $extTitle = $objIcon->show();
                }
                $folderText = basename($folder['folderpath']) . $extTitle;
                $folderShortText = substr(basename($folder['folderpath']), 0, 60) . '...' . $extTitle;

                if ($folder['id'] == $selected) {
                    $folderText = '<strong>' . $folderText . '</strong>';
                    $cssClass = 'confirm';
                } else {
                    $cssClass = '';
                }

                if ($treeType == 'htmldropdown') {
                    $node = & new treenode(array('title' => $folderText, 'text' => $folderShortText, 'link' => $folder['id'], 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass));
                } else {
                    $node = & new treenode(array('title' => $folderText, 'text' => $folderShortText, 'link' => $this->uri(array('action' => 'viewfolder', 'folder' => $folder['id'])), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass' => $cssClass, 'titleattr' => $titleAttr));
                }


                $parent = dirname($folder['folderpath']);

                if (array_key_exists($parent, $refArray)) {
                    $refArray[dirname($folder['folderpath'])]->addItem($node);
                }

                $refArray[$folder['folderpath']] = & $node;
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

    function getFolderType($folderType, $id) {
        switch ($folderType) {
            case 'users':
                if ($id == $this->objUser->userId()) {
                    $title = $this->objLanguage->languageText('mod_filemanager_myfiles', 'filemanager', 'My Files');
                } else {
                    // Detect whether folder is public
                    $title = $this->objUser->fullName($id) . "'s Files";
                }
                break;
            case 'context': // fix up here
                $objContext = $this->getObject('dbcontext', 'context');
                $title = $objContext->getTitle() . ' - Files';
                break;
            default:
                $title = 'unknown';
                break;

                return $title;
        }

        return $title;
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  string $userId Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
    function getUserFolders($userId) {
        return $this->getAll(' WHERE folderpath LIKE \'users/' . $userId . '/%\' ORDER BY folderlevel, folderpath');
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $id Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    function getFolder($id) {
        return $this->getRow('id', $id);
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $path Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    function getFolderId($path) {
        $folder = $this->getRow('folderpath', $path);

        if ($folder == FALSE) {
            return FALSE;
        } else {
            return $folder['id'];
        }
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $id Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    function getFolderName($id) {
        $folder = $this->getRow('id', $id);

        if ($folder == FALSE) {
            return FALSE;
        } else {
            return basename($folder['folderpath']);
        }
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $id Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    function getFolderPath($id) {
        $folder = $this->getRow('id', $id);

        if ($folder == FALSE) {
            return FALSE;
        } else {
            return $folder['folderpath'];
        }
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $id Parameter description (if any) ...
     * @return boolean Return description (if any) ...
     * @access public
     */
    function getFullFolderPath($id) {
        $folder = $this->getRow('id', $id);

        if ($folder == FALSE) {
            return FALSE;
        } else {
            $path = $this->objConfig->getcontentBasePath() . $folder['folderpath'];

            $path = $this->objCleanUrl->cleanUpUrl($path);

            return $path;
        }
    }

    /**
     * this returns full folder info for a given folder path
     * @param type $path The path to the folder
     */
    public function getFolderByPath($path) {
        //remove the trailing '/' if any
        $len = strlen($path);
        //do we have a trailing '/'?
        $lastChar = substr($path, -1);
        if ($lastChar == '/') {
            $path = substr($path, 0, $len - 1);
        }
      
        $folder = $this->getRow('folderpath',$path);
        return $folder;
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $id Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    public function getSubFolders($id) {
        $folder = $this->getFolder($id);

        if ($folder == FALSE) {
            return FALSE;
        } else {
            return $this->_getSubFolders($folder['folderpath'], $folder['folderlevel']);
        }
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $folderPath Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    public function getSubFoldersFromPath($folderPath) {
        $folder = $this->getFolder($this->getFolderId($folderPath));
        if ($folder == FALSE) {
            return FALSE;
        } else {
            return $this->_getSubFolders($folder['folderpath'], $folder['folderlevel']);
        }
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  string  $path  Parameter description (if any) ...
     * @param  unknown $level Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access private
     */
    private function _getSubFolders($path, $level) {
        return $this->getAll(' WHERE folderpath LIKE \'' . $path . '/%\' AND folderlevel = ' . ($level + 1) . ' ORDER BY folderpath');
    }

    /**
     * Method to convert a user path into a set of breadcrumbs
     * @param string $path Folder Path
     * @return string Generated Breadcrumbs
     */
    public function generateBreadCrumbs($path, $linkLast = FALSE) {
        $parts = explode('/', $path);

        switch ($parts[0]) {

            case 'users':
                if ($parts[1] == $this->objUser->userId()) {
                    $title = $this->getFolderType($parts[0], $parts[1]);
                    $href = $this->uri(NULL, 'filemanager');
                } else {
                    // Detect whether folder is public
                    $title = $this->getFolderType($parts[0], $parts[1]);
                    $href = $this->uri(array('action' => 'viewfolder', 'folder' => $this->getFolderId('users/' . $parts[1])), 'filemanager');
                }
                break;
            case 'context': // fix up here
                $title = $this->getFolderType($parts[0], $parts[1]);
                $href = $this->uri(array('action' => 'viewfolder', 'folder' => $this->getFolderId('context/' . $parts[1])), 'filemanager');
                break;
            case 'digitallibrary': // fix up here
                $title = 'Digital Library';
                $href = $this->uri(array('action' => 'home'), 'digitallibrary');
                break;

            default:
                $title = 'unknown';
                $href = $this->uri(NULL, 'filemanager');
                break;
        }


        $breadcrumbs = array();
        $breadcrumbs[] = array('link' => $href, 'title' => $title);

        if (count($parts) > 2) {

            $current = $parts[0] . '/' . $parts[1];

            for ($i = 2; $i <= (count($parts) - 1); $i++) {
                $current .= '/' . $parts[$i];
                $folderId = $this->getFolderId($current);

                if ($folderId == FALSE) {
                    
                } else {
                    $href = $href = $this->uri(array('action' => 'viewfolder', 'folder' => $folderId), 'filemanager');
                    $breadcrumbs[] = array('link' => $href, 'title' => $parts[$i]);
                }
            }
        }

        $breadcrumbStr = '';

        if ($linkLast) {
            foreach ($breadcrumbs as $breadcrumb) {
                $link = new link($breadcrumb['link']);
                $link->link = $breadcrumb['title'];

                $breadcrumbStr .= $link->show() . ' &gt; ';
            }
        } else {

            $numBreadCrumbs = count($breadcrumbs);
            $counter = 1;

            foreach ($breadcrumbs as $breadcrumb) {
                if ($counter == $numBreadCrumbs) {
                    $breadcrumbStr .= $breadcrumb['title'];
                } else {
                    $link = new link($breadcrumb['link']);
                    $link->link = $breadcrumb['title'];

                    $breadcrumbStr .= $link->show() . ' &gt; ';
                }

                $counter++;
            }
        }

        return $breadcrumbStr;
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  string  $userId Parameter description (if any) ...
     * @param  unknown $path   Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
    public function generateBreadcrumbsFromUserPath($userId, $path) {
        // users/1/archives/error_log/error_log
        $userPath = 'users/' . $userId;

        $regex = '/\\Ausers\/' . $userId . '\/';

        $remainderPath = preg_replace($regex . '/', '', $path);

        $homeLink = new link($this->uri(NULL));
        $homeLink->link = 'My Files';

        $breadcrumbs = $homeLink->show();

        $items = explode('/', $remainderPath);

        $itemCount = count($items);

        if ($itemCount > 0) {
            $counter = 1;
            foreach ($items as $item) {
                $userPath .= '/' . $item;

                if ($counter == $itemCount) {
                    $breadcrumbs .= ' &gt; ' . $item;
                } else {

                    $itemLink = new link($this->uri(array('action' => 'viewfolder', 'folder' => $this->getFolderId($userPath))));
                    $itemLink->link = $item;

                    $breadcrumbs .= ' &gt; ' . $itemLink->show();
                }


                $counter++;
            }
        }

        return $breadcrumbs;
    }

    /**
     * Method to show the folders of the current user as a tree drop down
     * @param  string $default Record Id of the Current Folder to highlight
     * @return string
     */
    function getTreedropdown($selected = '') {
        //Create a new tree
        $menu = new treemenu();

        $allFilesNode = new treenode(array('text' => 'My Files', 'link' => 'ROOT'));

        $refArray = array();

        $refArray['/users/' . $this->objUser->userId()] = & $allFilesNode;
        $folders = $this->getUserFolders($this->objUser->userId());
        if (!empty($this->contextCode)) {
            $folders = $this->getAllFolders('context', $this->contextCode, $this->objUser->userId());
            $refArray['/context/' . $this->contextCode] = & $allFilesNode;
        }
        if (count($folders) > 0) {
            foreach ($folders as $folder) {
                $node = & new treenode(array('text' => basename($folder['folderpath']), 'link' => $folder['id']));

                $parent = '/' . dirname($folder['folderpath']);

                if (array_key_exists($parent, $refArray)) {
                    $refArray['/' . dirname($folder['folderpath'])]->addItem($node);
                }

                $refArray['/' . $folder['folderpath']] = & $node;
            }
        }

        $menu->addItem($allFilesNode);

        $this->appendArrayVar('headerParams', $this->getJavascriptFile('TreeMenu.js', 'tree'));
        $this->setVar('pageSuppressXML', TRUE);

        $objSkin = & $this->getObject('skin', 'skin');
        $treeMenu = &new htmldropdown($menu, array('inputName' => 'parentfolder', 'id' => 'input_parentfolder', 'selected' => $selected));
        return $treeMenu->getMenu();
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $folderId Parameter description (if any) ...
     * @return object  Return description (if any) ...
     * @access public
     */
    function showCreateFolderForm($folderId) {
        $folderPath = $this->getFolderPath($folderId);

        if ($folderPath == FALSE) {
            return '';
        }

        $folderParts = explode('/', $folderPath);
        $form = new form('createfolder', $this->uri(array('action' => 'createfolder')));
        $label = new label('Create a subfolder in: ', 'input_parentfolder');

        $form->addToForm($label->show() . '<br/>' . $this->getTree($folderParts[0], $folderParts[1], 'htmldropdown', $folderId));

        $textinput = new textinput('foldername');
        $label = new label('Name of Folder: ', 'input_foldername');
        $form->addToForm('<br/>' . $label->show() . '<br/>' . $textinput->show() . '&nbsp;');
        $button = new button('create', 'Create Folder');
        $button->setToSubmit();
        $form->addToForm('<br/>' . $button->show());

        return $form->show();
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $id Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    function renameFolder($id, $newName) {
        // Get Full Folder details
        $folder = $this->getRow('id', $id);
        $oldPath = $folder['folderpath'];
        $level = $folder['folderlevel'];
        $folderparts = explode("/", $oldPath);
        $basepath = "";
        $index = 0;
        foreach ($folderparts as $folderpart) {
            if ($index < $level - 1) {
                $basepath.=$folderpart . '/';
            }
            $index++;
        }
        $newPath = $basepath . '/' . $newName;
        $this->updateFolderIndex($oldPath, $newPath);
    }

    /**
     * Rename a folder
     *
     * @param string $folderId FolderId
     * @param string $folderName New folder name
     * @return void
     * @access public
     */
    function renameFolder_($folderId, $folderName) {
        $folder = $this->getRow('id', $folderId);
        $path = $folder['folderpath'];
        $level = $folder['folderlevel'];
        if ($level < 1) {
            return FALSE;
        } else if ($level == 1) {
            $newPath = $folderName;
        } else {
            // Level >= 2
            $newPath = preg_replace('/^(.+\/)(.+)$/', "\${1}$folderName", $path);
        }

        $pathFull = $this->objConfig->getcontentBasePath() . $path;
        $newPathFull = $this->objConfig->getcontentBasePath() . $newPath;
        // Update filesystem
        $result = rename($pathFull, $newPathFull);
        if ($result === FALSE) {
            return FALSE;
        }
        // Update tbl_files_folders
        // Update current record
        $this->update(
                'id', $folderId, array(
            'folderpath' => $newPath
                )
        );
        // Update all records that are subfolders of current record
        $rs = $this->getAll("WHERE folderpath LIKE '{$path}/%' ORDER BY folderlevel, folderpath");
        if (!empty($rs)) {
            foreach ($rs as $rec) {
                $id_ = $rec['id'];
                $path_ = $rec['folderpath'];
                $newPath_ = preg_replace('|^(' . $path . ')(/.*)$|', "{$newPath}\${2}", $path_);
                $this->update(
                        'id', $id_, array(
                    'folderpath' => $newPath_
                        )
                );
            }
        }
        $result = $this->objFiles->renameFolder($path, $newPath);
        if ($result === FALSE) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $id Parameter description (if any) ...
     * @return mixed   Return description (if any) ...
     * @access public
     */
    function deleteFolder($id) {
        // Get Full Path of Folder
        $folder = $this->getFullFolderPath($id);

        $objSymlinks = $this->getObject('dbsymlinks');

        // If no record of folder, do nothing
        if ($folder == FALSE) {
            return 'norecordoffolder';
        } else {

            // Load the Indexer
            $objIndexFiles = $this->getObject('indexfiles');

            // Scan Directory
            $results = $objIndexFiles->scanDirectory($folder);

            // File Results
            // If there are files in the directory, delete them one by one
            if (count($results[0]) > 0) {

                foreach ($results[0] as $file) {
                    // Remove the usrfiles portion from the file
                    preg_match('/(?<=usrfiles(\\\|\/)).*/', $file, $regs);

                    // Clean up portion - esp convert backslash to forward slash
                    $path = $this->objCleanUrl->cleanUpUrl($regs[0]);

                    // Check if there is a record of the file
                    $fileInfo = $this->objFiles->getFileDetailsFromPath($path);

                    // If there is no record of the file, simply delete them from file system
                    if ($fileInfo == FALSE) {
                        @unlink($file);
                    } else {
                        // Otherwise, follow process, delete them from the database, then filesystem
                        $this->objFiles->deleteFile($fileInfo['id']);
                    }
                }
            }

            // Now delete sub folders
            if (count($results[1]) > 0) {

                // Reverse Results so that we start with the bottom most folders
                $folders = array_reverse($results[1]);

                // Go through each folder
                foreach ($folders as $subfolder) {
                    // Remove the usrfiles portion from the file
                    preg_match('/(?<=usrfiles(\\\|\/)).*/', $subfolder, $regs);

                    // Clean up portion - esp convert backslash to forward slash
                    $path = $this->objCleanUrl->cleanUpUrl($regs[0]);


                    // Remove Directory
                    if (rmdir($subfolder . '/')) {
                        // Clear Record
                        $objSymlinks->deleteSymlinksInFolder($this->getFolderId($path));
                        $this->delete('folderpath', $path);
                    }
                }
            }


            // Now delete the folder itself
            if (!file_exists($folder)) { // If folder does not exist, delete record.
                $this->delete('id', $id);
                $objSymlinks->deleteSymlinksInFolder($id);
            } else if (rmdir($folder)) { // Else delete folder, then record
                $this->delete('id', $id);
                $objSymlinks->deleteSymlinksInFolder($id);
            } else {
                return FALSE;
            }

            return FALSE;
        }
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $dir Parameter description (if any) ...
     * @return void
     * @access private
     */
    private function remove_directory($dir) {
        if ($handle = opendir("$dir")) {
            while (false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    if (is_dir("$dir/$item")) {
                        //remove_directory("$dir/$item");
                        echo "$dir/$item";
                    } else {
                        unlink("$dir/$item");
                        echo " removing $dir/$item\n";
                    }
                }
            }
            closedir($handle);
            rmdir($dir);
            //echo "removing $dir\n";
        }
    }

    public function checkPermissionUploadFolder($type, $id) {
        switch ($type) {
            case 'users':
                if ($id == $this->objUser->userId()) {
                    return TRUE;
                } else {
                    return FALSE;
                }
                break;
            case 'context':
                $objContext = $this->getObject('dbcontext', 'context');
                if ($id == $objContext->getContextCode()) {
                    return $this->objUser->isContextLecturer($this->objUser->userId(), $objContext->getContextCode()) || $this->objUser->isAdmin();
                } else {
                    return FALSE;
                }
                break;
            default:
                return FALSE;
        }
    }

    public function checkPermissionAccessFolder($type, $id) {
        return TRUE;
    }

    /**
     * Move files
     *
     * Moves files from one folder to another
     *
     * @param string $folderSourceId Source folder ID
     * @param string $folderDestId Destination folder ID
     * @param array $files Files to be moved
     * @return bool Success or failure
     */
    public function moveFiles($folderSourceId, $folderDestId, $files) {
        if ($folderSourceId == $folderDestId) {
            return TRUE;
        }
        $destFolder = $this->getRow('id', $folderDestId);
        $destPath = $destFolder['folderpath'];
        foreach ($files as $file) {
            $result = $this->objFiles->moveFile($file, $destPath);
            if ($result === FALSE) {
                return FALSE;
            }
        }
        return TRUE;
    }

    /**
     * This updates the access field of the supplied fileid
     * @param type $fileId The id of the file while access is to be changed
     * @param type $access Access vlaue. Can be public, private_all or private_selected
     */
    function setFolderAccess($folderId, $access) {
        $this->update("id", $folderId, array("access" => $access));
    }

    /**
     * Updates a folder visibility by setting the mode to hidden or visibile.
     * A hidden folder is only accessible to the creator
     * @param type $folderId
     * @param type $access 
     */
    function setFolderVisibility($folderId, $access) {
        $this->update("id", $folderId, array("visibility" => $access));
    }

    /**
     * Updates this folder with the alerts status. A value of y implies any changes
     * to the folder alerts users who have access
     * @param type $folderId
     * @param type $alertStatus 
     */
    function setFolderAlerts($folderId, $alertStatus) {
        $this->update("id", $folderId, array("alerts" => $alertStatus));
    }

}

?>
