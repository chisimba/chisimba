<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check

/**
 * Class to manage zipped files *
 * in to the database
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @author Wesley Nitsckie
 * @package utilities
 */
 class depzip extends object{
 
     /**
     *@var object objConfig
     */
     var $objConfig;
     
     /**
    *Initialize method
    */
     function init(){
         $this->objConfig= $this->newObject('config','config');
     }
     
     /**
     *Method to unzip a file 
     *@param string $file The full path to the file
     *@param string $destination The path to the destination folder
     *@access public
     *@return array The results
     */
     function unZip($file, $destination){     
         $res=-1;
        // create the unzip command
        $unzipCmd='unzip -o '.$this->objConfig->contentBasePath().$file.' -x -d '.$this->objConfig->contentBasePath().'import_dir';
        //execute the command
        $UnusedArrayResult=exec ($unzipCmd,$UnusedArrayResult,$res);      
     }
     
     /**
     *       From "User Contributed Notes" at http://it.php.net/manual/en/function.rmdir.php
     * Thanks flexer at cutephp dot com
     * 
     *function to check if the dir exists or is empty
     * AUthor Paul Scott
     * 
     *@param string $dir The full path to the folder     
     *@access public
     *@return boolean
     */
        function is_emtpy_folder($folder){
            $folder_content = "null";
            if(is_dir($folder) ){
                $handle = opendir($folder);
                while( (gettype( $name = readdir($handle)) != "boolean")){
                        $name_array[] = $name;
                }
                foreach($name_array as $temp)
                    $folder_content .= $temp;
            
                if($folder_content == "..")
                    return true;
                else
                    return false;
            
                closedir($handle);
            }
            else
                return true; // folder doesnt exist
        }
        
      /**
     *Method to delete a folder recursively 
     *@param string $dir The full path to the folder     
     *@access public
     *@return boolean
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
     *Method to delete a folder recursively 
     *@param string $dir The full path to the folder     
     *@access public
     *@return boolean
     */
        function m_deldir($aDir) {
            //echo('<p>Deleting:'.$aDir);
            if($this->is_emtpy_folder("import_dir") == 'true')
            {
                $this->deldir("import_dir");
                //unlink("import_dir");
                rename($aDir, "import_dir");
            }
            else{    
            rename($aDir, "import_dir");
            // unlink($aDir);
            }
            return; // uncomment to skip deletion (leave things)
        }
 }

?>