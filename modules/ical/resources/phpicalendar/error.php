<?php
if (!defined('BASE')) define('BASE','./');
require_once(BASE.'functions/template.php');


function error($error_msg='There was an error processing the request.', $file='NONE', $error_base='./') {
	global $template, $language, $enable_rss, $lang, $charset, $default_path;
	if (!isset($template))					$template = 'default';
	if (!isset($lang['l_powered_by']))		$lang['l_powered_by'] = 'Powered by';
	if (!isset($lang['l_error_title']))		$lang['l_error_title'] = 'Error!';
	if (!isset($lang['l_error_window']))	$lang['l_error_window'] = 'There was an error!';
	if (!isset($lang['l_error_calendar']))	$lang['l_error_calendar'] = 'The calendar "%s" was being processed when this error occurred.';
	if (!isset($lang['l_error_back']))		$lang['l_error_back'] = 'Please use the "Back" button to return.';
	if (!isset($lang['l_this_site_is']))	$lang['l_this_site_is'] = 'This site is';
	if (!isset($enable_rss))				$enable_rss = 'no';
		
	$error_calendar 	= sprintf($lang['l_error_calendar'], $file);
	$current_view 		= 'error';
	$display_date 		= $lang['l_error_title'];
	$calendar_name 		= $lang['l_error_title'];
	
	if (empty($default_path)) {
		if (isset($_SERVER['HTTPS']) || strtolower($_SERVER['HTTPS']) == 'on' ) {
			$default_path = 'https://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],'/rss/'));
		} else {
			$default_path = 'http://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],'/rss/'));
		}
	}
	
	$page = new Page(BASE.'templates/'.$template.'/error.tpl');
	
	$page->replace_files(array(
	'header'			=> BASE.'templates/'.$template.'/header.tpl',
	'footer'			=> BASE.'templates/'.$template.'/footer.tpl',
	));

	$page->replace_tags(array(
		'version'			=> $phpicalendar_version,
		'default_path'		=> $default_path.'/',
		'template'			=> $template,
		'cal'				=> $cal,
		'getdate'			=> $getdate,
		'charset'			=> $charset,
		'calendar_name'		=> $calendar_name,
		'display_date'		=> $display_date,
		'rss_powered'	 	=> $rss_powered,
		'rss_available' 	=> '',
		'event_js'			=> '',
		'todo_js'			=> '',
		'todo_available' 	=> '',
		'rss_valid' 		=> '',
		'error_msg'	 		=> $error_msg,
		'error_calendar' 	=> $error_calendar,
		'generated'	 		=> $generated,
		'l_powered_by'		=> $lang['l_powered_by'],
		'l_error_back'		=> $lang['l_error_back'],
		'l_error_window'	=> $lang['l_error_window']
				
		));
		
	$page->output();

	

}

?>