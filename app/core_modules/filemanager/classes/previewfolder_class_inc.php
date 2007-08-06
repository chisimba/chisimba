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
 * @version   CVS: $Id$
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
class previewfolder extends object
{

    /**
    * Constructor
    */
    public function init()
    {
        $this->objFileIcons = $this->getObject('fileicons', 'files');
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
    function previewContent($subFolders, $files)
    {
        return $this->previewLongView($subFolders, $files);
    
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
    function previewLongView($subFolders, $files)
    {
        $objTable = $this->newObject('htmltable', 'htmlelements');
        
        $objTable->startHeaderRow();
        $objTable->addHeaderCell('&nbsp;', '20');
        $objTable->addHeaderCell('&nbsp;', '20');
        $objTable->addHeaderCell('Name');
        $objTable->addHeaderCell('Size', 60);
        $objTable->addHeaderCell('&nbsp;', '30');
        
        $objTable->endHeaderRow();
        
        if (count($subFolders) == 0 && count($files) == 0) {
            $objTable->startRow();
            $objTable->addCell('<em>No files or folders found</em>', NULL, NULL, NULL, 'noRecordsMessage', 'colspan="5"');
            $objTable->endRow();
        } else {
        
            if (count($subFolders) > 0) {
                $folderIcon = $this->objFileIcons->getExtensionIcon('folder');
                
                foreach ($subFolders as $folder)
                {
                    $objTable->startRow();
                    $checkbox = new checkbox('files[]');
                    $checkbox->value = 'folder__'.$folder['id'];
                    $checkbox->cssId = htmlentities('input_files_'.basename($folder['folderpath']));
                    
                    $objTable->addCell($checkbox->show(), 20);
                    
                    $objTable->addCell($folderIcon);
                    
                    $folderLink = new link ($this->uri(array('action'=>'viewfolder', 'folder'=>$folder['id'])));
                    $folderLink->link = basename($folder['folderpath']);
                    $objTable->addCell($folderLink->show());
                    $objTable->addCell('<em>Folder</em>');
                    $objTable->endRow();
                }
            }
            
            if (count($files) > 0) {
                
                $fileSize = new formatfilesize();
                foreach ($files as $file)
                {
                    $objTable->startRow();
                    $checkbox = new checkbox('files[]');
                    $checkbox->value = $file['id'];
                    $checkbox->cssId = htmlentities('input_files_'.$file['filename']);
                    
                    $objTable->addCell($checkbox->show(), 20);
                    
                    $label = new label ($this->objFileIcons->getFileIcon($file['filename']), htmlentities('input_files_'.$file['filename']));
                    $objTable->addCell($label->show());
                    
                    $fileLink = new link ($this->uri(array('action'=>'fileinfo', 'id'=>$file['id'])));
                    $fileLink->link = basename($file['filename']);
                    $objTable->addCell($fileLink->show());
                    $objTable->addCell($fileSize->formatsize($file['filesize']));
                    $objTable->endRow();
                }
            }
        }
        return $objTable->show();
    }
    
    

    
    
    

}

?>