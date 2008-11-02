<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Initialises Zend Framework libraries for easy use in Chisimba
 *
 * @category  Chisimba
 * @package   utilities
 * @author    Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright GNU/GPL AVOIR/UWC 2008
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class zend extends object
{
    /**
     * Constructor
     */
    public function init()
    {
        // Zend must be in the include path in order for its classes to load each other
        $includePath = get_include_path();
        $zendPath = $this->getResourcePath('zend');
        if (strpos($includePath, $zendPath) === FALSE) {
            $includePath .= ":$zendPath";
            set_include_path($includePath);
        }

        // Register the Zend autoloader for easy access to its libraries
        require_once 'Zend/Loader.php';
        Zend_Loader::registerAutoload();
    }
}

// The Zend Exception class is already used elsewhere in Chisimba
// This is a temporary fix until the other copies have been migrated
if (!class_exists('Zend_Exception')) {
    class Zend_Exception extends Exception {}
}
