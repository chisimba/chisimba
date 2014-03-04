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
class style_settings_handler extends object {

    /**
     * \brief Object to be able use the html_builder class functionality.
     */
    private $html_buillder;

    /**
     * \brief Object reading XML theme roller file.
     */
    private $xml_style_settings;

    /**
     * \brief Object writing to XML theme roller file.
     */
    private $xml_style_editor;

    /**
     * \brief Variable string to store chosen theme from theme roller.
     */
    private $chosen_style;
    private $stylesListDropdown;
    private $pathToMainStyleSettings;

    /**
     * \brief Constructor instatiating private data members of the class
     * admin_page_handler.
     */
    public function init() {

        $objAltConfig = $this->getObject('altconfig', 'config');
        $siteRoot = $objAltConfig->getsiteRootPath();
        $this->pathToMainStyleSettings = $siteRoot . 'config/formbuilder_style_settings.xml';

        if (!file_exists($this->pathToMainStyleSettings)) {
            $path_to_style_settings = $this->getResourceUri('js/jqueryui/settings/style_settings.xml', 'formbuilder');
            $this->xml_style_settings = simplexml_load_file($path_to_style_settings) or die("Error: Cannot load jquery ui XML style settings file.");
            $this->xml_style_settings->asXML($this->pathToMainStyleSettings);
            $this->xml_style_settings = simplexml_load_file($this->pathToMainStyleSettings) or die("Error: Cannot load jquery ui XML style settings file.");
        } else {
            $this->xml_style_settings = simplexml_load_file($this->pathToMainStyleSettings) or die("Error: Cannot load jquery ui XML style settings file.");
        }


        $this->getCurrentStyle();
        $this->loadXMLFileString();
        $this->loadClass('dropdown', 'htmlelements');

///Instatiate an object from the class dropdown belonging to the module
///htmlelements.

        $this->stylesListDropdown = &new dropdown('stylelist_dropdown');
    }

    /**
     * \brief Private member function reading from the theme roller XML file
     * and gets the current stored theme.
     * \note the file being read is ThemeRoller.xml
     */
    private function getCurrentStyle() {
        foreach ($this->xml_style_settings->children() as $themes) {
            foreach ($themes->children() as $theme => $data) {
                $this->chosen_style = $data->name;
            }
        }
    }

    /**
     * \brief Memeber function that opens up the write handle for the theme
     * roller XML file.
     * \note the file being read is ThemeRoller.xml
     */
    private function loadXMLFileString() {

//        $file = $this->getResourceUri('js/jqueryui/settings/style_settings.xml', 'formbuilder');
        $fp = fopen($this->pathToMainStyleSettings, "rb") or die("Error: Cannot load jquery ui XML style settings file.");
        $str = fread($fp, filesize($this->pathToMainStyleSettings));
        $this->xml_style_editor = new DOMDocument();
        $this->xml_style_editor->formatOutput = true;
        $this->xml_style_editor->preserveWhiteSpace = false;
        $this->xml_style_editor->loadXML($str) or die("Error: Cannot load jquery ui XML style settings file.");
    }

    /**
     * \brief Private member function setting new Theme from the theme roller.
     * \brief It updates the new theme in the XML file.
     * \note the file being read is ThemeRoller.xml
     */
    public function setNewTheme($newTheme) {
/// get document element
        if (is_dir($this->getResourceUri("js/jqueryui/styles/$newTheme/"))) {
//        $settingDirectory = $this->getResourceUri('js/jqueryui/settings/', 'formbuilder');
//        print_r($settingDirectory);
//        chmod("$settingDirectory", 0600);
//        if (is_writable($settingDirectory)){
//            chmod("$settingDirectory", 0600);
//        }

            $root = $this->xml_style_editor->documentElement;
            $fnode = $root->firstChild;

///get a node
            $ori = $fnode->childNodes->item(0);

            $name = $this->xml_style_editor->createElement("name");
            $nameText = $this->xml_style_editor->createTextNode("$newTheme");
            $name->appendChild($nameText);


            $theme = $this->xml_style_editor->createElement("chosenTheme");
            $theme->appendChild($name);
            $fnode->replaceChild($theme, $ori);

            $this->xml_style_editor->save($this->pathToMainStyleSettings);
            "<xmp>NEW:\n" . $this->xml_style_editor->saveXML() . "</xmp>";
            return true;
        }
        return false;
    }

    /**
     * \brief Memeber function building the html content  for the theme viewer.
     * \note the file being read is ThemeRoller.xml
     */
    private function buildThemeViewer() {
        $themeViewer = "<div id='themeViewerContainer'>";
        $themeViewer = "Date: <div id='datepicker'></div>";
        $themeViewer .="<div class='buttonViewer'>
        <button>Button with icon only</button>
        <button>Button with icon on the left</button>
        <button>Button with two icons</button>
        <button>Button with two icons and no text</button></div>";
        $themeViewer .="</div>";
        return $themeViewer;
    }

    /**
     * \brief Memeber function building html content allowing the user to
     * select and set themes.
     * \return built html content for theme roller
     */
    public function buildThemeRoller() {
        $cssThemes = directoryToArray($this->getResourceUri("js/jqueryui/styles/", 'formbuilder'), false);
        foreach ($cssThemes as $key => $style) {
            if ($style != ".svn") {
                $this->stylesListDropdown->addOption($style, $style);
            }
        }

        if (isset($this->chosen_style)) {
            $this->stylesListDropdown->setSelected("$this->chosen_style");
        }
        $themRoller = "<div id='themeRollerContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:20px 20px 20px 20px;'> ";
        $themRoller .= "<h3>Style Selector</h3>";
        $themRoller .= "<label for='input_stylelist_dropdown'>Set a style for formbuilder</label><br>" . $this->stylesListDropdown->show();
        $themRoller .= "&nbsp;&nbsp;&nbsp;<button id='selectTheme'>Set Style</button>";
        $themRoller .= "<div id='themeLoaderContainer'></div>";
        //   $themRoller .= $this->html_buillder->buildButton("selectTheme", "Set Theme", "themeSelector");
        $themRoller .= "<div id='themeViewerContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:20px 20px 20px 20px;'>";
        $themRoller .= "<h3>Theme Viewer</h3>";
        $themRoller .= $this->buildThemeViewer();

        $themRoller .= "</div>";
        $themRoller .= "</div>";
        return $themRoller;
    }

    /**
     * \brief Memeber function that builds custom action links for controller.
     * \note Only one parameter for the action is allowed.
     * \return A built html link to be able to run on the controller.
     */
    private function getActionLinkWithParamter($actionParameter, $lnkText) {
        return '<a href="server_side_communication_interface.php'
                . '?action=' . $actionParameter
                . '">' . $lnkText . '</a>';
    }

}

/**
 * \brief php standard function that loops through directories and gets
 * directory names.
 * \return An array of directory names.
 */
function directoryToArray($directory, $recursive) {
    $array_items = array();
    if ($handle = opendir($directory)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                if (is_dir($directory . "/" . $file)) {
                    if ($recursive) {
                        $array_items = array_merge($array_items, directoryToArray($directory . "/" . $file, $recursive));
                    }
                    $file = $file;
                    $array_items[] = preg_replace("/\/\//si", "/", $file);
                } else {
                    $file = $file;
                    $array_items[] = preg_replace("/\/\//si", "/", $file);
                }
            }
        }
        closedir($handle);
    }
    return $array_items;
}

?>
