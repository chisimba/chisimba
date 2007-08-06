<?php

/**
 * Class to process Success and Error Message for Uploads
 *
 * It has two parts: The first part analyzes results from the upload class and creates an array that can be passed via URL
 * The second part takes the URL any converts them into confirmation messages
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
 * Class to process Success and Error Message for Uploads
 *
 * It has two parts: The first part analyzes results from the upload class and creates an array that can be passed via URL
 * The second part takes the URL any converts them into confirmation messages
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
class uploadmessages extends object
{
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->objFiles = $this->getObject('dbfile');
        $this->objCleanUrl = $this->getObject('cleanurl');
        $this->objMediaFileInfo = $this->getObject('dbmediafileinfo');
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
        
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('formatfilesize', 'files');
    }
    
    /**
    * Method to Analyze Upload Results for URL
    *
    * This function analyzes file uploads, and puts the results in an array so that it can be passed via URL
    * After uploads, the controller will nextAction with this array to flag the next page on the upload results
    *
    * @param  array $results Results as given from the upload class
    * @return array Analyzed Results
    */
    public function processMessageUrl($results)
    {
        // Remove File Inputs
        if (array_key_exists('nofileprovided', $results)) {
            unset ($results['nofileprovided']); 
        }
        
        // Prepare Success / Error Messages
        $success = array();
        $errors = array();
        $overwrite = array();
        
        // Check each file for result
        foreach ($results as $file)
        {
            if (array_key_exists('overwrite', $file) && $file['overwrite']) {
                $overwrite[] = $file['fileid'];
            } else if ($file['success'] == TRUE) {
                $success[] = $file['fileid'];
            } else {
                $errors[$file['name']] = $file['reason'];
            }
        }
        
        // Convert Success List into a single string
        $successList = '';
        $divider = '';
        
        if (count($success) > 0) {
            foreach ($success as $file)
            {
                $successList .= $divider.$file;
                $divider .= '__';
            }
        }
        
        // Convert Overwrite List into a single string
        $overwriteList = '';
        $divider = '';
        
        if (count($overwrite) > 0) {
            foreach ($overwrite as $file)
            {
                $overwriteList .= $divider.$file;
                $divider .= '__';
            }
        }
        
        // Convert Error List into a single string
        $errorList = '';
        $divider = '';
        
        if (count($errors) > 0) {
            foreach ($errors as $file=>$reason)
            {
                $errorList .= $divider.$file.'__'.$reason;
                $divider .= '___';
            }
        }
        
        // Put Message into Array
        $messages = array('successuploaded'=>$successList, 'erroruploads'=>$errorList, 'overwrite'=>$overwriteList);
        
        // Return Array
        return $messages;
    }
    
    /**
    * Method to process Successful Upload Messages
    * @return string Confirmation message of successful uploads
    */
    public function processSuccessMessages()
    {
        if ($this->getParam('successuploaded') == '') {
            $successMessage = '';
        } else {
            $files = explode('__', $this->getParam('successuploaded'));
            
            $successMessage = '';
            
            foreach ($files as $fileId)
            {
                $file = $this->objFiles->getFileInfo($fileId);
                
                if ($file != FALSE) {
                    $link = new link($this->uri(array('action'=>'fileinfo', 'id'=>$fileId)));
                    $link->link = $this->objCleanUrl->processFileName($file['filename']);
                    
                    if ($file['category'] == 'temp') {
                        $successMessage .= stripslashes(html_entity_decode('<li><strong>'.$file['filename'].'</strong> '.$this->objLanguage->languageText('mod_filemanager_successfullyuploadedbutexists', 'filemanager', 'has been successfully uploaded, <span class="highlight">but a file with the same filename already exists.</span>').'</li>'));
                    } else {
                        $successMessage .= '<li><strong>'.$link->show().'</strong> '.$this->objLanguage->languageText('mod_filemanager_successfullyuploaded', 'filemanager', 'has been successfully uploaded.').'</li>';
                    }
                }
            }
            
            if ($successMessage != '') {
                $successMessage = '<p><span class="success">'.$this->objLanguage->languageText('phrase_uploadresults', 'filemanager', 'Upload Results').':</span></p><ul>'.$successMessage.'</ul>';
            }
        }
        
        return $successMessage;
    }
    
    /**
    * Method to process Error Upload Messages
    * @return string Confirmation message of error uploads
    */
    public function processErrorMessages()
    {
        if ($this->getParam('erroruploads') == '') {
            $errorMessage = '';
        } else {
            $errors = explode('___', $this->getParam('erroruploads'));
            
            $errorMessage = '';
            
            foreach ($errors as $error)
            {
                $file = explode('__', $error);
                
                switch ($file[1])
                {
                    case 'bannedfile': $reason = $this->objLanguage->languageText('mod_filemanager_filetypeisbanned', 'filemanager', 'File Type is banned from uploads'); break;
                    case 'partialuploaded': $reason = $this->objLanguage->languageText('mod_filemanager_partiallyuploadedfile', 'filemanager', 'File was only partially uploaded'); break;
                    default: $reason = ''; break;
                }
                
                $errorMessage .= '<li><strong>'.$this->objCleanUrl->processFileName($file[0]).'</strong> could not be uploaded. '.$reason.'</li>';
            }
            
            if ($errorMessage != '') {
                $errorMessage = '<p><span class="error">'.$this->objLanguage->languageText('word_errors', 'filemanager', 'Errors').':</span></p><ul>'.$errorMessage.'</ul>';
            }
        }
        
        return $errorMessage;
    }
    
        /**
    * Method to process Error Upload Messages
    * @return string Confirmation message of error uploads
    */
    public function processOverwriteMessages()
    {
        if ($this->getParam('overwrite') == '') {
            $overwriteMessage = '';
        } else {
            $items = explode('__', $this->getParam('overwrite'));
            
            $overwriteMessage = '';
            
            $table = $this->newObject('htmltable', 'htmlelements');
            
            $table->startHeaderRow();
            $table->addHeaderCell('Filename');
            $table->addHeaderCell('File Size of Existing File', NULL, NULL, 'center');
            $table->addHeaderCell('File Size of New File', NULL, NULL, 'center');
            $table->addHeaderCell('Overwrite File?', NULL, NULL, 'center');
            $table->endHeaderRow();
            
            $actualItems = array();
            
            $formatsize = new formatfilesize();
            
            foreach ($items as $item)
            {
                // Get File Details
                $file = $this->objFiles->getFile($item);
                
                // Generate Path to File
                $tempFilePath = $this->objConfig->getcontentBasePath().'/filemanager_tempfiles/'.$item;
                
                // Create Boolean Variable - does file exist
                if (file_exists($tempFilePath)) {
                    $tempFileExists = TRUE;
                } else {
                    $tempFileExists = FALSE;
                }
                
                // If no record
                if ($file == FALSE) {
                    // Delete temp file if it exists
                    if ($tempFileExists && is_file($tempFilePath)) {
                        unlink($tempFilePath);
                    }
                } else if ($file && $tempFileExists) { // If Record and Temp File exists
                    // Add to Form for User to choose option
                    $actualItems[] = $item;
                    
                    $table->startRow();
                    $table->addCell('<strong>'.$file['filename'].'</strong>');
                    
                    $table->addCell($formatsize->formatsize($file['filesize']), NULL, NULL, 'center');
                    $table->addCell($formatsize->formatsize(filesize($tempFilePath)), NULL, NULL, 'center');
                    
                    $radio = new radio ($item);
                    $radio->addOption('delete', ' No');
                    $radio->addOption('overwrite', ' Yes');
                    $radio->setSelected('delete');
                    $radio->setBreakSpace(' &nbsp; ');
                    
                    $table->addCell($radio->show(), NULL, NULL, 'center');
                    $table->endRow();
                }
            }
            
            if (count($actualItems) > 0) {
                $form = new form('overwriteoptions', $this->uri(array('action'=>'fixtempfiles')));
                
                $form->addToForm($table->show());
                
                $list = '';
                $separator = '';
                
                foreach ($actualItems as $itemId)
                {
                    $list .= $separator.$itemId;
                    $separator = '__';
                }
                
                $hiddenInput = new hiddeninput('listitems', $list);
                $form->addToForm($hiddenInput->show());
                
                $button = new button ('submitform', 'Confirm Overwrite');
                $button->setToSubmit();
                $form->addToForm($button->show());
                
                $overwriteMessage = $form->show();
            }
        }
        
        return $overwriteMessage;
    }

}

?>