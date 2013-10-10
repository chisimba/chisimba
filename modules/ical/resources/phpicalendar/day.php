<?php
if (isset($_GET['jumpto_day'])) {
	$jumpto_day_time = strtotime($_GET['jumpto_day']);
	if ($jumpto_day_time == -1) {
		$getdate = date('Ymd', time() + $second_offset); 
	} else {
		$getdate = date('Ymd', $jumpto_day_time);
	}
}
define('BASE', './');
$current_view = 'day';
require_once(BASE.'functions/ical_parser.php');
require_once(BASE.'functions/list_functions.php');
require_once(BASE.'functions/template.php');
header("Content-Type: text/html; charset=$charset");

if ($minical_view == 'current') $minical_view = 'day';

$weekstart 		= 1;
$unix_time 		= strtotime($getdate);
$today_today 	= date('Ymd', time() + $second_offset);  
$next_day		= date('Ymd', strtotime("+1 day",  $unix_time));
$prev_day 		= date('Ymd', strtotime("-1 day",  $unix_time));

$display_date = localizeDate($dateFormat_day, $unix_time);
$sidebar_date = localizeDate($dateFormat_week_list, $unix_time);
$start_week_time = strtotime(dateOfWeek($getdate, $week_start_day));


// select for calendars
$list_icals 	= display_ical_list(availableCalendars($username, $password, $ALL_CALENDARS_COMBINED));
$list_years 	= list_years();
$list_months 	= list_months();
$list_weeks 	= list_weeks();
$list_jumps 	= list_jumps();
$list_calcolors = list_calcolors();
$list_icals_pick = display_ical_list(availableCalendars($username, $password, $ALL_CALENDARS_COMBINED), TRUE);

// login/logout
$is_logged_in = ($username != '' && !$invalid_login) ? true : false;
$show_user_login = (!$is_logged_in && $allow_login == 'yes');
$login_querys = login_querys();
$logout_querys = logout_querys();

$page = new Page(BASE.'templates/'.$template.'/day.tpl');

$page->replace_files(array(
	'header'			=> BASE.'templates/'.$template.'/header.tpl',
	'event_js'			=> BASE.'functions/event.js',
	'footer'			=> BASE.'templates/'.$template.'/footer.tpl',
    'sidebar'           => BASE.'templates/'.$template.'/sidebar.tpl',
    'search_box'        => BASE.'templates/'.$template.'/search_box.tpl'
	));

$page->replace_tags(array(
	'version'			=> $phpicalendar_version,
	'charset'			=> $charset,
	'default_path'		=> '',
	'template'			=> $template,
	'cal'				=> $cal,
	'getdate'			=> $getdate,
	'getcpath'			=> "&cpath=$cpath",
	'cpath'				=> $cpath,
	'calendar_name'		=> $cal_displayname,
	'current_view'		=> $current_view,
	'display_date'		=> $display_date,
	'sidebar_date'		=> $sidebar_date,
	'rss_powered'	 	=> $rss_powered,
	'rss_available' 	=> '',
	'rss_valid' 		=> '',
	'show_search' 		=> $show_search,
	'next_day' 			=> $next_day,
	'prev_day'	 		=> $prev_day,
	'show_goto' 		=> '',
	'show_user_login'	=> $show_user_login,
	'invalid_login'		=> $invalid_login,
	'login_querys'		=> $login_querys,
	'is_logged_in' 		=> $is_logged_in,
	'username'			=> $username,
	'logout_querys'		=> $logout_querys,
	'list_icals' 		=> $list_icals,
	'list_icals_pick' 	=> $list_icals_pick,
	'list_years' 		=> $list_years,
	'list_months' 		=> $list_months,
	'list_weeks' 		=> $list_weeks,
	'list_jumps' 		=> $list_jumps,
	'legend'	 		=> $list_calcolors,
	'style_select' 		=> $style_select,
	'l_goprint'			=> $lang['l_goprint'],
	'l_preferences'		=> $lang['l_preferences'],
	'l_calendar'		=> $lang['l_calendar'],
	'l_legend'			=> $lang['l_legend'],
	'l_tomorrows'		=> $lang['l_tomorrows'],
	'l_jump'			=> $lang['l_jump'],
	'l_todo'			=> $lang['l_todo'],
	'l_day'				=> $lang['l_day'],
	'l_week'			=> $lang['l_week'],
	'l_month'			=> $lang['l_month'],
	'l_year'			=> $lang['l_year'],
	'l_pick_multiple'	=> $lang['l_pick_multiple'],
	'l_powered_by'		=> $lang['l_powered_by'],
	'l_subscribe'		=> $lang['l_subscribe'],
	'l_download'		=> $lang['l_download'],
	'l_search'			=> $lang['l_search'],
	'l_this_site_is'	=> $lang['l_this_site_is']
	));

if ($allow_preferences != 'yes') {
	$page->replace_tags(array(
	'allow_preferences'	=> ''
	));
}
	
if ($allow_login == 'yes') {
	$page->replace_tags(array(
	'l_invalid_login'	=> $lang['l_invalid_login'],
	'l_password'		=> $lang['l_password'],
	'l_username'		=> $lang['l_username'],
	'l_login'			=> $lang['l_login'],
	'l_logout'			=> $lang['l_logout']
	));
}

if ($show_search != 'yes') {
	$page->nosearch($page);
}
	
$page->draw_day($page);
$page->tomorrows_events($page);
$page->get_vtodo($page);
$page->draw_subscribe($page);

$page->output();

?>
