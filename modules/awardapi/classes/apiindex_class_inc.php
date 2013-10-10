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
 * @version   CVS: $Id: apiindex_class_inc.php 74 2008-07-31 12:00:45Z nic $
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
 * @version   $Id: apiindex_class_inc.php 74 2008-07-31 12:00:45Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class apiindex extends object
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
		  $this->objIndex = $this->getObject('dbindex');
		  $this->objIndexValues = $this->getObject('dbindexvalues');    
		} catch (customException $e) {
			customException::cleanUp();
			exit;
		}
	}
	
	/**
	 * Method to return an XML-RPC message
	 *
	 * @param string $message
	 * @return XML-RPC response object
	 */
	public function getMessage($message)
	{
		$message = $message->getParam(0);
		return new XML_RPC_Response($message);
	}
	
    /**
     * XML-RPC method to get a list of indexes and latest values 
     *
     * @param unkown_type $params no params needed
     * @return XML_RPC_Response containing the index information
     */
	public function listIndexes($params) {
	    $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$a_index = $this->objIndex->getAll();
    	$indexarray = array();
    	if (is_array($a_index)) {
    	    foreach ($a_index as $index) {
    	       $latest = $this->objIndexValues->getLatestValue($index['id']);
    	   	   $a_xmlrpc = new XML_RPC_Value(array(
    		      new XML_RPC_Value($index['id'], "string"),
    		      new XML_RPC_Value($index['shortname'], "string"),
    			  new XML_RPC_Value($index['name'], "string"),
    			  new XML_RPC_Value($latest['value'], "string"),
    			  new XML_RPC_Value($latest['indexdate'], "string")
    		   ), "array");
    	
    		$indexarray[] = $a_xmlrpc;
		  }
		  $ret = new XML_RPC_Value($indexarray, "array");
    	  return new XML_RPC_Response($ret);
    	}
	}
	
	/**
     * XML-RPC method to get a list of indexes values 
     *
     * @param unkown_type $params id and year
     * @return XML_RPC_Response containing the index information
     */
	public function getIndexValues($params) {
	    $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
    	$param = $params->getParam(1);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$startYear = $param->scalarval();
    	$a_year = $this->objIndexValues->getValues($id,$startYear);
    	
    	$xmlarray = array();
    	if (is_array($a_year)) {
    	    foreach ($a_year as $year) {
    	       $indexarray = array();
    	       foreach ($year as $value) {
    	           if (empty($value)) {
    	               $value = "-1";    
    	           } else {
    	               $value = $value[0]['value'];
    	           }
    	           $xmlval = new XML_RPC_Value($value, "string");
    	           $indexarray[] = $xmlval;
    	       }
    	       $xmlyear = new XML_RPC_Value($indexarray,"array");
    	       $xmlarray[] = $xmlyear;
    	    }
		 }
		 $ret = new XML_RPC_Value($xmlarray, "array");
    	 return new XML_RPC_Response($ret);
	}
	
	/**
     * XML-RPC method to create an index
     *
     * @param unkown_type $params name and abbreviation
     * @return XML_RPC_Response TRUE
     */
	public function createIndex($params) {
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
    	$this->objIndex->insert(array("shortname"=>$abbreviation,"name"=>$name,"description"=>$name,"period"=>1,"display"=>"TRUE"));
    	
		$ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
	}
	
	/**
     * XML-RPC method to edit an index
     *
     * @param unkown_type $params id, name and abbreviation
     * @return XML_RPC_Response TRUE
     */
	public function editIndex($params) {
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
    	$this->objIndex->update('id',$id,array("shortname"=>$abbreviation,"name"=>$name,"description"=>$name,"period"=>1,"display"=>"TRUE"));
    	
		$ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
	}
	
	/**
     * XML-RPC method to create an index
     *
     * @param unkown_type $params id
     * @return XML_RPC_Response TRUE
     */
	public function deleteIndex($params) {
	    $param = $params->getParam(0);
		if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$id = $param->scalarval();
    	
    	$this->objIndex->delete('id',$id);
    	
		$ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
	}

	/**
	 * Method to update the index values in the db
	 *
	 * @param unknown_type $params an xml-rpc type array of all the index values to be updated
	 * @return xmlrpc response "true" on success
	 */
	public function updateIndexValues($params) {
	    $param = $params->getParam(0);
	    if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
    	}
    	$a_params = $param->scalarval();
	    $array_params = unserialize($a_params);
	    foreach ($array_params as $trip) {
	        $a_trip = unserialize($trip);
	        foreach ($a_trip as $key => $ord) { 
	           switch ($key) {
	               case 0:
	                   $indexId = $ord;
	                   break;
	                   
	               case 1:
					   $date = date("Y-m-d",strtotime($ord));
	                   break;
	                   
	               case 2:
	                   $val = $ord;
	                   break;
	           }
	        }
	        log_debug("indexid: $indexId date: $date value: $val");
	        if ($id = $this->objIndexValues->valueExists($indexId,$date)) {
	            $this->objIndexValues->update("id",$id,array("indexdate"=>$date,"value"=>$val));
	        } else {
	            $this->objIndexValues->insert(array("typeid"=>$indexId,"indexdate"=>$date,"value"=>$val));
	        }
	    }
	    $ret = new XML_RPC_Value("true","string");
	    return new XML_RPC_Response($ret);
	}

}
?>