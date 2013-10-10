<?php
/**
 * Class to handle bookmarks elements.
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface.
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
 * @version    0.001
 * @package    schools
 * @author     Kevin Cyster kcyster@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
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
// end security check

/**
 * Class to handle blog elements
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface
 *
 * @version    0.001
 * @package    schools
 * @author     Kevin Cyster kcyster@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 */
class bookmarksops extends object
{
    /**
     *
     * Varuable to hold the parent folders
     * 
     * @access public
     * @var type 
     */
    private $parents;
    
    /**
     * 
     * Variable to hold the script for the dialog object
     * 
     * @access public
     * @var string
     */
    public $script;

    /**
     * Standard init function called by the constructor call of Object
     *
     * @access public
     * @return NULL
     */
    public function init()
    {
        try {
            // Load core system objects.
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            $this->userId = $this->objUser->PKId();
            $this->objUserAdmin = $this->getObject('useradmin_model2', 'security');
            $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
            $this->objConfirm = $this->newObject('confirm', 'utilities');
            $this->objConfig = $this->getObject('altconfig', 'config');
            
            // Load html elements.
            $this->objIcon = $this->newObject('geticon', 'htmlelements');
            $this->objTable = $this->loadClass('htmltable', 'htmlelements');
            $this->objLink = $this->loadClass('link', 'htmlelements');
            $this->objInput = $this->loadClass('textinput', 'htmlelements');
            $this->objFieldset = $this->loadClass('fieldset', 'htmlelements');
            $this->objDropdown = $this->loadClass('dropdown', 'htmlelements');
            $this->objForm = $this->loadClass('form', 'htmlelements');
            $this->objLayer = $this->loadClass('layer', 'htmlelements');
            $this->objRadio = $this->loadClass('radio', 'htmlelements');
            
            // Load db classes,
            $this->objDBbookmarks = $this->getObject('dbbookmarks', 'bookmarks');
            $this->objDBfolders = $this->getObject('dbbookmarkfolders', 'bookmarks');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }

    /**
     *
     * Method to generate an error string for display
     * 
     * @access private
     * @param string $errorText The error string
     * @return string $string The formated error string
     */
    private function error($errorText)
    {
        $error = $this->objLanguage->languageText('word_error', 'system', 'WORD: word_error, not found');
        
        $this->objIcon->title = $error;
        $this->objIcon->alt = $error;
        $this->objIcon->setIcon('exclamation', 'png');
        $errorIcon = $this->objIcon->show();
        
        $string = '<span style="color: red">' . $errorIcon . '&nbsp;<b>' . $errorText . '</b></span>';
        return $string;
    }
    
    /**
     *
     * Method to generate the html for the user display template
     * 
     * @access public
     * @return string $string The html string to be sent to the template 
     */
    public function showMain()
    {
        
        $objLayer = new layer();
        $objLayer->id = 'main_folders_layer';
        $objLayer->floating = 'left';
        $objLayer->width = '35%';
        $objLayer->str = $this->showFolders();
        $folderLayer = $objLayer->show();
        
        $folderId = $this->getParam('folder_id');
        
        $objLayer = new layer();
        $objLayer->id = 'main_bookmarks_layer';
        $objLayer->floating = 'left';
        $objLayer->width = '65%';
        $objLayer->str = $this->showBookmarks($folderId);
        $bookmarkLayer = $objLayer->show();

        $string = $folderLayer . $bookmarkLayer;
        
        return $string;
    }
    
    /**
     *
     * Method to show the folders fieldset
     * 
     * @access public
     * @return string $string The html string for display 
     */
    public function showFolders()
    {
        $foldersLabel = $this->objLanguage->languageText('mod_bookmarks_folders', 'bookmarks', 'ERROR: mod_bookmarks_folders');
        $addFolderLabel = $this->objLanguage->languageText('mod_bookmarks_addfolder', 'bookmarks', 'ERROR: mod_bookmarks_addfolder');
        $editFolderLabel = $this->objLanguage->languageText('mod_bookmarks_editfolder', 'bookmarks', 'ERROR: mod_bookmarks_editfolder');
        $deleteFolderLabel = $this->objLanguage->languageText('mod_bookmarks_deletefolder', 'bookmarks', 'ERROR: mod_bookmarks_deletefolder');
        $addLabel = $this->objLanguage->languageText('word_add', 'system', 'ERROR: word_add');
        $editLabel = $this->objLanguage->languageText('word_edit', 'system', 'ERROR: word_edit');
        $deleteLabel = $this->objLanguage->languageText('word_delete', 'system', 'ERROR: word_delete');
        $confirmFolderLabel = $this->objLanguage->languageText('mod_bookmarks_confirmdeletefolder', 'bookmarks', 'ERROR: mod_bookmarks_confirmdeletefolder');
        $nameLabel = $this->objLanguage->languageText('word_name', 'system', 'ERROR: word_name');
        $rootLabel = $this->objLanguage->languageText('mod_bookmarks_rootfolder', 'bookmarks', 'ERROR: mod_bookmarks_rootfolder');
        $expandLabel = $this->objLanguage->languageText('word_expand', 'system', 'ERROR: word_expand');
        $noLabel = $this->objLanguage->languageText('mod_bookmarks_nosubfolders', 'bookmarks', 'ERROR: mod_bookmarks_nosubfolders');

        $folderArray = $this->buildTree();

        $this->objIcon->title = $addLabel;
        $this->objIcon->alt = $addLabel;
        $this->objIcon->setIcon('folder_add', 'png');
        $addFolderIcon = $this->objIcon->show();

        $objLink = new link($this->uri(array('action' => 'form')));
        $objLink->link = $addFolderIcon . '&nbsp;' . $addFolderLabel;
        $addFolderLink = $objLink->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startHeaderRow();
        $objTable->addHeaderCell('<b>' . $nameLabel . '</b>', '', '', 'left', 'heading', '');
        $objTable->addHeaderCell('<b>' . $editLabel . '</b>', '10%', '', 'left', 'heading', '');
        $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
        $objTable->endHeaderRow();
        
        $this->objIcon->setIcon('folder', 'png');
        $this->objIcon->title = $rootLabel;
        $this->objIcon->alt = $rootLabel;
        $icon = $this->objIcon->show();

        $objTable->startRow();
        $objTable->addCell($icon . '&nbsp;' . '<a href="#" id="root">' . $rootLabel . '</a>', '', '', '', '', 'colspan="3"', '');
        $objTable->endRow();

        if (!empty($folderArray))
        {
            foreach ($folderArray as $folder)
            {
                $id = $folder['id'];
                
                if (!empty($folder['child']))
                {
                    $this->objIcon->setIcon('folder_expand', 'png');
                    $this->objIcon->title = $expandLabel;
                    $this->objIcon->alt = $expandLabel;
                    $icon = "<a href='#$id' id='icon_$id'>" . $this->objIcon->show(). '</a>';
                    $link = "<a href='#$id' id='link_$id'>" . $folder['folder_name'] . '</a>';
                    $iconLink = $icon . '&nbsp;' . $link;
                }
                else
                {
                    $this->objIcon->setIcon('folder', 'png');
                    $this->objIcon->title = $noLabel;
                    $this->objIcon->alt = $noLabel;
                    $icon = "<a href='#$id' id='icon_$id'>" . $this->objIcon->show(). '</a>';
                    $link = "<a href='#$id' id='link_$id'>" . $folder['folder_name'] . '</a>';
                    $iconLink = $icon . '&nbsp;' . $link;
                }

                $this->objIcon->setIcon('folder_delete', 'png');
                $this->objIcon->title = $deleteFolderLabel;
                $this->objIcon->alt = $deleteFolderLabel;
                $deleteIcon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'delete', 'type' => 'folder', 'id' => $folder['id']));

                $this->objConfirm->setConfirm($deleteIcon , $location, $confirmFolderLabel);
                $deleteLink = $this->objConfirm->show();

                $this->objIcon->title = $editFolderLabel;
                $this->objIcon->alt = $editFolderLabel;
                $this->objIcon->setIcon('folder_edit', 'png');
                $editIcon = $this->objIcon->show();

                $objLink = new link($this->uri(array('action' => 'form', 'id' => $folder['id'])));
                $objLink->link = $editIcon;
                $editLink = $objLink->show();
                
                if (!empty($folder['child']))
                {
                    $objTable->startRow('parent_hidden', 'parent_' . $folder['id']);
                }
                else
                {
                    $objTable->startRow('parent_none', 'parent_' . $folder['id']);
                }
                $objTable->addCell($iconLink, '', '', '', '', '', '');
                $objTable->addCell($editLink, '', '', '', '', '', '');
                $objTable->addCell($deleteLink, '', '', '', '', '', '');
                $objTable->endRow();
                
                if (!empty($folder['child']))
                {
                    foreach ($folder['child'] as $child)
                    {
                        $this->getSubFolders($objTable, $child);
                    }
                }
            }
        }

