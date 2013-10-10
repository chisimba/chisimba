<?php
set_time_limit(0);
/**
* 
* Portal importer
*
* Portal importer module
* 
* PHP version 5
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
* @package   portalimporter
* @author    Charl Mert
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   $Id: dblog_class_inc.php 7861 2008-01-27 16:58:28Z dkeats $
* @link      http://avoir.uwc.ac.za
*/
 
// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 * 
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *         
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
* 
* Portal importer: 
* Portal importer module
*
* @author Administrative User
* @category Chisimba
* @package portalimporter
* @copyright UWC/AVOIR
* @licence GNU/GPL
*
*/
class dblog extends dbtable
{

    
    /**
    *
    * @param string object $objUser A property to hold an instance of the user object
    *
    */
    public $objUser;
  

    
    /**
    *
    * Constructor for the module dbtable class for DATABASETABLE{_UNSPECIFIED}
    * It sets the database table via the parent dbtable class init
    * method, and instantiates required objects.
    *
    */
    public function init()
    {
        try {
            parent::init('tbl_portalimporter_log');
            //Instantiate the user object
            $this->objUser = $this->getObject('user', 'security');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }
  
    /**
    *
    * This function will log the portal import to the database
    * @param string $mode: edit if coming from edit, add if coming from add
    *
    */
    public function log($fullpath, $contentid, $sectionid, $localpath='', $type='')
    {

            $ar = array(
              'filepath' => $fullpath,
              'filetype' => $type,
              'content_id' => $contentid,
              'section_id' => $sectionid,
              'localpath' => $localpath
            );

            $this->insert($ar);
    }

 
    /**
    *
    * This function will return the content id of the filename 
    * @author Charl Mert
    */
    public function getContentFileMatch($filename, $sectionid = '')
    {
		return $this->getAll("WHERE filepath like '%".mysql_escape_string($filename)."%'");
    }

    /**
    *
    * This function will return the section_id based on the path specified
	*
    * @author Charl Mert
    */
    public function getSectionPathMatch($path)
    {
		$sql = "SELECT DISTINCT(section_id) from tbl_portalimporter_log
				WHERE filepath like '%".mysql_escape_string($path)."%'";

		$data = $this->query($sql);

		return $data;	
    }


 
    /**
    *
    * Delete a record from DATABASETABLE{_UNSPECIFIED}. Use cautiously as it can delete
    * all records by accident if the wrong key is used.
    *
    * @param string $key The key of the record to delete
    * @param string $keyValue The value of the key where deletion should take place
    *
    */
    public function deleteRecord($key, $keyValue)
    {
       $this->delete($key, $keyValue);
    }
  

}
?>
