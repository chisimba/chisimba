<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to Generate a Radio Button List of available licenses
 * @author Tohir Solomons
 */
class licensechooser extends object
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
     * Size of Icons to be Use, either big (32x32) or small (20x20)
     *
     * @var string
     */
    public $icontype = 'big'; // or small
    
    /**
     * Constructor
     */
    public function init()
    {
        // Load the Creative Commons Object
        $this->objCC =& $this->getObject('dbcreativecommons');
        
        // Load the Sysconfig Object
        $this->objSysConfig =& $this->getObject('dbsysconfig', 'sysconfig');
        
        // Load the Radio Button Class
        $this->loadClass('radio', 'htmlelements');
        
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
            
            // Create Radio Button
            $radio = new radio($this->inputName);
            
            // Set Breakspace
            $radio->setBreakSpace('<br />');
            
            $iconsFolder = 'icons/creativecommons_v3';
            
            // Generate Blank Icon
            $this->objIcon->setIcon ('blank', NULL, $iconsFolder);
            $blankIcon = $this->objIcon->show();
            
            // Loop through Licenses
            foreach ($licenses as $license)
            {
                // Check if License is Enabled
                if ($this->objSysConfig->getValue($license['code'], 'creativecommons') == 'Y') {
                    
                    if ($this->icontype == 'big') {
                        $filename = $license['code'].'_big';
                    } else {
                        $filename = $license['code'];
                    }
                    
                    $filename = str_replace('/', '_', $filename);
                    
                    $this->objIcon->setIcon ($filename, NULL, $iconsFolder);
                    $iconList = $this->objIcon->show();
                    
                    $title = $license['title'];
                    if ($title == 'Attribution Non-commercial Share') {
                        $title = 'Attribution Non-commercial Share Alike';
                    }
                    // Add to Radio Group
                    $radio->addOption($license['code'], $iconList.' '.$title);
                }
            }
            
            // Set Default Selected Value
            $radio->setSelected($this->defaultValue);
            
            // Return Radio Button
            return $radio->show();
        }
    }
}

?>