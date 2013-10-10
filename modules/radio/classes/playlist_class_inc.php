<?php

/**
 * Short description for file
 *
 * This file handles everything to do with the playlists.
 * This inclused compiling the playlist,delete,update and queries
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   radio
 * @author    Prince Mbekwa <pmbekwa@uwc.ac.za>
 * @copyright 2007
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: playlist_class_inc.php 11948 2008-12-29 21:28:04Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */

/**
 * Short description for class
 *
 * Class handles playlist manipulation
 *
 * @category  Chisimba
 * @package   radio
 * @author    Prince Mbekwa <pmbekwa@uwc.ac.za>
 * @copyright 2007
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: playlist_class_inc.php 11948 2008-12-29 21:28:04Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */

class playlist extends object
{


    /**
     * Description for public
     * File where the playlist is located
     * @var    string
     * @access public
     */
	public $playlist_src;


    /**
     * Short description for public
     *
     * Loading of the source
     *
     * @return void
     * @access public
     */
    public function init()
    {
    	$this->playlist_src = $this->getResourcePath('includes/playlist/','radio');

    }

    /**
     * Short description for public
     *
     * Method to create a playlist
     *
     * @param  string $station_name  Parameter current station
     * @param  string $playlist_name Parameter playlist
     * @return string Return description (if any) ...
     * @access public
     */
	public function creat_playlist($station_name = "0", $playlist_name = "0")
	{

		if ($station_name != "0" && $playlist_name != "0")
		{
			if (!is_dir($this->playlist_src.$station_name))
			{
				mkdir($this->playlist_src.$station_name, 0777);

			}
			if ($fp = fopen($this->playlist_src.$station_name."/".$playlist_name.".data", "w+"))
			{
				fclose($fp);
				return "0";
			}else {return "0";}
		}else {return "0";}

	}

    /**
     * Short description for public
     *
     * Get info about all playlists compiled for a station
     *
     * @param  string $station Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
	public function get($station = "0")
	{
		if ($station != "0")
		{
			if (!is_dir($this->playlist_src.$station))
			{
				mkdir($this->playlist_src.$station, 0777);
			}
			$data = null;
			if ($handle=opendir($this->playlist_src.$station)){
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {
						$file = str_replace(".data", "", $file);
						$data .= "$file&";

					}
				}
				closedir($handle);
			}



		}
		return $data;
	}


    /**
     * Short description for public
     *
     * Get all songs for a particular playlist
     *
     * @param  string $station Parameter description (if any) ...
     * @return mixed  Return description (if any) ...
     * @access public
     */
	public function get_playlist_list($station = "0")
	{
		if ($station != "0")
		{
			if (file_exists($this->playlist_src.$station."/".date('l').".data")) {
				return date('l');
			}
			elseif (file_exists($this->playlist_src.$station."/default.data")) {

				return "default";
			}else {
				if (!is_dir($this->playlist_src.$station))
				{
					mkdir($this->playlist_src.$station, 0777);

				}
				if ($handle=opendir("$this->playlist_src.$station")){
					while (false !== ($file = readdir($handle))) {
						if ($file != "." && $file != "..") {
							$file = str_replace(".data", "", $file);
							$data .= "$file";

						}
					}
					closedir($handle);
				}else {return 0;}
				if ($data == ""){$fp = fopen($this->playlist_src.$station."/"."default.data", "w+"); fclose($fp); $data = "default&";}
				return $data;
			}
		}else {return 0;}
	}


