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
class previewfolder extends filemanagerobject
{


    public $editPermission = TRUE;
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->objFileIcons = $this->getObject('fileicons', 'files');
        $this->objLanguage = $this->getObject('language', 'language');
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
    function previewContent($subFolders, $files, $symlinks=array(), $restriction=array())
    {
        return $this->previewLongView($subFolders, $files, $symlinks, $restriction);
    
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
    function previewLongView($subFolders, $files, $symlinks, $restriction)
    {
        $objTable = $this->newObject('htmltable', 'htmlelements');
        
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
            $objTable->addCell('<em>'.$this->objLanguage->languageText('mod_filemanager_nofilesorfolders', 'filemanager', 'No files or folders found').'</em>', NULL, NULL, NULL, 'noRecordsMessage', 'colspan="5"');
            $objTable->endRow();
        } else {
        
            if (count($subFolders) > 0) {
                $folderIcon = $this->objFileIcons->getExtensionIcon('folder');
                
                foreach ($subFolders as $folder)
                {
                    $objTable->startRow();
                    
                    if ($this->editPermission) {
                        $checkbox = new checkbox('files[]');
                        $checkbox->value = 'folder__'.$folder['id'];
                        $checkbox->cssId = htmlentities('input_files_'.basename($folder['folderpath']));
                        
                        $objTable->addCell($checkbox->show(), 20);
                    }
                    
                    $objTable->addCell($folderIcon);
                    
                    $folderLink = new link ($this->uri(array('action'=>'viewfolder', 'folder'=>$folder['id'])));
                    $folderLink->link = basename($folder['folderpath']);
                    $objTable->addCell($folderLink->show());
                    $objTable->addCell('<em>'.$this->objLanguage->languageText('word_folder', 'system', 'Folder').'</em>');
                    $objTable->endRow();
                }
            }
            
            if (is_array($symlinks)) {
                $files = array_merge($files, $symlinks);
            }
            
            if (count($files) > 0) {
                //var_dump($files);
                $fileSize = new formatfilesize();
                foreach ($files as $file)
                {
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
                    
                    //$objTable->startRow();
                    if ($this->editPermission) {
                        $checkbox = new checkbox('files[]');
                        
                        if (isset($file['symlinkid'])) {
                            $checkbox->value = 'symlink__'.$file['symlinkid'];
                        } else {
                            $checkbox->value = $file['id'];
                        }
                        
                        $checkbox->cssId = htmlentities('input_files_'.$file['filename']);
                        
                        $objTable->addCell($checkbox->show(), 20);
                    }
                    
                    $label = new label ($this->objFileIcons->getFileIcon($file['filename']), htmlentities('input_files_'.$file['filename']));
                    $objTable->addCell($label->show());
                    
                    if (isset($file['symlinkid'])) {
                        $fileLink = new link ($this->uri(array('action'=>'symlink', 'id'=>$file['symlinkid'])));
                    } else {
                        $fileLink = new link ($this->uri(array('action'=>'fileinfo', 'id'=>$file['id'])));
                    }
                    $fileLink->link = basename($file['filename']);
                    $objTable->addCell($fileLink->show());
                    $objTable->addCell($fileSize->formatsize($file['filesize']));
                    $objTable->endRow();
                }
            }
        }
        
        if ($hidden > 0 && count($restriction) > 0) {
            $str = '
<script type="text/javascript">

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

</script>
            '.'<style type="text/css">tr.hidefile {display:none;}</style>';
            
            $str .= $this->objLanguage->languageText('mod_filemanager_browsingfor', 'filemanager', 'Browsing for').': ';
            $comma = '';
            
            foreach ($restriction as $restrict)
            {
                $str .= $comma.$restrict;
                $comma = ', ';
            }
            
            $str .= ' &nbsp; - ';
            
            $this->loadClass('checkbox', 'htmlelements');
            $this->loadClass('label', 'htmlelements');
            $checkbox = new checkbox ('showall');
            $checkbox->extra = ' onclick="turnOnFiles();"';
            
            $label = new label($this->objLanguage->languageText('mod_filemanager_showallfiles', 'filemanager', 'Show All Files'), $checkbox->cssId);
            
            $str .= $checkbox->show().$label->show();
        } else {
            $str = '';
        }
        
        return $str.$objTable->show();
    }
    
    

    
    
    

}

?>
