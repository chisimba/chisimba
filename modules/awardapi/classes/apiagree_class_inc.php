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
 * @version   $Id: apiagree_class_inc.php 94 2008-08-07 11:03:58Z nic $
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
 * @version   $Id: apiagree_class_inc.php 94 2008-08-07 11:03:58Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class apiagree extends object
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
          $this->objAgreeTypes = $this->getObject('dbagreetypes');
          $this->objBenefits = $this->getObject('dbbenefits');
		  
		} catch (customException $e) {
			customException::cleanUp();
			exit;
		}
	}
    
    public function listAgreeTypes($params) {
        $types = $this->objAgreeTypes->getAll("ORDER BY name");
        foreach ($types as $type) {
            $a_xmlrpc[] = new XML_RPC_Value(array(
                    new XML_RPC_Value($type['id'],"string"),
                    new XML_RPC_Value($type['name'],"string")
                ),"array");
        }
        $a_ret = new XML_RPC_Value($a_xmlrpc,"array");
        $ret = new XML_RPC_Response($a_ret);
        return $ret;
    }
    
     public function getAgreeValues($params) {
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
        $hours = $this->objBenefits->getAll("WHERE nameid = 'init_7' AND agreeid = '$id'");
        $agree = $this->objAgree->getRow('id',$id);
        $a_ret = new XML_RPC_Value(array(
            new XML_RPC_Value($agree['typeid'],"string"),
            new XML_RPC_Value($agree['implementation'],"string"),
            new XML_RPC_Value($hours[0]['value'],"string"),
            new XML_RPC_Value($agree['length'],"string"),
            new XML_RPC_Value($agree['workers'],"string"),
            new XML_RPC_Value($agree['notes'],"string")
        ),"array");
        
        $ret = new XML_RPC_Response($a_ret);
        return $ret;
    }
    
    /**
     * Method similar to getAgreevalues above, but returns agreement type
     * name instead of agreement type id
     */
    public function getAgreeDetails($params) {
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
        $hours = $this->objBenefits->getAll("WHERE nameid = 'init_7' AND agreeid = '$id'");
        if (empty($hours)) {
            $hours[0]['value'] = "0";
        }
        $agree = $this->objAgree->getRow('id',$id);
        $agreeType = $this->objAgreeTypes->getRow('id',$agree['typeid']);
        $a_ret = new XML_RPC_Value(array(
            new XML_RPC_Value($agreeType['name'],"string"),
            new XML_RPC_Value($agree['implementation'],"string"),
            new XML_RPC_Value($hours[0]['value'],"string"),
            new XML_RPC_Value($agree['length'],"string"),
            new XML_RPC_Value($agree['workers'],"string"),
            new XML_RPC_Value($agree['notes'],"string")
        ),"array");
        
        $ret = new XML_RPC_Response($a_ret);
        return $ret;
    }
    
    public function addAgree($params) {
        
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$unitId = $param->scalarval();
        
        $param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$typeId = $param->scalarval();
        
        $param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$startDate = $param->scalarval();
        
        $param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$hours = $param->scalarval();
        
        $param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$length = $param->scalarval();
        
        $param = $params->getParam(5);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$workers = $param->scalarval();
        
        $param = $params->getParam(6);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$notes = $param->scalarval();
        
        $unit = $this->objUnit->getRow('id',$unitId);
        $name = "{$unit['name']}-".date("d M Y",strtotime($startDate));
        
        $id = $this->objAgree->insert(array('typeid'=>$typeId,'implementation'=>$startDate,'length'=>$length,'workers'=>$workers,'unitId'=>$unitId,'notes'=>$notes,'name'=>$name));
        $this->objBenefits->insert(array('agreeid'=>$id, 'value'=>$hours, 'nameid'=>"init_7"));
        
        $ret = new XML_RPC_Value("TRUE", "string");
    	return new XML_RPC_Response($ret);
    }
    
    public function editAgree($params) {
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
        
        $param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$unitId = $param->scalarval();
        
        $param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$typeId = $param->scalarval();
        
        $param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$startDate = $param->scalarval();
        
        $param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$hours = $param->scalarval();
        
        $param = $params->getParam(5);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$length = $param->scalarval();
        
        $param = $params->getParam(6);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$workers = $param->scalarval();
        
        $param = $params->getParam(7);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$notes = $param->scalarval();
        
        $unit = $this->objUnit->getRow('id',$unitId);
        $name = "{$unit['name']}-".date("d M Y",strtotime($startDate));
        
        if ($benefitId = $this->objBenefits->benefitExists($id,"init_7")) {
            $this->objBenefits->update('id',$benefitId,array('value'=>$hours));
        } else {
            $this->objBenefits->insert(array('agreeid'=>$id, 'value'=>$hours, 'nameid'=>"init_7"));
        }
        $this->objAgree->update('id',$id,array('typeid'=>$typeId,'implementation'=>$startDate,'length'=>$length,'workers'=>$workers,'unitId'=>$unitId,'notes'=>$notes,'name'=>$name));
        
        $ret = new XML_RPC_Value("TRUE", "string");
    	return new XML_RPC_Response($ret);
    }
    
    public function deleteAgree($params) {
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
        
        $this->objAgree->deleteAgree($id);
        
        $ret = new XML_RPC_Value("TRUE", "string");
    	return new XML_RPC_Response($ret);
    }
}
?>