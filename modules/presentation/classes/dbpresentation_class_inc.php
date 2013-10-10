<?php
/**
 * 
 * _SHORTDESCRIPTION
 * 
 * _LONGDESCRIPTION
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
 * @package   helloforms
 * @author    _AUTHORNAME _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: dbpresentation_class_inc.php 11937 2008-12-29 21:20:32Z charlvn $
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
* Controller class for Chisimba for the module _MODULECODE
*
* @author _AUTHORNAME
* @package _MODULECODE
*
*/
class dbpresentation extends dbtable
{
    
    /**
    * 
    * Intialiser for the _MODULECODE controller
    * @access public
    * 
    */
    public function init()
    {
        try {
            parent::init('tbl_presentation');
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
    * Save method for tbl_presentation
    * @param string $mode: edit if coming from edit, add if coming from add
    *
    */
    public function saveData($mode)
    {
        //Retrieve the value of the primary keyfield $id
        $id = $this->getParam('id', NULL);
        //Retrieve the value of $title
        $title = $this->getParam('title', NULL);
        //Retrieve the value of $description
        $description = $this->getParam('description', NULL);
        //Retrieve the value of $slides
        $slides = $this->getParam('slides', NULL);
        //If coming from edit use the update code
        if ($mode=="edit") {
            $ar = array(
              'title' => $title,
              'description' => $description,
              'slides' => $slides,
              'modified' => $this->now(),
              'modifierid' => $this->objUser->userId()
            );
            $this->update('id', $id, $ar);
        } else {
            $ar = array(
              'title' => $title,
              'description' => $description,
              'slides' => $slides,
              'created' => $this->now(),
              'creatorid' => $this->objUser->userId()
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
}    
?>
