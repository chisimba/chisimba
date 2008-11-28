<?php
/**
 * Plugin authenticatoin class to authenticate a user via FileMakerPro9 and above
 *
 * @author Paul Scott <pscott@uwc.ac.za>
 * @category Chisimba
 * @package security
 * @copyright AVOIR
 * @see filemakerpro
 */

$this->loadClass ( "abauth", "security" );
$this->loadClass ( "ifauth", "security" );

class auth_fmp extends abauth implements ifauth {
    /**
     *
     * Init method. It sets up the LDAP connection parameters
     * and instantiates required objects.
     *
     */
    public function init() {
        //Instantiate the configuration object
        $this->objConfig = $this->getObject ( 'dbsysconfig', 'sysconfig' );
        // User object
        $this->objUser = $this->getObject ( 'user', 'security' );
        // The FMP object
        $this->objFMPro = $this->getObject ( 'fmpro', 'filemakerpro' );
        // Have to call the parent to init the class properties for sessions
        parent::init ( 'tbl_users' );
    }

    public function authenticate($username, $password) {
        $auth = $this->objFMPro->simpleAuth ( $username, $password );
        if ( $auth != FALSE) {
            $this->_record = $this->objFMPro->getUserInfo();
            $this->createUser($username);
            return TRUE;
        }
    }

    public function getUserDataAsArray($username) {
        return $this->_record;
    }

    public function createUser($username) {
        $data = $this->objUser->lookupData($username);
        $info = $this->getUserDataAsArray($username);
        if (is_array($data) || $this->objUser->valueExists('userid',$info['userid']))
        {
            return TRUE;
        } else {
            // Build up an array of the user's info
            if ($info['userid'] == FALSE)
            {
                $info['userid'] = mt_rand(1000,9999).date('ymd');
            } else {
                $info['staffnumber'] = $info['userid'];
            }
            $info['userId'] = $info['userid'];
            $info['sex'] = '';
            $info['accessLevel'] = 'guests';
            $info['howCreated'] = 'FMP';
            $info['isactive'] = '1';

            $objConf2 = $this->getObject('altconfig','config');
            $info['country'] = $objConf2->getCountry();
            // Instantiate the sqlusers class and call the adduser() function
            // To create the new user on the system.
            $tbl = $this->newObject('sqlusers','security');
            $id = $tbl->addUser($info);
            // If LDAP confirms the user is an Academic,
            // add as a site-lecturer in KNG groups.
            if ($this->isAcademic($username)) {
                $this->objUser->addLecturer($id);
            }
        }
    }

    public function checkUser($username) {

    }

    public function tryLogin($username, $passwd) {

    }

    public function getInfo($ldapconn, $username) {

    }

    public function isAcademic($username) {
        // Not yet implemented!
        return FALSE;
    }
}


?>