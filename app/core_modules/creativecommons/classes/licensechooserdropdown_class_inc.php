<?php

/**
 * License chooser dropdown
 * 
 * Class to display the CC license chooser as a dropdown menu
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
 * Class to Generate a Drop down List of available licenses
 * @author Tohir Solomons
 */
class licensechooserdropdown extends object
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
     * Constructor
     */
    public function init()
    {
        // Load the Creative Commons Object
        $this->objCC = $this->getObject('dbcreativecommons');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        
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
            
            $options = '';
            
            // Loop through Licenses
            foreach ($licenses as $license)
            {
                
                if ($this->objSysConfig->getValue($license['code'], 'creativecommons') == 'Y') {
                    
                    $title = $license['title'];
                    if ($title == 'Attribution Non-commercial Share') {
                        $title = 'Attribution Non-commercial Share Alike';
                    }
                    
                    $selected = ($license['code'] == $this->defaultValue) ? ' selected="selected"' : '';
                    
                    // Add to Radio Group
                    $options .= '<option value="'.$license['code'].'" class="'.$license['code'].'" '.$selected.'>'.$title.'</option>';
                }
            }
            
            $select = '<select name="'.$this->inputName.'" id="input_'.$this->inputName.'">';
            
            $select .= $options;
            
            $select .= '</select>';
            
            
            // Return Radio Button
            return $select;
        }
    }
}

?>