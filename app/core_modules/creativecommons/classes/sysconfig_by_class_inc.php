<?
/**
* Class to provide SysConfig an input for enabling/disabling the Attribution license
* @author Tohir Solomons
*/
class sysconfig_by extends object
{
    /**
    * @var string $defaultValue Current Value of the Parameter
    */
    function init()
    {
        $this->objLanguage =& $this->getObject('language', 'language');
    }
    
    /**
    * Method to set the current default value
    */
    function setDefaultValue($value)
    {
        $this->defaultValue = $value;
    }
    
    /**
    * Method to return a customized input to the SysConfig form
    */
    function show()
    {
        // Load the Radio button class
        $this->loadClass('radio', 'htmlelements');
        
        // Input MUST be called 'pvalue'
        $objElement = new radio ('pvalue');
        
        $objElement->addOption('Y', $this->objLanguage->languageText('word_yes'));
        $objElement->addOption('N', $this->objLanguage->languageText('word_no'));
        
        // Set Default Selected
        $objElement->setSelected($this->defaultValue);
        
        // Set radio buttons to be one per line
        $objElement->setBreakSpace(' &nbsp; ');
        
        $string = '<p>'.$this->objLanguage->languageText('mod_creativecommons_enableby', 'creativecommons').'</p>';
        
        // return finished radio button
        return $string.$objElement->show();
    }
    
    /**
    * Method to run actions that need to occur once the parameter is updated
    */
    function postUpdateActions()
    {
        return NULL;
    }
    
    
}

?>