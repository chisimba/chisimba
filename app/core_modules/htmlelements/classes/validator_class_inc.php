<?php
/**
 * Validator class
 * 
 * Form validation class for NextGen/Chisimba - outputs JavaScript for client-side data-integrity tests.
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
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
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
 * Validator class
 * 
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2004-2007, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
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

    /*
     * Return a string containing a list of errors found,
     * Seperated by a given deliminator...
     * 
     * @param  string $delim 
     * @return mixed  Return 
     * @access public
     */
    public function listErrors($delim = ' '){
        return implode($delim,$this->errors);
    }

    


    /*
     * Manually add something to the list of errors
     * 
     * @param  unknown $description 
     * @return void   
     * @access public 
     */
    public function addError($description){
        $this->errors[] = $description;
    }	

}//end class
?>
