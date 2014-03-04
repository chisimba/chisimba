<?php
/**
 *
 * Installer class for the module
 * 
 * The installer class for the module that sets up scripts
 * etc.
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
 * @package   backup
 * @author    Derek Keats <derek@dkeats.com>
 * @copyright 2012 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: installscripts_class_inc.php 18511 2010-07-28 09:35:42Z charlvn $
 * @link      http://chisimba.com/
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
 * Installer class for the module
 * 
 * The installer class for the module that sets up scripts
 * etc.
 * 
 * @category  Chisimba
 * @package   backup
 * @author    Derek Keats <derek@dkeats.com>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: installscripts_class_inc.php 18511 2010-12-31 16:12:33Z dkeats $
 * @link      http://chisimba.com/
 */
class backup_installscripts extends dbtable
{
    /**
     * Instance of the altconfig class in the config module.
     *
     * @access private
     * @var    object
     */
    private $objAltConfig;

    /**
     * The object property initialiser.
     *
     * @access public
     */
    public function init()
    {
       $this->objAltConfig = $this->getObject('altconfig', 'config');
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
        $this->writeBackupScript();
    }
    
    /**
     * 
     * Copy the default about text file to the about directory
     * in usrfiles
     * 
     * @access public
     * @return VOID
     * 
     */
    public function writeBackupScript()
    {
        $backupScript = $this->objAltConfig->getModulePath() . 'backup/resources/backup.sh';
        $targetScript = '/var/chisimba/scripts/backup.sh';
        copy($backupScript, $targetScript);
    }
}
?>