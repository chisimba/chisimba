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
 * @version   CVS: $Id: apibranch_class_inc.php 99 2008-08-07 15:06:18Z nic $
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
 * @version   $Id: apibranch_class_inc.php 99 2008-08-07 15:06:18Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class apibranch extends object
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
		  $this->objBranch = $this->getObject('dbbranch');
		  $this->objUnitBranch = $this->getObject('dbunitbranch');
		} catch (Exception $e){
            throw customException($e->getMessage());
            exit();
        }
    }    
		    
    /**
     * XML-RPC method to get a list of all the party branches
     *
     * @param unkown_type $params no params needed
     * @return XML_RPC_Response containing the trade union information
     */
	public function listPartyBranches($params) {
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
    	
    	$a_branch = $this->objBranch->getAll("WHERE partyid = '$id' ORDER BY name");
    	
    	$brancharray = array();
    	if (is_array($a_branch)) {
    	    foreach ($a_branch as $branch) {
    	       $sql = "SELECT count(id) AS units
                       FROM tbl_award_unit_branch
                       WHERE branchid = '{$branch['id']}'";
    	       $units = $this->objBranch->getArray($sql);
    	
    	       $sql = "SELECT count(agree.id) AS agrees
                       FROM tbl_award_unit_branch AS unitbranch, tbl_award_agree AS agree
                       WHERE unitbranch.branchid = '{$branch['id']}' AND unitbranch.unitid = agree.unitid";
    	       $agrees = $this->objBranch->getArray($sql);
    	       
    	       $sql = "SELECT district.regionid AS id 
    	               FROM tbl_award_branch AS branch, tbl_award_district AS district
    	               WHERE  branch.districtid = district.id AND branch.id = '{$branch['id']}'";
    	       $region = $this->objBranch->getArray($sql);
    	       
    	   	   $a_xmlrpc = new XML_RPC_Value(array(
    		      new XML_RPC_Value($branch['id'], "string"),
	              new XML_RPC_Value($branch['name'], "string"),
	              new XML_RPC_Value($units[0]['units'], "string"),
    		      new XML_RPC_Value($agrees[0]['agrees'], "string"),
    		      new XML_RPC_Value($region[0]['id'], "string")
    		   ), "array");
    	
    		$brancharray[] = $a_xmlrpc;
		  }
		  $ret = new XML_RPC_Value($brancharray, "array");
    	return new XML_RPC_Response($ret);
    	}
	}
	
    public function getBranchUnits($params) {
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
        
        $a_xmlrpc = array();
        $branchUnits = $this->objBranch->getBranchUnits($id);
        foreach ($branchUnits as $unit) {
            $a_xmlrpc[] = new XML_RPC_Value(array(
    		      new XML_RPC_Value($unit['id'], "string"),
	              new XML_RPC_Value($unit['name'], "string"),
	              new XML_RPC_Value($unit['sample'], "string"),
				  new XML_RPC_Value($unit['lastagree'], "string")
    		   ), "array");
        }
        $a_ret = new XML_RPC_Value($a_xmlrpc, "array");
        $ret = new XML_RPC_Response($a_ret);
        return $ret;
    }
    
	/**
     * XML-RPC method to create a Branch
     *
     * @param unkown_type $params name
     * @return XML_RPC_Response TRUE
     */
	public function insertBranch($params) {
	    $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$name = $param->scalarval();
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$region = $param->scalarval();
    	$param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$unionId = $param->scalarval();

    	$this->objDistrict = $this->getObject('dbdistrict');  
    	$districtArray = $this->objDistrict->getAll("WHERE regionid = '$region'");
		$districtRow = current($districtArray);
		$district = $districtRow['id'];
		if($district == NULL)
		{
		    $objLanguage = $this->getObject('language','language');
			$district = $this->objDistrict->insert(array('regionid'=>$region, 'name'=>$objLanguage->languageText('word_unknown')));
		}
		
		$branchId = $this->objBranch->insert(array('partyid'=>$unionId, 'districtid'=>$district, 'name'=>$name));
    	
		$ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
	}
	
	/**
     * XML-RPC method to edit a Branch
     *
     * @param unkown_type $params id, name
     * @return XML_RPC_Response TRUE
     */
	public function updateBranch($params) {
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
    	$region = $param->scalarval();
    	$param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$unionId = $param->scalarval();
    	
    	$this->objDistrict = $this->getObject('dbdistrict');  
    	$districtArray = $this->objDistrict->getAll("WHERE regionid = '$region'");
		$districtRow = current($districtArray);
		$district = $districtRow['id'];
		if($district == NULL)
		{
			$objLanguage = $this->getObject('language','language');
			$district = $this->objDistrict->insert(array('regionid'=>$region, 'name'=>$objLanguage->languageText('word_unknown')));
		}
    	
    	$this->objBranch->update('id',$id,array("partyid"=>$unionId, "districtid"=>$district,"name"=>$name));
    	
		$ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
	}
	
	public function deleteBranch($params) {
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
		
		$this->objBranch->deleteBranch($id);
		
	    $ret = new XML_RPC_Value("TRUE", "string");
	    return new XML_RPC_Response($ret);
	    
	}
}
?>