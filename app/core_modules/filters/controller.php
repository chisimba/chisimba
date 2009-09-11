<?php

/**
 * This module contains functionality
 * for handling filters in chisimba
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
 * @package   filters
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see

 */
/* -------------------- stories class extends controller ----------------*/
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
 * This is the entry point of the module.
 * @author Derek Keats
 *
 */
class filters extends controller {
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
    * @var string $objLanguage String object property for holding the
    * user object
    * @access public
    *
    */
    public $objUser;
    /**
    *
    * @var string $objLog String object property for holding the
    * logger object for logging user activity
    * @access public
    *
    */
    public $objLog;
	
    /**
    * Intialiser for the filter object
    */
    function init()
    { 
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
     *
     * The standard dispatch method for the this module.
     * The dispatch method uses methods determined from the action
     * parameter of the  querystring and executes the appropriate method,
     * returning its appropriate template. This template contains the code
     * which renders the module output.
     *
     */
    public function dispatch()
    {
        //Get action from query string and set default to view
        $this->action=$this->getParam('action', 'missingaction');
        /*
        * Convert the action into a method (alternative to
        * using case selections)
        */
        $method = $this->__getMethod($this->action);
        /*
        * Return the template determined by the method resulting
        * from action
        */
        return $this->$method();
    }
    
    /**
     * 
     * Method to disable all filters by moving them to the disabled directory
     * @access public
     */
    public function __disableall()
    {
    	if($this->objUser->isAdmin()) {
	        $objFilterManager = $this->getObject('filtermanager', 'filters');
	        $str = $objFilterManager->disableAll();
            $this->setvar('str', $this->objLanguage->languageText('mod_filters_weridness','filters'));
    	}
        return 'dump_tpl.php';
    }

     /**
     * 
     * Method to enable all filters by moving them from the disabled directory
     * back to the classes directory
     * @access public
     *
     */
    public function __enableall()
    {
    	if($this->objUser->isAdmin()) {
	        $objFilterManager = $this->getObject('filtermanager', 'filters');
	        $str = $objFilterManager->enableAll();
	        $this->setvar('str', $this->objLanguage->languageText('mod_filters_allenabled','filters')); 
    	}
        return 'dump_tpl.php';
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
        $this->setVar('str', "<h3>"
          . $this->objLanguage->languageText("phrase_unrecognizedaction")
          .": " . $this->action . "</h3>");
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
    function __getMethod($action)
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
    * be logged in or not. This module always requires
    * login
    *
    * @return boolean TRUE|FALSE
    *
    */
    public function requiresLogin()
    {
        return TRUE;
    }

} 

?>