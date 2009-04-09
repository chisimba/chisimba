<?php
/* -------------------- LDAP INTERFACE CLASS ----------------*/

/**
 *
 * Plugin authenticatoin class to authenticate a user via LDAP
 *
 * @author Derek Keats
 * @category Chisimba
 * @package security
 * @copyright AVOIR
 * @licence GNU/GPL
 *
 */

$this->loadClass ( "abauth", "security" );
$this->loadClass ( "ifauth", "security" );

class auth_ldap extends abauth implements ifauth {
    private $ldapserver;
    private $ldapuservarname;
    private $ldapWhere;

    /**
     *
     * Init method. It sets up the LDAP connection parameters
     * and instantiates required objects.
     *
     */
    public function init() {
        //Instantiate the configuration object
        $this->objConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        $this->objUser = $this->getObject ( 'user', 'security' );
        $this->ldapserver = $this->objConfig->getValue ( 'MOD_SECURITY_LDAPSERVER', 'security' );
        $this->ldapuservarname = $this->objConfig->getValue ( 'MOD_SECURITY_LDAPUSERVARNAME', 'security' );
        $this->ldapwhere = $this->objConfig->getValue ( 'MOD_SECURITY_LDAPWHERE', 'security' );
        if (strlen ( $this->ldapserver ) < 3) {
            $this->ldapserver = "192.102.9.68"; // hard-coded for now - will be changed later
            $this->ldapuservarname = 'generationqualifier';
            $this->ldapWhere = "o=UWC";
        }
        // Have to call the parent to init the class properties for sessions
        parent::init ( 'tbl_users' );
    }

    /**
     *
     * Method to authenticate the user via DAP
     * @param string $username The username supplied in the login
     * @param string $password The password supplied in the login
     * @return TRUE|FALSE Boolean indication of success of login
     */
    public function authenticate($username, $password, $remember = true) {
        // Check for blank password - there's a bug in LDAP that makes it accept '' as valid.
        //if ($password == '') {
        //    return FALSE;
        //}
        // Get the user domain-name - return FALSE if its not there
        $dn = $this->checkUser ( $username );
        //No dn found there no such user on the system.
        if (! $dn) {
            return FALSE;
        }
        // Now try to "login" to LDAP with the domain-name and password
        $ldapconn = ldap_connect ( $this->ldapserver );
        $ldapbind = ldap_bind ( $ldapconn, $dn, $password );
        if (! $ldapbind) {
            ldap_close ( $ldapconn );
            return FALSE;
        } else {
            // If the login succeeded we can get the info.
            $this->_record = $this->getInfo ( $ldapconn, $username );
            $this->createUser ( $username );
            ldap_close ( $ldapconn );
            $login = $this->objLu->login($username, $password, $remember);
            if(!$login) {
                // check if user is inactive
                if($this->objLu->isInactive()) {
                    throw new customException("User is inactive, please contact site admin");
                }
                else {
                    return FALSE;
                }
            }

            return TRUE;
        }
    }

    public function getUserDataAsArray($username) {
        return $this->_record;
    }

    /**
     *
     *
     */
    function createUser($username) {
        $data = $this->objUser->lookupData ( $username );
        $info = $this->getUserDataAsArray ( $username );
        //var_dump($data); var_dump($info); die();
        if (is_array ( $data ) || $this->objUser->valueExists ( 'userid', $info ['userid'] )) // if we already have this user
        {
            return TRUE;
        } else { // new user
            // Build up an array of the user's info
            if ($info ['userid'] == FALSE) {
                $info ['userid'] = mt_rand ( 1000, 9999 ) . date ( 'ymd' );
            } else {
                $info ['staffnumber'] = $info ['userid'];
            }
            $infoauth_user_id = $info ['userid'];
            $infosex = '';
            //$info ['accessLevel'] = 'guests';
            $infohowCreated = 'LDAP';
            $infois_active = '1';
            $infoemailAddress = $info['emailaddress'];
            $infohandle = $username;
            $infopasswd = '--LDAP--';
            $infofirstName = $info['firstname'];
            $objConf2 = $this->getObject ( 'altconfig', 'config' );
            $infocountry = $objConf2->getCountry ();
            $infoperm_type = 1;

            // To create the new user on the system.
            $tbl = $this->getObject ( 'useradmin_model2', 'security' );
            $id = $tbl->addUser ( $infoauth_user_id, $username, $infopasswd, $info['title'], $infofirstName, $info['surname'], $infoemailAddress, $infosex, $infocountry, '', $infoauth_user_id, $infohowCreated, $accountstatus='1' );
            // If LDAP confirms the user is an Academic,
            // add as a site-lecturer in KNG groups.
            if ($this->isAcademic ( $username )) {
                $this->objUser->addLecturer ( $id );
            }
        }

    }

