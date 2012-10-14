<?php

/**
*
* Class to load the JQuery JavaScript LIbrary
*
* This class merely loads the JavaScript for JQuery. It includes code to
* prefent a clash with Prototpe/Scriptaculous. It is not a wrapper. Developers
* still need to code their own JS functions.
*
* Refactored on 2010 01 05 to conform to Chisimba coding standards and to
* extract jQuery into its own module.
*
* @category  Chisimba
* @author  Tohir Solomons, Charl Mert
* @package jquery
* @copyright 2007 AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
*   Public License
* @version   $Id: jquery_class_inc.php 16059 2009-12-26 17:44:38Z dkeats $
* @link      http://avoir.uwc.ac.za
*/
class jquery extends object
{

    /**
    * jQuery Version to load
    *
    * Developers can load any version depending on the requirements for the
    * specific module. This will ensure that modules tested on a certain
    * version of jQuery won't have to be re-tested to work with subsequent
    * releases of jQuery. This will ensure protection against if jQueries
    * backwards compatibility fails for any reason.
    *
    * @var string $version
     * 
    */
    public $version;


    /**
    *
    * Constructor for the jQuery class
    *
    * @access public
    * @return VOID
    *
    */
    public function init()
    { 
        //Default jQuery version (Global)
        $this->version = '1.5.2';
    }

    /**
    *
    * Method to set the version of jQuery to load
    *
    * @param string $version The version of jQuery to load from the
    * resorces directory.
    * @access public
    * @return VOID
    *
    */
    public function setVersion($version)
    {
        $this->version = $version;
    }


    /**
    *
    * Method to load the JQuery JavaScript. It depends on $this->version
    * having been set.
    *
    * @return string JQuery JavaScript
    * @access public
    *
    */
    public function show()
    {
        // Make the version generalisable
        $packAr = array("1.2.2", "1.2.3");
        if (in_array($this->version, $packAr)) {
            $minType = "pack";
        } else {
            $minType = "min";
        }
        $jQueryCode = $this->version
           . "/jquery-" . $this->version
           . ".$minType.js";
        $returnStr = $this->getJavascriptFile($jQueryCode,'jquery')
          ."\n";
        // Make it so that there is no prototype conflict. But you cannot use
        //   $() syntax in scripts.
        $returnStr .= '<script type="text/javascript">jQuery.noConflict();</script>'
          ."\n\n";
        return $returnStr;
    }
    
    /**
     * Method to load the liveQuery plugin script files to the header
     */
    public function loadLiveQueryPlugin()
    {
        $this->appendArrayVar('headerParams',
          $this->getJavascriptFile('plugins/livequery/1.0.2/jquery.livequery.js',
          'jquery'));
    }
    
    /**
     * Method to load the form plugin script files to the header
     */
    public function loadFormPlugin()
    {
        $this->appendArrayVar('headerParams',
          $this->getJavascriptFile('plugins/form/2.12/jquery.form.js',
          'jquery'));
    }
    
    /**
     * Method to load the Image Fit plugin script files to the header
     */
    public function loadImageFitPlugin()
    {
        $this->appendArrayVar('headerParams',
          $this->getJavascriptFile('plugins/imagefit/0.2/jquery.imagefit_0.2.js',
          'jquery'));
    }


    /**
     * Method to load the Image Fit plugin script files to the header
     */
    public function loadEasingPlugin($version = '1.3')
    {
        switch ($this->version){
            case '1.2':
                $this->appendArrayVar('headerParams',
                  $this->getJavascriptFile('plugins/easing/1.2/jquery.easing.1.2.js',
                  'jquery'));
                break;
            case '1.3':
                $this->appendArrayVar('headerParams',
                  $this->getJavascriptFile('plugins/easing/1.3/jquery.easing.1.3.js',
                  'jquery'));
            break;
            default:
                $this->appendArrayVar('headerParams',
                  $this->getJavascriptFile('plugins/easing/1.3/jquery.easing.1.3.js',
                  'jquery'));
            break;
        }
    }

