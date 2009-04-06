<?php

/**
 * Creative Commons Sysconfig Default Class.
 * 
 * Sysconfig interface class for the Creative Commons default license.
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
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2009 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */

/**
 * The sysconfig interface for selecting a default license.
 * @author Charl van Niekerk
 */
class sysconfig_default extends object
{
    /**
     * Instance of the dbcreativecommons class in the creativecommons module.
     *
     * @var object
     */
    protected $objCC;

    /**
     * Instance of the language class in the language module.
     *
     * @var object
     */
    protected $objLanguage;

    /**
     * Initialises the object.
     */
    function init()
    {
        $this->objCC = $this->getObject('dbcreativecommons', 'creativecommons');
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
     * Sets the current value of the parameter.
     *
     * @param string $value The current value.
     */
    function setDefaultValue($value)
    {
        $this->defaultValue = $value;
    }
    
    /**
     * Generate the (X)HTML to display the customised form.
     *
     * @return string The generated (X)HTML.
     */
    function show()
    {
        // Get the parameter description.
        $description = '<p>'.$this->objLanguage->languageText('mod_creativecommons_default', 'creativecommons').'</p>';

        // Load the radio button class form the htmlelements module.
        $this->loadClass('radio', 'htmlelements');

        // The name of the input field must be "pvalue".
        $objElement = new radio('pvalue');

        // Add the licenses as options.
        foreach ($this->objCC->getAll() as $license) {
            $objElement->addOption($license['code'], $license['title']);
        }

        // Have the current value selected by default.
        $objElement->setSelected($this->defaultValue);
        
        // Display the radio buttons on separate lines.
        $objElement->setBreakSpace('<br />');
        
        // Return the output.
        return $description.$objElement->show();
    }
}
