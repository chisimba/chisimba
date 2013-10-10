<?php

/**
 *
 * skincatalogue Test module
 *
 * A test module for customizing testskin1
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place ­ Suite 330, Boston, MA  02111­1307, USA.
 *
 * @version     0.081
 * @package    skincatalogue
 * @author     Monwabisi Sifumba <wsifumba@gmail.com>
 * @copyright  2010 AVOIR
 * @license    http://www.gnu.org/licenses/gpl­2.0.txt The GNU General Public  
  License
 * @link       http://www.chisimba.com
 * 
 */
// security check ­ must be included in all scripts
if (!
        /**
         * Description for $GLOBALS
         * @global unknown $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// end security check
/*
 * The EditForm class
 */
class catalogueform extends object {

    /**
     * The images directory
     *
     * @access public
     * @var String
     */
    public $imageDir;

    /**
     * The XML object containing the values to be edited and used by the skin
     *
     * @access public
     * @var object
     */
    public $valuesDir;

    /**
     * The cache object
     *
     * @access public
     * @var object
     */
    public $objCache;

    /**
     * The skins directory
     *
     * @access public
     * @var string
     */
    public $skinRoot;

    /**
     * The name of the skin to be edited or to switch to
     *
     * @access public
     * @var string
     */
    public $SkinName;

    /**
     * The form object
     *
     * @access public
     * @var object The EditForm object to be returned based on the selected action
     */
    public $Form;

    /**
     * @access private
     * @var object
     */
    public $objConfig;

    public function init() {
        
    }

    public function BuildForm() {
        $this->objLanguage = $this->getObject("language", "language");
        $this->objConfig = $this->getObject("altconfig", "config");
        // Get the location of the skin root directory.
        $this->skinRoot = $this->objConfig->getSiteRootPath() . $this->objConfig->getskinRoot();
        // Load the cache object to cache the skin selector.
        $this->objCache = $this->getObject('cacheops', 'cache');
        $this->SkinName = $this->getParam("currentskin");
        $this->loadClass("form", "htmlelements");
        $this->loadClass("textinput", "htmlelements");
        $this->loadClass("button", "htmlelements");
        $this->loadClass("htmltable", "htmlelements");
        $this->loadClass("link", "htmlelements");
        $this->loadClass("label", "htmlelements");
        $this->loadClass("image", "htmlelements");
        $this->loadClass("colorpicker", "htmlelements");
        //$this->imageDir = $this->objConfig->getModulePath() . "skincatalogue/resources/images/";
        $this->valuesDir = $this->objConfig->getSiteRootPath() . $this->objConfig->getSkinRoot() . $this->SkinName . "/values.xml";
    }

    /**
     * Method to be returned when action selected is edit
     *
     * @access public
     * @return object The EditForm object
     * @param object The form object with the controls to configure the skin
     */
    public function __edit() {
        //create form elements and objects to be used
        //the form
        $this->Form = new form($this->objLanguage->languageText("mod_customizeform_name", "skincatalogue"), $this->uri(array("action" => "edit", "currentskin" => $this->SkinName)));
        //the label for input controls
        $valueLabel = new label($this->objLanguage->languageText("mod_inputLabel_value", "skincatalogue"));
        //the form elements will be contained within a table
        $table = new htmlTable();
        //the link to enable swithing between edit mode and view mode
        $ActionLink = new link($this->uri(array("action" => "view")));
        //the input control for setting border radius
        $borderSizeInput = new textinput($this->objLanguage->languageText("mod_borderSizeInput_name", "skincatalogue"));
        //the input control for setting navigation bar font size
        $NavFontSizeinput = new textinput($this->objLanguage->languageText("mod_navFontSizeinput_name", "skincatalogue"));
        //the input control for setting the body font size
        $FontSizeInput = new textinput($this->objLanguage->languageText("mod_fontSizeInput_name", "skincatalogue"));
        //the input control for setting the background image
        $BackgroundImageinput = new textinput($this->objLanguage->languageText("mod_backgroundimage_name", "skincatalogue"));
        //the form's save button
        $btnSave = new button($this->objLanguage->languageText("mod_savebtn_name", "skincatalogue"));
        //the input control for setting the border radius
        $bdrRadiusInput = new textinput($this->objLanguage->languageText("mod_bdrRadiusInput_name", "skincatalogue"));
        $ActionLink->link = "Skin Catalogue";
        $SkinColorInput = $this->getObject("colorpicker", "htmlelements");
        $btnSave->setValue($this->objLanguage->languageText("mod_savebtn_value", "skincatalogue"));
        $table->cssClass = $this->objLanguage->languageText("mod_cssclass_name", "skincatalogue");
        $btnSave->setToSubmit();
        $SkinColorInput->name = $this->objLanguage->languageText("mod_skincolorinput_name", "skincatalogue");
        $FontSizeInput->extra = "maxlength=2";
        $bdrRadiusInput->extra = "maxlength=2";
        $borderSizeInput->extra = "maxlength=2";
        $NavFontSizeinput->extra = "maxlength=2";
        //adding the link at the top of the form
        $this->Form->addToForm($ActionLink->show() . "</p>");
        //store all editable skin elements here
        $ArrFormElements = array(
            "background" => "<div class=featurebox ><h5 class=featureboxheader >Skin color</h5>" . $valueLabel->show() . $SkinColorInput->show() . "</div>",
            "border_size" => "<div class=featurebox ><h5 class=featureboxheader >Border size</h5>" . $valueLabel->show() . $borderSizeInput->show() . "</div>",
            "border_radius" => "<div class=featurebox ><h5 class=featureboxheader >Border Radius</h5>" . $valueLabel->show() . $bdrRadiusInput->show() . "</div>",
            "font_size" => "<div class=featurebox ><h5 class=featureboxheader >Body font size</h5>" . $valueLabel->show() . $FontSizeInput->show() . "</div>",
            "navigation_fontsize" => "<div class=featurebox ><h5 class=featureboxheader >Navigation bar font size</h5>" . $valueLabel->show() . $NavFontSizeinput->show() . "</div>",
            "background_image" => "<div class=featurebox ><h5 class=featureboxheader >Background image</h5>" . $valueLabel->show() . $BackgroundImageinput->show() . "</div>"
        );
        if (file_exists(strtolower($this->valuesDir))) {
            $ObjFileContents = simplexml_load_file($this->valuesDir);
            foreach ($ObjFileContents as $key => $value) {
                $table->startRow();
                $table->addCell($ArrFormElements[$key]);
                $table->endRow();
            }
            if (isset($_REQUEST['btnSave'])) {
                $ValuesFile = fopen($this->valuesDir, "w+");
                foreach ($ObjFileContents as $key => $value) {
                    if (isset($_REQUEST["txt" . $key])) {
                        if (!empty($_REQUEST['txt' . $key])) {
                            $ObjFileContents->$key = $_REQUEST["txt" . $key];
                        }
                    }
                }
                //write the changed values to the file
                fwrite($ValuesFile, $ObjFileContents->asXML());
            }
            $this->Form->addToForm($table->show() . "<br />");
            $this->Form->addToForm($btnSave->show());
            return $this->Form->show();
        } else {
            $this->Form->addToForm("<h1 class='error' >" . $this->objLanguage->languageText("mod_editerror_message", "skincatalogue") . "</h1>");
            return $this->Form->show();
        }
    }

    /**
     * Method to be returned when selected action is edit
     *
     * @access public
     * @param void
     * @return object The form containing the list of skins
     */
    public function __view() {
        $this->Form = new form($this->uri(array("action" => "view")));
        $EditLink = new link();
        $objTable = new htmlTable();
        $ApplyLink = new link();
        $EditLink->link = "Edit skin";
        $ApplyLink->link = "Apply Skin";
        // Check if the list of skins has been cached, otherwise regenerate.
        if ($this->objCache->skinlist === FALSE) {
            // Compile an array of the skin names.
            $dirList = array();
            $directories = glob($this->skinRoot . '*', GLOB_ONLYDIR);
            // Loop through the folders and build an array of available skins.
            foreach ($directories as $directory) {
                $key = basename($directory);
                if ($key != "_common" && $key != "_common2") {
                    if (file_exists($directory . '/skin.conf')) {
                        $conf = $this->readConf($directory . '/skin.conf');
                        $dirList[$key] = $conf['SKIN_NAME'];
                    } elseif (file_exists($directory . '/skinname.txt')) {
                        $dirList[$key] = trim(file_get_contents($directory . '/skinname.txt'));
                    } else {
                        $dirList[$key] = $key;
                    }
                }
            }
            // Attempt to cache this data for future use.
            $this->objCache->skinlist = $dirList;
        }
        asort($dirList);
        foreach ($dirList as $key => $value) {
            //get the contents of the skin's JSON file and store them in a resource object to be used by the JSON object
            $RawJSON = file_get_contents($this->objConfig->getSiteRootPath() . $this->objConfig->getSkinRoot() . $key . "/settings.json");
            //create a JSON object to store the skin settings
            $JSONobject = json_decode($RawJSON);
            $ApplyLink->href = $this->uri(array("action" => "view", "currentskin" => "$key"));
            $EditLink->href = $this->uri(array("action" => "edit", "currentskin" => "$key"));
            //this is also a bit clumsy but I was just pushing to get the layout I had in mind
            $objTable->startRow();
            $str = "<h5 class=featureboxheader >$value</h5>Description: " . $JSONobject->description . "<br />Version: " . $JSONobject->skinVersion . "<br />" . $EditLink->show() . "<br />" . $ApplyLink->show()."</p>";
            $objTable->addCell($str);
            $objTable->endRow();
        }
        $this->Form->addToForm($objTable->show());
        /**
         * Is this is accaptable?
         */
        if (!empty($this->SkinName)) {
            $_SESSION['870e3~skin~skin'] = $this->SkinName;
        }
        return $this->Form->show();
    }

}

?>