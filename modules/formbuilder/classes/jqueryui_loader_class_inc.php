<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/* !  \class jqueryui_loader
 *
 *  \brief This class includes the javascript library files and css style files for jquery ui 
 *  \brief This class reads settings from a xml config file and depending on the settings
 *  includes the right css style. 
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    March 1, 2012
 */
class jqueryui_loader extends object {

    private $jquery_lib;
    private $jqueryui_lib;
    private $xml_style_settings;
    private $CSS_type;
    private $css_head;
    private $pathToMainStyleSettings;

    public function init() {
        $this->jquery_lib = "<script language='JavaScript' src='" . $this->getResourceUri('js/jqueryui/lib/01.jquery.js', 'formbuilder') . "' type='text/javascript'></script>";
        $this->jqueryui_lib = "<script language='JavaScript' src='" . $this->getResourceUri('js/jqueryui/lib/02.jqueryui.js', 'formbuilder') . "' type='text/javascript'></script>";
        $path_to_style_settings = $this->getResourceUri('js/jqueryui/settings/style_settings.xml', 'formbuilder');
        
         $objAltConfig = $this->getObject('altconfig','config');
            $siteRoot=$objAltConfig->getsiteRootPath();
            $this->pathToMainStyleSettings=$siteRoot.'config/formbuilder_style_settings.xml';

        if (!file_exists($this->pathToMainStyleSettings)) {
        $path_to_style_settings = $this->getResourceUri('js/jqueryui/settings/style_settings.xml', 'formbuilder');
        $this->xml_style_settings = simplexml_load_file($path_to_style_settings) or die("Error: Cannot load jquery ui XML style settings file.");    
        $this->xml_style_settings->asXML($this->pathToMainStyleSettings);
        $this->xml_style_settings = simplexml_load_file($this->pathToMainStyleSettings) or die("Error: Cannot load jquery ui XML style settings file.");
        
        } else {
         $this->xml_style_settings = simplexml_load_file($this->pathToMainStyleSettings) or die("Error: Cannot load jquery ui XML style settings file.");   
        }
        
//        $this->xml_style_settings = simplexml_load_file($path_to_style_settings) or die("Error: Cannot load jquery ui XML style settings file.");

        $this->getSelectedStyle();
        $this->setStyle();
    }

    public function includeJqueyUI() {
        return $this->jquery_lib . $this->jqueryui_lib . $this->css_head;
    }

    /**
     * \brief This function returns the selected theme.
     */
    private function getSelectedStyle() {
        foreach ($this->xml_style_settings->children() as $themes) {
            foreach ($themes->children() as $theme => $data) {
                $this->CSS_type = $data->name;
            }
        }
    }
    
    /**
     *This mehtod allows you to view a theme tmeporarily
     * @param type $selectedTheme
     * @return boolean 
     */
        public function viewTheme($selectedTheme) {
        if (is_dir($this->getResourceUri("js/jqueryui/styles/$selectedTheme/"))) {

            $cm = $this->fetchFileNames($this->getResourceUri("js/jqueryui/styles/$selectedTheme/", 'formbuilder'), "css");
            if (count($cm) < 1) {
                return false;
            }
            $temp_css_head = "";
            foreach ($cm as $includeCssFile) {
                $temp_css_head .= "<link rel='stylesheet' type='text/css' href='" . $this->getResourceUri("js/jqueryui/styles/$selectedTheme/$includeCssFile") . "' />";
            }
            print_r("<head>".$temp_css_head."</head>");
//            $this->appendArrayVar('headerParams', $temp_css_head);
            return true;
        }
        return false;
    }

    /**
     * \brief This function sets the slected theme.
     */
    private function setStyle() {
        $cm = $this->fetchFileNames($this->getResourceUri("js/jqueryui/styles/$this->CSS_type/", 'formbuilder'), "css");
        if (count($cm) < 1){
            die("The style <i>".$this->CSS_type."</i> does not exist within jquery UI formbuilder module. Please correct the style settings within the xml config file wthin this module.");
    }
    
        foreach ($cm as $includeCssFile) {
            $this->css_head .= "<link rel='stylesheet' type='text/css' href='" . $this->getResourceUri("js/jqueryui/styles/$this->CSS_type/$includeCssFile") . "' />";
        }
    }

    /**
     * This gets all the files of a specific extension within a specific folder
     * @param type $path
     * @param type $fileType
     * @return type An array with the files
     */
    private function fetchFileNames($path, $fileType) {
        $filesArray = array();
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && (preg_match("/.$fileType/i", $file) == 1) && (substr($file, 0, 1) != "_")) {
                    $filesArray[] = $file;
                }
            }
            closedir($handle);
        }
        sort($filesArray);
        return $filesArray;
    }
}

?>
