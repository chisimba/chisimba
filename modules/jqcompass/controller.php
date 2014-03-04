<?php
/**
 *
 * Compass datagrid
 *
 * Compass DataGrid is an ajax-driven data grid that relies on server-side
 * code for its data. Rather than manipulating an existing table or breaking
 * it down into multiple pages, Compass DataGrid takes an empty table and
 * populates it by connecting to a server-side url via ajax. As users interact
 * with the grid, the grid talks with the server-side script letting it know
 * what the user is requesting. The server-side script then provides JSON
 * encoded data for the plugin to update the table. This is a Chisimba PHP
 * wrapper for Compass.
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
* Controller class for Chisimba for the module jqcompass
*
* @author Derek Keats
* @package jqcompass
*
*/
class jqcompass extends controller
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
    * Intialiser for the jqcompass controller
    * @access public
    * 
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('config', 'config');
        // Create an instance of the database class
        $this->objCompass = & $this->getObject('jqcompasshelper', 'jqcompass');
        //Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        //Log this module call
        $this->objLog->log();
    }
    
    
    /**
     * 
     * The standard dispatch method for the jqcompass module.
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
        // retrieve the mode (edit/add/translate) from the querystring
        $mode = $this->getParam("mode", null);
        // retrieve the sort order from the querystring
        $order = $this->getParam("order", null);
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
    
    
    /*------------- BEGIN: Set of methods to replace case selection ------------*/

    /**
    * 
    * Method corresponding to the view action. It fetches the stories
    * into an array and passes it to a main_tpl content template.
    * @access private
    * 
    */
    private function __view()
    {
        $str="\n\n\n<h1>Developer test code only</h1><br /><br />\n";
        $this->objCompass->loadCss();
        $this->objCompass->loadJs();
        $arrayParams = array(
            'images' => 'packages/jqcompass/resources/images/',
            'url' => 'index.php?module=jqcompass&action=getdata'
        );
        $this->objCompass->buildReadyFunction($arrayParams);
        $this->objCompass->loadReadyFunction();
        $str .= $this->objCompass->buildBaseTable() . "\n\n\n\n\n";
        $this->setVarByRef('str', $str);
        return "dump_tpl.php";
    }
    
    /**
    * 
    * Method corresponding to the edit action. It sets the mode to 
    * edit and returns the edit template.
    * @access private
    * 
    */
    private function __getdata()
    {
        // Must let the datagrid know current PAGINATION info
        // Page: The Current Page
        // Pages: Total number of pages found
        // Found: Total number of results found
        // Displaying start: The First result we are _currently_ showing
        // Displaying End: The last result we are _currently_ showing
        $pager = array(array(
                'page'				=>	1,
                'pages'				=> 	1,
                'found' 			=>	5,
                'displayingStart'	=> 	1,
                'displayingEnd'		=> 	5
        ));

        // Each array represents a column
        // Each column must have:
	// ID	an unique identifier for that column
	// Display		the text to display in that column
	// Width		the width of that column (pixels or percentage)
	// Sort			optional. either sort-asc or sort-desc if you are currently sorting by that column
        $headings = array(
                array(
                        'id'			=>		'one',
                        'display'		=>		'One',
                        'width'			=>		'25%',
                        'sort'			=>		''
                ),
                array(
                        'id'			=>		'two',
                        'display'		=>		'Two',
                        'width'			=>		'25%',
                        'sort'			=>		'sort-asc'
                ),
                array(
                        'id'			=>		'three',
                        'display'		=>		'Three',
                        'width'			=>		'25%',
                        'sort'			=>		''
                ),
                array(
                        'id'			=>		'four',
                        'display'		=>		'Four',
                        'width'			=>		'25%',
                        'sort'			=>		''
                )
        );

        // Each array items represents a new row
        // The array keys correspond to the id in the headings array above
        $rows = array(
                array(
                        'one' 	=>      'A1',
                        'two'	=>	'A2',
                        'three'	=>	'A3',
                        'four'	=> 	'A4',
                ),
                array(
                        'one' 	=>      'B1',
                        'two'	=>	'B2',
                        'three'	=>	'B3',
                        'four'	=> 	'B4',
                ),
                array(
                        'one' 	=>      'C1',
                        'two'	=>	'C2',
                        'three'	=>	'C3',
                        'four'	=> 	'C4',
                ),
                array(
                        'one' 	=>      'D1',
                        'two'	=>	'D2',
                        'three'	=>	'D3',
                        'four'	=> 	'D4',
                ),
                array(
                        'one' 	=>      'E1',
                        'two'	=>	'E2',
                        'three'	=>	'E3',
                        'four'	=> 	'E4',
                )
        );

        // Put it all in an array
        $json = array(
                'pager' => $pager,
                'headings' => $headings,
                'rows' => $rows
        );
        // Set json content-type header
        header("Content-Type: text/x-json");
        echo json_encode($json);
        exit;
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
            case 'view':
                return FALSE;
                break;
            default:
                return TRUE;
                break;
        }
     }
}
?>
