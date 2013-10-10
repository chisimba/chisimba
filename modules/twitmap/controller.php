<?php

/**
 *
 * Twitter interactive map module
 *
 * Twitter is a module that creates an integration between your Chisimba
 * site using your Twitter account now with Google maps integrattion.
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
 * @package   twitmap
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS:$
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
* Controller class for Chisimba for the module twitter
*
* @author Paul Scott <pscott@uwc.ac.za>
* @package twitmap
*
*/
class twitmap extends controller
{

    /**
    * object property for holding the configuration object
    *
    * @var string $objConfig String
    * @access public;
    */
    public $objConfig;

    /**
    * object property for holding the language object
    *
    * @var string $objLanguage String
    * @access public
    */
    public $objLanguage;
    /**
    * object property for holding the logger object for logging user activity
    *
    * @var string $objLog String
    * @access public
    */
    public $objLog;

    /**
    * object property for holding the twitmap operations object
    *
    * @var string $objOps String
    * @access public
    */
    public $objOps;


    /**
    * Intialiser for the twitmap controller
    *
    * @access public
    */
    public function init()
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        // Create the configuration object
        $this->objConfig = $this->getObject('altconfig', 'config');
        // Get the activity logger class
        $this->objLog=$this->newObject('logactivity', 'logger');
        // Log this module call
        $this->objLog->log();
        $this->objOps = $this->getObject('twitmapops');
    }


    /**
     *
     * The standard dispatch method for the twittermap module.
     * The dispatch method uses methods determined from the action
     * parameter of the  querystring and executes the appropriate method,
     * returning its appropriate template. This template contains the code
     * which renders the module output.
     *
     * @param void
     * @return string
     * @access public
     */
    public function dispatch($action = NULL)
    {
        switch ($action) {
            default:
            	$this->nextAction('showkml', array('time' => time()));
            	break;

            case 'showkml':
                // simply load the cached XML into a variable and send to the template
                $dataarr = $this->objOps->grabXML();
                $this->setVarByRef('data', $dataarr);

                $kml = $this->objOps->makeMap($dataarr);
                $this->setVarByRef('kml', $kml);
                header('Content-type: text/xml');
                echo $kml;
                //return 'view_tpl.php';
       }

   }

/**
         * Overide the login object in the parent class
         *
         * @param  void
         * @return bool
         * @access public
         */
        public function requiresLogin()
        {

           return FALSE;

        }
}
?>