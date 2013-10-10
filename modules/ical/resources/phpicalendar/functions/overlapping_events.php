<?php
// function to determine maximum necessary columns per day
// actually an algorithm to get the smallest multiple for two numbers
function kgv($a, $b) {
	$x = $a;
	$y = $b;
	while ($x != $y) {
		if ($x < $y) $x += $a;
		else $y += $b;
	}
	return $x;
}

// merge a given range into $ol_ranges. Returns the merged $ol_ranges.
// if count = -2, treat as a "delete" call (for removeOverlap)
// Why -2? That way, there's less fudging of the math in the code.
function merge_range($ol_ranges, $start, $end, $count = 0) {

	foreach ($ol_ranges as $loop_range_key => $loop_range) {
		
		if ($start < $end) {
			// handle ranges between $start and $loop_range['start']
			if ($start < $loop_range['start']) {
				$new_ol_ranges[] = array('count' => $count, 'start' => $start, 'end' => min($loop_range['start'], $end));
				$start = $loop_range['start'];
			}

			// $start is always >= $loop_range['start'] at this point.
			// handles ranges between $loop_range['start'] and $loop_range['end']
			if ($loop_range['start'] < $end && $start < $loop_range['end']) {
				// handles ranges between $loop_range['start'] and $start
				if ($loop_range['start'] < $start) {
					$new_ol_ranges[] = array('count' => $loop_range['count'], 'start' => $loop_range['start'], 'end' => $start);
				}
				// handles ranges between $start and $end (where they're between $loop_range['start'] and $loop_range['end'])
				$new_count = $loop_range['count'] + $count + 1;
				if ($new_count >= 0) {
					$new_ol_ranges[] = array('count' => $new_count, 'start' => $start, 'end' => min($loop_range['end'], $end));
				}
				// handles ranges between $end and $loop_range['end']
				if ($loop_range['end'] > $end) {
					$new_ol_ranges[] = array('count' => $loop_range['count'], 'start' => $end, 'end' => $loop_range['end']);
				}
				$start = $loop_range['end'];
			} else {
				$new_ol_ranges[] = $loop_range;
			}
		} else {
			$new_ol_ranges[] = $loop_range;
		}
	}

	// Catches anything left over.
	if ($start < $end) {
		$new_ol_ranges[] = array('count' => $count, 'start' => $start, 'end' => $end);
	}

	return $new_ol_ranges;
}

// Finds the highest value of 'count' in $ol_ranges
function find_max_overlap($ol_ranges) {

	$count = 0;
	foreach ($ol_ranges as $loop_range) {
		if ($count < $loop_range['count'])
			$count = $loop_range['count'];
	}

	return $count;
}

// Merges overlapping blocks
function flatten_ol_blocks($event_date, $ol_blocks, $new_block_key) {

	global $master_array;

	// Loop block = each other block in the array, the ones we're merging into new block.
	// New block = the changed block that caused the flatten_ol_blocks call. Everything gets merged into this.
	$new_block = $ol_blocks[$new_block_key];
	reset($ol_blocks);
	while ($loop_block_array = each($ol_blocks)) {
		$loop_block_key = $loop_block_array['key'];
		$loop_block = $loop_block_array['value'];
		// only compare with other blocks
		if ($loop_block_key != $new_block_key) {
			// check if blocks overlap
			if (($loop_block['blockStart'] < $new_block['blockEnd']) && ($loop_block['blockEnd'] > $new_block['blockStart'])) {
				// define start and end of merged overlap block
				if ($new_block['blockStart'] > $loop_block['blockStart']) $ol_blocks[$new_block_key]['blockStart'] = $loop_block['blockStart'];
				if ($new_block['blockEnd'] < $loop_block['blockEnd']) $ol_blocks[$new_block_key]['blockEnd'] = $loop_block['blockEnd'];
				$ol_blocks[$new_block_key]['events'] = array_merge($new_block['events'], $loop_block['events']);
				foreach ($loop_block['overlapRanges'] as $ol_range) {
					$new_block['overlapRanges'] = merge_range($new_block['overlapRanges'], $ol_range['start'], $ol_range['end'], $ol_range['count']);
				}
				$ol_blocks[$new_block_key]['overlapRanges'] = $new_block['overlapRanges'];
				$ol_blocks[$new_block_key]['maxOverlaps'] = find_max_overlap($new_block['overlapRanges']);
				foreach ($ol_blocks[$new_block_key]['events'] as $event) {
					$master_array[$event_date][$event['time']][$event['key']]['event_overlap'] = $ol_blocks[$new_block_key]['maxOverlaps'];
				}
				unset($ol_blocks[$loop_block_key]);
				reset($ol_blocks);
			}
		} 
	}

	return $ol_blocks;
}

