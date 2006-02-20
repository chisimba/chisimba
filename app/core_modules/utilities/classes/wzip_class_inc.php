<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
 * The class that is used for compression
 * @package wzip
 * @category utilities
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @version
 * @author Wesley Nitsckie 
 */
require_once('pclzip.lib.php');
class wzip extends object{
    /**
    * Constructor
    */
    function init(){
    
    }
    
    /**
    * Method used to deflate a compressed file
    * @param string $filename The path to the file
    * @param string $path The path to which the file will be unzipped
    * @return null
    */
    function unzip($filename,$path){
        // turn of reporting notices
        ini_set('error_reporting', 'E_ALL& ~E_NOTICE');
        
        //create a new instance of pclzip        
        $archive = new PclZip($filename);
        
        //extract the file
        //$objZip->extract($path);
        if ($archive->extract(PCLZIP_OPT_PATH, $path,
                        PCLZIP_OPT_REMOVE_PATH, 'install/release') == 0) {
            print ("Error : ".$archive->errorInfo(true));
        }
    }
    
    /**
    * MEthod to add files to an archive
    * @param string $filename The path to the file
    * @param string $path The path to which the file will be unzipped
    * @return null
    */
    function addArchive($path, $filename, $removePath = NULL)
    {
        
        $archive = new PclZip($filename);
        $v_list = $archive->create($path,
                PCLZIP_OPT_REMOVE_PATH, $removePath);
          if ($v_list == 0) {
            die("Error : ".$archive->errorInfo(true));
          }
            
    }
}
?>