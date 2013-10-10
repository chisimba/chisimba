<?php
/*
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
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * contains util methods for managing themes
 *
 * @author davidwaf
 */
class thememanager extends object {

    public $objDbThemes;
    public $objDbUmbrellaThemes;
    private $objLanguage;

    function init() {
        $this->objDbThemes = $this->getObject('dbthemes', 'oer');
        $this->objDbUmbrellaThemes = $this->getObject('dbumbrellathemes', 'oer');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objDBThemeManager = $this->getObject('dbumbrellathemes', 'oer');

        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('fieldset', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('hiddeninput', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('radio', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->addJS();
        $this->setupLanguageItems();
    }

    /**
     * Add required js files
     */
    function addJS() {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('plugins/validate/jquery.validate.min.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('themes.js', 'oer'));
    }

    /**
     * Set up language items
     */
    function setupLanguageItems() {
        // Serialize language items to Javascript
        $arrayVars['status_success'] = "mod_oer_status_success";
        $arrayVars['status_fail'] = "mod_oer_status_fail";
        $objSerialize = $this->getObject('serializevars', 'utilities');
        $objSerialize->languagetojs($arrayVars, 'oer');
    }

    /**
     * Saves a new umbtrella theme 
     * @return string
     */
    function addNewUmbrellaTheme() {
        $title = $this->getParam("title");
        $id = $this->objDbUmbrellaThemes->addUmbrellaTheme($title);
        // Note we are not returning a template as this is an AJAX save.
        if ($id !== NULL && $id !== FALSE) {
            die($id);
        } else {
            die("ERROR_DATA_INSERT_FAIL");
        }
    }

    /**
     * Saves a new theme into a db. A theme belongs to an umbrella theme
     */
    function addNewTheme() {
        $title = $this->getParam('title');
        $umbrellaTheme = $this->getParam('umbrellatheme');
        $id = $this->objDbThemes->addTheme($title, $umbrellaTheme);
        if ($id !== NULL && $id !== FALSE) {
            die($id);
        } else {
            die("ERROR_DATA_INSERT_FAIL");
        }
    }

    /**
     * Creates a table listing of current themes
     * @return string
     */
    function createThemeListingTable() {

        $header = new htmlheading();
        $header->type = 2;
        $header->str = $this->objLanguage->languageText('mod_oer_productthemes', 'oer');

        $cp = '';
        $button = new button('newumbrellatheme', $this->objLanguage->languageText('mod_oer_createumbrellatheme', 'oer'));
        $uri = $this->uri(array("action" => "newumbrellatheme"));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $cp.=$button->show() . '&nbsp';

        $button = new button('newtheme', $this->objLanguage->languageText('mod_oer_createtheme', 'oer'));
        $uri = $this->uri(array("action" => "newtheme"));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $cp.=$button->show() . '&nbsp';

        $button = new button('back', $this->objLanguage->languageText('word_back', 'system'));
        $uri = $this->uri(array());
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $cp.=$button->show() . '&nbsp';

        $objTable = $this->getObject('htmltable', 'htmlelements');
        $objTable->startHeaderRow();
        $objTable->addHeaderCell($this->objLanguage->languageText('mod_oer_count', 'oer'), "10%");
        $objTable->addHeaderCell(ucfirst($this->objLanguage->languageText('mod_oer_title', 'oer')), "45%");
        $objTable->addHeaderCell(ucfirst($this->objLanguage->languageText('mod_oer_umbrellatheme', 'oer')), "45%");
        $objTable->endHeaderRow();


        $themes = $this->objDbThemes->getThemes();
        $count = 1;
        foreach ($themes as $theme) {
            $objTable->startRow();
            $objTable->addCell($count, "20%");
            $objTable->addCell($theme['theme'], "45%");
            $objTable->addCell($theme['umbrellatheme'], "45%");

            $objTable->endRow();

            $count++;
        }
        return $header->show() . $cp . '<br/>' . $objTable->show();
    }

    /**
     * Function creates form for editing themes
     *
     * @return type 
     */
    function createAddEditThemeForm() {
        $objTable = $this->getObject('htmltable', 'htmlelements');

        $header = new htmlheading();
        $header->type = 2;
        $header->str = $this->objLanguage->languageText('mod_oer_createtheme', 'oer');



        //the title
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_title', 'oer'));
        $objTable->endRow();

        $objTable->startRow();

        $textinput = new textinput('title');
        $textinput->size = 60;
        $textinput->cssClass = "required";
        $objTable->addCell($textinput->show());
        $objTable->endRow();


        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_umbrellatheme', 'oer'));
        $objTable->endRow();

        $objDBThemeManager = $this->getObject('dbumbrellathemes', 'oer');

        $objTable->startRow();
        $umbrellaThemeDropDown = new dropdown('umbrellatheme');
        $umbrellaThemeDropDown->cssClass = "required";
        $objDBUmbrellaThemes = $this->getObject('dbumbrellathemes', 'oer');
        $umbrellaThemes = $objDBUmbrellaThemes->getUmbrellaThemes();

        foreach ($umbrellaThemes as $umbrellaTheme) {
            $umbrellaThemeDropDown->addOption($umbrellaTheme['id'], $umbrellaTheme['theme']);
        }

        $objTable->addCell($umbrellaThemeDropDown->show());
        $objTable->endRow();


        // Table for the buttons
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cssClass = "buttonHolder";

        $table->startRow();
        $table->addCell("&nbsp;");
        $buttonTitle = $this->objLanguage->languageText('word_save');
        $button = new button('submitTheme', $buttonTitle);
        $button->setToSubmit();
        $button->cssId = "submitTheme";
        $table->addCell($button->show());


        $button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
        $uri = $this->uri(array("action" => "viewthemes"));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $button->cssId = "cancelUmbrellaTheme";

        $table->addCell("&nbsp;");
        $table->addCell($button->show());
        $table->endRow();

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_oer_productthemes', 'oer'));
        $fieldset->addContent($objTable->show());

        // Insert a message area for Ajax result to display.
        $msgArea = "<br /><div id='save_results' class='ajax_results'></div>";

        // Add hidden fields for use by JS
        $hiddenFields = "\n\n";
        $hidMode = new hiddeninput('mode');
        $hidMode->cssId = "mode";
        $hidMode->value = $this->mode;
        $hiddenFields .= $hidMode->show() . "\n";
        $hidId = new hiddeninput('id');
        $hidId->cssId = "id";
        $hidId->value = $this->getParam('id', NULL);
        $hiddenFields .= $hidId->show() . "\n\n";


        $newThemeForm = new form('themesForm', NULL);
        $newThemeForm->addToForm($header->show());
        $newThemeForm->addToForm(
                $fieldset->show()
                . $table->show()
                . $hiddenFields
                . $msgArea
        );
        return $newThemeForm->show();
    }

    /**
     * Function creates a list of existing themes
     *
     * @return string
     */
    function createThemeListing() {
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $header = new htmlheading();
        $header->type = 4;
        $header->str = $this->objLanguage->languageText('mod_oer_umbrellathemes', 'oer');
        $themes = $this->objDBThemeManager->getUmbrellaThemes();
        $content = '<div id="umbrellatheme">';
        $content.='<ul id="umbrellatheme_ul">';
        foreach ($themes as $theme) {

            $editlink = new link($this->uri(array("action" => "editumbrellatheme", "id" => $theme['id'])));
            $objIcon->setIcon('edit');
            $editlink->link = $objIcon->show();

            $deletelink = new link($this->uri(array("action" => "confirmdeletetheme", "id" => $theme['id'])));
            $objIcon->setIcon('delete');
            $deletelink->link = $objIcon->show();

            $content.='<li id="umbrellatheme_li">' . $theme['theme'] . $editlink->show() . '&nbsp;' . $deletelink->show() . '</li>';
        }
        $content.='</ul>';
        $content.="</div>";
        return $header->show() . $content;
    }

    /**
     * Builds a form used to creating umbrella theme
     * 
     * @return string
     */
    function createAddEditUmbrellaThemeForm() {
        $objTable = $this->getObject('htmltable', 'htmlelements');

        $header = new htmlheading();
        $header->type = 2;
        $header->str = $this->objLanguage->languageText('mod_oer_createumbrellatheme', 'oer');



        //the title
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_oer_title', 'oer'));
        $objTable->endRow();

        $objTable->startRow();

        $textinput = new textinput('title');
        $textinput->size = 60;
        $textinput->cssClass = 'required';
        $objTable->addCell($textinput->show());
        $objTable->endRow();

        $fieldset = $this->newObject('fieldset', 'htmlelements');
        $fieldset->setLegend($this->objLanguage->languageText('mod_oer_productthemes', 'oer'));
        $fieldset->addContent($objTable->show());



        // Table for the buttons
        $table = $this->newObject('htmltable', 'htmlelements');
        $table->cssClass = "buttonHolder";

        $table->startRow();
        $table->addCell("&nbsp;");
        $buttonTitle = $this->objLanguage->languageText('word_save');
        $button = new button('submitUmbrellaTheme', $buttonTitle);
        $button->setToSubmit();
        $button->cssId = "submitUmbrellaTheme";
        $table->addCell($button->show());


        $button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
        $uri = $this->uri(array("action" => "viewthemes"));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $button->cssId = "cancelUmbrellaTheme";

        $table->addCell("&nbsp;");
        $table->addCell($button->show());
        $table->endRow();
        // Insert a message area for Ajax result to display.
        $msgArea = "<br /><div id='save_results' class='ajax_results'></div>";

        // Add hidden fields for use by JS
        $hiddenFields = "\n\n";
        $hidMode = new hiddeninput('mode');
        $hidMode->cssId = "mode";
        $hidMode->value = $this->mode;
        $hiddenFields .= $hidMode->show() . "\n";
        $hidId = new hiddeninput('id');
        $hidId->cssId = "id";
        $hidId->value = $this->getParam('id', NULL);
        $hiddenFields .= $hidId->show() . "\n\n";


        $newUmbrellaThemeForm = new form('umbrellaThemesForm', NULL);
        $newUmbrellaThemeForm->addToForm($header->show());
        $newUmbrellaThemeForm->addToForm(
                $fieldset->show()
                . $table->show()
                . $hiddenFields
                . $msgArea
        );
        return $newUmbrellaThemeForm->show();
    }

}

?>
