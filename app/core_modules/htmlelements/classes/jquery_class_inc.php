<?php

/**
* Class to load the JQuery JavaScript LIbrary
*
* This class merely loads the JavaScript for JQuery. It includes code to prefent a clash with Prototpe/Scriptaculous
* It is not a wrapper. Developers still need to code their own JS functions
*
* @category  Chisimba
* @author  Tohir Solomons
* @package htmlelements
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
* @version   $Id$
* @link      http://avoir.uwc.ac.za
*/

class jquery extends object
{

    /**
    * jQuery Version to load
    *
    * Developers can load any version depending on the requirements for the specific module 
    * This will ensure that modules tested on a certain version of jQuery won't have to be re-tested
    * to work with subsequent releases of jQuery.
    *
    * This will ensure protection against if jQueries backwards compatibility fails for any reason.
    *
    * @var string $version
    */
    public $version;


    /**
    * Constructor
    */
    public function init()
    { 
        //Default jQuery version (Global)
        $this->version = '1.2.3';
    }


    /**
    * Method to set the version of jQuery to load
    *
    */
    public function setVersion($version)
    {
        $this->version = $version;
    }


    /**
    * Method to load the JQuery JavaScript
    *
    * @return string JQuery JavaScript
    */
    public function show()
    {
        // Load JQuery

        switch ($this->version){
            case '1.2.1':
                $returnStr = $this->getJavascriptFile('jquery/1.2.1/jquery-1.2.1.pack.js','htmlelements')."\n";
            break;
            case '1.2.2':
                $returnStr = $this->getJavascriptFile('jquery/1.2.2/jquery-1.2.2.pack.js','htmlelements')."\n";
            break;
            case '1.2.3':
                $returnStr = $this->getJavascriptFile('jquery/1.2.3/jquery-1.2.3.pack.js','htmlelements')."\n";
            break;
            case '1.2.6':
                $returnStr = $this->getJavascriptFile('jquery/1.2.6/jquery-1.2.6.js','htmlelements')."\n";
            break;

            default: //Leaving this on 1.2.3 to be sure it won't effect the rest of the system
                $returnStr = $this->getJavascriptFile('jquery/1.2.3/jquery-1.2.3.pack.js','htmlelements')."\n";
            break;
        }

        $returnStr .= '<script language="JavaScript" type="text/javascript" >
                jQuery.noConflict();
                </script>'."\n"."\n";

        return $returnStr;
    }
    
    /**
     * Method to load the liveQuery plugin script files to the header
     */
    public function loadLiveQueryPlugin()
    {
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('jquery/plugins/livequery/1.0.2/jquery.livequery.js', 'htmlelements'));
    }
    
    /**
     * Method to load the form plugin script files to the header
     */
    public function loadFormPlugin()
    {
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('jquery/plugins/form/2.12/jquery.form.js', 'htmlelements'));
    }
    
    /**
     * Method to load the Image Fit plugin script files to the header
     */
    public function loadImageFitPlugin()
    {
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('jquery/plugins/imagefit/0.2/jquery.imagefit_0.2.js', 'htmlelements'));
    }


    /**
     * Method to load the Image Fit plugin script files to the header
     */
    public function loadEasingPlugin($version = '1.3')
    {
        switch ($this->version){
            case '1.2':
                $this->appendArrayVar('headerParams', $this->getJavascriptFile('jquery/plugins/easing/1.2/jquery.easing.1.2.js', 'htmlelements'));
            break;
            case '1.3':
                $this->appendArrayVar('headerParams', $this->getJavascriptFile('jquery/plugins/easing/1.3/jquery.easing.1.3.js', 'htmlelements'));
            break;
            default:
                $this->appendArrayVar('headerParams', $this->getJavascriptFile('jquery/plugins/easing/1.3/jquery.easing.1.3.js', 'htmlelements'));
            break;
        }
    }

    /**
     * Method to load the ddmenu plugin script files to the header
	 * TODO: This menu conflicts with prototype, resolve the conflict
     */
    public function loadDDMenuPlugin()
    {
        $basePath = 'jquery/plugins/ddmenu/0.3/';
        $this->appendArrayVar('headerParams', '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri($basePath.'ddmenu.css'.'">'));

        $this->loadEasingPlugin('1.3');
        $this->appendArrayVar('headerParams', $this->getJavascriptFile($basePath.'jquery.bdc.ddmenu.pack.js', 'htmlelements'));
    }


    /**
     * Method to load the accordion plugin script files to the header
     */
    public function loadAccordionMenuPlugin($version = '1.3')
    {
        $basePath = 'jquery/plugins/accordion/1.3/';
        $this->appendArrayVar('headerParams', '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri($basePath.'default.css'.'">'));
        $this->appendArrayVar('headerParams', $this->getJavascriptFile($basePath.'accordion.1.3.js', 'htmlelements'));
    }

    /**
     * Method to load the superfish menu plugin script files to the header
	 * TODO: Add superfish menu class to abstractly produce it
     */
    public function loadSuperFishMenuPlugin($version = '1.4.8')
    {
        $basePath = 'jquery/plugins/superfish/1.4.8/';
        $this->appendArrayVar('headerParams', '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri($basePath.'superfish.css'.'">'));
        //$this->appendArrayVar('headerParams', '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri($basePath.'superfish-navbar.css'.'">'));
        $this->appendArrayVar('headerParams', '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri($basePath.'superfish-vertical.css'.'">'));

        $this->appendArrayVar('headerParams', $this->getJavascriptFile($basePath.'hoverIntent.js', 'htmlelements'));
        $this->appendArrayVar('headerParams', $this->getJavascriptFile($basePath.'jquery.bgiframe.min.js', 'htmlelements'));
        $this->appendArrayVar('headerParams', $this->getJavascriptFile($basePath.'superfish.js', 'htmlelements'));
        $this->appendArrayVar('headerParams', $this->getJavascriptFile($basePath.'supersubs.js', 'htmlelements'));
    }

    /**
     * Method to load the jQuery Core API UI Tabbing library
	 * 
     */
    public function loadUITabbing($version = '')
    {
        $basePath = 'jquery/api/';
        $this->appendArrayVar('headerParams', '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri($basePath.'ui/css/flora.all.css'.'">'));
        $this->appendArrayVar('headerParams',$this->getJavascriptFile($basePath.'ui/ui.core.js'.'">', 'htmlelements'));
        $this->appendArrayVar('headerParams',$this->getJavascriptFile($basePath.'ui/ui.tabs.js'.'">', 'htmlelements'));
    }


}

?>