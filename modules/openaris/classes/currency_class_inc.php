<?php
/**
 * ahis Currency Class
 *
 * file housing Currency Class
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
 * @author    Samuel Onyach <sonyach@icsit.jkuat.ac.ke,onyachsamuel@yahoo.com>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: currency_class_inc.php
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

class currency extends dbtable
{

    public function init()
    {
        parent::init('tbl_ahis_currencies');
        
       
    }
    
    public function addData($isocurrencycode,$currency,$symbol,$remarks,$datecreated,$creadtedby,$sdate,$edate)
    {
		$data = $this->insert(array(
			'iso_currency_code' => stripslashes($isocurrencycode),
			'currency' =>  stripslashes($currency),
			'symbol' =>stripslashes($symbol),
			'remarks' => stripslashes( $remarks),	
			'start_date' =>$sdate,
			'end_date' =>$edate,			
			'date_created' => $datecreated,
			'created_by' => $creadtedby
			));
			if($data)
			return true;
			else
			return false;
			
	}
}
?>