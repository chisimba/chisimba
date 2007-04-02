<?php

/**
* Class to check whether temporary file uploads exists, and provide an interface to overwriting them
*
* Background: When a user uploads a file with a filename that already exists, that file is stored in a 
* temporary folder, and is not available for usage. The user then has an option to either delete the
* temp file, use it to overwrite the current one or rename it.
*
* @author Tohir Solomons
*/
class checkoverwrite extends object
{
    /**
    * Constructor
    */
    public function init()
    {
        $this->objUser =& $this->getObject('user', 'security');
        $this->objFile =& $this->getObject('dbfile');
        $this->objUserFolder =& $this->getObject('userfoldercheck');
        $this->objConfig =& $this->getObject('altconfig', 'config');
        $this->objThumbnails = $this->getObject('thumbnails');
        $this->objFileEmbed =& $this->getObject('fileembed');
        $this->objFileParts =& $this->getObject('fileparts', 'files');
        $this->objFormatDateTime =& $this->getObject('dateandtime', 'utilities');
        
        
        // HTML Elements
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        
        $this->loadClass('formatfilesize', 'files');
        
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
    }
    
    /**
    * Method to get the amount of temporary files that needs to be overwritten
    * @param string $userId User Id of the User
    * @return int Number of temporary files
    */
    public function checkUserOverwrite($userId=NULL)
    {
        if ($userId == '') {
            $userId = $this->objUser->userId();
        }
        
        $numTempFiles = $this->objFile->getTemporaryFiles($userId);
        
        return count($numTempFiles);
    }
    
    /**
    * Method to show a link to the temporary files
    * If no temporary files exist, nothing is returned
    */
    public function showLink()
    {
        if ($this->checkUserOverwrite() > 0) {
            
            $this->objIcon->setIcon('warning');
            $this->objIcon->align = 'left';
            
            $link = new link ($this->uri(array('action'=>'checkoverwrite'), 'filemanager'));
            $link->link = $this->objIcon->show().'Some Files need to be overwritten';
            return $link->show();
        } else {
            return NULL;
        }
    }
    
