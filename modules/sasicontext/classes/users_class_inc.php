<?php

/**
 * Users class
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
 * @package   sasicontext
 * @author    Qhamani Fenama <qfenama@gmail.com>
 * @copyright 2010 Qhamani Fenama
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
 */

class users extends object {// extends abauth implements ifauth {

    /**
     * Description for public
     * @var    unknown
     * @access public
     */
    public $objLanguage;
    /**
     * Description for public
     * @var    unknown
     * @access public
     */
    public $objConfig;
    /**
     * Description for public
     * @var    object
     * @access public
     */
    public $dbSasicontext;
    /**
     * Description for public
     * @var    object
     * @access public
     */
    public $objUser;
    /**
     * Description for public
     * @var    object
     * @access public
     */
    public $addtocontext = 0;
    /**
     * Description for public
     * @var    object
     * @access public
     */
    public $addtosite = 0;
    /**
     * Description for public
     * @var    object
     * @access public
     */
    public $removed = 0;
    /**
     * Description for public
     * @var    object
     * @access public
     */
    private $ldapserver;
    /**
     * Description for public
     * @var    object
     * @access public
     */
    private $ldapuservarname;
    /**
     * Description for public
     * @var    object
     * @access public
     */
    private $ldapWhere;

    /**
     * Constructor method to instantiate objects and get variables
     */
    public function init() {
        try {
            $this->objLanguage      = $this->getObject('language', 'language');
            $this->objUser          = $this->getObject('user', 'security');
            $this->dbSasicontext    = $this->getObject('dbsasicontext', 'sasicontext');
            $this->objSasiwebserver = $this->getObject('sasiwebserver');
            $this->objUserContext   = $this->getObject('usercontext', 'context');
            $this->dbContext        = $this->getObject('dbcontext', 'context');
            $this->objGroups        = $this->getObject('managegroups', 'contextgroups');
            $this->objGroupAdmin    = $this->getObject('groupadminmodel', 'groupadmin');
            $this->objAuthLdap      = $this->getObject('auth_uwcldap', 'security');
            $this->contextCode      = $this->dbContext->getContextCode();
            $this->objSysConfig     = $this->getObject('dbsysconfig', 'sysconfig');
            $this->ldapserver       = $this->objSysConfig->getValue ( 'MOD_SECURITY_LDAPSERVER', 'security' );
            $this->ldapuservarname  = $this->objSysConfig->getValue ( 'MOD_SECURITY_LDAPUSERVARNAME', 'security' );
            $this->ldapwhere        = $this->objSysConfig->getValue ( 'MOD_SECURITY_LDAPWHERE', 'security' );
            if (strlen ( $this->ldapserver ) < 3) {
                $this->ldapserver = "192.102.9.68"; // hard-coded for now - will be changed later
                $this->ldapuservarname = 'generationqualifier';
                $this->ldapWhere = "o=UWC";
            }
        } catch (customException $e) {
            echo customException::cleanUp();
            die();
        }
    }


    /*
     * Method that get the users on the class list
     *
     * @param var $subject
     * @return array $classlist
     *
    */
    public function getClassList($subject) {

        $param = array('Module' => $subject, 'Year' => date('Y'));
        $data = $this->objSasiwebserver->getData('Browse_Class_List', $param);
        $simpledata = $data['Browse_Class_ListResult']['Class_List']['row'];
        return $simpledata;
    }

    /*
     * Method that check whether a user already exist
     *
     * @param var $studeNo
     * @return boolean
     *
    */
    public function checkUserExists($username) {

        $userDetails = $this->objUser->lookupData($username);
        if ($userDetails ==  FALSE) {
            return FALSE;
        }
        return TRUE;
    }

    /*
     * Method that check whether a user is a member of a [-context-]
     *
     * @param var $studeNo
     * @return boolean
     *
    */
    public function checkIsAMember($username) {

        $userDetails = $this->objUser->lookupData($username);
        if ($userDetails ==  FALSE) {
            return FALSE;
        }
        else {
            if ($this->objUserContext->isContextMember( $userDetails['userid'], $this->contextCode)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    /*
     * Method that add users from the sasi webserver subject classlist to the context
     *
     * @param var @contextCode
     * @return boolean
    */
    public function synchronizeAll($contextCode, $remove) {
        //Get the class list
        $sub = $this->dbSasicontext->getSasicontextByField('contextcode', $contextCode);
        $subject = $sub['subject'];
        $users = $this->getClassList($subject);

        //Add users to the class list
        if (!empty($users[0])) {
            $arr = array();
            foreach ($users as $user) {
                $username = $user['column'][0];
                $arr[] = $username;
                //Check if user is already on the db(if not) add user to db from LDAP
                if(!$this->checkUserExists($username)) {
                    $dn = $this->objAuthLdap->checkUser($username);

                    $ldapconn = ldap_connect ( $this->ldapserver );
                    $ldapbind = @ldap_bind ( $ldapconn, $dn );

                    $this->objAuthLdap->_record = $this->objAuthLdap->getInfo ( $ldapconn, $username );
                    $passwd = $this->objAuthLdap->createUser ( $username );

                    //Close the connection
                    ldap_close ( $ldapconn );

                    $this->addtosite++;
                }
                //Check if user is not a member of the course already(if not) add the user to the course
                if(!$this->checkIsAMember($username)) {
                    $this->synchronizeUser($contextCode, $username, 'Students');
                }
            }

            //Remove users that are not in the class list from the course users
            if ($remove) {

                //get context users
                $contextusers = $this->objGroups->contextUsers('Students' ,$this->contextCode, array('userid'));

                foreach ($contextusers as $user) {
                    //compare with the class list from sasi webserver
                    if (!in_array($user['username'], $arr)) {
                        //get the userid via username
                        $userDetails = $this->objUser->lookupData($user['username']);
                        $userId = $userDetails['userid'];

                        //get the perm user id and groupId
                        $permUserId = $this->objGroupAdmin->getPermUserId($userId);
                        $groupId = $this->objGroupAdmin->getLeafId(array($contextCode, 'Students'));
                        $this->objGroupAdmin->deleteGroupUser($groupId, $permUserId);
                        $this->removed++;
                    }
                }
            }
        }
        return TRUE;
    }

    /*
     * Method that add users from the sasi webserver subject classlist to the context
     *
     * @param var @contextCode
     * @return boolean
    */
    public function synchronizeUser($contextCode, $username, $role) {

        //Get the groupId
        $groupId = $this->objGroupAdmin->getLeafId(array($contextCode, $role));
        $userDetails = $this->objUser->lookupData($username);
        $userId = $userDetails['userid'];
        $permUserId = $this->objGroupAdmin->getPermUserId($userId);

        $this->addtocontext++;
        return $this->objGroupAdmin->addGroupUser($groupId, $permUserId);
    }

    /*
     * Method that add users from the sasi webserver subject classlist to the context
     *
     * @param var @contextCode
     * @return boolean
    */
    public function synchronizeAllUsers($remove = 0) {
        $sasicontexts = $this->dbSasicontext->getAllSasicontext();
        if(is_array($sasicontexts)) {
            foreach($sasicontexts as $context) {
                $this->synchronizeAll($context['contextcode'], $remove);
            }
        }
    }
}
?>
