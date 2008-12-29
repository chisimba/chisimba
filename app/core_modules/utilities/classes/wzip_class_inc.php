<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check
/**
 * The class that is used for compression
 *
 * @category  Chisimba
 * @package utilities
 * @author Wesley Nitsckie
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version $Id$
 * @link      http://avoir.uwc.ac.za
 */

require_once('pclzip.lib.php');
class wzip extends object{

    /**
    * @var stores an error
    * @access public
    *
    */
    public $error;

    /**
    * Constructor
    */
    function init(){

    }

    /**
    * Method used to inflate a compressed file, with return values
    * @author Nic Appleby
    * @param string $filename The path to the file
    * @param string $path The path to which the file will be unzipped
    * @return TRUE|FALSE
    */
    function unZipArchive($filename,$path){
        $this->error = NULL;
        //if we are going to turn of reporting notices we should
        //put things back the way they were afterwards
        $error_reporting = ini_get('error_reporting');
        ini_set('error_reporting', 'E_ALL & ~E_NOTICE');

        //create a new instance of pclzip
        $archive = new PclZip($filename);
        if ($archive->extract(PCLZIP_OPT_PATH,$path,PCLZIP_OPT_REMOVE_PATH,'install/release') == 0) {
            $ret = FALSE;
            $this->error = "Error : ".$archive->errorInfo(TRUE);
        } else {
            $ret = TRUE;
        }
        ini_set('error_reporting',$error_reporting);
        return $ret;
    }

    /**
    * Method used to deflate a compressed file
    * @param string $filename The path to the file
    * @param string $path The path to which the file will be unzipped
    * @return NULL
    * @deprecated Terrible error handling, rather use the method above
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
            print ("Error : ".$archive->errorInfo(TRUE));
        }
    }




    /**
    * MEthod to add files to an archive
    * @param string $filename The path to the file
    * @param string $path The path to which the file will be unzipped
    * @return NULL
    */
    function addArchive($path, $filename, $removePath = NULL)
    {
        $archive = new PclZip($filename);
        $v_list = $archive->create($path, PCLZIP_OPT_REMOVE_PATH, $removePath);
          if ($v_list == 0) {
            die("Error : ".$archive->errorInfo(TRUE));
          }
          return $filename;

    }

    /**
    * Method to get the list of files in an archive
    * @param string path to zip file
    * @return array list of files
    */
    public function listArchiveFiles($path)
    {
        $zip = new PclZip($path);

        if (($list = $zip->listContent()) == 0) {
            return FALSE;
        } else {
            return $list;
        }
    }
    
    
    /**
     * zip extension zip create
     *
     * @param zip filename $zipFN
     * @param array of files to zip up $files
     * @param remove path? $removepath
     * @param move the files into the zip? $movefiles2zip
     * @return zipfile
     */
    public function packFilesZip($zipFN, $files, $removepath=TRUE, $movefiles2zip=TRUE)
    {
        if (!extension_loaded('zip')) {
            throw new customException($this->objLanguage->languageText("mod_utilities_nozipext", "utilities"));
        }
        $zip = new ZipArchive();
        if ($zip->open($zipFN, ZIPARCHIVE::CREATE)!==TRUE) {
            log_debug("Zip pack Error: cannot open <$zipFN>\n");
            throw new customException($this->objLanguage->languageText("mod_utilities_nozipcreate", "utilities"));
        }
        foreach ($files as $f) {
            $localFN = $removepath ? basename($f) : $f;
            $zip->addFile($f, $localFN);
        }
        $zip->close();
        return $zipFN;
    }

    public function unPackFilesFromZip($zipfile, $dest)
    {
        $zip = new ZipArchive;
        if ($zip->open($zipfile) === TRUE) {
            $zip->extractTo($dest);
            $zip->close();
            return TRUE;
        } else {
            return FALSE;
        }
    }
}
?>