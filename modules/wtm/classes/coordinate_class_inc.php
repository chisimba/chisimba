<?php
/**
* WTM coordinate class
*
* This file provides a position search class for the WTM module.
* It receives a gps coordinate and compass direction and determines which building
* is being targeted. Its purpose is to determine which building the phone is pointing at
*
* PHP version 5
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the
* Free Software Foundation, Inc.,
* 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
*
* @category Chisimba
* @package WTM
* @author Yen-Hsiang Huang <wtm.jason@gmail.com>
* @copyright 2007 AVOIR
* @license http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version CVS: $Id: demo_class_inc.php,v 1.4 2007-08-03 10:33:34 Exp $
* @link http://avoir.uwc.ac.za
*/

/**
* Security check: the $GLOBALS is an array used to control access to certain constants.
* Here it is used to check if the file is opening in engine, if not it
* stops the file from running.
*
* @global entry point $GLOBALS['kewl_entry_point_run']
* @name $kewl_entry_point_run
*/
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}


class coordinate extends object 
{
	public $objLanguage;
 
	public $objDBEvents;
	
	public $objDBBuildings;

 	/**
    * Constructor method to instantiate the language
	* object and building and event tables.
    */
	public function init()
	{
		$this->objLanguage = $this->getObject('language','language');
		
		$this->objDBEvents = $this->getObject('dbwtm_events','wtm');  
		
		$this->objDBBuildings = $this->getObject('dbwtm_buildings','wtm');  
	}

	/**
    * Echo name of building being targetted by 
	* given coordinates and compass heading.
    * @param integer $longcoord longitude coordinate
    * @param integer $latcoord latitude coordinate
	* @param decimal $angle compass heading in radians
    * @echoes target building.
    */
	public function search($longcoord,$latcoord,$angle)
	{
		//Coordinates are shifted to integer form (5 decimal accuracy).
		$longcoordinate = $longcoord * 100000;
		$latcoordinate = $latcoord * 100000;
		//Approximately 5m iterations.
		$length = 0.00005 * 100000;
		$counter = 0;
		
		$allBuildings = $this->objDBBuildings->listAll();
			
		//Approximately 100m range.
		while ($counter < 20)
		{
			foreach($allBuildings as $thisBuilding)
			{
				//Setting building boundaries using building data.
				$buildingeast = $thisBuilding["longcoordinate"] + $thisBuilding["xexpand"];
				$buildingwest = $thisBuilding["longcoordinate"] - $thisBuilding["xexpand"];
				$buildingnorth = $thisBuilding["latcoordinate"] + $thisBuilding["yexpand"];
				$buildingsouth = $thisBuilding["latcoordinate"] - $thisBuilding["yexpand"];
				
				//Coordinates are within longitude boundaries.
				if ($longcoordinate < $buildingeast && $longcoordinate > $buildingwest)
				{
					//Coordiantes are within latitude boundaries.
					if ($latcoordinate < $buildingnorth && $latcoordinate > $buildingsouth)
					{
						echo trim($thisBuilding["building"]) . "\n";
						echo trim($thisBuilding["id"]);
						exit;
					}
				}
			}
			
			//Coordinates are not within both boundaries.
			switch ($angle)
			{
				//Between N & E. Increase long and lat.
				case ($angle <= M_PI/2):
					$longcoordinate += $length * sin($angle);
					$latcoordinate += $length * cos($angle);
					break;
				//Between E & S. Increase long decrease lat.
				case ($angle > M_PI/2 && $angle <= M_PI):
					$longcoordinate += $length * sin($angle);
					$latcoordinate += $length * cos($angle);
					break;
				//Between S & W. Decrease long and lat.
				case ($angle > M_PI && $angle <= (3*M_PI)/2):
					$longcoordinate += $length * sin($angle);
					$latcoordinate += $length * cos($angle);
					break;
				//Between W & N. Decrease long increase lat.
				case ($angle > (3*M_PI)/2):
					$longcoordinate += $length * sin($angle);
					$latcoordinate += $length * cos($angle);
					break;
			}
			
			$counter += 1;
		}
	}
	
	/**
    * Retrieves and echoes list of events being requested for 
	* a spefic (target) building.
    * @param text $buildingid building ID
    * @echoes relevant event list.
    */
	public function retrieve ($buildingid)
	{
		$allEvents = $this->objDBEvents->listAll();
		
		foreach($allEvents as $thisEvent)
		{
			if ($buildingid == $thisEvent["buildingid"])
			{
				//Last 3 characters echoed characters determine media availability
				if ($thisEvent["description"] != NULL)
				{
					$text = 1;
				}
				else
				{
					$text = 0;
				}
				if ($thisEvent["imagename"] != NULL)
				{
					$image = 1;
				}
				else
				{
					$image = 0;
				}
				if ($thisEvent["videoname"] != NULL)
				{
					$video = 1;
				}
				else
				{
					$video = 0;
				}
				
				echo trim($thisEvent["id"]) . "\n";
				echo trim($thisEvent["date"]) . ": " . trim($thisEvent["event"]) . $text . $image . $video . "\n";
			}
		}
	}
	
	/**
    * Retrieves and echoes specific filename of the media 
	* being requested for a specific event.
    * @param text $eventid event ID
	* @param integer $num media type enumeration
    * @echoes media filename for specific event
    */
	public function retrievemedia($eventid, $num)
	{
		$allEvents = $this->objDBEvents->listAll();
		
		foreach($allEvents as $thisEvent)
		{
			if ($eventid == $thisEvent["id"])
			{
				switch ($num)
				{
					case 1:
						echo $thisEvent["description"];
						exit;
					case 2:
						echo $thisEvent["imagename"];
						exit;	
					case 3:
						echo $thisEvent["videoname"];
						exit;		
				}
			}
		}
	}
	
	
}
?>