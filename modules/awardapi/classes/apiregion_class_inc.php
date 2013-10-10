<?php

/**
 * AWARD API interface class
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
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: apiregion_class_inc.php 121 2008-08-15 11:04:47Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       core,api
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
 * AWARD XML-RPC Class
 * 
 * Class to provide AWARD XML-RPC functionality to Chisimba clients
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: apiregion_class_inc.php 121 2008-08-15 11:04:47Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class apiregion extends object
{

	/**
     * init method
     * 
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
	public function init()
	{
		try { 
		    $this->objRegion = $this->getObject('dbregion');
                    $this->objDistrict = $this->getObject('dbdistrict');
		} catch (Exception $e){
            throw customException($e->getMessage());
            exit();
        }
    }
    
    public function getRegionList($params) {
            $regions = $this->objRegion->getAll("ORDER BY name");
            
	    	$regionarray = array();
	    	if (is_array($regions)) {
	    	    foreach ($regions as $region) {
	    	   	    $a_xmlrpc = new XML_RPC_Value(array(
	    		        new XML_RPC_Value($region['id'], "string"),
	    		        new XML_RPC_Value($region['abbreviation'], "string"),
		                new XML_RPC_Value($region['name'], "string")
	    		    ), "array");
	    	
	    		   $regionarray[] = $a_xmlrpc;
			    }
			    $ret = new XML_RPC_Value($regionarray, "array");
	    	    return new XML_RPC_Response($ret);
	    	}
    }
    
    public function updateRegion($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
        
        $param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $name = $param->scalarval();
        
        $this->objRegion->update('id',$id,array('name'=>$name,'abbreviation'=>""));
        
        $ret = new XML_RPC_Value("TRUE","string");
        return new XML_RPC_Response($ret);
    }
    
    public function insertRegion($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $name = $param->scalarval();
        
        $id = $this->objRegion->insert(array('name'=>$name));
        $this->objDistrict->insert(array('regionid'=>$id, 'name'=>'default'));
        
		$ret = new XML_RPC_Value("TRUE","string");
        return new XML_RPC_Response($ret);
    }
}
?>