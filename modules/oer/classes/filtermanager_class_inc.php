<?php

/**
 * this contains utils for managing filtering products. This filter is used for
 * both original products and adaptations. The filter results are displayed
 * accordingly depending on the action used
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
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @version    0.001
 * @package    oer
 * @author     JCSE
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 *
 * @author davidwaf
 */
class filtermanager extends object {

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->loadClass('link', 'htmlelements');
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
        $this->loadClass('fieldset', 'htmlelements');
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('filterproducts.js', 'oer'));
        $this->setupLanguageItems();
    }

    /**
     * sets up necessary lang items for use in js
     */
    function setupLanguageItems() {
        // Serialize language items to Javascript
        $arrayVars['please_wait'] = "mod_oer_pleasewait";

        $objSerialize = $this->getObject('serializevars', 'utilities');
        $objSerialize->languagetojs($arrayVars, 'oer');
    }

    /**
     * builds a filter products form 
     */
    function buildFilterProductsForm($action, $label, $filterOptions) {

        $options = explode("!", $filterOptions);

        $typeOfProduct = "";
        $objElement = new checkbox('model', $this->objLanguage->languageText('mod_oer_model', 'oer'));
        $typeOfProduct.= $objElement->show() . '<br/>';

        $objElement = new checkbox('guide', $this->objLanguage->languageText('mod_oer_guide', 'oer'));
        $typeOfProduct.= $objElement->show() . '<br/>';

        $objElement = new checkbox('handbook', $this->objLanguage->languageText('mod_oer_handbook', 'oer'));
        $typeOfProduct.= $objElement->show() . '<br/>';

        $objElement = new checkbox('manual', $this->objLanguage->languageText('mod_oer_manual', 'oer'));
        $typeOfProduct.= $objElement->show() . '<br/>';

        $objElement = new checkbox('model', $this->objLanguage->languageText('mod_oer_bestpractice', 'oer'));

        $typeOfProduct.= $objElement->show() . '<br/>';


        $fieldset1 = $this->newObject('fieldset', 'htmlelements');
        $fieldset1->setLegend($this->objLanguage->languageText($label, 'oer'));
        $fieldset1->addContent($typeOfProduct);


        $themes = new dropdown('themes');
        $themes->addOption('all', $this->objLanguage->languageText('word_all', 'system'));
        $dbThemes = $this->getObject("dbthemes", "oer");
        $allThemes = $dbThemes->getThemes();
        foreach ($allThemes as $theme) {
            $themes->addOption($theme['id'], $theme['theme']);
        }

        foreach ($options as $option) {
            $optionArray = explode("=", $option);
            if ($optionArray[0] == 'themes')
                $themes->setSelected($optionArray[1]);
        }
        $themesField = $this->objLanguage->languageText('mod_oer_theme', 'oer') . '<br/>';
        $themesField.=$themes->show() . '<br/><br/>';


        $language = new dropdown('language');
        $language->addOption('all', $this->objLanguage->languageText('word_all', 'system'));
        $language->addOption('en', $this->objLanguage->languageText('mod_oer_english', 'oer'));

        foreach ($options as $option) {
            $optionArray = explode("=", $option);
            if ($optionArray[0] == 'language')
                $language->setSelected($optionArray[1]);
        }

        $languageField = $this->objLanguage->languageText('mod_oer_language', 'oer') . '<br/>';
        $languageField.=$language->show() . '<br/><br/>';

        $dbProducts = $this->getObject("dbproducts", "oer");
        $authors = $dbProducts->getProductAuthors();
        $author = new dropdown('author');
        $author->addOption('all', $this->objLanguage->languageText('word_all', 'system'));
        foreach ($authors as $cauthor) {
            $author->addOption($cauthor['author'], $cauthor['author']);
        }

        foreach ($options as $option) {
            $optionArray = explode("=", $option);
            if ($optionArray[0] == 'author')
                $author->setSelected($optionArray[1]);
        }
        $authorField = $this->objLanguage->languageText('mod_oer_author', 'oer') . '<br/>';
        $authorField.=$author->show() . '<br/><br/>';


        $institutions = new dropdown('institution');
        $institutions->addOption('all', $this->objLanguage->languageText('word_all', 'system'));
        $dbInstitutions = $this->getObject("dbinstitution", "oer");
        $allIntitutions = $dbInstitutions->getAllInstitutions();
        foreach ($allIntitutions as $institution) {

            $institutions->addOption($institution['id'], $institution['name']);
        }

        foreach ($options as $option) {
            $optionArray = explode("=", $option);
            if ($optionArray[0] == 'institution')
                $institutions->setSelected($optionArray[1]);
        }
        $institutionsField = $this->objLanguage->languageText('mod_oer_institutions', 'oer') . '<br/>';
        $institutionsField.=$institutions->show() . '<br/><br/>';

        $regions = new dropdown('region');
        $regions->addOption('all', $this->objLanguage->languageText('word_all', 'system'));
        $dbProducts = $this->getObject("dbproducts", "oer");
        $allRegions = $dbProducts->getProductRegions();
        foreach ($allRegions as $region) {
            if ($region != null) {
                $regions->addOption($region['region'], $region['region']);
            }
        }

        foreach ($options as $option) {
            $optionArray = explode("=", $option);
            if ($optionArray[0] == 'region')
                $regions->setSelected($optionArray[1]);
        }

        $regionsField = $this->objLanguage->languageText('mod_oer_region', 'oer') . '<br/>';
        $regionsField.=$regions->show() . '<br/><br/>';



        $countries = new dropdown('country');
        $countries->addOption('all', $this->objLanguage->languageText('word_all', 'system'));

        //$allCountries = $dbProducts->getProductCountries();

        $languageCode = $this->getObject("languagecode", "language");
        $allCountries = $languageCode->countryListArr();
 
        foreach ($allCountries as $code => $country) {
            $countries->addOption($code, $country);
        }
        foreach ($options as $option) {
            $optionArray = explode("=", $option);
            if ($optionArray[0] == 'country')
                $countries->setSelected($optionArray[1]);
        }

        $countriesField = $this->objLanguage->languageText('mod_oer_country', 'oer') . '<br/>';
        $countriesField.=$countries->show() . '<br/><br/>';

        $itemsPerPage = new dropdown('itemsperpage');
        $itemsPerPage->addOption('15', '15');
        $itemsPerPage->addOption('30', '30');
        $itemsPerPage->addOption('60', '60');
        $itemsPerPage->addOption('120', '120');

        foreach ($options as $option) {
            $optionArray = explode("=", $option);
            if ($optionArray[0] == 'itemsperpage')
                $itemsPerPage->setSelected($optionArray[1]);
        }

        $itemsPerPageField = $this->objLanguage->languageText('mod_oer_itemsperpage', 'oer') . '<br/>';
        $itemsPerPageField.=$itemsPerPage->show() . '<br/><br/>';


        $formData = new form('productfilter', $this->uri(array("action" => $action)));
        $formData->addToForm($themesField . $languageField . $authorField . $institutionsField . $regionsField . $countriesField . $itemsPerPageField);
        $formData->addToForm('<br/><div class="pleasewait" id="save_results"></div>');
        $button = new button('searchProductButton', $this->objLanguage->languageText('word_search', 'system'));
        $button->setToSubmit();
        $formData->addToForm('<br/>' . $button->show());


        $button = new button('cancel', $this->objLanguage->languageText('mod_oer_reset', 'oer'));
        $uri = $this->uri(array("action" => "home"));
        $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
        $formData->addToForm('&nbsp;&nbsp;' . $button->show());

        return $formData->show();
    }

    /**
     * this generates  filter sql to be used for filter products
     */
    function generateFilter() {
        $sql = "";
        $themes = $this->getParam("themes");
        $language = $this->getParam("language");
        $author = $this->getParam("author");
        $institution = $this->getParam("institution");
        $region = $this->getParam("region");
        $country = $this->getParam("country");
        $itemsPerPage = $this->getParam("itemsperpage");
        if ($themes != 'all') {
            $sql = " and themes like '%" . $themes . "%'";
        }
        if ($language != 'all') {
            $sql.= " and language='" . $language . "'";
        }
        if ($author != 'all') {
            $sql.=" and author = '" . $author . "'";
        }
        if ($region != 'all') {
            $sql.=" and region = '" . $region . "'";
        }
        if ($country != 'all') {
            $sql.=" and country = '" . $country . "'";
        }
        if ($institution != 'all') {
            $sql.=" and institutionid like '%" . $institution . "%'";
        }
      

        return $sql;
    }

    /**
     * this creates a string of selected values that will be used to remember
     * the filter options when the results are displayed
     */
    function getSelectedFilterOptions() {
        $themes = $this->getParam("themes");
        $language = $this->getParam("language");
        $author = $this->getParam("author");
        $institution = $this->getParam("institution");
        $region = $this->getParam("region");
        $country = $this->getParam("country");
        $itemsPerPage = $this->getParam("itemsperpage");
        $options = "themes=$themes!language=$language!author=$author!institution=$institution!region=$region!country=$country!itemsperpage=$itemsPerPage";
        return $options;
    }

    /**
     * Checks to see of a string contains a particular substring
     * @param $substring the substring to match
     * @param $string the string to search 
     * @return true if $substring is found in $string, false otherwise
     */
    function contains($substring, $string) {
        $pos = strpos($string, $substring);

        if ($pos === false) {
            // string needle NOT found in haystack
            return false;
        } else {
            // string needle found in haystack
            return true;
        }
    }

}

?>
