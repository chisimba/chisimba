<?php

/**
 *
 * Demodata installer class
 * 
 * Performs any post install actions necessary to build a working demo
 * site.
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
 * @package   wurfl
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: installscripts_class_inc.php 18511 2010-07-28 09:35:42Z charlvn $
 * @link      http://chisimba.com/
 * @see       http://wurfl.sourceforge.net/
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * DEMODATA Installer Class
 * 
 * Performs the necessary actions to install the demodata users and their images
 * 
 * @category  Chisimba
 * @package   wurfl
 * @author    Derek Keats <derek@dkeats.com>
 * @copyright 2010 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: installscripts_class_inc.php 18511 2010-12-31 16:12:33Z dkeats $
 * @link      http://chisimba.com/
 * @see       http://wurfl.sourceforge.net/
 */
class demodata_installscripts extends dbtable
{
    /**
     * Instance of the altconfig class in the config module.
     *
     * @access private
     * @var    object
     */
    private $objAltConfig;

    /**
     * The object property initialiser.
     *
     * @access public
     */
    public function init()
    {
       $this->objAltConfig = $this->getObject('altconfig', 'config');
    }

    /**
     * The actions to perform after installation of the module.
     *
     * @access public
     */
    public function postinstall()
    {
      // Extract the userimages
        $userImages = $this->objAltConfig->getModulePath() . 'demodata/resources/userimages/userimages.zip';
        $userDataFile = $this->objAltConfig->getModulePath() . 'demodata/sql/userdata.xml';
        $targetPath = $this->objAltConfig->getSiteRootPath() . 'user_images/';
        $userXml = $this->getUserDataAsXml($userDataFile);
        if ($userXml) {
            $this->createUsers($userXml);
        }

        // Unzip the userimages demo file.
        $zip = new ZipArchive();
        if ($zip->open($userImages) === TRUE) {
            $zip->extractTo($targetPath);
            $zip->close();
        }
    }

    /**
     *
     * Get the userdata file from the SQL directory for this module
     * and return it as an XML object
     *
     * @param string $userDataFile The file path to the XML file
     * @return string Object The XML object
     *
     */
    private function getUserDataAsXml($userDataFile)
    {
        if (file_exists($userDataFile)) {
            return simplexml_load_file($userDataFile);
        } else {
            return FALSE;
        }
    }

    /**
     * Loop over the XML file and create the users
     *
     * @param strng Object $userXml The users XML
     * @return boolean TRUE
     *
     */
    private function createUsers($userXml)
    {
        $data = array();
        foreach ($userXml->user as $user) {
            $id = $user->id;
            $email = $user->emailaddress;
            $username = $user->username;
            $password = $user->pass;
            $userid = $user->userid;
            $firstname = $user->firstname;
            $surname = $user->surname;
            $title = $user->title;
            $sex = $user->sex;
            $country = $user->country;
            $cellnumber = NULL;
            $staffnumber = $user->userid;
            $accountType = $user->howcreated;
            $creationdate = $user->creationdate;
            $accountstatus = $user->isactive;
            $logins = $user->logins;
            $updated = $user->updated;
            $permtype = $user->accesslevel;
            $data = array('id' => $id,
                          'emailAddress' => $email,
                          'handle' => $username,
                          'passwd' => $password,
                          'auth_user_id' => $userid,
                          'firstName' => $firstname,
                          'surname' => $surname,
                          'title' => $title,
                          'sex' => $sex,
                          'country' => $country,
                          'cellnumber' => $cellnumber,
                          'staffnumber' => $staffnumber,
                          'howCreated' => $accountType,
                          'creationDate' => $creationdate,
                          'logins' => $logins,
                          'updated'=> $updated,
                          'is_active' => $accountstatus,
                          'perm_type' => 1,
            );
            $adduser = $this->objLuAdmin->addUser($data);
            if(!$adduser) {
                 $errorArr = $this->objLuAdmin->getErrors();
                 throw new customException($errorArr[0]['params']['reason']);
                 exit(1);
            }
            // add the new user to the regular folks group for now
            $params = array('filters' => array('group_define_name' => 'Guest'));
            $group = $this->objLuAdmin->perm->getGroups($params);
            $result = $this->objLuAdmin->perm->addUserToGroup(array('perm_user_id' => $adduser, 'group_id' => $group[0]['group_id']));
            if(!$result) {
                $errorArr = $this->objLuAdmin->getErrors();
                throw new customException($errorArr[0]['params']['reason']);
                exit(1);
            }
        }
        return TRUE;
    }
}
?>