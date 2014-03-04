<?php

function list_jumps() {
	global $second_offset, $lang, $cal;
	$calName = join(',', array_map("getCalendarName", split(',', $cal)));
	$today = date('Ymd', time() + $second_offset);
	$return = '<option value="#">'.$lang['l_jump'].'</option>';
	$return .= '<option value="day.php?cal='.$calName.'&amp;getdate='.$today.'">'.$lang['l_goday'].'</option>';
	$return .= '<option value="week.php?cal='.$calName.'&amp;getdate='.$today.'">'.$lang['l_goweek'].'</option>';
	$return .= '<option value="month.php?cal='.$calName.'&amp;getdate='.$today.'">'.$lang['l_gomonth'].'</option>';
	$return .= '<option value="year.php?cal='.$calName.'&amp;getdate='.$today.'">'.$lang['l_goyear'].'</option>';
	return $return;
}

function list_calcolors() {
	global $template, $master_array, $unique_colors;
	$i = 1;
	if (is_array($master_array['-3'])) {
		foreach ($master_array['-3'] as $key => $val) {
			if ($i > $unique_colors) $i = 1;
			$val = str_replace ("\,", ",", $val);
			$return .= '<img src="templates/'.$template.'/images/monthdot_'.$i.'.gif" alt="" /> '.$val.'<br />';
			$i++;
		}
	}
	return $return;
}

function list_months() {
	global $getdate, $this_year, $cal, $dateFormat_month;
	$month_time 	= strtotime("$this_year-01-01");
	$getdate_month 	= date("m", strtotime($getdate));
	for ($i=0; $i<12; $i++) {
		$monthdate 		= date ("Ymd", $month_time);
		$month_month 	= date("m", $month_time);
		$select_month 	= localizeDate($dateFormat_month, $month_time);
		if ($month_month == $getdate_month) {
			$return .= "<option value=\"month.php?cal=$cal&amp;getdate=$monthdate\" selected=\"selected\">$select_month</option>\n";
		} else {
			$return .= "<option value=\"month.php?cal=$cal&amp;getdate=$monthdate\">$select_month</option>\n";
		}
		$month_time = strtotime ("+1 month", $month_time);
	}
	return $return;
}


function list_years() {
	global $getdate, $this_year, $cal, $num_years;
	$year_time = strtotime($getdate);
	for ($i=0; $i < $num_years; $i++) {
		$offset = $num_years - $i;
		$prev_time = strtotime("-$offset year", $year_time);
		$prev_date = date("Ymd", $prev_time);
		$prev_year = date("Y", $prev_time);
		$return .= "<option value=\"year.php?cal=$cal&amp;getdate=$prev_date\">$prev_year</option>\n";
	}
	
	$getdate_date = date("Ymd", $year_time);
	$getdate_year = date("Y", $year_time);
	$return .= "<option value=\"year.php?cal=$cal&amp;getdate=$getdate_date\" selected=\"selected\">$getdate_year</option>\n";
	
	for ($i=0; $i < $num_years; $i++) {
		$offset = $i + 1;
		$next_time = strtotime("+$offset year", $year_time);
		$next_date = date("Ymd", $next_time);
		$next_year = date("Y", $next_time);
		$return .=  "<option value=\"year.php?cal=$cal&amp;getdate=$next_date\">$next_year</option>\n";
	}
	
	return $return;
}


function list_weeks() {
	global $getdate, $this_year, $cal, $dateFormat_week_jump, $week_start_day;
	ereg ("([0-9]{4})([0-9]{2})([0-9]{2})", $getdate, $day_array2);
	$this_day 			= $day_array2[3]; 
	$this_month 		= $day_array2[2];
	$this_year 			= $day_array2[1];
	$check_week 		= strtotime($getdate);
	$start_week_time 	= strtotime(dateOfWeek(date("Ymd", strtotime("$this_year-01-01")), $week_start_day));
	$end_week_time 		= $start_week_time + (6 * 25 * 60 * 60);
		
	do {
		$weekdate 		= date ("Ymd", $start_week_time);
		$select_week1 	= localizeDate($dateFormat_week_jump, $start_week_time);
		$select_week2 	= localizeDate($dateFormat_week_jump, $end_week_time);
	
		if (($check_week >= $start_week_time) && ($check_week <= $end_week_time)) {
			$return .= "<option value=\"week.php?cal=$cal&amp;getdate=$weekdate\" selected=\"selected\">$select_week1 - $select_week2</option>\n";
		} else {
			$return .= "<option value=\"week.php?cal=$cal&amp;getdate=$weekdate\">$select_week1 - $select_week2</option>\n";
		}
		$start_week_time =  strtotime ("+1 week", $start_week_time);
		$end_week_time = $start_week_time + (6 * 25 * 60 * 60);
	} while (date("Y", $start_week_time) <= $this_year);

	return $return;
}

function list_languages() {
	global $getdate, $cal, $current_view;
	$dir_handle = @opendir(BASE.'languages/');
	$tmp_pref_language = urlencode(ucfirst($language));
	while ($file = readdir($dir_handle)) {
		if (substr($file, -8) == ".inc.php") {
			$language_tmp = urlencode(ucfirst(substr($file, 0, -8)));
			if ($language_tmp == $tmp_pref_language) {
				$return .= "<option value=\"$current_view.php?chlang=$language_tmp\" selected=\"selected\">in $language_tmp</option>\n";
			} else {
				$return .= "<option value=\"$current_view.php?chlang=$language_tmp\">in $language_tmp</option>\n";
			}
		}
	}
	closedir($dir_handle);
	
	return $return;
}


?>
