<?php
/**
 * A class for making time periods readable.
 *
 * This class allows for the conversion of an integer
 * number of seconds into a readable string.
 * For example, '121' into '2 minutes, 1 second'.
 * 
 * If an array is passed to the class, the associative
 * keys are used for the names of the time segments.
 * For example, array('seconds' => 12, 'minutes' => 1)
 * into '1 minute, 12 seconds'.
 *
 * This class is plural aware. Time segments with values
 * other than 1 will have an 's' appended.
 * For example, '1 second' not '1 seconds'.
 *
 * @author      Aidan Lister <aidan@php.net>
 * @author      Paul Scott (Chisimba integration)
 * @version     1.2.1
 * @link        http://aidanlister.com/repos/v/Duration.php
 */
class duration extends object
{

	public function init()
	{
		
	}
	
	/**
     * All in one method
     *
     * @param   int|array  $duration  Array of time segments or a number of seconds
     * @return  string
     */
    function toString ($duration, $periods = NULL)
    {
        if (!is_array($duration)) {
            $duration = Duration::int2array($duration, $periods);
        }
 
        return Duration::array2string($duration);
    }
 
 
    /**
     * Return an array of date segments.
     *
     * @param        int $seconds Number of seconds to be parsed
     * @return       mixed An array containing named segments
     */
    function int2array ($seconds, $periods = NULL)
    {        
        // Define time periods
        if (!is_array($periods)) {
            $periods = array (
                    'years'     => 31556926,
                    'months'    => 2629743,
                    'weeks'     => 604800,
                    'days'      => 86400,
                    'hours'     => 3600,
                    'minutes'   => 60,
                    'seconds'   => 1
                    );
        }
 
        // Loop
        $seconds = (float) $seconds;
        foreach ($periods as $period => $value) {
            $count = floor($seconds / $value);
 
            if ($count == 0) {
                continue;
            }
 
            $values[$period] = $count;
            $seconds = $seconds % $value;
        }
 
        // Return
        if (empty($values)) {
            $values = NULL;
        }
 
        return $values;
    }
 
 
    /**
     * Return a string of time periods.
     *
     * @package      Duration
     * @param        mixed $duration An array of named segments
     * @return       string
     */
    function array2string ($duration)
    {
        if (!is_array($duration)) {
            return FALSE;
        }
 
        foreach ($duration as $key => $value) {
            $segment_name = substr($key, 0, -1);
            $segment = $value . ' ' . $segment_name; 
 
            // Plural
            if ($value != 1) {
                $segment .= 's';
            }
 
            $array[] = $segment;
        }
 
        $str = implode(', ', $array);
        return $str;
    }
 
}
 
?>