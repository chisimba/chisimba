<?php
define('BASE', '../');
require_once(BASE.'functions/admin_functions.php');
require_once(BASE.'functions/ical_parser.php');
require_once(BASE.'functions/template.php');
header("Content-Type: text/html; charset=$charset");

if (empty($default_path)) {
	if (isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) == 'on' ) {
		$default_path = 'https://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],'/admin/'));
	} else {
		$default_path = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],'/admin/'));
	}
}
if ($allow_admin != 'yes') {
	exit(error('The administration menu has been turned off.', $cal, '../'));
}

// Load variables from forms and query strings into local scope
if($_POST) 	{extract($_POST, EXTR_PREFIX_SAME, "post_");}
if($_GET)  	{extract($_GET, EXTR_PREFIX_SAME, "get_");}

// Logout by clearing session variables
if ((isset($_GET['action'])) && ($_GET['action'] == 'logout')) {
	$_SESSION['phpical_loggedin'] = FALSE;
	unset($_SESSION['phpical_username']);
	unset($_SESSION['phpical_password']);
}


// if $auth_method == 'none', don't do any authentication
$username = $_POST['username'];
$password = $_POST['password'];

if ($auth_method == 'none') {
	$is_loged_in = TRUE;
} else {
	$is_loged_in = FALSE;
	
	if (is_loggedin()) {
		$is_loged_in = TRUE;
	}
	
	if (isset($username) && $_GET['action'] != 'logout') {
		$is_loged_in = TRUE;login ($username, $password);
	}
}
//die('here');
$login_good = ($is_loged_in) ? '' : 'oops';
$login_bad	= ((!$is_loged_in) && ($_GET['action'] == 'login')) ? 'oops' : '';

// Delete a calendar
// Not at all secure - need to strip out path info if used by users besides admin in the future
$delete_msg = '';
if ($_POST['action'] == 'delete') {
	foreach ($delete_calendar as $filename) {
		if (!delete_cal(urldecode($filename))) {
			$delete_msg = $delete_msg . '<font color="red">' . $lang['l_delete_error'] . ' ' . urldecode(substr($filename,0,-4)) . '</font><br />';
		} else {
			$delete_msg = $delete_msg . '<font color="green">' . urldecode(substr($filename,0,-4)) . ' ' . $lang['l_delete_success'] . '</font><br />';
		}
	}
}

// Add or Update a calendar
$addupdate_msg 	= '';
if ((isset($_POST['action']))  && ($_POST['action'] == 'addupdate')) {
	for ($filenumber = 1; $filenumber < 6; $filenumber++) {
		$file = $_FILES['calfile'];
		$addupdate_success = FALSE;

		if (!is_uploaded_file_v4($file['tmp_name'][$filenumber])) {
			$upload_error = get_upload_error($file['error'][$filenumber]);
		} elseif (!is_uploaded_ics($file['name'][$filenumber])) {
			$upload_error = $upload_error_type_lang;
		} elseif (!copy_cal($file['tmp_name'][$filenumber], $file['name'][$filenumber])) {
			$upload_error = $copy_error_lang . " " . $file['tmp_name'][$filenumber] . " - " . $calendar_path . "/" . $file['name'][$filenumber];
		} else {
			$addupdate_success = TRUE;
		}
		
		if ($addupdate_success == TRUE) {
			$addupdate_msg = $addupdate_msg . '<font color="green">'.$lang['l_cal_file'].' #'.$filenumber.': '.$lang['l_action_success'].'</font><br />';
		} else {
			$addupdate_msg = $addupdate_msg . '<font color="red">'.$lang['l_cal_file'].' #'.$filenumber.': '.$lang['l_upload_error'].'</font><br />';
		}
	}
}

$calendar_name = $lang['l_admin_header'];

$page = new Page(BASE.'templates/'.$template.'/admin.tpl');

$page->replace_files(array(
	'header'			=> BASE.'templates/'.$template.'/header.tpl',
	'footer'			=> BASE.'templates/'.$template.'/footer.tpl'
	));

$page->replace_tags(array(
	'event_js'			=> '',
	'charset'			=> $charset,
	'default_path'		=> $default_path.'/',
	'template'			=> $template,
	'cal'				=> $cal,
	'getdate'			=> $getdate,
	'calendar_name'		=> $calendar_name,
	'display_date'		=> $display_date,
	'current_view'		=> $current_view,
	'sidebar_date'		=> $sidebar_date,
	'rss_powered'	 	=> $rss_powered,
	'rss_available' 	=> '',
	'rss_valid' 		=> '',
	'show_search' 		=> '',
	'login_error'		=> $login_bad,
	'display_login'		=> $login_good,
	'delete_msg'		=> $delete_msg,
	'addupdate_msg'	=> $addupdate_msg,
	'l_day'				=> $lang['l_day'],
	'l_week'			=> $lang['l_week'],
	'l_month'			=> $lang['l_month'],
	'l_year'			=> $lang['l_year'],
	'l_admin_header'	=> $lang['l_admin_header'],
	'l_admin_subhead'	=> $lang['l_admin_subhead'],
	'l_invalid_login'	=> $lang['l_invalid_login'],
	'l_username'		=> $lang['l_username'],
	'l_password'		=> $lang['l_password'],
	'l_cal_file'		=> $lang['l_cal_file'],
	'l_delete_cal'		=> $lang['l_delete_cal'],
	'l_delete'			=> $lang['l_delete'],
	'l_logout'			=> $lang['l_logout'],
	'l_login'			=> $lang['l_login'],
	'l_submit'			=> $lang['l_submit'],
	'l_addupdate_cal'	=> $lang['l_addupdate_cal'],
	'l_addupdate_desc'	=> $lang['l_addupdate_desc'],
	'l_powered_by'		=> $lang['l_powered_by'],
	'l_this_site_is'	=> $lang['l_this_site_is']			
	));

$page->draw_admin();
$page->output();

?>
