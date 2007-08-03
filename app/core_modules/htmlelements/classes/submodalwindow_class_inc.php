<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 5
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   htmlelements
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// Include the HTML interface class

/**
 * Description for require_once
 */
require_once("ifhtml_class_inc.php");

/**
* Sub Modal Window
*
* This class is a wrapper to JavaScript to create a submodal window.
*
* Q&A
*
* 1) What is a Sub Modal Window?
* A sub modal window is similar to a popup window with a view differences. 
* A submodal window pops up in the current page, and disables any functionality on the opener page.
* A popup window opens in a separate window, and users are still able to access any functionality on the opener page
*
* 2) Any real life examples
* Sub Modal Windows are common in desktop applications. Consider 'Open File' in a word processor. When this window is open,
* all other functionality is disabled. This is a type of Sub Modal Window
*
* 3) How common is Sub Modal Windows in web applications
* Microsoft was the first to implement a proprietary JS solution with a function called showModalDialog.
* More info at: http://msdn.microsoft.com/workshop/author/dhtml/reference/methods/showmodaldialog.asp
*
* Since then, many others have developed cross-browser solutions to this. This implementation was released by Todd Huss,
* and is available at: http://gabrito.com/files/subModal/
*
*
* @author Tohir Solomons
*         
*         Example:
*         $objSubModalWindow = $this->getObject('submodalwindow');
*         echo $objSubModalWindow->show('Click this Button', $this->uri(array('action'=>'submodalexample_content')), 'button');
*         echo $objSubModalWindow->show('Click this Link', $this->uri(array('action'=>'submodalexample_content')), 'link');
*/
class submodalwindow extends object implements ifhtml
{
    
    /**
    * @var string $optionType - Either 'button' or 'link'
    */
    public $optionType = 'button';
    
    /**
    * @var string $text Text of button or link
    */
    public $text = 'Text of Button or Link';
    
    /**
    * @var string $url Link to Page to Open as Submodal window
    */
    public $url;
    
    /**
    * $var int $width Width of Sub Modal Window in pixels
    */
    public $width = 600;
    
    /**
    * $var int $height Height of Sub Modal Window in pixels
    */
    public $height = 400;
    
    /**
    * Constructor
    */
    public function init()
    { }
    
    /**
    * Method to Show Sub Modal Window Open from Button
    */
    private function showButton()
    {
        // Loadd Button Class
        $this->loadClass('button', 'htmlelements');
        
        // Generate a Random Number
        srand ((double) microtime( )*1000000); 
        $random_number = rand();
        
        // Create Button
        $button = new button ('modal_'.$random_number, $this->text, 
                'showPopWin(\''.$this->url.'\', '.$this->width.', '.$this->height.', null);'
            );
        
        // Return Button
        return $button->show();
    }
    
    /**
    * Method to Show Sub Modal Window Open from Link
    */
    private function showLink()
    {
        // Load the Link Class
        $this->loadClass('link', 'htmlelements');
        
        // Create Link
        $link = new link ($this->url);
        $link->link = $this->text;
        // Set class to submodal-[requiredwidth]-[requiredheight]
        $link->cssClass = 'submodal-'.$this->width.'-'.$this->height;
        
        // Return Link
        return $link->show();
    }
    
    /**
    * Method to Display the Sub Modal Window Opener - either button or link
    * @param string $text   Text of Link or Button
    * @param string $url    URL of Sub Modal Window
    * @param string $type   Type of Opener - Either button or link
    * @param int    $width  Width of Sub Modal Window
    * @param int    $height Height of Sub Modal Window
    */
    public function show($text=NULL, $url=NULL, $type='button', $width=NULL, $height=NULL)
    {
        // Set Text if not Null
        if ($text != NULL) {$this->text = $text;}
        
        // Set URL if not NULL
        if ($url != NULL) {$this->url = $url;}
        
        // Set Width if not Null
        if ($width != NULL) {$this->width = $width;}
        
        // Set Height if not NULL
        if ($height != NULL) {$this->height = $height;}
        
        // Set Option Type
        $this->optionType = $type;
        
        // Send the JavaScript to the header
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('submodal/submodal.js', 'htmlelements'));
        
        // Set Loading Page
        $this->appendArrayVar('headerParams', '<script type="text/javascript">setPopUpLoadingPage(\'core_modules/htmlelements/resources/submodal/loading.php\');</script>');
        
        // Show either as link or button
        if ($this->optionType == 'link') {
            return $this->showLink();
        } else {
            return $this->showButton();
        }
    }
}
?>