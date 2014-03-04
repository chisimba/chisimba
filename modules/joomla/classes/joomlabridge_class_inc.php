<?php
/**
* 
* Joomla wrapper for Chisimba
*
* This class allows for synchronization of users and sessions between Joomla and
* Chisimba. 
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
* @package   joomla
* @author    Derek Keats <dkeats@uwc.ac.za>
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   $Id: joomlabridge_class_inc.php 11943 2008-12-29 21:23:33Z charlvn $
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
* Joomla: 
* Joomla wrapper for Chisimba
*
* @author Derek Keats
* @category Chisimba
* @package joomla
* @copyright AVOIR
* @licence GNU/GPL
*
*/
class joomlabridge extends dbtable
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
            parent::init('tbl_joomlastatus');
            //Instantiate the user object
            $this->objUser = $this->getObject('user', 'security');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }
    
    public function getJoomlaStatus()
    {
        $jStatus = $this->getSession('joomlastatus');
        if ($jStatus=="" || $jStatus==NULL){
            $entry  = $this->getRow("pname", "joomlastatus");
            $jStatus = $entry['pvalue'];
            //If there is no entry, then it is not installed
            if ($jStatus =="") {
                $jStatus ="NOTINSTALLED";
            }
            //Cache it in the session only if it is COMPLETED
            if ($jStatus == "COMPLETED") {
                $this->setSession('joomlastatus',$jStatus);
            }
        }
        return $jStatus;
    }
    
    public function setJoomlaStatus($status)
    {
        $entry  = $this->getRow("pname", "joomlastatus");
        $jStatus = $entry['pname'];
        //If there is no entry, then it is not installed
        if ($jStatus =="") {
            $this->addStatus($status);
        } else {
            $this->updateStatus($status);
        }        
    }
    
    public function addStatus($pvalue)
    {
        $ar = array(
          'pname' => 'joomlastatus',
          'pvalue' => $pvalue
        );
        $this->insert($ar);
    }
    
    public function updateStatus($pvalue)
    {
        $ar = array('pvalue' => $pvalue);
        $this->update('pname', 'joomlastatus', $ar);
    }
    
    public function copyUsers()
    {
        //Instantiate the Chisimba users object
        $objUsers = $this->getObject('user','security');
        //Instantiate the Joomla users object
        $objJoomlaUsers = $this->getObject('dbjoomlausers', 'joomla');
        //Get the maximum ID from the Joomla users table
        $jSql = "SELECT MAX(id) AS idbase FROM `jos_users`";
        $ar = $objJoomlaUsers->getArray($jSql);
        $idBase = $ar['0'];
        //Set counter to start above $idBase so as not to delete built in account
        $count=$idBase++;
        //Initialize transfer count
        $tfCount = 0;
        //Initialize the failure count
        $failCount = 0;
        //Get an array of the Chisimba users
        $arUsers = $objUsers->getAll();
        foreach ($arUsers as $jUser) {
            $id = $count;
            $name = $jUser['firstname'] . " " . $jUser['surname'];
            $userId = $jUser['userid'];
            $userName = $jUser['username'];
            $email = $jUser['emailaddress'];
            $block = 0;
            $sendEmail = 1;
            $usertype = "Chisimba import";
            $password = "Chisimba import";
            $gid=0;
            //Check if they exist already in Joomla
            $userExists=NULL;
            $userExists=$objJoomlaUsers->valueExists("username", $userName);
            if (!$userExists) {
                //Insert them into Joomla
                $arUserdata = array(
                  'id' => $id,
                  'name' => $name,
                  'username' => $userName,
                  'email' => $email,
                  'password' => $password,
                  'usertype' => $usertype,
                  'block' => $block,
                  'sendemail' => $sendEmail,
                  'gid' => $gid
                );
                $objJoomlaUsers->insert($arUserdata);
                $tfCount++;
            } else {
                $failCount++;
            }
        }
        return "Users transferred: " . $tfCount
         . "<br />Failures: " . $failCount;
    }
    
    public function loginJoomla()
    {
        define('_VALID_MOS', TRUE);

        //Instantiate the Joomla users object
        $objJoomlaUsers = $this->getObject('dbjoomlausers', 'joomla');
        $username = $this->objUser->userName();
        $userExists=$objJoomlaUsers->valueExists("username", $username);
        if (!$userExists) {
            //Copy the current user to Joomla  
            echo "COPY USER HERE";
        } else {
            //Get the Joomla Id
            //--------------------------___FAKE IT FOR NOW+_____
            $joomlaId=45;
            //Get the Joomla userType
            $joUserType = "Chisimba user";
            $gid = 0;
            // Need to set $mosConfig_absolute_path in order to get the config file, where it would normally be set.
            //global $mosConfig_absolute_path;
            $mosConfig_absolute_path = $this->getResourcePath('joomla','joomla');
            require_once($mosConfig_absolute_path . "/configuration.php");
            require_once($mosConfig_absolute_path . "/globals.php");
            registerGlobals();
            // A hack - otherwise its not picked up
            $GLOBALS['mosConfig_sitename']=$mosConfig_sitename;
            $GLOBALS['mosConfig_secret']=$mosConfig_secret;
            
            require_once($mosConfig_absolute_path . "/includes/database.php");
            require_once($mosConfig_absolute_path . "/includes/joomla.php");
            $db = new database( $mosConfig_host, $mosConfig_user, $mosConfig_password, $mosConfig_db, $mosConfig_dbprefix, $mosConfig_offline);
            global $mainframe;
            $mainframe = new mosMainFrame($db,NULL,$mosConfig_absolute_path);
            $mainframe->initSession(); 
            
     
            echo "JOOMLA LOGIN HERE";        
        }
    }
}
?>