    /**
     * method to contact the ldap server and see if a given username is valid there
     * @author James Scoble
     * @param string $username
     * @param string $where the LDAP "domain" to look in
     * @return string|bool - string if successful, FALSE if not
     */
    public function checkUser($username) {
        $ldapconn = ldap_connect ( $this->ldapserver );
        $ldapbind = @ldap_bind ( $ldapconn );
        $where = $this->ldapWhere;
        if (! $ldapbind) {
            $this->setSession ( 'ldaperror', 'FAIL' );
            return FALSE;
        }
        $filter = 'cn=' . $username;
        $look = array ('dn' );
        $find = ldap_search ( $ldapconn, $where, $filter, $look );
        $data = ldap_get_entries ( $ldapconn, $find );
        ldap_close ( $ldapconn );
        if ($data ['count'] > 0) {
            return $data [0] ['dn'];
        } else {
            return FALSE;
        }
    }

    /** method to contact the ldap server and see if a given username and password are valid there
     * @author James Scoble
     * @param string $username
     * @param string $passwd
     * @param string $where the LDAP "domain" to look in
     * @return string|bool - string if successful, FALSE if not
     */
    public function tryLogin($username, $passwd) {
        // Check for blank password - there's a bug in LDAP that makes it accept '' as valid.
        if ($passwd == '') {
            return FALSE;
        }
        // Get the user domain-name - return FALSE if its not there
        $dn = $this->checkUser ( $username );
        if (! $dn) {
            return FALSE;
        }
        // Now try to "login" to LDAP with the domain-name and password
        $ldapconn = ldap_connect ( $this->ldapserver );
        $ldapbind = @ldap_bind ( $ldapconn, $dn, $passwd );
        if (! $ldapbind) {
            return FALSE;
        }
        // If the login succeeded we can get the info.
        $data = $this->getInfo ( $ldapconn, $username );
        ldap_close ( $ldapconn );

        return $data; // send an array of the results
    }

    /**
     * method to get a user's info from LDAP
     * @author James Scoble
     * @param string $username
     * @param dbasehandle $ldapconn
     * @param string $where the LDAP "domain" to look in
     * @return string|bool - string if successful, FALSE if not
     */
    public function getInfo($ldapconn, $username) {
        $filter = 'cn=' . $username;
        $look = array ('surname', 'givenname', 'mail', $this->ldapuservarname );
        $find = ldap_search ( $ldapconn, $this->ldapWhere, $filter, $look );
        $data = ldap_get_entries ( $ldapconn, $find );
        $results ['username'] = $username;
        $results ['surname'] = $data [0] ['surname'] [0];
        $results ['firstname'] = $data [0] ['givenname'] [0];
        $results ['emailaddress'] = $data [0] ['mail'] [0];
        if (isset ( $data [0] [$this->ldapuservarname] [0] ) && is_numeric ( $data [0] [$this->ldapuservarname] [0] )) {
            $usernumber = $data [0] [$this->ldapuservarname] [0];
        } else {
            $usernumber = FALSE;
        }
        // Check for existing account
        // if the final value is FALSE, a value will be auto-genned later on.
        $localId = $this->objUser->getUserId ( $username );
        if ($localId != FALSE) {
            $results ['userid'] = $localId;
        } else {
            $results ['userid'] = $usernumber;
        }

        $results ['title'] = '';
        $results ['logins'] = '0';
        $results ['password'] = '--LDAP--';
        if (! empty ( $results ) || ! is_bool ( $results ['userid'] )) {
            // send an array of the results
            return $results;
        } else {
            return FALSE;
        }
    }

    /**
     * method to check if a user is an Academic
     * @author James Scoble
     * @param string $username
     * @param string $where the LDAP "domain" to look in
     * @returns TRUE|FALSE
     * @Todo this needs to be made generic ---
     */
    public function isAcademic($username, $where = "ou=ACADEMIC") //,{$this->ldapWhere}")
{
        $test = $this->checkUser ( $username, $where );
        if (! $test) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

}
?>
