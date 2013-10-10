<?php

if (!defined('BASE')) define('BASE', './');
include_once(BASE.'functions/init.inc.php');
include_once(BASE.'functions/date_functions.php');
include_once(BASE.'functions/draw_functions.php');
include_once(BASE.'functions/overlapping_events.php');
include_once(BASE.'functions/timezones.php');

$php_started = getmicrotime();

$fillTime = $day_start;
$day_array = array ();
while ($fillTime < $day_end) {
	array_push ($day_array, $fillTime);
	preg_match ('/([0-9]{2})([0-9]{2})/', $fillTime, $dTime);
	$fill_h = $dTime[1];
	$fill_min = $dTime[2];
	$fill_min = sprintf('%02d', $fill_min + $gridLength);
	if ($fill_min == 60) {
		$fill_h = sprintf('%02d', ($fill_h + 1));
		$fill_min = '00';
	}
	$fillTime = $fill_h . $fill_min;
}

// what date we want to get data for (for day calendar)
if (!isset($getdate) || $getdate == '') $getdate = date('Ymd');
preg_match ("/([0-9]{4})([0-9]{2})([0-9]{2})/", $getdate, $day_array2);
$this_day = $day_array2[3];
$this_month = $day_array2[2];
$this_year = $day_array2[1];

// reading the file if it's allowed
$parse_file = true;
if ($save_parsed_cals == 'yes') {	
	if (sizeof ($cal_filelist) > 1) {
		$parsedcal = $tmp_dir.'/parsedcal-'.urlencode($cal_filename).'-'.$this_year;
		if (file_exists($parsedcal)) {
			$fd = fopen($parsedcal, 'r');
			$contents = fread($fd, filesize($parsedcal));
			fclose($fd);
			$master_array = unserialize($contents);
			$z=1;
			$y=0;
			if (sizeof($master_array['-4']) == (sizeof($cal_filelist))) {
				foreach ($master_array['-4'] as $temp_array) {
					$mtime = $master_array['-4'][$z]['mtime'];
					$fname = $master_array['-4'][$z]['filename'];
					$wcalc = $master_array['-4'][$z]['webcal'];	
					if ($wcalc == 'no') $realcal_mtime = filemtime($fname);
					$webcal_mtime = time() - strtotime($webcal_hours * 3600);
					if (($mtime == $realcal_mtime) && ($wcalc == 'no')) {
						$y++;
					} elseif (($wcalc == 'yes') && ($mtime > $webcal_mtime)) {
						//echo date('H:i',$mtime). ' > '. date('H:i',$webcal_mtime);
						$y++;
					}
					$z++;
				}
				foreach ($master_array['-3'] as $temp_array) {
					if (isset($temp_array) && $temp_array !='') $caldisplaynames[] = $temp_array;
				}

				if ($y == sizeof($cal_filelist)) {
					if ($master_array['-1'] == 'valid cal file') {
						$parse_file = false;
						$calendar_name = $master_array['calendar_name'];
						$calendar_tz = $master_array['calendar_tz'];
					}
				}
			}
		}
		if ($parse_file == true) unset($master_array);
	} else {
		foreach ($cal_filelist as $filename) {
			$realcal_mtime = filemtime($filename);
			$parsedcal = $tmp_dir.'/parsedcal-'.urlencode($cal_filename).'-'.$this_year;
			if (file_exists($parsedcal)) {
				$parsedcal_mtime = filemtime($parsedcal);
				if ($realcal_mtime == $parsedcal_mtime) {
					$fd = fopen($parsedcal, 'r');
					$contents = fread($fd, filesize($parsedcal));
					fclose($fd);
					$master_array = unserialize($contents);
					if ($master_array['-1'] == 'valid cal file') {
						$parse_file = false;
						$calendar_name = $master_array['calendar_name'];
						$calendar_tz = $master_array['calendar_tz'];
					}
				}
			}
		}
	}
}

if ($parse_file) {	
	$overlap_array = array ();
	$uid_counter = 0;
}

