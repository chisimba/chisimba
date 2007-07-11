<?php

/**
* Class to provide SysConfig an input for the KEWL_DEFAULT_SKIN parameter
* @author Tohir Solomons
*/
class sysconfig_kewl_default_skin extends object
{

    /**
    * Standard Constructor
    */
    public function init()
    { }
    
    /**
    * Method to set the current default value
    */
    public function setDefaultValue($value)
    {
        $this->defaultVaule = $value;
    }
    
    /**
    * Method to display the sysconfig interface
    */
    public function show()
    {
        // Load the Radio button class
        $this->loadClass('radio', 'htmlelements');
        
        // Load the Skin Object
        $objSkin = $this->getObject('skin', 'skin');
        
        $skinsList = $objSkin->getListofSkins();
        
        // Input MUST be called 'pvalue'
        $objElement = new radio ('pvalue');
        
        foreach ($skinsList as $element=> $value) {
           $objElement->addOption($element, $value);
        }
        
        // Set Default Selected
        $objElement->setSelected($this->defaultVaule);
        
        // Set radio buttons to be one per line
        $objElement->setBreakSpace('<br />');
        
        // return finished radio button
        return $objElement->show();
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