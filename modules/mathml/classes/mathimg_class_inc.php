<?php

/**
* Class to Render a MathML Expression as an image
*
* This class is a wrapper to PHP Math Publisher available at:
* http://www.xm1math.net/phpmathpublisher/
*
* Instead of requiring users to have an appropriate browser, as well as the necessary
* fonts installed, and changing the doctype for pages, this class renders the expression as an image.
*
* The image is stored to the filesystem, using an MD5 encrypted file name. PHP Math Publisher is
* intelligent to only render expressions once, thereafter returning the one stored on the file system.
*
* A few changes were made to the resources/phpmathpublisher/mathpublisher.php file
* - The four global variables were declared as such. $dirimg became $GLOBALS['dirimg'];
* - The two global variables for path were commented out. They are declared in this class
* - Allowed users to use pi, not just Pi
*
* @author Tohir Solomons
*/
require_once($this->getResourcePath('phpmathpublisher/mathpublisher.php', 'mathml'));
class mathimg extends object
{
    /**
    * Constructor
    *
    * This function sets the config paths for PHP Math Publisher, as well checks that the save path
    * in usrfiles/mathml/ exists.
    */
    function init()
    {   
        $objConfig = $this->getObject('altconfig', 'config');
        $GLOBALS['dirfonts'] = $objConfig->getModulePath().'mathml/resources/phpmathpublisher/fonts';
        //echo $GLOBALS['dirfonts'];
        $GLOBALS['dirimg'] = $objConfig->getcontentBasePath().'mathml';
        
        // Check that Previews Folder exists - if not, create it
        $mkdir = $this->getObject('mkdir', 'files');;
        $mkdir->mkdirs($GLOBALS['dirimg']);
    }
    
    /**
    * Method to render a MathML Expression as an image
    *
    * Please note that apart from rendering the image, the return value is the HTML to the Image
    * So it will return something like:
    * <img src="usrfiles/mathml/imagepath.png" alt="x+y=z" title="x+y=z"/>
    *
    * @param string $expression MathML expression to render
    * @param int $size Font Size?
    * @return string HTML for Rendered Image
    */
    function render($expression, $size=12)
    {
        return mathimage($expression, $size, 'usrfiles/mathml/');
    }
}
?>