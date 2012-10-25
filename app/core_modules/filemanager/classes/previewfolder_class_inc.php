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
        if ($this->viewType == strtolower($this->objLanguage->languageText("phrase_thumbnailview","system"))) {
            return $this->previewThumbnails($subFolders, $files, $symlinks, $restriction, $mode, $name, $forceRestriction);
        }else{
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

 
                    //TODO: make this a reusable function
		    //variables to store informatin partaining folder contants
		    $nmbrOfFiles = count($this->objFiles->getFolderFiles($folder['folderpath']));
                    $nmbrOfFolders = count($this->objFolder->getSubFolders($folder['id']));

                    //Assign the value to be displayed on the link's title depending on it's contents
		if ($nmbrOfFiles == 0 && $nmbrOfFolders == 0) {
                        $titleString = $this->objLanguage->languageText("mod_filemanager_emptyfolderindicator","filemanager");
                    }else{
                    $titleString = $this->objLanguage->languageText("mod_filemanager_contentsindicator","filemanager");
			$titleString = substr($titleString,0,9).$nmbrOfFolders.substr($titleString,8,11).$nmbrOfFiles.substr($titleString,18,12);
			}
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

    function previewThumbnails($subFolders, $files, $symlinks, $restriction, $mode, $name, $forceRestriction = FALSE) {
        $objTable = $this->newObject('htmltable', 'htmlelements');
        $objTable->cssId = $this->objLanguage->languageText('mod_filemanager_filemanagertableclass', 'filemanager', 'filemanagerTable');
        $objCleanUrl = $this->getObject("cleanurl", "filemanager");
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objMimeType = $this->newObject("mimetypes", "files");
        $objEmbed = $this->newObject("fileembed", "filemanager");
        $objImage;
	//The dom document
	$domDoc = new DOMDocument('UTF-8');
	//The dom elements
	$domElements = array();
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
        } else{

            if (count($subFolders) > 0) {
                foreach ($subFolders as $folder) {
                    $folderLink = new link($this->uri(array('action' => 'viewfolder', 'folder' => $folder['id'],'view'=> $this->viewType), $this->targetModule));
                    $objTable->startRow();
                    if ($this->editPermission) {

                    //TODO: make this a reusable function
		    //variables to store informatin partaining folder contants
		    $nmbrOfFiles = count($this->objFiles->getFolderFiles($folder['folderpath']));
                    $nmbrOfFolders = count($this->objFolder->getSubFolders($folder['id']));

                    //Assign the value to be displayed on the link's title depending on it's contents
		if ($nmbrOfFiles == 0 && $nmbrOfFolders == 0) {
                        $titleString = $this->objLanguage->languageText("mod_filemanager_emptyfolderindicator","filemanager");
                    }else{
                    	$titleString = $this->objLanguage->languageText("mod_filemanager_contentsindicator","filemanager");
			$titleString = substr($titleString,0,9).$nmbrOfFolders.substr($titleString,8,11).$nmbrOfFiles.substr($titleString,18,12);
			}

                        $checkbox = new checkbox('files[]');
                        $deleteconfirm = $this->newObject('jqueryconfirm', 'utilities');
                        $deleteconfirm->setConfirm('delete', $this->uri(array('action' => 'deletefolder', 'id' => $folder['id'])), $this->objLanguage->languageText('mod_filemanager_areyousuredeletefiles', 'filemanager'));
                        $checkbox->cssId = htmlentities('input_files_' . basename($folder['folderpath']));
                        $checkbox->value = 'folder__' . $folder['id'];

                    	//The value to appear when the mouse is over the link
			$folderLink->title = $titleString;
                        $folderLink->link = $folderIcon . "<p class='filedetails' ><br />".$this->objLanguage->languageText("phrase_foldername","system").": " . substr(basename($folder['folderpath']), 0, 12) . "<br />".$this->objLanguage->languageText("word_files","system").": " . count($this->objFiles->getFolderFiles($folder['folderpath'])) . "<br />".$this->objLanguage->languageText("word_folders","system").": " . count($this->objFolder->getSubFolders($folder['id'])) . "</p>";
                        
$accessVal = null;
                        if (key_exists("access", $folder)) {
                            $accessVal = $folder['access'];
                        }
                        if ($accessVal == 'private_all') {
                            $objIcon->setIcon('info');
                        }
                        //$objTable->startRow();
			$viewDiv = "<div class='fm_thumbnails' >".$checkbox->show() . $deleteconfirm->show() . $folderLink->show()."</div>";
                        $objTable->addCell($viewDiv);
                        $objTable->endRow();
                    }
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
                        $strPermissions = "";
			//DOM checkbox
			$domElements['checkbox'] = $domDoc->createElement('input');
			$domElements['checkbox']->setAttribute('type','checkbox');
			$domElements['checkbox']->setAttribute('name','files[]');
                        $checkbox = new checkbox('files[]');
			//DOM link
			$domElements['editLink'] = $domDoc->createElement('a');
			$domElements['editLink']->setAttribute('href',str_replace('amp;','',$this->uri(array('action' => 'editfiledetails', 'id' => $file['id']), $this->targetModule)));
                        $editLink = new link($this->uri(array('action' => 'editfiledetails', 'id' => $file['id']), $this->targetModule));

                        if (isset($file['symlinkid'])) {
			    $domElements['checkbox']->setAttribute('value','symlink_'.$file['symlink']);
                            $checkbox->value = 'symlink__' . $file['symlinkid'];
                        } else {
			    $domElements['checkbox']->setAttribute('value',$file['id']);
                            $checkbox->value = $file['id'];
                        }

			$domElements['viewDiv'] = $domDoc->createElement('div');
			$domElements['viewDiv']->setAttribute('class','fm_thumbnails');
                        $checkbox->cssId = htmlentities('input_files_' . $file['filename']);
			$domElements['checkbox']->setAttribute('id',htmlentities('input_files_'.$file['filename']));
                        $strPermissions .= $checkbox->show();
			$domElements['viewDiv']->appendChild($domElements['checkbox']);
			$domElements['editLink']->setAttribute('class',$this->objLanguage->languageText("mod_filemanager_buttonlinkclass","filemanager"));
                    	$editLink->cssClass = $this->objLanguage->languageText("mod_filemanager_buttonlinkclass","filemanager");
			$domElements['editLink']->appendChild($domDoc->createTextNode(substr($this->objLanguage->languageText("mod_filemanager_editfiledetails","filemanager"),0,4)));
			$domElements['viewDiv']->appendChild($domElements['editLink']);
                    	$editLink->link = substr($this->objLanguage->languageText("mod_filemanager_editfiledetails","filemanager"),0,4);
                    	$strPermissions .= $editLink->show();
                    }
        	$viewDiv="";

                    if (isset($file['symlinkid'])) {
			//The DOM file link
			$domElements['fileLink'] = $domDoc->createElement('a');
			$domElements['fileLink']->setAttribute('href',str_replace('amp;','',$this->uri(array('action' => 'symlink', 'id' => $file['symlinkid']))));
                        $fileLink = new link($this->uri(array('action' => 'symlink', 'id' => $file['symlinkid'])));
                    } else {
			//The DOM file link
			$domElements['fileLink'] = $domDoc->createElement('a');
			$domElements['fileLink']->setAttribute('href',str_replace('amp;','',$this->uri(array('action' => 'fileinfo', 'id' => $file['id']), $this->targetModule)));
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
                    }
                    	$downloadLink = new link($objCleanUrl->cleanUpUrl(($this->objAltConfig->getcontentPath() . $file['path'])));
			//The DOM download link
			$domElements['downloadLink'] = $domDoc->createElement('a');
			$domElements['downloadLink']->setAttribute('href',$objCleanUrl->cleanUpUrl(($this->objAltConfig->getcontentPath() . $file['path'])));
			$domElements['downloadLink']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("mod_filemanager_downloadlinkvalue","filemanager")));
			$domElements['downloadLink']->setAttribute('class',$this->objLanguage->languageText("mod_filemanager_buttonlinkclass","filemanager"));
			$domElements['viewDiv']->appendChild($domElements['downloadLink']);
			//creating space between the link at the top and the string below
			$domElements['viewDiv']->appendChild($domDoc->createElement('p'));

                    	$downloadLink->link = $this->objLanguage->languageText("mod_filemanager_downloadlinkvalue","filemanager");
                    	$downloadLink->cssClass = $this->objLanguage->languageText("mod_filemanager_buttonlinkclass","filemanager");
			$strPermissions .= $downloadLink->show();
			$viewDiv .= $strPermissions;
                        $filepath = $this->objAltConfig->getSiteRoot() . '/usrfiles/' . $file['path'];
                        $fileType = $this->getObject("fileparts", "files");

                        //Text to display video and audio file information
			$domElements['playerString'] = $domDoc->createElement('p');
			$domElements['playerString']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("word_filename","system") .": ". substr($file['filename'], 0, 12) . ".."));
			//new line
			$domElements['playerString']->appendChild($domDoc->createElement('br'));
			$domElements['playerString']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("phrase_filesize","system").": ".$objFileSize->formatsize($file['filesize'])));
			//new line
			$domElements['playerString']->appendChild($domDoc->createElement('br'));
			$domElements['playerString']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("phrase_filetype","system").": ".$fileType->getExtension($file['filename'])));
			//new line
			$domElements['playerString']->appendChild($domDoc->createElement('br'));
			$domElements['playerString']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("phrase_dateuploaded","system").": ".$file['datecreated']));

                        $playerString = $this->objLanguage->languageText("word_filename","system") .": ". substr($file['filename'], 0, 10) . "..<br />".$this->objLanguage->languageText("phrase_filesize","system").": " . $file['filesize'] . "kb<br />".$this->objLanguage->languageText("phrase_mimetype","system").": " . $fileType->getExtension($file['filename']) ."<br />".$this->objLanguage->languageText("phrase_dateuploaded","system").": " . $file['datecreated'];



			//The DOM image paragraph (to display image file information)
			$domElements['imgParagraph'] = $domDoc->createElement('p');
			$domElements['imgParagraph']->setAttribute('class','filedetails');
			$domElements['imgParagraph']->appendChild($domDoc->createElement('br'));
			$domElements['imgParagraph']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("word_filename","system") .": ".substr($file['filename'], 0, 10)));
			//new line
			$domElements['imgParagraph']->appendChild($domDoc->createElement('br'));
			$domElements['imgParagraph']->appendChild($domDoc->createtextNode($this->objLanguage->languageText("phrase_filesize","system").": ".$objFileSize->formatsize($file['filesize'])));
			//new line
			$domElements['imgParagraph']->appendChild($domDoc->createElement('br'));
			$domElements['imgParagraph']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("phrase_filetype").": ".$fileType->getExtension($file['filename'])));
			//new line
			$domElements['imgParagraph']->appendChild($domDoc->createElement('br'));
			$domElements['imgParagraph']->appendChild($domDoc->createTextNode($this->objLanguage->languageText("phrase_dateuploaded","system").": ".$file['datecreated']));

                        //Text to display image file information
                        $imageParagraph = "<p class='filedetails' ><br /><br />".$this->objLanguage->languageText("word_filename","system") ." : ". substr($file['filename'], 0, 10) . "..<br />".$this->objLanguage->languageText("phrase_filesize","system").": " . $file['filesize'] . " kb<br />".$this->objLanguage->languageText("phrase_mimetype").": " . $fileType->getExtension($file['filename']) . "<br />".$this->objLanguage->languageText("phrase_dateuploaded","system").": " . $file['datecreated'] . "</p>";
                    

                    // generate image thumbnails
		    //The DOM image
		    $domElements['image'] = $domDoc->createElement('img');
                    if (ereg("image", $file['mimetype'])) {
			$domElements['image']->setAttribute('src',str_replace('amp;','',$this->objAltConfig->getcontentPath() . $file['path']));
			$domElements['fileLink']->appendChild($domElements['image']);
			$domElements['fileLink']->appendChild($domElements['imgParagraph']);
			$domElements['viewDiv']->appendChild($domElements['fileLink']);
                        $fileLink->link = $objEmbed->embed($objCleanUrl->cleanUpUrl(($this->objAltConfig->getcontentPath() . $file['path'])), $this->objLanguage->languageText("word_image","system")) . $imageParagraph;
                    }

                    //create audio player object
                    $objPlayer = "";
                    if (ereg("audio", $file['mimetype'])) {
                        $fileLink->link = $playerString;
                        $objPlayer = $objEmbed->showSoundPlayer($objCleanUrl->cleanUpUrl(($this->objAltConfig->getcontentPath() . $file['path'])));
			$domElements['fileLink']->appendChild($domElements['playerString']);
			$domElements['viewDiv']->appendChild($domElements['fileLink']);
			$viewDiv .= $objPlayer;
                    }

                    //video
                    if (ereg("video", $file['mimetype'])) {
                        $fileLink->link =$playerString;
                        $objPlayer = $objEmbed->showWithFlowPlayer($objCleanUrl->cleanUpUrl(($this->objAltConfig->getcontentPath() . $file['path'])));
			$viewDiv .=  $objPlayer;
			$domElements['fileLink']->appendChild($domElements['playerString']);
			$domElements['viewDiv']->appendChild($domElements['fileLink']);
                    }

                    //other formats
			 if( !ereg("audio", $file['mimetype']) && !ereg("image", $file['mimetype']) &&  !ereg("video", $file['mimetype'])) {
                        	$objImage = $this->objFileIcons->getExtensionIcon($fileType->getExtension($file['filename']));
                        	$fileLink->link = $objImage.$imageParagraph;
				$domElements['image']->setAttribute('src',$this->objAltConfig->getskinRoot().'_common/icons/filetypes32/'.$fileType->getExtension($file['filename']).'.png');
				$domElements['fileLink']->appendChild($domElements['image']);
				$domElements['fileLink']->appendChild($domElements['imgParagraph']);
				$domElements['viewDiv']->appendChild($domElements['fileLink']);
			}
                    

                    if ($mode == 'fckimage' || $mode == 'fckflash' || $mode == 'fcklink') {
			$viewDiv .= $fileLink->show()."</div>";
                    	$selectStr = '<a href=\'javascript:selectFile("' . $filepath . '");\'>' . $viewDiv. '</a>';
                        $objTable->addCell($selectStr);
                    } else if ($mode == 'selectfilewindow') {
			$selectFileStr = '<a href=\'javascript:selectFileWindow("' . $name . '","' . $file['filename'] . '","' . $file['id'] . '");\'>' . $viewDiv. '</a>';
                        $objTable->addCell($selectFileStr);
                    } else if ($mode == 'selectimagewindow') {
                 	$selectImageStr = '<a href=\'javascript:selectImageWindow("' . $name . '", "' . $filepath . '","' . $file['filename'] . '","' . $file['id'] . '");\' class=\' ' .$this->objLanguage->languageText('mod_filemanager_buttonlinkclass','filemanager').' \' >'.$this->objLanguage->languageText('word_select','system').'</a>'; 
			$viewDiv .= $selectImageStr."<br />".$fileLink->show()."</div>";
                        $objTable->addCell($viewDiv);
                    } else {
                        //close the paragraph element and append all elements inside the div
                        $viewDiv .= $fileLink->show()."";
                        $objTable->addCell($domDoc->saveHTML($domElements['viewDiv']).$objPlayer);
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
