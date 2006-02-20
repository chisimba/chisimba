<?php
/* -------------------- dbConfig class ----------------*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* The purpose of the kngDbConfig class is to return the configuration
* properties for the database. By abstracting the configuration
* properties into a class we have the opportunity to change the
* configuration method later in the project, for example from
* php config files to XML
*/

require_once "config/config_db_inc.php";

class dbConfig {

	//publics for the database
	public $_dbDriver=NULL;
	public $_dbServer=NULL;
	public $_dbUser=NULL;
	public $_dbPassword=NULL;
	public $_dbDatabase=NULL;
	public $_dbConString=NULL;

	//publics for the read only database
	public $_rodbDriver=NULL;
	public $_rodbServer=NULL;
	public $_rodbUser=NULL;
	public $_rodbPassword=NULL;
	public $_rodbDatabase=NULL;
	public $_rodbConString=NULL;

	//publics for the LDAP database
	public $_ldapDriver=NULL;
	public $_ldapServer=NULL;
	public $_ldapUser=NULL;
	public $_ldapPort=NULL;
	public $_ldapO=NULL;
	public $_ldapPassword=NULL;
	public $_ldapDatabase=NULL;
	public $_ldapConString=NULL;

	/*----------- SET OF PRPOERTIES FOR MAIN DATABASE -------- */

	/**
	* Property to return the database driver
	*/
	public function dbDriver() {
		return KEWL_DB_DRIVER;
	}

	/**
	* Property to return the database server
	*/
	public function dbServer() {
		return KEWL_DB_SERVER;
	}

	/**
	* Property to return the database user
	*/
	public function dbUser() {
		return KEWL_DB_USER;
	}

	/**
	* Property to return the database password
	*/
	public function dbPassword() {
		return KEWL_DB_PASSWORD;
	}

	/**
	* Property to return the database name
	*/
	public function dbDatabase() {
		return KEWL_DB_DATABASE;
	}

	/**
	* Property to return the database connection string
	*/
	public function dbConString() {
		$ret=$this->dbDriver()."://".$this->dbUser().":".$this->dbPassword()."@".$this->dbServer()."/".$this->dbDatabase();
		return $ret;
	}


	/*----------- SET OF PRPOERTIES FOR READ ONLY DATABASE -------- */

	/**
	* Property to return the read only database driver
	*/
	public function rodbDriver() {
		return KEWL_RODB_DRIVER;
	}

	/**
	* Property to return the read only database server
	*/
	public function rodbServer() {
		return KEWL_RODB_SERVER;
	}

	/**
	* Property to return the read only database user
	*/
	public function rodbUser() {
		return KEWL_RODB_USER;
	}

	/**
	* Property to return the read only database password
	*/
	public function rodbPassword() {
		return KEWL_RODB_PASSWORD;
	}

	/**
	* Property to return the read only database name
	*/
	public function rodbDatabase() {
		return KEWL_RODB_DATABASE;
	}

	/**
	* Property to return the read only database connection string
	*/
	public function rodbConString() {
		$ret=$this->rodbDriver()."://".$this->rodbUser().":".$this->rodbPassword()."@".$this->rodbServer()."/".$this->rodbDatabase();
		return $ret;
	}



	/*----------- SET OF PRPOERTIES FOR LDAP CONNECTION -------- */

	/**
	* Property to return the LDAP driver
	*/
	public function ldapDriver() {
		return KEWL_LDAP_DRIVER;
	}

	/**
	* Property to return the LDAP database server
	*/
	public function ldapServer() {
		return KEWL_LDAP_SERVER;
	}

	/**
	* Property to return the LDAP database user
	*/
	public function ldapUser() {
		return KEWL_LDAP_USER;
	}

	/**
	* Property to return the LDAP database password
	*/
	public function ldapPassword() {
		return KEWL_LDAP_PASSWORD;
	}

	/**
	* Property to return the LDAP database name
	*/
	public function ldapDatabase() {
		return KEWL_LDAP_DATABASE;
	}

	/**
	* Property to return the LDAP port
	*/
	public function ldapPort() {
		return KEWL_LDAP_PORT;
	}

	/**
	* Property to return the LDAP o
	*/
	public function ldapO() {
		return KEWL_LDAP_O;
	}

	/**
	* Property to return the read only database connection string
	* >>>>>>>>>note this is incomplete and will not work; read more on PEAR LDAP
	*/
	public function ldapConString() {
		$ret=$this->ldapDriver()."://".$this->ldapUser().":".$this->ldapPassword()."@".$this->rodbServer().":".$this->ldapPort()."/".$this->ldapDatabase();
		return $ret;
	}


    /**---------------- MIRRORING PROPERTIES -----------**/

    /**
     * Return's server name (used for dynamic mirroring)
     */
    public function serverName()
    {
        if (defined('KEWL_SERVERNAME')){
            return KEWL_SERVERNAME;
        } else {
            return 'default';
        }
    }

    /**
     * Returns mirror webservice WSDL URL (in production will usually be a service
     * on a non-standard port on the localhost)
     * @return string WSDL URL
     */
    public function mirrorWsdlUrl()
    {
        if (defined('KEWL_MIRROR_WSDL_URL')) {
            return KEWL_MIRROR_WSDL_URL;
        } else {
            return NULL;
        }
    }

} # end of kngDbConfig class

?>
