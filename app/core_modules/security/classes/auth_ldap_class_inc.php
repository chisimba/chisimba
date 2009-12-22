<?php
/* -------------------- IFAUTH INTERFACE CLASS ----------------*/

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
    //these are arrays, to support multiple AD servers,
    private $ldapserver=array();
    private $ldapuservarname=array();
    private $ldapWhere=array();
    private $ldapPassword=array();
    private $ldapPort=array();
    private $ldapusernamevar=array();
    private  $currentIndex;

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
        //explode these, might be multiple values, separated by hash
        $this->ldapserver = explode("#", $this->objConfig->getValue ( 'MOD_SECURITY_LDAPSERVER', 'security' ));
        $this->ldapuservarname = explode("#",$this->objConfig->getValue ( 'MOD_SECURITY_LDAPUSERVARNAME', 'security' ));
        $this->ldapWhere =explode("#", $this->objConfig->getValue ( 'MOD_SECURITY_LDAPWHERE', 'security' ));
        $this->ldapPassword = explode("#",$this->objConfig->getValue ( 'MOD_SECURITY_LDAPPASSWORD', 'security' ));
        $this->ldapPort =explode("#", $this->objConfig->getValue ( 'MOD_SECURITY_LDAPPORT', 'security' ));
        $this->ldapusernamevar =explode("#", $this->objConfig->getValue ( 'MOD_SECURITY_LDAPUSERNAMEVAR', 'security' ));

        parent::init ( 'tbl_users' );
    }

    /**
     *
     * Method to authenticate the user via DAP
     * @param string $username The username supplied in the login
     * @param string $password The password supplied in the login
     * @return TRUE|FALSE Boolean indication of success of login
     */
    public function authenticate($username, $password) {
        // Check for blank password - there's a bug in LDAP that makes it accept '' as valid.
        if ($password == '') {
            return FALSE;
        }
        // Get the user domain-name - return FALSE if its not there
        $dn = $this->checkUser ( $username );
        //No dn found there no such user on the system.
        if (! $dn) {
            return FALSE;
        }
        // Now try to "login" to LDAP with the domain-name and password
        $ldapconn = ldap_connect ( $this->ldapserver[$this->currentIndex] );
        ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ldapconn,LDAP_OPT_PROTOCOL_VERSION,3);


        $ldapbind = @ldap_bind ( $ldapconn, $dn, $password );

        if (! $ldapbind) {
            ldap_close ( $ldapconn );
            return FALSE;
        } else {
            // If the login succeeded we can get the info.
            $this->createUser ( $username );
            $this->_record=$this->getLocalUserInfoAsArray($username);

            ldap_close ( $ldapconn );
            $this->setActive($username);
            $login = $this->objLu->login($username, '--LDAP--', $remember);
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

    public function setActive($userId) {
        $sql="UPDATE tbl_users  SET  isActive = '1' WHERE  username='".$userId."'";
        $this->query($sql);
    }


    public function getUserDataAsArray($username) {
        return $this->_record;
    }


    /**
     * Look up user's data in the database.
     * @param string $username
     * @return array on success, FALSE on failure.
     */
    public function getLocalUserInfoAsArray($username) {
        /*$array = array();
        $array['username'] = $this->objLu->getProperty('handle');
        $array['userid'] = $this->objLu->getProperty('auth_user_id');
        $array['isactive'] = $this->objLu->getProperty('is_active');
        $array['emailaddress'] = $this->objLu->getProperty('email');
        $array['sex'] = $this->objLu->getProperty('sex');

        var_dump($array); die();*/

        $sql="SELECT
            tbl_users.username,
            tbl_users.userid,
            tbl_users.title,
            tbl_users.firstname,
            tbl_users.surname,
            tbl_users.pass,
            tbl_users.creationdate,
            tbl_users.emailaddress,
            tbl_users.logins,
            tbl_users.isactive,
            tbl_users.accesslevel
            FROM
            tbl_users
            WHERE
            (username = '".addslashes($username)."')";
        $array=$this->getArray($sql);
        //var_dump($array[0]); die();
        if (!empty($array)) {
            return $array[0];
        } else {
            return FALSE;
        }
    }




    /**
     *
     *
     */
    function createUser($username) {
        $data = $this->objUser->lookupData ( $username );
        $info = $this->getUserDataAsArray ( $username );

        if (is_array ( $data ) || $this->objUser->valueExists ( 'userid', $info ['userid'] )) // if we already have this user
        {
            return TRUE;
        } else { // new user

            //Getting extra details from LDAP
            $ldapData = $this->getUserLdapDetails($username);
            // Build up an array of the user's info
            if ($info ['userid'] == FALSE) {
                $info ['userid'] = mt_rand ( 1000, 9999 ) . date ( 'ymd' );
            } else {

                $info [$this->ldapusernamevar[$this->currentIndex]] = $username;

            }


            $info ['staffnumber'] = $username;
            $info ['username'] = $username;
            $info ['userId'] = $info ['userid'];
            $info ['sex'] = '';
            $info ['cellnumber'] = '';
            $info ['accessLevel'] = 'guests';
            $info ['howCreated'] = 'LDAP';
            $info ['isactive'] = '1';

            if (isset($ldapData['sn'][0])) {
                $info ['surname'] = $ldapData['sn'][0];
            }

            if (isset($ldapData['title'][0])) {
                $info ['title'] = $ldapData['title'][0];
            }

            if (isset($ldapData['givenname'][0])) {
                $info ['firstname'] = $ldapData['givenname'][0];
            }
            if (isset($ldapData['mail'][0])) {
                $info ['email'] = $ldapData['mail'][0];
            }


            log_debug(var_export($ldapData['title'], true));
            log_debug(var_export($ldapData['sn'],true));
            log_debug(var_export($ldapData['givenname'],true));

            $objConf2 = $this->getObject ( 'altconfig', 'config' );
            $info ['country'] = $objConf2->getCountry ();

            // Instantiate the sqlusers class and call the adduser() function
            // To create the new user on the KNG system.
            //$tbl = $this->newObject ( 'sqlusers', 'security' );
            //$id = $tbl->addUser ( $info );
            $objUserAdmin = $this->getObject('useradmin_model2', 'security');
            $objUserAdmin->addUser($info['userid'], $info['username'], '--LDAP--',$info['title'], $info['firstname'], $info['surname'], $info['email'], $info['sex'], $info['country'], $info['cellnumber'], $info['staffnumber'], 'ldap', '1');
            // If LDAP confirms the user is an Academic,
            // add as a site-lecturer in KNG groups.
            if ($this->isAcademic ( $username )) {
                $this->objUser->addLecturer ( $id );
            }

            return TRUE;
        }

    }


    /**
     * method to contact the ldap server and see if a given username is valid there
     * @author James Scoble
     * @param string $username
     * @param string $where the LDAP "domain" to look in
     * erLdapDetailsreturn string|bool - string if successful, FALSE if not
     */
    public function getUserLdapDetails($username) {

        $ldapconn = ldap_connect ('ldap://'.$this->ldapserver[$this->currentIndex], $this->ldapPort[$this->currentIndex]) or die ('LDAP CONNECTION FAILED');

        ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($ldapconn,LDAP_OPT_PROTOCOL_VERSION,3);

        $ldapbind = ldap_bind ( $ldapconn, $this->ldapuservarname[$this->currentIndex],$this->ldapPassword[$this->currentIndex] );


        $where = $this->ldapWhere[$this->currentIndex];
        if (! $ldapbind) {
            $this->setSession ( 'ldaperror', 'FAIL' );
            return FALSE;
        }
        $filter = '(CN=' . $username.')';
        //$filter = '(sn=*)';
        $look = array ('DN','CN','SN','TITLE','GIVENNAME','MAIL' );
        $find = ldap_search ( $ldapconn, $where, $filter, $look );
        $data = ldap_get_entries ( $ldapconn, $find );
        ldap_close ( $ldapconn );
        if ($data ['count'] > 0) {
            return $data [0];
        } else {
            return FALSE;
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
        $index=0;
        //loop through till you get server to connect ti
        foreach($this->ldapserver as $server) {

            $ldapconn = ldap_connect('ldap://'.$server, $this->ldapPort[$index]) or die ('LDAP CONNECTION FAILED');

            ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($ldapconn,LDAP_OPT_PROTOCOL_VERSION,3);


            $ldapbind = ldap_bind ( $ldapconn, $this->ldapuservarname[$index],$this->ldapPassword[$index] );

            $where = $this->ldapWhere[$index];

            if (! $ldapbind) {
                $this->setSession ( 'ldaperror', 'FAIL' );
                //$returnValue= FALSE;
            }
            $filter = '(CN=' . $username.')';
            $look = array ('DN' );
            $find = ldap_search ( $ldapconn, $where, $filter, $look );
            if (!$find) {

                // echo "Couldn't Find Any Matching AD Object";
                // exit;

                /***
                 * NOTE: possibley source of untraceable bug. May be log this somewhere?
                 */
                $returnValue=FALSE;
            }

            $data = ldap_get_entries ( $ldapconn, $find );
            ldap_close ( $ldapconn );

            if ($data ['count'] > 0) {
                //set this global for later use
                $this->currentIndex=$index;
                return  $data [0] ['dn'];
            }
            $index++;
        }
        return $returnValue;
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
        foreach($this->ldapserver as $server) {
            // Now try to "login" to LDAP with the domain-name and password
            $ldapconn = ldap_connect ( $server );
            ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($ldapconn,LDAP_OPT_PROTOCOL_VERSION,3);


            $ldapbind = @ldap_bind ( $ldapconn, $dn, $passwd );
            if ($ldapbind) {

                // If the login succeeded we can get the info.
                $data = $this->getInfo ( $ldapconn, $username );
                ldap_close ( $ldapconn );

                return $data; // send an array of the results
            }
        }

        return FALSE;
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
        foreach($this->ldapuservarname as $servername) {
            $look = array ('surname', 'givenname', 'mail', $ldapuservarname );
            $find = ldap_search ( $ldapconn, $this->ldapWhere, $filter, $look );
            $data = ldap_get_entries ( $ldapconn, $find );
            $results ['username'] = $username;
            $results ['surname'] = $data [0] ['surname'] [0];
            $results ['firstname'] = $data [0] ['givenname'] [0];
            $results ['emailaddress'] = $data [0] ['mail'] [0];
            if (isset ( $data [0] [$ldapuservarname] [0] ) && is_numeric ( $data [0] [$ldapuservarname] [0] )) {
                $usernumber = $data [0] [$ldapuservarname] [0];
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
            }
        }
        return FALSE;
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