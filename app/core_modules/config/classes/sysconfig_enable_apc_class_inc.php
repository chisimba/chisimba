<?php

/**
 * Enable APC configuration
 *
 * This class provides a custom sysconfig input to modify the ENABLE_APC sysconfig parameter
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
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */

/**
 * Enable APC configuration
 *
 * This class provides a custom sysconfig input to modify the ENABLE_APC sysconfig parameter
 *
 * @category  Chisimba
 * @package   config
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2008 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class sysconfig_enable_apc extends object {

    /**
     * Standard Constructor
     *
     */
    public function init() {
        $this->objLanguage = $this->getObject ( 'language', 'language' );
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

        // Input MUST be called 'pvalue'
        $objElement = new radio ( 'pvalue' );

        $objElement->addOption ( 'TRUE', $this->objLanguage->languageText ( 'word_true', 'system', 'True' ) );
        $objElement->addOption ( 'FALSE', $this->objLanguage->languageText ( 'word_false', 'system', 'False' ) );

        // Set Default Selected
        $objElement->setSelected ( $this->defaultVaule );

        // Set radio buttons to be inline
        $objElement->setBreakSpace ( ' &nbsp;' );

        $str = '<p>' . $this->objLanguage->languageText ( 'mod_config_enableapcinfo', 'config', 'This parameter enables APC caching to improve the site speed. However it requires APC to be installed by the server owner - pecl install apc' ) . '</p>';

        // return finished radio button
        return $str . $objElement->show ();
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