// Builds $overlap_array structure, and updates event_overlap in $master_array for the given events.
function checkOverlap($event_date, $event_time, $uid) {
	global $master_array, $overlap_array;
	if (!isset($event_date)) return;
	$event = $master_array[$event_date][$event_time][$uid];
	// Copy out the array - we replace this at the end.
	$ol_day_array = $overlap_array[$event_date];
	$drawTimes = drawEventTimes($event['event_start'], $event['event_end']);

	// For a given date,
	// 	- check to see if the event's already in a block, and if so, add it.
	//		- make sure the new block doesn't overlap another block, and if so, merge the blocks.
	// - check that there aren't any events we already passed that we should handle.
	//		- "flatten" the structure again, merging the blocks.

	// $overlap_array structure:
	//	array of ($event_dates)
	//		array of unique overlap blocks (no index) -

	// $overlap_block structure
	// 'blockStart'    - $start_time of block - earliest $start_time of the events in the block. 
	//					 Shouldn't be any overlap w/ a different overlap block in that day (as if they overlap, they get merged).
	// 'blockEnd'      - $end_time of block - latest $end_time of the events in the block.
	// 'maxOverlaps'   - max number of overlaps for the whole block (highest 'count' in overlapRanges)
	// 'events'        - array of event "pointers" (no index) - each event in the block.
	//		'time' - $start_time of event in the block
	//		'key'  - $uid of event
	// 'overlapRanges' - array of time ranges + overlap counts (no index) - the specific overlap info.
	//					 Shouldn't be any overlap w/ the overlap ranges in a given overlap_block - if there is overlap, the block should be split.
	//		'count' - number of overlaps that time range (can be zero if that range has no overlaps).
	//		'start' - start_time for the overlap block.
	//		'end'	- end_time for the overlap block.

	$ol_day_array = $overlap_array[$event_date];
	// Track if $event has been merged in, so we don't re-add the details to 'event' or 'overlapRanges' multiple times.
	$already_merged_once = false;
	// First, check the existing overlap blocks, see if the event overlaps with any.
	if (isset($ol_day_array)) {
		foreach ($ol_day_array as $loop_block_key => $loop_ol_block) {
			// Should $event be in this $ol_block? If so, add it.
			if ($loop_ol_block['blockStart'] < $drawTimes['draw_end'] && $loop_ol_block['blockEnd'] > $drawTimes['draw_start']) {
				// ... unless it's already in the $ol_block
				if (!in_array(array('time' => $drawTimes['draw_start'], 'key' => $uid), $loop_ol_block['events'])) {
					$loop_ol_block['events'][] = array('time' => $drawTimes['draw_start'], 'key' => $uid);
					if ($loop_ol_block['blockStart'] > $drawTimes['draw_start']) $loop_ol_block['blockStart'] = $drawTimes['draw_start'];
					if ($loop_ol_block['blockEnd'] < $drawTimes['draw_end']) $loop_ol_block['blockEnd'] = $drawTimes['draw_end'];

					// Merge in the new overlap range
					$loop_ol_block['overlapRanges'] = merge_range($loop_ol_block['overlapRanges'], $drawTimes['draw_start'], $drawTimes['draw_end']);
					$loop_ol_block['maxOverlaps'] = find_max_overlap($loop_ol_block['overlapRanges']);
					foreach ($loop_ol_block['events'] as $max_overlap_event) {
						$master_array[$event_date][$max_overlap_event['time']][$max_overlap_event['key']]['event_overlap'] = $loop_ol_block['maxOverlaps'];
					}
					$ol_day_array[$loop_block_key] = $loop_ol_block;
					$ol_day_array = flatten_ol_blocks($event_date, $ol_day_array, $loop_block_key);
					$already_merged_once = true;
					break;
				// Handle repeat calls to checkOverlap - semi-bogus since the event shouldn't be created more than once, but this makes sure we don't get an invalid event_overlap.
				} else {
					$master_array[$event_date][$event_time][$uid]['event_overlap'] = $loop_ol_block['maxOverlaps'];
				}
			}
		}
	}

	// Then, check all the events, make sure there isn't a new overlap that we need to create.
	foreach ($master_array[$event_date] as $time_key => $time) {
		// Skip all-day events for overlap purposes.
		if ($time_key != '-1') {
			foreach ($time as $loop_event_key => $loop_event) {
				// Make sure we haven't already dealt with the event, and we're not checking against ourself.
				if ($loop_event['event_overlap'] == 0 && $loop_event_key != $uid) {
					$loopDrawTimes = drawEventTimes($loop_event['event_start'], $loop_event['event_end']);
					if ($loopDrawTimes['draw_start'] < $drawTimes['draw_end'] && $loopDrawTimes['draw_end'] > $drawTimes['draw_start']) {
						if ($loopDrawTimes['draw_start'] < $drawTimes['draw_start']) {
							$block_start = $loopDrawTimes['draw_start'];
						} else {
							$block_start = $drawTimes['draw_start'];
						}
						if ($loopDrawTimes['draw_end'] > $drawTimes['draw_end']) {
							$block_end = $loopDrawTimes['draw_end'];
						} else {
							$block_end = $drawTimes['draw_end'];
						}
						$events = array(array('time' => $loopDrawTimes['draw_start'], 'key' => $loop_event_key));
						$overlap_ranges = array(array('count' => 0, 'start' => $loopDrawTimes['draw_start'], 'end' => $loopDrawTimes['draw_end']));
						// Only add $event if we haven't already put it in a block
						if (!$already_merged_once) {
							$events[] = array('time' => $drawTimes['draw_start'], 'key' => $uid); 
							$overlap_ranges = merge_range($overlap_ranges, $drawTimes['draw_start'], $drawTimes['draw_end']);
							$already_merged_once = true;
						}
						$ol_day_array[] = array('blockStart' => $block_start, 'blockEnd' => $block_end, 'maxOverlaps' => 1, 'events' => $events, 'overlapRanges' => $overlap_ranges);

						foreach ($events as $max_overlap_event) {
							$master_array[$event_date][$max_overlap_event['time']][$max_overlap_event['key']]['event_overlap'] = 1;
						}
						// Make sure we pass in the key of the newly added item above.
						end($ol_day_array);
						$last_day_key = key($ol_day_array);
						$ol_day_array = flatten_ol_blocks($event_date, $ol_day_array, $last_day_key);
					}
				}
			}
		}
	}

	$overlap_array[$event_date] = $ol_day_array;

//for debugging the checkOverlap function
//if ($event_date == '20050506') {
//print 'Date: ' . $event_date . ' / Time: ' . $event_time . ' / Key: ' . $uid . "<br />\n";
//print '<pre>';
//print_r($master_array[$event_date]);
//print_r($overlap_array[$event_date]);
//print '</pre>';
//}

}

