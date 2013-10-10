<?php

/**
 * Short description for file
 *
 * System generation file
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
 * @copyright 2007 Administrative User
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: system_class_inc.php 11948 2008-12-29 21:28:04Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */


/**
 * Short description for class
 *
 * Initialize all headers and playlists
 *
 * @category  Chisimba
 * @package   radio
 * @author    Prince Mbekwa <pmbekwa@uwc.ac.za>
 * @copyright 2007
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: system_class_inc.php 11948 2008-12-29 21:28:04Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class system extends object
{

    /**
     * Description for public
     * @var    object
     * @access public
     */
	public $playlist;

    /**
     * Description for public
     * @var    object
     * @access public
     */
	public $stream;

    /**
     * Description for public
     * @var    object
     * @access public
     */
	public $settings;

    /**
     * Description for public
     * @var    object
     * @access public
     */
	public $stations;

    /**
     * Description for public
     * @var    object
     * @access public
     */
	public $console;

    /**
     * Description for public
     * @var    object
     * @access public
     */
	public $stats;

    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @return void
     * @access public
     */
	public function int(){
		clearstatcache();
		#error_reporting(6143);
		error_reporting(0);
		$version = "1";
		$this->stream = new stream;
		$this->playlist = new playlist;
		$this->stations = new stations;
		$this->settings = new settings;
		$this->console = new console;
		$this->stats = new stats;
		$this->console->ban_check();
		$station = getParam('station');
		$key = getParam('debug');
		$station = $this->stations->default_s($station);
		$playlist_name = $this->playlist->get_playlist_list($station);
		$settings_data = $this->settings->get($station);
		$settings_data_temp = explode("&", $settings_data);
		$header_title = $settings_data_temp[0];
		$header_genre = $settings_data_temp[1];
		$header_bitrate = $stats->bitrate($station, $playlist_name);
		if ($header_bitrate == "0" or $header_bitrate == "")
		{
		$header_bitrate = $settings_data_temp[2];
		}
		$header_site = $settings_data_temp[3];
		$debugkey = $settings_data_temp[4];
		$site_temp = explode("/", $_SERVER["PHP_SELF"]);
		$laast_one = count($site_temp) -1;
		$between = str_replace($site_temp[$laast_one], "", $_SERVER["PHP_SELF"]);
		$station_site = "http://".$_SERVER["HTTP_HOST"].$between;
	}

    /**
     * Short description for public
     *
     * Long description (if any) ...
     *
     * @param  string  $key  Parameter description (if any) ...
     * @param  string  $key2 Parameter description (if any) ...
     * @return boolean Return description (if any) ...
     * @access public
     */
	public function debug($key, $key2)
	{
			if($key != "" && $key2 != "" && $key == $key2)
			{
				return true;
			}else {return false;}
			if (debug($key, $debugkey))
			{
			$debug = true;
			}else {$debug = false;}
	}


}
?>
