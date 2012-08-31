<?php

/**
 * Toolbar Type sysconfig
 * 
 * Class to generate a toolbar selector for sysconfig
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
 * @package   toolbar
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: sysconfig_kewl_default_skin_class_inc.php 2792 2007-08-02 09:59:31Z paulscott $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */


/**
 * Toolbar Type sysconfig
 * 
 * Class to generate a toolbar selector for sysconfig
 * 
 * @category  Chisimba
 * @package   toolbar
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class sysconfig_toolbar_type extends object
{

    /**
    * Standard Constructor
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
    * Method to set the current default value
    */
    public function setDefaultValue($value)
    {
        $this->defaultValue = strtolower($value);
    }
    
    /**
    * Method to display the sysconfig interface
    */
    public function show()
    {
        // Load the Radio button class
        $this->loadClass('radio', 'htmlelements');
        
        
        // Input MUST be called 'pvalue'
        $objElement = new radio ('pvalue');
        

        $objElement->addOption('dropdown', $this->objLanguage->languageText('mod_toolbar_dynamicdropdown', 'toolbar', 'Dynamic Dropdown'));
        $objElement->addOption('flat', $this->objLanguage->languageText('mod_toolbar_flatbuttons', 'toolbar', 'Flat Buttons'));
        $objElement->addOption('elearning', $this->objLanguage->languageText('mod_toolbar_elearningtoolbar', 'toolbar', 'Elearning Toolbar'));
        
        // Set Default Selected
        $objElement->setSelected($this->defaultValue);
        
        // Set radio buttons to be one per line
        $objElement->setBreakSpace('<br />');
        
        // return finished radio button
        return $objElement->show();
    }
    
    /**
    * Method to run actions that need to occur once the parameter is updated
    */
    public function postUpdateActions()
    {
        return NULL;
    }
    
    
}

?>