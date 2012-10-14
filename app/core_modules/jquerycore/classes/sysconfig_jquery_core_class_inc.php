<?php
/**
 *
 * Class to provide SysConfig an input for the JQUERY_CORE parameter
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
 * @copyright 2007 AVOIR
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
* Class to provide SysConfig an input for the SYSTEM_TYPE parameter*
*
* @package   jquerycore
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class sysconfig_jquery_core extends object
{
    /**
     * 
     * Variable to hold the current sysconfig core version
     * 
     * @access proteced
     * @var string
     */
    protected $defaultValue;

    /**
     * 
     * Variable to hold jquery core versions
     * 
     * @access proteced
     * @var array
     */
    protected $coreVersions;

    /**
     *
     * Intialiser for the class
     * @access public
     * @return VOID
     *
     */
    public function init()
    {
        $this->getCoreVersions();

        $this->objLanguage = $this->getObject('language', 'language');
        $this->objDropdown = $this->loadClass('dropdown', 'htmlelements');
    }

    /**
     *
     * Method to set the current default value
     * 
     * @access public
     * @param string $value The current value set in sysconfig
     * @return VOID 
     */
    public function setDefaultValue($value)
    {
        $this->defaultValue = $value;
    }
        
    /**
     *
     * Method to get the jquery core versions.
     * 
     * @access private
     * @return VOID
     */
    private function getCoreVersions()
    {
        $path = 'core_modules/jquerycore/resources/core/';
        if (is_dir($path))
        {
            if ($dh = opendir($path))
            {
                $this->coreVersions = array();
                while (($file = readdir($dh)) !== false)
                {
                    if (filetype($path.$file) == 'dir' && $file != '..' && $file != '.')
                    {
                        $key = str_replace('.', '', $file);
                        $this->coreVersions[$key] = $file;
                    }
                }                
                closedir($dh);
                ksort($this->coreVersions);
            }
        }
    }
    
    /**
     *
     * Method to return a customised input to the sysconfig form
     * 
     * @access public
     * @return string $string The html string to be displayed in sysconfig 
     */
    public function show()
    {
        $string = $this->objLanguage->languageText('mod_jquerycore_selectversion', 'jquerycore', 'ERROR: mod_jquerycore_selectversion');

        $objDrop = new dropdown('pvalue');
        foreach ($this->coreVersions as $version)
        {
            $objDrop->addOption($version, $version);
        }
        $objDrop->setSelected($this->defaultValue);

        $string .= '&nbsp;' . $objDrop->show();
        
        return $string;        
    }
    
    /**
     *
     * Method to execute after the param has been updated
     * 
     * @access public
     * @return VOID; 
     */
    public function postUpdateActions()
    {
        return;
    }
}
?>