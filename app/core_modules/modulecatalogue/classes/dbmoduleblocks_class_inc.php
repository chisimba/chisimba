<?php
/* ------------------- modules class extends dbTable ------------- */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
* The class representing the modules blocks table
* @author   Nic Appleby
* @category Chisimba
* @package  Modulecatalogue
* @version  $Id$
*/

class dbmoduleblocks extends dbTable
{

    /**
     * Description for private
     * @var    unknown
     * @access private
     */
    private $objLanguage;

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
    public $objConfig;
   
    /**
     * standard init function
     *
     */
    public function init() {
    	try {
    		parent::init('tbl_module_blocks');
    		//Config and Language Objects
    		$this->objLanguage = $this->getObject('language', 'language');
    		$this->objConfig = $this->getObject('altconfig','config');
    	} catch (Exception $e) {
    		echo customException::cleanUp();
    		exit();
    	}
    }
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $type Parameter description (if any) ...
     * @return string  Return description (if any) ...
     * @access public 
     */
    public function getBlocks($type = NULL) {
    	if ($type == NULL) {
    		$filter = '';
    	} else {
    		$filter = "WHERE blockwidth = '$type'";
    	}
    	return $this->getAll($filter);
    }
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $moduleid  Parameter description (if any) ...
     * @param  unknown $blockName Parameter description (if any) ...
     * @param  unknown $width     Parameter description (if any) ...
     * @return void   
     * @access public 
     */
    public function addBlock($moduleid,$blockName,$width) {
        $exists = $this->getAll(" WHERE moduleid = '$moduleid' AND blockname = '$blockName' AND blockwidth = '$width'");
    	if (count($exists) < 1) {
    		$arrData = array('moduleid'=>$moduleid,'blockname'=>$blockName,'blockwidth'=>$width);
    		$this->insert($arrData);
    	}
    }
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $moduleid Parameter description (if any) ...
     * @return void   
     * @access public 
     */
    public function deleteModuleBlocks($moduleid) {
    	$record = $this->getAll("WHERE moduleid = '$moduleid'");
    	foreach($record as $block) {
    		$this->delete('id',$block['id']);
    	}
    }
    
    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $moduleid  Parameter description (if any) ...
     * @param  unknown $blockname Parameter description (if any) ...
     * @return void   
     * @access public 
     */
    public function deleteBlock($moduleid,$blockname) {
    	$record = $this->getAll("WHERE moduleid = '$moduleid' AND blockname = '$blockname'");
    	if (is_array($record)) {
    		$record = current($record);
    	}
    	$this->delete('id',$record['id']);
    }
}
?>