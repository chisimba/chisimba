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
    public $icontype = 'small'; // or big
    
    /**
     * Constructor
     */
    public function init()
    {
        // Load the Creative Commons Object
        $this->objCC =& $this->getObject('dbcreativecommons');
        
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
        // Get All Licenses
        $licenses = $this->objCC->getAll();
        
        // Create Radio Button
        $radio = new radio($this->inputName);
        
        // Set Breakspace
        $radio->setBreakSpace('<br />');
        
        // Determine Size of Icon
        if ($this->icontype == 'big') {
            $iconsFolder = 'icons/creativecommons';
        } else {
            $iconsFolder = 'icons/creativecommons_small';
        }
        
        // Generate Blank Icon
        $this->objIcon->setIcon ('blank', NULL, $iconsFolder);
        $blankIcon = $this->objIcon->show();
        
        // Loop through Licenses
        foreach ($licenses as $license)
        {
            // Get List of Icons
            $icons = explode(',', $license['images']);
    
            $iconList = '';
            
            // Generate Icons
            foreach ($icons as $icon)
            {
                $this->objIcon->setIcon ($icon, NULL, $iconsFolder);
                $iconList .= $this->objIcon->show();
        
            }
            
            // Add Blank Spaces
            $times = 4-count($icons);
            for ($i=1; $i<=$times; $i++) {
                $iconList .= $blankIcon;
            }
            
            // Add to Radio Group
            $radio->addOption($license['code'], $iconList.$license['title']);
        }
        
        // Set Default Selected Value
        $radio->setSelected($this->defaultValue);
        
        // Return Radio Button
        return $radio->show();
    }
}

?>