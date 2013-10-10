<?php

/**
 * Short description for file
 *
 * Executes commands
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
 * @version   $Id: consolecommands_class_inc.php 11948 2008-12-29 21:28:04Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */

/**
 * Short description for class
 *
 * Class used for executing commands. Mainly for debugging
 *
 * @category  Chisimba
 * @package   radio
 * @author    Prince Mbekwa <pmbekwa@uwc.ac.za>
 * @copyright 2007
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: consolecommands_class_inc.php 11948 2008-12-29 21:28:04Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class consolecommands extends object
{

    /**
     * Short description for public
     *
     * Init function
     *
     * @return void
     * @access public
     */
	public function init()
	{

	}

    /**
     * Short description for public
     *
     * Method to set settings for a particular station
     *
     * @param  string  $command Parameter description (if any) ...
     * @return unknown Return description (if any) ...
     * @access public
     */
	public function commands($command){
		if($command == "settings")
		{
			$station = $data_temp[1];
			$out = settings::get($station);
			return $out;
		}
		return false;
	}
}
?>