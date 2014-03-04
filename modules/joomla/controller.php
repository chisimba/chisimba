<?php

/**
 * 
 * Make Joomla a Chisimba plugin
 * 
 * Module to interface to Joomla, which must be unpacked into
 * resources/joomla for it to work. This smodule allows Joomla to be
 * installed with a Chisimba site. 
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
 * @package   joomla
 * @author    Derek Keats <dkeats@uwc.ac.za>
 * @copyright 2007 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 11943 2008-12-29 21:23:33Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * 
 */



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
*
* Controller for a module to interface to Joomla, which must be 
* unpacked into resources/joomla for it to work
*
* @author    Derek Keats
* @version   1.0
* @copyright 2005 GNU GPL
*            
*/
class joomla extends controller
{

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
    public $objConfig;

    /**
     * Description for public
     * @var    unknown
     * @access public 
     */
    public $lists;

    /**
    * 
    * Standard constructor method 
    * 
    */
    public function init()
    {
        
        // Create  an instance of the configuration object
       ////////////////USED??? $this->objConfig = $this->getObject('dbsysconfig', 'sysconfig');
        //Get the page for the list signup
        $this->joomla = $this->getResourceUri('joomla/index.php','joomla');
        //Create an instance of the language object
        $this->objLanguage = $this->getObject("language", "language");
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }

    /**
    * 
    * Standard dispatch method 
    * 
    */
    public function dispatch()
    { 
        //Get action from query string and set default to view
        $action=$this->getParam('action', 'default');
        /*
        * Convert the action into a method (alternative to
        * using case selections)
        */
        try {
            $method = $this->_getMethod($action);
            //Return the template determined by the method resulting from action
            return $this->$method();
        }
        catch (customException $e)
        {
            customException::cleanUp();
            exit;
        }
    }
    
    public function _default()
    {
        $this->setVar('uri', $this->joomla);
        return 'main_tpl.php';
    }
    
    public function _confirminstallation()
    {
        $objJoomla = $this->getObject('joomlabridge', 'joomla');
        $objJoomla->setJoomlaStatus("STAGE1_INSTALL");
        $this->setVar('uri', $this->joomla);
        return 'main_tpl.php';
    }
    
    public function _copyusers()
    {
        $objJoomla = $this->getObject('joomlabridge', 'joomla');
        $str = $objJoomla->copyUsers();
        $objJoomla->setJoomlaStatus("COMPLETED");
        $this->setVarByRef('str', $str);
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
    private function _actionError()
    {
        $str = "<br /><br /><span class=\"error\">"
          . $this->objLanguage->languageText("mod_joomla_invalidaction", "joomla")
          . ": <em>" .$this->getParam("action", NULL) . "</em></span><br /><br />";
        $this->setVarByRef('str', $str);
        return 'dump_tpl.php';
    }
  
    /**
    *
    * Method to check if a given action is a valid method
    * of this class preceded by double underscore (_). If the action
    * is not a valid method it returns FALSE, if it is a valid method
    * of this class it returns TRUE.
    *
    * @access private
    * @param string $action The action parameter
    * @return boolean TRUE|FALSE
    *
    */
    private function _validAction($action)
    {
        if (method_exists($this, "_".$action)) {
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
    * @param string $action The action parameter
    * @return stromg the name of the method
    *
    */
    private function _getMethod($action)
    {
        if ($this->_validAction($action)) {
            return "_" . $action;
        } else {
            return "_actionError";
        }
    }

} #end of class
?>
