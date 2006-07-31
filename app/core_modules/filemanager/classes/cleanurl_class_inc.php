<?

/**
* Class to Clean up a URL
*
* This class cleans up URLs in the following way:
* - Backstrokes (\) are converted to forward strokes (/)
* - Double strokes or more are made into one
*
* @author Tohir Solomons
*/
class cleanurl extends object
{
    
    /**
    * Constructor
    */
    public function init()
    { }
    
    /**
    * Method to clean up a url
    * @param string $url Url to clean up
    */
    public function cleanUpUrl(&$url)
    {
        $url = str_replace ('\\', '/', $url); // Convert backstrokes to forward strokes
        
        //$url = preg_replace('/\/{2,}/', '/', $url); // convert 
        $url = preg_replace('/\\/{2,}/', '/', $url); // Convert multiples stokes into one
        
        return $url;
    }
    
    /**
    * Method for processing a filename for better display and making it XHTML compliant
    * @param string $fileName
    */
    public function processFileName($fileName)
    {
        $fileName = htmlentities($fileName);
        $fileName = str_replace('_', ' ', $fileName);
        
        return $fileName;
    }
}
?>