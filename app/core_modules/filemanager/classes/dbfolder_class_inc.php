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
 * @version   CVS: $Id$
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
class dbfolder extends dbTable
{


    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_files_folders');

        $this->objFiles = $this->getObject('dbfile');
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objCleanUrl = $this->getObject('cleanurl');

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
    public function indexFolder($folder, $isFullPath=TRUE)
    {
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
    * Method to add a folder to the database records
    * @param  string $folder Path to the folder
    * @return string Record Id
    */
    private function addFolder($folder)
    {
        return $this->insert(array('folderpath'=> $folder, 'folderlevel'=>count(explode('/', $folder))));
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
    public function uri($params = array(), $module = '', $mode = '', $omitServerName=FALSE, $javascriptCompatibility = FALSE)
    {
        $objFileManagerObject = $this->getObject('filemanagerobject');
        return $objFileManagerObject->uri($params, $module, $mode, $omitServerName, $javascriptCompatibility);
    }


    /**
    * Method to show the folders of the current user as a DHTML Tree
    * @param  string $default Record Id of the Current Folder to highlight
    * @return string
    */
    function showUserFolders($default='')
    {
        //Create a new tree
        $menu  = new treemenu();


        $icon         = 'folder.gif';
        $expandedIcon = 'folder-expanded.gif';

        $allFilesNode = new treenode(array('text' => 'My Files', 'link' => $this->uri(NULL), 'icon' => $icon, 'expandedIcon' => $expandedIcon));



        $refArray = array();

        $refArray['/users/'.$this->objUser->userId()] =& $allFilesNode;

        $folders = $this->getUserFolders($this->objUser->userId());

        if (count($folders) > 0) {
            foreach ($folders as $folder)
            {
                $folderText = basename($folder['folderpath']);

                if ($folder['id'] == $default) {
                    $folderText = '<strong>'.$folderText.'</strong>';
                    $cssClass = 'confirm';
                } else {
                    $cssClass = '';
                }

                $node =& new treenode(array('text' => $folderText, 'link' => $this->uri(array('action'=>'viewfolder', 'folder'=>$folder['id'])), 'icon' => $icon, 'expandedIcon' => $expandedIcon, 'cssClass'=>$cssClass));

                $parent = '/'.dirname($folder['folderpath']);

                //echo $folder['folderpath'].' - '.$parent.'<br />';
                if (array_key_exists($parent, $refArray)) {
                    $refArray['/'.dirname($folder['folderpath'])]->addItem($node);
                }

                $refArray['/'.$folder['folderpath']] =& $node;
            }
        }

        $menu->addItem($allFilesNode);

        $this->appendArrayVar('headerParams', $this->getJavascriptFile('TreeMenu.js', 'tree'));
        $this->setVar('pageSuppressXML', TRUE);

        $objSkin =& $this->getObject('skin', 'skin');
        $treeMenu = &new dhtml($menu, array('images' => 'skins/_common/icons/tree', 'defaultClass' => 'treeMenuDefault'));
        return $treeMenu->getMenu();
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
    function getUserFolders($userId)
    {
        return $this->getFolders('users', $userId);
    }
    
    function getFolders($type, $id)
    {
        return $this->getAll(' WHERE folderpath LIKE \''.$type.'/'.$id.'/%\' ORDER BY folderlevel, folderpath');
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
    function getFolder($id)
    {
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
    function getFolderId($path)
    {
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
    function getFolderName($id)
    {
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
    function getFolderPath($id)
    {
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
    function getFullFolderPath($id)
    {
        $folder = $this->getRow('id', $id);

        if ($folder == FALSE) {
            return FALSE;
        } else {
            $path = $this->objConfig->getcontentBasePath().$folder['folderpath'];

            $path = $this->objCleanUrl->cleanUpUrl($path);

            return $path;
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
    public function getSubFolders($id)
    {
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
    public function getSubFoldersFromPath($folderPath)
    {
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
    private function _getSubFolders($path, $level)
    {
        return $this->getAll(' WHERE folderpath LIKE \''.$path.'/%\' AND folderlevel = '.($level+1).' ORDER BY folderpath');
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
    public function generateBreadcrumbsFromUserPath($userId, $path)
    {
        // users/1/archives/error_log/error_log
        $userPath = 'users/'.$userId;

        $regex = '/\\Ausers\/'.$userId.'\/';

        $remainderPath = preg_replace($regex.'/', '', $path);

        $homeLink = new link ($this->uri(NULL));
        $homeLink->link = 'My Files';

        $breadcrumbs = $homeLink->show();

        $items = explode('/', $remainderPath);

        $itemCount = count($items);

        if ($itemCount > 0) {
            $counter = 1;
            foreach ($items as $item)
            {
                $userPath .= '/'.$item;

                if ($counter == $itemCount) {
                    $breadcrumbs .= ' &gt; '.$item;
                } else {

                    $itemLink = new link ($this->uri(array('action'=>'viewfolder', 'folder'=>$this->getFolderId($userPath))));
                    $itemLink->link = $item;

                    $breadcrumbs .= ' &gt; '.$itemLink->show();
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
    function getTreedropdown($selected = '')
    {
        //Create a new tree
        $menu  = new treemenu();

        $allFilesNode = new treenode(array('text' => 'My Files', 'link' => 'ROOT'));

        $refArray = array();

        $refArray['/users/'.$this->objUser->userId()] =& $allFilesNode;

        $folders = $this->getUserFolders($this->objUser->userId());

        if (count($folders) > 0) {
            foreach ($folders as $folder)
            {
                $node =& new treenode(array('text' => basename($folder['folderpath']), 'link' => $folder['id']));

                $parent = '/'.dirname($folder['folderpath']);

                //echo $folder['folderpath'].' - '.$parent.'<br />';
                if (array_key_exists($parent, $refArray)) {
                    $refArray['/'.dirname($folder['folderpath'])]->addItem($node);
                }

                $refArray['/'.$folder['folderpath']] =& $node;
            }
        }

        $menu->addItem($allFilesNode);

        $this->appendArrayVar('headerParams', $this->getJavascriptFile('TreeMenu.js', 'tree'));
        $this->setVar('pageSuppressXML', TRUE);

        $objSkin =& $this->getObject('skin', 'skin');
        $treeMenu = &new htmldropdown($menu, array('inputName'=> 'parentfolder', 'id'=>'input_parentfolder','selected'=>$selected));
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
    function showCreateFolderForm($folderId)
    {
        $form = new form ('createfolder', $this->uri(array('action'=>'createfolder')));

        $label = new label ('Create a subfolder in: ', 'input_parentfolder');

        $form->addToForm($label->show().$this->getTreedropdown($folderId));


        // $objInputMasks = $this->getObject('inputmasks', 'htmlelements');
        // echo $objInputMasks->show();

        $textinput = new textinput('foldername');
        //$textinput->setCss('text input_mask anytext');

        $label = new label ('Name of Folder: ', 'input_foldername');

        $form->addToForm(' &nbsp; '.$label->show().$textinput->show());

        $button = new button ('create', 'Create Folder');
        $button->setToSubmit();

        $form->addToForm(' '.$button->show());

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
    function deleteFolder($id)
    {
        // Get Full Path of Folder
        $folder = $this->getFullFolderPath($id);

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
                foreach ($results[0] as $file)
                {
                    // Remove the usrfiles portion from the file
                    preg_match('/(?<=usrfiles(\\\|\/)).*/', $file, $regs);

                    // Clean up portion - esp convert backslash to forward slash
                    $path = $this->objCleanUrl->cleanUpUrl($path);

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
                foreach ($folders as $subfolder)
                {
                    // Remove the usrfiles portion from the file
                    preg_match('/(?<=usrfiles(\\\|\/)).*/', $subfolder, $regs);

                    // Clean up portion - esp convert backslash to forward slash
                    $path = $this->objCleanUrl->cleanUpUrl($path);

                    // Remove Directory
                    if (rmdir($subfolder.'/')) {
                        // Clear Record
                        $this->delete('folderpath', $path);
                    }
                }
            }

            // Now delete the folder itself
            if (!file_exists($folder)) { // If folder does not exist, delete record.
                $this->delete('id', $id);
            } else if (rmdir($folder)) { // Else delete folder, then record
                $this->delete('id', $id);
            } else {
                return FALSE;
            }
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
    private function remove_directory($dir)
    {
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





}

?>