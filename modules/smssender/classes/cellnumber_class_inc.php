<?php

/**
* Class to Repair and Validate Cellphone numbers
*
* @author Tohir Solomons
*/
class cellnumber extends object
{
    /**
    * Class constructor
    * @access public
    */
    public function init ()
    {
    }
    
    /**
    * This function fixes a cell number by removing empty spaces,
    * and adding the country prefix
    *
    * @param string $number Cell Phone Number
    * @param string $countryNumber International Dialling Code - at the moment, defaults to South Africa (27) but should be made configurable
    *
    * @return Cleaned Up Number
    */
    public function fixnumber($number, $countryNumber=27)
    {
        $number = trim ($number); // Remove Empty Spaces
        $number = str_replace(' ', '', $number);
        
        // Check if Ten Digit Number = 0834797241
        if (strlen($number) == 10) {
            if (substr($number, 0, 1) == '0') {
                $number = substr($number, 1);
            }
            
            $number = $countryNumber.$number;
        }
        return $number;
    }
}
?>