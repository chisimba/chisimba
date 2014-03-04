<?php

/**
 * Zoopy controller class
 * 
 * Class to control the zoopy module
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
 * @category  chisimba
 * @package   zoopy
 * @author    Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright 2009 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @link      http://www.zoopy.com/
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
// end security check

/**
 * Zoopy controller class
 *
 * Class to control the zoopy module.
 *
 * @category  Chisimba
 * @package   zoopy
 * @author    Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright 2009 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @link      http://www.zoopy.com/
 */

class zoopy extends controller
{
    /**
     * Object of the dbsysconfig class in the sysconfig module.
     *
     * @access protected
     * @var object
     */
    protected $objSysConfig;

    /**
     * Object of the zoopylib class in the zoopy module.
     *
     * @access protected
     * @var object
     */
    protected $objZoopyLib;

    /**
     * Initialises the instance variables.
     *
     * @access public
     */
    public function init()
    {
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objZoopyLib = $this->getObject('zoopylib', 'zoopy');
    }

    /**
     * Gets the name of the template to be used.
     *
     * @access public
     * @return string Name of the template.
     */
    public function dispatch()
    {
        $uri = $this->objSysConfig->getValue('mod_zoopy_feed_uri', 'zoopy');
        if ($uri) {
            $this->objZoopyLib->loadFeed($uri);
            $this->appendArrayVar('headerParams', '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('zoopy.css', 'zoopy').'">');
            return 'main_tpl.php';
        } else {
            $this->nextAction('step2', array('pmodule_id'=>'zoopy'), 'sysconfig');
        }
    }

    /**
     * Determines of the user needs to be logged on in order to view the page.
     *
     * @access public
     * @return boolean True if the user needs to be logged in, false otherwise.
     */
    public function requiresLogin()
    {
        return false;
    }
}
