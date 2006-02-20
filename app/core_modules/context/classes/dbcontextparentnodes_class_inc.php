<?php
/* -------------------- dbTable class ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
} 
// end security check
/**
* Class to access the Context Tables 
* @package dbfile
* @category context
* @copyright 2004, University of the Western Cape & AVOIR Project
* @license GNU GPL
* @version 
* @author Wesley  Nitsckie
* @example :
*/

 class dbcontextparentnodes extends dbTable{
 
	 /**
	 * @var object objDBContext;
	 */
	 var $objDBContext;
     
     /**
	 * @var object objUser;
	 */
	 var $objUser;
	 
	 /**
	 * Constructor
	 */
     function init(){
         parent::init('tbl_context_parentnodes_has_tbl_context');
		 $this->objDBContext = & $this->newObject('dbcontext', 'context');
         $this->objUser = & $this->newObject('user', 'security');
     }
     
     
     /**
     * Method add a entry to the database
     * @param string $contextId The context ID
     */
     function createEntry($contextId, $contextCode)
     {
       if(!$this->valueExists('tbl_context_contextCode',$contextCode))
       {           
            //create a bridge entry            
            $this->insert(array(
                'tbl_context_contextCode' => $contextCode,                
                'tbl_context_id '=>$contextId));       
        }      
     }
     
     
	 
	
 }
 ?>