<?php
/**
 * 
 * External blocks
 * 
 * Render blocks from this site so they may be used by an external site, such
 * as another Chisimba site, or any site capable of sending an Ajax request.
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
 * @package   helloforms
 * @author    Derek Keats derek.keats@wits.ac.za
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: controller.php,v 1.4 2007-11-25 09:13:27 dkeats Exp $
 * @link      http://avoir.uwc.ac.za
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
* Controller class for Chisimba for the module externalblocks
*
* @author Derek Keats
* @package externalblocks
*
*/
class externalblocks extends controller
{
    
    
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
    * @var string object $objBlock The block to be provided externally
    * @access public
    *
    */
    public $objBlock;

    /**
    *
    * @var string object $objRender The class to render the block
    * @access public
    *
    */
    public $objRender;

    /**
    * 
    * Intialiser for the externalblocks controller
    * @access public
    * 
    */
    public function init() {
        $this->objRender = $this->getObject ( 'renderblocks', 'externalblocks' );
        $this->objBlock = $this->getObject ( 'blocks', 'blocks' );
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    
    /**
     * 
     * The standard dispatch method for the externalblocks module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch()
    {
        //Get action from query string and set default to view
        $action=$this->getParam('action', 'getForAjax');
        /*
        * Convert the action into a method (alternative to 
        * using case selections)
        */
        $method = $this->__getMethod($action);
        /*
        * Return the template determined by the method resulting 
        * from action
        */
        return $this->$method();
    }
    
    /**
     *
     * Method to get the block requested for display on the remote
     * server via an Ajax call. Note the use of a blank page template.
     *
     * @return string The populated template
     *
     */
    public function __getForAjax()
    {
        $this->setPageTemplate('page_template.php');
        $str = $this->objRender->getBlock();
        $this->setVarByRef( 'str', $str);
        return 'extblock_tpl.php';
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
          .": " . $action . "</h3>");
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
    function __getMethod(& $action)
    {
        if ($this->__validAction($action)) {
            return "__" . $action;
        } else {
            return "__actionError";
        }
    }
    
    /**
    *
    * Set requriresLogin to false since the  external request will not
    * be a login session on the same server except in rare circumstances
    * such as testing during development.
    *
    * @return boolean FALSE
    *
    */
    public function requiresLogin()
    {
        return FALSE;
    }
}
?>