    /**
     * Short description for public
     *
     * Calculate the timing for songs
     *
     * @param  number  $sec      Parameter description (if any) ...
     * @param  boolean $padHours Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
	public function sec2hms ($sec, $padHours = false)
	{

	  	// holds formatted string
	  	$hms = "";

	  	// there are 3600 seconds in an hour, so if we
	  	// divide total seconds by 3600 and throw away
	  	// the remainder, we've got the number of hours
	  	$hours = intval(intval($sec) / 3600);

	  	// add to $hms, with a leading 0 if asked for
	  	$hms .= ($padHours)
	  	? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
	  	: $hours. ':';

	  	// dividing the total seconds by 60 will give us
	  	// the number of minutes, but we're interested in
	  	// minutes past the hour: to get that, we need to
	  	// divide by 60 again and keep the remainder
	  	$minutes = intval(($sec / 60) % 60);

	  	// then add to $hms (with a leading 0 if needed)
	  	$hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';

	  	// seconds are simple - just divide the total
	  	// seconds by 60 and keep the remainder
	  	$seconds = intval($sec % 60);

	  	// add to $hms, again with a leading 0 if needed
	  	$hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

	  	// done!
	  	return $hms;

	}

    /**
     * Short description for public
     *
     * Build a playlist
     *
     * @param  string $replace_what Parameter description (if any) ...
     * @param  string $replace_with Parameter description (if any) ...
     * @param  string $max          Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
	public function build_list($replace_what = "0", $replace_with= "0", $max = "0")
	{
		if ($max == ""){$max = "0";}
		$stop = "0";
		$teller = "0";
		$data = "";
		while ($stop == "0")
		{
			$data .= $teller. "&";
			if ($teller >= $max){$stop = "yes";}
			$teller++;
		}
		$data = str_replace($replace_with."&", $replace_with."Q"."&", $data);
		$data = str_replace($replace_what."&", $replace_with."&", $data);
		$data = str_replace($replace_with."Q"."&", $replace_what."&", $data);
		return $data;
	}

    /**
     * Short description for public
     *
     * Add songs to a playlist
     *
     * @param  string $file          Parameter description (if any) ...
     * @param  mixed  $time          Parameter description (if any) ...
     * @param  string $bitrate       Parameter description (if any) ...
     * @param  string $playlist_name Parameter description (if any) ...
     * @param  string $station_name  Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
	public function add_songs($file = "0", $time = "0", $bitrate = "0", $playlist_name = "0", $station_name = "0")
	{
		if ($file != "0" && $time != "0" && $bitrate != "0" && $playlist_name != "0" && $station_name != "0")
		{
			if ($playlist_name != "" && $station_name != ""){
				if (!is_dir($this->playlist_src.$station_name))
				{
					mkdir($this->playlist_src.$station_name, 0777);

				}
				if (file_exists($this->playlist_src.$station_name."/".$playlist_name.".data")) {

					$file_data = file_get_contents($this->playlist_src.$station_name."/".$playlist_name.".data");

				}else
				{
					$fp = fopen($this->playlist_src.$station_name."/".$playlist_name.".data", "w+");
					fclose($fp);
				}

				$file_data1 = explode(";", $file_data);
				$laast = count($file_data1) - 2;
				$file_data2 = explode("&", $file_data1[$laast]);
				$file_data3 = $file_data2[3];
				if ($file_data3 == "" or $file_data3 == "0"){$file_data3 = time();}
				$time = explode(":", $time);
				$min_sec = $time[0] * 60;
				$max_time = $min_sec + $time[1];
				$start_time =$file_data3 + 1;
				$end_time = $start_time + $max_time;



				if ($fp = fopen($this->playlist_src.$station_name."/".$playlist_name.".data", "a"))
				{	if (fwrite($fp, $file. "&". $bitrate. "&" .$start_time. "&" .$end_time. ";"))
				{return "1";}else {return "0";}
				fclose($fp);

				}else {return "0";}
			}else {return "0";}
		}

	}

    /**
     * Short description for public
     *
     * Delete songs in a playlist
     *
     * @param  string $number        Parameter description (if any) ...
     * @param  string $playlist_name Parameter description (if any) ...
     * @param  string $station_name  Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
	public function del_songs($number = "nothing", $playlist_name = "0", $station_name = "0")
	{
		if ($number != "nothing" && $playlist_name != "0" && $station_name != "0")
		{
			if ($fp = fopen($this->playlist_src.$station_name."/".$playlist_name.".data", "r"))
			{
				$data = fread($fp, 990000);
				fclose($fp);
				if ($fp = fopen($this->playlist_src.$station_name."/".$playlist_name.".data", "w+"))
				{
					$data_arry = explode (";", $data);
					$stop = "0";
					$teller = "0";
					$once = "0";
					while ($stop == "0")
					{
						$out = explode("&", $data_arry[$teller]);
						$file = isset($out[0]) ? $out[0] : 0;
						$bitrate = isset($out[1]) ? $out[1] : 0;
						$start_time = isset($out[2]) ? $out[2] : 0;
						$end_time = isset($out[3]) ? $out[3] : 0;
						$total_time = $end_time - $start_time;
						if ($once == "0"){$end = $start_time; $once = "1";}
						if ($file != "")
						{
							if ($teller != $number)
							{
								$end_t = $end +$total_time;
								$data_out .= $file. "&" .$bitrate. "&" .$end. "&" .$end_t .";";
								$end = $end_t;
							}
						}else
						{
							$stop = "yes";
						}
						$teller++;
					}

					if (fwrite($fp, $data_out))
					{fclose($fp); return "1";
					}else {fclose($fp); return "0";}

				}else {return "0";}
			}else {return "0";}
		}else {return "0";}
	}

    /**
     * Short description for public
     *
     * Sync and sort songs in a playlist
     *
     * @param  string $station  Parameter description (if any) ...
     * @param  string $playlist Parameter description (if any) ...
     * @param  string $volgorde Parameter description (if any) ...
     * @return void
     * @access public
     */
	public function move_songs($station = "0", $playlist = "0", $volgorde = "0")
	{

		if ($station != "0" && $playlist != "0" && $volgorde != "0")
		{
			if (file_exists($this->playlist_src.$station.'/'.$playlist.'.data')){
				clearstatcache();
				$filename = $this->playlist_src.$station."/".$playlist.".data";
				$handle = fopen($filename, "r");
				$fstat = fstat($handle);
				$data_out = fread($handle, $fstat["size"]);
				fclose($handle);
				$stop = "0";
				$out = explode(";", $data_out);
				$teller = "0";
				$once = "0";
				$teller_o = "0";
				while ($stop == "0")
				{
					$out2 = explode("&", $out[$teller]);
					$file = isset($out2[0]) ? $out2[0] : 0;
					$bitrate = isset($out2[1]) ? $out2[1] : 0;
					$start = isset($out2[2]) ? $out2[2] : 0;
					if ($once == "0"){$time_start = $start; $once = "1";}

						$end = isset($out2[3]) ? $out2[3] : 0;

					if ($file != "" && $file != "Array")
					{
						$teller_o++;
						$total_time = $end - $start;

						$file2[$teller] = $file. "&" .$bitrate. "&" .$total_time;
						$teller++;
					}else {$stop = "1";}
				}
				$stop = "0";
				$out3 = explode("&", $volgorde);
				$teller = "0";
				$data = null;
				while ($stop == "0" && $teller_o >= "1")
				{
					$out4 = explode("&", isset($file2[$out3[$teller]]) ? $file2[$out3[$teller]] : '');
					$file = isset($out4[0]) ? $out4[0] : 0;
					$biterate = isset($out4[1]) ? $out4[1] : 0;
					$total_time = isset($out4[2]) ? $out4[2] : 0;
					$start = $time_start;
					$end = $start + $total_time;
					$time_start = $end;
					if ($file != "" && $file != "Array")
					{
						$data .= $file. "&". $biterate. "&" .$start. "&" .$end. ";";
					}else {$stop = "1";}
					$teller++;
				}
				$fp = fopen($this->playlist_src.$station."/".$playlist.".data", "w+");
				fwrite($fp, $data);
				fclose($fp);
				return true;
			}
		}
	}

