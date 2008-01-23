<?php

/**
* Context Object
* 
* This class contains required functionality for creating contexts
* and retrieve context details
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
* @author    Tohir Solomons <tsolomons@uwc.ac.za>
* @copyright 2008 Tohir Solomons
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
* Context Object
* 
* This class contains required functionality for creating contexts
* and retrieve context details
* 
* @category  Chisimba
* @package   context
* @author    Tohir Solomons <tsolomons@uwc.ac.za>
* @copyright 2008 Tohir Solomons
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @version   Release: @package_version@
* @link      http://avoir.uwc.ac.za
* @see       core
*/
class dbcontext extends dbTable
{
    /**
    * @var object $objUser : The user Object
    */
    public $objUser;
    
    
    /**
    *Initialize by send the table name to be accessed
    */
    public function init()
    {
        parent::init('tbl_context');
        $this->objUser = $this->getObject('user','security');
    }
    
    
    /**
    * Method to get the details for a given
    * context
    * @param  string    $contextCode
    * @return array 
    * @access public
    */
    public function getContextDetails($contextCode)
    {
       return $this->getRow('contextcode',$contextCode);
    }
    
    
    /**
    * Method to get a field from the
    * current table
    * @param  $fiedname    string : the name of the field
    * @param  $contextCode int    : the context Code
    * @return string       | bool : The field value or FALSE when not found
    * @access public
    */
    public function getField($fieldname,$contextCode=NULL)
    {
        if(!isset($contextCode)) {
            $contextCode=$this->getContextCode();
        }
        
        $line= $this->getRow('contextcode',$contextCode);
        
        $fieldname = strtolower($fieldname);
        
        if ($line[$fieldname]) {
            return $line[$fieldname];
        } else {
            return FALSE;
        }
    }
    
    
    /**
    * Method to create a context
    * 
    * @param string $contextCode Context Code
    * @param string $title Title of the Context - Menu and Title is the same
    * @param string $status Status of the Context
    * @param string access Access Settings for the Context
    * @param string $about About the Context
    *
    * @return boolean Result of adding a context
    */
    public function createContext($contextCode, $title, $status='Published', $access='Private', $about=NULL)
    {
        $contextCode = preg_replace('/\W*/', '', $contextCode);
        $contextCode = strtolower($contextCode);
        
        if (trim($title) == '') {
            $title = $contextCode;
        }
        
        //check if there is an entry in the database
        if($this->valueExists('contextcode', $contextCode))
        {
            // If Yes, do not create
            return FALSE;
        } else {
            // Insert Record
            $result = $this->insert(array(
                'contextcode' => $contextCode,
                'title' => $title,
                'menutext' => $title,
                'access' => $access,
                'status' => $status,
                'about' => $about,
                'userid' => $this->objUser->userId(),
                'dateCreated' => date("Y-m-d"),
                'updated' => date("Y-m-d H:i:s"),
                'lastupdatedby' => $this->objUser->userId()
            ));
            
            // If Successful
            if ($result) {
                // Prepare to add context to search index
                $objIndexData = $this->getObject('indexdata', 'search');
                
                $docId = 'context_contextcode_'.$contextCode;
                
                $docDate = date('Y-m-d H:M:S');
                $url = $this->uri(array('action'=>'joincontext', 'contextcode'=>$contextCode), 'context');
                $title = $title;
                $contents = $title.' '.$about;
                $teaser = $about;
                $userId = $this->objUser->userId();
                $module = 'context';
                
                // Todo - Set permissions on entering course, e.g. iscontextmember.
                $permissions = NULL;
                
                if (strtolower($access) == 'private') {
                    $permissions = 'iscontextmember';
                }
                
                if (strtolower($status) == 'unpublished') {
                    $permissions = 'iscontextlecturer';
                }
                
                $extra = array('status'=>$status, 'access'=>$access, 'contextcode'=>$contextCode);
                
                $objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId, NULL, NULL, 'root', NULL, $permissions, NULL, NULL, $extra);
                
                
                
                // Create Groups
                $contextGroups=$this->getObject('managegroups','contextgroups');
                $contextGroups->createGroups($contextCode, $title);
                
                // Join Context
                $this->joinContext($contextCode);
            }
           
           // Return Result
           return $result;
        }
    }
    
    
    /**
    * Method to update a context
    * 
    * @param string $contextCode Context Code
    * @param string $title Title of the Context - Menu and Title is the same
    * @param string $status Status of the Context
    * @param string access Access Settings for the Context
    * @param string $about About the Context
    *
    * @return boolean Result of Update
    */
    public function updateContext($contextCode, $title, $status, $access, $about)
    {
        return $this->update('contextcode', $contextCode, array(
            'title' => $title,
            'menutext' => $title,
            'access' => $access,
            'status' => $status,
            'about' => $about,
            'updated' => date("Y-m-d H:i:s"),
            'lastupdatedby' => $this->objUser->userId()
        ));
    }
   
   
   
   
    /**
    * Method to allow users to enter a context
    * @param string $contextCode Context user want to enter
    * @return boolean Result of entering context
    */
    public function joinContext($contextCode='')
    {
        // TODO: Check ability to enter course
        
        if ($contextCode == '') {
            $contextCode = $this->getParam('contextCode');
        }
        
        if (!isset($contextCode))
        {
            $contextCode = $this->getParam('context_dropdown');
        }
        
        if (isset($contextCode))
        {
            $this->leaveContext();
            $line = $this->getRow('contextCode', $contextCode);
            
            $this->setSession('contextId', $line['id']);
            $this->setSession('contextCode', $contextCode);
            return TRUE;
        } else {
            return FALSE;
        }
    }
   
    /**
    * Method to leave the current context
    * @access public
    */
    public function leaveContext()
    {
        $this->unsetSession('contextCode');
        $this->unsetSession('contextId');
        $this->unsetSession('contextTitle');
        $this->unsetSession('contextmenuText');
        $this->unsetSession('contextabout');
        $this->unsetSession('contextIsActive');
        $this->unsetSession('contextIsClosed');
        $this->unsetSession('contextDateCreated');
        $this->unsetSession('contextCreatorId');
        
        // Unset Workgroup Session If it Exists
        $objModule = $this->getObject('modules','modulecatalogue');
        if ($objModule->checkIfRegistered('workgroup', 'workgroup')) {
            $objDbWorkgroup = $this->getObject('dbWorkgroup', 'workgroup');
            $objDbWorkgroup->unsetWorkgroupId();
        }
    }
    
    
    /**
     * Method to check whether a context exists
     * @param string $contextCode Context Code to check
     * @return boolean
     */
    public function contextExists($contextCode)
    {
        return $this->valueExists('contextcode', $contextCode);
    }
   
    /**
    * Method to retrieve the contextCode from the Session Variable
    * @return contextCode
    * @access public
    */
    public function getContextCode()
    {
        return $this->getSession('contextCode');
    }
    
    /**
    * Method to get the contextId
    * @return string
    * @access public
    */
    public function getContextId()
    {
       return $this->getSession('contextId');
    }
    
    /**
    * Method to get the Title of course that user is currently logged into
    * @access public 
    * @return context Title
    */
    public function getTitle($contextCode=NULL)
    {
        if (!isset($contextCode))
        {
            $contextCode = $this->getSession('contextCode');
        }
        
        return $this->getField('title', $contextCode);
    }
    
    /**
    * Method to get the MenuText
    * @param  string $contextCode : The contextCode
    * @return array 
    * @access public
    */
    public function getMenuText($contextCode=NULL)
    {
        if (!isset($contextCode))
        {
            $contextCode = $this->getSession('contextCode');
        }
        
        return $this->getField('menutext', $contextCode);
    }
    
    /**
    * Method to get the MenuText
    * @param  string $contextCode : The contextCode
    * @return array 
    * @access public
    */
    public function getAbout($contextCode=NULL)
    {
       if (!isset($contextCode))
       {
          $contextCode = $this->getSession('contextCode');
       }
       
       return $this->getField('about', $contextCode);
    }
    
    /**
    * Method to check if one is in a context
    * @access public
    * @return boolean
    */
    public function isInContext()
    {
        if ($this->getContextCode()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
    * Method to return a list all of courses
    * @return array 
    * @access public
    */
    public function getListOfContext()
    {
        return $this->getAll(' ORDER BY title');
    }
    
    /**
    * Method to return a list of public courses
    * @return array 
    * @access public
    */
    public function getListOfPublicContext()
    {
        return $this->getAll("WHERE access='Open' OR access='Public' ORDER BY menutext");
    }
    
    
    /**
    * Method to delete a course
    * @param  string $contextCode: The Context Code
    * @return array 
    * @access public
    */
    public function deleteContext($contextCode)
    {
        $this->delete('contextCode',$contextCode);
        //delete groups
        $contextGroups=$this->getObject('manageGroups','contextgroups');
        $contextGroups->deleteGroups($contextCode);
    }
    
    
}
?>