// Remove an event from the overlap data.
// This could be completely bogus, since overlap array is empty when this gets called in my tests, but I'm leaving it in anyways.
function removeOverlap($ol_start_date, $ol_start_time, $ol_key) {
	global $master_array, $overlap_array;
	if (isset($overlap_array[$ol_start_date])) {
		if (sizeof($overlap_array[$ol_start_date]) > 0) {
			$ol_end_time = $master_array[$ol_start_date][$ol_start_time][$ol_key]['event_end'];
			foreach ($overlap_array[$ol_start_date] as $block_key => $block) {
				if (in_array(array('time' => $ol_start_time, 'key' => $ol_key), $block['events'])) {
					// Check if this is a 2-event block (i.e., there's no block left when we remove $ol_key
					// and if so, just unset it and move on.
					if (count($block['events']) == 2) {
						foreach ($block['events'] as $event) {
							$master_array[$ol_start_date][$event['time']][$event['key']]['event_overlap'] = 0;
						}
						unset($overlap_array[$ol_start_date][$block_key]);
					} else {
						// remove $ol_key from 'events'
						$event_key = array_search(array('time' => $ol_start_time, 'key' => $ol_key), $block['events']);
						unset($overlap_array[$ol_start_date][$block_key]['events'][$event_key]);

						// These may be bogus, since we're not using drawEventTimes.
						// "clean up" 'overlapRanges' and calc the new maxOverlaps.
						// use the special "-2" count to tell merge_range we're deleting.
						$overlap_array[$ol_start_date][$block_key]['overlapRanges'] = merge_range($block['overlapRanges'], $ol_start_time, $ol_end_time, -2);
						$overlap_array[$ol_start_date][$block_key]['maxOverlaps'] = find_max_overlap($block['overlapRanges']);

						// recreate blockStart and blockEnd from the other events, and fix maxOverlap while we're at it.
						$blockStart = $ol_end_time;
						$blockEnd = $ol_start_time;
						foreach ($overlap_array[$ol_start_date][$block_key]['events'] as $event) {
							$blockStart = min($blockStart, $event['time']);
							$blockEnd = max($blockEnd, $master_array[$ol_start_date][$event['time']][$event['key']]['event_end']);
							$master_array[$ol_start_date][$event['time']][$event['key']]['event_overlap'] = $overlap_array[$ol_start_date][$block_key]['maxOverlaps'];
						}
						$overlap_array[$ol_start_date][$block_key]['blockStart'] = $blockStart;
						$overlap_array[$ol_start_date][$block_key]['blockEnd'] = $blockEnd;
					}
				}
			}
		}
	}
}
?>