    /**
     * Short description for public
     *
     * delete a playlist
     *
     * @param  string  $station_name  Parameter description (if any) ...
     * @param  string  $playlist_name Parameter description (if any) ...
     * @return integer Return description (if any) ...
     * @access public
     */
	public function del_playlist($station_name = "0", $playlist_name = "0")
	{
		if ($station_name != "0" && $playlist_name != "0")
		{
			if (unlink($this->playlist_src.$station_name."/".$playlist_name.".data"))
			{
				return 1;
			}else {return 0;}

		}
	}


    /**
     * Short description for public
     *
     * Force-reload a list
     *
     * @param  string  $station  Parameter description (if any) ...
     * @param  string  $playlist Parameter description (if any) ...
     * @param  boolean $debug    Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public
     */
	public function reload($station = "0", $playlist = "0",$debug=false)
	{
		if($debug){echo "Start reloading!<br>";}
		if ($station != "0" && $playlist != "0"){
			$file2 = "$this->playlist_src$station/$playlist.data";
			if($debug){echo "reloading! [$station - $playlist]<br>";}
			if (file_exists($file2)) {
				if($debug){echo "reloading file ok!<br>";}
				$fp = fopen($file2, 'rb');
				$filedata = fread($fp, 262144);
				fclose($fp);
				$clock = time();

				unlink($file2);
				$fp4 = fopen($file2, 'w+b');
				$filedata2 = explode(";", $filedata);
				$content = null;
				$stop = "";
				$teller = 0;
				$laast_end = "";
				while ($stop == ""){
					$newtime_end = "";
					$newtime_start = "";
					$out = explode("&", $filedata2[$teller]);
					$songname = isset($out[0]) ? $out[0] : 0;
					$kbps = isset($out[1]) ? $out[1] : 0;
					$time = isset($out[3]) ? $out[3] : 0 - isset($out[2]) ? $out[2] : 0;
					if ($laast_end == ""){$laast_end = $clock;}
					$newtime_end = $laast_end + $time;
					$newtime_start = $laast_end;
					$laast_end = $newtime_end;
					if ($songname != ""){
						if($debug){echo "reloading adding song!<br>";}
						$content .= "$songname&$kbps&$newtime_start&$newtime_end;";
					}
					$teller++;
					if ($songname == ""){$stop = 1; $yes = 1;}
				}

				if($debug){
					echo "reloading writeing!<br>";
				}
				fwrite($fp4, $content);
				fclose($fp4);
			}else {return "0";}
		}else {return "0";}
	}

