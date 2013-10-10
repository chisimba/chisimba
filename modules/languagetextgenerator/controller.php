<?php

/**
* Controller for the Language Text Generator Module
* @author Tohir Solomons
*/
class languagetextgenerator extends controller
{
    
    /**
    * Constructor method to instantiate objects and get variables
    * @access public
    */
    public function init()
    {
        // Load the English Database Class
        $this->objLang = $this->getObject('dbenglish');
        
        // Load the Language Text Class
        $this->objLanguage = $this->getObject('language', 'language');
    }
    
    /**
    * Controller Dispath Method to run a specified action
    */
    public function dispatch()
    {
        // Since this module only has one action, all functionality
        // is contained in this one function
        
        // Get the Search String
        $search = $this->getParam('search');
        
        // If Item was Searched, Process Search Results
        if ($search != '') {
            $results = $this->objLang->searchString($search);
            
            $this->setVarByRef('results', $results);
        }
        
        // Return Template
        return 'template.php';
    }

}
?>