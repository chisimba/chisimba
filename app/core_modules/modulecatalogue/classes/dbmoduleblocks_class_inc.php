<?php
/* ------------------- modules class extends dbTable ------------- */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* The class representing the modules blocks table
* @author Nic Appleby
* @category Chisimba
* @package Modulecatalogue
* @version $Id$
*/

class dbmoduleblocks extends dbTable
{
    private $objLanguage;
    public $objConfig;
   
    /**
     * standard init function
     *
     */
    public function init() {
    	try {
    		parent::init('tbl_module_blocks');
    		//Config and Language Objects
    		$this->objLanguage =& $this->getObject('language', 'language');
    		$this->objConfig =& $this->getObject('altconfig','config');
    	} catch (Exception $e) {
    		echo customException::cleanUp();
    		exit();
    	}
    }
    
    public function getBlocks($type = NULL) {
    	if ($type == NULL) {
    		$filter = '';
    	} else {
    		$filter = "WHERE blockwidth = '$type'";
    	}
    	return $this->getAll($filter);
    }
    
    public function addBlock($moduleid,$blockName,$width) {
    	$exists = $this->getAll("WHERE moduleid = '$moduleid' AND blockname = '$blockName' AND 'blockwidth' = '$width'");
    	if (count($exists) < 1) {
    		$arrData = array('moduleid'=>$moduleid,'blockname'=>$blockName,'blockwidth'=>$width);
    		$this->insert($arrData);
    	}
    }
    
    public function deleteModuleBlocks($moduleid) {
    	$record = $this->getAll("WHERE moduleid = '$moduleid'");
    	foreach($record as $block) {
    		$this->delete('id',$block['id']);
    	}
    }
    
    public function deleteBlock($moduleid,$blockname) {
    	$record = $this->getAll("WHERE moduleid = '$moduleid' AND blockname = '$blockname'");
    	if (is_array($record)) {
    		$record = current($record);
    	}
    	$this->delete('id',$record['id']);
    }
}
?>