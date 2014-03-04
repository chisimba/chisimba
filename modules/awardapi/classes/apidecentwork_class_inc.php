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
class apidecentwork extends object
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
		  $this->objDWCategories = $this->getObject('dbdwcategories');
		  $this->objDWValues = $this->getObject('dbdwvalues');
		} catch (Exception $e){
            throw customException($e->getMessage());
            exit();
        }
    }
    
    public function listCategories($params) {
        
        $cats = $this->objDWCategories->getAll("ORDER BY category");

        $catarray = array();
        if (is_array($cats)) {
            foreach ($cats as $cat) {
                $a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value($cat['id'], "string"),
                new XML_RPC_Value($cat['category'], "string")
                ), "array");

                $catarray[] = $a_xmlrpc;
            }
            $ret = new XML_RPC_Value($catarray, "array");
            return new XML_RPC_Response($ret);
        }
    }
    
	public function listValues($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
        $values = $this->objDWValues->getAll("WHERE categoryid = '$id' ORDER BY year DESC");

        $valarray = array();
        if (is_array($values)) {
            foreach ($values as $value) {
                $a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value($value['id'], "string"),
                new XML_RPC_Value($value['label'], "string"),
                new XML_RPC_Value($value['value'], "string"),
                new XML_RPC_Value($value['unit'], "string"),
                new XML_RPC_Value($value['source'], "string"),
                new XML_RPC_Value($value['year'], "string"),
                new XML_RPC_Value($value['notes'], "string")
                ), "array");

                $valarray[] = $a_xmlrpc;
            }
            $ret = new XML_RPC_Value($valarray, "array");
            return new XML_RPC_Response($ret);
        }
    }
    
    public function updateCategory($params) {
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
        
        $this->objDWCategories->update('id',$id,array('category'=>$name));
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
    
    public function insertCategory($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $name = $param->scalarval();
        
        $this->objDWCategories->insert(array('category'=>$name));
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
    
    public function deleteCategory($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
        $this->objDWCategories->deleteCategory($id);
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
	
	public function updateValue($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
		
		$param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $categoryid = $param->scalarval();
    
        $param = $params->getParam(2);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $label = $param->scalarval();
        
		$param = $params->getParam(3);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $value = $param->scalarval();
		
        $param = $params->getParam(4);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $unit = $param->scalarval();
		
        $param = $params->getParam(5);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $source = $param->scalarval();
		
        $param = $params->getParam(6);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $year = $param->scalarval();
		
        $param = $params->getParam(7);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $notes = $param->scalarval();
        
        $this->objDWValues->update('id',$id,array('categoryid'=>$categoryid,
												  'label'=>$label,
												  'value'=>$value,
												  'unit'=>$unit,
												  'source'=>$source,
												  'year'=>$year,
												  'notes'=>$notes));
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
	
	public function insertValue($params) {    
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $categoryid = $param->scalarval();
        
		$param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $label = $param->scalarval();
        
		$param = $params->getParam(2);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $value = $param->scalarval();
		
        $param = $params->getParam(3);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $unit = $param->scalarval();
		
        $param = $params->getParam(4);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $source = $param->scalarval();
		
        $param = $params->getParam(5);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $year = $param->scalarval();
		
        $param = $params->getParam(6);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $notes = $param->scalarval();
        
        $this->objDWValues->insert(array('categoryid'=>$categoryid,
										 'label'=>$label,
										 'value'=>$value,
										 'unit'=>$unit,
										 'source'=>$source,
										 'year'=>$year,
										 'notes'=>$notes));
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
	
	public function getValueData($params) {
		$param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
		
		$value = $this->objDWValues->getRow('id',$id);
		$a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value($value['label'], "string"),
                new XML_RPC_Value($value['value'], "string"),
                new XML_RPC_Value($value['unit'], "string"),
                new XML_RPC_Value($value['source'], "string"),
                new XML_RPC_Value($value['year'], "string"),
                new XML_RPC_Value($value['notes'], "string")
                ), "array");
		return new XML_RPC_Response($a_xmlrpc);
	}
    
    public function deleteValue($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
        $this->objDWValues->delete('id',$id);
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
}
?>