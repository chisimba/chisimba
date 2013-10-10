<?php
/**
 * Database access to manage users in the oeruserdata module
 *
 * Database access to manage users in the oeruserdata module. It works with
 * primary user data, as well as the userextra data that is created
 * by the OER module.
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
 * Database access to manage users in the oeruserdata module
 *
 * Database access to manage users in the oeruserdata module. It works with
 * primary user data, as well as the userextra data that is created
 * by the OER project.
 * 
 * @package   oer
 * @author    Derek Keats derek@dkeats.com
 * 
 */
class dboeruserdata extends dbtable {

    /**
     * 
     * @var string object $objAdmin Holds the useradmin model from security
     * @access private
     * 
     */
    private $objAdmin;
    /**
     * 
     * @var string object $objAdmin Holds the user object
     * @access private
     * 
     */
    private $objUser;
    
    /**
     * 
     * @var string array $fieldsAr An array of all the fields we will be using
     * @access private
     * 
     */
    private $fieldsAr;

    /**
     * 
     * Standard init method
     * 
     * @access public
     * @return void
     * 
     */
    public function init() {
        parent::init("tbl_oeruserdata_userextra");
        $this->objAdmin = $this->getObject('useradmin_model2', 'security');
        $this->objUser = $this->getObject('user', 'security');
        $this->ObjDbUserGroups = $this->getObject('dbusergroups', 'oer');
        $this->fieldsAr = array(
            'id', 'userid', 'title', 'firstname', 'surname', 'username', 'password', 
            'confirmpassword', 'email', 'sex', 'birthdate', 'address', 'city', 
            'state', 'postalcode', 'country', 'orgcomp', 'jobtitle', 
            'occupationtype', 'workphone', 'mobilephone', 'website', 
            'description', 'mode');
    }
    
    /**
     * 
     * Load the data where each field becomes a property of this class
     * 
     * @param boolean $sanityCheck Whether or to perform security parsing
     * @access private
     * @return VOID
     * 
     */
    private function loadData($sanityCheck=FALSE)
    {
        if ($sanityCheck) {
            $objSanity = $this->getObject('sanitizevars', 'security');
        }
        foreach($this->fieldsAr as $field) {
            if ($sanityCheck) {
                $strValue = $this->getParam($field, NULL);
                if ($strValue!==NULL) {
                    // No fields in the querystring
                    $objSanity->disallowQuerystringFormElements($this->fieldsAr);
                    $this->$field = $objSanity->sanitize($strValue, FALSE, TRUE);
                }
            } else {
                $this->$field = $this->getParam($field, NULL);
            }
        }
    }
    
    /**
     *
     * Save after an edit. It has to use the API to update the user
     * as well as store the extra data in the oer module's table.
     * 
     * @return string The id of the record or and error code
     * @access public 
     * 
     */
    public function editSave()
    {       
        $this->loadData();
        if ($this->id == NULL) {
            return 'ERROR_DATA_INSERT_FAIL';
        } else {
            // Send the data to the primary users table
            $this->objAdmin->updateUserDetails(
              $this->id, $this->username, $this->firstname, 
              $this->surname, $this->title, $this->email, 
              $this->sex, $this->country, $this->mobilephone, 
              NULL, $this->password, '', '');
            
            // Check if the user is already in there, else add
            
            $sql = "SELECT id FROM tbl_oeruserdata_userextra WHERE parentid='" . $this->id . "';";
            $res = $this->getArray($sql);
            if (!empty($res)) {
                // Send the data to the oer users data
                $data = array(
                    'birthdate' => $this->birthdate, 
                    'address' => $this->address, 
                    'city' => $this->city, 
                    'state' => $this->state, 
                    'postalcode' => $this->postalcode, 
                    'orgcomp' => $this->orgcomp, 
                    'jobtitle' => $this->jobtitle, 
                    'occupationtype' => $this->occupationtype, 
                    'workphone' => $this->workphone, 
                    'website' => $this->website, 
                    'description' => $this->description
                );
                $res = $this->update('parentid', $this->id, $data);
            } else {
                // Add the data to the oer users data
                $data = array(
                    'parentid' => $this->id,
                    'birthdate' => $this->birthdate, 
                    'address' => $this->address, 
                    'city' => $this->city, 
                    'state' => $this->state, 
                    'postalcode' => $this->postalcode, 
                    'orgcomp' => $this->orgcomp, 
                    'jobtitle' => $this->jobtitle, 
                    'occupationtype' => $this->occupationtype, 
                    'workphone' => $this->workphone, 
                    'website' => $this->website, 
                    'description' => $this->description
                );
                $res = $this->insert($data);
            }
            if ($res) {
                return 'SUCCESS';//$this->id;
            } else {
                return 'ERROR_DATA_INSERT_FAIL';
            }
        }
    }
    