    /**
    * Method to show the interface for overwriting files
    */
    public function showUserOverwiteInterface()
    {
        $userId = $this->objUser->userId();
        
        $tempFiles = $this->objFile->getTemporaryFiles($userId);
        
        $form = new form ('fixtempfiles', $this->uri(array('action'=>'fixtempfiles')));
        
        $divider = '';
        $splitter = '';
        $listItems = '';
        
        $submitButton = new button ('submitform', 'Update Files');
        $submitButton->setToSubmit();
        
        $objHighlightLabels = $this->getObject('highlightlabels', 'htmlelements');
        $objHighlightLabels->show();
        
        $formatsize = new formatfilesize();
        
        foreach ($tempFiles as $file)
        {
            //$form->addToForm($divider);
            //$divider = '<hr size="1" width="70%" />';
            
            $listItems .= $splitter.$file['id'];
            $splitter = '|';
            
            // Rename Id of the button to keep them unique
            $submitButton->cssId = 'input_button_'.$file['id'];
            
            $originalFile = $this->objFile->getOriginalFile($file['filename'], $userId);
            
            $form->addToForm('<h2>'.$file['filename'].'</h2>');
            $table = $this->newObject('htmltable', 'htmlelements');
            
            $table->startHeaderRow();
            $table->addHeaderCell('Info', '20%');
            $table->addHeaderCell('Old File', '40%');
            $table->addHeaderCell('New File', '40%');
            $table->endHeaderRow();
            
            
            
            if ($originalFile['category'] == 'images') {
                $oldThumbnail = $this->objFileEmbed->embed($this->objThumbnails->getThumbnail($originalFile['id'], $originalFile['filename']), 'image');
                $newThumbnail = $this->objFileEmbed->embed($this->objThumbnails->getThumbnail($file['id'], $file['filename']), 'image');
                
                // File Preview
                $table->startRow();
                $table->addCell('File Preview');
                $table->addCell($oldThumbnail);
                $table->addCell($newThumbnail);
                $table->endRow();
                
            } else {
                $oldThumbnail = 'gdsgsd';
                $newThumbnail = 'gdsgsd';
            }
            
            
            
            // File Size
            $table->startRow();
            $table->addCell('Size of File');
            $table->addCell($formatsize->formatsize($originalFile['filesize']));
            $table->addCell($formatsize->formatsize($file['filesize']));
            $table->endRow();
            
            // Date Uploaded
            $table->startRow();
            $table->addCell('Date Uploaded');
            $table->addCell($this->objFormatDateTime->formatDateOnly($originalFile['datecreated']).' at '.$this->objFormatDateTime->formatTime($originalFile['timecreated']));
            $table->addCell($this->objFormatDateTime->formatDateOnly($file['datecreated']).' at '.$this->objFormatDateTime->formatTime($file['timecreated']));
            $table->endRow();
            
            $form->addToForm($table->show());
            
            $form->addToForm('<h4>Options:</h4>');
            
            $radio = new radio($file['id']);
            $radio->setBreakSpace('<br />');
            $radio->addOption('ignore', 'Ignore this file for the time being');
            $radio->addOption('overwrite', 'Overwrite old file with new one');
            $radio->addOption('deletetemp', 'Delete the Temporary File');
            
            $extension = $this->objFileParts->getExtension($file['filename']);
            
            if (strlen($extension) == strlen($file['filename'])) {
                $filename = $file['filename'];
                $extension = '';
            } else {
                $filename = substr($file['filename'], 0, strlen($file['filename'])-strlen($extension)-1);
                $extension = '.'.$extension;
            }
            
            $textinput = new textinput ('rename__'.$file['id'], $filename);
            $textinput->size = 50;
            
            $extensioninput = new textinput ('extension__'.$file['id'], $extension);
            $extensioninput->size = 5;
            $extensioninput->extra = ' readonly="true"';
            
            // Fix Up - Highlight Radio Button
            //$textinput->extra = ' onclick="document.getElementById(\'input_gen19Srv1Nme34_247ignore\').focus();"; ';
            
            //$radio->addOption('rename', 'Rename the temporary file to: ');
            $radio->setSelected('ignore');
            
            //$form->addToForm($radio->show().$textinput->show().$extensioninput->show());
            $form->addToForm($radio->show());
            
            $form->addToForm('<p align="center">'.$submitButton->show().'</p>');
        }
        
        $hiddenInput = new hiddeninput ('listitems', $listItems);
        $form->addToForm($hiddenInput->show());
        
        return $form->show();
    }
    
    
    /**
    * Method to overwrite a file
    * It archives the old file, and moves the new one into the place of the old
    * @param string $fileId Record Id of the File
    */
    public function overWriteFile($fileId)
    {
        $file = $this->objFile->getRow('id', $fileId);
        
        if ($file == FALSE) {
            return FALSE;
        }
        
        $originalFile = $this->objFile->getOriginalFile($file['filename'], $file['userid']);
        
        if ($originalFile == FALSE) {
            return FALSE;
        }
        
        // Create Subfolder
        $this->objUserFolder->checkUserFolderVersionExists($originalFile['userid'], $originalFile['category'], $originalFile['version']);
        
        $originalSource = $this->objConfig->getcontentBasePath().$originalFile['path'];
        $originalDestination = $this->objConfig->getcontentBasePath().'users/'.$originalFile['userid'].'/'.$originalFile['category'].'/'.$originalFile['version'].'/'.$originalFile['filename'];
        
        $tempSource = $this->objConfig->getcontentBasePath().$file['path'];
        
        //echo $originalDestination;
        
        if (file_exists($originalDestination)) {
            unlink($originalDestination);
        }
        
        
        rename($originalSource, $originalDestination);
        
        rename($tempSource, $originalSource);
        
        // Update Original File
        $fileId = $originalFile['id'];
        $version = $originalFile['version'];
        $path = 'users/'.$originalFile['userid'].'/'.$originalFile['category'].'/'.$originalFile['version'].'/'.$originalFile['filename'];
        $category = $originalFile['category'];
        
        $this->objFile->updateOverwriteDetails($fileId, $version, $path, $category);
        
        // Update New File
        $fileId = $file['id'];
        $version = $originalFile['version']+1;
        $path = 'users/'.$originalFile['userid'].'/'.$originalFile['category'].'/'.$originalFile['filename'];
        $category = $originalFile['category'];
        
        return $this->objFile->updateOverwriteDetails($fileId, $version, $path, $category);
    }
}

?>