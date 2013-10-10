<?php
/**
 *
 * The patch install class for eLearn setup.
 * 
 * All the functionality of this module is in this class, which supplements
 * the setup of an eLearning system type.
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
 * @package   wurfl
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: installscripts_class_inc.php 18511 2010-07-28 09:35:42Z charlvn $
 * @link      http://chisimba.com/
 * @see       http://wurfl.sourceforge.net/
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
 * 
 * The patch install class for eLearn setup.
 * 
 * All the functionality of this module is in this class, which supplements
 * the setup of an eLearning system type. * 
 * @category  Chisimba
 * @package   oeruserdata
 * @author    Derek Keats <derek@dkeats.com>
 * @copyright 2010 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: installscripts_class_inc.php 18511 2010-12-31 16:12:33Z dkeats $
 * @link      http://chisimba.com/
 * @see       http://wurfl.sourceforge.net/
 */
class elearnsettings_installscripts extends dbtable
{
    /**
     * Instance of the altconfig class in the config module.
     *
     * @access private
     * @var    object
     */
    private $objAltConfig;
    
    /**
     * Instance of the dbconfig class in the sysconfig module.
     *
     * @access private
     * @var    object
     */
    private $dbConfig;

    /**
     * The object property initialiser.
     *
     * @access public
     */
    public function init()
    {
       $this->objAltConfig = $this->getObject('altconfig', 'config');
       $this->dbConfig = $this->getObject('dbsysconfig', 'sysconfig');
    }

    /**
     * The actions to perform after installation of the module.
     *
     * @access public
     * @return void
     * 
     */
    public function postinstall()
    {
        $this->setToolbar();
        $this->setConfigType();
        $this->setSystemType();
        $this->setSkin();
    }

    
    /**
     *  Set the toolbar to an elearning toolbar
     * 
     * @access private
     * @return void
     *  
     */
    private function setToolbar()
    {
        $this->dbConfig->changeParam("TOOLBAR_TYPE", "toolbar", "elearning");
    }
    
    /**
     *  Set the skin to an elearning skin defaulting metalic elearning
     * 
     * @access private
     * @return void
     *  
     */
    private function setSkin()
    {
        $this->objAltConfig->setdefaultSkin("metallic-elearn");
    }
    
    /**
     *  Set the system type to elearn in the systext module 
     * 
     * @access private
     * @return void
     *  
     */
    private function setSystemType()
    {
        $this->dbConfig->changeParam("SYSTEM_TYPE", "systext", "elearn");
    }
    
    /**
     *  Set the system type in the system config as well
     * 
     * @access private
     * @return void
     *  
     */
    private function setConfigType()
    {
        $this->objAltConfig->setSystemType("E-Learning Management System");
    }

}
?>