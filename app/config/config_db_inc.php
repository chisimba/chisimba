<?PHP 
/** 
* Database configuration information for the main
* KEWL.NextGen database.
*/
// Server name needs to be unique within a mirroring cluster
define('KEWL_SERVERNAME', 'gen6Srv42Nme32');
// The main server ensures all server names are unique within a mirroring cluster
define('KEWL_MAINSERVERNAME', 'http://kngforge.uwc.ac.za/kng_unstable');
// The server name was generated
define('KEWL_SERVERNAME_GENERATED', 'TRUE');
define('KEWL_DB_DRIVER', 'mysql');
define('KEWL_DB_SERVER', 'localhost');
define('KEWL_DB_USER', 'root');
define('KEWL_DB_PASSWORD', '');
define('KEWL_DB_DATABASE', 'php5alpha');

/**  configuration information for readonly
* Use to provide the capability to have a second
* database for read only queries, eg. students reading
* content within a course
*/ 
define('KEWL_RODB_DRIVER', 'mysql');
define('KEWL_RODB_SERVER', 'somesite.somewhere.com');
define('KEWL_RODB_USER', 'kngro');
define('KEWL_RODB_PASSWORD', 'fully.tanked');
define('KEWL_RODB_DATABASE', 'dbkewl_readonly');

/**
* LDAP configuration
*/ 
$ldap_driver="ldap";
$ldap_ip="191.10.9.8";
$ldap_port="389";
$ldap_server=$ldap_ip.':'.$ldap_port;
$ldap_o="o=UWC";
$ldap_dbuser="";
$ldap_dbpassword="";
//$ldap_dbconstr = "$ldap_driver://$ldap_dbuser:$ldap_dbpassword@$ldap_server/$database";
?>
