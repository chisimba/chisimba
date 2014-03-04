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
 * @version   $Id$
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
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class apiconditions extends object
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
		  $this->objConditionTypes = $this->getObject('dbbenefittypes');
		  $this->objConditionNames = $this->getObject('dbbenefitnames');
		  $this->objConditions = $this->getObject('dbbenefits');
		} catch (Exception $e){
            throw customException($e->getMessage());
            exit();
        }
    }
    
    public function listTypes($params) {
        $types = $this->objConditionTypes->getALL("ORDER BY id");
        
        $typearray = array();
        if (is_array($types)) {
            foreach ($types as $type) {
                $a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value($type['id'], "string"),
                new XML_RPC_Value($type['name'], "string")
                ), "array");

                $typearray[] = $a_xmlrpc;
            }
            $ret = new XML_RPC_Value($typearray, "array");
            return new XML_RPC_Response($ret);
        }
    }
    
    public function loadValues($params) {
        $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$agreeId = $param->scalarval();
        
        $param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$typeId = $param->scalarval();
        
        $names = $this->objConditionNames->getAll("WHERE typeid = '$typeId' ORDER BY id");
        $conditionArray = array();
        foreach ($names as $name) {
            $benefit = $this->objConditions->getAll("WHERE agreeId = '$agreeId' AND nameid = '{$name['id']}'");
            $benefit = current($benefit);
            $conditionArray[] = new XML_RPC_Value(array(
                new XML_RPC_Value($benefit['id'],"string"),
                new XML_RPC_Value($name['id'],"string"),
                new XML_RPC_Value($name['name'],"string"),
                new XML_RPC_Value($name['measure'],"string"),
                new XML_RPC_Value($benefit['value'],"string"),
                new XML_RPC_Value($benefit['notes'],"string")
                ),"array");
        }
        $ret = new XML_RPC_Value($conditionArray, "array");
        return new XML_RPC_Response($ret);
    }
    
    public function saveValues($params) {
        $param = $params->getParam(0);
        $condCount = $param->arraysize();
        
        for ($i=0;$i<$condCount;$i++) {
            $condition = $param->arraymem($i);
            $xml_agreeId = $condition->arraymem(0);
            $agreeId = $xml_agreeId->scalarval();
            $xml_nameId = $condition->arraymem(1);
            $nameId = $xml_nameId->scalarval();
            $xml_id = $condition->arraymem(2);
            $id = $xml_id->scalarval();
            $xml_value = $condition->arraymem(3);
            $value = $xml_value->scalarval();
            $xml_notes = $condition->arraymem(4);
            $notes = $xml_notes->scalarval();
            if ($id != "") {
                $this->objConditions->update('id',$id,array('agreeid'=>$agreeId,'nameid'=>$nameId,'value'=>$value,'notes'=>$notes));
            } else {
                $this->objConditions->insert(array('agreeid'=>$agreeId,'nameid'=>$nameId,'value'=>$value,'notes'=>$notes));
            }
        }
        
        $ret = new XML_RPC_Value("TRUE","string");
        return new XML_RPC_Response($ret);
    }
}
?>