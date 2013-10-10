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
* @author    Administrative User <admin@localhost.local>
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   $Id: dbcontent_class_inc.php 11929 2008-12-29 21:15:36Z charlvn $
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
class dbcontent extends dbtable
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
            parent::init('tbl_portalimporter_content');
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
    * Save method for DATABASETABLE{_UNSPECIFIED}
    * @param string $mode: edit if coming from edit, add if coming from add
    *
    */
    public function saveData($mode)
    {
        //Retrieve the value of the primary keyfield $id
        $id = $this->getParam('id', NULL);
        //Retrieve the value of $filepath
        $filepath = $this->getParam('filepath', NULL);
        //Retrieve the value of $filetype
        $filetype = $this->getParam('filetype', NULL);
        //Retrieve the value of $portalpath
        $portalpath = $this->getParam('portalpath', NULL);
        //Retrieve the value of $portal
        $portal = $this->getParam('portal', NULL);
        //Retrieve the value of $section
        $section = $this->getParam('section', NULL);
        //Retrieve the value of $subportal
        $subportal = $this->getParam('subportal', NULL);
        //Retrieve the value of $page
        $page = $this->getParam('page', NULL);
        //Retrieve the value of $structuredcontent
        $structuredcontent = $this->getParam('structuredcontent', NULL);
        //Retrieve the value of $rawcontent
        $rawcontent = $this->getParam('rawcontent', NULL);
        //Retrieve the value of $puid
        $puid = $this->getParam('puid', NULL);

        //If coming from edit use the update code
        if ($mode=="edit") {
            $ar = array(
              'filepath' => $filepath,
              'filetype' => $filetype,
              'portalpath' => $portalpath,
              'portal' => $portal,
              'section' => $section,
              'subportal' => $subportal,
              'page' => $page,
              'structuredcontent' => $structuredcontent,
              'rawcontent' => $rawcontent,
              'puid' => $puid
            );
            $this->update('id', $id, $ar);
        } else {
            $ar = array(
              'filepath' => $filepath,
              'filetype' => $filetype,
              'portalpath' => $portalpath,
              'portal' => $portal,
              'section' => $section,
              'subportal' => $subportal,
              'page' => $page,
              'structuredcontent' => $structuredcontent,
              'rawcontent' => $rawcontent,
              'puid' => $puid
            );
            $this->insert($ar);
        }

    }

    /**
    * Method to retrieve the data for edit and prepare the vars for
    * the edit template.
    *
    * @param string $mode The mode should be edit or add
    */
    public function getForEdit()
    {
        $order = $this->getParam("order", NULL);
        // retrieve the group ID from the querystring
        $keyvalue=$this->getParam("id", NULL);
        if (!$keyvalue) {
          die($this->objLanguage->languageText("modules_badkey").": ".$keyvalue);
        }
        // Get the data for edit
        $key="id";
        return $this->getRow($key, $keyvalue);
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