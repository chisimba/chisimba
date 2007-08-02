<?php

/**
 * context db file
 * 
 * Context database abstraction class
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
 * @package   context
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
/* -------------------- dbTable class ----------------*/
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
 * context db file
 * 
 * Context database abstraction class
 * 
 * @category  Chisimba
 * @package   context
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
 class dbcontext extends dbTable{
     /**
     * @var object $objUser : The user Object
     */
     public $objUser;

	 /**
     * @var object $objFSContext : The File System Object for the context
     */
     public $objFSContext;

     /**
    *Initialize by send the table name to be accessed
    */
    public function init(){
        parent::init('tbl_context');
        $this->objUser=&$this->newObject('user','security');
		$this->objFSContext=&$this->newObject('fscontext','context');
    }

    /**
    * Method to get the details for a given
    * context
    * @param  int    $contextId
    * @return array 
    * @access public
    */
    public function getContextDetails($contextCode){
        return $this->getRow('contextCode',$contextCode);
    }

    /**
    * Method that gets the root
    * node for a given context
    * @Param      int $contextId
    * @return     string
    * @deprecated
    */
     public function getRootNode($contextCode){
        $line=$this->getRow('contextcode',$contextCode);
        return $line["rootnodeid"];
    }

    /**
    *Method to get the root Node Id
    * @param  string $contextId : The Context Id
    * @return string
    * @access public
    */
    public function getRootNodeId($contextId=NULL){
        if($contextId==NULL)
        {
            $contextId=$this->getSession('contextId');
        }
        $this->changeTable('tbl_context_parentnodes');
        $line=$this->getRow('tbl_context_parentnodes_has_tbl_context_tbl_context_id',$contextId);
        return $line["id"];
    }

    /**
    * Method to get a field from the
    * current table
    * @param  $fiedname    string : the name of the field
    * @param  $contextCode int    : the context Code
    * @return string       | bool : The field value or FALSE when not found
    * @access public      
    */
    public function getField($fiedname,$contextCode=NULL){
        //if a $contextCode is set then lookup inthe database
        //else look in the session variables
        $this->changeTable('tbl_context');
        if(!isset($contextCode)){
            $contextCode=$this->getContextCode();
            if($this->getSession('context'.$fiedname))
                return $this->getSession('context'.$fiedname);
            else
                return FALSE;
        }else{
            $line= $this->getRow('contextcode',$contextCode);
            if ($line[$fiedname]) {
                return $line[$fiedname];
            }
            else
                return FALSE;
        }
    }

    /**
     * Method to save an context edit
     * @return bool  
     * @access public
     */
    public function saveEdit()
    {
    	
    	try{
            
            
            $contextCode = $this->getContextCode();
            $menuText = htmlentities($this->getParam('menutext'));
            $title = htmlentities($this->getParam('title'));
            $userId = $this->objUser->userId();
            $status = $this->getParam('status');
            $access = $this->getParam('access');
           
            $fields = array(                       
                        'title' => $title,
                        'menutext' => $menuText,
                        'userid' => $userId,
                        'access' => $access,
                        'status' => $status,
                        'dateCreated' => $this->getDate()
                        ); 

            $this->setLastUpdated();
            
            return $this->update('contextcode', $contextCode, $fields);
        }                        
        catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
    }
    
    /**
     * Method to edit the context about
     * Modified by Jarrett L. Jordaan
     * Date : 21 May 2007
     * @param 
     * @return bool  
     * @access public
     */
    public function saveAboutEdit($newCourse = NULL)
    {
    	try{
    		
    		$about = $this->getParam('about');
    		if(!(strlen($about)>0))
			$about = $newCourse['about'];

    		return $this->update('contextcode', $this->getContextCode(), array('about' => $about));
    	}                        
        catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
    }
    
    /**
    * Method to save the context
    * @param      $mode  string: Either edit or add
    * @return     NULL  
    * @access     public
    * @deprecated
    */
    public function saveContext($mode)
    {

        $about = $this->getParam('about');
        $contextCode = $this->getParam('contextcode');
        $title = $this->getParam('title');
        $menuText = $this->getParam('menutext');
        $isActive = $this->getParam('isactive');
        $isClosed = $this->getParam('isclosed');

        $objMMedia =& $this->getObject('parse4mmedia','filters');
        $about = $objMMedia->parseAll($about);
        $about = addslashes($about);

        if ($mode=="edit")
        {
            $rsArray=array(
                'title' => $title,
                'menuText' => $menuText,
                'isClosed' => $isClosed,
                'about' => $about,
                'isActive' => $isActive);
            return $this->update("contextCode", $contextCode, $rsArray);
        }else
            die('Unkown mode');

    }

    /**
     * Method to create the context
     * Modified by Jarrett L. Jordaan
     * Date : 21 May 2007
     * @return boolean
     */
    public function createContext($newCourse = NULL)
    {
        try{
            $contextCode = htmlentities($this->getParam('contextcode'));
		if(!(strlen($contextCode)>0))
	    		$contextCode = $newCourse['contextcode'];
            $contextCode = strtolower(str_replace(' ','_',$contextCode));
		$contextCode = strtolower(str_replace('$','_',$contextCode));
            $menuText = htmlentities($this->getParam('menutext'));
		if(!(strlen($menuText)>0))
			$menuText = $newCourse['menutext'];
            $title = htmlentities($this->getParam('title'));
		if(!(strlen($title)>0))
 			$title = $newCourse['title'];
            $userId = $this->objUser->userId();
		if(!(strlen($userId)>0))
 			$userId = $newCourse['userid'];
            $status = $this->getParam('status');
		if(!(strlen($status)>0))
 			$status = $newCourse['isclosed'];
            $access = $this->getParam('access');
		if(!(strlen($access)>0))
 			$access = $newCourse['isactive'];
            $contextCode = $contextCode;

            if($this->valueExists('contextcode', $contextCode))
            {
                //check if there is an entry in the database

                return FALSE;
            } else {
                //check if the folder exist
                if($this->objFSContext->folderExists($contextCode) == FALSE)
                { 
                    //create the folder
                    $this->objFSContext->createContextFolder($contextCode);
                    
                } else {
                    
                    return FALSE;
                }
            }
            
            $contextGroups=&$this->getObject('manageGroups','contextgroups');
            $contextGroups->createGroups($contextCode, $title);
            
            $fields = array(
                        'contextcode' => $contextCode,
                        'title' => $title,
                        'menutext' => $menuText,
                        'userid' => $userId,
                        'access' => $access,
                        'status' => $status,
                        'dateCreated' => $this->getDate()
                        ); 

            $this->setLastUpdated();
            
            $ret = $this->insert($fields);
            
            $this->joinContext($contextCode);
            
            return $ret;
        }                        
        catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
    }
    
    /**
    * Method to create a context
    * @param  $rootNodeId  int : The root node ID
    * @return $contextCode : The contextCode
    * @access public      
    */
    public function OLD_createContext()
    {
        $this->changeTable('tbl_context');
		$objDBParentNodes = & $this->getObject('dbparentnodes', 'context');
        $objDBParentBridge = & $this->getObject('dbcontextparentnodes', 'context');

        $contextCode = $this->getParam('contextCode');
        if ($this->objFSContext->createContextFolder($contextCode))
		{
			$title = $this->getParam('title');
			$menuText = $this->getParam('menutext');
			$isActive = $this->getParam('isactive');
			$about = $this->getParam('about');

			if ($isActive='on')
				$isActive=1;
			else
				$isActive=0;

			//add a context
			$contextId=$this->insert(array(
					'contextCode'=> $contextCode,
					'title' => $title,
					'dateCreated' => date("Y-m-d H:i:s"),
					'about' => $about,
					'menutext' => $menuText,
					'isActive' => $isActive));

			// add parent nodes
			$objDBParentBridge->createEntry($contextId, $contextCode);
			$objDBParentNodes->createEntry($contextId, $contextCode, $title);

			$this->resetTable();
			//create groups
			$contextGroups=&$this->getObject('manageGroups','contextgroups');
            $contextGroups->createGroups($contextCode, $title);

			return $contextId;
		}
		else
		{
			return FALSE;
		}
    }

    /**
    * Method that allows users
    * to enter a context
    * @return bool  
    * @access public
    */
    public function joinContext($contextCode=''){
        //$this->changeTable('tbl_context');
        
        if ($contextCode == '') {
            $contextCode=$this->getParam('contextCode');
        }

        if(!isset($contextCode))
        {
        	 $contextCode=$this->getParam('context_dropdown');
        }

        if(isset($contextCode))
        {
            $this->leaveContext();
            $line=$this->getRow('contextCode',$contextCode);
           
            $this->setSession('contextId',$line['id']);
            $this->setSession('contextCode',$contextCode);
            $this->setSession('contextTitle',stripslashes($line['title']));
            $this->setSession('contextmenuText',stripslashes($line['menutext']));
            $this->setSession('contextstatus',$line['status']);
            $this->setSession('contextlastupdatedby',$line['lastupdatedby']);
            $this->setSession('contextaccess',$line['access']);
            $this->setSession('contextdateCreated',$line['datecreated']);
            $this->setSession('contextcreatorId',$line['userid']);
            $this->setSession('contextabout',stripslashes($line['about']));

            return TRUE;
        }
        else
            return FALSE;
    }

    /**
    * Method that allows one
    * to leave a context that you
    * are currently in
    * @return array 
    * @access public
    */
    public function leaveContext(){
        $this->unsetSession('contextCode');
        $this->unsetSession('contextId');
        $this->unsetSession('contextTitle');
        $this->unsetSession('contextmenuText');
        $this->unsetSession('contextabout');
        $this->unsetSession('contextIsActive');
        $this->unsetSession('contextIsClosed');
        $this->unsetSession('contextDateCreated');
        $this->unsetSession('contextCreatorId');
        $objModule =& $this->getObject('modules','modulecatalogue');
       	if ($objModule->checkIfRegistered('workgroup', 'workgroup')) {
            $objDbWorkgroup =& $this->getObject('dbWorkgroup', 'workgroup');
            $objDbWorkgroup->unsetWorkgroupId();
		}
   	}

    /**
    * Method to retrieve the
    * contextCode from the Session Variable
    * @return contextCode
    * @access public     
    */
    public function getContextCode()
    {
        return $this->getSession('contextCode');
    }

    /**
    * Method to get the Title of
    * course that you are currenly logged into
    * @access public 
    * @return context Title
    */
    public function getTitle($contextCode=NULL)
    {
        $this->resetTable();
        if(isset($contextCode))
        {
            $line=$this->getRow('contextCode',$contextCode);
            return $line["title"];
        }else{
            return $this->getSession('contextmenuText');
        }
    }

    /**
    * Method to get the MenuText
    * @param  string $contextCode : The contextCode
    * @return array 
    * @access public
    */
    public function getMenuText($contextCode=NULL)
    {
        if(isset($contextCode))
        {
            $line=$this->getRow('contextCode',$contextCode);
            return $line["menutext"];
        }else{
            return $this->getSession('contextmenuText');
        }

    }
    
    /**
    * Method to get the MenuText
    * @param  string $contextCode : The contextCode
    * @return array 
    * @access public
    */
    public function getAbout($contextCode=NULL)
    {
        if(isset($contextCode))
        {
            $line=$this->getRow('about',$contextCode);
            return $line["about"];
        }else{
            return $this->getSession('contextabout');
        }

    }

    /**
    *Methods to check if one
    *is in a context
    * @access public
    *                return boolean $ret
    */

    public function isInContext(){
        if ($this->getContextCode())
            $ret=TRUE;
        else
            $ret=FALSE;

        return $ret;
    }

    /**
    *Method to return a list
    *of courses
    * @return array 
    * @access public
    */
    public function getListOfContext(){
        return $this->getAll();
    }

    /**
    *Method to return a list
    *of public courses
    * @return array 
    * @access public
    */
    public function getListOfPublicContext(){
        return $this->getAll("WHERE access='Open' OR access='Public' ORDER BY menutext");
    }

    /**
    *Method to return a
    *formatted xml for a sql
    * @param  string $contextCode: The contextCode
    * @param  string $sql          : The optional sql
    * @return string $smlstring : The result returned as xml
    * @access public
    */
    public function getContextXML($contextCode=NULL,$sql=NULL){
        if($contextCode==NULL)
        {
            $contextCode=$this->getContextCode();
        }
         include_once("XML/sql2xml.php");
        $objDBConfig=&$this->newObject('dbconfig','config');
        //create a sql2xml object and parse the database connection
        $sql2xmlclass = new xml_sql2xml($objDBConfig->dbConString());
        //get the xml with the given sql

        if(isset($sql))
            $xmlstring = $sql2xmlclass->getxml($sql);
        else
            $xmlstring = $sql2xmlclass->getxml("Select * from tbl_context,tbl_context_parentnodes,tbl_context_nodes,tbl_context_page_content where tbl_context.contextCode='".$contextCode."'");

        return $xmlstring;

    }

    /**
    * Method to delete a course
    * @param  string $contextCode: The Context Code
    * @return array 
    * @access public
    */

    public function deleteContext($contextCode){
        $this->delete('contextCode',$contextCode);
    	//delete groups
        $contextGroups=&$this->getObject('manageGroups','contextgroups');
        $contextGroups->deleteGroups($contextCode);

   		$this->objFSContext->deleletContextFolder($contextCode);
    }

    /**
    *Method to get the parent nodes
    * @param  string $contextId : The Context Id
    * @return the    parent Node Id
    * @access public
    */
    public function getParentNodes($contextId=NULL){
        //set the working to to tbl_context_parentnodes
        $this->changeTable('tbl_context_parentnodes');
        if(isset($contextId)){
            $ret=$this->getRow('id',$contextId);
        }else{
            $ret=$this->getRow('id',$this->getSession('contextId'));
        }
        //reset the table
        $this->reset();
        return $ret;
    }

    /**
    *Method to get the contextId
    * @return string
    * @access public
    */
    public function getContextId(){
        return $this->getSession('contextId');
    }

    /**
    *Method to change the working table
    * @return NULL  
    * @access public
    */
    public function changeTable($tableName){
        parent::init($tableName);
    }

    /**
    *Method to reset the working table
    * @return NULL  
    * @access public
    */
    public function resetTable(){
        parent::init('tbl_context');
    }

    /**
    *Method to get the current Formmatted date
    * @return string
    * @access public
    */
    public function getDate(){
        return date("Y-m-d H:i:s");
    }

    /**
    * Method to get the course details for a given
    * root node Id
    * @param  string $rootNodeId The root nodeId
    * @return array 
    */
    public function rootToContext($rootNodeId){
        $this->changeTable('tbl_context_parentnodes');
        $line = $this->getRow('id', $rootNodeId);
       // print_r($line);
        $this->resetTable();
        return $this->getContextDetails($line['tbl_context_parentnodes_has_tbl_context_tbl_context_contextCode']);

    }
    
    /**
     * Method to set the last updated field
     * @return bool
     */
    public function setLastUpdated()
    {
    	$fields = array('updated' => $this->getDate(),
    					'lastupdatedby' => $this->objUser->userId());
    	return $this->update('contextcode' ,$this->getContextCode(),$fields);
    }
    
    
    /**
    * Method to archive a context
    * @param  string  $contextCode
    * @return boolean
    */
    public function archiveContext($contextCode)
    {
     	$this->leaveContext($contextCode);
		return $this->update('contextcode' ,$contextCode,array('archive' => 1));
	}
	
	 /**
    * Method to archive a context
    * @param  string  $contextCode
    * @return boolean
    */
    public function undeleteContext($contextCode)
    {
     	$this->update('contextcode' ,$contextCode,array('archive' => 0));
     	return TRUE; 
	}
    

}
?>