        $folderTable = $objTable->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . $foldersLabel. '</b>';
        $objFieldset->contents =  $addFolderLink . '<br />' . $folderTable;
        $folderFieldset = $objFieldset->show();
        
        $string = $folderFieldset;
        
        return $string;
    }
    
    
    /**
     *
     * Method to display the subfolders
     * 
     * @access public
     * @return VOID 
     */
    public function getSubFolders($objTable, $folderArray)
    {
        $editFolderLabel = $this->objLanguage->languageText('mod_bookmarks_editfolder', 'bookmarks', 'ERROR: mod_bookmarks_editfolder');
        $deleteFolderLabel = $this->objLanguage->languageText('mod_bookmarks_deletefolder', 'bookmarks', 'ERROR: mod_bookmarks_deletefolder');
        $confirmFolderLabel = $this->objLanguage->languageText('mod_bookmarks_confirmdeletefolder', 'bookmarks', 'ERROR: mod_bookmarks_confirmdeletefolder');
        $expandLabel = $this->objLanguage->languageText('word_expand', 'system', 'ERROR: word_expand');
        $noLabel = $this->objLanguage->languageText('mod_bookmarks_nosubfolders', 'bookmarks', 'ERROR: mod_bookmarks_nosubfolders');

        $id = $folderArray['id'];

        if (!empty($folderArray['child']))
        {
            $this->objIcon->setIcon('folder_expand', 'png');
            $this->objIcon->title = $expandLabel;
            $this->objIcon->alt = $expandLabel;
            $icon = "<a href='#$id' id='icon_$id'>" . $this->objIcon->show(). '</a>';
            $link = "<a href='#$id' id='link_$id'>" . $folderArray['folder_name'] . '</a>';
            $iconLink = $icon . '&nbsp;' . $link;
        }
        else
        {
            $this->objIcon->setIcon('folder', 'png');
            $this->objIcon->title = $noLabel;
            $this->objIcon->alt = $noLabel;
            $icon = "<a href='#$id' id='icon_$id'>" . $this->objIcon->show(). '</a>';
            $link = "<a href='#$id' id='link_$id'>" . $folderArray['folder_name'] . '</a>';
            $iconLink = $icon . '&nbsp;' . $link;
        }

        $this->objIcon->setIcon('folder_delete', 'png');
        $this->objIcon->title = $deleteFolderLabel;
        $this->objIcon->alt = $deleteFolderLabel;
        $deleteIcon = $this->objIcon->show();

        $location = $this->uri(array('action' => 'delete', 'type' => 'folder', 'id' => $folderArray['id']));

        $this->objConfirm->setConfirm($deleteIcon , $location, $confirmFolderLabel);
        $deleteLink = $this->objConfirm->show();

        $this->objIcon->title = $editFolderLabel;
        $this->objIcon->alt = $editFolderLabel;
        $this->objIcon->setIcon('folder_edit', 'png');
        $editIcon = $this->objIcon->show();

        $objLink = new link($this->uri(array('action' => 'form', 'id' => $folderArray['id'])));
        $objLink->link = $editIcon;
        $editLink = $objLink->show();

        $string = '';
        for ($i = 1; $i <= count($folderArray['parents']); $i++)
        {
            $string .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        $string .= $iconLink;

        $id = 'row_';
        foreach ($folderArray['parents'] as $parent)
        {
            $id .= $parent;
        }
        if (!empty($folderArray['child']))
        {
            $objTable->startRow('child_hidden', $id);
        }
        else
        {
            $objTable->startRow('child_none', $id);                    
        }
        $objTable->addCell($string, '', '', '', '', '', '');
        $objTable->addCell($editLink, '', '', '', '', '', '');
        $objTable->addCell($deleteLink, '', '', '', '', '', '');
        $objTable->endRow();

        if (!empty($folderArray['child']))
        {
            foreach($folderArray['child'] as $child)
            {
                $this->getSubFolders($objTable, $child);
            }
        }
    }

    /**
     *
     * Method to display the template to add or edit folders
     * 
     * @access public
     * @return string $string The html string for display
     */
    public function showManageFolders()
    {
        $idValue = $this->getParam('id', NULL);
        $folderArray = $this->objDBfolders->getFolders($this->userId, $idValue);

        if (empty($idValue))
        {
            $nameValue = null;
            $parentIdValue = $this->getParam('parent_id', NULL);
        }
        else
        {
            $folderData = $this->objDBfolders->getFolder($idValue);
            $nameValue = $folderData['folder_name'];
            $parentIdValue = $folderData['parent_id'];            
        }
        
        $errors = $this->getSession('errors');

        $nameValue = !empty($errors) ? $errors['data']['folder_name'] : $nameValue;
        $parentIdValue = !empty($errors) ? $errors['data']['parent_id'] : $parentIdValue;

        $nameError = (!empty($errors) && array_key_exists('folder_name', $errors['errors'])) ? $errors['errors']['folder_name'] : NULL;

        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'ERROR: word_cancel');
        $folderNameLabel = $this->objLanguage->languageText('mod_bookmarks_foldername', 'bookmarks', 'ERROR: mod_bookmarks_foldername');
        $parentFolderLabel = $this->objLanguage->languageText('mod_bookmarks_parentfolder', 'bookmarks', 'ERROR: mod_bookmarks_parentfolder');
        $rootFolderLabel = $this->objLanguage->languageText('mod_bookmarks_rootfolder', 'bookmarks', 'ERROR: mod_bookmarks_rootfolder');
        
        $objInput = new textinput('folder_name', $nameValue, '', '50');
        $nameInput = $objInput->show();
        
        $objDrop = new dropdown('parent_id');
        $objDrop->addOption('', $rootFolderLabel);
        if (!empty($folderArray))
        {
            $objDrop->addFromDB($folderArray, 'folder_name', 'id');
        }
        $objDrop->setSelected($parentIdValue);
        $parentDrop = $objDrop->show();

        $objInput = new textinput('id', $idValue, 'hidden', '50');
        $idInput = $objInput->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('save');
        $objButton->setToSubmit();
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('cancel');
        $objButton->setToSubmit();
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($parentFolderLabel, '200px', '', '', 'odd', '', '');
        $objTable->addCell($parentDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($folderNameLabel, '200px', '', '', 'even', '', '');
        $objTable->addCell($nameError . $nameInput, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($idInput . $saveButton . '&nbsp' . $cancelButton, '', '', '', 'odd', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('folders', $this->uri(array(
            'action' => 'validate',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $form = $objForm->show();
        
        $string = $form;
        
        return $string;        
    }

    /**
     *
     * Method to validate the component data
     * 
     * @access public
     * @param array $data The data to validate
     * @return boolean TRUE on errors | FALSE if no errors
     */
    public function validate($data)
    {
        
        $errors = array();
        foreach ($data as $fieldname => $value)
        {
            if ($fieldname != 'parent_id')
            {
                if ($value == NULL)
                {
                    $name = explode('_', $fieldname);
                    $name = implode(' ', $name);
                    $array = array('fieldname' => $name);
                    $errorText = $this->objLanguage->code2Txt('mod_bookmarks_error_1', 'bookmarks', $array, 'ERROR: mod_bookmarks_error_1');
                    $errors[$fieldname] = '<div>' . $this->error(ucfirst(strtolower($errorText))) . '</div>';
                }
            }
        }
        $errorArray = array();
        $errorArray['data'] = $data;
        $errorArray['errors'] = $errors;

        $this->setSession('errors', $errorArray);
        if (empty($errors))
        {
            return FALSE;
        }
        return TRUE;
    }
    
    /**
     *
     * Method to show the folders fieldset
     * 
     * @access public
     * @return string $string The html string for display 
     */
    public function showBookmarks($folderId = NULL)
    {
        $bookmarksLabel = $this->objLanguage->languageText('mod_bookmarks_bookmarks', 'bookmarks', 'ERROR: mod_bookmarks_bookmarks');
        $deleteLabel = $this->objLanguage->languageText('word_delete', 'system', 'ERROR: word_delete');
        $nameLabel = $this->objLanguage->languageText('mod_bookmarks_bookmarkname', 'bookmarks', 'ERROR: mod_bookmarks_bookmarkname');
        $locationLabel = $this->objLanguage->languageText('mod_bookmarks_location', 'bookmarks', 'ERROR: mod_bookmarks_location');
        $noBookmarksLabel = $this->objLanguage->languageText('mod_bookmarks_bookmarkhome', 'bookmarks', 'ERROR: mod_bookmarks_bookmarkhome');
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startHeaderRow();
        $objTable->addHeaderCell('<b>' . $nameLabel . '</b>', '30%', '', 'left', 'heading', '');
        $objTable->addHeaderCell('<b>' . $locationLabel . '</b>', '', '', 'left', 'heading', '');
        $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
        $objTable->endHeaderRow();
        
        $objTable->startRow();
        $objTable->addCell($this->error($noBookmarksLabel), '', '', '', '', 'colspan="3"', '');
        $objTable->endRow();
        
        $bookmarksTable = $objTable->show();
        
        if (!empty($folderId))
        {
            $bookmarksTable = $this->ajaxGetBookmarks(FALSE, $folderId);
        }

        $objLayer = new layer();
        $objLayer->id = 'folder_bookmarks';
        $objLayer->str = $bookmarksTable;
        $bookmarkLayer = $objLayer->show();
        
        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . $bookmarksLabel. '</b>';
        $objFieldset->contents = $bookmarkLayer;
        $bookmarkFieldset = $objFieldset->show();
        
        $string = $bookmarkFieldset;
        
        return $string;
    }  
    
    /**
     *
     * Method to return the html string for an ajax request
     * 
     * @access public
     * @param boolean $isAjax TRUE if this is an ajax request | FALSE if not
     * @return VOID 
     */
    public function ajaxGetBookmarks($isAjax = TRUE, $folderId = NULL)
    {
        $folderLabel = $this->objLanguage->languageText('mod_bookmarks_folder', 'bookmarks', 'ERROR: mod_bookmarks_folder');
        $deleteLabel = $this->objLanguage->languageText('word_delete', 'system', 'ERROR: word_delete');
        $deleteBookmarkLabel = $this->objLanguage->languageText('mod_bookmarks_deletebookmark', 'bookmarks', 'ERROR: mod_bookmarks_deletebookmark');
        $nameLabel = $this->objLanguage->languageText('mod_bookmarks_bookmarkname', 'bookmarks', 'ERROR: mod_bookmarks_bookmarkname');
        $locationLabel = $this->objLanguage->languageText('mod_bookmarks_location', 'bookmarks', 'ERROR: mod_bookmarks_location');
        $noBookmarksLabel = $this->objLanguage->languageText('mod_bookmarks_nofolderbookmarks', 'bookmarks', 'ERROR: mod_bookmarks_nofolderbookmarks');
        $rootLabel = $this->objLanguage->languageText('mod_bookmarks_rootfolder', 'bookmarks', 'ERROR: mod_bookmarks_rootfolder');
        $confirmBookmarkLabel = $this->objLanguage->languageText('mod_bookmarks_confirmdeletebookmark', 'bookmarks', 'ERROR: mod_bookmarks_confirmdeletebookmark');

        $folderId = $this->getParam('id', $folderId);
        $folderId = ($folderId == 'root') ? NULL : $folderId;
        
        $folderArray = $this->objDBfolders->getFolder($folderId);
        if (empty($folderArray))
        {
            $folderArray['folder_name'] = $rootLabel;
        }
        $bookmarksArray = $this->objDBbookmarks->getBookmarks($this->userId, $folderId);
        if (empty($folderId))
        {
            $folderId = 'root';
        }

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startHeaderRow();
        $objTable->addHeaderCell('<b>' . $nameLabel . '</b>', '30%', '', 'left', 'heading', '');
        $objTable->addHeaderCell('<b>' . $locationLabel . '</b>', '', '', 'left', 'heading', '');
        $objTable->addHeaderCell('<b>' . $deleteLabel . '</b>', '10%', '', 'left', 'heading', '');
        $objTable->endHeaderRow();
        
        if (empty($bookmarksArray))
        {
            $objTable->startRow();
            $objTable->addCell($this->error($noBookmarksLabel), '', '', '', '', 'colspan="3"', '');
            $objTable->endRow();
        }
        else
        {
            $i = 0;
            foreach ($bookmarksArray as $bookmark)
            {
                $class = (($i % 2) == 0) ? 'even' : 'odd';
                $this->objIcon->setIcon('bookmark_delete', 'png');
                $this->objIcon->title = $deleteBookmarkLabel;
                $this->objIcon->alt = $deleteBookmarkLabel;
                $deleteIcon = $this->objIcon->show();

                $location = $this->uri(array('action' => 'delete', 'type' => 'bookmark', 'id' => $bookmark['id'], 'folder_id' => $folderId));

                $this->objConfirm->setConfirm($deleteIcon , $location, $confirmBookmarkLabel);
                $deleteLink = $this->objConfirm->show();

                $siteRoot = $this->objConfig->getsiteRoot();
                $location = $siteRoot . 'index.php?' . $bookmark['location'];
                if (!empty($bookmark['contextcode']))
                {
                    $linkClass = " class='contextcode_" . $bookmark['contextcode'] . "'";
                    $link = "<a href='#' id='location_" . $bookmark['id'] . "'" . $linkClass . ">" . $location . '</a>';
                }
                else
                {
                    $link = "<a href='" . $location . "' id='location_" . $bookmark['id'] . "'>" . $location . '</a>';
                }                
                
                $objTable->startRow();
                $objTable->addCell($bookmark['bookmark_name'], '', '', '', $class, '', '');
                $objTable->addCell($link, '', '', '', $class, '', '');
                $objTable->addCell($deleteLink, '', '', '', $class, '', '');
                $i++;
            }
        }
        $bookmarkTable = $objTable->show();
        
        if ($isAjax)
        {
            echo '<b>' . $folderLabel . ' - ' . $folderArray['folder_name'] . '</b><br />' . $bookmarkTable;
            die();
        }
        else
        {
            return '<b>' . $folderLabel . ' - ' . $folderArray['folder_name'] . '</b><br />' . $bookmarkTable;
        }
    }
    
    /**
     *
     * Methoid to put the bookmarking link in the page template.
     * 
     * @access public
     * @return string The bookmark link string 
     */
    public function showLink($toolbar = TRUE)
    {
        $addBookmarkLabel = $this->objLanguage->languageText('mod_bookmarks_addbookmark', 'bookmarks', 'ERROR: mod_bookmarks_addbookmark');
        $bookmarkPageLabel = $this->objLanguage->languageText('mod_bookmarks_bookmarkpage', 'bookmarks', 'ERROR: mod_bookmarks_bookmarkpage');
        $saveLabel = $this->objLanguage->languageText('word_save', 'system', 'ERROR: word_save');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'ERROR: word_cancel');
        $bookmarkNameLabel = $this->objLanguage->languageText('mod_bookmarks_bookmarkname', 'bookmarks', 'ERROR: mod_bookmarks_bookmarkname');
        $locationLabel = $this->objLanguage->languageText('mod_bookmarks_location', 'bookmarks', 'ERROR: mod_bookmarks_location');
        $folderLabel = $this->objLanguage->languageText('mod_bookmarks_folder', 'bookmarks', 'ERROR: mod_bookmarks_folder');
        $rootFolderLabel = $this->objLanguage->languageText('mod_bookmarks_rootfolder', 'bookmarks', 'ERROR: mod_bookmarks_rootfolder');
        $noNameLabel = $this->objLanguage->languageText('mod_bookmarks_noname', 'bookmarks', 'ERROR: mod_bookmarks_noname');
        $successTitleLabel = $this->objLanguage->languageText('word_success', 'system', 'ERROR: word_success');
        $successLabel = $this->objLanguage->languageText('mod_bookmarks_success', 'bookmarks', 'ERROR: mod_bookmarks_success');
        
        $arrayVars = array();
        $arrayVars['no_name'] = $noNameLabel;
       
        // pass error to javascript.
        $this->script = "<script type=\"text/javascript\">var no_name = '$noNameLabel';</script>";

        $folderArray = $this->objDBfolders->getFolders($this->userId);

        $objInput = new textinput('bookmark_name', '', '', '50');
        $nameInput = $objInput->show();
        
        $objInput = new textinput('location', '', 'hidden', '50');
        $locationHiddenInput = $objInput->show();
        
        $objInput = new textinput('visible_location', '', '', '50');
        $objInput->extra = 'disabled="disabled"';
        $locationVisibleInput = $objInput->show();
        
        $objDrop = new dropdown('folder_id');
        $objDrop->addOption('', $rootFolderLabel);
        if (!empty($folderArray))
        {
            $objDrop->addFromDB($folderArray, 'folder_name', 'id');
        }
        $folderDrop = $objDrop->show();

        $objButton = new button('save', $saveLabel);
        $objButton->setId('modal_save');
        $saveButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('modal_cancel');
        $cancelButton = $objButton->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($folderLabel, '200px', '', '', 'odd', '', '');
        $objTable->addCell($folderDrop, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($bookmarkNameLabel, '200px', '', '', 'even', '', '');
        $objTable->addCell($nameInput, '', '', '', 'even', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($locationLabel, '200px', '', '', 'odd', '', '');
        $objTable->addCell($locationHiddenInput . $locationVisibleInput, '', '', '', 'odd', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($saveButton . '&nbsp' . $cancelButton, '', '', '', 'even', 'colspan="7"', '');
        $objTable->endRow();
        $formTable = $objTable->show();

        $objForm = new form('modal_bookmarks', $this->uri(array(
            'action' => 'save'
        ), 'bookmarks'));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $form = $objForm->show();
               
        $objLayer = new layer();
        $objLayer->id = 'form_layer';
        $objLayer->str =  $form;
        $formLayer = $objLayer->show();
        
        $this->objDialog = $this->newObject('dialog', 'jquerycore');
        $this->objDialog->setCssId('dialog_add_bookmark');
        $this->objDialog->setTitle($addBookmarkLabel);
        $this->objDialog->setContent($formLayer);
        $this->objDialog->setWidth(750);
        $this->objDialog->unsetButtons();
        $dialog = $this->objDialog->show();
        $this->script .= $this->objDialog->script;

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell('<span class="success">' . $successLabel . '</span>', '200px', '', '', 'odd', '', '');
        $objTable->endRow();
        $successTable = $objTable->show();

        $this->objDialog = $this->newObject('dialog', 'jquerycore');
        $this->objDialog->setCssId('dialog_bookmark_success');
        $this->objDialog->setTitle($successTitleLabel);
        $this->objDialog->setContent($successTable);
        $this->objDialog->setWidth(370);
        $dialog .= $this->objDialog->show();
        $this->script .= $this->objDialog->script;        

        $this->objIcon->title = $bookmarkPageLabel;
        $this->objIcon->alt = $bookmarkPageLabel;
        $this->objIcon->setIcon('bookmark', 'png');
        $bookmarkIcon = $this->objIcon->show();

        $link = '<a href="#" id="add_bookmark"><strong>' . $bookmarkIcon . '</strong></a>';
       
        if ($toolbar)
        {
            return $link . $dialog;
        }
        else
        {
            return $dialog;
        }
    }
    
    /**
     *
     * Method to show the goto bookmarks link.
     * 
     * @access public
     * @return string $string The goto bookmark icon
     */
    public function showGotoLink()
    {
        $gotoLabel = $this->objLanguage->languageText('mod_bookmarks_gotobookmarks', 'bookmarks', 'ERROR: mod_bookmarks_gotobookmarks');

        $hasBookmarks = $this->objDBbookmarks->hasBookmarks($this->userId);

        if ($hasBookmarks)
        {
            $this->objIcon->title = $gotoLabel;
            $this->objIcon->alt = $gotoLabel;
            $this->objIcon->setIcon('bookmark_go', 'png');
            $gotoIcon = $this->objIcon->show();            
            
            $uri = $this->uri(array(), 'bookmarks');
            $gotoLink = '<a href="' . $uri . '"><strong>' . $gotoIcon . '</strong></a>';
        }
        else
        {
            $gotoLink = NULL;
        }
        
        return $gotoLink;
    }
    
    /**
     *
     * Method to show bookmarks in a block
     * 
     * @access public
     * @return string The html string for display 
     */
    public function showBookmarking()
    {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('bookmark_block.js', 'bookmarks'));

        $bookmarksLabel = $this->objLanguage->languageText('mod_bookmarks_bookmarks', 'bookmarks', 'ERROR: mod_bookmarks_bookmarks');
        $foldersLabel = $this->objLanguage->languageText('mod_bookmarks_folders', 'bookmarks', 'ERROR: mod_bookmarks_folders');
        $noBookmarksLabel = $this->objLanguage->languageText('mod_bookmarks_nofolderbookmarks', 'bookmarks', 'ERROR: mod_bookmarks_nofolderbookmarks');
        $rootFolderLabel = $this->objLanguage->languageText('mod_bookmarks_rootfolder', 'bookmarks', 'ERROR: mod_bookmarks_rootfolder');
      
        $foldersArray = $this->objDBfolders->getFolders($this->userId);
        $bookmarksArray = $this->objDBbookmarks->getBookmarks($this->userId);

        $objDrop = new dropdown('block_folder_id');
        $objDrop->addOption('', $rootFolderLabel);
        if (!empty($foldersArray))
        {
            $objDrop->addFromDB($foldersArray, 'folder_name', 'id');
        }
        $folderDrop = $objDrop->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($folderDrop, '', '', '', '', '', '');
        $objTable->endRow();
        $folderTable = $objTable->show();
  
        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . $foldersLabel. '</b>';
        $objFieldset->contents =  $folderTable;
        $folderFieldset = $objFieldset->show();
        
        if (empty($bookmarksArray))
        {
            $objTable = new htmltable();
            $objTable->width = 100%
            $objTable->cellpadding = '4';
            $objTable->startRow();
            $objTable->addCell($this->error($noBookmarksLabel), '', '', '', '', '', '');
            $objTable->endRow();
            $bookmarkTable = $objTable->show();
        }
        else
        {
            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            
            foreach ($bookmarksArray as $bookmark)
            {
                $siteRoot = $this->objConfig->getsiteRoot();
                $location = $siteRoot . 'index.php?' . $bookmark['location'];
                if (!empty($bookmark['contextcode']))
                {
                    $linkClass = " class='block_contextcode_" . $bookmark['contextcode'] . "'";
                    $link = "<a href='#' id='block_" . $bookmark['id'] . "'" . $linkClass . ">" . $bookmark['location'] . '</a>';
                }
                else
                {
                    $link = "<a href='" . $location . "' id='block_" . $bookmark['id'] . "'>" . $bookmark['location'] . '</a>';
                }                

                $objTable->startRow();
                $objTable->addCell('<b>' . $bookmark['bookmark_name'] . '</b>', '', '', '', '', '', '');
                $objTable->endRow();
                $objTable->startRow();
                $objTable->addCell($link, '', '', '', '', '', '');
                $objTable->endRow();
            }        
            $bookmarkTable = $objTable->show();
        }
        
        $objLayer = new layer();
        $objLayer->id = 'bookmarks_block_layer';
        $objLayer->str = $bookmarkTable;
        $bookmarkLayer = $objLayer->show();

        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . $bookmarksLabel. '</b>';
        $objFieldset->contents =  $bookmarkLayer;
        $bookmarksFieldset = $objFieldset->show();
        
        return $folderFieldset . '<br />' . $bookmarksFieldset;        
    }
    
    /**
     *
     * Method to return bookmarks for a block
     * 
     * @access public
     * @return VOID 
     */
    public function ajaxGetBlockBookmarks()
    {
        $noBookmarksLabel = $this->objLanguage->languageText('mod_bookmarks_nofolderbookmarks', 'bookmarks', 'ERROR: mod_bookmarks_nofolderbookmarks');

        $folderId = $this->getParam('id');
        
        $bookmarksArray = $this->objDBbookmarks->getBookmarks($this->userId, $folderId);

        if (empty($bookmarksArray))
        {
            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startRow();
            $objTable->addCell($this->error($noBookmarksLabel), '', '', '', '', '', '');
            $objTable->endRow();
            $bookmarkTable = $objTable->show();
        }
        else
        {
            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            
            foreach ($bookmarksArray as $bookmark)
            {
                $siteRoot = $this->objConfig->getsiteRoot();
                $location = $siteRoot . 'index.php?' . $bookmark['location'];
                if (!empty($bookmark['contextcode']))
                {
                    $linkClass = " class='block_contextcode_" . $bookmark['contextcode'] . "'";
                    $link = "<a href='#' id='block_" . $bookmark['id'] . "'" . $linkClass . ">" . $bookmark['location'] . '</a>';
                }
                else
                {
                    $link = "<a href='" . $location . "' id='block_" . $bookmark['id'] . "'>" . $bookmark['location'] . '</a>';
                }                

                $objTable->startRow();
                $objTable->addCell('<b>' . $bookmark['bookmark_name'] . '</b>', '', '', '', '', '', '');
                $objTable->endRow();
                $objTable->startRow();
                $objTable->addCell($link, '', '', '', '', '', '');
                $objTable->endRow();
            }        
            $bookmarkTable = $objTable->show();
        }
        
        echo $bookmarkTable;
        die();
    }

    /**
     *
     * Method to build the folder tree
     * 
     * @access public
     * @return array The folder tree array
     */
    function buildTree()
    {
        $folders = $this->objDBfolders->getFolders($this->userId);
        $list = array();
        foreach ($folders as $folder) 
        {
            $list[$folder['parent_id']][] = $folder;
        }
        
        $tree = array();
        foreach ($folders as $key => $folder)
        {
            if (empty($folder['parent_id']))
            {
                $this->parents = array();
                $tree = array_merge($tree, $this->getLeaves($list, array($folders[$key])));
            }
        }
        return $tree;
    }
    
    /**
     *
     * Method to get sub folders for the folder tree
     * 
     * @access public
     * @param type $list Arry of all folders
     * @param type $parent array of parent folder details
     * @return type 
     */
    function getLeaves($list, $parent) 
    {
        $tree = array();
        foreach ($parent as $folder) 
        {
            if (!empty($parent[0]['parent_id']))
            {
                $this->parents[] = $parent[0]['parent_id'];
                $parents = array_unique($this->parents);
                while (end($parents) != $folder['parent_id'])
                {
                    array_pop($parents);
                }
                $folder['parents'] = $parents;
            }
            
            if(isset($list[$folder['id']])) 
            {   
                $folder['child'] = $this->getLeaves($list, $list[$folder['id']]);
            }
            $tree[] = $folder;
        } 
        return $tree;
    }
    
    /**
     *
     * Method to return the bookmark link params
     * 
     * @access public
     * @return array The array of params 
     */
    public function bookmarkParams()
    {
        $headerParams = $this->getJavascriptFile('bookmark_link.js', 'bookmarks');
        $headerParams .= "\n" . $this->script;
        $array = array('headerParams' => $headerParams);
        return $array;
    }
}
?>