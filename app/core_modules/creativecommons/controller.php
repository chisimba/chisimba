<?php

/**
 * Controller for the Creative Commons Module
 * @author Tohir Solomons
 */
class creativecommons extends controller
{
    /**
     * Constructor
     */
    public function init()
    {
        $this->objCC = $this->getObject('dbcreativecommons');
    }
    
    /**
     * Dispatch Method
     *
     * @param string $action Action to be taken
     * @return string
     */
    public function dispatch($action)
    {
        switch ($action)
        {
            case 'selecttest': // Demo the License Chooser
                return 'template_selectdemo.php';
            default:
                $licences = $this->objCC->getAll();
                $this->setVarByRef('licences', $licences);
                return 'list_licences.php';
        }
        
    }
}

?>