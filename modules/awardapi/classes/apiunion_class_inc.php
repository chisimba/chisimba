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
 * Class to provide AWARD Trade Union information from the database
 * 
 * @category  Chisimba
 * @package   award
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 Nic Appleby
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: apiunion_class_inc.php 74 2008-07-31 12:00:45Z nic $
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
 * @version   $Id: apiunion_class_inc.php 74 2008-07-31 12:00:45Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class apiunion extends object
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
		  $this->objParty = $this->getObject('dbparty');  
		  $this->objBranch = $this->getObject('dbbranch');  
		  $this->objUnitBranch = $this->getObject('dbunitbranch');   
		} catch (customException $e) {
			customException::cleanUp();
			exit;
		}
	}
	
    /**
     * XML-RPC method to get a list of all the trade unions 
     *
     * @param unkown_type $params no params needed
     * @return XML_RPC_Response containing the trade union information
     */
	public function listTradeUnions($params) {
    	$a_union = $this->objParty->getAll("ORDER BY name");

    	$unionarray = array();
    	if (is_array($a_union)) {
    	    foreach ($a_union as $union) {
    	   	   $a_xmlrpc = new XML_RPC_Value(array(
    		      new XML_RPC_Value($union['id'], "string"),
	              new XML_RPC_Value($union['name'], "string"),
	              new XML_RPC_Value($union['abbreviation'], "string")
    		   ), "array");
    	
    		$unionarray[] = $a_xmlrpc;
		  }
		  $ret = new XML_RPC_Value($unionarray, "array");
    	return new XML_RPC_Response($ret);
    	}
	}
	
	/**
     * XML-RPC method to create a trade union
     *
     * @param unkown_type $params name and abbreviation
     * @return XML_RPC_Response TRUE
     */
	public function insertUnion($params) {
	    $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$name = $param->scalarval();
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$abbreviation = $param->scalarval();
    	$this->objParty->insert(array("name"=>$name,"abbreviation"=>$abbreviation));
    
		$ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
	}
	
	/**
     * XML-RPC method to edit a trade union
     *
     * @param unkown_type $params id, name and abbreviation
     * @return XML_RPC_Response TRUE
     */
	public function updateUnion($params) {
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
    	$abbreviation = $param->scalarval();
    	$this->objParty->update('id',$id,array("name"=>$name,"abbreviation"=>$abbreviation));
    	
		$ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
	}
	
	/**
     * XML-RPC method to get a counts for branches below and a list of the branches
     *
     * @param unkown_type $params no params needed
     * @return XML_RPC_Response containing the trade union information
     */
	public function tradeUnionInfo($params) {
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
		
    	//$sql = "SELECT COUNT(id) AS count FROM tbl_award_branch WHERE partyid = '1749'";
		$sql = "SELECT COUNT(id) AS count FROM tbl_award_branch WHERE partyid = '$id'";
    	$branchCount = $this->objBranch->getArray($sql);
		$count = current($branchCount);
		
		//$allBranches = $this->objBranch->getAll("WHERE partyid = '1749'");
		$allBranches = $this->objBranch->getAll("WHERE partyid = '$id'");
		$allUnits = '';
		
		foreach ($allBranches as $b)
		{
			$branch = $b['id'];
			$sql = "SELECT COUNT(branchid) AS count FROM tbl_award_unit_branch WHERE branchid = '$branch'";
			$unitCount = $this->objUnitBranch->getArray($sql);
			$countUnits = current($unitCount);
			$allUnits += $countUnits['count'];
		}

		$p_branches = $count['count'];
		
		//$branches = $this->objBranch->getAll("WHERE partyid = '1749' ORDER BY name");
		$branches = $this->objBranch->getAll("WHERE partyid = '$id' ORDER BY name");
		
    	$brancharray = array();
    	if (is_array($branches)) {
    	    foreach ($branches as $branch) {
    	   	   $a_xmlrpc = new XML_RPC_Value(array(
    		      new XML_RPC_Value($branch['id'], "string"),
    		      new XML_RPC_Value($branch['name'], "string"),
	              new XML_RPC_Value($p_branches, "string"),
	              new XML_RPC_Value($allUnits, "string")
    		   ), "array");
    	
    		$brancharray[] = $a_xmlrpc;
		  }
		  $ret = new XML_RPC_Value($brancharray, "array");
    	return new XML_RPC_Response($ret);
    	}
	}
	
	/**
     * XML-RPC method to return a list of party branches governed by a specified trade union
     *
     * @param XML_RPC params $params id of the trade union
     * @return XML_RPC_Response the list of branches
     */
	public function getBranches($params) {
		$param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
		
    	$branches = $this->objBranch->getAll("WHERE partyid = '$id' ORDER BY name");
		
    	$brancharray = array();
    	if (is_array($branches)) {
    	    foreach ($branches as $branch) {
    	   	   $a_xmlrpc = new XML_RPC_Value(array(
    		      new XML_RPC_Value($branch['id'], "string"),
    		      new XML_RPC_Value($branch['name'], "string"),
    		   ), "array");
    	
    		$brancharray[] = $a_xmlrpc;
		  }
		  $ret = new XML_RPC_Value($brancharray, "array");
    	return new XML_RPC_Response($ret);
    	}
	}
	
	/**
     * XML-RPC method to delete a trade union
     *
     * @param unkown_type $params id
     * @return XML_RPC_Response TRUE
     */
	public function deleteUnion($params) {
	    $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
    	
    	$this->objParty->deleteParty($id);
    	
		$ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
	}
}
?>