<?php
/**
 * converts temperature measurements: kelvin, celcius, and fahrenheit
 *
 * @author     Nazheera Khan <2524939@uwc.ac.za>
 * @author     Ebrahim Vasta <2623441@uwc.ac.za>
 * @package    conversions
 * @copyright  UWC 2007
 * @filesource
 */
class temp extends object
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
     * The following function converts Celsius to Fahrenheit
     *
     * @param  numerical value ($value)
     * @return fahrenheit equivalent
     * @access public
     */
    public function convCelsToFahren($value = NULL) 
    {
        $answer = ((9/5) *($value) +32);
        return $answer;
    }
    /**
     * The following function converts Celsius to Kelvin
     *
     * @param  numerical value ($value)
     * @return kelvin equivalent
     * @access public
     */
    public function convCelsToKel($value = NULL) 
    {
        $answer = $value+273.15;
        return $answer;
    }
    /**
     * The following function converts Fahrenheit to Celsius
     *
     * @param  numerical value ($value)
     * @return celsius equivalent
     * @access public
     */
    public function convFahrenToCels($value = NULL) 
    {
        $answer = (5/9) *($value-32);
        return $answer;
    }
    /**
     * The following function converts Kelvin to Celsius
     *
     * @param  numerical value ($value)
     * @return celsius equivalent
     * @access public
     */
    public function convKelToCels($value = NULL) 
    {
        $answer = $value-273.15;
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
         * 1 = Celsius
         * 2 = Fahrenheit
         * 3 = Kelvin
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
        //Does the conversion from Celsius to Fahrenheit and returns the answer
        elseif ($from == "1" && $to == "2") {
            return $value . " " . $this->objLanguage->languageText("mod_conversions_Celsius", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convCelsToFahren($value)) , 2) . " " . $this->objLanguage->languageText("mod_conversions_Fahrenheit", "conversions");
        }
        //Does the conversion from Fahrenheit to Celsius and returns the answer
        elseif ($from == "2" && $to == "1") {
            return $value . " " . $this->objLanguage->languageText("mod_conversions_Fahrenheit", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convFahrenToCels($value)) , 2) . " " . $this->objLanguage->languageText("mod_conversions_Celsius", "conversions") . ".";
        }
        //Does the conversion from Fahrenheit to Kelvin and returns the answer
        elseif ($from == "2" && $to == "3") {
            $tempVal = $this->convFahrenToCels($value);
            return $value . " " . $this->objLanguage->languageText("mod_conversions_Fahrenheit", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convCelToKel($tempVal)) , 2) . " " . $this->objLanguage->languageText("mod_conversions_Kelvin", "conversions") . ".";
        }
        //Does the conversion from Kelvin to Fahrenheit and returns the answer
        elseif ($from == "3" && $to == "2") {
            $tempVal = $this->convKelToCels($value);
            return $value . " " . $this->objLanguage->languageText("mod_conversions_Kelvin", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convCelsToFahren($tempVal)) , 2) . " " . $this->objLanguage->languageText("mod_conversions_Fahrenheit", "conversions") . ".";
        }
        //Does the conversion from Celsius to Kelvin and returns the answer
        elseif ($from == "1" && $to == "3") {
            return $value . " " . $this->objLanguage->languageText("mod_conversions_Celsius", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convCelsToKel($value)) , 2) . " " . $this->objLanguage->languageText("mod_conversions_Kelvin", "conversions") . ".";
        }
        //Does the conversion from Kelvin to Celsius and returns the answer
        elseif ($from == "3" && $to == "1") {
            return $value . " " . $this->objLanguage->languageText("mod_conversions_Kelvin", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round(($this->convKelToCels($value)) , 2) . " " . $this->objLanguage->languageText("mod_conversions_Celsius", "conversions") . ".";
        }
        //Checks to see if $value is NULL
        else {
            return $this->objLanguage->languageText('mod_conversions_insertError', 'conversions');
        }
    }
}
?>
