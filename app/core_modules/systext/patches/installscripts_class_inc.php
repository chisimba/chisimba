<?php
/**
 *
 * Installer class for the module
 * 
 * The installer class for the module creates a 
 * group called SchoolUserManagers
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
 * Installer class for the module
 * 
 * The installer class for the module creates a 
 * group called Usermanagers
 * 
 * @category  Chisimba
 * @package   schoolregisterusers
 * @author    Derek Keats <derek@dkeats.com>
 * @copyright 2010 Derek Keats
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: installscripts_class_inc.php 18511 2010-12-31 16:12:33Z dkeats $
 * @link      http://chisimba.com/
 * @see       http://wurfl.sourceforge.net/
 */
class systext_installscripts extends dbtable
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
        $this->objUser = $this->getObject('user', 'security');
        $this->userId = $this->objUser->PKId();
        $this->facet = $this->getObject('systext_facet', 'systext');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objModules = $this->getObject('modules', 'modulecatalogue');
    }

    /**
     * The actions to perform after installation of the module.
     *
     * @access public
     * @return void
     * 
     */
    public function postinstall($version)
    {
        // remove data added with earlier patch method
        $types = $this->facet->listSystemTypes();
        foreach ($types as $type)
        {
            $init = stripos($type['id'], 'init_');
            if ($init === FALSE)
            {
                $this->facet->deleteSystemType($type['id']);
                $abstracts = $this->facet->listAbstractText($type['id']);
                foreach ($abstracts as $abstract)
                {
                    $init = stripos($abstract['id'], 'init');
                    if ($init === FALSE)
                    {
                        $this->facet->deleteAbstractText($abstract['id']);
                    }
                }
            }
        }
        $texts = $this->facet->listTextItems();
        foreach ($texts as $text)
        {
            $init = stripos($text['id'], 'init_');
            if ($init === FALSE)
            {
                $this->facet->deleteTextItem($text['id']);
            }
        }
        
        switch ($version)        
        {
            case '1.900':
                $patchFile = $this->objConfig->getSiteRootPath() . 'core_modules/systext/sql/patch-1.900.xml';
                ini_set('max_execution_time','600');
                $objXml = simplexml_load_file($patchFile);
                
                foreach ($objXml as $table => $row)
                {                
                    $sqlArray = array();
                    foreach ($row as $field => $value)
                    {
                        $sqlArray[$field] = $value;
                    }
                    $result = $this->objModules->insert($sqlArray, $table);
                }
                break;
            case '1.910':
                $patchFile = $this->objConfig->getSiteRootPath() . 'core_modules/systext/sql/patch-1.910.xml';
                ini_set('max_execution_time','600');
                $objXml = simplexml_load_file($patchFile);
                
                foreach ($objXml as $table => $row)
                {                
                    $sqlArray = array();
                    foreach ($row as $field => $value)
                    {
                        $sqlArray[$field] = $value;
                    }
                    $result = $this->objModules->insert($sqlArray, $table);
                }
                break;
        }
    }
}
?>