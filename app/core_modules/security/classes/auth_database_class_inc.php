<?php
/* -------------------- IFAUTH INTERFACE CLASS ----------------*/

/**
*
* Plugin authenticatoin class to authenticate a user via a database
*
* @author Derek Keats, James Scoble
* @category Chisimba
* @package security
* @copyright AVOIR
* @licence GNU/GPL
*
*/

$this->loadClass("abauth", "security");
$this->loadClass("ifauth", "security");

class auth_database extends abauth implements ifauth
{
    /**
    *
    * Init method. It sets up a connection to the users database table
    * and instantiates required objects.
    *
    */
    public function init()
    {
        parent::init('tbl_users');
    }

    /**
    *
    * Method to authenticate the user via the database
    * @param string $username The username supplied in the login
    * @param string $password The password supplied in the login
    * @return TRUE|FALSE Boolean indication of success of login
    */
    public function authenticate($username, $password)
    {
        $auth = $this->objLu->login($username, $password, true);

        //Retrieve the users data from the database
        $line=$this->getUserDataAsArray($username);
        // set the line as a stdClass, serialize and store in session to lower db calls
        $user = new stdClass();
        // add the user info to the class
        $user->username = $line['username'];
        $user->userid = $line['userid'];
        $user->title = $line['title'];
        $user->firstname = $line['firstname'];
        $user->surname = $line['surname'];
        $user->pass = NULL;
        $user->creationdate = $line['creationdate'];
        $user->emailaddress = $line['emailaddress'];
        $user->logins = $line['logins'];
        $user->isactive = $line['isactive'];
        // serialize the object to preserve structure etc
        $user = serialize($user);
        // set it into session to be used elsewhere (objUser mainly)
        $this->setSession('userprincipal', $user);
        if ($line) {
            if ($line['isactive']=='0'){
                DEFINE('STATUS','inactive');
                return FALSE;
            }
            //LDAP will be handled in chain-of-command
            if ($line['pass']==sha1('--LDAP--')){
                return FALSE;
            } else {
                $password=sha1(trim($password));
                // if the login was successful
                if($this->objLu->isloggedIn() == TRUE) {
                //if ( strtolower($line['pass'])==strtolower($password) ) {
                    $this->_record = $line;
                    return TRUE;
                }
            }
        }
        return FALSE;
    }

    /**
    * Look up user's data in the database.
    * @param string $username
    * @return array on success, FALSE on failure.
    */
    public function getUserDataAsArray($username)
    {
        //echo $this->objLu->getProperty('handle'); die();
        //var_dump($this->objLu); die();
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
        if (!empty($array))
        {
            return $array[0];
        } else {
            return FALSE;
        }
    }

    public function getUserDataAsArray2($username)
    {
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
        if (!empty($array))
        {
            return $array[0];
        } else {
            return FALSE;
        }
    }

}
?>