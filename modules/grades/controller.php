<?php
/**
 * 
 * grades
 * 
 * Module to hold grades - can be used in conjunction with the schools module
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
 * @package   grades
 * @author    Kevin Cyster kcyster@gmail.com
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @to      http://www.chisimba.com
 *
 */
 
// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 * 
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *         
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
* 
* Controller class for Chisimba for the module grades
*
* @author Kevin Cyster
* @package grades
*
*/
class grades extends controller
{
    
    /**
    * 
    * @var string $objConfig String object property for holding the 
    * configuration object
    * @access public;
    * 
    */
    public $objConfig;
    
    /**
    * 
    * @var string $objLanguage String object property for holding the 
    * language object
    * @access public
    * 
    */
    public $objLanguage;
    /**
    *
    * @var string $objLog String object property for holding the 
    * logger object for logging user activity
    * @access public
    * 
    */
    public $objLog;

    /**
    * 
    * Intialiser for the grades controller
    * @access public
    * 
    */
    public function init()
    {
        $this->objGroupOps = $this->getObject('groupops', 'groupadmin');
        $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('config', 'config');
        
        // Create an instance of the database class
        $this->objDBgrades = $this->getObject('dbgrades', 'grades');
        $this->objDBsubjects = $this->getObject('dbsubjects', 'grades');
        $this->objDBstrands = $this->getObject('dbstrands', 'grades');
        $this->objDBclasses = $this->getObject('dbclasses', 'grades');
        $this->objDBbridging = $this->getObject('dbbridging', 'grades');
        $this->objOps = $this->getObject('gradesops', 'grades');

        $this->appendArrayVar('headerParams',
          $this->getJavaScriptFile('grades.js',
          'grades'));
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }
    
    
    /**
     * 
     * The standard dispatch method for the grades module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch()
    {
        //Get action from query string and set default to view
        $action=$this->getParam('action', 'view');
        /*
        * Convert the action into a method (alternative to 
        * using case selections)
        */
        $method = $this->__getMethod($action);
        // Set the layout template to compatible one
        $this->setLayoutTemplate('layout_tpl.php');
        /*
        * Return the template determined by the method resulting 
        * from action
        */
        return $this->$method();
    }
    
    
    /*------------- BEGIN: Set of methods to replace case selection ------------*/

    /**
    * 
    * Method corresponding to the view action. It shows the default
    * dynamic canvas template, showing you how to create block based
    * view templates
    * @access private
    * 
    */
    private function __view()
    {
        // All the action is in the blocks
        return "main_tpl.php";
    }/**
     *
     * Method corresponding to the manage action. It shows the 
     * template corresponding to the component selected
     * 
     * @acces private 
     */
    private function __list()
    {
        return 'list_tpl.php';
    }
    
    /**
     *
     * Method corresponding to the form action. It shows 
     * template corresponsding to the component and action
     * 
     * @access private
     */
    private function __form()
    {
        return 'form_tpl.php';
    }
    
    /**
     *
     * Method corresponding to the validate action. if validation
     * is successful it adds the data to the database else it returns
     * form action
     * 
     * @access private 
     */
    private function __validate()
    {
        $type = $this->getParam('type');
        $cancel = $this->getParam('cancel', NULL);
        $id = $this->getParam('id');
        if (!empty($cancel))
        {
            $this->setSession('errors', array());
            return $this->nextAction('list', array('type' => $type));
        }
        
        $data = array();
        $data['name'] = $this->getParam('name');
        $data['description'] = $this->getParam('description');
        
        $errorsFound = $this->objOps->validate($data);
        if (!$errorsFound)
        {
            switch ($type)
            {
                case 'grade':
                    $dbClass = $this->objDBgrades;
                    break;
                case 'subject':
                    $dbClass = $this->objDBsubjects;
                    break;
                case 'strand':
                    $dbClass = $this->objDBstrands;
                    break;
                case 'class':
                    $dbClass = $this->objDBclasses;
                    break;
            }
            
            if (!empty($id))
            {
                $data['modified_by'] = $this->objUser->PKId();
                $data['date_modified'] = date('Y-m-d H:i:s');
                $dbClass->updateData($id, $data);             
            }
            else
            {
                $data['created_by'] = $this->objUser->PKId();
                $data['date_created'] = date('Y-m-d H:i:s');
                $newId = $dbClass->insertData($data);
                
                if ($newId && $type == 'grade')
                {
                    $this->objGroups->addGroup($data['name'], $data['description']);
                }
            }
            $this->setSession('errors', array());
            return $this->nextAction('list', array('type' => $type));
        }
        return $this->nextAction('form', array('type' => $type, 'id' => $id));
    }
       
    /**
    * 
    * Method corresponding to the delete action. It requires a 
    * confirmation, and then delets the item, and then sets 
    * nextAction to be null, which returns the {yourmodulename} module 
    * in view mode. 
    * 
    * @access private
    */
    private function __delete()
    {
        $type = $this->getParam('type');
        $id = $this->getParam('id');
        
        switch ($type)
        {
            case 'grade_id':
                $this->objDBgrades->deleteData($id);
                break;
            case 'subject_id':
                $this->objDBsubjects->deleteData($id);
                break;
            case 'strand_id':
                $this->objDBstrands->deleteData($id);
                break;
            case 'class_id':
                $this->objDBclasses->deleteData($id);
                break;
        }       
        $this->objDBbridging->deleteLinks($type, $id);
        return $this->nextAction('list', array('type' => substr($type, 0, -3)));
    }
    
    /**
     *
     * Method that corresponds to the to action. It returns the 
     * page to to vaarious lerning components
     * 
     * @access private 
     */
    private function __link()
    {
        return 'link_tpl.php';
    }
    
    /**
     *
     * Method that corresponds to the saveto action. It returns the 
     * page to to various learning components after saving a to
     * 
     * @access private 
     */
    private function __savelink()
    {
        $from = $this->getParam('from');
        $to = $this->getParam('to');
        
        $id = $this->getParam($from, NULL);
        $link = $this->getParam($to, NULL);

        $data = array();
        $data[$from] = $id;
        $data[$to] = $link;        
        $data['created_by'] = $this->objUser->PKId();
        $data['date_created'] = date('Y-m-d H:i:s');
        
        $tab = substr($to, 0, -3);

        $savedId = $this->objDBbridging->insertData($data);
        
        // save associated links.
        $links = $this->objDBbridging->getAll();
        $linkArray = $links;
        
        foreach ($links as $item)
        {
            $data = array();
            $data['created_by'] = $this->objUser->PKId();
            $data['date_created'] = date('Y-m-d H:i:s');
            $data[$to] = $link;
            
            if (array_key_exists($from, $item) && !empty($item[$from]))
            {
                foreach ($item as $field => $value)
                {
                    if ($field != $from && $field != 'id' && $field != 'created_by' && $field != 'date_created'
                        && $field != 'modified_by' && $field != 'date_modified' && $field != 'puid')
                    {
                        if (!empty($value) && $value != $link)
                        {
                            $data[$field] = $value;
                            
                            $exists = FALSE;
                            foreach ($linkArray as $row)
                            {
                                if ($row[$to] == $link && $row[$field] == $value)
                                {
                                    $exists = TRUE;
                                }
                            }
                            if (!$exists)
                            {
                                $this->objDBbridging->insertData($data);
                            }
                        }
                    }
                }
            }
        }

        // save associated links.
        $links = $this->objDBbridging->getAll();
        $linkArray = $links;
        
        foreach ($links as $item)
        {
            $data = array();
            $data['created_by'] = $this->objUser->PKId();
            $data['date_created'] = date('Y-m-d H:i:s');
            $data[$to] = $link;
            
            if (array_key_exists($to, $item) && !empty($item[$to]))
            {
                foreach ($item as $field => $value)
                {
                    if ($field != $to && $field != 'id' && $field != 'created_by' && $field != 'date_created'
                        && $field != 'modified_by' && $field != 'date_modified' && $field != 'puid')
                    {
                        if (!empty($value) && $value != $id)
                        {
                            $data[$field] = $value;
                            
                            $exists = FALSE;
                            foreach ($linkArray as $row)
                            {
                                if ($row[$from] == $id && $row[$field] == $value)
                                {
                                    $exists = TRUE;
                                }
                            }
                            if (!$exists)
                            {
                                $this->objDBbridging->insertData($data);
                            }
                        }
                    }
                }
            }
        }

        return $this->nextAction('link', array('type' => substr($from, 0, -3), 'id' => $id, 'tab' => $tab));        
    }
    
    /**
     *
     * Method that corresponds to the ajaxShowSubject action.
     * It returns the html for the ajax request
     * 
     * @access private 
     */
    private function __ajaxShowSubject()
    {
        return $this->objOps->ajaxShowSubject();
    }
    
    /**
     *
     * Method that corresponds to the ajaxShowStrand action.
     * It returns the html for the ajax request
     * 
     * @access private 
     */
    private function __ajaxShowStrand()
    {
        return $this->objOps->ajaxShowStrand();
    }
    
    /**
     *
     * Method that corresponds to the ajaxShowContext action.
     * It returns the html for the ajax request
     * 
     * @access private 
     */
    private function __ajaxShowContext()
    {
        return $this->objOps->ajaxShowContext();
    }
    
    /**
     *
     * Method that corresponds to the deletelink action. It returns the 
     * page to to various learning components after saving a link
     * 
     * @access private 
     */
    private function __deletelink()
    {
        $type = $this->getParam('type');
        $link = $this->getParam('link');
        $id = $this->getParam('id');
        $del = $this->getParam('del');

       $this->objDBbridging->deleteLink($del);

        return $this->nextAction('link', array('type' => $type, 'id' => $id, 'tab' => $link));
    }
    
    /**
    * 
    * Method to return an error when the action is not a valid 
    * action method
    * 
    * @access private
    * @return string The dump template populated with the error message
    * 
    */
    private function __actionError()
    {
        return 'dump_tpl.php';
    }
    
    /**
    * 
    * Method to check if a given action is a valid method
    * of this class preceded by double underscore (__). If it __action 
    * is not a valid method it returns FALSE, if it is a valid method
    * of this class it returns TRUE.
    * 
    * @access private
    * @param string $action The action parameter passed byref
    * @return boolean TRUE|FALSE
    * 
    */
    function __validAction(& $action)
    {
        if ($this->objUser->isLoggedIn())
        {
            $groupId = $this->objGroups->getId("School Managers");
            $userId = $this->objUser->userId();
            if ($this->objUser->isAdmin() || 
                $this->objGroupOps->isGroupMember($groupId, $userId ))
            {
                return TRUE;
            }
            else
            {
                return FALSE;
            }
        }

        if (method_exists($this, "__".$action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /**
    * 
    * Method to convert the action parameter into the name of 
    * a method of this class.
    * 
    * @access private
    * @param string $action The action parameter passed byref
    * @return string the name of the method
    * 
    */
    function __getMethod(& $action)
    {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__actionError";
        }
    }
    
    /*------------- END: Set of methods to replace case selection ------------*/
    


    /**
    *
    * This is a method to determine if the user has to 
    * be logged in or not. Note that this is an example, 
    * and if you use it view will be visible to non-logged in 
    * users. Delete it if you do not want to allow annonymous access.
    * It overides that in the parent class
    *
    * @return boolean TRUE|FALSE
    *
    */
    public function requiresLogin()
    {
        $action=$this->getParam('action','NULL');
        switch ($action)
        {
            default:
                return TRUE;
                break;
        }
     }
}
?>