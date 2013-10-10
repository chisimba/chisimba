<?php
/**
 * Returns a drop down list and the conversion answer for the left drop-down menu, in the module conversions
 * It also returns the different forms dealt with within the module
 * @category  Chisimba
 * @package   conversions
 * @author    Nonhlanhla Gangeni <2539399@uwc.ac.za>
 * @author    Nazheera Khan <2524939@uwc.ac.za>
 * @author    Faizel Lodewyk <2528194@uwc.ac.za>
 * @author    Hendry Thobela <2649282@uwc.ac.za>
 * @author    Ebrahim Vasta <2623441@uwc.ac.za>
 * @author    Keanon Wagner <2456923@uwc.ac.za>
 * @author    Raymond Williams <2541826@uwc.ac.za>
 * @copyright 2007 UWC
 * @filesource
 */
class navigate extends object
{
    /**
     * Constructor method to instantiate objects and get variables
     *
     * @return void
     * @access public
     */
    public function init() 
    {
        $this->objDist = $this->getObject('dist');
        $this->objTemp = $this->getObject('temp');
        $this->objVol = $this->getObject('vol');
        $this->objWeight = $this->getObject('weight');
        $this->objLanguage = $this->getObject('language', 'language');
    }
    /**
     * the navigation menu used in the module
     *
     * @return the navigation form
     * @access public
     */
    public function conversionsFormNav() 
    {
        //creating a form
        $gform = new form('goTo', $this->uri(array(
            'action' => 'goto'
        )));
        //start a fieldset
        $this->loadClass('fieldset', 'htmlelements');
        $gfieldset = new fieldset;
        $gt = $this->newObject('htmltable', 'htmlelements');
        $gt->cellpadding = 5;
        //to dropdown
        $gtodrop = new dropdown('goTo');
        $gtodrop->addOption("dist", $this->objLanguage->languageText("mod_conversions_Distance", "conversions"));
        $gtodrop->addOption("temp", $this->objLanguage->languageText("mod_conversions_Temperature", "conversions"));
        $gtodrop->addOption("vol", $this->objLanguage->languageText("mod_conversions_Volume", "conversions"));
        $gtodrop->addOption("weight", $this->objLanguage->languageText("mod_conversions_Weight", "conversions"));
        $gt->startRow();
        $gtlabel = new label($this->objLanguage->languageText('mod_conversions_select', 'conversions') . ':', 'input_goTo');
        $gt->addCell($gtlabel->show() . $gtodrop->show());
        $gt->endRow();
        //end off the form and add the buttons
        $this->objconvButton2 = new button($this->objLanguage->languageText('mod_conversions_go', 'conversions'));
        $this->objconvButton2->setValue($this->objLanguage->languageText('mod_conversions_go', 'conversions'));
        $this->objconvButton2->setToSubmit();
        $gfieldset->addContent($gt->show());
        $gform->addToForm($gfieldset->show());
        $gform->addToForm($this->objconvButton2->show());
        $gform = $gform->show();
        $gobjFeatureBox = $this->getObject('featurebox', 'navigation');
        $gret = $gobjFeatureBox->showContent($this->objLanguage->languageText("mod_conversions_goto", "conversions") , $gform);
        return $gret;
    }
    /**
     * the distance form used in this module to do the distance conversion
     *
     * @return the distance form
     * @access public
     */
    public function dist() 
    {
        //creating a form
        $cform = new form('distance', $this->uri(array(
            'action' => 'dist'
        )));
        //start a fieldset
        $this->loadClass('fieldset', 'htmlelements');
        $cfieldset = new fieldset;
        $ct = $this->newObject('htmltable', 'htmlelements');
        $ct->cellpadding = 5;
        //value textfield
        $ct->startRow();
        $ctvlabel = new label($this->objLanguage->languageText('mod_conversions_value', 'conversions') . ':', 'input_cvalue');
        $ctv = new textinput('value');
        $ct->addCell($ctvlabel->show());
        $ct->addCell($ctv->show());
        $ct->endRow();
        //conversions dropdown
        $fromdrop = new dropdown('from');
        $fromdrop->addOption(1, $this->objLanguage->languageText("mod_conversions_Centimeters", "conversions"));
        $fromdrop->addOption(2, $this->objLanguage->languageText("mod_conversions_Millimeters", "conversions"));
        $fromdrop->addOption(3, $this->objLanguage->languageText("mod_conversions_Feet", "conversions"));
        $fromdrop->addOption(4, $this->objLanguage->languageText("mod_conversions_Yards", "conversions"));
        $fromdrop->addOption(5, $this->objLanguage->languageText("mod_conversions_Meters", "conversions"));
        $fromdrop->addOption(6, $this->objLanguage->languageText("mod_conversions_Kilometers", "conversions"));
        $fromdrop->addOption(7, $this->objLanguage->languageText("mod_conversions_Miles", "conversions"));
        $todrop = new dropdown('to');
        $todrop->addOption(1, $this->objLanguage->languageText("mod_conversions_Centimeters", "conversions"));
        $todrop->addOption(2, $this->objLanguage->languageText("mod_conversions_Millimeters", "conversions"));
        $todrop->addOption(3, $this->objLanguage->languageText("mod_conversions_Feet", "conversions"));
        $todrop->addOption(4, $this->objLanguage->languageText("mod_conversions_Yards", "conversions"));
        $todrop->addOption(5, $this->objLanguage->languageText("mod_conversions_Meters", "conversions"));
        $todrop->addOption(6, $this->objLanguage->languageText("mod_conversions_Kilometers", "conversions"));
        $todrop->addOption(7, $this->objLanguage->languageText("mod_conversions_Miles", "conversions"));
        $ct->startRow();
        $flabel = new label($this->objLanguage->languageText('mod_conversions_convertfrom', 'conversions') . ':', 'input_convertfrom');
        $ct->addCell($flabel->show());
        $ct->addCell($fromdrop->show());
        $ct->endRow();
        $ct->startRow();
        $tlabel = new label($this->objLanguage->languageText('mod_conversions_convertto', 'conversions') . ':', 'input_convertto');
        $ct->addCell($tlabel->show());
        $ct->addCell($todrop->show());
        $ct->endRow();
        //end off the form and add the buttons
        $this->objconvButton = new button($this->objLanguage->languageText('mod_conversions_convert', 'conversions'));
        $this->objconvButton->setValue($this->objLanguage->languageText('mod_conversions_convert', 'conversions'));
        $this->objconvButton->setToSubmit();
        $cfieldset->addContent($ct->show());
        $cform->addToForm($cfieldset->show());
        $cform->addToForm($this->objconvButton->show());
        $cform = $cform->show();
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_conversions_distanceconverter", "conversions") , $cform);
        return $ret;
    }
    /**
     * the temperature form used in this module to do the temperature conversion
     *
     * @return the temperature form
     * @access public
     */
    public function temp() 
    {
        //creating a form
        $cform = new form('temperature', $this->uri(array(
            'action' => 'temp'
        )));
        //start a fieldset
        $this->loadClass('fieldset', 'htmlelements');
        $cfieldset = new fieldset;
        $ct = $this->newObject('htmltable', 'htmlelements');
        $ct->cellpadding = 5;
        //value textfield
        $ct->startRow();
        $ctvlabel = new label($this->objLanguage->languageText('mod_conversions_value', 'conversions') . ':', 'input_cvalue');
        $ctv = new textinput('value');
        $ct->addCell($ctvlabel->show());
        $ct->addCell($ctv->show());
        $ct->endRow();
        //conversions dropdown
        $fromdrop = new dropdown('from');
        $fromdrop->addOption(1, $this->objLanguage->languageText("mod_conversions_Celsius", "conversions"));
        $fromdrop->addOption(2, $this->objLanguage->languageText("mod_conversions_Fahrenheit", "conversions"));
        $fromdrop->addOption(3, $this->objLanguage->languageText("mod_conversions_Kelvin", "conversions"));
        $todrop = new dropdown('to');
        $todrop->addOption(1, $this->objLanguage->languageText("mod_conversions_Celsius", "conversions"));
        $todrop->addOption(2, $this->objLanguage->languageText("mod_conversions_Fahrenheit", "conversions"));
        $todrop->addOption(3, $this->objLanguage->languageText("mod_conversions_Kelvin", "conversions"));
        $ct->startRow();
        $flabel = new label($this->objLanguage->languageText('mod_conversions_convertfrom', 'conversions') . ':', 'input_convertfrom');
        $ct->addCell($flabel->show());
        $ct->addCell($fromdrop->show());
        $ct->endRow();
        $ct->startRow();
        $tlabel = new label($this->objLanguage->languageText('mod_conversions_convertto', 'conversions') . ':', 'input_convertto');
        $ct->addCell($tlabel->show());
        $ct->addCell($todrop->show());
        $ct->endRow();
        //end off the form and add the buttons
        $this->objconvButton = new button($this->objLanguage->languageText('mod_conversions_convert', 'conversions'));
        $this->objconvButton->setValue($this->objLanguage->languageText('mod_conversions_convert', 'conversions'));
        $this->objconvButton->setToSubmit();
        $cfieldset->addContent($ct->show());
        $cform->addToForm($cfieldset->show());
        $cform->addToForm($this->objconvButton->show());
        $cform = $cform->show();
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_conversions_temperatureconverter", "conversions") , $cform);
        return $ret;
    }
    /**
     * the volume form used in this module to do the volume conversion
     *
     * @return the volume form
     * @access public
     */
    public function vol() 
    {
        //creating a form
        $cform = new form('volume', $this->uri(array(
            'action' => 'vol'
        )));
        //start a fieldset
        $this->loadClass('fieldset', 'htmlelements');
        $cfieldset = new fieldset;
        $ct = $this->newObject('htmltable', 'htmlelements');
        $ct->cellpadding = 5;
        //value textfield
        $ct->startRow();
        $ctvlabel = new label($this->objLanguage->languageText('mod_conversions_value', 'conversions') . ':', 'input_cvalue');
        $ctv = new textinput('value');
        $ct->addCell($ctvlabel->show());
        $ct->addCell($ctv->show());
        $ct->endRow();
        //conversions dropdown
        $fromdrop = new dropdown('from');
        $fromdrop->addOption(1, $this->objLanguage->languageText("mod_conversions_Litres", "conversions"));
        $fromdrop->addOption(2, $this->objLanguage->languageText("mod_conversions_Milliliters", "conversions"));
        $fromdrop->addOption(4, $this->objLanguage->languageText("mod_conversions_CubicMetres", "conversions"));
        $fromdrop->addOption(5, $this->objLanguage->languageText("mod_conversions_CubicCentimetres", "conversions"));
        $fromdrop->addOption(3, $this->objLanguage->languageText("mod_conversions_CubicDecimetres", "conversions"));
        $todrop = new dropdown('to');
        $todrop->addOption(1, $this->objLanguage->languageText("mod_conversions_Litres", "conversions"));
        $todrop->addOption(2, $this->objLanguage->languageText("mod_conversions_Milliliters", "conversions"));
        $todrop->addOption(4, $this->objLanguage->languageText("mod_conversions_CubicMetres", "conversions"));
        $todrop->addOption(5, $this->objLanguage->languageText("mod_conversions_CubicCentimetres", "conversions"));
        $todrop->addOption(3, $this->objLanguage->languageText("mod_conversions_CubicDecimetres", "conversions"));
        $ct->startRow();
        $flabel = new label($this->objLanguage->languageText('mod_conversions_convertfrom', 'conversions') . ':', 'input_convertfrom');
        $ct->addCell($flabel->show());
        $ct->addCell($fromdrop->show());
        $ct->endRow();
        $ct->startRow();
        $tlabel = new label($this->objLanguage->languageText('mod_conversions_convertto', 'conversions') . ':', 'input_convertto');
        $ct->addCell($tlabel->show());
        $ct->addCell($todrop->show());
        $ct->endRow();
        //end off the form and add the buttons
        $this->objconvButton = new button($this->objLanguage->languageText('mod_conversions_convert', 'conversions'));
        $this->objconvButton->setValue($this->objLanguage->languageText('mod_conversions_convert', 'conversions'));
        $this->objconvButton->setToSubmit();
        $cfieldset->addContent($ct->show());
        $cform->addToForm($cfieldset->show());
        $cform->addToForm($this->objconvButton->show());
        $cform = $cform->show();
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_conversions_volumeconverter", "conversions") , $cform);
        return $ret;
    }
    /**
     * the weight form used in this module to do the weight conversion
     *
     * @return the weight form
     * @access public
     */
    public function weight() 
    {
        //creating a form
        $cform = new form('weight', $this->uri(array(
            'action' => 'weight'
        )));
        //start a fieldset
        $this->loadClass('fieldset', 'htmlelements');
        $cfieldset = new fieldset;
        $ct = $this->newObject('htmltable', 'htmlelements');
        $ct->cellpadding = 5;
        //value textfield
        $ct->startRow();
        $ctvlabel = new label($this->objLanguage->languageText('mod_conversions_value', 'conversions') . ':', 'input_cvalue');
        $ctv = new textinput('value');
        $ct->addCell($ctvlabel->show());
        $ct->addCell($ctv->show());
        $ct->endRow();
        //conversions dropdown
        $fromdrop = new dropdown('from');
        $fromdrop->addOption(1, $this->objLanguage->languageText("mod_conversions_Kilograms", "conversions"));
        $fromdrop->addOption(2, $this->objLanguage->languageText("mod_conversions_Grams", "conversions"));
        $fromdrop->addOption(3, $this->objLanguage->languageText("mod_conversions_MetricTon", "conversions"));
        $fromdrop->addOption(4, $this->objLanguage->languageText("mod_conversions_Pounds", "conversions"));
        $fromdrop->addOption(5, $this->objLanguage->languageText("mod_conversions_Ounces", "conversions"));
        $todrop = new dropdown('to');
        $todrop->addOption(1, $this->objLanguage->languageText("mod_conversions_Kilograms", "conversions"));
        $todrop->addOption(2, $this->objLanguage->languageText("mod_conversions_Grams", "conversions"));
        $todrop->addOption(3, $this->objLanguage->languageText("mod_conversions_MetricTon", "conversions"));
        $todrop->addOption(4, $this->objLanguage->languageText("mod_conversions_Pounds", "conversions"));
        $todrop->addOption(5, $this->objLanguage->languageText("mod_conversions_Ounces", "conversions"));
        $ct->startRow();
        $flabel = new label($this->objLanguage->languageText('mod_conversions_convertfrom', 'conversions') . ':', 'input_convertfrom');
        $ct->addCell($flabel->show());
        $ct->addCell($fromdrop->show());
        $ct->endRow();
        $ct->startRow();
        $tlabel = new label($this->objLanguage->languageText('mod_conversions_convertto', 'conversions') . ':', 'input_convertto');
        $ct->addCell($tlabel->show());
        $ct->addCell($todrop->show());
        $ct->endRow();
        //end off the form and add the buttons
        $this->objconvButton = new button($this->objLanguage->languageText('mod_conversions_convert', 'conversions'));
        $this->objconvButton->setValue($this->objLanguage->languageText('mod_conversions_convert', 'conversions'));
        $this->objconvButton->setToSubmit();
        $cfieldset->addContent($ct->show());
        $cform->addToForm($cfieldset->show());
        $cform->addToForm($this->objconvButton->show());
        $cform = $cform->show();
        $objFeatureBox = $this->getObject('featurebox', 'navigation');
        $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_conversions_weightconverter", "conversions") , $cform);
        return $ret;
    }
    /**
     * Gets the answer for the conversion and returns it in a featurebox
     *
     * @param  numerical value $value
     * @param  string $from  Unit to be converted from
     * @param  string $to    Unit to be converted to
     * @param  unknown $action  this is the action of the form used
     * @return the answer to a conversion in a featurebox
     * @access public
     */
    public function answer($value = NULL, $from = NULL, $to = NULL, $action = NULL) 
    {
        if (isset($value)) {
            switch ($action) {
                case 'dist':
                    //gets an answer for the conversion
                    $answer = $this->objDist->doConversion($value, $from, $to);
                    //gets the object featurebox
                    $objFeatureBox = $this->getObject('featurebox', 'navigation');
                    //places the anser in the featurebox
                    $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_conversions_answer", "conversions") , $answer);
                    //returns the featurebox
                    return $ret;
                    break;

                case 'temp':
                    //gets an answer for the conversion
                    $answer = $this->objTemp->doConversion($value, $from, $to);
                    //gets the object featurebox
                    $objFeatureBox = $this->getObject('featurebox', 'navigation');
                    //places the anser in the featurebox
                    $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_conversions_answer", "conversions") , $answer);
                    //returns the featurebox
                    return $ret;
                    break;

                case 'vol':
                    //gets an answer for the conversion
                    $answer = $this->objVol->doConversion($value, $from, $to);
                    //gets the object featurebox
                    $objFeatureBox = $this->getObject('featurebox', 'navigation');
                    //places the anser in the featurebox
                    $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_conversions_answer", "conversions") , $answer);
                    //returns the featurebox
                    return $ret;
                    break;

                case 'weight':
                    //gets an answer for the conversion
                    $answer = $this->objWeight->doConversion($value, $from, $to);
                    //gets the object featurebox
                    $objFeatureBox = $this->getObject('featurebox', 'navigation');
                    //places the anser in the featurebox
                    $ret = $objFeatureBox->showContent($this->objLanguage->languageText("mod_conversions_answer", "conversions") , $answer);
                    //returns the featurebox
                    return $ret;
                    break;
            }
        }
    }
}
?>
