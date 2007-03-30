<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to Generate a Drop down List of available licenses
 * @author Tohir Solomons
 */
class licensechooserdropdown extends object
{
    /**
     * Name of the Form Input
     *
     * @var string
     */
    public $inputName = 'creativecommons';
    
    /**
     * Name of the Default Value
     *
     * @var string
     */
    public $defaultValue;
    
    /**
     * Constructor
     */
    public function init()
    {
        // Load the Creative Commons Object
        $this->objCC =& $this->getObject('dbcreativecommons');
        $this->objSysConfig =& $this->getObject('dbsysconfig', 'sysconfig');
        
        // Load the GetIcon Object
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
    }
    
    /**
     * Method to display the list
     *
     * @return string Rendered Input
     */
    public function show()
    {
        $objModules = $this->getObject('modules', 'modulecatalogue');
        
        if (!$objModules->checkIfRegistered('creativecommons')) {
            return '';
        } else {
        
            // Get All Licenses
            $licenses = $this->objCC->getAll();
            
            $options = '';
            
            // Loop through Licenses
            foreach ($licenses as $license)
            {
                
                if ($this->objSysConfig->getValue($license['code'], 'creativecommons') == 'Y') {
                    
                    $title = $license['title'];
                    if ($title == 'Attribution Non-commercial Share') {
                        $title = 'Attribution Non-commercial Share Alike';
                    }
                    
                    $selected = ($license['code'] == $this->defaultValue) ? ' selected="selected"' : '';
                    
                    // Add to Radio Group
                    $options .= '<option value="'.$license['code'].'" class="'.$license['code'].'" '.$selected.'>'.$title.'</option>';
                }
            }
            
            $select = '<select name="'.$this->inputName.'" id="input_'.$this->inputName.'">';
            
            $select .= $options;
            
            $select .= '</select>';
            
            
            // Return Radio Button
            return $select;
        }
    }
}

?>