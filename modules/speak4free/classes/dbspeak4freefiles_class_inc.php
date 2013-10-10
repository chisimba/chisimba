<?php
// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end of security

/**
* Web Present Fikes Class
*
* This class interacts with the database to store the details of the list
* of
*/
class dbspeak4freefiles extends dbtable
{

    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_speak4free_files');
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
    }

    /**
    * Method to get the details of a file
    * @param string $id Record ID of the file
    * @return array Details of the file
    */
    public function getFile($id)
    {
        return $this->getRow('id', $id);
    }

    /**
    * Method to create a record id for the process of uploading
    * This is needed to create a folder for the presentation
    * All different formats of the same presentation are then stored in this folder
    *
    * @param string Record Id
    */
    public function autoCreateTitle()
    {
        return $this->insert(array(
                'processstage'=>'uploading'
            ));
    }

    /**
    * Method to delete a record id autocreated for the process of uploading
    * This is done when an error occurs
    *
    * @param string Record Id
    */
    public function removeAutoCreatedTitle($id)
    {
        $row = $this->getRow('id', $id);

        if ($row['processstage'] == 'uploading') {
            $this->delete('id', $id);
        }
    }

    public function updateReadyForConversion($id, $filename, $mimetype)
    {
        return $this->update('id', $id, array(
                'filename' => stripslashes($filename),
                'title' => stripslashes($filename),
                'mimetype' => $mimetype,
                'filetype' => $this->getFileType($filename),
                'processstage' => 'readyforconversion',
                'creatorid' => $this->objUser->userId(),
                'dateuploaded' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
            ));
    }

    public function updateFileDetails($id, $title, $description, $license)
    {
        return $this->update('id', $id, array(
                'title' => stripslashes($title),
                'description' => stripslashes($description),
                'cclicense' => $license
            ));
    }

    public function getLatestUploads()
    {
        return $this->getAll(' ORDER BY dateuploaded DESC LIMIT 4');
    }

        public function getMyUploads()
    {
        return $this->getAll(" where creatorid ='".$this->objUser->userid()."' ORDER BY dateuploaded DESC");
    }
    public function getLatestPresentation()
    {
        return $this->getAll(' ORDER BY dateuploaded DESC LIMIT 1');
    }

    public function getByUser($user, $order='dateuploaded DESC')
    {
        return $this->getAll(' WHERE creatorid=\''.$user.'\' ORDER BY '.$order);
    }

    private function getFileType($filename)
    {
        $pathInfo = pathinfo($filename);

        switch ($pathInfo['extension'])
        {
            case 'ppt':
            case 'pps':
                $type = 'powerpoint';
                break;
            case 'odp':
                $type = 'openoffice';
                break;
            default:
                $type = 'unknown';
                break;
        }

        return $type;
    }


    private function getFilesForConversion()
    {
        return $this->getAll(' WHERE processstage != \'finished\' AND inprocess=\'N\'');
    }

    private function isInProcess($id)
    {
        $row = $this->getRow('id', $id);

        return ($row['inprocess'] == 'Y' ? TRUE : FALSE);
    }

    public function convertFiles()
    {
        //echo '<pre>';
        $files = $this->getFilesForConversion();

        if (count($files) == 0) {
            return 'allfilesconverted';
        } else {
            foreach ($files as $file)
            {
                if (!$this->isInProcess($file['id'])) {
                    $result = $this->convertFile($file);
                } else {
                    $result = 'inprocess';
                }

                echo $file['id'].' - '.$result.'<br />';
            }

            return $result;
        }
    }

    private function setOutOfProcess($id)
    {
        return $this->update('id', $id, array('inprocess'=>'N'));
    }

    private function setInProcess($id, $step)
    {
        return $this->update('id', $id, array('inprocess'=>'Y', 'processstage'=>$step));
    }


    private function convertFile($file)
    {
        //print_r($file);

        $path_parts = pathinfo($file['filename']);

        $ext = $path_parts['extension'];

        //echo $this->objConfig->getcontentBasePath().'speak4freent/'.$file['id'].'/'.$file['id'].'.'.$ext;

        if ($file['filetype'] != 'powerpoint' || $file['filetype'] != 'openoffice')
        {
            $ext = $this->fixUnknownFileType($file['id'], $file['filename']);
            //echo $ext.'<br />';
        }



        $step = 'otherconversion';
        $this->setInProcess($file['id'], $step);
        $result = $this->convertAlternative($file['id'], $ext);
        $this->setOutOfProcess($file['id']);

        

        if ($result) {
            $step = 'pdfconversion';
            $this->setInProcess($file['id'], $step);
            $result = $this->convertFileFromFormat($file['id'], 'odp', 'pdf');
            
            log_debug('donewithpdf');
            
            if ($result) {
                log_debug('startwithswf2');
                $swf2 = $this->checkspeak4freentVersion2($file['id']);
                log_debug('endwithswf2');
            }
            $this->setOutOfProcess($file['id']);
        }
        
        if ($result) {
            $step = 'flashconversion';
            $this->setInProcess($file['id'], $step);
            $result = $this->convertFileFromFormat($file['id'], 'odp', 'swf');
            $this->setOutOfProcess($file['id']);
        }

        if ($result) {
            $step = 'htmlconversion';
            $this->setInProcess($file['id'], $step);
            $result = $this->convertFileFromFormat($file['id'], 'odp', 'html');
            $this->setOutOfProcess($file['id']);

            if ($result == TRUE) {
                $this->update('id', $file['id'], array('processstage'=>'finished'));
                $step = 'finishedconversion';
            }
        }
        
        
        
        if ($result == TRUE)
        {
            $objSlides = $this->getObject('dbspeak4freentslides');
            $objSlides->scanPresentationDir($file['id']);
        }


        return $step;
    }

    private function fixUnknownFileType($id, $existingFilename)
    {
        $objScan = $this->getObject('scanfordelete');

        // Set Directory to Var
        $presentationFolder = $this->objConfig->getcontentBasePath().'speak4freent/'.$id;

        // Scan Results
        $results = $objScan->scanDirectory($presentationFolder);

        if (count($results['files']) > 0)
        {
            foreach ($results['files'] as $file)
            {
                //echo $file.'<br />';
                $file = basename($file);

                if (preg_match('/'.$id.'\.(odp|ppt|pptx)/', $file)) {
                    $path_parts = pathinfo($file);

                    $ext = $path_parts['extension'];

                    if ($existingFilename == '')
                    {
                        $existingFilename = $file;
                    }


                    if ($ext == 'odp')
                    {
                        $this->update('id', $id, array(
                            'filename' => $existingFilename,
                            'filetype' => 'openoffice',
                        ));
                    } else {
                        $this->update('id', $id, array(
                            'filename' => $existingFilename,
                            'filetype' => 'powerpoint',
                        ));
                    }

                    //echo 'in here'.$ext;
                    // Return Correct Extension
                    return $ext;
                }
            }
        }
        // Unknown Item
        return 'bug';
    }

    private function convertAlternative($id, $ext)
    {
        $other = ($ext == 'odp' ? 'ppt' : 'odp');

        return $this->convertFileFromFormat($id, $ext, $other);
    }

    private function convertFileFromFormat($id, $inputExt, $outputExt)
    {
        $source = $this->objConfig->getcontentBasePath().'speak4freent/'.$id.'/'.$id.'.'.$inputExt;
        $conv = $this->objConfig->getcontentBasePath().'speak4freent/'.$id.'/'.$id.'.'.$outputExt;


       // echo $source.'<br />'.$conv;

        if (!file_exists($conv)) {
            $objConvertDoc = $this->getObject('convertdoc', 'documentconverter');

            $objConvertDoc->convert($source, $conv);

            if (file_exists($conv)) {
                system ('chmod 777 '.$conv);
                @chmod ($conv, 0777);
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return TRUE;
        }

    }

    public function getPresentationThumbnail($id, $title='')
    {
        $objViewer = $this->getObject('viewer');
        return $objViewer->getPresentationThumbnail($id, $title);
    }

    public function deleteFile($id)
    {
        // First Step is to scan presentation directory and delete all file in that folder
        // Load Object
        $objScanForDelete = $this->newObject('scanfordelete');

        // Set Directory to Var
        $presentationFolder = $this->objConfig->getcontentBasePath().'speak4freent/'.$id;

        // Scan Results
        $results = $objScanForDelete->scanDirectory($presentationFolder);


        // Variable to detect permission issues
        $numNoDeletes = 0;

        // First Delete Files, if they are any
        if (count($results['files']) > 0)
        {
            // Loop through files
            foreach ($results['files'] as $file)
            {
                // Extra Check that file exists
                if (file_exists($file))
                {
                    // Delete File
                    if(!unlink($file))
                    {
                        // If unable to, possible due to permissions
                        // Flag Variable
                        $numNoDeletes++;
                    }
                }
            }
        }

        // IF all files deleted, and there are subdirectories
        // Delete subdirectories
        if ($numNoDeletes != 0 && count($results['folders'] > 0))
        {
            // Loop through array
            foreach ($results['folders'] as $folder)
            {
                // Extra check that folder exists
                if (file_exists($folder))
                {
                    // Delete Folder
                    if(!rmdir($folder))
                    {
                        // If unable to, possible due to permissions
                        // Flag Variable
                        $numNoDeletes++;
                    }
                }
            }
        }

        // Now Delete Folder itself
        if ($numNoDeletes == 0 and file_exists($presentationFolder))
        {
            //recursive delete function
            $this->deldir($presentationFolder);
        }

        $thumb = $this->objConfig->getcontentBasePath().'speak4freent_thumbnails/'.$id.'.jpg';
        if (file_exists($thumb))
        {
            unlink ($thumb);
        }



        $objTags = $this->getObject('dbspeak4freenttags');
        $objTags->deleteTags($id);

        $objSlides = $this->getObject('dbspeak4freentslides');
        $objSlides->deleteSlides($id);

        $this->delete('id', $id);

    }


    public function regenerateFile($id, $type)
    {

        switch ($type)
        {
            case 'flash':
                return $this->regenerateSWF($id); break;
            case 'slides':
                return $this->regenerateSlides($id); break;
            case 'pdf':
                return $this->regeneratePDF($id); break;
            default: return FALSE;
        }

    }

    public function getLatestByBuddies($userId)
    {
        $objBuddies = $this->getObject('dbbuddies', 'buddies');
        $buddies = $objBuddies->getBuddies($userId);

        if (count($buddies) == 0) {
            return array();
        } else {
            $where = 'WHERE (';
            $or = '';

            foreach ($buddies as $buddy)
            {
                $where .= $or.' creatorid=\''.$buddy['buddyid'].'\'';
                $or = ' OR ';
            }

            $where .= ')';
        }

        $where .= ' ORDER BY dateuploaded DESC LIMIT 10';

        return $this->getAll($where);
    }

    private function regenerateSWF($id)
    {
        $swfFile =  $this->objConfig->getcontentBasePath().'speak4freent/'.$id.'/'.$id.'.swf';
        if (file_exists($swfFile))
        {
            unlink($swfFile);
        }
        $file =  $this->objConfig->getcontentBasePath().'speak4freent/'.$id.'/'.$id.'.odp';
        if (file_exists($file))
        return $this->convertFileFromFormat($id, 'odp', 'swf');
        $file =  $this->objConfig->getcontentBasePath().'speak4freent/'.$id.'/'.$id.'.ppt';
        if (file_exists($file))
        return $this->convertFileFromFormat($id, 'ppt', 'swf');

        $file =  $this->objConfig->getcontentBasePath().'speak4freent/'.$id.'/'.$id.'.pptx';
        if (file_exists($file))
        return $this->convertFileFromFormat($id, 'pptx', 'swf');

    }

    private function regenerateSlides($id)
    {
        $objSlides = $this->getObject('dbspeak4freentslides');
        $objSlides->deleteSlides($id);

        $result = $this->convertFileFromFormat($id, 'odp', 'html');

        if ($result == TRUE)
        {
            $objSlides = $this->getObject('dbspeak4freentslides');
            $objSlides->scanPresentationDir($id);
        }

        return $result;
    }

    private function regeneratePDF($id)
    {
        $pdfFile =  $this->objConfig->getcontentBasePath().'speak4freent/'.$id.'/'.$id.'.pdf';
        if (file_exists($pdfFile))
        {
            unlink($pdfFile);
        }

        return $this->convertFileFromFormat($id, 'odp', 'swf');
    }


     /**
     * Method to delete a folder recursively
     * @param string $dir The full path to the folder
     * @access public
     * @return boolean
     */
     function deldir($dir) {
         $dh=@opendir($dir);
         while ($file=@readdir($dh)) {
             if($file!="." && $file!="..") {
                 $fullpath=$dir."/".$file;
                 if(!is_dir($fullpath)) {
                     unlink($fullpath);
                 } else {
                     $this->deldir($fullpath);
                 }
             }
         }
         @closedir($dh);
         if(@rmdir($dir)) {
             return true;
         } else {
             return false;
         }
     }
     
    
    
    /**
     * Method to check for Version2 of speak4freent Flash file
     * @param string $id Record Id
     * @return boolean
     */
    public function checkspeak4freentVersion2($id)
    {
        $presentationFolder = $this->objConfig->getcontentBasePath().'speak4freent/'.$id;
        
        if (file_exists($presentationFolder.'/'.$id.'_v2.swf')) {
            return TRUE;
        } else {
            
            $objSWF = $this->getObject('pdf2flash', 'swftools');
            $objSWF->debug = FALSE;
            $objSWF->useCustomViewPort = TRUE;
            $objSWF->customViewPort = $this->getResourcePath('speak4freent2.swf');
            
            $pdf = $presentationFolder.'/'.$id.'.pdf';
            $flash2 = $presentationFolder.'/'.$id.'_v2.swf';
            
            return $objSWF->convert2SWF($pdf, $flash2);
        }
    }
    
    /**
     * Method to check for Version2 of speak4freent Flash file
     * @param string $id Record Id
     * @return boolean
     */
    public function onlyCheckspeak4freentVersion2($id)
    {
        $presentationFolder = $this->objConfig->getcontentBasePath().'speak4freent/'.$id;
        
        if (file_exists($presentationFolder.'/'.$id.'_v2.swf')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


}
?>