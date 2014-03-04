<?php
/**
* Class to provide SysConfig an input for the ALLOW_PARSE parameter of the Glossary Module
* @author Tohir Solomons
*/
class sysconfig_allow_parse extends object
{
    /**
    * @var string $defaultValue Current Value of the Parameter
    */
    public function init()
    {
        $this->objLanguage =& $this->getObject('language', 'language');
    }
    
    /**
    * Method to set the current default value
    */
    public function setDefaultValue($value)
    {
        $this->defaultValue = $value;
    }
    
    /**
    * Method to return a customized input to the SysConfig form
    */
    public function show()
    {
        // Load the Radio button class
        $this->loadClass('radio', 'htmlelements');
        
        // Input MUST be called 'pvalue'
        $objElement = new radio ('pvalue');
        
        $objElement->addOption('1', $this->objLanguage->languageText('word_yes'));
        $objElement->addOption('0', $this->objLanguage->languageText('word_no'));
        
        // Set Default Selected
        $objElement->setSelected($this->defaultValue);
        
        // Set radio buttons to be one per line
        $objElement->setBreakSpace(' &nbsp; ');
        
        $string = '<p>'.$this->objLanguage->languageText('mod_glossary_allowparse', 'glossary').'</p>';
        
        // return finished radio button
        return $string.$objElement->show();
    }
    
    /**
    * Method to run actions that need to occur once the parameter is updated
    */
    public function postUpdateActions()
    {
        return NULL;
    }
    
    
}

?>