    /**
     * Method to load the ddmenu plugin script files to the header
     * TODO: This menu conflicts with prototype, resolve the conflict
     */
    public function loadDDMenuPlugin()
    {
        $basePath = 'plugins/ddmenu/0.3/';
        $this->appendArrayVar('headerParams',
                '<link rel="stylesheet" type="text/css" href="'
                . $this->getResourceUri($basePath . 'ddmenu.css' . '">'));
        $this->loadEasingPlugin('1.3');
        $this->appendArrayVar('headerParams',
          $this->getJavascriptFile($basePath.'jquery.bdc.ddmenu.pack.js',
          'jquery'));
    }


    /**
     * Method to load the accordion plugin script files to the header
     */
    public function loadAccordionMenuPlugin($version = '1.3')
    {
        $basePath = 'plugins/accordion/1.3/';
        $this->appendArrayVar('headerParams', 
          '<link rel="stylesheet" type="text/css" href="'
          .$this->getResourceUri($basePath.'default.css'.'">'));
        $this->appendArrayVar('headerParams',
          $this->getJavascriptFile($basePath.'accordion.1.3.js',
          'jquery'));
    }

    /**
     * Method to load the superfish menu plugin script files to the header
     * TODO: Add superfish php menu class to wrap it
     */
    public function loadSuperFishMenuPlugin($version = '1.4.8')
    {
        $basePath = 'plugins/superfish/1.4.8/';
        $this->appendArrayVar('headerParams',
          '<link rel="stylesheet" type="text/css" href="'
          .$this->getResourceUri ($basePath . 'superfish.css' . '">'));
        $this->appendArrayVar('headerParams',
          '<link rel="stylesheet" type="text/css" href="'
          .  $this->getResourceUri($basePath . 'superfish-vertical.css'
          . '">'));
        $this->appendArrayVar('headerParams', 
          $this->getJavascriptFile($basePath . 'hoverIntent.js',
          'jquery'));
        $this->appendArrayVar('headerParams', 
          $this->getJavascriptFile($basePath . 'jquery.bgiframe.min.js',
          'jquery'));
        $this->appendArrayVar('headerParams', 
          $this->getJavascriptFile($basePath . 'superfish.js',
          'jquery'));
        $this->appendArrayVar('headerParams', 
          $this->getJavascriptFile($basePath . 'supersubs.js',
          'jquery'));
    }


    /**
     * Method to load the flexgrid
     * - The flexgrid plugin draws a sortable by column click/multi selectable/configurable column display table
     * Input : An HTML table
     * OR
     * Input : A Json object representing the table structure
     *
     * Output : Local Spreadsheet-like Grid View
     * Depricated: use jqGrid instead
     */
    public function loadFlexgridPlugin($version = '')
    {
        die("<H1>THIS CODE USES THE loadFlexgridPlugin which is deprecated</h1>");
    }

    /**
     * Method to load the jQuery Core API UI Tabbing library
     * 
     */
    public function loadUITabbing($version = '')
    {
        $basePath = 'api/';
        $this->appendArrayVar('headerParams',
          '<link rel="stylesheet" type="text/css" href="'
          . $this->getResourceUri($basePath
          .'ui/css/flora.all.css'.'">'));
        $this->appendArrayVar('headerParams',
          $this->getJavascriptFile($basePath
          . 'ui/ui.core.js'.'">',
          'jquery'));
        $this->appendArrayVar('headerParams',
          $this->getJavascriptFile($basePath
          .'ui/ui.tabs.js'.'">', 'jquery'));
    }

    /**
     * Method to load the Simple Tree Menu
     * - The simple tree menu creates an explorer like tree menu that can load child elements on ajax requests
     */
    public function loadSimpleTreePlugin($version = '0.3', $theme = 'default')
    {
        $basePath = 'plugins/simpletree/'.$version.'/';
        $this->appendArrayVar('headerParams', 
          $this->getJavascriptFile($basePath . 'jquery.simple.tree.js'));
        $this->appendArrayVar('headerParams', 
          '<link rel="stylesheet" type="text/css" href="'
          .$this->getResourceUri($basePath.'style.css'.'">'));
    }

