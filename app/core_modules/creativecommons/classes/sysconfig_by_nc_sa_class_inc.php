<?php

/**
 * Sysconfig CC class
 * 
 * Sysconfig interface class for Creative Commons (BY-NC-SA)
 * 
 * PHP version 3
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
/**
* Class to provide SysConfig an input for enabling/disabling the BY/NC-SA license
* @author Tohir Solomons
*/
class sysconfig_by_nc_sa extends object
{
    /**
    * @var string $defaultValue Current Value of the Parameter
    */
    function init()
    {
        $this->objLanguage =& $this->getObject('language', 'language');
    }
    
    /**
    * Method to set the current default value
    */
    function setDefaultValue($value)
    {
        $this->defaultValue = $value;
    }
    
    /**
    * Method to return a customized input to the SysConfig form
    */
    function show()
    {
        // Load the Radio button class
        $this->loadClass('radio', 'htmlelements');
        
        // Input MUST be called 'pvalue'
        $objElement = new radio ('pvalue');
        
        $objElement->addOption('Y', $this->objLanguage->languageText('word_yes'));
        $objElement->addOption('N', $this->objLanguage->languageText('word_no'));
        
        // Set Default Selected
        $objElement->setSelected($this->defaultValue);
        
        // Set radio buttons to be one per line
        $objElement->setBreakSpace(' &nbsp; ');
        
        $string = '<p>'.$this->objLanguage->languageText('mod_creativecommons_enableby-nc-sa', 'creativecommons').'</p>';
        
        // return finished radio button
        return $string.$objElement->show();
    }
    
    /**
    * Method to run actions that need to occur once the parameter is updated
    */
    function postUpdateActions()
    {
        return NULL;
    }
    
    
}

?>