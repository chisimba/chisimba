<?php

/**
 * Class to Handle Single Process Uploads outside of File Manager
 *
 * This class generates a file input that can be added to any form
 * It also provides a handler for the results, and simplies the calls
 * to the upload class
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
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: upload_class_inc.php 10139 2008-08-12 11:52:14Z tohir $
 * @link      http://avoir.uwc.ac.za
 * @see
 */


/**
 * Class to Handle Single Process Uploads outside of File Manager
 *
 * This class generates a file input that can be added to any form
 * It also provides a handler for the results, and simplies the calls
 * to the upload class
 *
 * @category  Chisimba
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */

$this->loadClass('filemanagerobject', 'filemanager');
class uploadinput extends filemanagerobject
{
    /**
    * @var string $name Name of the File Selector Input
    */
    public $name;
    
    /**
    * @var array $restrictFileList Extensions to restrict the upload to
    */
    public $restrictFileList;
    
    /**
     * @var boolean $enableOverwriteIncrement
     * If a file called myinfo.txt is uploaded, but one exists, new file will be called 'myinfo_1.txt'
     */
    public $enableOverwriteIncrement = FALSE;
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->name = 'fileupload';
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        
        $this->objUser = $this->getObject('user', 'security');
    }
    
    /**
     * Method to show the file input
     *
     * Remember to add $form->extra = 'enctype="multipart/form-data"'; to the form
     * @return string
     */
    public function show()
    {
        $input = new textinput($this->name);
        $input->fldType = 'file';
        $input->cssClass = '';
        $input->size = '50';
        
        $objLanguage = $this->getObject('language', 'language');
        
        $objFolder = $this->getObject('dbfolder');
        $tree = $objFolder->getTree('users', $this->objUser->userId(), 'htmldropdown');
        
        $objQuotas = $this->getObject('dbquotas');
        $maxFileSize = new hiddeninput('MAX_FILE_SIZE', $objQuotas->getRemainingSpaceUser($this->objUser->userId()));
        
        $restrict = '';
        $restrictStr = '';
        
        if (count($this->restrictFileList) > 0) {
            $divider = '';
            $comma = '';
            foreach ($this->restrictFileList as $restriction)
            {
                $restrict .= $divider.$restriction;
                $restrictStr .= $comma.$restriction;
                $divider = '___';
                $comma = ', ';
            }
            
            $restrictInput = new hiddeninput('restrictions__'.$this->name, $restrict);
            
            $restrict = $restrictInput->show();
            $restrictStr = ' ('.$restrictStr.')';
        }
        
        return $maxFileSize->show().$input->show().$restrictStr.'<br /> '.$objLanguage->languageText('mod_filemanager_saveuploadfilein', 'filemanager', 'Save Uploaded File in').': '.$tree.$restrict;
    }
    
    /**
     * Method to handle the upload
     *
     * Call this method on the second page once file hand is upload
     * $name should match $this->name in previous function
     *
     * @param string $name Name of the file input
     * @return array Details of the Upload
     */
    public function handleUpload($name)
    {
        $objFolder = $this->getObject('dbfolder');
        $uploadPath = $objFolder->getFolderPath($this->getParam('parentfolder'));
        
        $objUpload = $this->getObject('upload');
        $objUpload->setUploadFolder($uploadPath);
        $objUpload->enableOverwriteIncrement = $this->enableOverwriteIncrement;
        
        $restrictions = $this->getParam('restrictions__'.$name);
        
        if ($restrictions == '') {
            $restrictions = NULL;
        } else {
            $restrictions = explode('___', $restrictions);
        }
        
        $fileUploadResultsArray = array();
        
        return $objUpload->uploadFile($name, $restrictions, $fileUploadResultsArray);
    }
}



?>