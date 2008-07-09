<?php
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
/**
 * An activity logging class. The class is called within each module to log the action performed by the user.
 * In addition to the action, the user id and the ip address from which the user is accessing the site is stored.
 *
 * @author    Derek Keats, Megan Watson
 * @copyright GPL
 * @package   logger
 * @version   0.2
 */
class logactivity extends dbTable
{
    /**
     * Property to hold the user object
     *
     * @var string $objUser The user object
     */
    public $objUser;
    /**
     * Property to hold the modules object
     *
     * @var string $objMod The user object
     */
    public $objMod;
    /**
     * Property for the event code
     *
     * @var string $eventcode The eventcode for the logged event
     */
    public $eventcode;
    /**
     * Property for the name of the parameter to save
     *
     * @var string $eventcode The parameter name
     */
    public $eventParamName;
    /**
     * Property for the value of the parameter to save
     *
     * @var string $eventcode The parameter value
     */
    public $eventParamValue;
    /**
     * Property for the value of flag saying whether the
     * paramValue is multilingualized
     *
     * @var string $eventcode The parameter value
     */
    public $isLanguageCode;
    /**
     * Property to set whether to log more than once per
     * session. Use for often updated modules for example
     * chat refresh.
     *
     * @var BOOLEAN $logOncePerSession @values TRUE|FALSE
     *              
     */
    public $logOncePerSession;
    
