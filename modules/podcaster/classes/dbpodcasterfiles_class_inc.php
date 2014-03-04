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
class dbpodcasterfiles extends dbtable
{

    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_podcaster_files');
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
    }

   /**
   * Function to generate jnlp file
   */

  public function generatePresenterJNLP($id){
   $location = "http://" . $_SERVER['HTTP_HOST'];
   $presentationsURL = $location . $this->getResourceUri('presentations', 'realtime');
   $filename =  $this->objConfig->getcontentBasePath().'podcaster/presenter_studio.jnlp';
   $codebase=$this->objConfig->getSitePath().'/usrfiles/podcaster';
   $fileHandle = fopen($filename, 'w') or die("can't open file");
   fwrite($fileHandle,"<?xml version=<\"1.0\" encoding=\"utf-8\"?>\n");
   fwrite($fileHandle,"<jnlp spec=\"1.0+\"\n");
   fwrite($fileHandle,"codebase=\"".$codebase."\"\n");
   fwrite($fileHandle,"href=\"presenter_studio.jnlp\">\n");
   fwrite($fileHandle,"<information>\n");
   fwrite($fileHandle,"<title>Presenter Studio</title>\n");
   fwrite($fileHandle,"<vendor>FSIU</vendor>\n");
   fwrite($fileHandle,"<description>Allows user to show live presentations</description>\n");
   fwrite($fileHandle,"<homepage href=\"http://fsiu.uwc.ac.za\"/>\n");
   fwrite($fileHandle,"<description kind=\"short\">Allows user to show live presentations</description>\n");
   fwrite($fileHandle,"</information>\n");
   fwrite($fileHandle,"<resources>\n");    
   fwrite($fileHandle,"<jar href=\"".$presentationsURL."/presentations-client.jar\"/>\n");   
  // fwrite($fileHandle,"<jar href=\"".$presentationsURL."/video.jar\"/>\n");   
        
   fwrite($fileHandle," <j2se version=\"1.5+\"  initial-heap-size=\"128M\" max-heap-size=\"512M\"\n");
   fwrite($fileHandle," href=\"http://java.sun.com/products/autodl/j2se\"/>\n");
       
   fwrite($fileHandle," </resources>\n");
   fwrite($fileHandle,"<security>\n");
   fwrite($fileHandle,"<all-permissions/>\n");
   fwrite($fileHandle,"</security> \n");


   fwrite($fileHandle,"<application-desc\n");
   fwrite($fileHandle,"main-class=\"avoir.realtime.presentations.client.presenter.ui.MainFrame\" name=\"Presenter Studio\" >\n");
   fwrite($fileHandle,"<argument>".$_SERVER['HTTP_HOST']."</argument>\n");
   fwrite($fileHandle,"<argument>3128</argument>\n");
   fwrite($fileHandle,"<argument>".$this->objConfig->getcontentBasePath()."podcaster/".$id."</argument>\n");

   fwrite($fileHandle,"<argument>".$this->objUser->userName()."</argument>\n");

   fwrite($fileHandle,"</application-desc>\n"); 
   fwrite($fileHandle,"</jnlp>\n");
   fclose($fileHandle);
  }

  /**
   * Function to generate jnlp file
   */

  public function generateClientJNLP($id){
   $location = "http://" . $_SERVER['HTTP_HOST'];
   $presentationsURL = $location . $this->getResourceUri('presentations', 'realtime');
   $filename =  $this->objConfig->getcontentBasePath().'podcaster/client.jnlp';
   $codebase=$this->objConfig->getSiteRoot().'/usrfiles/podcaster';
   $fileHandle = fopen($filename, 'w') or die("can't open file");
   fwrite($fileHandle,"<?xml version=<\"1.0\" encoding=\"utf-8\"?>\n");
   fwrite($fileHandle,"<jnlp spec=\"1.0+\"\n");
   fwrite($fileHandle,"codebase=\"".$codebase."\"\n");
   fwrite($fileHandle,"href=\"client.jnlp\">\n");
   fwrite($fileHandle,"<information>\n");
   fwrite($fileHandle,"<title>Live Presentations</title>\n");
   fwrite($fileHandle,"<vendor>FSIU</vendor>\n");
   fwrite($fileHandle,"<description>Allows user to view live presentations</description>\n");
   fwrite($fileHandle,"<homepage href=\"http://fsiu.uwc.ac.za\"/>\n");
   fwrite($fileHandle,"<description kind=\"short\">Allows user to view live presentations</description>\n");
   fwrite($fileHandle,"</information>\n");
   fwrite($fileHandle,"<resources>\n");    
   fwrite($fileHandle,"<jar href=\"".$presentationsURL."/presentations-client.jar\"/>\n");   
  // fwrite($fileHandle,"<jar href=\"".$presentationsURL."/video.jar\"/>\n");   
        
   fwrite($fileHandle," <j2se version=\"1.5+\"  initial-heap-size=\"128M\" max-heap-size=\"512M\"\n");
   fwrite($fileHandle," href=\"http://java.sun.com/products/autodl/j2se\"/>\n");
       
   fwrite($fileHandle," </resources>\n");
   fwrite($fileHandle,"<security>\n");
   fwrite($fileHandle,"<all-permissions/>\n");
   fwrite($fileHandle,"</security> \n");


   fwrite($fileHandle,"<application-desc\n");
   fwrite($fileHandle,"main-class=\"avoir.realtime.presentations.client.ClientViewer\" name=\"Presenter Studio\" >\n");

   fwrite($fileHandle,"<argument>".$_SERVER['HTTP_HOST']."</argument>\n");
   fwrite($fileHandle,"<argument>3128</argument>\n");
   fwrite($fileHandle,"<argument>".$this->objConfig->getcontentBasePath()."podcaster_thumbnails/".$id."</argument>\n");
   fwrite($fileHandle,"<argument>".$id."</argument>\n");
   fwrite($fileHandle,"</application-desc>\n"); 

   fwrite($fileHandle,"</jnlp>\n");
   fclose($fileHandle);
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
                'processstage'=>'uploadedraw',
                'creatorid' => $this->objUser->userId(),
                'dateuploaded' => strftime('%Y-%m-%d', mktime())            
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
    //renamed updateReadyForConversion to updateFinal
    public function updateFinal($id)
    {
        return $this->update('id', $id, array(
                'processstage' => 'uploadedfinal',
                'creatorid' => $this->objUser->userId(),
                'dateuploaded' => strftime('%Y-%m-%d %H:%M:%S', mktime()),
            ));
    }

    public function getLatestPodcasts()
    {
        return $this->getAll(' ORDER BY dateuploaded DESC LIMIT 10');
    }
    public function getLatestPodcast()
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
            case 'mp3':
                $type = 'mp3';
                break;
            default:
                $type = 'unknown';
                break;
        }

        return $type;
    }

    private function isInProcess($id)
    {
        $row = $this->getRow('id', $id);

        return ($row['inprocess'] == 'Y' ? TRUE : FALSE);
    }


    private function setOutOfProcess($id)
    {
        return $this->update('id', $id, array('inprocess'=>'N'));
    }

    private function setInProcess($id, $step)
    {
        return $this->update('id', $id, array('inprocess'=>'Y', 'processstage'=>$step));
    }


    public function getPodcastThumbnail($id, $title='')
    {
        $objViewer = $this->getObject('viewer');
        return $objViewer->getPodcastThumbnail($id, $title);
    }

    public function deleteFile($id)
    {
        // First Step is to scan presentation directory and delete all file in that folder
        // Load Object
        $objScanForDelete = $this->newObject('scanfordelete');

        // Set Directory to Var
        $presentationFolder = $this->objConfig->getcontentBasePath().'podcaster/'.$id;

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

        $thumb = $this->objConfig->getcontentBasePath().'podcaster_thumbnails/'.$id.'.jpg';
        if (file_exists($thumb))
        {
            unlink ($thumb);
        }



        $objTags = $this->getObject('dbpodcastertags');
        $objTags->deleteTags($id);

        $objSlides = $this->getObject('dbpodcasterslides');
        $objSlides->deleteSlides($id);

        $this->delete('id', $id);

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
     * Method to check for Version2 of podcaster Flash file
     * @param string $id Record Id
     * @return boolean
     */
    public function checkpodcasterVersion2($id)
    {
        $presentationFolder = $this->objConfig->getcontentBasePath().'podcaster/'.$id;
        
        if (file_exists($presentationFolder.'/'.$id.'_v2.swf')) {
            return TRUE;
        } else {
            
            $objSWF = $this->getObject('pdf2flash', 'swftools');
            $objSWF->debug = FALSE;
            $objSWF->useCustomViewPort = TRUE;
            $objSWF->customViewPort = $this->getResourcePath('podcaster2.swf');
            
            $pdf = $presentationFolder.'/'.$id.'.pdf';
            $flash2 = $presentationFolder.'/'.$id.'_v2.swf';
            
            return $objSWF->convert2SWF($pdf, $flash2);
        }
    }
    
    /**
     * Method to check for Version2 of podcaster Flash file
     * @param string $id Record Id
     * @return boolean
     */
    public function onlyCheckpodcasterVersion2($id)
    {
        $presentationFolder = $this->objConfig->getcontentBasePath().'podcaster/'.$id;
        
        if (file_exists($presentationFolder.'/'.$id.'_v2.swf')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }


}
?>