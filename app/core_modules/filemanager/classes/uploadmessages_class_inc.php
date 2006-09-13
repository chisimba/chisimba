<?

/**
* Class to process Success and Error Message for Uploads
*
* It has two parts: The first part analyzes results from the upload class and creates an array that can be passed via URL
* The second part takes the URL any converts them into confirmation messages
*
* @author Tohir Solomons
*/
class uploadmessages extends object
{
    
    /**
    * Constructor
    */
    public function init()
    {
        $this->objFiles =& $this->getObject('dbfile');
        $this->objCleanUrl =& $this->getObject('cleanurl');
        $this->objMediaFileInfo =& $this->getObject('dbmediafileinfo');
        $this->objUser =& $this->getObject('user', 'security');
        $this->objLanguage =& $this->getObject('language', 'language');
    }
    
    /**
    * Method to Analyze Upload Results for URL
    *
    * This function analyzes file uploads, and puts the results in an array so that it can be passed via URL
    * After uploads, the controller will nextAction with this array to flag the next page on the upload results
    *
    * @param array $results Results as given from the upload class
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
        $categories = array();
        
        // Do files need to be overwritten
        $overwrite = FALSE;
        
        // Check each file for result
        foreach ($results as $file)
        {
            if (array_key_exists('overwrite', $file) && $file['overwrite']) {
                $overwrite = TRUE;
            } else {
                $overwrite = FALSE;
            }
            
            if ($file['success'] == TRUE) {
                $success[] = $file['fileid'];
                $categories[] = $file['originalfolder'];
            } else {
                $errors[$file['name']] = $file['reason'];
            }
        }
        
        // Convert Success List into a single string
        $successList = '';
        $divider = '';
        
        foreach ($success as $file)
        {
            $successList .= $divider.$file;
            $divider .= '__';
        }
        
        // Convert Error List into a single string
        $errorList = '';
        $divider = '';
        
        foreach ($errors as $file=>$reason)
        {
            $errorList .= $divider.$file.'__'.$reason;
            $divider .= '___';
        }
        
        // Determine amount of varying categories
        // If number of categories is 1, redirect to that category
        $categories = array_unique($categories);
        if (count($categories) == 1) {
            $category = $categories[0];
        } else {
            $category = '';
        }
        
        // Put Message into Array
        $messages = array('successuploaded'=>$successList, 'erroruploads'=>$errorList, 'category'=>$category, 'overwriteboolean'=>$overwrite);
        
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
    * Method to process Errpr Upload Messages
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

}

?>