    /**
     *
     * Save a new user. It has to use the API to update the user
     * as well as store the extra data in the oer module's table.
     * 
     * @return type string The id of the primary user record
     * @access public
     * 
     */
    public function addSave()
    {
        // Add sanity checks and disallow loggedin users from adding
        if ($this->objUser->isLoggedIn()) {
            if ($this->objUser-isAdmin()) {
                $sanityCheck=FALSE;
            } else {
                return 'ERROR_DATA_INSERT_FAIL';
            }
        } else {
            $sanityCheck=TRUE;
        }
        $this->loadData($sanityCheck);
        // Send the data to the primary users table
        // Check if the username already exists, if so, return an error
        if ($this->objAdmin->userNameAvailable($this->username) == FALSE) {
            return 'usernametaken';
        }
        
        $objCaptcha = $this->getObject('captcha', 'login');
        $res= $objCaptcha->verifyCaptcha();
        if($res != 'ok'){
            return $res;
        }
        // Generate a userId for the user
        $userId = $this->objAdmin->generateUserId();
        // Add the user and get the id back
        $pkid = $this->objAdmin->addUser(
          $userId, $this->username, $this->password, $this->title, 
          $this->firstname, $this->surname, $this->email, $this->sex, 
          $this->country, $this->mobilephone, '', 'useradmin', 1);
        //Send the user an email
        $this->objAdmin->sendRegistrationMessage($pkid, $this->password);
        
        // Send the data to the oer users data
        $data = array(
            'parentid' => $pkid,
            'birthdate' => $this->birthdate, 
            'address' => $this->address, 
            'city' => $this->city, 
            'state' => $this->state, 
            'postalcode' => $this->postalcode, 
            'orgcomp' => $this->orgcomp, 
            'jobtitle' => $this->jobtitle, 
            'occupationtype' => $this->occupationtype, 
            'workphone' => $this->workphone, 
            'website' => $this->website, 
            'description' => $this->description
        );
        $this->insert($data);
        return "SUCCESS";//$pkid;
    }
    
    /**
     *
     * Fetch data from the two tables, primary users and the extra data
     * fromt his module, and return it as an array for building the edit.
     * 
     * @param string $id The id of the primary user record
     * @return string array A record of the given user
     * @access public
     * 
     */
    public function getForEdit($id)
    {
        $sql = "SELECT tbl_users.*, " 
            . "tbl_oeruserdata_userextra.parentid, "
            . "tbl_oeruserdata_userextra.birthdate, "
            . "tbl_oeruserdata_userextra.address, "
            . "tbl_oeruserdata_userextra.city, "
            . "tbl_oeruserdata_userextra.state, "
            . "tbl_oeruserdata_userextra.postalcode, "
            . "tbl_oeruserdata_userextra.orgcomp, "
            . "tbl_oeruserdata_userextra.jobtitle, "
            . "tbl_oeruserdata_userextra.occupationtype, "
            . "tbl_oeruserdata_userextra.workphone, "
            . "tbl_oeruserdata_userextra.description, "
            . "tbl_oeruserdata_userextra.website "
            . "FROM tbl_users LEFT OUTER JOIN "
            . "tbl_oeruserdata_userextra ON tbl_oeruserdata_userextra.parentid "
            . "= tbl_users.id WHERE tbl_users.id = '$id';";
        $res = $this->getArray($sql);
        if (!empty ($res)) {
            return $res[0];
        } else {
            return FALSE;
        }
        
    }
    
    /**
     *
     * Get an array of records for the user listing page
     * 
     * @param integer $start Starting record in paginated set
     * @param integer $records Page size for paginated set
     * 
     */
    public function getForListing($start, $records)
    {
        $sql = "SELECT id, title, firstname, surname, username "
        . " FROM tbl_users WHERE isactive=1 LIMIT $start, $records";
       
        $rs = $this->getArray($sql); 
        return $rs;
    }
    
    /**
     *
     * Delete a user from the OER module and set the user as inactive 
     * in the main user table
     * 
     * @param string $id The id of the primary user record
     * @return TRUE or an error code
     */
    public function deleteUser($id) 
    {
        if ($id) {
            // Set the primary user record inactive
            $this->objAdmin->setUserAsInActive($id);
            $this->delete('parentid', $id);
            return 'RECORD_DELETED';  
        } else {
            return 'ERROR_NO_ID';
        }
    }
}
?>