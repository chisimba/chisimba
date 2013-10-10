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
 * AWARD index data access class
 * 
 * Class to provide AWARD Bragaining unit information via XML_RPC
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: apiunit_class_inc.php 74 2008-07-31 12:00:45Z nic $
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
 * AWARD XML-RPC Class
 * 
 * Class to provide AWARD XML-RPC functionality to Chisimba clients
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: apiunit_class_inc.php 74 2008-07-31 12:00:45Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class apiunit extends object
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
		  $this->objUnit = $this->getObject('dbunit');
          $this->objAgree = $this->getObject('dbagreement');
		  $this->objRegion = $this->getObject('dbregion');
		  $this->objUnitBranch = $this->getObject('dbunitbranch');
		  $this->objDistrict = $this->getObject('dbdistrict');
		  $this->objUnitSic = $this->getObject('dbunitsic');
		  $this->objBranch = $this->getObject('dbbranch');
          $this->objUnitRegion = $this->getObject('dbunitregion');
		} catch (customException $e) {
			customException::cleanUp();
			exit;
		}
	}
	
	public function listBu($params) {
	    $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$str = $param->scalarval();
    	$a_bu = $this->objUnit->getUnitOverview($str);
    	$buarray = array();
    	if (is_array($a_bu)) {
    	    foreach ($a_bu as $bu) {
    	   	   $a_xmlrpc = new XML_RPC_Value(array(
    		      new XML_RPC_Value($bu['id'], "string"),
    			  new XML_RPC_Value($bu['name'], "string"),
    			  new XML_RPC_Value($bu['agreeCount'], "string"),
    			  // new XML_RPC_Value($bu['sic'], "string"),
    			  new XML_RPC_Value($bu['union'], "string"),
    			  //new XML_RPC_Value($bu['region'], "string"),
    			  new XML_RPC_Value($bu['lastAgreement'], "string")
    		   ), "array");
    	
    		$buarray[] = $a_xmlrpc;
		  }
		  $ret = new XML_RPC_Value($buarray, "array");
    	  return new XML_RPC_Response($ret);
    	}
	}
	
	public function BUValues($params) {
	    $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
    	
    	$ub = $this->objUnitBranch->getRow('unitid',$id);
    	$branchId = $ub['branchid'];
    	$branch = $this->objBranch->getRow('id',$branchId);
    	$partyId = $branch['partyid'];
    	$region = $this->objUnitRegion->getRow('unitid',$id);
    	$a_sic = $this->objUnitSic->getRow('unitid',$id);
    	$bu = $this->objUnit->getRow('id',$id);
    	
    	$a_xmlrpc = new XML_RPC_Value(array(
    		      new XML_RPC_Value($partyId, "string"),
    			  new XML_RPC_Value($branchId, "string"),
    			  new XML_RPC_Value($a_sic['major_divid'], "string"),
    			  new XML_RPC_Value($a_sic['divid'], "string"),
    			  new XML_RPC_Value($a_sic['major_groupid'], "string"),
    			  new XML_RPC_Value($a_sic['groupid'], "string"),
    			  new XML_RPC_Value($a_sic['sub_groupid'], "string"),
    			  new XML_RPC_Value($region['regionid'], "string"),
    		      new XML_RPC_Value($bu['name'], "string"),
    		      new XML_RPC_Value($bu['notes'], "string")
    		   ), "array");
    		   
        return new XML_RPC_Response($a_xmlrpc);
	}
    
    public function addUnit($params) {
    	$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$name = $param->scalarval();
        
        $param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pbId = $param->scalarval();
        
        $param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$sicMDId = $param->scalarval();
        
        $param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$sicDId = $param->scalarval();
        
        $param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$sicMGId = $param->scalarval();
        
        $param = $params->getParam(5);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$sicGId = $param->scalarval();
        
        $param = $params->getParam(6);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$sicSGId = $param->scalarval();
        
        $param = $params->getParam(7);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$regId = $param->scalarval();
        
        $param = $params->getParam(8);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$notes = $param->scalarval();
        
        $param = $params->getParam(9);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$force = $param->scalarval();
        
        $duplicates = $this->objUnit->getAll("WHERE name = '$name'");
        if (!empty($duplicates)) {
            $a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value("2","string"),
                new XML_RPC_Value($duplicates[0]['id'],"string"),
                new XML_RPC_Value("$name","string")
                    ),"array"
            );
            $ret = new XML_RPC_Response($a_xmlrpc);
        } else {
            $similars = $this->objUnit->getAll("WHERE name LIKE '$name%'");
            if (empty($similars) || $force == '1') {
                $id = $this->objUnit->insertUnit($name, $pbId, $sicMDId, $sicDId, $sicMGId, $sicGId, $sicSGId, $regId, $notes);
                $a_xmlrpc = new XML_RPC_Value(array(
                    new XML_RPC_Value("0","string"),
                    new XML_RPC_Value($id,"string"),
					new XML_RPC_Value("$name","string")
                    ),"array"
                );
                $ret = new XML_RPC_Response($a_xmlrpc);
            } else {
                $a_xmlrpc = new XML_RPC_Value(array(
                    new XML_RPC_Value("1","string"),
                    new XML_RPC_Value("$name%","string")
                    ),"array"
                );
                $ret = new XML_RPC_Response($a_xmlrpc);
            }
        }
       return $ret; 
    }
    
    public function editUnit($params) {
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
        
        $param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$pbId = $param->scalarval();
        
        $param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$sicMDId = $param->scalarval();
        
        $param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$sicDId = $param->scalarval();
        
        $param = $params->getParam(5);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$sicMGId = $param->scalarval();
        
        $param = $params->getParam(6);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$sicGId = $param->scalarval();
        
        $param = $params->getParam(7);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$sicSGId = $param->scalarval();
        
        $param = $params->getParam(8);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$regId = $param->scalarval();
        
        $param = $params->getParam(9);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$notes = $param->scalarval();
        
        $this->objUnit->updateUnit($id, $name, $pbId, $sicMDId, $sicDId, $sicMGId, $sicGId, $sicSGId, $regId, $notes);
        
        $ret = new XML_RPC_Value("TRUE", "string");
    	return new XML_RPC_Response($ret);
    }
    
    public function getOverview($params) {
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
        
        $unit = $this->objUnit->getRow('id',$id);
        $sicStr = $this->objUnitSic->getSicStr($id);
        $union = $this->objAgree->getUnionFullName($id);
        $region = $this->objRegion->getRegionName($id);
        $lastAgree = $this->objAgree->getLastAgreement($id);
        if (!is_array($lastAgree)) {
            $lastAgree['length'] = $lastAgree['workers'] = "";
        }
        $a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value($union,"string"),
                new XML_RPC_Value(array(
                            new XML_RPC_Value($sicStr[0],"string"),
                            new XML_RPC_Value($sicStr[1],"string"),
                            new XML_RPC_Value($sicStr[2],"string"),
                            new XML_RPC_Value($sicStr[3],"string"),
                            new XML_RPC_Value($sicStr[4],"string")),
                    "array"),
                new XML_RPC_Value($region,"string"),
                new XML_RPC_Value($lastAgree['length'],"string"),
                new XML_RPC_Value($lastAgree['workers'],"string"),
                new XML_RPC_Value($unit['notes'],"string")
                ),"array"
            );
        $ret = new XML_RPC_Response($a_xmlrpc);
        return $ret;
    }
    
    public function listAgree($params) {
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$unitId = $param->scalarval();
        $agrees = $this->objAgree->getAll("WHERE unitid = '$unitId' ORDER BY implementation DESC");
        if (!empty($agrees)) {
            foreach ($agrees as $agree) {
                $a_xmlrpc[] = new XML_RPC_Value(array(
                    new XML_RPC_Value($agree['id'],"string"),
                    new XML_RPC_Value($agree['name'],"string"),
                    new XML_RPC_Value($agree['length'],"string"),
                    new XML_RPC_Value($agree['implementation'],"string"),
                    new XML_RPC_Value($agree['workers'],"string")
                ),"array");
            }
        } else {
            $a_xmlrpc = array();
        }
        $a_ret = new XML_RPC_Value($a_xmlrpc,"array");
        $ret = new XML_RPC_Response($a_ret);
        return $ret;
    }
    
    public function deleteUnit($params) {
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
        
        $this->objUnit->deleteUnit($id);
        
        $ret = new XML_RPC_Value("TRUE", "string");
    	return new XML_RPC_Response($ret);
    }
}
?>