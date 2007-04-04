<?php



/**

* 

* 

*/

// Check for older PHP versions - added 15 Dec 2006 by jsc
if (substr(PHP_VERSION,0,1) < '5'){
    print "Chisimba requires PHP 5 or better. We see this computer is currently running PHP".PHP_VERSION."<br />\n";
    print "To run Chisimba, you wil need to run PHP5.<br />\n";
    die;
}

if (substr(PHP_VERSION,0,2) > '5.1'){
    print "We see this computer is currently running PHP".PHP_VERSION."<br />\n";
    print "Chisimba currently has problems with PHP 5.2 or above.<br />\n";
    die;
}

/*
$path=substr(str_replace('\\','/',$_SERVER['SCRIPT_FILENAME']),0,-20)."/config/";
if (file_exists($path."installdone.txt") && file_exists($path."config.xml")&&file_exists($path."dbdetails_inc.php")){
    print "The installation has been done already.<br />Click <a href='";
    print "../index.php'>here</a> to enter the site.<br />\n";
    die;
}*/

require_once dirname(__FILE__).'/installwizard.inc';



if (!defined('PATH_SEPARATOR')) {

	define('PATH_SEPARATOR',(substr(PHP_OS, 0, 3) == 'WIN') ? ';' : ':');

}



// do some hax0ring to the global variables first

$_POST = install_gpc_stripslashes($_POST);

$_GET = install_gpc_stripslashes($_GET);

$_REQUEST = install_gpc_stripslashes($_REQUEST);



$wizard = new InstallWizard();



$wizard->run();





/**

* Strips the slashes from a variable if magic quotes is set for GPC

* Handle normal variables and arrays

*

* @param mixed $var	the var to cleanup

*

* @return mixed

* @access public

*/

function install_gpc_stripslashes($var)

{

	if (get_magic_quotes_gpc()) {

		if (is_array($var)) install_stripslashes_array($var, true);

		else $var = stripslashes($var);

	}

	return $var;



}//end install_gpc_stripslashes()





/**

* Strips the slashes from an entire associative array

*

* @param array		$array			the array to stripslash

* @param boolean	$strip_keys		whether or not to stripslash the keys as well

*

* @return array

* @access public

*/

function install_stripslashes_array(&$array, $strip_keys=false)

{

	if(is_string($array)) return stripslashes($array);

	$keys_to_replace = Array();

	foreach($array as $key => $value) {

		if (is_string($value)) {

			$array[$key] = stripslashes($value);

		} elseif (is_array($value)) {

			install_stripslashes_array($array[$key], $strip_keys);

		}



		if ($strip_keys && $key != ($stripped_key = stripslashes($key))) {

			$keys_to_replace[$key] = $stripped_key;

		}

	}

	// now replace any of the keys that needed strip slashing

	foreach($keys_to_replace as $from => $to) {

		$array[$to]   = &$array[$from];

		unset($array[$from]);

	}

	return $array;



}//end install_stripslashes_array()





?>

