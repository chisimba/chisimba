<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Short description for class
 * 
 * Long description (if any) ...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
class Validator extends controller {

    /**
     * Description for public
     * @var    array 
     * @access public
     */
    public $errors; // A variable to store a list of error messages

    /**
    /* Validate something's been entered
    /* NOTE: Only this method does nothing to prevent SQL injection
    /* use with addslashes() command
    */
    public function validateGeneral($theinput,$description = ''){
        if (trim($theinput) != "") {
            return true;
        }else{
            $this->errors[] = $description;
            return false;
        }
    }

    /**
    * Validate text only
    */
    public function validateTextOnly($theinput,$description = ''){
        $result = ereg ("^[A-Za-z\ ]+$", $theinput );
        if ($result){
            return true;
        }else{
            $this->errors[] = $description;
            return false; 
        }
    }

    /**
    * Validate text only, no spaces allowed
    */
    public function validateTextOnlyNoSpaces($theinput,$description = ''){
        $result = ereg ("^[A-Za-z0-9]+$", $theinput );
        if ($result){
            return true;
        }else{
            $this->errors[] = $description;
            return false; 
        }
    }

    /**
    * Validate email address
    */
    public function validateEmail($themail,$description = ''){
        $result = ereg ("^[^@ ]+@[^@ ]+\.[^@ \.]+$", $themail );
        if ($result){
            return true;
        }else{
            $this->errors[] = $description;
            return false; 
        }

    }

    /**
    * Validate numbers only
    */
    public function validateNumber($theinput,$description = ''){
        if (is_numeric($theinput)) {
            return true; // The value is numeric, return true
        }else{ 
            $this->errors[] = $description; // Value not numeric! Add error description to list of errors
            return false; // Return false
        }
    }

    /**
    * Validate month only
    */
    public function validateMonth($theinput,$description = ''){
        if (is_numeric($theinput) && $theinput <= '12' && $theinput >= '1') {
            return true; // The value is numeric, return true
        }else{ 
            $this->errors[] = $description; // Value not numeric! Add error description to list of errors
            return false; // Return false
        }
    }


    /**
    * Validate day only
    */
    public function validateDay($theinput,$description = ''){
        if (is_numeric($theinput) && $theinput <= '31' && $theinput >= '1') {
            return true; // The value is numeric, return true
        }else{ 
            $this->errors[] = $description; // Value not numeric! Add error description to list of errors
            return false; // Return false
        }
    }



    /**
    * Validate date
    */
    public function validateDate($thedate,$description = ''){

        if (strtotime($thedate) === -1 || $thedate == '') {
            $this->errors[] = $description;
            return false;
        }else{
            return true;
        }
    }

    /**
    * Check whether any errors have been found (i.e. validation has returned false)
    * since the object was created
    */
    public function foundErrors() {
        if (count($this->errors) > 0){
            return true;
        }else{
            return false;
        }
    }

    // Return a string containing a list of errors found,
    // Seperated by a given deliminator


    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  string $delim Parameter description (if any) ...
     * @return mixed  Return description (if any) ...
     * @access public
     */
    public function listErrors($delim = ' '){
        return implode($delim,$this->errors);
    }

    // Manually add something to the list of errors


    /**
     * Short description for function
     * 
     * Long description (if any) ...
     * 
     * @param  unknown $description Parameter description (if any) ...
     * @return void   
     * @access public 
     */
    public function addError($description){
        $this->errors[] = $description;
    }	

}//end class
?>