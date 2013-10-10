<?php
/**
 * 
 * statusbar
 * 
 * This creates a floating bar to hold various icon links to features that should be available to the user at all times regardless of the page the user is viewing.
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
 * @package   statusbar
 * @author    Kevin Cyster kcyster@gmail.com
 * @copyright 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
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
* Controller class for Chisimba for the module statusbar
*
* @author Kevin Cyster
* @package statusbar
*
*/
class statusbar extends controller
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
     * Variable to hold the userId
     * 
     * @access public
     * @var string
     */
    public $userId;

    /**
     * 
     * Variable to hold the PKId
     * 
     * @access public
     * @var string
     */
    public $PKId;

    /**
    * 
    * Intialiser for the statusbar controller
    * @access public
    * 
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->PKId = $this->objUser->PKId();
        $this->userId = $this->objUser->userId();
        $this->objLanguage = $this->getObject('language', 'language');

        // Create the configuration object
        $this->objConfig = $this->getObject('config', 'config');
        
        // Create an instance of the database class
        $this->objDBsettings = $this->getObject('dbstatusbar_settings', 'statusbar');        
        $this->objDBbridging = $this->getObject('dbstatusbar_bridging', 'statusbar');
        
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('statusbar.js', 'statusbar'));
               
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        $this->objOps = $this->getObject('statusbarops', 'statusbar');
        
        $this->objMessage = $this->getObject('dbmessaging', 'messaging');
        $this->objCalendar = $this->getObject('dbcalendar', 'calendarbase');
        //Log this module call
        $this->objLog->log();
    }
    
    
    /**
     * 
     * The standard dispatch method for the statusbar module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch()
    {
        //Get action from query string and set default to view
        $action=$this->getParam('action', 'main');
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
    private function __main()
    {
        // All the action is in the blocks
        return "main_tpl.php";
    }
    
    /**
     *
     * Method corresponding to the ajaxShowSettings action. 
     * 
     * @access private
     * @return VOID
     */
    private function __ajaxShowSettings()
    {
        $settings = array();       
        $settings['orientation'] = $this->objOps->ajaxShowOrientation(FALSE);
        $settings['interval'] = $this->objOps->ajaxShowInterval(FALSE);

        echo json_encode($settings);
        die();
    }
        
    /**
     *
     * Method corresponding to the ajaxShowOrientation action. 
     * 
     * @access private
     * @return VOID
     */
    private function __ajaxShowOrientation()
    {
        return $this->objOps->ajaxShowOrientation();
    }
        
    /**
     *
     * Method corresponding to the ajaxSaveSettings action. 
     * 
     * @access private
     * @return VOID
     */
    private function __ajaxSaveSettings()
    {
        $orientation = $this->getParam('orientation', $this->objOps->orientation);
        $position = $this->getParam('position', $this->objOps->position);
        $display = $this->getParam('display', $this->objOps->display);
        $alert = $this->getParam('alert', $this->objOps->alert);
        
        $this->objOps->orientation = $orientation;
        $this->objOps->position = $position;
        $this->objOps->display = $display;
        $this->objOps->alert = $alert;

        $this->objDBsettings->saveSettings($orientation, $position, $display, $alert);
        
        echo 'true';
        die();
    }
        
    /**
     *
     * Method corresponding to the ajaxSaveMessage action. 
     * 
     * @access private
     * @return VOID
     */
    private function __ajaxSaveMessage()
    {
        $recipient = $this->getParam('recipient');
        $message = $this->getParam('message');
        
        $this->objMessage->addImMessage($recipient, $message);
        
        echo 'true';
        die();
    }
    
    /**
     *
     * Method corresponding to the ajaxShowMessage action
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxShowMessage()
    {
        $messages = $this->objMessage->getInstantMessages();

        $message = array();
        $message['id'] = $messages[0]['mid'];
        $message['from'] = $messages[0]['firstname'] . '&nbsp;' . $messages[0]['surname'];
        $message['message'] = $messages[0]['message'];
        
        echo json_encode($message);
        die();
    }
            
    /**
     *
     * Method corresponding to the ajaxShowPosition action. 
     * 
     * @access private
     * @return VOID
     */
    private function __ajaxShowPosition()
    {
        return $this->objOps->ajaxShowPosition();
    }
        
    /**
     *
     * Method corresponding to the ajaxShowMain action. 
     * 
     * @access private
     * @return VOID
     */
    private function __ajaxShowMain()
    {
        return $this->objOps->showMain(TRUE);
    }
    
    /**
     *
     * Method corrsponding to the ajaxUpdateMessage action
     * 
     * @accesss private
     * @return VOID 
     */
    private function __ajaxUpdateMessage()
    {
        $id = $this->getParam('id');
        
        $this->objMessage->updateMessage($id);
        $messages = $this->objMessage->getInstantMessages();
        
        $count = count($messages);
        
        if ($count > 0)
        {
            echo $count;
        }
        else
        {
            echo 0;
        }
        die();        
    }
        
    /**
     *
     * Method corresponding to the ajaxShowCalendarAlerts action
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxShowCalendarAlerts()
    {
        $events = $this->objOps->ajaxGetCalendarAlerts();
        $from = date('H:i', strtotime($events[0]['timefrom']));
        $to = date('H:i', strtotime($events[0]['timeto']));
        
        $alerts = array();
        $alerts['id'] = ($events[0]['userorcontext'] == 1) ? $events[0]['b_id'] : $events[0]['id'];
        $alerts['type'] = ($events[0]['userorcontext'] == 1) ? 'context' : 'user';
        $alerts['title'] = $events[0]['eventtitle'];
        $alerts['from'] = $from . '&nbsp;-&nbsp;' . $to;
        
        echo json_encode($alerts);
        die();
    }
    
    /**
     *
     * Method corresponding to the ajaxShowContentAlerts action
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxShowContentAlerts()
    {
        $events = $this->objOps->ajaxGetContentAlerts();

        $alerts = array();
        $alerts['id'] = $events[0]['b_id'];
        $alerts['description'] = $events[0]['description'];

        $linkClass = " class='contextcode_" . $events[0]['contextcode'] . "'";
        $alerts['link'] = "<a href='#' id='location_" . $events[0]['b_id'] . "'" . $linkClass . ">" . $events[0]['link'] . "</a>";
        
        echo json_encode($alerts);
        die();
    }
    
    /**
     *
     * Method corrsponding to the ajaxUpdateCalendarAlert action
     * 
     * @accesss private
     * @return VOID 
     */
    private function __ajaxUpdateCalendarAlert()
    {
        $id = $this->getParam('id');
        $type = $this->getParam('type');
        
        if ($type == 'user')
        {
            $this->objCalendar->updateAlert($id);
        }
        else
        {
            $this->objDBbridging->updateAlert($id);
        }
        
        $alerts = $this->objOps->ajaxGetCalendarAlerts();
        
        $count = count($alerts);
        
        if ($count > 0)
        {
            echo $count;
        }
        else
        {
            echo '';
        }
        die();        
    }
        
    /**
     *
     * Method corrsponding to the ajaxUpdateContentAlert action
     * 
     * @accesss private
     * @return VOID 
     */
    private function __ajaxUpdateContentAlert()
    {
        $id = $this->getParam('id');
        $this->objDBbridging->updateAlert($id);
        
        $alerts = $this->objOps->ajaxGetContentAlerts();
        
        $count = count($alerts);
        
        if ($count > 0)
        {
            echo $count;
        }
        else
        {
            echo '';
        }
        die();        
    }
        
    /**
     *
     * Method to set the context
     * 
     * @access private
     * @return VOID 
     */
    private function __ajaxSetContext()
    {
        $contextcode = $this->getParam('contextcode');
        
        $context = $this->objContext->getContextCode();

        if ($contextcode != $context)
        {
            $this->objContext->leaveContext();
            $this->objContext->joinContext($contextcode);
        }
        echo 'true';
        die();
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
    private function __validAction(& $action)
    {
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
    * @return stromg the name of the method
    * 
    */
    private function __getMethod(& $action)
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