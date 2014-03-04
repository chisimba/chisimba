<?php

/**
 * Logs to file current activities of users
 * on the current radio station.
 * Writes this info to file for later use by administrator
 *
 *
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
 * @version   $Id: console_class_inc.php 11948 2008-12-29 21:28:04Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       radio module
 */

/**
 * Console class used for executing commands
 *
 * Long description (if any) ...
 *
 * @category  Chisimba
 * @package   radio
 * @author    Prince Mbekwa <pmbekwa@uwc.ac.za>
 * @copyright 2007
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: console_class_inc.php 11948 2008-12-29 21:28:04Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class console extends object
{

    /**
     * Description for public
     * log object
     * @var    string
     * @access public
     */
	public $log;

    /**
     * Description for public
     * Users object
     * @var    string
     * @access public
     */
	public $users;

    /**
     * Description for public
     * Banned list object
     * @var    string
     * @access public
     */
	public $ban;

    /**
     * Description for public
     * Executable commands object
     * @var    object
     * @access public
     */
	public $console_commands;

	/**
    * Constructor for the class
    */
    public function init()
    {
        $this->log = $this->getResourcePath('includes/log/','radio');
        $this->users = $this->getResourcePath('includes/users','radio');
        $this->ban = $this->getResourcePath('includes/ban','radio');
        $this->console_commands = $this->newObject('consolecommands','radio');

    }

    /**
     * Short description for public
     * Startup method
     * Long description (if any) ...
     *
     * @param  string $station Parameter description current station
     * @return array Return description
     * @access public
     */
	public function start_up($station = "0")
	{
		$data = "0";
		if($station != "0")
		{
		 if (file_exists($this->log.$station.".data")) {
		 	$fp = fopen($this->log.$station.".data", "rb");
		 	$data = fread($fp, filesize($this->log.$station.".data"));
		 	fclose($fp);
			 }else{return "0";}
		}
		return $data;
	}

    /**
     * Short description for public
     * Method to log inputs and activities on the sation
     *
     * Long description (if any) ...
     *
     * @param  string $station Parameter description (if any) ...
     * @param  string $data_q  Parameter description (if any) ...
     * @return void
     * @access public
     */
	public function add_log($station = "0",$data_q = "0")
	{
		$time_stamp = time();
		if($station != "0")
		{

		 if (file_exists($this->log.$station.".data")) {
		 	$fp = fopen($this->log.$station.".data", "rb");
		 	$data = fread($fp, filesize($this->log.$station.".data"));
		 	fclose($fp);
		 	$data .= $data.$data_q.";".$time_stamp."&";
		 	$fp = fopen($this->log.$station.".data", "w+b");
		 	fwrite($fp,$data);
		 	fclose($fp);
		 	}else
		 	{
			$data = $data_q.";".$time_stamp."&";
		 	$fp = fopen($this->log.$station.".data", "w+b");
		 	fwrite($fp,$data);
		 	fclose($fp);
		 	return true;
			}
		}
		return false;
	}

    /**
     * Short description for public
     * Method to add online users
     *
     * Long description (if any) ...
     * Method logs currently listerning users, their ip addresses
     *
     * @param  string $station Parameter description currently playing station
     * @return void
     * @access public
     */
	public function add_online_user($station = "0")
	{

		$ip = $_SERVER["REMOTE_ADDR"];
		$time = time() + 300;
		$result = false;
		if($station != "0" && $ip != "0")
		{
			if ($handle = opendir($this->users)) {
	    		while (false !== ($file = readdir($handle))) {
	        		if ($file != "." && $file != "..") {
	        			if ($handle2 = opendir($this->users.'/'.$file)) {
	    					while (false !== ($file2 = readdir($handle2))) {
	        					if ($file2 != "." && $file2 != "..") {
	        						unlink($this->users.'/'.$file."/".$file2);
	        						$result = true;
	        	  				}
	    					}
	    					closedir($handle2);
						}
	        	  	}
	    		}
	    		closedir($handle);
				}
			if (is_dir($this->users.'/'.$ip)) {
				$fp = fopen($this->users.'/'.$ip."/".$station.".".$time.".data","w+b");
				fclose($fp);
			}else{
				mkdir($this->users.'/'.$ip, 0777);
				$fp = fopen($this->users.'/'.$ip."/".$station.".".$time.".data","w+b");
				fclose($fp);
			}
		}
		return $result;
	}


    /**
     * Short description for public
     * Method to pdate current users of the radio
     *
     * Long description (if any) ...
     * Logs whomever is currently streaming the sund of the station
     *
     * @return void
     * @access public
     */
	public function update_online_users()
	{
		$time_end = time() + 300;
		$result = false;
		if ($handle = opendir($this->users)) {
	    	while (false !== ($file = readdir($handle))) {
	        	if ($file != "." && $file != "..") {
	            	if ($handle2 = opendir($this->users.'/'.$file)) {
	    				while (false !== ($file2 = readdir($handle2))) {
	        				if ($file2 != "." && $file2 != "..") {
	          					$fp = fopen($this->users.'/'.$file."/".$file2, "rb");
			  					$time_start = fread($fp,filesize($this->users.'/'.$file."/".$file2));
			  					$result = true;
			  					fclose($fp);
			  						if($time_end <= $time_start){
			  							unlink($this->users.'/'.$file."/".$file2);
			  							$result = true;
			  						}
	        				}
	    				}
	    				closedir($handle2);
					}
	        	}
	    	}
	    	closedir($handle);
	    	return $result;
		}
	}

    /**
     * Short description for public
     * Method to check for banned Ip / users
     *
     * Long description (if any) ...
     *
     * @return void
     * @access public
     */
	public function ban_check()
	{
			if ($handle = opendir($this->ban)) {
	    		while (false !== ($file = readdir($handle))) {
	        		if ($file != "." && $file != "..") {
	            		if($file == $_SERVER["REMOTE_ADDR"].".data")
	            		{
							exit();
						}
	           }
	        }
	    }
	    closedir($handle);
		return false;
	}

    /**
     * Short description for public
     * Method to check for banned Ip /user (specific)
     *
     * Long description (if any) ...
     *
     * @param  string  $ip Parameter description (if any) ...
     * @return boolean Return description (if any) ...
     * @access public
     */
	public function ban_check2($ip = "0")
	{
			if ($handle = opendir($this->ban)) {
	    		while (false !== ($file = readdir($handle))) {
	        		if ($file != "." && $file != "..") {
	            		if($file == $ip.".data")
	            		{
							return true;
						}
	           		}
	        	}
	    	}
	    	closedir($handle);
	    	return false;

	}

    /**
     * Short description for public
     * Method to add Ip / user to banned list
     *
     * Long description (if any) ...
     *
     * @param  string $ip Parameter description Ip to be banned
     * @return void
     * @access public
     */
	public function add_to_ban($ip = "0")
	{
		if($ip != "0" && $ip != "")
		{
		$fp = fopen($this->ban.$ip.".data", "w+b");
		fclose($fp);
		return true;
		}
		return false;
	}

    /**
     * Short description for public
     * Method to revoke banned IP / user
     *
     * Long description (if any) ...
     *
     * @param  string $ip Parameter description user / IP
     * @return void
     * @access public
     */
	public function remove_ban($ip)
	{
		if(file_exists($this->ban.$ip.".data"))
		{
			unlink($this->ban.$ip.".data");
			return true;
		}
		return false;
	}

    /**
     * Short description for public
     *
     * Method to execute commands
     *
     * Long description (if any) ...
     *
     * @param  unknown $command Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access public
     */
	public function commands($command)
	{
		$data =	$command;
		if($data != ""){
			$data_temp = explode(" ",$data);
			$options_teller = count($data_temp);
			$command = $data_temp[0];
			$result =  $this->console_commands->commands($command);
			return $result;
		}
		return false;
	}

}
?>