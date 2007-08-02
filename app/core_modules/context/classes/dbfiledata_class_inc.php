<?php

/**
 * db file data class
 * 
 * File data database abstraction class for context
 * 
 * PHP versions 4 and 5
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
 * db file data class
 * 
 * File data database abstraction class for context
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
     * @param string $fileId   The File Id
     * @param string $parentId The Parent Id
     * @param string $path     The the path the the file
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
     * @param string $fileId   The File Id
     * @param string $parentId The Parent Id
     * @param string $path     The the path the the file
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