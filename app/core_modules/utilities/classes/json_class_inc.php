<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Chisimba's JSON wrapper functions
 *
 * @category  Chisimba
 * @package   utilities
 * @author    Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright GNU/GPL AVOIR/UWC 2008
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class json extends object
{
    /**
     * @var object $objZend The Chisimba Zend object
     * @access protected
     */
    protected $objZend;

    /**
     * Constructor
     */
    public function init()
    {
        $this->objZend = $this->getObject('zend', 'utilities');
    }

    /**
     * Encode a PHP array to a JSON string
     * @param $val array The PHP array to be encoded
     * @return string The encoded JSON string
     * @access public
     */
    public function encode($val)
    {
        if (function_exists('json_encode')) {
            return json_encode($val);
        } else {
            return Zend_Json::encode($val);
        }
    }

    /**
     * Decode a JSON string to a PHP array
     * @param $val string The JSON string to be decoded
     * @return array The decoded PHP array
     * @access public
     */
    public function decode($val)
    {
        if (function_exists('json_decode')) {
            return json_decode($val);
        } else {
            return Zend_Json::decode($val);
        }
    }
}