    /**
     * Method to load the jqGrid plugin.
     * - The jqgrid plugin is a javascript grid plugin with more features than flexgrid e.g. editable grid cells.
     * Input : An HTML table, XML, JSON
     * Output : Spreadsheet-like Grid Control
     * @var string $version
     * @var string $theme This theme relates to the directory name. Can be found here : core_modules/htmlelements/resources/jquery/plugins/jqgrid/3.2.4/themes/[$theme]
     */
    public function loadJqGridPlugin($version = '3.2.4', $theme = 'basic')
    {

        $basePath = 'plugins/jqgrid/3.2.4/';
        $this->appendArrayVar('headerParams', 
          '<link rel="stylesheet" type="text/css" href="'
          .$this->getResourceUri($basePath.'themes/'.$theme.'/grid.css'.'">'));
        //The supplied js includer breaks:
        //$this->appendArrayVar('headerParams',
        // $this->getJavascriptFile($basePath.'jquery.jqGrid.js',
        // 'jquery'));
        //Manually including the minified files here
        
        //Made Changes to the loader, gave it a facebook smack, Baa!
        //$this->appendArrayVar('headerParams',
        //  $this->getJavascriptFile($basePath.'js/min/grid.base-min.js',
        //  'jquery'));
        $this->appendArrayVar('headerParams', 
          $this->getJavascriptFile($basePath.'js/grid.base.js', 'jquery'));

        //Edited delGridRow Behaviour to exclude modal window (using jquery box plugin instead: faster)
        //$this->appendArrayVar('headerParams', $this->getJavascriptFile($basePath.'js/min/grid.formedit-min.js', 'jquery'));
        $this->appendArrayVar('headerParams',
           $this->getJavascriptFile($basePath.'js/grid.formedit.js',
           'jquery'));
        
        $this->appendArrayVar('headerParams', 
          $this->getJavascriptFile($basePath.'js/min/grid.inlinedit-min.js',
          'jquery'));
        $this->appendArrayVar('headerParams', 
          $this->getJavascriptFile($basePath . 'js/min/grid.subgrid-min.js',
          'jquery'));
        $this->appendArrayVar('headerParams', 
          $this->getJavascriptFile($basePath . 'js/min/grid.custom-min.js',
          'jquery'));
        $this->appendArrayVar('headerParams',
          $this->getJavascriptFile($basePath.'js/min/grid.postext-min.js',
          'jquery'));
        $this->appendArrayVar('headerParams',
          $this->getJavascriptFile($basePath.'js/jqDnR.js',
          'jquery'));
        $this->appendArrayVar('headerParams',
          $this->getJavascriptFile($basePath.'js/jqModal.js',
          'jquery'));
    }

    /**
     * Method to load the boxy plugin
     * Allows for the creation of facebook style pop up windows/forms
     */
    public function loadBoxPlugin($version = '0.1.3', $theme = 'default')
    {
        $basePath = 'plugins/boxy/'.$version.'/';
        $this->appendArrayVar('headerParams',
           $this->getJavascriptFile($basePath.'javascripts/jquery.boxy.js'));
        $this->appendArrayVar('headerParams', 
          '<link rel="stylesheet" type="text/css" href="' .
          $this->getResourceUri($basePath.'stylesheets/'.$theme.'/boxy.css'.'">'));
    }

    /**
     * Method to load the facebox plugin
     * Allows for the creation of facebook style notification windows/forms
     */
    public function loadFaceboxPlugin($version = '1.2', $theme = 'default')
    {
        $basePath = 'plugins/facebox/'.$version.'/';
        $this->appendArrayVar('headerParams',
          $this->getJavascriptFile($basePath.'facebox.js'));
        $this->appendArrayVar('headerParams',
          '<link rel="stylesheet" type="text/css" href="'
         . $this->getResourceUri($basePath . 'themes/' . $theme
         . '/facebox.css' .'">'));
    }

