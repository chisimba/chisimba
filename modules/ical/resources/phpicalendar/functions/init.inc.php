<?php 
$phpicalendar_version = '2.22';
// uncomment when developing, comment for shipping version
error_reporting (E_ERROR | E_WARNING | E_PARSE);
#error_reporting(0);
// Older versions of PHP do not define $_SERVER. Define it here instead.
if (!isset($_SERVER) && isset($HTTP_SERVER_VARS)) {
	$_SERVER = &$HTTP_SERVER_VARS;
}
#=================Initialize global variables=================================
// Define some magic strings.
$ALL_CALENDARS_COMBINED = 'all_calendars_combined971';

// Pull in the configuration and some functions.
if (!defined('BASE')) define('BASE', './');
include_once(BASE.'config.inc.php');

$cookie_name = 'phpicalendar_'.basename($default_path);
if (isset($_COOKIE["$cookie_name"]) && !isset($_POST['unset'])) {
	$phpicalendar = unserialize(stripslashes($_COOKIE[$cookie_name]));
	if (isset($phpicalendar['cookie_language'])) 	$language 			= $phpicalendar['cookie_language'];
	if (isset($phpicalendar['cookie_calendar'])) 	$default_cal_check	= $phpicalendar['cookie_calendar'];
	if (isset($phpicalendar['cookie_cpath'])) 		$default_cpath_check= $phpicalendar['cookie_cpath'];
	if (isset($phpicalendar['cookie_view'])) 		$default_view 		= $phpicalendar['cookie_view'];
	if (isset($phpicalendar['cookie_style']) && is_dir(BASE.'templates/'.$phpicalendar['cookie_style'].'/')){ 
		$template 			= $phpicalendar['cookie_style'];
	}	
	if (isset($phpicalendar['cookie_startday'])) 	$week_start_day		= $phpicalendar['cookie_startday'];
	if (isset($phpicalendar['cookie_time']))		$day_start			= $phpicalendar['cookie_time'];
}
#cpath modifies the calendar path based on the url or cookie values.  This allows you to run multiple calendar subsets from a single phpicalendar installation. Operations on cpath are largely hidden from the end user.
if ($calendar_path == '') {
	$calendar_path = BASE.'calendars';
}
$cpath = ''; #initialize cpath to prevent later undef warnings.
if(isset($_REQUEST['cpath'])&& $_REQUEST['cpath'] !=''){
	$cpath 	= str_replace('..','',$_REQUEST['cpath']);				
	$calendar_path 	.= "/$cpath";				
	$tmp_dir 	.= "/$cpath";				
}elseif(isset($default_cpath_check) && $default_cpath_check !='' ){
	$cpath 	= str_replace('..','',$default_cpath_check);				
	$calendar_path 	.= "/$cpath";				
	$tmp_dir 	.= "/$cpath";
}
#these need cpath to be set
#set up specific template folder for a particular cpath
if (isset($user_template["$cpath"])){ 
  $template = $user_template["$cpath"]; 
}
#set up specific webcals for a particular cpath
if (is_array($more_webcals[$cpath])){
	array_merge($list_webcals, $more_webcals["$cpath"]);
}
include_once(BASE.'error.php');
include_once(BASE.'functions/calendar_functions.php');
include_once(BASE.'functions/userauth_functions.php');


// Set the cookie URI.
if ($cookie_uri == '') {
	$cookie_uri = $_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'], '/'));
}

if ($bleed_time == '') $bleed_time = -1;

// Grab the action (login or logout).
if (isset($_GET['action']))			$action = $_GET['action'];
else if (isset($_POST['action']))		$action = $_POST['action'];
else											$action = '';
	
// Login and/or logout.
list($username, $password, $invalid_login) = user_login();
if ($action != 'login') $invalid_login = false;
if ($action == 'logout' || $invalid_login) {
	list($username, $password) = user_logout();
}

// language support
$language = strtolower($language);
$lang_file = BASE.'languages/'.$language.'.inc.php';

unset($lang); #$lang is array of phrases in appropriate language
if (is_file($lang_file)) {
	include($lang_file);
} else {
	exit(error('The requested language "'.$language.'" is not a supported language. Please use the configuration file to choose a supported language.'));
}

if (!isset($getdate)) {
	if (isset($_GET['getdate']) && ($_GET['getdate'] !== '')) {
		$getdate = $_GET['getdate'];
	} else {
		$getdate = date('Ymd', time() + $second_offset);
	}
}

if (ini_get('max_execution_time') < 60) {
	@ini_set('max_execution_time', '60');
}

