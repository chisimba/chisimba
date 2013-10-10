<?php
/**
 * converts volume measurements: litres, millilitres, cubic decimeters, cubic meters & cubic centimeters
 *
 * @author     Nonhlanhla Gangeni <2539399@uwc.ac.za>
 * @package    conversions
 * @copyright  UWC 2007
 * @filesource
 */
class vol extends object
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
     * The following function converts Litres to Cubic Meters
     *
     * @param  numerical value ($value)
     * @return cubic meter equivalent
     * @access public
     */
    public function convLitresToCubicMeters($value = NULL) 
    {
        $answer = $value/10000;
        return $answer;
    }
    /**
     * The following function converts Millilitres to Cubic Meters
     *
     * @param  numerical value ($value)
     * @return cubic meter equivalent
     * @access public
     */
    public function convMillilitresToCubicMeters($value = NULL) 
    {
        $answer = $value/10000000;
        return $answer;
    }
    /**
     * The following function converts Cubic Meters to Litres
     *
     * @param  numerical value ($value)
     * @return litre equivalent
     * @access public
     */
    public function convCubicMetersToLitres($value = NULL) 
    {
        $answer = $value*10000;
        return $answer;
    }
    /**
     * The following function converts Cubic Meters to Millilitres
     *
     * @param  numerical value ($value)
     * @return millilitre equivalent
     * @access public
     */
    public function convCubicMetersToMillilitres($value = NULL) 
    {
        $answer = $value*10000000;
        return $answer;
    }
    /**
     * The following function converts Cubic Decimeters to Cubic Meters
     *
     * @param  numerical value ($value)
     * @return cubic meter equivalent
     * @access public
     */
    public function convCubicDecimetersToCubicMeters($value = NULL) 
    {
        $answer = $value/1000;
        return $answer;
    }
    /**
     * The following function converts Cubic Meters to Cubic Decimeters
     *
     * @param  numerical value ($value)
     * @return cubic decimeter equivalent
     * @access public
     */
    public function convCubicMetersToCubicDecimeters($value = NULL) 
    {
        $answer = $value*1000;
        return $answer;
    }
    /**
     * The following function converts Cubic Centimeters to Cubic Meters
     *
     * @param  numerical value ($value)
     * @return cubic meter equivalent
     * @access public
     */
    public function convCubicCentimetersToCubicMeters($value = NULL) 
    {
        $answer = $value/1000000;
        return $answer;
    }
    /**
     * The following function converts Cubic Meters to Cubic Centimeters
     *
     * @param  numerical value ($value)
     * @return cubic centimeter equivalent
     * @access public
     */
    public function convCubicMetersToCubicCentimeters($value = NULL) 
    {
        $answer = $value*1000000;
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
         * 1 = Litres
         * 2 = Millilitres
         * 3 = Cubic Decimeter
         * 4 = Cubic Meter
         * 5 = Cubic Centimeter
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
        //Does the conversion from Litres to Millilitres and returns the answer
        elseif ($from == "1" && $to == "2") {
            $tempVal = $this->convLitresToCubicMeters($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symL", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToMillilitres($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symML", "conversions") . ".";
        }
        //Does the conversion from Litres to Cubic Decimeter and returns the answer
        elseif ($from == "1" && $to == "3") {
            $tempVal = $this->convLitresToCubicMeters($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symL", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToCubicDecimeters($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symDM", "conversions") . "<sup>3</sup>" . ".";
        }
        //Does the conversion from Litres to Cubic Meters and returns the answer
        elseif ($from == "1" && $to == "4") {
            return $value . $this->objLanguage->languageText("mod_conversions_symL", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convLitresToCubicMeters($value) , 2) . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . "<sup>3</sup>" . ".";
        }
        //Does the conversion from Litres to Cubic Centimeters and returns the answer
        elseif ($from == "1" && $to == "5") {
            $tempVal = $this->convLitresToCubicMeters($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symL", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToCubicCentimeters($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . "<sup>3</sup>" . ".";
        }
        //Does the conversion from Millilitres to Litres and returns the answer
        elseif ($from == "2" && $to == "1") {
            $tempVal = $this->convMillilitresToCubicMeters($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symML", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToLitres($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symL", "conversions") . ".";
        }
        //Does the conversion from Millilitres to Cubic Decimeters and returns the answer
        elseif ($from == "2" && $to == "3") {
            $tempVal = $this->convMillilitresToCubicMeters($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symML", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToCubicDecimeters($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symDM", "conversions") . "<sup>3</sup>" . ".";
        }
        //Does the conversion from Millilitres to Cubic Meters and returns the answer
        elseif ($from == "2" && $to == "4") {
            return $value . $this->objLanguage->languageText("mod_conversions_symML", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMillilitresToCubicMeters($value) , 2) . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . "<sup>3</sup>" . ".";
        }
        //Does the conversion from Millilitres to Cubic Centimeters and returns the answer
        elseif ($from == "2" && $to == "5") {
            $tempVal = $this->convMillilitresToCubicMeters($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symML", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToCubicCentimeters($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . "<sup>3</sup>" . ".";
        }
        //Does the conversion from Cubic Decimeters to Cubic Meters and returns the answer
        elseif ($from == "3" && $to == "4") {
            return $value . $this->objLanguage->languageText("mod_conversions_symDM", "conversions") . "<sup>3</sup>" . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicDecimetersToCubicMeters($value) , 2) . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . "<sup>3</sup>" . ".";
        }
        //Does the conversion from Cubic Meters to Litres and returns the answer
        elseif ($from == "4" && $to == "1") {
            return $value . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . "<sup>3</sup>" . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToLitres($value) , 2) . $this->objLanguage->languageText("mod_conversions_symL", "conversions") . ".";
        }
        //Does the conversion from Cubic Meters to Millilitres and returns the answer
        elseif ($from == "4" && $to == "2") {
            return $value . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . "<sup>3</sup>" . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToMillilitres($value) , 2) . $this->objLanguage->languageText("mod_conversions_symML", "conversions") . ".";
        }
        //Does the conversion from Cubic Meters to Cubic Decimeters and returns the answer
        elseif ($from == "4" && $to == "3") {
            return $value . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . "<sup>3</sup>" . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToCubicDecimeters($value) , 2) . $this->objLanguage->languageText("mod_conversions_symDM", "conversions") . "<sup>3</sup>" . ".";
        }
        //Does the conversion from Cubic Centimeters to Cubic Meters and returns the answer
        elseif ($from == "5" && $to == "4") {
            return $value . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . "<sup>3</sup>" . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicCentimetersToCubicMeters($value) , 2) . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . "<sup>3</sup>" . ".";
        }
        //Does the conversion from Cubic Meters to Cubic Centimeters and returns the answer
        elseif ($from == "4" && $to == "5") {
            return $value . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . "<sup>3</sup>" . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToCubicCentimeters($value) , 2) . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . "<sup>3</sup>" . ".";
        }
        //Does the conversion from Cubic Centimeters to Cubic Decimeters and returns the answer
        elseif ($from == "5" && $to == "3") {
            $tempVal = $this->convCubicCentimetersToCubicMeters($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symM", "conversions") . "<sup>3</sup>" . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToCubicDecimeters($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symDM", "conversions") . "<sup>3</sup>" . ".";
        }
        //Does the conversion from Cubic Decimeters to Cubic Centimeters and returns the answer
        elseif ($from == "3" && $to == "5") {
            $tempVal = $this->convCubicDecimetersToCubicMeters($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symDM", "conversions") . "<sup>3</sup>" . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToCubicCentimeters($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . "<sup>3</sup>" . ".";
        }
        //Does the conversion from Cubic Decimeters to Litres and returns the answer
        elseif ($from == "3" && $to == "1") {
            $tempVal = $this->convCubicDecimetersToCubicMeters($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symDM", "conversions") . "<sup>3</sup>" . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToLitres($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symL", "conversions") . ".";
        }
        //Does the conversion from Cubic Decimeters to Millilitres and returns the answer
        elseif ($from == "3" && $to == "2") {
            $tempVal = $this->convCubicDecimetersToCubicMeters($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symDM", "conversions") . "<sup>3</sup>" . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToMillilitres($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symMM", "conversions") . ".";
        }
        //Does the conversion from Cubic Centimeters to Litres and returns the answer
        elseif ($from == "5" && $to == "1") {
            $tempVal = $this->convCubicCentimetersToCubicMeters($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . "<sup>3</sup>" . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToLitres($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symL", "conversions") . ".";
        }
        //Does the conversion from Cubic Centimeters to Millilitres and returns the answer
        elseif ($from == "5" && $to == "2") {
            $tempVal = $this->convCubicCentimetersToCubicMeters($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symCM", "conversions") . "<sup>3</sup>" . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convCubicMetersToMillilitres($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symML", "conversions") . ".";
        }
        //Checks to see if $value is NULL
        else {
            return $this->objLanguage->languageText('mod_conversions_insertError', 'conversions');
        }
    }
}
?>
