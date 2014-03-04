<?php

define('BASE','./');
require_once(BASE.'functions/ical_parser.php');
require_once(BASE.'functions/template.php');
header("Content-Type: text/html; charset=$charset");
$display_date = $preferences_lang;

if ($allow_preferences != 'yes') {
	exit(error('Preferences are not available for this installation.', $cal));
}

if ($cookie_uri == '') {
	$cookie_uri = $_SERVER['SERVER_NAME'].substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'], '/'));
}

$current_view = "preferences";
$back_page = BASE.$default_view.'.php?cal='.$cal.'&amp;getdate='.$getdate.'&amp;cpath='.$cpath;
if ($allow_preferences == 'no') header("Location: $back_page");

if (isset($_GET['action'])) {
	$action = $_GET['action'];
} else {
	$action = '';
} 

$startdays = array ('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
$cpath = $_REQUEST['cpath'];

if ($action == 'setcookie') { 
	$cookie_language 	= $_POST['cookie_language'];
   	$cookie_cpath     	= $_POST['cpath'];
	$cookie_calendar 	= $_POST['cookie_calendar'];
	$cookie_view 		= $_POST['cookie_view'];
	$cookie_style 		= $_POST['cookie_style'];
	$cookie_startday	= $_POST['cookie_startday'];
	$cookie_time		= $_POST['cookie_time'];
	$cookie_unset		= $_POST['unset'];
	$the_cookie = array ("cookie_language" => "$cookie_language", "cookie_calendar" => "$cookie_calendar", "cookie_view" => "$cookie_view", "cookie_startday" => "$cookie_startday", "cookie_style" => "$cookie_style", "cookie_time" => "$cookie_time", "cookie_cpath"=>"$cookie_cpath");
	$the_cookie 		= serialize($the_cookie);
	if ($cookie_unset) { 
		setcookie("$cookie_name","$the_cookie",time()-(60*60*24*7) ,"/","$cookie_uri",0);
	} else {
		setcookie("$cookie_name","$the_cookie",time()+(60*60*24*7*12*10) ,"/","$cookie_uri",0);
		if (isset($_POST['cookie_view'])) 
			$default_view = $_POST['cookie_view'];
		if (isset($_POST['cookie_style']) && is_dir(BASE.'templates/'.$_POST['cookie_style'].'/')) 
			$template = $_POST['cookie_style'];
		if (isset($_POST['cookie_language']) && is_file(BASE.'languages/'.strtolower($_POST['cookie_language']).'.inc.php')) 
			include(BASE.'languages/'.strtolower($_POST['cookie_language']).'.inc.php');
	}
	$_COOKIE[$cookie_name] = $the_cookie;
    $cpath = $cookie_cpath;
    $cal = $cookie_calendar;
}

if (isset($_COOKIE[$cookie_name])) {
	$phpicalendar 		= unserialize(stripslashes($_COOKIE[$cookie_name]));
	$cookie_language 	= $phpicalendar['cookie_language'];
	$cookie_calendar 	= $phpicalendar['cookie_calendar'];
	$cookie_view 		= $phpicalendar['cookie_view'];
	$cookie_style 		= $phpicalendar['cookie_style'];
	$cookie_startday	= $phpicalendar['cookie_startday'];
	$cookie_time		= $phpicalendar['cookie_time'];
	if ($cookie_unset) { 
		unset ($cookie_language, $cookie_calendar, $cookie_view, $cookie_style,$cookie_startday);
	}
}

if ((!isset($_COOKIE[$cookie_name])) || ($cookie_unset)) {
	# No cookie set -> use defaults from config file.
	$cookie_language = ucfirst($language);
	$cookie_calendar = $default_cal;
	$cookie_view = $default_view;
	$cookie_style = $template;
	$cookie_startday = $week_start_day;
	$cookie_time = $day_start;
}

if ($action == 'setcookie') { 
	if (!$cookie_unset) {
		$message = $lang['l_prefs_set'];
	} else {
		$message = $lang['l_prefs_unset'];
	}
} else {
	$message = '';
}

// select for languages
$dir_handle = @opendir(BASE.'languages/');
$tmp_pref_language = urlencode(ucfirst($language));
while ($file = readdir($dir_handle)) {
	if (substr($file, -8) == ".inc.php") {
		$language_tmp = urlencode(ucfirst(substr($file, 0, -8)));
		if ($language_tmp == $cookie_language) {
			$language_select .= '<option value="'.$language_tmp.'" selected="selected">'.$language_tmp.'</option>';
		} else {
			$language_select .= '<option value="'.$language_tmp.'">'.$language_tmp.'</option>';
		}
	}
}
closedir($dir_handle);

// select for calendars
$calendar_select = display_ical_list(availableCalendars($username, $password, $ALL_CALENDARS_COMBINED),TRUE);
$calendar_select .="<option value=\"$ALL_CALENDARS_COMBINED\">$all_cal_comb_lang</option>";
$calendar_select = str_replace("<option value=\"$cookie_calendar\">","<option value=\"$cookie_calendar\" selected='selected'>",$calendar_select);
// select for dayview
$view_select 	= ($default_view == 'day') ? '<option value="day" selected="selected">{L_DAY}</option>' : '<option value="day">{L_DAY}</option>';
$view_select    .= ($default_view == 'week') ? '<option value="week" selected="selected">{L_WEEK}</option>' : '<option value="week">{L_WEEK}</option>';
$view_select    .= ($default_view == 'month') ? '<option value="month" selected="selected">{L_MONTH}</option>' : '<option value="month">{L_MONTH}</option>';

// select for time
for ($i = 000; $i <= 1200; $i += 100) {
	$s = sprintf("%04d", $i);
	$time_select .= '<option value="'.$s.'"';
	if ($s == $cookie_time) {
		$time_select .= ' selected="selected"';
	}
	$time_select .= ">$s</option>\n";
}

// select for day of week
$i=0;
foreach ($daysofweek_lang as $daysofweek) {
	if ($startdays[$i] == $cookie_startday) {
		$startday_select .= '<option value="'.$startdays[$i].'" selected="selected">'.$daysofweek.'</option>';
	} else {
		$startday_select .= '<option value="'.$startdays[$i].'">'.$daysofweek.'</option>';
	}
	$i++;
}

$dir_handle = @opendir(BASE.'templates/');
while ($file = readdir($dir_handle)) {
	if (($file != ".") && ($file != "..") && ($file != "CVS")) {
		if (is_dir(BASE.'templates/'.$file)) {
			$file_disp = ucfirst($file);
			$style_select .= ($file == "$cookie_style") ? "<option value=\"$file\" selected=\"selected\">$file_disp</option>\n" : "<option value=\"$file\">$file_disp</option>\n";
		}
	}
}
closedir($dir_handle);

$php_ended = getmicrotime();
$generated = number_format(($php_ended-$php_started),3);

$page = new Page(BASE.'templates/'.$template.'/preferences.tpl');

$page->replace_files(array(
	'header'			=> BASE.'templates/'.$template.'/header.tpl',
	'footer'			=> BASE.'templates/'.$template.'/footer.tpl'
	));

$page->replace_tags(array(
	'version'			=> $phpicalendar_version,
	'charset'			=> $charset,
	'template'			=> $template,
	'default_path'		=> '',
	'cpath'				=> $cpath,
	'cal'				=> $cal,
	'getdate'			=> $getdate,
	'calendar_name'		=> $calendar_name,
	'display_date'		=> $display_date,
	'rss_powered'	 	=> $rss_powered,
	'rss_available' 	=> '',
	'rss_valid' 		=> '',
	'event_js' 			=> '',
	'language_select' 	=> $language_select,
	'calendar_select' 	=> $calendar_select,
	'view_select' 		=> $view_select,
	'time_select' 		=> $time_select,
	'startday_select' 	=> $startday_select,
	'style_select' 		=> $style_select,
	'display_date'	 	=> $lang['l_preferences'],
	'generated'	 		=> $generated,
	'message'	 		=> $message,
	'l_preferences'		=> $lang['l_preferences'],
	'l_prefs_subhead'	=> $lang['l_prefs_subhead'],
	'l_select_lang'		=> $lang['l_select_lang'],
	'l_select_view'		=> $lang['l_select_view'],
	'l_select_time'		=> $lang['l_select_time'],
	'l_select_day'		=> $lang['l_select_day'],
	'l_select_cal'		=> $lang['l_select_cal'],
	'l_select_style'	=> $lang['l_select_style'],
	'l_unset_prefs'		=> $lang['l_unset_prefs'],
	'l_set_prefs'		=> $lang['l_set_prefs'],
	'l_day'				=> $lang['l_day'],
	'l_week'			=> $lang['l_week'],
	'l_month'			=> $lang['l_month'],
	'l_year'			=> $lang['l_year'],
	'l_subscribe'		=> $lang['l_subscribe'],
	'l_download'		=> $lang['l_download'],
	'l_powered_by'		=> $lang['l_powered_by'],
	'l_this_site_is'	=> $lang['l_this_site_is']	
			
	));

$page->output();

?>
