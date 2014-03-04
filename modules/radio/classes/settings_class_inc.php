<?php

/**
 * Short description for file
 *
 * This is the file that takes care of storing settings
 * for various stations in the system. Writes all of this to
 * a file called setting. It also logs the header.data file
 * for streaming
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
 * @version   $Id: settings_class_inc.php 11948 2008-12-29 21:28:04Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       console_class_inc
 */

/**
 * Short description for class
 *
 * Class to administer the various settings for radio
 *
 * @category  Chisimba
 * @package   radio
 * @author    Prince Mbekwa <pmbekwa@uwc.ac.za>
 * @copyright 2007
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: settings_class_inc.php 11948 2008-12-29 21:28:04Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class settings extends object
{

    /**
     * Description for public
     * Object holds the source file parth
     * where station data can be found
     * @var    string
     * @access public
     */
	public $station_src;
	/**
    * Constructor for the class
    */
    public function init()
    {
    	$this->station_src = $this->getResourcePath('includes/station/','radio');
    }

    /**
     * Short description for public
     *
     * Gets data for the stated station
     *
     * @param  string  $station Parameter station
     * @return array Return description (if any) ...
     * @access public
     */
	public function get($station = "0")
	{
		$header_title = "Radio";
		$header_genre = "N/A";
		$header_bitrate = "N/A";
		$header_site  = "N/A";
		$debugkey  = "test";
		if (is_dir($this->station_src.$station)) {
			if (file_exists($this->station_src.$station."/settings.data")) {
				$fp = fopen($this->station_src.$station."/settings.data", "rb");
				$data = fread($fp, filesize($this->station_src.$station."/settings.data"));
				fclose($fp);
				return $data;
				}else{
					$data = $header_title."&".$header_genre."&".$header_bitrate."&".$header_site."&".$debugkey;
				}
			}	else
				{
				$data = $header_title."&".$header_genre."&".$header_bitrate."&".$header_site."&".$debugkey;
				}
			return $data;
	}

    /**
     * Short description for public
     *
     * Update station info and write to file
     *
     * @param  string $station Parameter surrent station
     * @param  string $where   Parameter description (if any) ...
     * @param  string $what    Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
	public function update($station = "0",$where = "0",$what = "0")
	{
		if (is_dir($this->station_src.$station)) {
			if (file_exists($this->station_src.$station."/settings.data")) {
				$fp = fopen($this->station_src.$station."/settings.data", "rb");
				$data = fread($fp, filesize($this->station_src.$station."/settings.data"));
				fclose($fp);
				$data_out = explode("&",$data);
				$change = $data_out[$where];
				$data  = str_replace($change,$what,$data );
				$fp = fopen($this->station_src.$station."/settings.data", "w+b");
				fwrite($fp, $data);
				fclose($fp);
			}else{
				return "0";
			}
		}else{
			return "0";
		}

	}


    /**
     * Short description for public
     *
     * Add newly created station
     *
     * @param  string $station        Parameter description (if any) ...
     * @param  string $header_title   Parameter description (if any) ...
     * @param  string $header_genre   Parameter description (if any) ...
     * @param  string $header_bitrate Parameter description (if any) ...
     * @param  string $header_site    Parameter description (if any) ...
     * @param  string $debugkey       Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
	public function add($station = "0", $header_title = "N/A",$header_genre = "N/A",$header_bitrate = "N/A",$header_site = "N/A",$debugkey = "0")
	{
		$station = str_replace(" ","_",$station);
		if($station != "0" && $debugkey != "0")
		{

			$data = $header_title."&".$header_genre."&".$header_bitrate."&".$header_site."&".$debugkey;
			if (is_dir($this->station_src.$station)) {
				$fp = fopen($this->station_src.$station."/settings.data", "w+b");
				fwrite($fp, $data);
				fclose($fp);
				return true;

			}
			else{
				mkdir($this->station_src.$station, 0777);
				$fp = fopen($this->station_src.$station."/settings.data", "w+b");
				fwrite($fp, $data);
				fclose($fp);
				return true;
			}
		}else{
			return "0";}
	}
}



?>