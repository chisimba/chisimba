<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
* Class to access the Context Tables 
* @package dbfile
* @category context
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version 
* @author Wesley  Nitsckie
* @example :
*/

 class dbfiledata extends dbTable{
	 /**
	 * @var object objDBContext;
	 */
	 var $objDBContext;
	 
	 /**
	 * Constructor
	 */
     function init(){
         parent::init('tbl_context_filedata');
 		 $this->objDBContext = & $this->newObject('dbcontext', 'context');

	 }
     
     
     
     /**
     * Method to insert file data
     * @param string $fileId The File Id
     * @param string $parentId The Parent Id
     * @param string $path The the path the the file
     */
     function insertFileData($fileId, $parentId, $path)
     {
         $completepath = $path;
         $fp = fopen(realpath($completepath), "rb");
         $count=0;
        while (!feof($fp)) 
        {        
              // Make the data mysql insert safe
              
            $binarydata = fread($fp, 65535);
            $this->insert(array(
                'tbl_context_file_tbl_context_parentnodes_id' => $parentId,
                'tbl_context_file_id' => $fileId,
                'segment' => $count,
                'filedata' => $binarydata
            ));            
            $count=$count+1;
        }
        fclose($fp);    
     
     }
     
     /**
     * Method to update file data
     * @param string $fileId The File Id
     * @param string $parentId The Parent Id
     * @param string $path The the path the the file
     */
     function updateFileData($fileId, $parentId, $path)
     {
         //first delet the old file data
         $this->delete('tbl_context_file_id', $fileId);
         //then add the new binary data
         $this->insertFileData($fileId,  $parentId, $path);
     }
	 
	 
 }
 ?>