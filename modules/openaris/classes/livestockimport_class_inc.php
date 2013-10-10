<?php
/**
 * ahis livestockimport Class
 *
 * animalmovement class
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
 * @package   ahis
 * @author    Isaac Oteyo<ioteyo@icsit.jkuat.ac.ke>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: animalmovement_class_inc.php 12627 2009-02-26 14:29:10Z ioteyo$
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check


/**
 * ahis animalmovement Class
 * 
 * Class to access animal movement DB
 * 
 * @category  Chisimba
 * @package   ahis
 * @author    Isaac Oteyo <ioteyo@icsit.jkuat.ac.ke>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: animalmovement_class_inc.php 
 * @link      http://avoir.uwc.ac.za
 */
 
 class livestockimport extends dbtable{
 /**
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init() {
		try {
			parent::init('tbl_ahis_livestockimport');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	public function addLivestockimportData($district,$entrypoint,$destination,$classification,$origin,$eggs,$milk,$cheese,$poultry,$beef,$count)
    {
		
			$sql = $this->insert(array(
			'district' => stripslashes($district),
			'entrypoint' => $entrypoint,
			'destination' => $destination,
			'classification' => $classification,
			'origin' => $origin,
			'eggs' => $eggs,
			'milk' => $milk,
			'cheese' => $cheese,
			'poultry' => $poultry,
			'beef' => $beef,
			'count' => $count,
			));
			if($sql)
			return true;
			else
			return false;
			
	} 
	
	public function getDistrict($id){	
	$sql="SELECT * FROM tbl_ahis_geography_level2 where id='$id' "; 
	$data=$this->getArray($sql);
	return $data[0]['name'];	
	}
	
 }


?>