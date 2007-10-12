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
        //Retrieve the users data from the database
        $line=$this->getUserDataAsArray($username);
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
                if ( strtolower($line['pass'])==strtolower($password) ) {
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
