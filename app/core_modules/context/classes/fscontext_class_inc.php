<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
 * This object manages the File Sysmtem for the context
 * Class fscontext
 * This object handles the contentnodes table and all the
 * operations that takes place with the content nodes
 * @author Wesley Nitsckie
 * @version $Id$ 
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @package context
 **/

 class fscontext extends object
 {
     
     /**
    * @var object $objConfig
    */
    var  $objConfig;
    
    /**
    * @var object $objZip
    */
    var $objZip;
    
     function init()
     {
         $this->objConfig=&$this->newObject('altconfig','config');
         $this->objZip=&$this->newObject('depzip','utilities');
     }
 
     /**
     * Method to create the folders
     * @param string $contextId The Context Id
     */
     function createContextFolder($contextCode)
     {
         //check if content folder exists
         
         //creat a context folder
        $ret=$this->makeFolder($contextCode);
         //create a images folder
        if ($ret){
            $ret = $this->makeFolder('flash/',$contextCode);
            $ret = $this->makeFolder('maps/',$contextCode);
            $ret = $this->makeFolder('documents/',$contextCode);
            $ret = $this->makeFolder('media/',$contextCode);
            $ret = $this->makeFolder('staticcontent/',$contextCode);
            $ret = $this->makeFolder('images/',$contextCode);
        }
        return $ret;
     }
     
     /**
     * Method to delete a context Folder
     *@param string $contextId The Context Id
     */
     function deleletContextFolder($contextCode)
     {
         //STILL NEED A DECISION ON BLOBS OR FILE SYSTEM
         $this->objZip->deldir($this->objConfig->getsiteRootPath().'/usrfiles/content/'.$contextCode.'/');
     }
     
     /**
     * Method copy images
     * @param string $contextCode The Context Code
     * @param string $path the path of the file
     * @param string $file The image file
     */
    function copyImage($contextCode,$path,$file)
    {
        //STILL NEED A DECISION ON BLOBS OR FILE SYSTEM
        //$copyResult=copy($path.'/'.$file,$this->objConfig->contentBasePath().'/'.$contextCode.'/images/'.$file);
    }
    
    /**
    * Method to create a new folder in  the images folder
    * @param string $folder The Folder path
    */
    function newFolderInImages($contextCode, $folder)
    {
        $this->makeFolder('images/'.$folder, $contextCode);
    }     
     
     /**
    * method to create specified folder
    * @access public
    * @param string $folder The folder that needs to be created
    */
    function makeFolder($folder,$contextCode=NULL)
    {   
        $dir = $this->objConfig->getsiteRootPath().'/usrfiles/content';
        if (!(file_exists($dir))){
            $oldumask = umask(0);
            $ret = mkdir($dir, 0777);
            umask($oldumask);
        }
        
        if ($contextCode==''){
            $dir = $this->objConfig->getsiteRootPath().'/usrfiles/content/'.$folder;
        } else {
            $dir=$this->objConfig->getsiteRootPath().'/usrfiles/content/'.$contextCode.'/'.$folder;
        }
        if (!(file_exists($dir))){
            $oldumask = umask(0);
            $ret = mkdir($dir, 0777);  //STILL NEED A DECISION ON BLOBS OR FILE SYSTEM
            umask($oldumask);
        }
        else
        {
            $ret = FALSE;
        }
        
        return $ret;
    }
    
    /*
    * MEthod to check if a folder exists
    * @param string $path The Path to the folder
    * @return boolean
    */
    function folderExists($contextCode){
        $dir = $this->objConfig->getsiteRootPath().'/usrfiles/content/'.$contextCode;
        
        file_exists($dir);
    }
 }
 ?>