    /**
     * Constructor method
     */
    public function init()
    {
        try {
            //  Set the parent table
            parent::init('tbl_logger');
            //  Set default to log each time the page is loaded
            $this->logOncePerSession = FALSE;
            //  Get an instance of the user object
            $this->objUser = $this->getObject('user', 'security');
            $this->userId = $this->objUser->userId();
            
        } catch(Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    } //function init
    


    /**
     * Method to add the current event. It checks if the property logOncePerSession
     * is set and if so it checks if it has already been loged in this session.
     */
    public function log()
    {
        // Check if logger is registered - save in session to prevent additional DB hits.
        if($this->checkIfReg()){
            
            // Set the default value for eventcode - not been implemented yet
            $this->eventcode = "pagelog";
            $this->eventParamName = "parameters";
            $this->eventParamValue = $this->_cleanParams();
            
            $module = $this->getParam('module');
            
            //Check if its set to log once per session
            if ($this->logOncePerSession == TRUE) {
                if (!$this->_isLoggedThisSession($module)) {
                    $this->_setSessionFlag($module);
                    $this->_logData();
                    return TRUE;
                }
                return FALSE;
            } else {
                $this->_logData();
                return TRUE;
            }
        }
        return FALSE;
    } // function log()

    /**
    * Method to log an event other than a page event
    */
    public function logEvent($eventcode = 'pagelog')
    {
        $this->eventcode = $eventcode;
        $this->_logData();
    }
    
    /********************* PRIVATE METHODS BELOW THIS LINE *****************/
    
    
    /**
    * Method to check if the module has been registered.
    * After the first check, the result is stored in session for additional checks
    * If the current module is the modulecatalogue - reset the session - for if logger gets installed.
    *
    * @author Megan Watson
    * @access private
    * @return bool   
    */
    private function checkIfReg()
    {
        $isReg = $this->getSession('logIsReg');
        $module = $this->getParam('module');
        
        if(isset($isReg) && !empty($isReg) && $module != 'modulecatalogue'){
            if($isReg == 'true'){
                return TRUE;
            }else{
                return FALSE;
            }
        }
        
        $objMod = $this->getObject('modules', 'modulecatalogue');
        if ($objMod->checkIfRegistered('logger', 'logger')) {
            $this->setSession('logIsReg', 'true');
            return TRUE;
        }else{
            $this->setSession('logIsReg', 'false');
            return FALSE;
        }
    }
    
    /**
     * Method to return the querystring with the module=modulecode
     * part removed. It builds this using $_GET to work on different servers
     */
    private function _cleanParams()
    {   
        $str = '';
        $amp = '';
        foreach ($_GET as $item=>$value)
        {
            if ($item != 'module') {
                $str .= $amp.$item.'='.$value;
                $amp = '&';
            }
        }
        
        return $str;
        
    } //function _cleanParams

    /**
     * Method to return the context for Logging
     */
    private function _getContext()
    {
        $this->objDBContext = $this->getObject('dbcontext', 'context');
        return $this->objDBContext->getContextCode();
    } //function _getContext

    /**
     * Method to check if there is a session log variable set for
     * the module that is passed to it
     *
     * @param string $module The module to look up, usually the
     *                       current module
     *                       
     */
    private function _isLoggedThisSession($module)
    {
        return $this->getSession($module.'_log', FALSE);
    } //function _isLoggedThisSession

    /**
     * Method to set a session log variable set for
     * the module that is passed to it
     *
     * @param string $module The module to set, usually the
     *                       current module
     *                       
     */
    private function _setSessionFlag($module)
    {
        $this->setSession($module.'_log', TRUE);
    } //function _setSessionFlag

    /**
    * Method to get the id of the previously logged call for the current user - saved in session
    *
    * @author Megan Watson
    * @access private
    * @return string  $id
    */
    private function getPreviousId()
    {
        $id = $this->getSession('previous_log_id');
        return $id;
    }

    /**
    * Method to set the id of the current log in session.
    *
    * @author Megan Watson
    * @access private
    * @return void   
    */
    private function setPreviousId($id)
    {
        $this->setSession('previous_log_id', $id);
    }

    /**
     * Method to log the data to the database
     *  id - The framework generated primary key
     *  userId - The userId of the currently logged in user
     *  module - The module code from the querystring
     *  eventCode - A code to represent the event
     *  eventParamName - The type of event parameters sent
     *  eventParamValue - Any parameters the event needs to send
     *  context - The context of the event
     *  dateCreated - The datetime stamp for the event
     */
    private function _logData()
    {
    	if(date('d') == '01')
    	{
    		$this->_execute('TRUNCATE TABLE tbl_logger');
    		// put in an event handler to do something with the log file
    		$this->_monthlyCleanup();
    	}
        $action = $this->getParam('action');
        $previousId = $this->getPreviousId();
        $ip = $_SERVER['REMOTE_ADDR'];
     
        $module = $this->getParam('module');
        if($module == '_default' || $module == ''){
            $objConfig = $this->getObject('altconfig', 'config');
            $module = $objConfig->getdefaultModuleName();
        }
            
        $referrer = '';
        if(isset($_SERVER['HTTP_REFERER'])) {
            $referrer = $_SERVER['HTTP_REFERER'];
            $check = strpos($referrer, 'module=errors');
            
            if(!($check === FALSE)){
                $referrer = substr($referrer, 0, $check+13);
            }
        }
        
        // Create Array
        $logArray = array(
            'userid' => $this->userId,
            'module' => $module,
            'eventcode' => $this->eventcode,
            'eventparamname' => $this->eventParamName,
            'eventparamvalue' => $this->eventParamValue,
            'context' => $this->_getContext() ,
            'datecreated' => $this->now(),
            'referrer' => $referrer
        );
        
        // If Old Version, Log Current Details
        $objMod = $this->getObject('modules', 'modulecatalogue');
        $version = $objMod->getVersion('logger');
        
        if($version >= '0.7'){
            // Else Add Additional Fields
            $logArray['previous_id'] = $previousId;
            $logArray['action'] = $action;
            $logArray['ipaddress'] = $ip;
            
            logger_log($logArray);
            $this->insert($logArray);
            
        } else if ($version == '0.6'){
            
            $logArray['action'] = $action;
            $logArray['ipaddress'] = $ip;
            
            logger_log($logArray);
            $this->insert($logArray);
        
        }else{
        	$id = $this->insert($logArray); 	 
         	$this->setPreviousId($id);
        	logger_log($logArray);
        }
        

    } //function _logData
    

    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @return boolean Return description (if any) ...
     * @access public 
     */
    public function _monthlyCleanup()
    {
    	$ts = intval(time()/86400)*86400;
    	$this->objConfig = $this->getObject('altconfig', 'config');
    	// rename the current log file to <date>_logger.log
    	$curLog = $this->objConfig->getSiteRootPath()."/error_log/logger.log";
    	$arkLog = $this->objConfig->getSiteRootPath()."/error_log/".$ts."_logger.log";
        if (!file_exists($arklog)){
            copy($curLog, $arkLog);
            unlink($curLog);
        }
    	// mail it to the sys admin?
    	
    	return TRUE;
    }

} //end of class

?>
