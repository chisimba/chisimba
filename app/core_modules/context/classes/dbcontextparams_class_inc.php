<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check

 /**
 * Class to access the ContextParams Tables 
 * @package context
 * @copyright 2004, University of the Western Cape & AVOIR Project
 * @license GNU GPL
 * @author Wesley  Nitsckie
 **/
class dbcontextparams extends dbTable{
     /**
    *Initialize by send the table name to be accessed 
    */
     function init(){
        parent::init('tbl_contextparams');
        
        $this->_objDBContext = & $this->newObject('dbcontext', 'context');
    }
    
   
    /**
     * Method to add a parameter
     * @param string $param
     * @param string $value
     * @param string $contextCode
     * @access public
     * @return bool
     */
    public function setParam($contextCode, $param, $value = null)
    {
    	try{
	    	$fields = array(
	    				'param' => $param,
	    				'value' => $value,
	    				'contextcode' => $contextCode);
	    	
	    	if($this->getParamValue($contextCode, $param))
	    	{
	    		//edit the param
	    		$sql = "UPDATE tbl_contextparams SET value = '".$value."' WHERE contextcode = '".$contextCode."' AND param = '".$param."'";
	    		return $this->getArray($sql);
	    	} else {
	    		return $this->insert($fields);	
	    	}
	    	
    	
    	 }                        
        catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
    				
    }
    
    /**
     * Method to get a param
     * @param string $contextCode
     * @param string $param
     * @return string
     */
    public function getParamValue($contextCode, $param)
    {
    	
    	$line = $this->getAll("WHERE contextcode = '".$contextCode."'  AND param = '".$param."'");
    	if(count($line) > 0)
    	{
    		return $line[0]['value'];
    	} else {
    		return FALSE;
    	}  	
    	
    }
    
 }

?>