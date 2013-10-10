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
class apisoc extends object {

    /**
     * init method
     * 
     * Standard Chisimba init method
     * 
     * @return void  
     * @access public
     */
    public function init() {
        try { 
            $this->objSocMajorGroup = $this->getObject('dbsocmajorgroup');
            $this->objSocSubMajorGroup = $this->getObject('dbsocsubmajorgroup');
            $this->objSocMinorGroup = $this->getObject('dbsocminorgroup');
            $this->objSocUnitGroup = $this->getObject('dbsocunitgroup');
            $this->objSocName = $this->getObject('dbsocname');
            $this->objWageSocName = $this->getObject('dbwagesocname');
	} catch (Exception $e){
            throw customException($e->getMessage());
            exit();
        }
    }
    
    public function listSocMajorGroup($params) {
        
        $socs = $this->objSocMajorGroup->getAll("ORDER BY id");

        $socarray = array();
        if (is_array($socs)) {
            foreach ($socs as $soc) {
                $sql = "SELECT COUNT(wagesocname.id) AS sample
                        FROM tbl_award_wage_socname AS wagesocname,
                            tbl_award_socname AS socname
                        WHERE wagesocname.socnameid = socname.id
                            AND socname.major_groupid = '{$soc['id']}'";
                $socCount = $this->objWageSocName->getArray($sql);
                $a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value($soc['id'], "string"),
                new XML_RPC_Value($soc['description'], "string"),
                new XML_RPC_Value($socCount[0]['sample'], "string")
                ), "array");

                $socarray[] = $a_xmlrpc;
            }
            $ret = new XML_RPC_Value($socarray, "array");
            return new XML_RPC_Response($ret);
        }
    }
    
    public function listSocSubMajorGroup($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
        $socs = $this->objSocSubMajorGroup->getAll("WHERE major_groupid = '$id' ORDER BY id");

        $socarray = array();
        if (is_array($socs)) {
            foreach ($socs as $soc) {
                $sql = "SELECT COUNT(wagesocname.id) AS sample
                        FROM tbl_award_wage_socname AS wagesocname,
                            tbl_award_socname AS socname
                        WHERE wagesocname.socnameid = socname.id
                            AND socname.submajor_groupid = '{$soc['id']}'";
                $socCount = $this->objWageSocName->getArray($sql);
                $a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value($soc['id'], "string"),
                new XML_RPC_Value($soc['description'], "string"),
                new XML_RPC_Value($socCount[0]['sample'], "string")
                ), "array");

                $socarray[] = $a_xmlrpc;
            }
            $ret = new XML_RPC_Value($socarray, "array");
            return new XML_RPC_Response($ret);
        }
    }
    
    public function listSocMinorGroup($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
        $socs = $this->objSocMinorGroup->getAll("WHERE submajor_groupid = '$id' ORDER BY id");

        $socarray = array();
        if (is_array($socs)) {
            foreach ($socs as $soc) {
                $sql = "SELECT COUNT(wagesocname.id) AS sample
                        FROM tbl_award_wage_socname AS wagesocname,
                            tbl_award_socname AS socname
                        WHERE wagesocname.socnameid = socname.id
                            AND socname.minor_groupid = '{$soc['id']}'";
                $socCount = $this->objWageSocName->getArray($sql);
                $a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value($soc['id'], "string"),
                new XML_RPC_Value($soc['description'], "string"),
                new XML_RPC_Value($socCount[0]['sample'], "string")
                ), "array");

                $socarray[] = $a_xmlrpc;
            }
            $ret = new XML_RPC_Value($socarray, "array");
            return new XML_RPC_Response($ret);
        }
    }
    
    public function listSocUnitGroup($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
        $socs = $this->objSocUnitGroup->getAll("WHERE minor_groupid = '$id' ORDER BY id");

        $socarray = array();
        if (is_array($socs)) {
            foreach ($socs as $soc) {
                $sql = "SELECT COUNT(wagesocname.id) AS sample
                        FROM tbl_award_wage_socname AS wagesocname,
                            tbl_award_socname AS socname
                        WHERE wagesocname.socnameid = socname.id
                            AND socname.unit_groupid = '{$soc['id']}'";
                $socCount = $this->objWageSocName->getArray($sql);
                $a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value($soc['id'], "string"),
                new XML_RPC_Value($soc['description'], "string"),
                new XML_RPC_Value($socCount[0]['sample'], "string")
                ), "array");

                $socarray[] = $a_xmlrpc;
            }
            $ret = new XML_RPC_Value($socarray, "array");
            return new XML_RPC_Response($ret);
        }
    }
    
    public function listSocName($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
        $socs = $this->objSocName->getAll("WHERE unit_groupid = '$id' ORDER BY id");

        $socarray = array();
        if (is_array($socs)) {
            foreach ($socs as $soc) {
                $sql = "SELECT COUNT(id) AS sample
                        FROM tbl_award_wage_socname
                        WHERE socnameid = '{$soc['id']}'";
                $socCount = $this->objWageSocName->getArray($sql);
                $a_xmlrpc = new XML_RPC_Value(array(
                new XML_RPC_Value($soc['id'], "string"),
                new XML_RPC_Value($soc['name'], "string"),
                new XML_RPC_Value($socCount[0]['sample'], "string")
                ), "array");

                $socarray[] = $a_xmlrpc;
            }
            $ret = new XML_RPC_Value($socarray, "array");
            return new XML_RPC_Response($ret);
        }
    }
   
    public function updateSocMajorGroup($params) {
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
        
        $this->objSocMajorGroup->update('id',$id,array('description'=>$name));
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
    
    public function insertSocMajorGroup($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $name = $param->scalarval();
        
        $this->objSocMajorGroup->insert(array('description'=>$name));
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
    
    public function updateSocSubMajorGroup($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
    
        $param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $parentId = $param->scalarval();
        
        $param = $params->getParam(2);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $name = $param->scalarval();
        
        $this->objSocSubMajorGroup->update('id',$id,array('major_groupid'=>$parentId,'description'=>$name));
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
    
    public function insertSocSubMajorGroup($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $parentId = $param->scalarval();
        
        $param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $name = $param->scalarval();
        
        $this->objSocSubMajorGroup->insert(array('major_groupid'=>$parentId,'description'=>$name));
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
    
    public function updateSocMinorGroup($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
    
        $param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socMajId = $param->scalarval();
        
        $param = $params->getParam(2);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socSubMajId = $param->scalarval();
        
        $param = $params->getParam(3);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $name = $param->scalarval();
        
        $this->objSocMinorGroup->update('id',$id,array('major_groupid'=>$socMajId, 'submajor_groupid'=>$socSubMajId, 'description'=>$name));
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
    
    public function insertSocMinorGroup($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socMajId = $param->scalarval();
        
        $param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socSubMajId = $param->scalarval();
        
        $param = $params->getParam(2);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $name = $param->scalarval();
        
        $this->objSocMinorGroup->insert(array('major_groupid'=>$socMajId, 'submajor_groupid'=>$socSubMajId, 'description'=>$name));
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
    
    public function updateSocUnitGroup($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
    
        $param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socMajId = $param->scalarval();
        
        $param = $params->getParam(2);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socSubMajId = $param->scalarval();
        
        $param = $params->getParam(3);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socMinId = $param->scalarval();
        
        $param = $params->getParam(4);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $name = $param->scalarval();
        
        $this->objSocUnitGroup->update('id',$id,array('major_groupid'=>$socMajId, 'submajor_groupid'=>$socSubMajId, 'minor_groupid'=>$socMinId, 'description'=>$name));
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
    
    public function insertSocUnitGroup($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socMajId = $param->scalarval();
        
        $param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socSubMajId = $param->scalarval();
        
        $param = $params->getParam(2);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socMinId = $param->scalarval();
        
        $param = $params->getParam(3);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $name = $param->scalarval();
        
        $this->objSocUnitGroup->insert(array('major_groupid'=>$socMajId, 'submajor_groupid'=>$socSubMajId, 'minor_groupid'=>$socMinId, 'description'=>$name));
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
    
    public function updateSocName($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $id = $param->scalarval();
    
        $param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socMajId = $param->scalarval();
        
        $param = $params->getParam(2);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socSubMajId = $param->scalarval();
        
        $param = $params->getParam(3);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socMinId = $param->scalarval();
        
        $param = $params->getParam(4);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socUnitId = $param->scalarval();
        
        $param = $params->getParam(5);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $name = $param->scalarval();
        
        $this->objSocName->update('id',$id,array('major_groupid'=>$socMajId, 'submajor_groupid'=>$socSubMajId, 'minor_groupid'=>$socMinId, 'unit_groupid'=>$socUnitId, 'name'=>$name));
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
    
    public function insertSocName($params) {
        $param = $params->getParam(0);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socMajId = $param->scalarval();
        
        $param = $params->getParam(1);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socSubMajId = $param->scalarval();
        
        $param = $params->getParam(2);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socMinId = $param->scalarval();
        
        $param = $params->getParam(3);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $socUnitId = $param->scalarval();
        
        $param = $params->getParam(4);
        if (!XML_RPC_Value::isValue($param)) {
            log_debug($param);
        }
        $name = $param->scalarval();
        
        $this->objSocName->insert(array('major_groupid'=>$socMajId, 'submajor_groupid'=>$socSubMajId, 'minor_groupid'=>$socMinId, 'unit_groupId'=>$socUnitId, 'name'=>$name));
        
        $ret = new XML_RPC_Value("TRUE", "string");
        return new XML_RPC_Response($ret);
    }
}   
?>