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
     *@access public
     * @var boolean
     */
    public $editPermission = TRUE;
    /**
     * The string variable to store the target module
     *@access public
     * @var string
     */
    public $targetModule = "filemanager";


    /**
     * The string to indicate the current type of view
     *@access public
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
        if ($this->viewType == "list" || empty($this->viewType)) {
            return $this->previewLongView($subFolders, $files, $symlinks, $restriction, $mode, $name, $forceRestriction);
        }
        if ($this->viewType == "thumbnails") {
            return $this->previewThumbnails($subFolders, $files, $symlinks, $restriction, $mode, $name, $forceRestriction);
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

                    $folderLink = new link($this->uri(array('action' => 'viewfolder', 'folder' => $folder['id'],'view'=> $this->viewType), $this->targetModule));


                    $extTitle = '';
                    $accessVal = null;

                    if (key_exists("access", $folder)) {
                        $accessVal = $folder['access'];
                    }

                    if ($accessVal == 'private_all') {
                        $objIcon->setIcon('info');
                        $extTitle = $objIcon->show();
                    }

                    $NmbrofFiles = count($this->objFiles->getFolderFiles($folder['folderpath']));
                    $NmbrofFolders = count($this->objFolder->getSubFolders($folder['id']));
                    $TitleString = "contains " . $NmbrofFolders . " folder(s) and " . $NmbrofFiles . " file(s)";

                    if ($NmbrofFiles == 0 && $NmbrofFolders == 0) {
                        $TitleString = "empty";
                    }
                    $folderLink->title = $TitleString;
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

    function previewThumbnails($subFolders, $files, $symlinks, $restriction, $mode, $name, $forceRestriction = FALSE) {
        $objTable = $this->newObject('htmltable', 'htmlelements');
        $objTable->cssId = $this->objLanguage->languageText('mod_filemanager_filemanagertableclass', 'filemanager', 'filemanagerTable');
        $objCleanUrl = $this->getObject("cleanurl", "filemanager");
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objMimeType = $this->newObject("mimetypes", "files");
        $objEmbed = $this->newObject("fileembed", "filemanager");
        $ViewDiv;
        $objImage;
        $folderIcon = $this->objFileIcons->getExtensionIcon('folder');
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
                    $folderLink = new link($this->uri(array('action' => 'viewfolder', 'folder' => $folder['id'],'view'=> $this->viewType), $this->targetModule));
                    $objTable->startRow();
                    if ($this->editPermission) {

                        //TODO: Make this a reusable function
                        $NmbrofFiles = count($this->objFiles->getFolderFiles($folder['folderpath']));
                        $NmbrofFolders = count($this->objFolder->getSubFolders($folder['id']));
                        $TitleString = "contains " . $NmbrofFolders . " folder(s) and " . $NmbrofFiles . " file(s)";

                        if ($NmbrofFiles == 0 && $NmbrofFolders == 0) {
                            $TitleString = "empty";
                        }
                        $checkbox = new checkbox('files[]');
                        $Deleteconfirm = $this->newObject('jqueryconfirm', 'utilities');
                        $Deleteconfirm->setConfirm('delete', $this->uri(array('action' => 'deletefolder', 'id' => $folder['id'])), $this->objLanguage->languageText('mod_filemanager_areyousuredeletefiles', 'filemanager'));
                        $checkbox->cssId = htmlentities('input_files_' . basename($folder['folderpath']));
                        $checkbox->value = 'folder__' . $folder['id'];
                        $folderLink->link = $folderIcon . "<p class='filedetails' ><br />Folder name: " . substr(basename($folder['folderpath']), 0, 12) . "<br />Files: " . $NmbrofFiles . "<br />Folders: " . $NmbrofFolders . "</p>";
                        $accessVal = null;
                        $folderLink->title = $TitleString;
                        if (key_exists("access", $folder)) {
                            $accessVal = $folder['access'];
                        }
                        if ($accessVal == 'private_all') {
                            $objIcon->setIcon('info');
                        }
                        $objTable->startRow();
                        $ViewDiv = "<div class='fm_thumbnails' >".$checkbox->show() . $Deleteconfirm->show() . $folderLink->show()."</div>";
                        $objTable->addCell($ViewDiv);
                        $objTable->endRow();
                    }
                }
            }

            if (is_array($symlinks)) {
                $files = array_merge($files, $symlinks);
            }

            if (count($files) > 0) {
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
                        $strPermissions = "";
                        $checkbox = new checkbox('files[]');
                        $editLink = new link($this->uri(array('action' => 'editfiledetails', 'id' => $file['id']), $this->targetModule));

                        if (isset($file['symlinkid'])) {
                            $checkbox->value = 'symlink__' . $file['symlinkid'];
                        } else {
                            $checkbox->value = $file['id'];
                        }

                        $checkbox->cssId = htmlentities('input_files_' . $file['filename']);
                        $strPermissions .= $checkbox->show();
                    }

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
                        $filepath = $this->objAltConfig->getSiteRoot() . '/usrfiles/' . $file['path'];
                        $fileType = $this->getObject("fileparts", "files");
                        //Text to display video and audio file information
                        $PlayerString = "Name: " . substr($file['filename'], 0, 17) . "..<br />Size: " . $file['filesize'] . "kb<br />Type: " . $fileType->getExtension($file['filename']) . "<br />License: " . $file['license'] . "<br />upload date: " . $file['datecreated'];
                        //Text to display image file information
                        $ImageParagraph = "<p class='filedetails' ><br /><br />Name: " . substr($file['filename'], 0, 15) . "..<br />size: " . $file['filesize'] . "kb<br />type: " . $fileType->getExtension($file['filename']) . "<br />license: " . $file['license'] . "<br />upload date: " . $file['datecreated'] . "</p>";
                    }
                    // generate image thumbnails
                    if (ereg("image", $file['mimetype'])) {
                        $fileLink->link = $objEmbed->embed($objCleanUrl->cleanUpUrl(($this->objAltConfig->getcontentPath() . $file['path'])), 'image') . $ImageParagraph;
                    }
                    //create and append audio player object
                    $objPlayer = "";
                    if (ereg("audio", $file['mimetype'])) {
                        $objPlayer = $objEmbed->showSoundPlayer($objCleanUrl->cleanUpUrl(($this->objAltConfig->getcontentPath() . $file['path'])));
                        $fileLink->link = $PlayerString;
                    }
                    //video
                    if (ereg("video", $file['mimetype'])) {
                        $player = $objEmbed->showWMV($objCleanUrl->cleanUpUrl(($this->objAltConfig->getcontentPath() . $file['path'])));
                        $fileLink->link = $PlayerString;
                    }
                    //other formats
                    if ($objMimeType->isValidMimeType($objMimeType->getMimeType($file['filename'])) && !ereg("image", $file['mimetype']) && !ereg("audio", $file['mimetype']) && !ereg("video", $file['mimetype'])) {
                        $objImage = $this->objFileIcons->getExtensionIcon($fileType->getExtension($file['filename']));
                        $fileLink->link = $objImage . $ImageParagraph;
                    }

                    $downloadLink = new link($objCleanUrl->cleanUpUrl(($this->objAltConfig->getcontentPath() . $file['path'])));
                    $downloadLink->link = "download";
                    $editLink->link = "edit";
                    $downloadLink->cssClass = "buttonlink";
                    $editLink->cssClass = "buttonlink";
                    $strPermissions .= $editLink->show();
                    $selectStr = '<a href=\'javascript:selectFile("' . $filepath . '");\'>' . basename($file['filename']) . '</a>';
                    $selectFileStr = '<a href=\'javascript:selectFileWindow("' . $name . '","' . $file['filename'] . '","' . $file['id'] . '");\'>' . basename($file['filename']) . '</a>';
                    $selectImageStr = '<a href=\'javascript:selectImageWindow("' . $name . '", "' . $filepath . '","' . $file['filename'] . '","' . $file['id'] . '");\'>' . basename($file['filename']) . '</a>';

                    if ($mode == 'fckimage' || $mode == 'fckflash' || $mode == 'fcklink') {

                        $objTable->addCell($selectStr);
                    } else if ($mode == 'selectfilewindow') {
                        $objTable->addCell($selectFileStr);
                    } else if ($mode == 'selectimagewindow') {

                    } else {
                        //append all elements inside the div
                        $ViewDiv = "<div class='fm_thumbnails' >".$strPermissions.$downloadLink->show()."</p>".$objPlayer.$fileLink->show()."</div>";
                        $objTable->addCell($ViewDiv);
                    }
                    $objTable->endRow();
                }
            }
        }
        $str = "<style type='text/css' >
                #filemanagerTable tr{
                        display: inline-block;
                        margin: 0.5pc;
                 }
                #filemanagerTable tr td a img{
                        width: 13pc;
                        height: 13pc;
                 }
                #filemanagerTable tr td{
                        height: 13pc;
                        width: 13pc;
                        border: 0.5pc solid #fff;
                        background: #fff;
                        box-shadow: 0 0 5px #5d5d5d;
                 }
                 #filemanagerTable tr td  a p.filedetails{
                        position: absolute;
                        margin-top: -13.5pc;
                        height: 13.5pc;
                        width: 13pc;
                        color: #000000;
                        text-shadow: 1px 0 1px #000000;
                        opacity: 0;
                        -moz-transition-property: opacity;
                        -moz-transition-duration: 0.5s;
                        -moz-transition-timing-function: ease;
                        -webkit-transition-timing-function: ease;
                        -webkit-transition-duration: 0.5s;
                        -webkit-transition-propery: opacity;
                   }
                   #filemanagerTable tr td a:hover{
                         text-decoration: none;
                   }
                   #filemanagerTable tr td a p:hover{
                         opacity: 0.85;
                         background: #FFF;
                   }
                   #filemanagerTable tr td p:hover{
                        
                   }
                 .buttonlink{
                        background: #CACACA;
                        margin: 1pc 0.5pc;
                 }
</style>";

        if ($hidden > 0 && count($restriction) > 0) {
            //$str = '';
            $str = '<style type="text/css">
                tr.hidefile {display:none;}
                </style>';
            $str .= $this->objLanguage->languageText('mod_filemanager_browsingfor', 'filemanager', 'Browsing for') . ': ';
            $comma = '';
            foreach ($restriction as $restrict) {
                $str .= $comma . $restrict;
                $comma = ', ';
            }
            if (!$forceRestriction) {
                $str = '<script type="text/javascript">
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
        }
        return $str . $objTable->show();
    }

}

?>