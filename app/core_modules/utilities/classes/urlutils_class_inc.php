<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Miscellaneous functions related to the current URL
 * of the browser
 *
 * @category  Chisimba
 * @package   utilities
 * @author    Derek Keats<derek@dkeats.com>
 * @copyright 2012 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://www.chisimba.com
 */
class urlutils extends object 
{

    /**
     * Standard init method
     */
    public function init()
    {

    }
    
    /**
     *
     * Get the current page URL. Note that this makes some
     * assumptions about the way Chisimba servers are
     * configured.
     * 
     * @return string The current page URL
     */
    function curPageURL()
    {
        $pageURL = 'http';
        if (!empty($_SERVER['HTTPS']) 
          && $_SERVER['HTTPS'] !== 'off' 
          || $_SERVER['SERVER_PORT'] == 443) {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"].":"
              . $_SERVER["SERVER_PORT"] 
              . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] 
            . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
    
    /**
     * 
     * Get the whole query string
     * 
     * @return string The full Querystring
     * @access public
     * 
     */
    public function getQueryString()
    {
        //return $_SERVER['QUERY_STRING'];
        die($_SERVER['QUERY_STRING']);
        
    }
}
?>