$calnumber = 1;
foreach ($cal_filelist as $cal_key=>$filename) {
	
	// Find the real name of the calendar.
	$actual_calname = getCalendarName($filename);
	
	if ($parse_file) {	
		
		// Let's see if we're doing a webcal
		$is_webcal = FALSE;
		if (substr($filename, 0, 7) == 'http://' || substr($filename, 0, 8) == 'https://' || substr($filename, 0, 9) == 'webcal://') {
			$is_webcal = TRUE;
			$cal_webcalPrefix = str_replace('http://','webcal://',$filename);
			$cal_httpPrefix = str_replace('webcal://','http://',$filename);
			$cal_httpsPrefix = str_replace('webcal://','https://',$filename);
			$cal_httpsPrefix = str_replace('http://','https://',$cal_httpsPrefix);
			$filename = $cal_httpPrefix;
			$master_array['-4'][$calnumber]['webcal'] = 'yes';
			$actual_mtime = time();
		} else {
			$actual_mtime = @filemtime($filename);
		}
		
		$ifile = @fopen($filename, "r");
		if ($ifile == FALSE) exit(error($lang['l_error_cantopen'], $filename));
		$nextline = fgets($ifile, 1024);
		if (trim($nextline) != 'BEGIN:VCALENDAR') exit(error($lang['l_error_invalidcal'], $filename));
		
		// Set a value so we can check to make sure $master_array contains valid data
		$master_array['-1'] = 'valid cal file';
	
		// Set default calendar name - can be overridden by X-WR-CALNAME
		$calendar_name = $cal_filename;
		$master_array['calendar_name'] 	= $calendar_name;
		
	// read file in line by line
	// XXX end line is skipped because of the 1-line readahead
		while (!feof($ifile)) {
			$line = $nextline;
			$nextline = fgets($ifile, 1024);
			$nextline = ereg_replace("[\r\n]", "", $nextline);
			while (substr($nextline, 0, 1) == " ") {
				$line = $line . substr($nextline, 1);
				$nextline = fgets($ifile, 1024);
				$nextline = ereg_replace("[\r\n]", "", $nextline);
			}
			$line = trim($line);
			
		switch ($line) {
			case 'BEGIN:VEVENT':
				// each of these vars were being set to an empty string
				unset (
					$start_time, $end_time, $start_date, $end_date, $summary, 
					$allday_start, $allday_end, $start, $end, $the_duration, 
					$beginning, $rrule_array, $start_of_vevent, $description, $url, 
					$valarm_description, $start_unixtime, $end_unixtime, $display_end_tmp, $end_time_tmp1, 
					$recurrence_id, $uid, $class, $location, $rrule, $abs_until, $until_check,
					$until, $bymonth, $byday, $bymonthday, $byweek, $byweekno, 
					$byminute, $byhour, $bysecond, $byyearday, $bysetpos, $wkst,
					$interval, $number
				);
					
				$except_dates 	= array();
				$except_times 	= array();
				$bymonth	 	= array();
				$bymonthday 	= array();
				$first_duration = TRUE;
				$count 			= 1000000;
				$valarm_set 	= FALSE;
				$attendee		= array();
				$organizer		= array();
				
				break;
			
			case 'END:VEVENT':
				
				if (!isset($url)) $url = '';
				if (!isset($type)) $type = '';
				
				// Handle DURATION
				if (!isset($end_unixtime) && isset($the_duration)) {
					$end_unixtime 	= $start_unixtime + $the_duration;
					$end_time 	= date ('Hi', $end_unixtime);
				}
					
				// CLASS support
				if (isset($class)) {
					if ($class == 'PRIVATE') {
						$summary ='**PRIVATE**';
						$description ='**PRIVATE**';
					} elseif ($class == 'CONFIDENTIAL') {
						$summary ='**CONFIDENTIAL**';
						$description ='**CONFIDENTIAL**';
					}
				}	 
				
				// make sure we have some value for $uid
				if (!isset($uid)) {
					$uid = $uid_counter;
					$uid_counter++;
					$uid_valid = false;
				} else {
					$uid_valid = true;
				}
				
				if ($uid_valid && isset($processed[$uid]) && isset($recurrence_id['date'])) {
					
					$old_start_date = $processed[$uid][0];
					$old_start_time = $processed[$uid][1];
					if ($recurrence_id['value'] == 'DATE') $old_start_time = '-1';
					$start_date_tmp = $recurrence_id['date'];
					if (!isset($start_date)) $start_date = $old_start_date;
					if (!isset($start_time)) $start_time = $master_array[$old_start_date][$old_start_time][$uid]['event_start'];
					if (!isset($start_unixtime)) $start_unixtime = $master_array[$old_start_date][$old_start_time][$uid]['start_unixtime'];
					if (!isset($end_unixtime)) $end_unixtime = $master_array[$old_start_date][$old_start_time][$uid]['end_unixtime'];
					if (!isset($end_time)) $end_time = $master_array[$old_start_date][$old_start_time][$uid]['event_end'];
					if (!isset($summary)) $summary = $master_array[$old_start_date][$old_start_time][$uid]['event_text'];
					if (!isset($length)) $length = $master_array[$old_start_date][$old_start_time][$uid]['event_length'];
					if (!isset($description)) $description = $master_array[$old_start_date][$old_start_time][$uid]['description'];
					if (!isset($location)) $location = $master_array[$old_start_date][$old_start_time][$uid]['location'];
					if (!isset($organizer)) $organizer = $master_array[$old_start_date][$old_start_time][$uid]['organizer'];
					if (!isset($status)) $status = $master_array[$old_start_date][$old_start_time][$uid]['status'];
					if (!isset($attendee)) $attendee = $master_array[$old_start_date][$old_start_time][$uid]['attendee'];
					if (!isset($url)) $url = $master_array[$old_start_date][$old_start_time][$uid]['url'];
					removeOverlap($start_date_tmp, $old_start_time, $uid);
					if (isset($master_array[$start_date_tmp][$old_start_time][$uid])) {
						unset($master_array[$start_date_tmp][$old_start_time][$uid]);  // SJBO added $uid twice here
						if (sizeof($master_array[$start_date_tmp][$old_start_time]) == 0) {
							unset($master_array[$start_date_tmp][$old_start_time]);
						}
					}
					
					$write_processed = false;
				} else {
					$write_processed = true;
				}
				
				if (!isset($summary)) 		$summary = '';
				if (!isset($description)) 	$description = '';
				if (!isset($status)) 		$status = '';
				if (!isset($class)) 		$class = '';
				if (!isset($location)) 		$location = '';
				
				$mArray_begin = mktime (0,0,0,12,21,($this_year - 1));
				$mArray_end = mktime (0,0,0,1,12,($this_year + 1));
				
				if (isset($start_time) && isset($end_time)) {
					// Mozilla style all-day events or just really long events
					if (($end_time - $start_time) > 2345) {
						$allday_start = $start_date;
						$allday_end = ($start_date + 1);
					}
				}
				if (isset($start_unixtime,$end_unixtime) && date('Ymd',$start_unixtime) != date('Ymd',$end_unixtime)) {
					$spans_day = true;
					$bleed_check = (($start_unixtime - $end_unixtime) < (60*60*24)) ? '-1' : '0';
				} else {
					$spans_day = false;
					$bleed_check = 0;
				}
				if (isset($start_time) && $start_time != '') {
					preg_match ('/([0-9]{2})([0-9]{2})/', $start_time, $time);
					preg_match ('/([0-9]{2})([0-9]{2})/', $end_time, $time2);
					if (isset($start_unixtime) && isset($end_unixtime)) {
						$length = $end_unixtime - $start_unixtime;
					} else {
						$length = ($time2[1]*60+$time2[2]) - ($time[1]*60+$time[2]);
					}
					
					$drawKey = drawEventTimes($start_time, $end_time);
					preg_match ('/([0-9]{2})([0-9]{2})/', $drawKey['draw_start'], $time3);
					$hour = $time3[1];
					$minute = $time3[2];
				}
	
				// RECURRENCE-ID Support
				if (isset($recurrence_d)) {
					
					$recurrence_delete["$recurrence_d"]["$recurrence_t"] = $uid;
				}
					
				// handle single changes in recurring events
				// Maybe this is no longer need since done at bottom of parser? - CL 11/20/02
				if ($uid_valid && $write_processed) {
					if (!isset($hour)) $hour = 00;
					if (!isset($minute)) $minute = 00;
					$processed[$uid] = array($start_date,($hour.$minute), $type);
				}
							
				// Handling of the all day events
				if ((isset($allday_start) && $allday_start != '')) {
  					$start = strtotime($allday_start);
 					if ($spans_day) {
 						$allday_end = date('Ymd',$end_unixtime);
 					}
  					if (isset($allday_end)) {
  						$end = strtotime($allday_end);
  					} else {
						$end = strtotime('+1 day', $start);
					}
					// Changed for 1.0, basically write out the entire event if it starts while the array is written.
					if (($start < $mArray_end) && ($start < $end)) {
						while (($start != $end) && ($start < $mArray_end)) {
							$start_date2 = date('Ymd', $start);
							$master_array[($start_date2)][('-1')][$uid]= array (
								'event_text' => $summary, 
								'description' => $description, 
								'location' => $location, 
								'organizer' => serialize($organizer), 
								'attendee' => serialize($attendee), 
								'calnumber' => $calnumber, 
								'calname' => $actual_calname, 
								'url' => $url, 
								'status' => $status, 
								'class' => $class );
							$start = strtotime('+1 day', $start);
						}
						if (!$write_processed) $master_array[($start_date)]['-1'][$uid]['exception'] = true;
					}
				}
				
				// Handling regular events
				if ((isset($start_time) && $start_time != '') && (!isset($allday_start) || $allday_start == '')) {
					if (($end_time >= $bleed_time) && ($bleed_check == '-1')) {
						$start_tmp = strtotime(date('Ymd',$start_unixtime));
						$end_date_tmp = date('Ymd',$end_unixtime);
						while ($start_tmp < $end_unixtime) {
							$start_date_tmp = date('Ymd',$start_tmp);
							if ($start_date_tmp == $start_date) {
								$time_tmp = $hour.$minute;
								$start_time_tmp = $start_time;
							} else {
								$time_tmp = '0000';
								$start_time_tmp = '0000';
							}
							if ($start_date_tmp == $end_date_tmp) {
								$end_time_tmp = $end_time;
							} else {
								$end_time_tmp = '2400';
								$display_end_tmp = $end_time;
							}
							
							$master_array[$start_date_tmp][$time_tmp][$uid] = array (
								'event_start' => $start_time_tmp, 
								'event_end' => $end_time_tmp, 
								'start_unixtime' => $start_unixtime, 
								'end_unixtime' => $end_unixtime, 
								'event_text' => $summary, 
								'event_length' => $length, 
								'event_overlap' => 0, 
								'description' => $description, 
								'status' => $status, 
								'class' => $class, 
								'spans_day' => true, 
								'location' => $location, 
								'organizer' => serialize($organizer), 
								'attendee' => serialize($attendee), 
								'calnumber' => $calnumber, 
								'calname' => $actual_calname, 
								'url' => $url );
							if (isset($display_end_tmp)){
								$master_array[$start_date_tmp][$time_tmp][$uid]['display_end'] = $display_end_tmp;
							}
							checkOverlap($start_date_tmp, $time_tmp, $uid);
							$start_tmp = strtotime('+1 day',$start_tmp);
						}
						if (!$write_processed) $master_array[$start_date][($hour.$minute)][$uid]['exception'] = true;
					} else {
						if ($bleed_check == '-1') {
							$display_end_tmp = $end_time;
							$end_time_tmp1 = '2400';	
						}
						
						if (!isset($end_time_tmp1)) $end_time_tmp1 = $end_time;
					
						// This if statement should prevent writing of an excluded date if its the first recurrance - CL
						if (!in_array($start_date, $except_dates)) {
							$master_array[($start_date)][($hour.$minute)][$uid] = array (
								'event_start' => $start_time, 
								'event_end' => $end_time_tmp1, 
								'start_unixtime' => $start_unixtime, 
								'end_unixtime' => $end_unixtime, 
								'event_text' => $summary, 
								'event_length' => $length, 
								'event_overlap' => 0, 
								'description' => $description, 
								'status' => $status, 
								'class' => $class, 
								'spans_day' => false, 
								'location' => $location, 
								'organizer' => serialize($organizer), 
								'attendee' => serialize($attendee), 
								'calnumber' => $calnumber, 
								'calname' => $actual_calname, 
								'url' => $url );
							if (isset($display_end_tmp)){
								$master_array[($start_date)][($hour.$minute)][$uid]['display_end'] = $display_end_tmp;
							}
							checkOverlap($start_date, ($hour.$minute), $uid);
							if (!$write_processed) $master_array[($start_date)][($hour.$minute)][$uid]['exception'] = true;
						}
					}
				}
				
				// Handling of the recurring events, RRULE
				if (isset($rrule_array) && is_array($rrule_array)) {
					if (isset($allday_start) && $allday_start != '') {
						$hour = '-';
						$minute = '1';
						$rrule_array['START_DAY'] = $allday_start;
						$rrule_array['END_DAY'] = $allday_end;
						$rrule_array['END'] = 'end';
						$recur_start = $allday_start;
						$start_date = $allday_start;
						if (isset($allday_end)) {
							$diff_allday_days = dayCompare($allday_end, $allday_start);
						 } else {
							$diff_allday_days = 1;
						}
					} else {
						$rrule_array['START_DATE'] = $start_date;
						$rrule_array['START_TIME'] = $start_time;
						$rrule_array['END_TIME'] = $end_time;
						$rrule_array['END'] = 'end';
					}
					
					$start_date_time = strtotime($start_date);
					$this_month_start_time = strtotime($this_year.$this_month.'01');
					if ($current_view == 'year' || ($save_parsed_cals == 'yes' && !$is_webcal)|| $current_view == 'print' && $printview == 'year') {
						$start_range_time = strtotime($this_year.'-01-01 -2 weeks');
						$end_range_time = strtotime($this_year.'-12-31 +2 weeks');
					} else {
						$start_range_time = strtotime('-1 month -2 day', $this_month_start_time);
						$end_range_time = strtotime('+2 month +2 day', $this_month_start_time);
					}
					
					foreach ($rrule_array as $key => $val) {
						switch($key) {
							case 'FREQ':
								switch ($val) {
									case 'YEARLY':		$freq_type = 'year';	break;
									case 'MONTHLY':		$freq_type = 'month';	break;
									case 'WEEKLY':		$freq_type = 'week';	break;
									case 'DAILY':		$freq_type = 'day';		break;
									case 'HOURLY':		$freq_type = 'hour';	break;
									case 'MINUTELY':	$freq_type = 'minute';	break;
									case 'SECONDLY':	$freq_type = 'second';	break;
								}
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = strtolower($val);
								break;
							case 'COUNT':
								$count = $val;
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $count;
								break;
							case 'UNTIL':
								$until = str_replace('T', '', $val);
								$until = str_replace('Z', '', $until);
								if (strlen($until) == 8) $until = $until.'235959';
								$abs_until = $until;
								ereg ('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})', $until, $regs);
								$until = mktime($regs[4],$regs[5],$regs[6],$regs[2],$regs[3],$regs[1]);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = localizeDate($dateFormat_week,$until);
								break;
							case 'INTERVAL':
								if ($val > 0){
								$number = $val;
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $number;
								}
								break;
							case 'BYSECOND':
								$bysecond = $val;
								$bysecond = split (',', $bysecond);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $bysecond;
								break;
							case 'BYMINUTE':
								$byminute = $val;
								$byminute = split (',', $byminute);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $byminute;
								break;
							case 'BYHOUR':
								$byhour = $val;
								$byhour = split (',', $byhour);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $byhour;
								break;
							case 'BYDAY':
								$byday = $val;
								$byday = split (',', $byday);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $byday;
								break;
							case 'BYMONTHDAY':
								$bymonthday = $val;
								$bymonthday = split (',', $bymonthday);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $bymonthday;
								break;					
							case 'BYYEARDAY':
								$byyearday = $val;
								$byyearday = split (',', $byyearday);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $byyearday;
								break;
							case 'BYWEEKNO':
								$byweekno = $val;
								$byweekno = split (',', $byweekno);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $byweekno;
								break;
							case 'BYMONTH':
								$bymonth = $val;
								$bymonth = split (',', $bymonth);
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $bymonth;
								break;
							case 'BYSETPOS':
								$bysetpos = $val;
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $bysetpos;
								break;
							case 'WKST':
								$wkst = $val;
								$master_array[($start_date)][($hour.$minute)][$uid]['recur'][$key] = $wkst;
								break;
							case 'END':

							$recur = $master_array[($start_date)][($hour.$minute)][$uid]['recur'];

							// Modify the COUNT based on BYDAY
							if ((is_array($byday)) && (isset($count))) {
								$blah = sizeof($byday);
								$count = ($count / $blah);
								unset ($blah);
							}
						
							if (!isset($number)) $number = 1;
							// if $until isn't set yet, we set it to the end of our range we're looking at
							
							if (!isset($until)) $until = $end_range_time;
							if (!isset($abs_until)) $abs_until = date('YmdHis', $end_range_time);
							$end_date_time = $until;
							$start_range_time_tmp = $start_range_time;
							$end_range_time_tmp = $end_range_time;
							
							// If the $end_range_time is less than the $start_date_time, or $start_range_time is greater
							// than $end_date_time, we may as well forget the whole thing
							// It doesn't do us any good to spend time adding data we aren't even looking at
							// this will prevent the year view from taking way longer than it needs to
							if ($end_range_time_tmp >= $start_date_time && $start_range_time_tmp <= $end_date_time) {
							
								// if the beginning of our range is less than the start of the item, we may as well set it equal to it
								if ($start_range_time_tmp < $start_date_time){
									$start_range_time_tmp = $start_date_time;
								}	
								if ($end_range_time_tmp > $end_date_time) $end_range_time_tmp = $end_date_time;
					
								// initialize the time we will increment
								$next_range_time = $start_range_time_tmp;
								
								// FIXME: This is a hack to fix repetitions with $interval > 1 
								if ($count > 1 && $number > 1) $count = 1 + ($count - 1) * $number; 
								
								$count_to = 0;
								// start at the $start_range and go until we hit the end of our range.
								if(!isset($wkst)) $wkst='SU';
								$wkst3char = two2threeCharDays($wkst);

								while (($next_range_time >= $start_range_time_tmp) && ($next_range_time <= $end_range_time_tmp) && ($count_to != $count)) {
									$func = $freq_type.'Compare';
									$diff = $func(date('Ymd',$next_range_time), $start_date);
									if ($diff < $count) {
										if ($diff % $number == 0) {
											$interval = $number;
											switch ($rrule_array['FREQ']) {
												case 'DAILY':
													$next_date_time = $next_range_time;
													$recur_data[] = $next_date_time;
													break;
												case 'WEEKLY':
													// Populate $byday with the default day if it's not set.
													if (!isset($byday)) {
														$byday[] = strtoupper(substr(date('D', $start_date_time), 0, 2));
													}
													if (is_array($byday)) {
														foreach($byday as $day) {
															$day = two2threeCharDays($day);	
															#need to find the first day of the appropriate week.
															#dateOfweek uses weekstartday as a global variable. This has to be changed to $wkst, 
															#but then needs to be reset for other functions
															$week_start_day_tmp = $week_start_day;
															$week_start_day = $wkst3char;
															
															$the_sunday = dateOfWeek(date("Ymd",$next_range_time), $wkst3char);
															$next_date_time = strtotime($day,strtotime($the_sunday)) + (12 * 60 * 60);
															$week_start_day = $week_start_day_tmp; #see above reset to global value
															
															#reset $next_range_time to first instance in this week.
															if ($next_date_time < $next_range_time){ 
																$next_range_time = $next_date_time; 
															}
															// Since this renders events from $next_range_time to $next_range_time + 1 week, I need to handle intervals
															// as well. This checks to see if $next_date_time is after $day_start (i.e., "next week"), and thus
															// if we need to add $interval weeks to $next_date_time.
															if ($next_date_time > strtotime($week_start_day, $next_range_time) && $interval > 1) {
															#	$next_date_time = strtotime('+'.($interval - 1).' '.$freq_type, $next_date_time);
															}
															$recur_data[] = $next_date_time;
														}
													}
													break;
												case 'MONTHLY':
													if (empty($bymonth)) $bymonth = array(1,2,3,4,5,6,7,8,9,10,11,12);
													$next_range_time = strtotime(date('Y-m-01', $next_range_time));
													$next_date_time = $next_date_time;
													if (isset($bysetpos)){
														/* bysetpos code from dustinbutler
														start on day 1 or last day. 
														if day matches any BYDAY the count is incremented. 
														SETPOS = 4, need 4th match 
														SETPOS = -1, need 1st match 
													 	*/ 
													 	$year = date('Y', $next_range_time); 
													 	$month = date('m', $next_range_time); 
													 	if ($bysetpos > 0) { 
													  		$next_day = '+1 day'; 
													  		$day = 1; 
													 	} else { 
													  		$next_day = '-1 day'; 
													  		$day = $totalDays[$month]; 
													 	} 
													 	$day = mktime(0, 0, 0, $month, $day, $year); 
													 	$countMatch = 0; 
													 	while ($countMatch != abs($bysetpos)) { 
													  		/* Does this day match a BYDAY value? */ 
													  		$thisDay = $day; 
													  		$textDay = strtoupper(substr(date('D', $thisDay), 0, 2)); 
													  		if (in_array($textDay, $byday)) { 
													   			$countMatch++; 
													  		} 
													  		$day = strtotime($next_day, $thisDay); 
													 	} 
													 	$recur_data[] = $thisDay; 
													}elseif ((isset($bymonthday)) && (!isset($byday))) {
														foreach($bymonthday as $day) {
															if ($day < 0) $day = ((date('t', $next_range_time)) + ($day)) + 1;
															$year = date('Y', $next_range_time);
															$month = date('m', $next_range_time);
															if (checkdate($month,$day,$year)) {
																$next_date_time = mktime(0,0,0,$month,$day,$year);
																$recur_data[] = $next_date_time;
															}
														}
													} elseif (is_array($byday)) {
														foreach($byday as $day) {
															ereg ('([-\+]{0,1})?([0-9]{1})?([A-Z]{2})', $day, $byday_arr);
															//Added for 2.0 when no modifier is set
															if ($byday_arr[2] != '') {
																$nth = $byday_arr[2]-1;
															} else {
																$nth = 0;
															}
															$on_day = two2threeCharDays($byday_arr[3]);
															$on_day_num = two2threeCharDays($byday_arr[3],false);
															if ((isset($byday_arr[1])) && ($byday_arr[1] == '-')) {
																$last_day_tmp = date('t',$next_range_time);
																$next_range_time = strtotime(date('Y-m-'.$last_day_tmp, $next_range_time));
																$last_tmp = (date('w',$next_range_time) == $on_day_num) ? '' : 'last ';
																$next_date_time = strtotime($last_tmp.$on_day.' -'.$nth.' week', $next_range_time);
																$month = date('m', $next_date_time);
																if (in_array($month, $bymonth)) {
																	$recur_data[] = $next_date_time;
																}
																#reset next_range_time to start of month
																$next_range_time = strtotime(date('Y-m-'.'1', $next_range_time));

															} elseif (isset($bymonthday) && (!empty($bymonthday))) {
																// This supports MONTHLY where BYDAY and BYMONTH are both set
																foreach($bymonthday as $day) {
																	$year 	= date('Y', $next_range_time);
																	$month 	= date('m', $next_range_time);
																	if (checkdate($month,$day,$year)) {
																		$next_date_time = mktime(0,0,0,$month,$day,$year);
																		$daday = strtolower(strftime("%a", $next_date_time));
																		if ($daday == $on_day && in_array($month, $bymonth)) {
																			$recur_data[] = $next_date_time;
																		}
																	}
																}
															} elseif ((isset($byday_arr[1])) && ($byday_arr[1] != '-')) {
																$next_date_time = strtotime($on_day.' +'.$nth.' week', $next_range_time);
																$month = date('m', $next_date_time);
																if (in_array($month, $bymonth)) {
																	$recur_data[] = $next_date_time;
																}
															}
															$next_date = date('Ymd', $next_date_time);
														}
													}
													break;
												case 'YEARLY':
													if ((!isset($bymonth)) || (sizeof($bymonth) == 0)) {
														$m = date('m', $start_date_time);
														$bymonth = array("$m");
													}	

													foreach($bymonth as $month) {
														// Make sure the month & year used is within the start/end_range.
														if ($month < date('m', $next_range_time)) {
															$year = date('Y', strtotime('+1 years', $next_range_time));
														} else {
															$year = date('Y', $next_range_time);
														}
														if (isset($bysetpos)){
															/* bysetpos code from dustinbutler
															start on day 1 or last day. 
															if day matches any BYDAY the count is incremented. 
															SETPOS = 4, need 4th match 
															SETPOS = -1, need 1st match 
															*/ 
															if ($bysetpos > 0) { 
																$next_day = '+1 day'; 
																$day = 1; 
															} else { 
																$next_day = '-1 day'; 
																$day = date("t",$month); 
															} 
															$day = mktime(12, 0, 0, $month, $day, $year); 
															$countMatch = 0; 
															while ($countMatch != abs($bysetpos)) { 
																/* Does this day match a BYDAY value? */ 
																$thisDay = $day;
																$textDay = strtoupper(substr(date('D', $thisDay), 0, 2)); 
																if (in_array($textDay, $byday)) { 
																	$countMatch++; 
																} 
																$day = strtotime($next_day, $thisDay); 
															} 
															$recur_data[] = $thisDay; 															
														}
														if ((isset($byday)) && (is_array($byday))) {
															$checkdate_time = mktime(0,0,0,$month,1,$year);
															foreach($byday as $day) {
																ereg ('([-\+]{0,1})?([0-9]{1})?([A-Z]{2})', $day, $byday_arr);
																if ($byday_arr[2] != '') {
																	$nth = $byday_arr[2]-1;
																} else {
																	$nth = 0;
																}
																$on_day = two2threeCharDays($byday_arr[3]);
																$on_day_num = two2threeCharDays($byday_arr[3],false);
																if ($byday_arr[1] == '-') {
																	$last_day_tmp = date('t',$checkdate_time);
																	$checkdate_time = strtotime(date('Y-m-'.$last_day_tmp, $checkdate_time));
																	$last_tmp = (date('w',$checkdate_time) == $on_day_num) ? '' : 'last ';
																	$next_date_time = strtotime($last_tmp.$on_day.' -'.$nth.' week', $checkdate_time);
																} else {															
																	$next_date_time = strtotime($on_day.' +'.$nth.' week', $checkdate_time);
																}
															}
														} else {
															$day 	= date('d', $start_date_time);
															$next_date_time = mktime(0,0,0,$month,$day,$year);
															//echo date('Ymd',$next_date_time).$summary.'<br>';
														}
														$recur_data[] = $next_date_time;
													}
													if (isset($byyearday)) {
														foreach ($byyearday as $yearday) {
															ereg ('([-\+]{0,1})?([0-9]{1,3})', $yearday, $byyearday_arr);
															if ($byyearday_arr[1] == '-') {
																$ydtime = mktime(0,0,0,12,31,$this_year);
																$yearnum = $byyearday_arr[2] - 1;
																$next_date_time = strtotime('-'.$yearnum.' days', $ydtime);
															} else {
																$ydtime = mktime(0,0,0,1,1,$this_year);
																$yearnum = $byyearday_arr[2] - 1;
																$next_date_time = strtotime('+'.$yearnum.' days', $ydtime);
															}
															$recur_data[] = $next_date_time;
														}
													} 
													break;
												default:
													// anything else we need to end the loop
													$next_range_time = $end_range_time_tmp + 100;
													$count_to = $count;
											}
										} else {
											$interval = 1;
										}
										$next_range_time = strtotime('+'.$interval.' '.$freq_type, $next_range_time);
									} else {
										// end the loop because we aren't going to write this event anyway
										$count_to = $count;
									}
									// use the same code to write the data instead of always changing it 5 times						
									if (isset($recur_data) && is_array($recur_data)) {
										$recur_data_hour = @substr($start_time,0,2);
										$recur_data_minute = @substr($start_time,2,2);
										foreach($recur_data as $recur_data_time) {
											$recur_data_year = date('Y', $recur_data_time);
											$recur_data_month = date('m', $recur_data_time);
											$recur_data_day = date('d', $recur_data_time);
											$recur_data_date = $recur_data_year.$recur_data_month.$recur_data_day;

											if (($recur_data_time > $start_date_time) && ($recur_data_time <= $end_date_time) && ($count_to != $count) && !in_array($recur_data_date, $except_dates)) {
												if (isset($allday_start) && $allday_start != '') {
													$start_time2 = $recur_data_time;
													$end_time2 = strtotime('+'.$diff_allday_days.' days', $recur_data_time);
													while ($start_time2 < $end_time2) {
														$start_date2 = date('Ymd', $start_time2);
														$master_array[($start_date2)][('-1')][$uid] = array ('event_text' => $summary, 'description' => $description, 'location' => $location, 'organizer' => serialize($organizer), 'attendee' => serialize($attendee), 'calnumber' => $calnumber, 'calname' => $actual_calname, 'url' => $url, 'status' => $status, 'class' => $class, 'recur' => $recur );
														$start_time2 = strtotime('+1 day', $start_time2);
													}
												} else {
													$start_unixtime_tmp = mktime($recur_data_hour,$recur_data_minute,0,$recur_data_month,$recur_data_day,$recur_data_year);
													$end_unixtime_tmp = $start_unixtime_tmp + $length;
													
													if (($end_time >= $bleed_time) && ($bleed_check == '-1')) {
														$start_tmp = strtotime(date('Ymd',$start_unixtime_tmp));
														$end_date_tmp = date('Ymd',$end_unixtime_tmp);
														while ($start_tmp < $end_unixtime_tmp) {
															$start_date_tmp = date('Ymd',$start_tmp);
															if ($start_date_tmp == $recur_data_year.$recur_data_month.$recur_data_day) {
																$time_tmp = $hour.$minute;
																$start_time_tmp = $start_time;
															} else {
																$time_tmp = '0000';
																$start_time_tmp = '0000';
															}
															if ($start_date_tmp == $end_date_tmp) {
																$end_time_tmp = $end_time;
															} else {
																$end_time_tmp = '2400';
																$display_end_tmp = $end_time;
															}
															
															// Let's double check the until to not write past it
															$until_check = $start_date_tmp.$time_tmp.'00';
															if ($abs_until > $until_check) {
																$master_array[$start_date_tmp][$time_tmp][$uid] = array (
																	'event_start' => $start_time_tmp, 
																	'event_end' => $end_time_tmp, 
																	'start_unixtime' => $start_unixtime_tmp, 
																	'end_unixtime' => $end_unixtime_tmp, 
																	'event_text' => $summary, 
																	'event_length' => $length, 
																	'event_overlap' => 0, 
																	'description' => $description, 
																	'status' => $status, 
																	'class' => $class, 
																	'spans_day' => true, 
																	'location' => $location, 
																	'organizer' => serialize($organizer), 
																	'attendee' => serialize($attendee), 
																	'calnumber' => $calnumber, 
																	'calname' => $actual_calname, 
																	'url' => $url, 
																	'recur' => $recur);
																if (isset($display_end_tmp)){
																	$master_array[$start_date_tmp][$time_tmp][$uid]['display_end'] = $display_end_tmp;
																}
																checkOverlap($start_date_tmp, $time_tmp, $uid);
															}
															$start_tmp = strtotime('+1 day',$start_tmp);
														}
													} else {
														if ($bleed_check == '-1') {
															$display_end_tmp = $end_time;
															$end_time_tmp1 = '2400';
																
														}
														if (!isset($end_time_tmp1)) $end_time_tmp1 = $end_time;
													
														// Let's double check the until to not write past it
														$until_check = $recur_data_date.$hour.$minute.'00';
														if ($abs_until > $until_check) {
															$master_array[($recur_data_date)][($hour.$minute)][$uid] = array (
																'event_start' => $start_time, 
																'event_end' => $end_time_tmp1, 
																'start_unixtime' => $start_unixtime_tmp, 
																'end_unixtime' => $end_unixtime_tmp, 
																'event_text' => $summary, 
																'event_length' => $length, 
																'event_overlap' => 0, 
																'description' => $description, 
																'status' => $status, 
																'class' => $class, 
																'spans_day' => false, 
																'location' => $location, 
																'organizer' => serialize($organizer), 
																'attendee' => serialize($attendee), 
																'calnumber' => $calnumber, 
																'calname' => $actual_calname, 
																'url' => $url, 
																'recur' => $recur);
															if (isset($display_end_tmp)){
																$master_array[($recur_data_date)][($hour.$minute)][$uid]['display_end'] = $display_end_tmp;
															}
															checkOverlap($recur_data_date, ($hour.$minute), $uid);
														}
													}
												}
											}
										}
										unset($recur_data);
									}
								}
							}
						}	
					}
				}

				// This should remove any exdates that were missed.
				// Added for version 0.9.5 modified in 2.22 remove anything that doesn't have an event_start
				if (is_array($except_dates)) {
					foreach ($except_dates as $key => $value) {
						if (isset ($master_array[$value])){
							foreach ($master_array[$value] as $time => $value2){
								if (!isset($value2[$uid]['event_start'])){
						unset($master_array[$value][$time][$uid]);
							}
						}
					}
				}
				}
				
			   // Clear event data now that it's been saved.
			   unset($start_time, $start_time_tmp, $end_time, $end_time_tmp, $start_unixtime, $start_unixtime_tmp, $end_unixtime, $end_unixtime_tmp, $summary, $length, $description, $status, $class, $location, $organizer, $attendee);

				break;
			case 'END:VTODO':
				if ((!$vtodo_priority) && ($status == 'COMPLETED')) {
					$vtodo_sort = 11;
				} elseif (!$vtodo_priority) { 
					$vtodo_sort = 10;
				} else {
					$vtodo_sort = $vtodo_priority;
				}
				
				// CLASS support
				if (isset($class)) {
					if ($class == 'PRIVATE') {
						$summary = '**PRIVATE**';
						$description = '**PRIVATE**';
					} elseif ($class == 'CONFIDENTIAL') {
						$summary = '**CONFIDENTIAL**';
						$description = '**CONFIDENTIAL**';
					}
				}
				
				$master_array['-2']["$vtodo_sort"]["$uid"] = array ('start_date' => $start_date, 'start_time' => $start_time, 'vtodo_text' => $summary, 'due_date'=> $due_date, 'due_time'=> $due_time, 'completed_date' => $completed_date, 'completed_time' => $completed_time, 'priority' => $vtodo_priority, 'status' => $status, 'class' => $class, 'categories' => $vtodo_categories, 'description' => $description, 'calname' => $actual_calname);
				unset ($start_date, $start_time, $due_date, $due_time, $completed_date, $completed_time, $vtodo_priority, $status, $class, $vtodo_categories, $summary, $description);
				$vtodo_set = FALSE;
				
				break;
				
			case 'BEGIN:VTODO':
				$vtodo_set = TRUE;
				break;
			case 'BEGIN:VALARM':
				$valarm_set = TRUE;
				break;
			case 'END:VALARM':
				$valarm_set = FALSE;
				break;
				
			default:
		
				unset ($field, $data, $prop_pos, $property);
				if (ereg ("([^:]+):(.*)", $line, $line)){
				$field = $line[1];
				$data = $line[2];
				
				$property = $field;
				$prop_pos = strpos($property,';');
				if ($prop_pos !== false) $property = substr($property,0,$prop_pos);
				$property = strtoupper($property);
				
				switch ($property) {
					
					// Start VTODO Parsing
					//
					case 'DUE':
						$datetime = extractDateTime($data, $property, $field);
						$due_date = $datetime[1];
						$due_time = $datetime[2];
						break;
						
					case 'COMPLETED':
						$datetime = extractDateTime($data, $property, $field);
						$completed_date = $datetime[1];
						$completed_time = $datetime[2];
						break;
						
					case 'PRIORITY':
						$vtodo_priority = "$data";
						break;
						
					case 'STATUS':
						$status = "$data";
						break;
						
					case 'CLASS':
						$class = "$data";
						break;
						
					case 'CATEGORIES':
						$vtodo_categories = "$data";
						break;
					//
					// End VTODO Parsing				
						
					case 'DTSTART':
						$datetime = extractDateTime($data, $property, $field);
						$start_unixtime = $datetime[0];
						$start_date = $datetime[1];
						$start_time = $datetime[2];
						$allday_start = $datetime[3];
						break;
						
					case 'DTEND':
						$datetime = extractDateTime($data, $property, $field);
						$end_unixtime = $datetime[0];
						$end_date = $datetime[1];
						$end_time = $datetime[2];
						$allday_end = $datetime[3];
						break;
						
					case 'EXDATE':
						$data = split(",", $data);
						foreach ($data as $exdata) {
							$exdata = str_replace('T', '', $exdata);
							$exdata = str_replace('Z', '', $exdata);
							preg_match ('/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})/', $exdata, $regs);
							$except_dates[] = $regs[1] . $regs[2] . $regs[3];
							// Added for Evolution, since they dont think they need to tell me which time to exclude.
							if (($regs[4] == '') && ($start_time != '')) { 
								$except_times[] = $start_time;
							} else {
								$except_times[] = $regs[4] . $regs[5];
							}
						}
						break;
						
					case 'SUMMARY':
						$data = str_replace("\\n", "<br />", $data);
						$data = str_replace("\\t", "&nbsp;", $data);
						$data = str_replace("\\r", "<br />", $data);
						$data = str_replace('$', '&#36;', $data);
						$data = stripslashes($data);
						$data = htmlentities(urlencode($data));
						if ($valarm_set == FALSE) { 
							$summary = $data;
						} else {
							$valarm_summary = $data;
						}
						break;
						
					case 'DESCRIPTION':
						$data = str_replace("\\n", "<br />", $data);
						$data = str_replace("\\t", "&nbsp;", $data);
						$data = str_replace("\\r", "<br />", $data);
						$data = str_replace('$', '&#36;', $data);
						$data = stripslashes($data);
						$data = htmlentities(urlencode($data));
						if ($valarm_set == FALSE) { 
							$description = $data;
						} else {
							$valarm_description = $data;
						}
						break;
						
					case 'RECURRENCE-ID':
						$parts = explode(';', $field);
						foreach($parts as $part) {
							$eachval = split('=',$part);
							if ($eachval[0] == 'RECURRENCE-ID') {
								// do nothing
							} elseif ($eachval[0] == 'TZID') {
								$recurrence_id['tzid'] = $eachval[1];
							} elseif ($eachval[0] == 'RANGE') {
								$recurrence_id['range'] = $eachval[1];
							} elseif ($eachval[0] == 'VALUE') {
								$recurrence_id['value'] = $eachval[1];
							} else {
								$recurrence_id[] = $eachval[1];
							}
						}
						unset($parts, $part, $eachval);
						
						$data = str_replace('T', '', $data);
						$data = str_replace('Z', '', $data);
						ereg ('([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{0,2})([0-9]{0,2})', $data, $regs);
						$recurrence_id['date'] = $regs[1] . $regs[2] . $regs[3];
						$recurrence_id['time'] = $regs[4] . $regs[5];
			
						$recur_unixtime = mktime($regs[4], $regs[5], 0, $regs[2], $regs[3], $regs[1]);
			
						$dlst = date('I', $recur_unixtime);
						$server_offset_tmp = chooseOffset($recur_unixtime);
						if (isset($recurrence_id['tzid'])) {
							$tz_tmp = $recurrence_id['tzid'];
							$offset_tmp = $tz_array[$tz_tmp][$dlst];
						} elseif (isset($calendar_tz)) {
							$offset_tmp = $tz_array[$calendar_tz][$dlst];
						} else {
							$offset_tmp = $server_offset_tmp;
						}
						$recur_unixtime = calcTime($offset_tmp, $server_offset_tmp, $recur_unixtime);
						$recurrence_id['date'] = date('Ymd', $recur_unixtime);
						$recurrence_id['time'] = date('Hi', $recur_unixtime);
						$recurrence_d = date('Ymd', $recur_unixtime);
						$recurrence_t = date('Hi', $recur_unixtime);
						unset($server_offset_tmp);
						break;
						
					case 'UID':
						$uid = $data;
						break;
					case 'X-WR-CALNAME':
						$actual_calname = $data;
						$master_array['calendar_name'] = $actual_calname;
							$cal_displaynames[$cal_key] = $actual_calname; #correct the default calname based on filename
						break;
					case 'X-WR-TIMEZONE':
						$calendar_tz = $data;
						$master_array['calendar_tz'] = $calendar_tz;
						break;
					case 'DURATION':
						if (($first_duration == TRUE) && (!stristr($field, '=DURATION'))) {
							ereg ('^P([0-9]{1,2}[W])?([0-9]{1,2}[D])?([T]{0,1})?([0-9]{1,2}[H])?([0-9]{1,2}[M])?([0-9]{1,2}[S])?', $data, $duration); 
							$weeks 			= str_replace('W', '', $duration[1]); 
							$days 			= str_replace('D', '', $duration[2]); 
							$hours 			= str_replace('H', '', $duration[4]); 
							$minutes 		= str_replace('M', '', $duration[5]); 
							$seconds 		= str_replace('S', '', $duration[6]); 
							$the_duration 	= ($weeks * 60 * 60 * 24 * 7) + ($days * 60 * 60 * 24) + ($hours * 60 * 60) + ($minutes * 60) + ($seconds);
							$first_duration = FALSE;
						}	
						break;
					case 'RRULE':
						$data = str_replace ('RRULE:', '', $data);
						$rrule = split (';', $data);
						foreach ($rrule as $recur) {
							ereg ('(.*)=(.*)', $recur, $regs);
							$rrule_array[$regs[1]] = $regs[2];
						}
						break;
					case 'ATTENDEE':
						$field 		= str_replace("ATTENDEE;CN=", "", $field);
						$data 		= str_replace ("mailto:", "", $data);
						$attendee[] = array ('name' => stripslashes($field), 'email' => stripslashes($data));
						break;
					case 'ORGANIZER':
						$field 		 = str_replace("ORGANIZER;CN=", "", $field);
						$data 		 = str_replace ("mailto:", "", $data);
						$organizer[] = array ('name' => stripslashes($field), 'email' => stripslashes($data));
						break;
					case 'LOCATION':
						$data = str_replace("\\n", "<br />", $data);
						$data = str_replace("\\t", "&nbsp;", $data);
						$data = str_replace("\\r", "<br />", $data);
						$data = stripslashes($data);
						$location = $data;
						break;
					case 'URL':
						$url = $data;
						break;
				}
			}
		}
	}
	}
	if (!isset($master_array['-3'][$calnumber])) $master_array['-3'][$calnumber] = $actual_calname;
	if (!isset($master_array['-4'][$calnumber]['mtime'])) $master_array['-4'][$calnumber]['mtime'] = $actual_mtime;
	if (!isset($master_array['-4'][$calnumber]['filename'])) $master_array['-4'][$calnumber]['filename'] = $filename;
	if (!isset($master_array['-4'][$calnumber]['webcal'])) $master_array['-4'][$calnumber]['webcal'] = 'no';
	$calnumber = $calnumber + 1;
}

