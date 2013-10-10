<?php
/**
 * converts weight measurements: kilograms, grams, metric tons, pounds and ounces
 *
 * @author     Faizel Lodewyk <2528194@uwc.ac.za>
 * @author     Keanon Wagner <2456923@uwc.ac.za>
 * @package    conversions
 * @copyright  UWC 2007
 * @filesource
 */
class weight extends object
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
     * The following function converts Kilograms to Metric Tons
     *
     * @param  numerical value ($value)
     * @return metric tons equivalent
     * @access public
     */
    public function convKilogramsToMetrictons($value = NULL) 
    {
        $answer = ($value*0.001);
        return $answer;
    }
    /**
     * The following function converts Metric Tons to Kilograms
     *
     * @param  numerical value ($value)
     * @return kilogams equivalent
     * @access public
     */
    public function convMetrictonsToKilograms($value = NULL) 
    {
        $answer = ($value*1000);
        return $answer;
    }
    /**
     * The following function converts Grams to Metric Tons
     *
     * @param  numerical value ($value)
     * @return cubic metric tons equivalent
     * @access public
     */
    public function convGramsToMetrictons($value = NULL) 
    {
        $answer = ($value*0.00001);
        return $answer;
    }
    /**
     * The following function converts Metric Tons to Grams
     *
     * @param  numerical value ($value)
     * @return cubic grams equivalent
     * @access public
     */
    public function convMetrictonsToGrams($value = NULL) 
    {
        $answer = ($value*1000000);
        return $answer;
    }
    /**
     * The following function converts Pounds to Metric Tons
     *
     * @param  numerical value ($value)
     * @return metric tons equivalent
     * @access public
     */
    public function convPoundsToMetrictons($value = NULL) 
    {
        $answer = ($value*0.000454);
        return $answer;
    }
    /**
     * The following function converts Metric Tons to Pounds
     *
     * @param  numerical value ($value)
     * @return pounds equivalent
     * @access public
     */
    public function convMetrictonsToPounds($value = NULL) 
    {
        $answer = ($value*2204.6);
        return $answer;
    }
    /**
     * The following function converts Ounces to Metric Tons
     *
     * @param  numerical value ($value)
     * @return metric tons equivalent
     * @access public
     */
    public function convOuncesToMetrictons($value = NULL) 
    {
        $answer = ($value*0.00045);
        return $answer;
    }
    /**
     * The following function converts Metric Tons to Ounces
     *
     * @param  numerical value ($value)
     * @return ounces equivalent
     * @access public
     */
    public function convMetrictonsToOunces($value = NULL) 
    {
        $answer = ($value*2222.2222);
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
         * 1 = Kilograms
         * 2 = Grams
         * 3 = Metric Tons
         * 4 = Pounds
         * 5 = Ounces
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
        //Does the conversion from Kilograms to Grams and returns the answer
        elseif ($from == "1" && $to == "2") {
            $tempVal = $this->convKilogramsToMetrictons($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symKG", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToGrams($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symG", "conversions") . ".";
        }
        //Does the conversion from Kilograms to Metric Tons and returns the answer
        elseif ($from == "1" && $to == "3") {
            return $value . $this->objLanguage->languageText("mod_conversions_symKG", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convKilogramsToMetrictons($value) , 2) . " " . $this->objLanguage->languageText("mod_conversions_symTONS", "conversions") . ".";
        }
        //Does the conversion from Kilograms to Pounds and returns the answer
        elseif ($from == "1" && $to == "4") {
            $tempVal = $this->convKilogramsToMetrictons($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symKG", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToPounds($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symLBS", "conversions") . ".";
        }
        //Does the conversion from Kilograms to Ounces and returns the answer
        elseif ($from == "1" && $to == "5") {
            $tempVal = $this->convKilogramsToMetrictons($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symKG", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToOunces($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symOZ", "conversions") . ".";
        }
        //Does the conversion from Grams to Kilograms and returns the answer
        elseif ($from == "2" && $to == "1") {
            $tempVal = $this->convGramsToMetrictons($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symG", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToKilograms($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symKG", "conversions") . ".";
        }
        //Does the conversion from Grams to Metric Tons and returns the answer
        elseif ($from == "2" && $to == "3") {
            return $value . $this->objLanguage->languageText("mod_conversions_symG", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convGramsToMetrictons($value) , 2) . " " . $this->objLanguage->languageText("mod_conversions_symTONS", "conversions") . ".";
        }
        //Does the conversion from Grams to Pounds and returns the answer
        elseif ($from == "2" && $to == "4") {
            $tempVal = $this->convGramsToMetrictons($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symG", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToPounds($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symLBS", "conversions") . ".";
        }
        //Does the conversion from Grams to Ounces and returns the answer
        elseif ($from == "2" && $to == "5") {
            $tempVal = $this->convGramsToMetrictons($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symG", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToOunces($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symOZ", "conversions") . ".";
        }
        //Does the conversion from Metric Tons to Kilograms and returns the answer
        elseif ($from == "3" && $to == "1") {
            return $value . " " . $this->objLanguage->languageText("mod_conversions_symTONS", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToKilograms($value) , 2) . $this->objLanguage->languageText("mod_conversions_symKG", "conversions") . ".";
        }
        //Does the conversion from Metric Tons to Grams and returns the answer
        elseif ($from == "3" && $to == "2") {
            return $value . " " . $this->objLanguage->languageText("mod_conversions_symTONS", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToGrams($value) , 2) . $this->objLanguage->languageText("mod_conversions_symG", "conversions") . ".";
        }
        //Does the conversion from Metric Tons to Pounds and returns the answer
        elseif ($from == "3" && $to == "4") {
            return $value . " " . $this->objLanguage->languageText("mod_conversions_symTONS", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToPounds($value) , 2) . $this->objLanguage->languageText("mod_conversions_symLBS", "conversions") . ".";
        }
        //Does the conversion from Metric Tons to Ounces and returns the answer
        elseif ($from == "3" && $to == "5") {
            return $value . " " . $this->objLanguage->languageText("mod_conversions_symTONS", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToOunces($value) , 2) . $this->objLanguage->languageText("mod_conversions_symOZ", "conversions") . ".";
        }
        //Does the conversion from Pounds to Kilograms and returns the answer
        elseif ($from == "4" && $to == "1") {
            $tempVal = $this->convPoundsToMetrictons($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symLBS", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToKilograms($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symKG", "conversions") . ".";
        }
        //Does the conversion from Pounds to Grams and returns the answer
        elseif ($from == "4" && $to == "2") {
            $tempVal = $this->convPoundsToMetrictons($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symLBS", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToGrams($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symG", "conversions") . ".";
        }
        //Does the conversion from Pounds to Metric Tons and returns the answer
        elseif ($from == "4" && $to == "3") {
            return $value . $this->objLanguage->languageText("mod_conversions_symLBS", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convPoundsToMetrictons($value) , 2) . " " . $this->objLanguage->languageText("mod_conversions_symTONS", "conversions") . ".";
        }
        //Does the conversion from Pounds to Ounces and returns the answer
        elseif ($from == "4" && $to == "5") {
            $tempVal = $this->convPoundsToMetrictons($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symLBS", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToOunces($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symOZ", "conversions") . ".";
        }
        //Does the conversion from Ounces to Kilograms and returns the answer
        elseif ($from == "5" && $to == "1") {
            $tempVal = $this->convOuncesToMetrictons($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symOZ", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToKilograms($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symKG", "conversions") . ".";
        }
        //Does the conversion from Ounces to Grams and returns the answer
        elseif ($from == "5" && $to == "2") {
            $tempVal = $this->convOuncesToMetrictons($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symOZ", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToGrams($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symG", "conversions") . ".";
        }
        //Does the conversion from Ounces to Metric Tons and returns the answer
        elseif ($from == "5" && $to == "3") {
            return $value . $this->objLanguage->languageText("mod_conversions_symOZ", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convOuncesToMetrictons($value) , 2) . " " . $this->objLanguage->languageText("mod_conversions_symTONS", "conversions") . ".";
        }
        //Does the conversion from Ounces to Pounds and returns the answer
        elseif ($from == "5" && $to == "4") {
            $tempVal = $this->convOuncesToMetrictons($value);
            return $value . $this->objLanguage->languageText("mod_conversions_symOZ", "conversions") . " " . $this->objLanguage->languageText("mod_conversions_convertedTo", "conversions") . " " . round($this->convMetrictonsToOunces($tempVal) , 2) . $this->objLanguage->languageText("mod_conversions_symLBS", "conversions") . ".";
        }
        //Checks to see if $value is NULL
        else {
            return $this->objLanguage->languageText('mod_conversions_insertError', 'conversions');
        }
    }
}
?>
