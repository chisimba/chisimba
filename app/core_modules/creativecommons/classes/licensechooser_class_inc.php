<?php

/**
 * License Chooser
 * 
 * Class to display the License Chooser in a Chisimba Module
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
 * @package   creativecommons
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to Generate a Radio Button List of available licenses
 * @author Tohir Solomons
 */
class licensechooser extends object
{
    /**
     * Name of the Form Input
     *
     * @var string
     */
    public $inputName = 'creativecommons';
    
    /**
     * Name of the Default Value
     *
     * @var string
     */
    public $defaultValue;
    
    /**
     * Size of Icons to be Use, either big (32x32) or small (20x20)
     *
     * @var string
     */
    public $icontype = 'big'; // or small
    
    /**
     * Constructor
     */
    public function init()
    {
        // Load the Creative Commons Object
        $this->objCC = $this->getObject('dbcreativecommons');
        
        // Load the Sysconfig Object
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        
        // Load the Radio Button Class
        $this->loadClass('radio', 'htmlelements');
        
        // Load the GetIcon Object
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
    }
    
    /**
     * Method to display the list
     *
     * @return string Rendered Input
     */
    public function show()
    {
        $objModules = $this->getObject('modules', 'modulecatalogue');
        
        if (!$objModules->checkIfRegistered('creativecommons')) {
            return '';
        } else {
        
            // Get All Licenses
            $licenses = $this->objCC->getAll();
            
            // Create Radio Button
            $radio = new radio($this->inputName);
            
            // Set Breakspace
            $radio->setBreakSpace('<br />');
            
            $iconsFolder = 'icons/creativecommons_v3';
            
            // Generate Blank Icon
            $this->objIcon->setIcon ('blank', NULL, $iconsFolder);
            $blankIcon = $this->objIcon->show();
            
            // Loop through Licenses
            foreach ($licenses as $license)
            {
                // Check if License is Enabled
                if ($this->objSysConfig->getValue($license['code'], 'creativecommons') == 'Y') {
                    
                    if ($this->icontype == 'big') {
                        $filename = $license['code'].'_big';
                    } else {
                        $filename = $license['code'];
                    }
                    
                    $filename = str_replace('/', '_', $filename);
                    
                    $this->objIcon->setIcon ($filename, NULL, $iconsFolder);
                    $iconList = $this->objIcon->show();
                    
                    $title = $license['title'];
                    if ($title == 'Attribution Non-commercial Share') {
                        $title = 'Attribution Non-commercial Share Alike';
                    }
                    // Add to Radio Group
                    $radio->addOption($license['code'], $iconList.' '.$title);
                }
            }
            
            // Set Default Selected Value
            $radio->setSelected($this->defaultValue);
            
            // Return Radio Button
            return $radio->show();
        }
    }

/**
     * Method to set the icon size
     *
     * @return string Rendered Input
     */
    public function setIconSize($size = 'big')
    {
        $this->icontype = $size;
    }

}

?>