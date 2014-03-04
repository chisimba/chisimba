<?php

/**
 * Short description for file
 *
 * This file writes setup data for the various stations
 * available. This includes default settings as well as anything
 * pertaining to a station
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
 * @version   $Id: stations_class_inc.php 11948 2008-12-29 21:28:04Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */

/**
 * Short description for class
 *
 * Class used for storing or reading settings for a station
 *
 * @category  Chisimba
 * @package   radio
 * @author    Prince Mbekwa <pmbekwa@uwc.ac.za>
 * @copyright 2007
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: stations_class_inc.php 11948 2008-12-29 21:28:04Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class stations extends object
{

    /**
     * Description for public
     * The source for settings
     * @var    string
     * @access public
     */
	public $path;


	 /**
    * Constructor for the class
    */
    public function init()
    {
        $this->path = $this->getResourcePath('includes/station','radio');
        $this->objLanguage = $this->getObject('language','language');

    }


    /**
     * Short description for public
     *
     * Get all settings
     *
     * @return string Return description (if any) ...
     * @access public
     */
	public function get()
	{
		$a = null;
		$data = null;
		if ($handle = opendir($this->path)) {

		    while (false !== ($file = readdir($handle))) {
		        if ($file != "." && $file != "..") {
		           $data .= $file."&";
		           $a = "1";
		        }
		    }
		    closedir($handle);
		}
		if($a == ""){$data = "test&";}
		return $data;
	}



    /**
     * Short description for public
     *
     * Get all defaults for a station
     *
     * @param  string $station Parameter description (if any) ...
     * @return string Return description (if any) ...
     * @access public
     */
	public function default_s($station = "0")
	{
		$data = null;
		if($station == "0" or $station == "")
		{

			$once = "0";
			if ($handle = opendir($this->path)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != "." && $file != "..") {
						if($once == "0")
						{
							$data = $file;
							$once = "1";
						}
					}
				}
				closedir($handle);
			}
			if($data == ""){$data = "test";}
			return $data;
		}
		return $station;
	}

    /**
     * Short description for public
     *
     * Remove a station
     *
     * @param  string $station Parameter description (if any) ...
     * @return void
     * @access public
     */
	public function del($station = "0")
	{
		if($station != "0"){
			if (is_dir($this->path."/".$station)) {
				if ($handle = opendir($this->path."/".$station)) {
					while (false !== ($file = readdir($handle))) {
						if ($file != "." && $file != "..") {
							unlink($this->path."/".$station."/".$file);
						}
					}
				}
				closedir($handle);
				rmdir($this->path."/".$station);
				return true;
			}
		}
	}


    /**
     * Short description for public
     *
     * Admin panel login method
     *
     * @param  string  $station  Parameter description (if any) ...
     * @param  string  $uname    Parameter description (if any) ...
     * @param  string  $password Parameter description (if any) ...
     * @return boolean Return description (if any) ...
     * @access public
     */
	public function login($station = "0", $uname = "0", $password = "0")
	{

		if (is_dir($this->path."/".$station)) {
			if (file_exists($this->path."/".$station."/".$uname.".data")) {
				$fp = fopen($this->path."/".$station."/".$uname.".data", "rb");
				$passwd = fread($fp,filesize($this->path."/".$station."/".$uname.".data"));
				fclose($fp);
				$password = md5("$password");
				if($passwd  == $password){return true;}

			}
			return false;
		}
	}

    /**
     * Short description for public
     *
     * Add admin users
     *
     * @param  string  $station  Parameter description (if any) ...
     * @param  string  $uname    Parameter description (if any) ...
     * @param  string  $password Parameter description (if any) ...
     * @return boolean Return description (if any) ...
     * @access public
     */
	public function add_admin($station = "0", $uname = "0", $password = "")
	{
		if (is_dir($this->path."/".$station)) {
			if (!file_exists($this->path."/".$station."/".$uname.".data")) {
				$fp = fopen($this->path."/".$station."/".$uname.".data", "w+b");
				$password = md5("$password");
				fwrite($fp, $password);
				fclose($fp);
				return true;
			}
			return false;
		}
	}

    /**
     * Short description for public
     *
     * Remove admin users
     *
     * @param  string  $station Parameter description (if any) ...
     * @param  string  $uname   Parameter description (if any) ...
     * @return boolean Return description (if any) ...
     * @access public
     */
	public function del_admin($station = "0", $uname = "0")
	{
		if (file_exists($this->path."/".$station."/".$uname.".data")) {
			unlink($this->path."/".$station."/".$uname.".data");
			return true;
		}
		return false;
	}


    /**
     * Short description for public
     *
     * Get all administrators
     *
     * @return string Return description (if any) ...
     * @access public
     */
	public function get_admins()
	{

		if ($handle = opendir($this->path)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if($once == "")
					{
						$data .= $file;
						$once = "1";
					}else{
						$data .= ";".$file;
					}
					if ($handle2 = opendir($this->path.'/'.$file)) {
						while (false !== ($file2 = readdir($handle2))) {
							if ($file2 != "." && $file2 != "..") {
								$data .= "&".$file2;
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

}
?>