    /**
     * Method to load the pngFix that enables png transparency in IE 5.5 & 6
     * Simply load the plugin and voila, all pngs maintain transparency
     */
    public function loadPngFixPlugin($version = '1.1')
    {
        $basePath = 'plugins/pngfix/'.$version.'/';
        $this->appendArrayVar('headerParams',
          $this->getJavascriptFile($basePath
          . 'jquery.pngFix.js'));
        //Activating the plugin
        $script = '
        <script type="text/javascript">
            jQuery(document).ready(function(){ 
                jQuery(document).pngFix();
            });
        </script>';
        $this->appendArrayVar('headerParams', $script);
    }

   /**
     * Method to load the jframe plugin
     *
     * This plugin enables any div with a 'src' <div src=""> attribute
     * to behave as if it where an <iframe src=""> loading the content 
     * via ajax.
     * 
     * Simply load the plugin and viola, all div's with src become ajax frames
     */
    public function loadJFramePlugin($version = '1.131')
    {
        $basePath = 'plugins/jframe/' 
          . $version . '/';
        $this->appendArrayVar('headerParams',
          $this->getJavascriptFile($basePath
          . 'jquery.jframe.js'));
    }

	
   /**
     * Method to load the fg menu plugin
     *
     * This plugin creates a pre released version of the jQuery ipod style menu
	 * created by the filament group
     * 
     */
    public function loadFgMenuPlugin($version = '3.0', $theme = 'start')
    {
        $basePath = 'plugins/fgmenu/'.$version.'/';
        $this->appendArrayVar('headerParams',
          $this->getJavascriptFile($basePath.'fg.menu.js'));
        $this->appendArrayVar('headerParams',
          '<link rel="stylesheet" type="text/css" href="'
          . $this->getResourceUri($basePath
          . 'fg.menu.css') .'">');
        $this->appendArrayVar('headerParams',
          '<link rel="stylesheet" type="text/css" href="'
          .$this->getResourceUri($basePath . 'theme/' . $theme.'/ui.all.css')
          .'">');
    }

    /**
     * Method to load the cluetip plugin
     * This plugin makes it easy to produce highly customizable tooltips.
     *
     * View Demo : http://plugins.learningjquery.com/cluetip/demo/
     */
    public function loadCluetipPlugin($version = '0.9.9')
    {
        $basePath = 'plugins/cluetip/'.$version . '/';
        $this->appendArrayVar('headerParams', 
          '<link rel="stylesheet" type="text/css" href="'
          .$this->getResourceUri($basePath . 'jquery.cluetip.css'
          . '">'));
        $this->appendArrayVar('headerParams', 
          $this->getJavascriptFile($basePath.'lib/jquery.hoverIntent.js',
          'jquery'));
        $this->appendArrayVar('headerParams', 
          $this->getJavascriptFile($basePath . 'lib/jquery.bgiframe.min.js',
          'jquery'));
        $this->appendArrayVar('headerParams', 
          $this->getJavascriptFile($basePath . 'jquery.cluetip.js',
          'jquery'));
    }

    /**
     * Method to load the corner plugin
     * This plugin creates round/beveled and just about any type of corner.
	 *
	 * View Demo : http://www.malsup.com/jquery/corner/
     */
    public function loadCornerPlugin($version = '1.7')
    {
        $basePath = 'plugins/corner/' . $version . '/';
        $this->appendArrayVar('headerParams', 
          $this->getJavascriptFile($basePath . 'jquery.corner.js',
          'jquery'));
    }
    
    /**
     * Method to load the tablesorter plugin, which allows sorting of tables
     * by their <thead> elements
     */
    public function loadTablesorterPlugin() {
        $basePath = 'plugins/tablesorter/';
        $this->appendArrayVar('headerParams',
            $this->getJavascriptFile($basePath . 'jquery.tablesorter.min.js'));
    }

}

?>