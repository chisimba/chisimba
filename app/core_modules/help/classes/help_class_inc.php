<?php

/**
 * File containing the class for creating the link to the help
 * 
 * The class creates an icon which links to the help for the module and opens in a pop-up window.
 * 
 * PHP version 3
 * 
 * @category  Chisimba
 * @package   help
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 University of the Western Cape
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

/**
* Class for displaying a help icon for a module. The icon opens the help in a pop up window.
*
* @author Tohir Solomons
*/
class help extends object  {

    /**
     * Variable containing the calling module - the module for which the help file is generated.
     * @var    string
     * @access public
     */
    public $rootModule=NULL;

    /**
     * Variable containing the action or current page for which help is required.
     * @var string
     * @access public 
     */
    public $helpItem;

    /**
    * Constructor function for the class. Function sets up global variables and objects used within the class.
    *
    * @access public
    * @return void
    */
    public function init()
    {
        $this->objHelpIcon = $this->newObject('geticon','htmlelements');
        $this->rootModule = $this->getParam('module', NULL);
        $this->objLanguage = &$this->getObject('language', 'language');
    }

    /**
    * Method to display a help link which opens in a popup window
    *
    * If two parameter are not given, it takes parameters as being:
    * $helpItem = The action parameter in the querystring
    * $module = The current module calling the help item.
    *
    * This method first checks if the language text elements exists.
    * If it does, show the icon and link,
    * Else return NULL
    *
    * @access public
    * @param  string $helpItem The help item action to show
    * @param  string $module   The module for which the help is being generated
    * @param  strin  $helpText An alternate display to the help icon
    * @return string Icon and Popup Link | Null if no help exists for that element
    */
    public function show($helpItem = FALSE, $module = FALSE, $helpText = FALSE)
    {
        $objSkin = & $this->getObject('skin','skin');
        $getAction = FALSE;

        if(!($helpText === FALSE)){
            $helpLink = $helpText;
        }else{
            $this->objHelpIcon->setIcon('help', 'gif', 'icons/modules');
            $helpLink = $this->objHelpIcon->show();
        }

        if ($module) {
            $this->rootModule = $module;
        }

        if (!$helpItem) {
            $helpItem = $this->getParam('action', FALSE);
        }

        // Check if the help for the action exists
        if(!($helpItem === FALSE)){
            if ($this->objLanguage->valueExists('code', 'help_'.$this->rootModule.'_overview_'.$helpItem, $module)){
                $getAction = TRUE;
            }else if($this->objLanguage->valueExists('code', 'help_'.$this->rootModule.'_about_'.$helpItem, $module)){
                $getAction = TRUE;
            }
        }

        // If there is no action or the help doesn't exist - check for the module overview.
        if(!$helpItem || !$getAction){
            if($this->objLanguage->valueExists('code', 'help_'.$this->rootModule.'_about')){
                $helpItem = 'about';
                $getAction = TRUE;
            }else if($this->objLanguage->valueExists('code', 'help_'.$this->rootModule.'_overview')){
                $helpItem = 'about';
                $getAction = TRUE;
            }
        }

        if ($getAction){
            //Popup window
            $this->objPop=& $this->getObject('windowpop','htmlelements');
            $location=$this->uri(array(
                'action'=>'view',
                'helpid'=>$helpItem,
                'rootModule'=>$this->rootModule,
                'module'=>'help'));

            $this->objPop->set('window_name','help');
            $this->objPop->set('location',$location);
            $this->objPop->set('linktext', $helpLink);
            $this->objPop->set('width','400');
            $this->objPop->set('height','400');
            $this->objPop->set('resizable','yes');
            $this->objPop->set('left','300');
            $this->objPop->set('top','300');
            $this->objPop->set('scrollbars', 'yes');
            //        echo $this->objPop->putJs(); // you only need to do this once per page
            return $this->objPop->show();
        } else {
            return NULL;
        }
    }
}

?>