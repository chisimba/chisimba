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

 class dbparentnodes extends dbTable{
 
	 /**
	 * @var object objDBContext;
	 */
	 var $objDBContext;
     
     /**
	 * @var object objUser;
	 */
	 var $objUser;
     
     
     function init(){
         parent::init('tbl_context_parentnodes');
		 $this->objDBContext = & $this->newObject('dbcontext', 'context');
         $this->objUser = & $this->newObject('user', 'security');
     }
     
	  /**
	 * Method to get the parentNode
	 * @param string $contextCode The Context Code
	 * @return string
	 */
	 function getParentNodeId($contextCode=NULL){
		 if(!isset($contextCode)){
			 $contextCode = $this->objDBContext->getContextCode();
		 }
		 
		 $row = $this->getRow('tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode', $contextCode);
		 return $row['id'];
	 }
     
     /**
     * Method add a entry to the database
     * @param string $contextId The context ID
     */
     function createEntry($contextId, $contextCode, $title = NULL){
       if(!$this->valueExists('tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode',$contextCode)){                                              
            $rootId=$this->insert(array(
                'tbl_context_parentnodes_has_tbl_context_tbl_context_id' => $contextId,
                'tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode' => $contextCode,
                'title' => $title,
                'datemodified' => $this->getDate(),
                'dateCreated' => $this->getDate(),
                'userId' =>$this->objUser->userId(),
                'menu_text' => $title));
            
        }  
     
     }
     
     /**
    *Method to return a formatted date string
    */
    function getDate(){
        return date("Y-m-d H:i:s");
    }
    
 }
 ?>