<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Simple class to handle server side validation.
 *
 * @category  Chisimba
 * @author Charl Mert
 * @package utilities
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @link      http://avoir.uwc.ac.za
 */

class validate extends object
{
    /**
	 * Constructor
     */
    function init()
    {
    }

    /**
     * Method to validate a required parameter.
     * @param string $paramName
     * @param string $errMessage
     * @return TRUE if valid FALSE if not.
    */
    function valRequired($paramName)
    {
		if (isset($paramName) && $paramName != '' && $paramName != NULL) {
	        return TRUE;
		} else {
			return FALSE;
		} 
    }

}
?>
