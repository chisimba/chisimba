<?php

/**
 * Short description for file
 * 
 * Long description (if any) ...
 * 
 * PHP version 3
 * 
 * The license text...
 * 
 * @category  Chisimba
 * @package   help
 * @author    Megan Watson <mwatson@uwc.ac.za>
 * @copyright 2007 Megan Watson
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
/* -------------------- help class extends controller ----------------*/
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
* Class for providing a service to other modules that want to
* display help
*
* @author Tohir Solomons
*/
class help extends object  {

    /**
     * Description for var
     * @var    string
     * @access public
     */
    var $rootModule=NULL;

    /**
     * Description for var
     * @var    unknown
     * @access public 
     */
    var $helpItem;

    /**
    * Constructor Method for the class
    */
    function init()
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
    * @param  string $helpItem The help item action to show
    * @param  string $module   The module where the help/action exits
    * @param  strin  $helpText An alternate display to the help icon
    * @return string Icon and Popup Link | Null if now help exists for that element
    */
    function show($helpItem = FALSE, $module = FALSE, $helpText = FALSE)
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