<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

/**
 * apache log file parser class
 * This is a utility class that may be useful. It was written for the apache log module that has now been removed from cvs.
 *
 * @category  Chisimba
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @package utilities
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class logparser extends object
{
    /**
     * The file object from filemanager
     *
     * @var object
     */
    public $objFile;

    /**
     * Standard init function
     *
     * @param void
     * @return void
     * @access public
     */
    public function init()
    {
        $this->objFile = $this->getObject('dbfile', 'filemanager');
    }

    /**
     * Method to convert a logfile to an array to manipulate
     *
     * @param string filename $file
     * @return array
     * @access public
     */
    public function log2arr($file)
    {
        $fpath = $this->objFile->getFullFilePath($file);
        $file = file($fpath);
        return $file;
    }

    /**
     * Get the statistics from the file manager
     *
     * @param string $file
     * @return array
     */
    public function logfileStats($file)
    {
        $fname = $this->objFile->getFileName($file);
        $fsize = $this->objFile->getFileSize($file);
        $fpath = $this->objFile->getFullFilePath($file);

        return array('filesize' => $fsize, 'filename' => $fname, 'filepath' => $fpath);
    }

    /**
     * Method to parse and glean info from an apache2 logfile entry
     *
     * @param string $line
     * @return array
     * @access public
     */
    public function parselogEntry($line)
    {
        $stuff = explode('"',$line);
        //unset the blanks
        unset($stuff[4]);
        unset($stuff[6]);
        //split the first line into ip and date
        $comps = explode(" ", $stuff[0]);
        $ip = $comps[0];
        $date = $comps[3].$comps[4];

        //fix up the date to be more readable
        $date = str_replace("[","",$date);
        $date = str_replace("]","",$date);

        $date = $this->fixDates($date);
        $ts = strtotime($date);
        $request = $stuff[1];
        $servercode = $stuff[2];
        $requrl = $stuff[3];
        $useragent = $stuff[5];

        $requestarr = array('fullrecord' => $line, 'ip' => $ip, 'date' => $date, 'ts' => $ts, 'request' => $request, 'servercode' => $servercode, 'requrl' => $requrl, 'useragent' => $useragent);

        return $requestarr;
    }

    /**
     * Method to fix the dates that appear in std format in the logfiles
     *
     * @param string $datetime
     * @return ISO formatted date
     * @access private
     */
    private function fixDates($datetime)
    {
        $datetime = explode("/", $datetime);

        $day = $datetime[0];
        $month = $datetime[1];
        $yearandtime = $datetime[2];
        $yndarr = explode(":", $yearandtime);
        $year = $yndarr[0];
        $hours = $yndarr[1];
        $minutes = $yndarr[2];
        $secsandtz = $yndarr[3];

        $sarr = explode("+",$secsandtz);
        $seconds = $sarr[0];
        $tz = $sarr[1];

        $datestring = $day . " " . $month . " " . $year . " " . $hours . ":" . $minutes . ":" . $seconds . " " . "+" . $tz;
        $date = strtotime($datestring);
        $ref = date('r', $date);

        return $ref;
    }
}
?>