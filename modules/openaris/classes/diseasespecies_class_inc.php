<?php
/**
 * disease species Class
 *
 * disease species class
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
 * @copyright 2009 AVOIR-JKUAT
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: diseasespecies_class_inc.php 12627 2009-09-27 14:29:10Z ioteyo$
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
 * disease species Class
 * 
 * Class to access agents  DB
 * 
 * @category  Chisimba
 * @package   ahis
 * @author    Isaac Oteyo <ioteyo@icsit.jkuat.ac.ke>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: diseasespecies_class_inc.php 
 * @link      http://avoir.uwc.ac.za
 */
 
 class diseasespecies extends dbtable{
 /**
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init() {
		try {
			parent::init('tbl_ahis_diseasespecies');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	public function addDiseaseSpeciesData($disease,$species,$description,$dateStartPicker,$dateEndPicker,$dateCreatedPicker,$createdby,$dateModifiedPicker,$modifiedby)
    {
		
			$sql = $this->insert(array(
			'diseaseid' => $disease,
			'speciesid' => $species,
			'description' => $description,
			'startdate' => $dateStartPicker,
			'enddate' => $dateEndPicker,
			'datecreated' => $dateCreatedPicker,
			'createdby' => $createdby,
			'datemodified' => $dateModifiedPicker,
			'modifiedby' => $modifiedby
			));//echo $sql;
			if($sql)
			return true;
			else
			return false;
			
	}
	
	public function getSpecies($diseaseId) {
		$sql = "SELECT s.id AS id, s.speciesname AS speciesname
				FROM tbl_ahis_speciesnew AS s, tbl_ahis_diseasespecies AS ds
				WHERE ds.diseaseid = '$diseaseId' AND ds.speciesid = s.id";
		return $this->getArray($sql);	
	}
 }


?>