<?php
/**
 * converts distance/length measurements: centimeters, millimeters, feet,
 * yards, meters, kilometres and miles
 *
 * @author     Hendry Thobela <2649282@uwc.ac.za>
 * @author     Raymond Williams <2541826@uwc.ac.za>
 * @package    conversions
 * @copyright  UWC 2007
 * @filesource
 */
class dist extends object
{
    /**
     * Constructor method to instantiate objects and get variables
     *
     * @return void
     * @access public
     */
    public function init() 
    {
        $this->objLanguage = $this->getObject('language', 'language');
    }
    /**
     * The following function converts Miles to Centimeters
     *
     * @param  numerical value ($value)
     * @return centimeter equivalent
     * @access public
     */
    public function convMilesToCentimeters($value = NULL) 
    {
        $answer = ($value*160934);
        return $answer;
    }
    /**
     * The following function converts Miles to Millilitres
     *
     * @param  numerical value ($value)
     * @return input value millilitre equivalent
     * @access public
     */
    public function convMilesToMillimeters($value = NULL) 
    {
        $answer = ($value*1609000);
        return $answer;
    }
    /**
     * The following function converts Miles to Feet
     *
     * @param  numerical value ($value)
     * @return input value feet equivalent
     * @access public
     */
    public function convMilesToFeet($value = NULL) 
    {
        $answer = ($value*5280);
        return $answer;
    }
    /**
     * The following function converts Miles to Yards
     *
     * @param  numerical value ($value)
     * @return input value yards equivalent
     * @access public
     */
    public function convMilesToYards($value = NULL) 
    {
        $answer = ($value*1760);
        return $answer;
    }
    /**
     *The following function converts Miles to Meters
     *
     * @param  numerical value ($value)
     * @return input value meters equivalent
     * @access public
     */
    public function convMilesToMeters($value = NULL) 
    {
        $answer = ($value*1609);
        return $answer;
    }
    /**
     * The following function converts Miles to Kilometers
     *
     * @param  numerical value ($value)
     * @return input value kilometers equivalent
     * @access public
     */
    public function convMilesToKilometers($value = NULL) 
    {
        $answer = ($value*1.60900);
        return $answer;
    }
    /**
     * The following function converts Centimeters to Miles
     *
     * @param  numerical value ($value)
     * @return input value miles equivalent
     * @access public
     */
    public function convCentimetersToMiles($value = NULL) 
    {
        $answer = ($value*0.000006214);
        return $answer;
    }
    /**
     * The following function converts Millimeters to Miles
     *
     * @param  numerical value ($value)
     * @return input value miles equivalent
     * @access public
     */
    public function convMillimetersToMiles($value = NULL) 
    {
        $answer = ($value*6.214e-7);
        return $answer;
    }
    /**
     * The following function converts Yards to Miles
     *
     * @param  numerical value ($value)
     * @return input value miles equivalent
     * @access public
     */
    public function convYardsToMiles($value = NULL) 
    {
        $answer = ($value*0.00056);
        return $answer;
    }
    /**
     * The following function converts Feet to Miles
     *
     * @param  numerical value ($value)
     * @return input value miles equivalent
     * @access public
     */
    public function convFeetToMiles($value = NULL) 
    {
        $answer = ($value*0.00019);
        return $answer;
    }
    /**
     * The following function converts Meters To Miles
     *
     * @param  numerical value ($value)
     * @return input value miles equivalent
     * @access public
     */
    public function convMetersToMiles($value = NULL) 
    {
        $answer = ($value*0.00062);
        return $answer;
    }
    /**
     * The following function converts Kilometers to Miles
     *
     * @param  numerical value ($value)
     * @return input value miles equivalent
     * @access public
     */
    public function convKilometersToMiles($value = NULL) 
    {
        $answer = ($value*0.62140);
        return $answer;
    }
    /**
     * The following function below does the actual conversion
     *
     * @param  numerical value $value
     * @param  string $from  Unit to be converted from
     * @param  string $to    Unit to be converted to
     * @return converted value
     * @access public
     */
    public function doConversion($value = NULL, $from = NULL, $to = NULL) 
    {
        /**
         * 1 = Centimeters
         * 2 = Millimeters
         * 3 = Feet
         * 4 = Yards
         * 5 = Meters
         * 6 = Kilometers
         * 7 = Miles
         *
         * The variable $tempVal is used in cases where there is no direct conversion from one value to another
         *
         */
        // Check to see if $value is a numerical vaulue
        if (!is_numeric($value)) {
            return $this->objLanguage->languageText('mod_conversions_insertNumError', 'conversions');
        }
        // Checks to see if $from and $to are equal
        elseif ($from == $to && !empty($value)) {
            return $this->objLanguage->languageText('mod_conversions_itselfError', 'conversions');
        }
        //Does the conversion from Centimeters to Millimeters and returns the answer
        elseif ($from == "1" && $to == "2") {
            $tempVal = $this->convCentimetersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToMillimeters($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symMM", "conversions") . ".";
        }
        //Does the conversion from Centimeters to Feet and returns the answer
        elseif ($from == "1" && $to == "3") {
            $tempVal = $this->convCentimetersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToFeet($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symFT", "conversions") . ".";
        }
        //Does the conversion from Centimeters to Yards and returns the answer
        elseif ($from == "1" && $to == "4") {
            $tempVal = $this->convCentimetersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToYards($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symYD", "conversions") . ".";
        }
        //Does the conversion from Centimeters to Meters and returns the answer
        elseif ($from == "1" && $to == "5") {
            $tempVal = $this->convCentimetersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToMeters($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . ".";
        }
        //Does the conversion from Centimeters to Kilometers and returns the answer
        elseif ($from == "1" && $to == "6") {
            $tempVal = $this->convCentimetersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToKilometers($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symKM", "conversions") . ".";
        }
        //Does the conversion from Centimeters to Miles and returns the answer
        elseif ($from == "1" && $to == "7") {
            return $value . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convCentimetersToMiles($value)) , 2) . " " . $this->objLanguage->languageText("mod_conversions_Miles", "conversions") . ".";
        }
        //Does the conversion from Millimeters to Centimeters and returns the answer
        elseif ($from == "2" && $to == "1") {
            $tempVal = $this->convMillimetersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symMM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToCentimeters($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . ".";
        }
        //Does the conversion from Millimeters to Feet and returns the answer
        elseif ($from == "2" && $to == "3") {
            $tempVal = $this->convMillimetersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symMM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToFeet($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symFT", "conversions") . ".";
        }
        //Does the conversion from Millimeters to Yards and returns the answer
        elseif ($from == "2" && $to == "4") {
            $tempVal = $this->convMillimetersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symMM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToYards($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symYD", "conversions") . ".";
        }
        //Does the conversion from Millimeters to Meters and returns the answer
        elseif ($from == "2" && $to == "5") {
            $tempVal = $this->convMillimetersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symMM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToMeters($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . ".";
        }
        //Does the conversion from Millimeters to Kilometers and returns the answer
        elseif ($from == "2" && $to == "6") {
            $tempVal = $this->convMillimetersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symMM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToKilometers($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symKM", "conversions") . ".";
        }
        //Does the conversion from Millimeters to Miles and returns the answer
        elseif ($from == "2" && $to == "7") {
            return $value . $this->objLanguage->languageText("mod_conversions_symMM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMillimetersToMiles($value)) , 2) . " " . $this->objLanguage->languageText("mod_conversions_Miles", "conversions") . ".";
        }
        //Does the conversion from Feet to Centimeters and returns the answer
        elseif ($from == "3" && $to == "1") {
            $tempVal = $this->convFeetToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symFT", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToCentimeters($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . ".";
        }
        //Does the conversion from Feet to Millimeters and returns the answer
        elseif ($from == "3" && $to == "2") {
            $tempVal = $this->convFeetToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symFT", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToMillimeters($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symMM", "conversions") . ".";
        }
        //Does the conversion from Feet to Yards and returns the answer
        elseif ($from == "3" && $to == "4") {
            $tempVal = $this->convFeetToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symFT", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToYards($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symYD", "conversions") . ".";
        }
        //Does the conversion from Feet to Meters and returns the answer
        elseif ($from == "3" && $to == "5") {
            $tempVal = $this->convFeetToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symFT", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToMeters($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . ".";
        }
        //Does the conversion from Feet to Kilometers and returns the answer
        elseif ($from == "3" && $to == "6") {
            $tempVal = $this->convFeetToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symFT", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToKilometers($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symKM", "conversions") . ".";
        }
        //Does the conversion from Feet to Miles and returns the answer
        elseif ($from == "3" && $to == "7") {
            return $value . $this->objLanguage->languageText("mod_conversions_symFT", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convFeetToMiles($value)) , 2) . " " . $this->objLanguage->languageText("mod_conversions_Miles", "conversions") . ".";
        }
        //Does the conversion from Yards to Centimeters and returns the answer
        elseif ($from == "4" && $to == "1") {
            $tempVal = $this->convYardsToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symYD", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToCentimeters($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . ".";
        }
        //Does the conversion from Yards to Millimeters and returns the answer
        elseif ($from == "4" && $to == "2") {
            $tempVal = $this->convYardsToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symYD", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToMillimeters($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symMM", "conversions") . ".";
        }
        //Does the conversion from Yards to Feet and returns the answer
        elseif ($from == "4" && $to == "3") {
            $tempVal = $this->convYardsToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symYD", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToFeet($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symFT", "conversions") . ".";
        }
        //Does the conversion from Yards to Meters and returns the answer
        elseif ($from == "4" && $to == "5") {
            $tempVal = $this->convYardsToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symYD", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToMeters($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . ".";
        }
        //Does the conversion from Yards to Kilometers and returns the answer
        elseif ($from == "4" && $to == "6") {
            $tempVal = $this->convYardsToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symYD", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToKilometers($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symKM", "conversions") . ".";
        }
        //Does the conversion from Yards to Miles and returns the answer
        elseif ($from == "4" && $to == "7") {
            return $value . $this->objLanguage->languageText("mod_conversions_symYD", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convYardsToMiles($value)) , 2) . " " . $this->objLanguage->languageText("mod_conversions_Miles", "conversions") . ".";
        }
        //Does the conversion from Meters to Centimeters and returns the answer
        elseif ($from == "5" && $to == "1") {
            $tempVal = $this->convMetersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToCentimeters($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . ".";
        }
        //Does the conversion from Meters to Millimeters and returns the answer
        elseif ($from == "5" && $to == "2") {
            $tempVal = $this->convMetersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToMillimeters($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symMM", "conversions") . ".";
        }
        //Does the conversion from Meters to Feet and returns the answer
        elseif ($from == "5" && $to == "3") {
            $tempVal = $this->convMetersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToFeet($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symFT", "conversions") . ".";
        }
        //Does the conversion from Meters to Yards and returns the answer
        elseif ($from == "5" && $to == "4") {
            $tempVal = $this->convMetersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToYards($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symYD", "conversions") . ".";
        }
        //Does the conversion from Meters to Kilometers and returns the answer
        elseif ($from == "5" && $to == "6") {
            $tempVal = $this->convMetersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToKilometers($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symKM", "conversions") . ".";
        }
        //Does the conversion from Meters to Miles and returns the answer
        elseif ($from == "5" && $to == "7") {
            return $value . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMetersToMiles($value)) , 2) . " " . $this->objLanguage->languageText("mod_conversions_Miles", "conversions") . ".";
        }
        //Does the conversion from Kilometers to Centimeters and returns the answer
        elseif ($from == "6" && $to == "1") {
            $tempVal = $this->convKilometersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symKM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToCentimeters($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . ".";
        }
        //Does the conversion from Kilometers to Millimeters and returns the answer
        elseif ($from == "6" && $to == "2") {
            $tempVal = $this->convKilometersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symKM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToMillimeters($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symMM", "conversions") . ".";
        }
        //Does the conversion from Kilometers to Feet and returns the answer
        elseif ($from == "6" && $to == "3") {
            $tempVal = $this->convKilometersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symKM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToFeet($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symFT", "conversions") . ".";
        }
        //Does the conversion from Kilometers to Yards and returns the answer
        elseif ($from == "6" && $to == "4") {
            $tempVal = $this->convKilometersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symKM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToYards($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symYD", "conversions") . ".";
        }
        //Does the conversion from Kilometers to Meters and returns the answer
        elseif ($from == "6" && $to == "5") {
            $tempVal = $this->convKilometersToMiles($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symKM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToMeters($tempVal)) , 2) . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . ".";
        }
        //Does the conversion from Kilometers to Miles and returns the answer
        elseif ($from == "6" && $to == "7") {
            return $value . $this->objLanguage->languageText("mod_conversions_symKM", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convKilometersToMiles($value)) , 2) . " " . $this->objLanguage->languageText("mod_conversions_Miles", "conversions") . ".";
        }
        //Does the conversion from Miles to Centimeters and returns the answer
        elseif ($from == "7" && $to == "1") {
            return $value . " " . $this->objLanguage->languageText("mod_conversions_Miles", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToCentimeters($value)) , 2) . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . ".";
        }
        //Does the conversion from Miles to Millimeters and returns the answer
        elseif ($from == "7" && $to == "2") {
            return $value . " " . $this->objLanguage->languageText("mod_conversions_Miles", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToMillimeters($value)) , 2) . $this->objLanguage->languageText("mod_conversions_symMM", "conversions") . ".";
        }
        //Does the conversion from Miles to Feet and returns the answer
        elseif ($from == "7" && $to == "3") {
            return $value . " " . $this->objLanguage->languageText("mod_conversions_Miles", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToFeet($value)) , 2) . $this->objLanguage->languageText("mod_conversions_symFT", "conversions") . ".";
        }
        //Does the conversion from Miles to Yards and returns the answer
        elseif ($from == "7" && $to == "4") {
            return $value . " " . $this->objLanguage->languageText("mod_conversions_Miles", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToYards($value)) , 2) . $this->objLanguage->languageText("mod_conversions_symYD", "conversions") . ".";
        }
        //Does the conversion from Miles to Meters and returns the answer
        elseif ($from == "7" && $to == "5") {
            return $value . " " . $this->objLanguage->languageText("mod_conversions_Miles", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToMeters($value)) , 2) . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . ".";
        }
        //Does the conversion from Miles to Kilometers and returns the answer
        elseif ($from == "7" && $to == "6") {
            return $value . " " . $this->objLanguage->languageText("mod_conversions_Miles", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convMilesToKilometers($value)) , 2) . $this->objLanguage->languageText("mod_conversions_symKM", "conversions") . ".";
        }
        //Checks to see if $value is NULL
        else {
            return $this->objLanguage->languageText('mod_conversions_insertError', 'conversions');
        }
    }
}
?>
