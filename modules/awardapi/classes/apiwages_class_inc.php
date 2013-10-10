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
 * @version   $Id$
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
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class apiwages extends object
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
          $this->objWage = $this->getObject('dbwage');
          $this->objAgree = $this->getObject('dbagreement');
          $this->objPayPeriodTypes = $this->getObject('dbpayperiodtypes');
          $this->objWageSocName = $this->getObject('dbwagesocname');
		  
		} catch (customException $e) {
			customException::cleanUp();
			exit;
		}
	}
    
    public function listWages($params) {
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
        $a_xmlrpc = array();
        $wages = $this->objAgree->getWageList($id);
        foreach ($wages as $wage) {
            $a_xmlrpc[] = new XML_RPC_Value(array(
                    new XML_RPC_Value($wage['id'],"string"),
                    new XML_RPC_Value($wage['name'],"string"),
                    new XML_RPC_Value(round($wage['rate'],2),"string"),
                    //new XML_RPC_Value($wage['period'],"string"),
                    new XML_RPC_Value($wage['notes'],"string")
                ),"array");
        }
        $a_ret = new XML_RPC_Value($a_xmlrpc,"array");
        $ret = new XML_RPC_Response($a_ret);
        return $ret;
    }
    
    public function getWageValues($params) {
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
        
        $wage = $this->objWage->getRow('id',$id);
        $socName = $this->objWageSocName->getWageSoc($id);
        $a_xmlrpc = new XML_RPC_Value(array(
                        new XML_RPC_Value($socName['id'],"string"),
                        new XML_RPC_Value($socName['name'],"string"),
                        new XML_RPC_Value(round($wage['weeklyrate'],2),"string"),
                        new XML_RPC_Value($wage['payperiodtypeid'],"string"),
                        new XML_RPC_Value($wage['notes'],"string")
                    ),"array");
        
        $ret = new XML_RPC_Response($a_xmlrpc);
        return $ret;
    }
    
    public function getSocList($params) {
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$search = $param->scalarval();
        
        $socs = $this->objWageSocName->getSocList($search);
        $a_xmlrpc = array();
        foreach ($socs as $soc) {
            $a_xmlrpc[] =  new XML_RPC_Value(array(
                    new XML_RPC_Value($soc['id'],"string"),
                    new XML_RPC_Value("{$soc['name']} - ({$soc['sample']})","string")
                ),"array");
        }
        $a_ret = new XML_RPC_Value($a_xmlrpc,"array");
        $ret = new XML_RPC_Response($a_ret);
        return $ret;
    }
    
    public function addWage($params) {
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$agreeId = $param->scalarval();
        
        $param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$socId = $param->scalarval();
        
        $param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$ppId = $param->scalarval();
        
        $param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$rate = $param->scalarval();
        
        $param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$notes = $param->scalarval();
        
        $id = $this->objWage->insert(array("agreeid"=>$agreeId,"payperiodtypeid"=>$ppId,"weeklyrate"=>$rate,"notes"=>$notes));
        $this->objWageSocName->insert(array('id'=>$id, 'socnameid'=>$socId, 'gradeid'=>"1", "jobcodeid" => "1"));
                        
        $ret = new XML_RPC_Value("TRUE", "string");
    	return new XML_RPC_Response($ret);
        
    }
    
    public function editWage($params) {
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
        
        $param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$agreeId = $param->scalarval();
        
        $param = $params->getParam(2);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$socId = $param->scalarval();
        
        $param = $params->getParam(3);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$ppId = $param->scalarval();
        
        $param = $params->getParam(4);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$rate = $param->scalarval();
        
        $param = $params->getParam(5);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$notes = $param->scalarval();
        
        $this->objWage->update('id',$id,array("agreeid"=>$agreeId,"payperiodtypeid"=>$ppId,"weeklyrate"=>$rate,"notes"=>$notes));
        
        if ($this->objWageSocName->valueExists("id",$id)) {
            $this->objWageSocName->update('id',$id,array('socnameid'=>$socId));
        } else {
            $this->objWageSocName->insert(array('id'=>$id, 'socnameid'=>$socId, 'gradeid'=>"1", "jobcodeid" => "1"));
        }
                
        $ret = new XML_RPC_Value("TRUE", "string");
    	return new XML_RPC_Response($ret);
        
    }
    
    public function listPayPeriodTypes($params) {
        $ppTypes = $this->objPayPeriodTypes->getAll("ORDER BY id");
        foreach ($ppTypes as $type) {
            $a_xmlrpc[] =  new XML_RPC_Value(array(
                    new XML_RPC_Value($type['id'],"string"),
                    new XML_RPC_Value($type['name'],"string")
                ),"array");
        }
        $a_ret = new XML_RPC_Value($a_xmlrpc,"array");
        $ret = new XML_RPC_Response($a_ret);
        return $ret;
    }
    
    public function deleteWage($params) {
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
        $this->objWage->deleteWage($id);
        $ret = new XML_RPC_Response(new XML_RPC_Value("TRUE","string"));
        return $ret;
    }
}
?>