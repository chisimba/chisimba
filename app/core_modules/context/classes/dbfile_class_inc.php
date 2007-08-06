<?php

/**
 * db file class
 * 
 * File database abstraction class for context
 * 
 * PHP version 3
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   context
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check


/**
 * db file class
 * 
 * File database abstraction class for context
 * 
 * @category  Chisimba
 * @package   context
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
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
	 
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return void  
     * @access public
     */
     function init(){
         parent::init('tbl_context_file');
 		 $this->objDBContext =  $this->newObject('dbcontext', 'context');
         $this->objDBFileData =  $this->newObject('dbfiledata', 'context');
     }
	 
	  /**
	 * Method to get the list of files
	 * @param string  $contextCode The context Code
	 * @return array
	 */
	 function getFiles($contextCode=NULL, $category=NULL, $orderBy='filedate DESC'){
		$objDBParentNode =  $this->newObject('dbparentnodes', 'context');
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
     * @param  string  completePath The path to the file
     * @param  string  $fileId      The File Id
     * @param  string  $parentId    The Parent Id
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