if ($parse_file) {	
	// Sort the array by absolute date.
	if (isset($master_array) && is_array($master_array)) { 
		ksort($master_array);
		reset($master_array);
		
		// sort the sub (day) arrays so the times are in order
		foreach (array_keys($master_array) as $k) {
			if (isset($master_array[$k]) && is_array($master_array[$k])) {
				ksort($master_array[$k]);
				reset($master_array[$k]);
			}
		}
	}
	
	// write the new master array to the file
	if (isset($master_array) && is_array($master_array) && $save_parsed_cals == 'yes') {
		$write_me = serialize($master_array);
		$fd = @fopen($parsedcal, 'w');
		if ($fd == FALSE) exit(error($lang['l_error_cache'], $filename));
		@fwrite($fd, $write_me);
		@fclose($fd);
		@touch($parsedcal, $realcal_mtime);
	}
}


// Set a calender name for all calenders combined
if ($cal == $ALL_CALENDARS_COMBINED) {
	$calendar_name = $all_cal_comb_lang;
}
$cal_displayname = urldecode(implode(', ', $cal_displaynames)); #reset this with the correct names
$template_started = getmicrotime();

//If you want to see the values in the arrays, uncomment below.

//print '<pre>';
//print_r($master_array);
//print_r($overlap_array);
//print_r($day_array);
//print_r($rrule_array);
//print_r($recurrence_delete);
//print_r($cal_displaynames);
//print_r($cal_filelist);
//print '</pre>';
?>
