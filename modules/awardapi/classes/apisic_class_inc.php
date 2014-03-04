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
 * @version   CVS: $Id: apisic_class_inc.php 74 2008-07-31 12:00:45Z nic $
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
 * @version   $Id: apisic_class_inc.php 74 2008-07-31 12:00:45Z nic $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class apisic extends object
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
		  $this->objSicDiv = $this->getObject('dbsicdiv');
		  $this->objSicMajorDiv = $this->getObject('dbsicmajordiv');
		  $this->objSicMajorGroup = $this->getObject('dbsicmajorgroup');
		  $this->objSicSubGroup = $this->getObject('dbsicsubgroup');
		  $this->objSicGroup = $this->getObject('dbsicgroup');
		} catch (Exception $e){
            throw customException($e->getMessage());
            exit();
        }
    }
    
    public function getSicDList($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
        $sics = $this->objSicDiv->getAll("WHERE major_divid = '$id' ORDER BY id");

        $sicarray = array();
        if (is_array($sics)) {
            foreach ($sics as $sic) {
                $a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value($sic['id'], "string"),
                new XML_RPC_Value($sic['description'], "string")
                ), "array");

                $sicarray[] = $a_xmlrpc;
            }
            $ret = new XML_RPC_Value($sicarray, "array");
            return new XML_RPC_Response($ret);
        }
    }
    
    public function getSicGList($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
        $sics = $this->objSicGroup->getAll("WHERE major_groupid = '$id' ORDER BY id");

        $sicarray = array();
        if (is_array($sics)) {
            foreach ($sics as $sic) {
                $a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value($sic['id'], "string"),
                new XML_RPC_Value($sic['description'], "string"),
                new XML_RPC_Value($sic['code'], "string")
                ), "array");

                $sicarray[] = $a_xmlrpc;
            }
            $ret = new XML_RPC_Value($sicarray, "array");
            return new XML_RPC_Response($ret);
        }
    }
    
    public function getSicMDList($params) {
        $sics = $this->objSicMajorDiv->getAll("ORDER BY id");

        $sicarray = array();
        if (is_array($sics)) {
            foreach ($sics as $sic) {
                $a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value($sic['id'], "string"),
                new XML_RPC_Value($sic['code'], "string"),
                new XML_RPC_Value($sic['description'], "string")
                ), "array");

                $sicarray[] = $a_xmlrpc;
            }
            $ret = new XML_RPC_Value($sicarray, "array");
            return new XML_RPC_Response($ret);
        }
    }
    
    public function getSicMGList($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
        $sics = $this->objSicMajorGroup->getAll("WHERE divid = '$id' ORDER BY id");

        $sicarray = array();
        if (is_array($sics)) {
            foreach ($sics as $sic) {
                $a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value($sic['id'], "string"),
                new XML_RPC_Value($sic['description'], "string"),
                new XML_RPC_Value($sic['code'], "string")
                ), "array");

                $sicarray[] = $a_xmlrpc;
            }
            $ret = new XML_RPC_Value($sicarray, "array");
            return new XML_RPC_Response($ret);
        }
    }
    
    public function getSicSGList($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
        $sics = $this->objSicSubGroup->getAll("WHERE groupid = '$id' ORDER BY id");

        $sicarray = array();
        if (is_array($sics)) {
            foreach ($sics as $sic) {
                $a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value($sic['id'], "string"),
                new XML_RPC_Value($sic['description'], "string"),
                new XML_RPC_Value($sic['code'], "string")
                ), "array");

                $sicarray[] = $a_xmlrpc;
            }
            $ret = new XML_RPC_Value($sicarray, "array");
            return new XML_RPC_Response($ret);
        }
    }

}   
?>