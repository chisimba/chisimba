<?php

/**
 * Class to Show a File Selector Input
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
 * Class to Show a File Selector Input
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
$this->loadClass('filemanagerobject', 'filemanager');

class previewfolder extends filemanagerobject {

    /**
     * The variable to indicate wether the user has edit permition or not
     * @access public
     * @var boolean
     */
    public $editPermission = TRUE;

    /**
     * The string variable to store the target module
     * @access public
     * @var string
     */
    public $targetModule = "filemanager";

    /**
     * The string to indicate the current type of view
     * @access public
     * @var string
     */
    public $viewType;

    /**
     * Constructor
     */
    public function init() {
        $this->objFileIcons = $this->getObject('fileicons', 'files');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objAltConfig = $this->getObject("altconfig", "config");
        $this->objUser = $this->getObject("user", "security");
        $this->objFolder = $this->getObject("dbfolder", "filemanager");
        $this->objFiles = $this->getObject("dbfile", "filemanager");
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('formatfilesize', 'files');
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  unknown $subFolders Parameter description (if any) ...
     * @param  unknown $files      Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access public
     */
    function previewContent($subFolders, $files, $mode, $name, $symlinks = array(), $restriction = array(), $forceRestriction = FALSE) {
        $this->viewType = $this->getParam('view');
        if ($this->viewType == strtolower($this->objLanguage->languageText("phrase_thumbnailview", "system"))) {
            return $this->previewThumbnails($subFolders, $files, $symlinks, $restriction, $mode, $name, $forceRestriction);
        } else {
            return $this->previewLongView($subFolders, $files, $symlinks, $restriction, $mode, $name, $forceRestriction);
        }
    }

    /**
     * Short description for function
     *
     * Long description (if any) ...
     *
     * @param  array  $subFolders Parameter description (if any) ...
     * @param  array  $files      Parameter description (if any) ...
     * @return object Return description (if any) ...
     * @access public
     */
    function previewLongView($subFolders, $files, $symlinks, $restriction, $mode, $name, $forceRestriction = FALSE) {
        $objTable = $this->newObject('htmltable', 'htmlelements');
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objTable->startHeaderRow();
        if ($this->editPermission) {
            $objTable->addHeaderCell('&nbsp;', '20');
        }
        $objTable->addHeaderCell('&nbsp;', '20');
        $objTable->addHeaderCell($this->objLanguage->languageText('word_name', 'system', 'Name'));
        $objTable->addHeaderCell($this->objLanguage->languageText('word_size', 'system', 'Size'), 60);
        $objTable->addHeaderCell('&nbsp;', '30');

        // Set Restriction as empty if it is none
        if (count($restriction) == 1 && $restriction[0] == '') {
            $restriction = array();
        }

        $objTable->endHeaderRow();

        $hidden = 0;

        if (count($subFolders) == 0 && count($files) == 0 && count($symlinks) == 0) {
            $objTable->startRow();
            $objTable->addCell('<em>' . $this->objLanguage->languageText('mod_filemanager_nofilesorfolders', 'filemanager', 'No files or folders found') . '</em>', NULL, NULL, NULL, 'noRecordsMessage', 'colspan="5"');
            $objTable->endRow();
        } else {

            if (count($subFolders) > 0) {
                $folderIcon = $this->objFileIcons->getExtensionIcon('folder');


                foreach ($subFolders as $folder) {

                    $objTable->startRow();

                    if ($this->editPermission) {
                        $checkbox = new checkbox('files[]');
                        $checkbox->value = 'folder__' . $folder['id'];
                        $checkbox->cssId = htmlentities('input_files_' . basename($folder['folderpath']));

                        $objTable->addCell($checkbox->show(), 20);
                    }

                    $objTable->addCell($folderIcon);

                    $folderLink = new link($this->uri(array('action' => 'viewfolder', 'folder' => $folder['id'], 'view' => $this->viewType), $this->targetModule));


                    $extTitle = '';
                    $accessVal = null;

                    if (key_exists("access", $folder)) {
                        $accessVal = $folder['access'];
                    }

                    if ($accessVal == 'private_all') {
                        $objIcon->setIcon('info');
                        $extTitle = $objIcon->show();
                    }


                    //TODO: make this a reusable function
                    //variables to store informatin partaining folder contants
                    $nmbrOfFiles = count($this->objFiles->getFolderFiles($folder['folderpath']));
                    $nmbrOfFolders = count($this->objFolder->getSubFolders($folder['id']));

                    //Assign the value to be displayed on the link's title depending on it's contents
                    if ($nmbrOfFiles == 0 && $nmbrOfFolders == 0) {
                        $titleString = $this->objLanguage->languageText("mod_filemanager_emptyfolderindicator", "filemanager");
                    } else {
                        $titleString = $this->objLanguage->languageText("mod_filemanager_contentsindicator", "filemanager");
                        $titleString = substr($titleString, 0, 9) . $nmbrOfFolders . substr($titleString, 8, 11) . $nmbrOfFiles . substr($titleString, 18, 12);
                    }
                    //End subfolder and files count

                    $folderLink->title = $titleString;
                    $folderLink->link = substr(basename($folder['folderpath']), 0, 70) . '...' . $extTitle;

                    $objTable->addCell($folderLink->show());
                    $objTable->addCell('<em>' . $this->objLanguage->languageText('word_folder', 'system', 'Folder') . '</em>');
                    $objTable->endRow();
                }
            }

            if (is_array($symlinks)) {
                $files = array_merge($files, $symlinks);
            }
            if (count($files) > 0) {
                //var_dump($files);
                $fileSize = new formatfilesize();
                foreach ($files as $file) {
                    $visibility = null;
                    if (key_exists("visibility", $file)) {
                        $visibility = $file['visibility'];
                    }
                    $showFile = true;
                    if ($visibility == 'hidden') {
                        if ($file['creatorid'] == $this->objUser->userid()) {
                            $showFile = true;
                        } else {
                            $showFile = false;
                        }
                    }
                    if (!$showFile) {
                        continue;
                    }
                    if (count($restriction) > 0) {
                        if (!in_array(strtolower($file['datatype']), $restriction)) {
                            $objTable->startRow('hidefile');
                            $hidden++;
                        } else {
                            $objTable->startRow();
                        }
                    } else {
                        $objTable->startRow();
                    }

                    if ($this->editPermission) {
                        $checkbox = new checkbox('files[]');

                        if (isset($file['symlinkid'])) {
                            $checkbox->value = 'symlink__' . $file['symlinkid'];
                        } else {
                            $checkbox->value = $file['id'];
                        }

                        $checkbox->cssId = htmlentities('input_files_' . $file['filename']);
                        $objTable->addCell($checkbox->show(), 20);
                    }

                    $label = new label($this->objFileIcons->getFileIcon($file['filename']), htmlentities('input_files_' . $file['filename']));
                    $objTable->addCell($label->show());

                    if (isset($file['symlinkid'])) {
                        $fileLink = new link($this->uri(array('action' => 'symlink', 'id' => $file['symlinkid'])));
                    } else {

                        $fileLink = new link($this->uri(array('action' => 'fileinfo', 'id' => $file['id']), $this->targetModule));
                    }

                    $linkTitle = '';
                    $access = null;
                    if (key_exists("access", $file)) {
                        $access = $file['access'];
                    }
                    if ($access == 'private_all') {
                        $objIcon->setIcon('info');
                        $linkTitle = basename($file['filename']) . $objIcon->show();
                    } else {
                        $linkTitle = basename($file['filename']);
                    }

                    $fileLink->link = $linkTitle;
                    $filepath = $this->objAltConfig->getSiteRoot() . '/usrfiles/' . $file['path'];
                    $selectStr = '<a href=\'javascript:selectFile("' . $filepath . '");\'>' . basename($file['filename']) . '</a>';
                    $selectFileStr = '<a href=\'javascript:selectFileWindow("' . $name . '","' . $file['filename'] . '","' . $file['id'] . '");\'>' . basename($file['filename']) . '</a>';
                    $selectImageStr = '<a href=\'javascript:selectImageWindow("' . $name . '", "' . $filepath . '","' . $file['filename'] . '","' . $file['id'] . '");\'>' . basename($file['filename']) . '</a>';

                    if ($mode == 'fckimage' || $mode == 'fckflash' || $mode == 'fcklink') {
                        $objTable->addCell($selectStr);
                    } else if ($mode == 'selectfilewindow') {
                        $objTable->addCell($selectFileStr);
                    } else if ($mode == 'selectimagewindow') {
                        $objTable->addCell($selectImageStr);
                    } else {
                        $objTable->addCell($fileLink->show());
                    }
                    $objTable->addCell($fileSize->formatsize($file['filesize']));
                    $objTable->endRow();
                }
            }
        }

        if ($hidden > 0 && count($restriction) > 0) {
            $str = '';
            $str .= '<style type="text/css">
tr.hidefile {display:none;}
</style>';
            $str .= $this->objLanguage->languageText('mod_filemanager_browsingfor', 'filemanager', 'Browsing for') . ': ';
            $comma = '';
            foreach ($restriction as $restrict) {
                $str .= $comma . $restrict;
                $comma = ', ';
            }
            if (!$forceRestriction) {
                $str .= '<script type="text/javascript">
var onOrOff = "off";
function turnOnFiles(value)
{
    if (onOrOff == \'off\') {
        jQuery(\'tr.hidefile\').each(function (i) {
            this.style.display = \'table-row\';
        });
        adjustLayout();
        onOrOff = "on";
    } else {
        jQuery(\'tr.hidefile\').each(function (i) {
            this.style.display = \'none\';
        });
        adjustLayout();
        onOrOff = "off";
    }
}
</script>';
                $str .= ' &nbsp; - ';
                $this->loadClass('checkbox', 'htmlelements');
                $this->loadClass('label', 'htmlelements');
                $checkbox = new checkbox('showall');
                $checkbox->extra = ' onclick="turnOnFiles();"';
                $label = new label($this->objLanguage->languageText('mod_filemanager_showallfiles', 'filemanager', 'Show All Files'), $checkbox->cssId);
                $str .= $checkbox->show() . $label->show();
            }
        } else {
            $str = '';
        }
        return $str . $objTable->show();
    }

    /**
     * prevew the files in thumbnails view
     *
     * Long description (if any) ...
     *
     * @param  array  $subFolders Parameter description (if any) ...
     * @param  array  $files      Parameter description (if any) ...
     * @return object Return description (if any) ...
     * @access public
     */
    function previewThumbnails($subFolders, $files, $symlinks, $restriction, $mode, $name, $forceRestriction = FALSE) {
        $objTable = $this->newObject('htmltable', 'htmlelements');
        $objTable->cssId = $this->objLanguage->languageText('mod_filemanager_filemanagertableclass', 'filemanager', 'filemanagerTable');
        $objCleanUrl = $this->getObject("cleanurl", "filemanager");
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objMimeType = $this->newObject("mimetypes", "files");
        $objEmbed = $this->newObject("fileembed", "filemanager");
        $this->objFileIcons->size = 'large';
        //The dom document
        $domDoc = new DOMDocument('UTF-8');
        //The dom elements array
        $domElements = array();
        // Set Restriction as empty if it is none
        if (count($restriction) == 1 && $restriction[0] == '') {
            $restriction = array();
        }

        $hidden = 0;

        if (count($subFolders) == 0 && count($files) == 0 && count($symlinks) == 0) {
            $objTable->startRow();
            $objTable->addCell('<em>' . $this->objLanguage->languageText('mod_filemanager_nofilesorfolders', 'filemanager', 'No files or folders found') . '</em>', NULL, NULL, NULL, 'noRecordsMessage', 'colspan="5"');
            $objTable->endRow();
        } else {

            if (count($subFolders) > 0) {
                foreach ($subFolders as $folder) {
                    //The DOM folder link
                    $domElements['viewDiv'] = $domDoc->createElement('div');
                    $domElements['viewDiv']->setAttribute('class', 'fm_thumbnails');
                    $domElements['folderLink'] = $domDoc->createElement('a');
                    //The DOM icon folder
                    $domElements['folderIcon'] = $domDoc->createElement('img');
                    $domElements['folderIcon']->setAttribute('src', $this->objFileIcons->getIconSrc('folder'));
                    $domElements['folderIcon']->setAttribute('class', 'iconThumbnail');
                    $domElements['folderLink']->setAttribute('title', $this->objLanguage->languageText('mod_filemanager_clicktoopen', 'filemanager'));
                    $domElements['folderLink']->setAttribute('href', str_replace('amp;', '', $this->uri(array('action' => 'viewfolder', 'folder' => $folder['id'], 'view' => $this->viewType), $this->targetModule)));

                    $objTable->startRow();
                    if ($this->editPermission) {
                        //The DOM folder checkbox
                        $domElements['folderCheckbox'] = $domDoc->createElement('input');
                        $domElements['folderCheckbox']->setAttribute('type', 'checkbox');
                        $domElements['folderCheckbox']->setAttribute('name', 'files[]');
                        //The DOM delete link
                        $domElements['deleteconfirm'] = $domDoc->createElement('a');
                        $domElements['deleteconfirm']->appendChild($domDoc->createTextNode($this->objLanguage->languageText('word_delete', 'system')));
                        $domElements['deleteconfirm']->setAttribute('class', $this->objLanguage->languageText('mod_filemanager_buttonlinkclass', 'filemanager'));
                        $domElements['deleteconfirm']->setAttribute('href', str_replace('amp;', '', $this->uri(array('action' => 'deletefolder', 'id' => $folder['id'], 'module' => $this->targetModule))));
                        $domElements['viewDiv']->appendChild($domElements['folderCheckbox']);
                        $domElements['viewDiv']->appendChild($domElements['deleteconfirm']);
                        $domElements['viewDiv']->appendChild($domDoc->createElement('br'));
                    }

                    //TODO: make this a reusable function
                    //variables to store informatin partaining folder contants
                    $nmbrOfFiles = count($this->objFiles->getFolderFiles($folder['folderpath']));
                    $nmbrOfFolders = count($this->objFolder->getSubFolders($folder['id']));

                    //Assign the value to be displayed on the link's title depending on it's contents
                    if ($nmbrOfFiles == 0 && $nmbrOfFolders == 0) {
                        $titleString = $this->objLanguage->languageText("mod_filemanager_emptyfolderindicator", "filemanager");
                    } else {
                        $titleString = $this->objLanguage->languageText("mod_filemanager_contentsindicator", "filemanager");
                        $titleString = substr($titleString, 0, 9) . $nmbrOfFolders . substr($titleString, 8, 11) . $nmbrOfFiles . substr($titleString, 18, 12);
                    }
                    //End of subfolder and files count

                    $domElements['folderCheckbox']->setAttribute('id', htmlentities('input_files_' . basename($folder['folderpath'])));
                    $domElements['folderCheckbox']->setAttribute('value', 'folder_' . $folder['id']);

                    //The value to appear when the mouse is over the link
                    $domElements['folderParagraph'] = $domDoc->createElement('p');
                    $domElements['folderParagraph']->setAttribute('class', 'filedetails');
                    $domElements['folderParagraph']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("phrase_foldername", "system") . ": " . substr(basename($folder['folderpath']), 0, 12)));
                    $domElements['folderParagraph']->appendChild($domDoc->createElement('br'));
                    $domElements['folderParagraph']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("word_files", "system") . ": " . count($this->objFiles->getFolderFiles($folder['folderpath']))));
                    $domElements['folderParagraph']->appendChild($domDoc->createElement('br'));
                    $domElements['folderParagraph']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("word_folders", "system") . ": " . count($this->objFolder->getSubFolders($folder['id']))));
                    //creating space between the link at the top and the string below
                    $domElements['viewDiv']->appendChild($domDoc->createElement('p'));
                    $domElements['folderLink']->appendChild($domElements['folderIcon']);
                    $domElements['folderLink']->appendChild($domElements['folderParagraph']);
                    $domElements['viewDiv']->appendChild($domDoc->createElement('br'));
                    $domElements['viewDiv']->appendChild($domElements['folderLink']);

                    $accessVal = null;
                    if (key_exists("access", $folder)) {
                        $accessVal = $folder['access'];
                    }
                    if ($accessVal == 'private_all') {
                        $domElements['folderParagraph']->appendChild($domDoc->createElement('br'));
                        $domElements['folderParagraph']->appendChild($domDoc->createTextNode($this->objLanguage->languageText('word_access', 'system') . ': '));
                        $domElements['folderParagraph']->appendChild($domDoc->createTextNode($folder['access']));
                    }
                    $objTable->addCell($domDoc->saveHTML($domElements['viewDiv']));
                    $objTable->endRow();
                }
            }

            if (is_array($symlinks)) {
                $files = array_merge($files, $symlinks);
            }

            if (count($files) > 0) {
                $objFileSize = new formatfilesize();

                foreach ($files as $file) {
                    $visibility = null;
                    if (key_exists("visibility", $file)) {
                        $visibility = $file['visibility'];
                    }
                    $showFile = true;
                    if ($visibility == 'hidden') {
                        if ($file['creatorid'] == $this->objUser->userid()) {
                            $showFile = true;
                        } else {
                            $showFile = false;
                        }
                    }
                    if (!$showFile) {
                        continue;
                    }
                    if (count($restriction) > 0) {
                        if (!in_array(strtolower($file['datatype']), $restriction)) {
                            $objTable->startRow('hidefile');
                            $hidden++;
                        } else {
                            $objTable->startRow();
                        }
                    } else {
                        $objTable->startRow();
                    }

                    if ($this->editPermission) {
                        //DOM checkbox
                        $domElements['checkbox'] = $domDoc->createElement('input');
                        $domElements['checkbox']->setAttribute('type', 'checkbox');
                        $domElements['checkbox']->setAttribute('name', 'files[]');
                        //DOM link
                        $domElements['editLink'] = $domDoc->createElement('a');
                        $domElements['editLink']->setAttribute('title', $this->objLanguage->languageText('mod_filemanager_clicktoedit','filemanager'));
                        $domElements['editLink']->setAttribute('href', str_replace('amp;', '', $this->uri(array('action' => 'editfiledetails', 'id' => $file['id']), $this->targetModule)));

                        if (isset($file['symlinkid'])) {
                            $domElements['checkbox']->setAttribute('value', 'symlink_' . $file['symlink']);
                        } else {
                            $domElements['checkbox']->setAttribute('value', $file['id']);
                        }

                        //The DOM div elements
                        $domElements['viewDiv'] = $domDoc->createElement('div');
                        $domElements['viewDiv']->setAttribute('class', 'fm_thumbnails');
                        $domElements['checkbox']->setAttribute('id', htmlentities('input_files_' . $file['filename']));
                        $domElements['viewDiv']->appendChild($domElements['checkbox']);
                        $domElements['editLink']->setAttribute('class', $this->objLanguage->languageText("mod_filemanager_buttonlinkclass", "filemanager"));
                        $domElements['editLink']->appendChild($domDoc->createTextNode(substr($this->objLanguage->languageText("mod_filemanager_editfiledetails", "filemanager"), 0, 4)));
                        $domElements['viewDiv']->appendChild($domElements['editLink']);
                    }

                    if (isset($file['symlinkid'])) {
                        //The DOM file link
                        $domElements['fileLink'] = $domDoc->createElement('a');
                        $domElements['fileLink']->setAttribute('href', str_replace('amp;', '', $this->uri(array('action' => 'symlink', 'id' => $file['symlinkid']))));
                    } else {
                        //The DOM file link
                        $domElements['fileLink'] = $domDoc->createElement('a');
                        $domElements['fileLink']->setAttribute('href', str_replace('amp;', '', $this->uri(array('action' => 'fileinfo', 'id' => $file['id']), $this->targetModule)));
                    }
                    $linkTitle = '';
                    $access = null;
                    if (key_exists("access", $file)) {
                        $access = $file['access'];
                    }

                    if ($access == 'private_all') {
                        $objIcon->setIcon('info');
                        $linkTitle = basename($file['filename']) . $objIcon->show();
                    }
                    $domElements['fileLink']->setAttribute('title', $this->objLanguage->languageText('mod_filemanager_clicktoviewinfo', 'filemanager'));
                    //The DOM download link
                    $domElements['downloadLink'] = $domDoc->createElement('a');
                    $domElements['downloadLink']->setAttribute('title', $this->objLanguage->languageText('mod_filemanager_clicktodownload', 'filemanager'));
                    $domElements['downloadLink']->setAttribute('href', $objCleanUrl->cleanUpUrl(($this->objAltConfig->getcontentPath() . $file['path'])));
                    $domElements['downloadLink']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("mod_filemanager_downloadlinkvalue", "filemanager")));
                    $domElements['downloadLink']->setAttribute('class', $this->objLanguage->languageText("mod_filemanager_buttonlinkclass", "filemanager"));
                    $domElements['viewDiv']->appendChild($domElements['downloadLink']);
                    //creating space between the link at the top and the string below
                    $domElements['viewDiv']->appendChild($domDoc->createElement('p'));

                    $filepath = $this->objAltConfig->getSiteRoot() . '/usrfiles/' . $file['path'];
                    $fileType = $this->getObject("fileparts", "files");

                    //Text to display video and audio file information
                    $domElements['playerString'] = $domDoc->createElement('p');
                    $domElements['playerString']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("word_filename", "system") . ": " . substr($file['filename'], 0, 12) . ".."));
                    //new line
                    $domElements['playerString']->appendChild($domDoc->createElement('br'));
                    $domElements['playerString']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("phrase_filesize", "system") . ": " . $objFileSize->formatsize($file['filesize'])));
                    //new line
                    $domElements['playerString']->appendChild($domDoc->createElement('br'));
                    $domElements['playerString']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("phrase_filetype", "system") . ": " . $fileType->getExtension($file['filename'])));
                    //new line
                    $domElements['playerString']->appendChild($domDoc->createElement('br'));
                    $domElements['playerString']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("phrase_dateuploaded", "system") . ": " . $file['datecreated']));

                    //The DOM image paragraph (to display image file information)
                    $domElements['imgParagraph'] = $domDoc->createElement('p');
                    $domElements['imgParagraph']->setAttribute('class', 'filedetails');
                    $domElements['imgParagraph']->appendChild($domDoc->createElement('br'));
                    $domElements['imgParagraph']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("word_filename", "system") . ": " . substr($file['filename'], 0, 10)));
                    //new line
                    $domElements['imgParagraph']->appendChild($domDoc->createElement('br'));
                    $domElements['imgParagraph']->appendChild($domDoc->createtextNode($this->objLanguage->languageText("phrase_filesize", "system") . ": " . $objFileSize->formatsize($file['filesize'])));
                    //new line
                    $domElements['imgParagraph']->appendChild($domDoc->createElement('br'));
                    $domElements['imgParagraph']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("phrase_filetype") . ": " . $fileType->getExtension($file['filename'])));
                    //new line
                    $domElements['imgParagraph']->appendChild($domDoc->createElement('br'));
                    $domElements['imgParagraph']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("phrase_dateuploaded", "system") . ": " . $file['datecreated']));

                    // generate image thumbnails
                    //The DOM image
                    $domElements['image'] = $domDoc->createElement('img');
                    if (ereg("image", $file['mimetype'])) {
                        $domElements['image']->setAttribute('src', str_replace('amp;', '', $this->objAltConfig->getcontentPath() . $file['path']));
                        $domElements['image']->setAttribute('class', 'imgThumbnail');
                        $domElements['fileLink']->appendChild($domElements['image']);
                        $domElements['fileLink']->appendChild($domElements['imgParagraph']);
                        $domElements['viewDiv']->appendChild($domElements['fileLink']);
                    }

                    //create audio/video player object
                    $objPlayer = "";
                    if (ereg("audio", $file['mimetype'])) {
                        $objPlayer = $objEmbed->showSoundPlayer($objCleanUrl->cleanUpUrl(($this->objAltConfig->getcontentPath() . $file['path'])));
                        $domElements['fileLink']->appendChild($domElements['playerString']);
                        $domElements['viewDiv']->appendChild($domElements['fileLink']);
                    }

                    //video
                    if (ereg("video", $file['mimetype'])) {
                        $objPlayer = $objEmbed->showWithFlowPlayer($objCleanUrl->cleanUpUrl(($this->objAltConfig->getcontentPath() . $file['path'])));
                        $domElements['fileLink']->appendChild($domElements['playerString']);
                        $domElements['viewDiv']->appendChild($domElements['fileLink']);
                    }
                    
                    //zip
                    

                    //other formats
                    if (!ereg("audio", $file['mimetype']) && !ereg("image", $file['mimetype']) && !ereg("video", $file['mimetype'])) {
                        $domElements['image']->setAttribute('src', $this->objFileIcons->getIconSrc($file['datatype']));
                        $domElements['image']->setAttribute('class', 'iconThumbnail');
                        $domElements['image']->normalize();
                        $domElements['fileLink']->appendChild($domElements['image']);
                        $domElements['fileLink']->appendChild($domElements['imgParagraph']);
                        $domElements['viewDiv']->appendChild($domElements['fileLink']);
                    }


                    if ($mode == 'fckimage' || $mode == 'fckflash' || $mode == 'fcklink') {
                        //Disable file preview
                        $domElements['fileLink']->removeAttribute('href');
                        //The DOM select string
                        $domElements['selectStr'] = $domDoc->createElement('a');
                        $domElements['selectStr']->appendChild($domDoc->createTextNode($this->objLanguage->languageText('word_select', 'system')));
                        $domElements['selectStr']->setAttribute('href', 'javascript:selectFile("' . $filepath . '");');
                        $domElements['selectStr']->setAttribute('class', $this->objLanguage->languageText('mod_filemanager_buttonlinkclass', 'filemanager'));
                        $domElements['viewDiv']->appendChild($domElements['selectStr']);
                        $objTable->addCell($domDoc->saveHTML($domElements['viewDiv']) . $objPlayer);
                    } else if ($mode == 'selectfilewindow') {
                        $domElements['fileLink']->removeAttribute('href');
                        //The DOM select file string
                        $domElements['selectFileStr'] = $domDoc->createElement('a');
                        $domElements['selectFileStr']->setAttribute('href', 'javascript:selectFileWindow("' . $name . '","' . $file['filename'] . '","' . $file['id'] . '");');
                        $domElements['selectFileStr']->appendChild($domDoc->createTextNode($this->objLanguage->languageText('word_select', 'system')));
                        $domElements['selectFileStr']->setAttribute('class', $this->objLanguage->languageText('mod_filemanager_buttonlinkclass', 'filemanager'));
                        $domElements['viewDiv']->appendChild($domElements['selectFileStr']);
                        $objTable->addCell($domDoc->saveHTML($domElements['viewDiv']) . $objPlayer);
                    } else if ($mode == 'selectimagewindow') {
                        $domElements['fileLink']->removeAttribute('href');
                        //The DOM image select link
                        $domElements['imageSelect'] = $domDoc->createElement('a');
                        $domElements['imageSelect']->setAttribute('href', 'javascript:selectImageWindow("' . $name . '","' . $filepath . '","' . $file['filename'] . '","' . $file['id'] . '");');
                        $domElements['imageSelect']->appendChild($domDoc->createTextNode($this->objLanguage->languageText('word_select', 'system')));
                        $domElements['imageSelect']->setAttribute('class', $this->objLanguage->languageText('mod_filemanager_buttonlinkclass', 'filemanager'));
                        $domElements['viewDiv']->appendChild($domElements['imageSelect']);
                        $objTable->addCell($domDoc->saveHTML($domElements['viewDiv']) . $objPlayer);
                    } else {
                        //return the document with all available objects and elements
                        $objTable->addCell($domDoc->saveHTML($domElements['viewDiv']) . $objPlayer);
                        $objTable->endRow();
                    }
                }
            }
        }

        if ($hidden > 0 && count($restriction) > 0) {
            $str = '';
            $str .= '<style type="text/css">
#filemanagerTable tr.hidefile {display:none;}
</style>';
            $str .= $this->objLanguage->languageText('mod_filemanager_browsingfor', 'filemanager', 'Browsing for') . ': ';
            $comma = '';
            foreach ($restriction as $restrict) {
                $str .= $comma . $restrict;
                $comma = ', ';
            }
            if (!$forceRestriction) {
                $str .= '<script type="text/javascript">
var onOrOff = "off";
function turnOnFiles(value)
{
    if (onOrOff == \'off\') {
        jQuery(\'#filemanagerTable tr.hidefile\').each(function (i) {
            this.style.display = \'inline-block\';
        });
        adjustLayout();
        onOrOff = "on";
    } else {
        jQuery(\'#filemanagerTable tr.hidefile\').each(function (i) {
            this.style.display = \'none\';
        });
        adjustLayout();
        onOrOff = "off";
    }
}
</script>';
                $str .= ' &nbsp; - ';
                $this->loadClass('checkbox', 'htmlelements');
                $this->loadClass('label', 'htmlelements');
                $checkbox = new checkbox('showall');
                $checkbox->extra = ' onclick="turnOnFiles();"';
                $label = new label($this->objLanguage->languageText('mod_filemanager_showallfiles', 'filemanager', 'Show All Files'), $checkbox->cssId);
                $str .= $checkbox->show() . $label->show();
            }
        } else {
            $str = '';
        }
        return $str . $objTable->show();
    }

}

?>
