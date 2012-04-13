<?php
/**
 * 
 * jquerycore
 * 
 * Jquerycore is to hold all jquery core, jquery user interface and plugin versions.
 * The engine will load the latest jquery core and jquery user interface automatically.
 * No module may load jquery core or jquery user interface. A specific version may be
 * requested using a call to jquerycore. Module specific plugings should be load via
 * jquerycore only. Since all jquery scripts are loaded through jquerycore duplicate
 * loading of jquery will be reduced, if not removed. Also jquerycore will reduce
 * imcompatability issues as it will load a version of jquery core and the applicable
 * highest version of jquery user interface that is supported by that core version and
 * vice versa. A pluging will also load the applicable highest version of jquery core
 * it requires. Jquerycore will also hold the wrapper classes for jquery plugins. It
 * will also hold various css themes for the jquery user interface
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
 * @package   jquerycore
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
* Controller class for Chisimba for the module jquerycore
*
* @author Kevin Cyster
* @package jquerycore
*
*/
class jquerycore extends controller
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
    * Intialiser for the jquerycore controller
    * @access public
    * 
    */
    public function init()
    {
    }
    
    
    /**
     * 
     * The standard dispatch method for the jquerycore module.
     * The dispatch method uses methods determined from the action 
     * parameter of the  querystring and executes the appropriate method, 
     * returning its appropriate template. This template contains the code 
     * which renders the module output.
     * 
     */
    public function dispatch()
    {
        $location=$this->uri(array(), "_default");
        header("Location:$location");
    }
}
?>
