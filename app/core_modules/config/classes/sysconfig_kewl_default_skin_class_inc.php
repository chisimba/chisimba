<?php

/**
 * Skin sysconfig
 *
 * Skin system configuration
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
 * @package   config
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */

/**
 * sysconfig for skins
 *
 * Chisimba skin system configuration manipulation class
 *
 * @category  Chisimba
 * @package   config
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class sysconfig_kewl_default_skin extends object {

    /**
     * Standard Constructor
     */
    public function init() {

    }

    /**
     * Method to set the current default value
     *
     */
    public function setDefaultValue($value) {
        $this->defaultVaule = $value;
    }

    /**
     * Method to display the sysconfig interface
     *
     */
    public function show() {
        // Load the Radio button class
        $this->loadClass ( 'radio', 'htmlelements' );

        // Load the Skin Object
        $objSkin = $this->getObject ( 'skin', 'skin' );

        $skinsList = $objSkin->getListofSkins ();

        // Input MUST be called 'pvalue'
        $objElement = new radio ( 'pvalue' );

        foreach ( $skinsList as $element => $value ) {
            $objElement->addOption ( $element, $value );
        }

        // Set Default Selected
        $objElement->setSelected ( $this->defaultVaule );

        // Set radio buttons to be one per line
        $objElement->setBreakSpace ( '<br />' );

        // return finished radio button
        return $objElement->show ();
    }

    /**
     * Method to run actions that need to occur once the parameter is updated
     *
     */
    public function postUpdateActions() {
        return NULL;
    }
}

?>