    /**
     * Short description for public
     *
     * Get information about a list for a radio station
     * executed usually at startup of the module
     *
     * @param  string $station  Parameter description (if any) ...
     * @param  string $playlist Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
	public function get_playlist_info($station = "0", $playlist = "0")
	{
		if ($station != "0" && $playlist != "0")
		{
			$file2 = "$this->playlist_src$station/$playlist.data";
			$data = null;
			if (file_exists($file2)) {
				$playlist_open = fopen($file2, "rb");
				$filesize = filesize($file2);
				if ($filesize == "0" or $filesize == ""){$filesize = "1";}
				$playlist_data = fread($playlist_open, $filesize);
				fclose($playlist_open);
				$playlist_data_open_1 = explode(";", $playlist_data);
				$teller = "0";
				$stop = "";
				while ($stop == "")
				{
					$playlist_data_open_2 = explode("&", $playlist_data_open_1[$teller]);
					$filename = $playlist_data_open_2[0];
					if ($filename != ""){
						$bitrate = $playlist_data_open_2[1];
						$start_time = $playlist_data_open_2[2];
						$end_time = $playlist_data_open_2[3];
						$out2 = explode("/", $filename);
						$laast = count($out2) - 1;
						$songname = explode(".", $out2[$laast]);
						$tottaltime = $end_time - $start_time;
						$tottaltime  = playlist::sec2hms($tottaltime);
						$endtime = $end_time - time();
						$endtime = playlist::sec2hms($endtime);
						if (time() >= $start_time)
						{
							if (time() <= $end_time)
							{
								$data .= $songname[0]. "&" .$bitrate. "&$tottaltime&$endtime Playing;";
							}else {$ago = time() - $end_time; $ago = playlist::sec2hms($ago); $data .= $songname[0]. "&" .$bitrate. "&$ago;";}
						}else {$togo = $start_time - time(); $togo = playlist::sec2hms($togo); $data .= $songname[0]. "&" .$bitrate. "&$togo;";}
						$teller++;
					}else {$stop = "1";}
				}

			}else {return "0";}
		}else {return "0";}
		return $data;
	}
}
?>
