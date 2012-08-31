<?php
/**
* Class to provide SysConfig an input for the SYSTEM_TYPE parameter
* @author Tohir Solomons
*/
class sysconfig_system_type extends object
{
    /**
    * @var string $defaultValue Current Value of the Parameter
    */
    var $defaultValue;
    
    /**
    * Standard Constructor
    */
    public function init()
    { 
        $this->objAbstract = $this->getObject('systext_facet', 'systext');
        $this->objLanguage = $this->getObject('language', 'language');
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
        // Load the Radio Button Class
        $this->loadClass('radio', 'htmlelements');
        
        // Input MUST be called 'pvalue'
        $objElement = new radio ('pvalue');
        
        $systemTypeList = $this->objAbstract->listSystemTypes();
        
        foreach($systemTypeList as $systemType){
            $objElement -> addOption($systemType['systemtype'], $systemType['systemtype']);
        }
        
        // Set Default Selected
        $objElement->setSelected($this->defaultValue);
        $objElement->setBreakSpace('<br />');
        
        $string = '<p>Please select the type of system text should be abstracted to:</p>';
        
        // Return String
        return $string.$objElement->show();
    }
    
    /**
    * Method to run actions that need to occur once the parameter is updated
    */
    public function postUpdateActions()
    {
        $this->objAbstract->updateSession();
        return;
    }
    
    
}

?>