// Pull the calendars off the GET line if provided. The $cal_filename
// is always an array, because this makes it easier to deal with below.
$cal_filenames = array();
if (isset($_GET['cal'])) {
	// If the cal value is not an array, split it into an array on
	// commas.
	if (!is_array($_GET['cal']))
		$_GET['cal'] = explode(',', $_GET['cal']);
	
	// Grab the calendar filenames off the cal value array.
	$cal_filenames = $_GET['cal'];
} else {
	if (isset($default_cal_check)) {
		if ($default_cal_check != $ALL_CALENDARS_COMBINED) {
			$calcheck = $calendar_path.'/'.$default_cal_check.'.ics';
			$calcheckopen = @fopen($calcheck, "r");
			if ($calcheckopen == FALSE) {
				$cal_filenames[0] = $default_cal;
			} else {
				$cal_filenames[0] = $default_cal_check;
			}
		} else {
			$cal_filenames[0] = $ALL_CALENDARS_COMBINED;
		}
	} else {
		$cal_filenames[0] = $default_cal;
	}
}
//load cal_filenames if $ALL_CALENDARS_COMBINED
if ($cal_filenames[0] == $ALL_CALENDARS_COMBINED){
	$cal_filenames = availableCalendars($username, $password, $ALL_CALENDARS_COMBINED);
}
// Separate the calendar identifiers into web calendars and local
// calendars.
$web_cals = array();
$local_cals = array();
foreach ($cal_filenames as $cal_filename) {
	// If the calendar identifier begins with a web protocol, this is a web
	// calendar.
	$cal_filename = urldecode($cal_filename); #need to decode for substr statements to identify webcals
	$cal_filename = str_replace(' ','%20', $cal_filename); #need to reencode blank spaces for matching with $list_webcals
	if (substr($cal_filename, 0, 7) == 'http://' ||
		substr($cal_filename, 0, 8) == 'https://' ||
		substr($cal_filename, 0, 9) == 'webcal://')
	{
		$web_cals[] = $cal_filename;
	}
	
	// Otherwise it is a local calendar.
	else {
		// Check blacklisted.
		if (in_array($cal_filename, $blacklisted_cals)  && $cal_filename !='') {
			exit(error($lang['l_error_restrictedcal'], $cal_filename));
		}
		$local_cals[] = urldecode(str_replace(".ics", '', basename($cal_filename)));
	}
}

// We will build the calendar display name as we go. The final display
// name will get constructed at the end.
$cal_displaynames = array();

// This is our list of final calendars.
$cal_filelist = array();

// This is our list of URL encoded calendars.
$cals = array();

// Process the web calendars.
foreach ($web_cals as $web_cal) {
	// Make some protocol alternatives, and set our real identifier to the
	// HTTP protocol.
	$cal_webcalPrefix = str_replace('http://','webcal://',$web_cal);
	$cal_httpPrefix = str_replace('webcal://','http://',$web_cal);
	$cal_httpsPrefix = str_replace('webcal://','https://',$web_cal);
	$cal_httpsPrefix = str_replace('http://','https://',$web_cal);
	$web_cal = $cal_httpPrefix;
		
	// We can only include this web calendar if we allow all web calendars
	// (as defined by $allow_webcals) or if the web calendar shows up in the
	// list of web calendars defined in config.inc.php.
	if ($allow_webcals != 'yes' &&
		!in_array($cal_webcalPrefix, $list_webcals) &&
		!in_array($cal_httpPrefix, $list_webcals) &&
		!in_array($cal_httpsPrefix, $list_webcals))
	{
		exit(error($lang['l_error_remotecal'], $web_cal));
	}
	
	// Pull the display name off the URL.
	$cal_displaynames[] = substr(str_replace('32', ' ', basename($web_cal)), 0, -4);
	
	// FIXME
	$cals[] = urlencode($web_cal);
	//$filename = $cal_filename;
	$subscribe_path = $cal_webcalPrefix;
	
	// Add the webcal to the available calendars.
	$cal_filelist[] = $web_cal;
}

// Process the local calendars.
if (count($local_cals) > 0) {
	$local_cals = availableCalendars($username, $password, $local_cals);
	foreach ($local_cals as $local_cal) {
		$cal_displaynames[] = str_replace('32', ' ', getCalendarName($local_cal));
	}
	$cal_filelist = array_merge($cal_filelist, $local_cals);
	$cals = array_merge($cals, array_map("urlencode", array_map("getCalendarName", $local_cals)));
	
	// Set the download and subscribe paths from the config, if there is
	// only one calendar being displayed and those paths are defined.
	if (count($local_cals) == 1) {
		$filename = $local_cals[0];
		if (($download_uri == '') && (preg_match('/(^\/|\.\.\/)/', $filename) == 0)) {
			$subscribe_path = 'webcal://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']).'/'."$cpath/".$filename;
			$download_filename = $filename;
		} elseif ($download_uri != '') {
			$newurl = eregi_replace("^(http://)", "", $download_uri); 
				$subscribe_path = 'webcal://'.$newurl.'/'."$cpath/".basename($filename);
				$download_filename = $download_uri.'/'."$cpath/".basename($filename);
		} else {
			$subscribe_path = "$cpath/";
			$download_filename = "$cpath/";
		}
	}
}

// We should only allow a download filename and subscribe path if there is
// only one calendar being displayed.
if (count($cal_filelist) > 1) {
	$subscribe_path = '';
	$download_filename = '';
}

// Build the final cal list. This is a comma separated list of the
// url-encoded calendar names and web calendar URLs.
$cal = implode(',', $cals);

// Build the final display name used for template substitution.
asort($cal_displaynames);
$cal_displayname = implode(', ', $cal_displaynames);

$rss_powered = ($enable_rss == 'yes') ? 'yes' : '';

function getmicrotime() { 
	list($usec, $sec) = explode(' ',microtime()); 
	return ((float)$usec + (float)$sec); 
}
#uncomment for diagnostics
#echo "after init.inc.ics<pre>";
#echo "cals";
#print_r($cals);echo"\n\n";
#echo "cal_filenames";
#print_r($cal_filenames);echo"\n\n";
#echo "web_cals";
#print_r($web_cals);echo"\n\n";
#echo "local_cals";
#print_r($local_cals);echo"\n\n";
#echo "cal_filelist";
#print_r($cal_filelist);
#echo "cal_displaynames";
#print_r($cal_displaynames);
#echo "</pre><hr>";

?>
