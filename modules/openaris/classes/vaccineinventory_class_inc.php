<?php
/**
 * ahis vaccine inventory Class
 *
 * vaccine inventory class
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
 * @author    Joseph Gatheru<jgatheru@icsit.jkuat.ac.ke>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: passive_class_inc.php 12627 2009-02-26 14:29:10Z nic $
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
 * ahis passive Class
 * 
 * Class to access passive surveillance reports in the DB
 * 
 * @category  Chisimba
 * @package   ahis
 * @author    Joseph Gatheru<mujoga@gmail.com>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: passive_class_inc.php 12627 2009-02-26 14:29:10Z nic $
 * @link      http://avoir.uwc.ac.za
 */
class vaccineinventory extends dbtable {
	
    /**
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init() {
		try {
			parent::init('tbl_ahis_vaccine_inventory');
		}
		catch (customException $e)
		{
			customException::cleanUp();
			exit;
		}
	}
	
	/**
	 * Method to return the next outbreak reference number
	 *
	 * @return int Reference no.
	 */
	public function addData($district_name, $vaccine_name, $total_doses_on_hand, $dosesatmonthstart,$dosesatendstart,$monthdoses,$dosesused,$doseswasted)
    {
		$data = $this->insert(array(
			'district_name' => stripslashes($district_name),
			'vaccine_name' =>  stripslashes($vaccine_name),
			'$total_doses_on_hand' => $total_doses_on_hand,
			'$dosesatmonthstart' => $dosesatmonthstart,
			'dosesatendstart' => $dosesatendstart,
			'monthdoses' => $monthdoses,
			'dosesused' => $dosesused,
			'doseswasted' => $doseswasted
			));
			if($data)
			return true;
			else
			return false;
			
	} 
	
	
}