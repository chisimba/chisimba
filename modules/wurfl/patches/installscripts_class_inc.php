<?php

/**
 * WURFL Installer Class
 * 
 * Performs the necessary actions to install the WURFL mobile device detection library.
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
 * WURFL Installer Class
 * 
 * Performs the necessary actions to install the WURFL mobile device detection library.
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
class wurfl_installscripts extends object
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
     */
    public function postinstall()
    {
        // Compile the path to the WURFL resources directory.
        $resources = $this->objAltConfig->getModulePath() . 'wurfl/resources/';

        // Unzip the WURFL XML file.
        $zip = new ZipArchive();
        if ($zip->open($resources . 'wurfl-2.0.18.xml.zip') === TRUE) {
            $zip->extractTo($resources);
            $zip->close();
        }
    }
}
?>
