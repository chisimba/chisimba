<?php

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

class Validator extends controller {

    var $errors; // A variable to store a list of error messages

    /**
    /* Validate something's been entered
    /* NOTE: Only this method does nothing to prevent SQL injection
    /* use with addslashes() command
    */
    function validateGeneral($theinput,$description = ''){
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
    function validateTextOnly($theinput,$description = ''){
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
    function validateTextOnlyNoSpaces($theinput,$description = ''){
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
    function validateEmail($themail,$description = ''){
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
    function validateNumber($theinput,$description = ''){
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
    function validateMonth($theinput,$description = ''){
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
    function validateDay($theinput,$description = ''){
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
    function validateDate($thedate,$description = ''){

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
    function foundErrors() {
        if (count($this->errors) > 0){
            return true;
        }else{
            return false;
        }
    }

    // Return a string containing a list of errors found,
    // Seperated by a given deliminator
    function listErrors($delim = ' '){
        return implode($delim,$this->errors);
    }

    // Manually add something to the list of errors
    function addError($description){
        $this->errors[] = $description;
    }	

}//end class
?>
