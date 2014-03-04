<?php

/**
 * Short description for file
 *
 * Statistics for all the stations. This inlcude user stats,
 * playlists, and stations and gives all breakdowns for display
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
 * @version   $Id: stats_class_inc.php 11948 2008-12-29 21:28:04Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
error_reporting(0);

/**
 * Short description for class
 *
 * Class handles data agregation for various playlist songs, users and stations
 *
 * @category  Chisimba
 * @package   radio
 * @author    Prince Mbekwa <pmbekwa@uwc.ac.za>
 * @copyright 2007
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: stats_class_inc.php 11948 2008-12-29 21:28:04Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class stats extends object
{

    /**
     * Description for public
     * Users source
     * @var    string
     * @access public
     */
	public $users_src;

    /**
     * Description for public
     * playlist source
     * @var    string
     * @access public
     */
	public $playlist_src;

    /**
     * Description for public
     * live feeds source
     * @var    unknown
     * @access public
     */
	public $live_src;

	/**
    * Constructor for the class
    */
    public function init()
    {
    	$this->users_src = $this->getResourcePath('includes/users','radio');
    	$this->live_src = $this->getResourcePath('includes/live/','radio');
    	$this->playlist_src = $this->getResourcePath('includes/playlist/','radio');

    }


    /**
     * Short description for public
     *
     * Get a list of users currently online
     *
     * @param  string $staion Parameter description current station
     * @return mixed  Return description (if any) ...
     * @access public
     */
	public function get_users_online($staion)
	{
		$teller = "0";
		if ($handle = opendir($this->users_src)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if ($handle2 = opendir($this->users_src.'/'.$file)) {
						while (false !== ($file2 = readdir($handle2))) {
							if ($file2 != "." && $file2 != "..") {
								$temp = explode(".",$file2);
								if($temp[0] == $staion){
									if($temp[1] <= time()){ unlink($this->users_src."/".$file."/".$staion.".".$file2.".data"); }else{
										$teller++;}
								}
							}
						}
						closedir($handle2);
					}


				}
			}
			closedir($handle);
		}
		return $teller;
	}

    /**
     * Short description for public
     *
     * Get users currently online (usernames / ip address)
     *
     * @param  unknown $staion Parameter description station name
     * @return string  Return description (if any) ...
     * @access public
     */
	public function get_users_online_names($staion)
	{
		$teller = "0";
		$data = null;
		if ($handle = opendir($this->users_src)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if ($handle2 = opendir($this->users_src.'/'.$file)) {
						while (false !== ($file2 = readdir($handle2))) {
							if ($file2 != "." && $file2 != "..") {
								$temp = explode(".",$file2);
								if($temp[0] == $staion){
									$data .= $file."&";
								}
							}
						}
					}
					closedir($handle2);


				}
			}
		}
		closedir($handle);

		return $data;
	}

    /**
     * Short description for public
     *
     * Get information about a station
     *
     * @param  string  $station  Parameter description (if any) ...
     * @param  string  $playlist Parameter description (if any) ...
     * @return boolean Return description (if any) ...
     * @access public
     */
	public function station_status($station = "0", $playlist = "0")
	{
		if($station  != "0" && $playlist != "0"){
			if (file_exists($this->playlist_src.$station."/".$playlist.".data")) {
				$data = file_get_contents($this->playlist_src.$station."/".$playlist.".data");
				$stop = "0";
				$out = explode(";",$data);
				$teller = "0";
				while($stop == "0")
				{
					$out2= explode("&",$out[$teller]);
					$file = $out2[0];
					if($file != "")
					{
						$start_time = $out2[2];
						$stop_time =$out2[3];
						if(time() >= $start_time)
						{
							if(time() <= $stop_time)
							{
								return true;
							}
						}

					}
					else
					{
						$stop = "yes";
					}
					$teller++;
				}
			}
			if(file_exists("$this->live_src$station/live.data")){
				return true;
			}
			return false;
		}
	}

    /**
     * Short description for public
     *
     * Get information about a song surrently playing
     *
     * @param  string $station  Parameter description (if any) ...
     * @param  string $playlist Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
	public function now_playing($station = "0", $playlist = "0")
	{
		$song = "0";
		if($station  != "0" && $playlist != "0"){
			if (file_exists($this->playlist_src.$station."/".$playlist.".data")) {
				$data = file_get_contents($this->playlist_src.$station."/".$playlist.".data");
				$stop = "0";
				$out = explode(";",$data);
				$total_files = count($out);
				$teller = "0";
				while($stop == "0")
				{
					$out2= explode("&",$out[$teller]);
					$file = $out2[0];
					if($file != "")
					{
						$start_time = $out2[2];
						$stop_time =$out2[3];
						if(time() >= $start_time)
						{
							if(time() <= $stop_time)
							{
								if(file_exists("$this->live_src$station/live.data")){
									return "LIVE";
								}
								$out3 = explode("/",$file);
								$laast = count($out3) -1;
								$song2 = explode(".",$out3[$laast]);
								$song = $song2[0];
								return $song;
							}
						}

					}else{$stop = "yes";}
					$teller++;
				}
			}
			if(file_exists("$this->live_src$station/live.data")){
				return "LIVE";
			}
			return $song;
		}
	}

    /**
     * Short description for public
     *
     * Get dynamic bitrate of the song
     *
     * @param  string  $station  Parameter description (if any) ...
     * @param  string  $playlist Parameter description (if any) ...
     * @return integer Return description (if any) ...
     * @access public
     */
	public function bitrate($station = "0", $playlist = "0")
	{
		$song = "0";
		if($station  != "0" && $playlist != "0"){
			if (file_exists($this->playlist_src.$station."/".$playlist.".data")) {
				$data = file_get_contents($this->playlist_src.$station."/".$playlist.".data");
				$stop = "0";
				$out = explode(";",$data);
				$teller = "0";
				while($stop == "0")
				{
					$out2= explode("&",$out[$teller]);
					$file = $out2[0];
					if($file != "")
					{
						$start_time = $out2[2];
						$stop_time =$out2[3];
						if(time() >= $start_time)
						{
							if(time() <= $stop_time)
							{

								$bitrate = $out2[1];
								return $bitrate;
							}
						}

					}
					else
					{
						$stop = "yes";
					}
					$teller++;
				}
			}
			return 0;
		}
	}

    /**
     * Short description for public
     *
     * Get information about last played song
     *
     * @param  string $station  Parameter description (if any) ...
     * @param  string $playlist Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
	public function laast_song($station = "0", $playlist = "0")
	{
		$song = "0";
		if($station  != "0" && $playlist != "0"){
			if (file_exists($this->playlist_src.$station."/".$playlist.".data")) {
				$data = file_get_contents($this->playlist_src.$station."/".$playlist.".data");
				$stop = "0";
				$out = explode(";",$data);
				$teller = 0;
				while($stop == "0")
				{
					$out2= explode("&",$out[$teller]);
					$file = $out2[0];
					if($file != "")
					{
						$start_time = $out2[2];
						$stop_time =$out2[3];
						if(time() >= $start_time)
						{
							if(time() <= $stop_time)
							{
								$teller_temp = ($teller) - 1;
								$out2 = explode("&",$out[$teller_temp]);
								$file = $out2[0];
								if($file == ""){ $total = count($out) -2;  $out2= explode("&",$out[$total]);
								$file = $out2[0]; }
								$out3 = explode("/",$file);
								$laast = count($out3) -1;
								$song2 = explode(".",$out3[$laast]);
								$song = $song2[0];
								return $song;
							}
						}

					}
					else
					{
						$stop = "yes";
					}
					$teller++;
				}
			}
			return $song;
		}
	}

    /**
     * Short description for public
     *
     * Get information about the next song in the list
     *
     * @param  string $station  Parameter description (if any) ...
     * @param  string $playlist Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
	public function next_song($station = "0", $playlist = "0")
	{
		$song = "0";
		if($station  != "0" && $playlist != "0"){
			if (file_exists($this->playlist_src.$station."/".$playlist.".data")) {
				$data = file_get_contents($this->playlist_src.$station."/".$playlist.".data");
				$stop = "0";
				$out = explode(";",$data);
				$teller = "0";
				while($stop == "0")
				{
					$out2= explode("&",$out[$teller]);
					$file = $out2[0];
					if($file != "")
					{
						$start_time = $out2[2];
						$stop_time =$out2[3];
						if(time() >= $start_time)
						{
							if(time() <= $stop_time)
							{
								$out2= explode("&",$out[$teller + 1]);
								$file = $out2[0];
								if($file == ""){$out2= explode("&",$out[0]);
								$file = $out2[0]; }
								$out3 = explode("/",$file);
								$laast = count($out3) -1;
								$song2 = explode(".",$out3[$laast]);
								$song = $song2[0];
								return $song;
							}
						}

					}
					else
					{
						$stop = "yes";
					}
					$teller++;
				}
			}
			return $song;
		}
	}

}
?>