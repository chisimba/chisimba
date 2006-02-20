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

 class dbfile extends dbTable{
    /**
	 * @var object objDBContext;
	 */
	 var $objDBContext;
     
     /**
	 * @var object objDBData;
	 */
	 var $objDBFileData;
	 
     function init(){
         parent::init('tbl_context_file');
 		 $this->objDBContext = & $this->newObject('dbcontext', 'context');
         $this->objDBFileData = & $this->newObject('dbfiledata', 'context');
     }
	 
	  /**
	 * Method to get the list of files
	 * @param string  $contextCode The context Code
	 * @return array
	 */
	 function getFiles($contextCode=NULL, $category=NULL, $orderBy='filedate DESC'){
		$objDBParentNode = & $this->newObject('dbparentnodes', 'context');
		if(!isset($contextCode)){
			
			$contextCode = $this->objDBContext->getContextCode();
		 }
		 
		 $parentId = $objDBParentNode->getParentNodeId($contextCode);
		 $where = "WHERE tbl_context_parentnodes_id = '$parentId'";
         if ($category != NULL) {
            if ($category == 'images') {
                $where .= " AND (category='images'
                OR (category IS NULL
                    AND (
                    datatype = 'jpg'
                    OR datatype = 'jpeg'
                    OR datatype = 'gif'
                    OR datatype = 'png'
                    OR datatype = 'bmp'
                    )
                   )
                )";
            }
            elseif ($category == 'documents') {
                $where .= " AND (
				category='file'" //for backward compatibility
				." OR category='documents'
                OR (category IS NULL
                    AND datatype != 'jpg'
                    AND datatype != 'jpeg'
                    AND datatype != 'gif'
                    AND datatype != 'png'
                    AND datatype != 'bmp'
                   )
                )";
            }
            else {
                $where .= " AND category='$category'";
            }
         }
		 $order = "ORDER BY $orderBy";
		 $ret = $this->getAll($where.' '.$order);
		 
		 return $ret;
	 }
     
     /**
     * Method to delete a file and the file data
     * @param string $id The Id of the file
     */
     function deleteFile($id){
         $row = $this->getRow('id', $id);
         $path = $row['path'];
         if ($path != '') {
             $result = @unlink($path);
         }
         return $this->delete('id', $id);
     }
     
     /**
     * Method to save a file 
     * @param string completePath The path to the file
     * @param string $fileId The File Id
     * @param string $parentId The Parent Id
     * @return boolean
     */
     function insertFile( $parentId, $params, $filepath){
        /*
        $title = $this->getParam('title');
        $name = $this->getParam('name');       
        $description = $this->getParam('description');
        $datatype = $this->getParam('datatype');
        $size = $this->getParam('size');
        $filedate = $this->getParam('filedate');
        $path =$this->getParam('path');
        */
        $title = $params['title'];
        $name = $params['name'];       
        $description = $params['description'];
        $version = $params['version'];
        $datatype = $params['datatype'];
        $size = $params['size'];
        $filedate = $params['filedate'];
        $path =$params['path'];
        $category =$params['category'];
        $fileId = $this->insert(array(
            'tbl_context_parentnodes_id' => $parentId,
            'title' => $title,
            'name' => $name,
            'filedate' => $filedate,
            'description' => $description,
            'version'=>$version,
            'size' => $size,
            'datatype' => $datatype,
            'path' => $path,
            'category' => $category
        ));
        if($path == ''){
            $this->objDBFileData->insertFileData($fileId, $parentId, $filepath);
        }
    }
     
     /**
     * Method to update a file
     * @return boolean
     */
     function updateFile($id, $params){
        /*
        $id = $this->getParam('id');
        $title = $this->getParam('title');
        $name = $this->getParam('title');        
        $description = $this->getParam('description');
        $datatype = $this->getParam('datatype');
        $size = $this->getParam('title');
        $filedate = $this->getParam('filedate');
        $path =$this->getParam('path');
        $params = array(
        'title' => $title,
        'name' => $name,
        'filedate' => $filedate,
        'description' => $description,
        'size' => $size,
        'datatype' => $datatype,
        'path' => $path);
        */
        $this->update('id', $id, $params);
        /*
        if($path == ''){       
            $row = $this->getRow('id', $id);
            $this->objDBFileData->updateFileData($id, $row['tbl_context_parentnodes_id'], $path);
        }
        */
     }
     
 }
 ?>