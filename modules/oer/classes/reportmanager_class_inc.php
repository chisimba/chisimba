<?php

/**
 * Contains util methods for generating reports
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
 * @author     pwando paulwando@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 *
 * @author pwando
 */
class reportmanager extends object {

    private $dbproducts;
    private $dbInstitution;
    private $objLanguage;
    public $objConfig;
    private $objUser;
    private $dbSectionContent;

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objUser = $this->getObject("user", "security");
        $this->dbInstitution = $this->getObject("dbinstitution", "oer");
        $this->dbInstitutionType = $this->getObject("dbinstitutiontypes", "oer");
        $this->dbproducts = $this->getObject("dbproducts", "oer");
        $this->dbOERAdaptations = $this->getObject("dboer_adaptations", "oer");
        $this->dbSectionContent = $this->getObject("dbsectioncontent", "oer");
        $this->dbSectionNodes = $this->getObject("dbsectionnodes", "oer");
        $this->languageCode = $this->getObject("languagecode", "language");
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
    }

    /**
     * setup make report tool view
     * return string
     */
    public function makeReportToolView() {
        // Original products count
        $orProdCount = $this->dbproducts->getOriginalProductCount();
        // adaptations count
        $orAdaptationsCount = $this->dbproducts->getAdaptationCount();
        // original lang count
        $orLangs = $this->dbproducts->getOriginalProductLanguages();
        $orLangCount = count($orLangs);
        // adaptation lang count
        $adaptLangs = $this->dbproducts->getAdaptationsLanguages();
        $adaptLangCount = count($adaptLangs);
        // adaptation institutions
        $adaptInsts = $this->dbproducts->getDistinctAdaptationsInstitutions();
        // product oerresource
        $prodResources = $this->dbproducts->getDistinctProductsOerResource();
        // adaptation oerresource
        $adaptResources = $this->dbproducts->getDistinctAdaptationsOerResource();
        //General table

        //$genTable = "<fieldset><legend>" . $this->objLanguage->languageText("mod_oer_wordgeneral", "oer", "General") . "</legend>";
        $genTable = '<table>';
        $genTable .= '<tr class="reportheader">';
        $genTable .= '<td align="left" valign="top"  width="255">' .
                '<div class="reportPropertyTitle">' .
                $this->objLanguage->languageText("mod_oer_wordproperty", "oer", "Property") . '</div></td>';
        $genTable .= '<td align="right" valign="top">' .
                '<div class="reportPropertyTitle">' .
                $this->objLanguage->languageText("mod_oer_wordvalue", "oer", "Value") . '</div></td>';
        $genTable .= "</tr>";
        $genTable .= "<tr>";
        $genTable .= '<td align="left" valign="top">' .
                '<div class="reportPropertyValue">' .
                $this->objLanguage->languageText("mod_oer_praseoriginalscount", "oer", "Number of UNESCO Originals") . '</div></td>';
        $genTable .= '<td align="right" valign="top">' .
                '<div class="reportPropertyValue">' . $orProdCount .
                '</div></td>';
        $genTable .= "</tr>";
        $genTable .= "<tr>";
        $genTable .= '<td align="left" valign="top">' .
                '<div class="reportPropertyValue">' .
                $this->objLanguage->languageText("mod_oer_praseadaptationscount", "oer", "Number of adaptations") . '</div></td>';
        $genTable .= '<td align="right" valign="top">' .
                '<div class="reportPropertyValue">' . $orAdaptationsCount .
                '</div></td>';
        $genTable .= "</tr>";
        $genTable .= "<tr>";
        $genTable .= '<td align="left" valign="top">' .
                '<div class="reportPropertyValue">' .
                $this->objLanguage->languageText("mod_oer_praselanguagecount", "oer", "Number of languages in UNESCO Originals") . '</div></td>';
        $genTable .= '<td align="right" valign="top">' .
                '<div class="reportPropertyValue">' . $orLangCount .
                '</div></td>';
        $genTable .= "</tr>";
        $genTable .= "<tr>";
        $genTable .= '<td align="left" valign="top">' .
                '<div class="reportPropertyValue">' .
                $this->objLanguage->languageText("mod_oer_praseadaptationlanguagecount", "oer", "Number of languages in adaptations") . '</div></td>';
        $genTable .= '<td align="right" valign="top">' .
                '<div class="reportPropertyValue">' . $adaptLangCount .
                '</div></td>';
        $genTable .= "</tr>";
        $genTable .= "</table>";
        //Create fieldset for general report
        $genFieldset = $this->newObject('fieldset', 'htmlelements');
        $genFieldset->setLegend($this->objLanguage->languageText("mod_oer_wordgeneral", "oer", "General"));
        $genFieldset->addContent('<div class="reporttable">'.$genTable.'</div>');
        $genFieldset = $genFieldset->show();
        //Original language table        
        $oriLangTable = '<table>';
        $oriLangTable .= '<tr class="reportheader">';
        $oriLangTable .= '<td align="left" valign="top" width="255">' .
                '<div class="reportPropertyTitle">' .
                $this->objLanguage->languageText("mod_oer_language", "oer", "Language") . '</div></td>';
        $oriLangTable .= '<td align="right" valign="top">' .
                '<div class="reportPropertyTitle">' .
                $this->objLanguage->languageText("mod_oer_total", "oer", "Total") . '</div></td>';
        $oriLangTable .= "</tr>";
        if (!empty($orLangs)) {
            foreach ($orLangs as $orLang) {
                $orLang = $orLang["language"];
                //Get originals with this language
                $orLangProds = $this->dbproducts->getLanguageOriginalProducts($orLang);
                $orLangProdsCount = count($orLangProds);
                if (!empty($orLang)) {
                    $orLang = $this->languageCode->getLanguage($orLang);
                }
                $oriLangTable .= "<tr>";
                $oriLangTable .= '<td align="left" valign="top">' .
                        '<div class="reportPropertyValue">' .
                        $orLang . '</div></td>';
                $oriLangTable .= '<td align="right" valign="top">' .
                        '<div class="reportPropertyValue">' . $orLangProdsCount .
                        '</div></td>';
                $oriLangTable .= "</tr>";
            }
        } else {
            $oriLangTable .= '<td align="left" valign="top" colspan="2">' .
                    '<div class="reportPropertyValue">' .
                    $this->objLanguage->languageText("mod_oer_nooriginallangs", "oer", "No Languages are specified for original products") .
                    '</div></td>';
        }
        $oriLangTable .= "</tr></table>";
        //Create fieldset for original products languages report
        $oriLangFieldset = $this->newObject('fieldset', 'htmlelements');
        $oriLangFieldset->setLegend($this->objLanguage->languageText("mod_oer_originallangbreakdown", "oer", "Language breakdown - originals"));
        $oriLangFieldset->addContent('<div class="reporttable">'.$oriLangTable.'</div>');
        $oriLangFieldset = $oriLangFieldset->show();
        //Adaptation language table
        $adaptLangTable = '<table>';
        $adaptLangTable .= '<tr class="reportheader">';
        $adaptLangTable .= '<td align="left" valign="top" width="255">' .
                '<div class="reportPropertyTitle">' .
                $this->objLanguage->languageText("mod_oer_language", "oer", "Language") . '</div></td>';
        $adaptLangTable .= '<td align="right" valign="top">' .
                '<div class="reportPropertyTitle">' .
                $this->objLanguage->languageText("mod_oer_total", "oer", "Total") . '</div></td>';
        $adaptLangTable .= "</tr>";
        if (!empty($adaptLangs)) {
            foreach ($adaptLangs as $adaptLang) {
                $adaptLang = $adaptLang["language"];
                //Get adaptations using this language
                $langAdaptations = $this->dbproducts->getLanguageAdaptations($adaptLang);
                $langAdaptationsCount = count($langAdaptations);
                if ($orLang == "en") {
                    $orLang = "English";
                }
                $adaptLangTable .= "<tr>";
                $adaptLangTable .= '<td align="left" valign="top">' .
                        '<div class="reportPropertyValue">' .
                        $orLang . '</div></td>';
                $adaptLangTable .= '<td align="right" valign="top">' .
                        '<div class="reportPropertyValue">' . $langAdaptationsCount .
                        '</div></td>';
                $adaptLangTable .= "</tr>";
            }
        } else {
            $adaptLangTable .= '<tr><td align="left" valign="top" colspan="2">' .
                    '<div class="reportPropertyValue">' .
                    $this->objLanguage->languageText("mod_oer_nooriginallangs", "oer", "No Languages are specified for original products") .
                    '</div></td></tr>';
        }
        $adaptLangTable .= "</table>";
        //Create fieldset for adaptations languages report
        $adaptLangFS = $this->newObject('fieldset', 'htmlelements');
        $adaptLangFS->setLegend($this->objLanguage->languageText("mod_oer_adaptationlangbreakdown", "oer", "Language breakdown - adaptations"));
        $adaptLangFS->addContent('<div class="reporttable">'.$adaptLangTable.'</div>');
        $oriLangFS = $adaptLangFS->show();
        //Institution breakdown table
        $instBreakdownTable = '<table>';
        $instBreakdownTable .= '<tr class="reportheader">';
        $instBreakdownTable .= '<td align="left" valign="top" width="255">' .
                '<div class="reportPropertyTitle">' .
                $this->objLanguage->languageText("mod_oer_typeofadaptation", "oer", "Type of adaptation") . '</div></td>';
        $instBreakdownTable .= '<td align="right" valign="top">' .
                '<div class="reportPropertyTitle">' .
                $this->objLanguage->languageText("mod_oer_total", "oer", "Total") . '</div></td>';
        $instBreakdownTable .= "</tr>";
        if (!empty($adaptInsts)) {
            $allNulls = true;
            foreach ($adaptInsts as $adaptInst) {
                if (!empty($adaptInst['institutionid'])) {
                    $instName = $this->dbInstitution->getInstitutionName($adaptInst['institutionid']);
                    $allNulls = false;
                    $instCount = $adaptInst["institutioncount"];
                    $instBreakdownTable .= "<tr>";
                    $instBreakdownTable .= '<td align="left" valign="top">' .
                            '<div class="reportPropertyValue">' .
                            $instName . '</div></td>';
                    $instBreakdownTable .= '<td align="right" valign="top">' .
                            '<div class="reportPropertyValue">' . $instCount .
                            '</div></td>';
                    $instBreakdownTable .= "</tr>";
                }
            }
            if ($allNulls) {
                $instBreakdownTable .= '<tr><td align="left" valign="top" colspan="2">' .
                        '<div class="reportPropertyValue">' .
                        $this->objLanguage->languageText("mod_oer_noadaptationinst", "oer", "No Institutions are specified for adaptations") .
                        '</div></td></tr>';
            }
        } else {
            $instBreakdownTable .= '<tr><td align="left" valign="top" colspan="2">' .
                    '<div class="reportPropertyValue">' .
                    $this->objLanguage->languageText("mod_oer_noadaptationinst", "oer", "No Institutions are specified for adaptations") .
                    '</div></td></tr>';
        }
        $instBreakdownTable .= "</table>";
        //Create fieldset for Breakdown by type - institutions
        $instBdownFS = $this->newObject('fieldset', 'htmlelements');
        $instBdownFS->setLegend($this->objLanguage->languageText("mod_oer_breakdownbyinst", "oer", "Breakdown by type - institutions"));
        $instBdownFS->addContent('<div class="reporttable">'.$instBreakdownTable.'</div>');
        $instBdownFS = $instBdownFS->show();
        //product type breakdown table
        $originalsBreakdownTable = '<table>';
        $originalsBreakdownTable .= '<tr class="reportheader">';
        $originalsBreakdownTable .= '<td align="left" valign="top" width="255">' .
                '<div class="reportPropertyTitle">' .
                $this->objLanguage->languageText("mod_oer_typeofproduct", "oer", "Type of product") . '</div></td>';
        $originalsBreakdownTable .= '<td align="right" valign="top">' .
                '<div class="reportPropertyTitle">' .
                $this->objLanguage->languageText("mod_oer_total", "oer", "Total") . '</div></td>';
        $originalsBreakdownTable .= "</tr>";
        if (!empty($prodResources)) {
            $allNulls = true;
            foreach ($prodResources as $prodResource) {
                if (!empty($prodResource['oerresource'])) {
                    $allNulls = false;
                    $originalsBreakdownTable .= "<tr>";
                    $originalsBreakdownTable .= '<td align="left" valign="top">' .
                            '<div class="reportPropertyValue">' .
                            $prodResource['oerresource'] . '</div></td>';
                    $originalsBreakdownTable .= '<td align="right" valign="top">' .
                            '<div class="reportPropertyValue">' . $prodResource['oerresourcecount'] .
                            '</div></td>';
                    $originalsBreakdownTable .= "</tr>";
                }
            }
            if ($allNulls) {
                $originalsBreakdownTable .= '<tr><td align="left" valign="top" colspan="2">' .
                        '<div class="reportPropertyValue">' .
                        $this->objLanguage->languageText("mod_oer_noadaptationtypeoriginals", "oer", "No types are specified for original products") .
                        '</div></td></tr>';
            }
        } else {
            $originalsBreakdownTable .= '<tr><td align="left" valign="top" colspan="2">' .
                    '<div class="reportPropertyValue">' .
                    $this->objLanguage->languageText("mod_oer_noadaptationtypeoriginals", "oer", "No types are specified for original products") .
                    '</div></td></tr>';
        }
        $originalsBreakdownTable .= "</table>";
        //Create fieldset for Breakdown by type - originals
        $originalBdownFS = $this->newObject('fieldset', 'htmlelements');
        $originalBdownFS->setLegend($this->objLanguage->languageText("mod_oer_breakdownbyoriginals", "oer", "Breakdown by type - originals"));
        $originalBdownFS->addContent('<div class="reporttable">'.$originalsBreakdownTable.'</div>');
        $originalBdownFS = $originalBdownFS->show();
        //adaptation type breakdown table        
        $adaptationBreakdownTable = '<table>';
        $adaptationBreakdownTable .= '<tr class="reportheader">';
        $adaptationBreakdownTable .= '<td align="left" valign="top" width="255">' .
                '<div class="reportPropertyTitle">' .
                $this->objLanguage->languageText("mod_oer_typeofadaptation", "oer", "Type of adaptation") . '</div></td>';
        $adaptationBreakdownTable .= '<td align="right" valign="top">' .
                '<div class="reportPropertyTitle">' .
                $this->objLanguage->languageText("mod_oer_total", "oer", "Total") . '</div></td>';
        $adaptationBreakdownTable .= "</tr>";
        if (!empty($adaptResources)) {
            $allNulls = true;
            foreach ($adaptResources as $adaptResource) {
                if (!empty($adaptResource['oerresource'])) {
                    $allNulls = false;
                    $adaptationBreakdownTable .= "<tr>";
                    $adaptationBreakdownTable .= '<td align="left" valign="top">' .
                            '<div class="reportPropertyValue">' .
                            ucfirst($adaptResource['oerresource']) . '</div></td>';
                    $adaptationBreakdownTable .= '<td align="right" valign="top">' .
                            '<div class="reportPropertyValue">' . $adaptResource['oerresourcecount'] .
                            '</div></td>';
                    $adaptationBreakdownTable .= "</tr>";
                }
            }
            if ($allNulls) {
                $adaptationBreakdownTable .= '<tr><td align="left" valign="top" colspan="2">' .
                        '<div class="reportPropertyValue">' .
                        $this->objLanguage->languageText("mod_oer_noadaptationtypeadaptations", "oer", "No types are specified for adaptations") .
                        '</div></td></tr>';
            }
        } else {
            $adaptationBreakdownTable .= '<tr><td align="left" valign="top" colspan="2">' .
                    '<div class="reportPropertyValue">' .
                    $this->objLanguage->languageText("mod_oer_noadaptationtypeoriginals", "oer", "No types are specified for original products") .
                    '</div></td></tr>';
        }
        $adaptationBreakdownTable .= "</table>";
        //Create fieldset for Breakdown by type - adaptations
        $adaptBdownFS = $this->newObject('fieldset', 'htmlelements');
        $adaptBdownFS->setLegend($this->objLanguage->languageText("mod_oer_breakdownbyadaptations", "oer", "Breakdown by type - adaptations"));
        $adaptBdownFS->addContent('<div class="reporttable">'.$adaptationBreakdownTable.'</div>');
        $adaptBdownFS = $adaptBdownFS->show();

        //country breakdown table
        $countryBreakdownTable = '<table>';
        $countryBreakdownTable .= '<tr class="reportheader">';
        $countryBreakdownTable .= '<td align="left" valign="top" width="255">' .
                '<div class="reportPropertyTitle">' .
                $this->objLanguage->languageText("mod_oer_countryadaptation", "oer", "Country of adaptation") . '</div></td>';
        $countryBreakdownTable .= '<td align="right" valign="top">' .
                '<div class="reportPropertyTitle">' .
                $this->objLanguage->languageText("mod_oer_total", "oer", "Total") . '</div></td>';
        $countryBreakdownTable .= "</tr>";
        if (!empty($adaptInsts)) {
            $allNulls = true;
            foreach ($adaptInsts as $adaptInst) {
                if (!empty($adaptInst['institutionid'])) {
                    $instData = $this->dbInstitution->getInstitutionById($adaptInst['institutionid']);
                    $allNulls = false;
                    $instCount = $adaptInst["institutioncount"];
                    $countryBreakdownTable .= "<tr>";
                    $countryBreakdownTable .= '<td align="left" valign="top">' .
                            '<div class="reportPropertyValue">' .
                            ucfirst($instData['country']) . '</div></td>';
                    $countryBreakdownTable .= '<td align="right" valign="top">' .
                            '<div class="reportPropertyValue">' . $instCount .
                            '</div></td>';
                    $countryBreakdownTable .= "</tr>";
                }
            }
            if ($allNulls) {
                $countryBreakdownTable .= '<tr><td align="left" valign="top" colspan="2">' .
                        '<div class="reportPropertyValue">' .
                        $this->objLanguage->languageText("mod_oer_noadaptationinst", "oer", "No Institutions are specified for adaptations") .
                        '</div></td></tr>';
            }
        } else {
            $countryBreakdownTable .= '<tr><td align="left" valign="top" colspan="2">' .
                    '<div class="reportPropertyValue">' .
                    $this->objLanguage->languageText("mod_oer_noadaptationinst", "oer", "No Institutions are specified for adaptations") .
                    '</div></td></tr>';
        }
        $countryBreakdownTable .= "</table>";
        //Create fieldset for Breakdown by country - adaptations
        $countryBdownFS = $this->newObject('fieldset', 'htmlelements');
        $countryBdownFS->setLegend($this->objLanguage->languageText("mod_oer_breakdownbycountry", "oer", "Breakdown by country - adaptations"));
        $countryBdownFS->addContent('<div class="reporttable">'.$countryBreakdownTable.'</div>');
        $countryBdownFS = $countryBdownFS->show();

        //main table        
        $supportTable = '<div class="allreportstable"><table border="0" style="table-layout:fixed;" width="680">';
        $supportTable .= "<tr>";
        $supportTable .= '<td align="left" valign="top">' . $genFieldset . '</td>';
        $supportTable .= '<td align="left" valign="top">' . '</td>';
        $supportTable .= '</tr>';
        $supportTable .= "<tr>";
        $supportTable .= '<td align="left" valign="top">' . '</td>';
        $supportTable .= '<td align="left" valign="top">' . '</td>';
        $supportTable .= '</tr>';
        $supportTable .= "<tr>";
        $supportTable .= '<td align="left" valign="top">' . $oriLangFieldset . '</td>';
        $supportTable .= '<td align="left" valign="top">' . $originalBdownFS . '</td>';
        $supportTable .= '</tr>';
        $supportTable .= "<tr>";
        $supportTable .= '<td align="left" valign="top">' . '</td>';
        $supportTable .= '<td align="left" valign="top">' . '</td>';
        $supportTable .= '</tr>';
        $supportTable .= "<tr>";
        $supportTable .= '<td align="left" valign="top">' . $oriLangFS . '</td>';
        $supportTable .= '<td align="left" valign="top">' . $adaptBdownFS . '</td>';
        $supportTable .= '</tr>';
        $supportTable .= "<tr>";
        $supportTable .= '<td align="left" valign="top">' . '</td>';
        $supportTable .= '<td align="left" valign="top">' . '</td>';
        $supportTable .= '</tr>';
        $supportTable .= "<tr>";
        $supportTable .= '<td align="left" valign="top">' . $instBdownFS . '</td>';
        $supportTable .= '<td align="left" valign="top">' . $countryBdownFS . '</td>';
        $supportTable .= '</tr>';
        $supportTable .= "<tr>";
        $supportTable .= '<td align="left" valign="top">' . '</td>';
        $supportTable .= '<td align="left" valign="top">' . '</td>';
        $supportTable .= '</tr>';
        $supportTable .= "</table></div>";

        $tabber = '<div id="reports">
<div class="tabber">

     <div class="tabbertab">
	  <h2 class="reportingtool">' . $this->objLanguage->languageText('mod_oer_reportingtool', 'oer') . '</h2>'
                . $supportTable . '
     </div>


     <div class="tabbertab">
	  <h2 class="mostrated">' . $this->objLanguage->languageText('mod_oer_word_adaptations_by_multiple_creteria', 'oer') . '</h2>
            '.$this->displayAdaptationsByCriteria().'
     </div>


';

        return $tabber;
    }

    /**
     * this displays adaptatios by criteria
     * @return type 
     */
    public function displayAdaptationsByCriteria() {
        $languageCode = $this->getObject("languagecode", "language");

        $uri = $this->uri(array('action' => 'viewbasicreport'));
        $objForm = new form('formReport', $uri);
        $countries = $this->dbproducts->getCountriesWithAdaptations();

        $countrySelect = new dropdown('countryDropdown[]');
        $countrySelect->extra = ' multiple="1" size="4" style="width:200pt;" ';

        foreach ($countries as $country) {
            $countrySelect->addOption($country['country'], $languageCode->getName($country['country']));
        }


        $content = ' 
                <div class="topReportingDiv">
                <fieldset>
                <legend>' .
                $this->objLanguage->languageText('mod_oer_countryandregion', 'oer')
                . '</legend>
                    
                    <table><tr><td>
                            
                                <div class="report_block">
                                   ' .
                $this->objLanguage->languageText('mod_oer_selectcountry', 'oer')
                . '<br>'
                . $countrySelect->show() . '<br>' .
                $this->objLanguage->languageText('mod_oer_ctrl_country', 'oer')
                . '<br><br> 
                                </div>
                                </td>
                                <td>
                                <div class="report_block">
                                     ';

        $regionSelect = new dropdown('regionDropdown[]');
        $regionSelect->extra = ' multiple="1" size="4" style="width:200pt;" ';
        $regionSelect->addOption('Africa');
        $regionSelect->addOption('Arab States');
        $regionSelect->addOption('Asia and the Pacific');
        $regionSelect->addOption('Europe and North America');

        $content .= ' ' .
                $this->objLanguage->languageText('mod_oer_selectregion', 'oer')
                . '<br>' . $regionSelect->show() . '
                                	<br>
                                    ' .
                $this->objLanguage->languageText('mod_oer_ctrl_region', 'oer')
                . '
                                    
                                </div>
                                </td>
                                </tr>
                                </table>
                                </fieldset>
                            <div>
                        

                        <br>
                        <fieldset>
                            <legend>' .
                $this->objLanguage->languageText('mod_oer_themesandkeywords', 'oer')
                . '</legend>
                            <div class="legendContent">
                                <div class="report_block">
                                	' .
                $this->objLanguage->languageText('mod_oer_unescotheme', 'oer')
                . '<br>';

        $themeSelect = new dropdown('themeDropdown[]');
        $themeSelect->extra = ' multiple="1" size="4" style="width:200pt;" ';
        $dbThemes = $this->getObject("dbthemes", "oer");
        $themes = $dbThemes->getThemes();

        foreach ($themes as $theme) {
            $themeSelect->addOption($theme['umbrellatheme']);
        }

        /*      $keywordSelect = new dropdown('keywordDropdown[]');
          $keywordSelect->extra = ' multiple="1" size="4" style="width:200pt;" ';

          $keywordArray = $this->objDbReporting->getProductKeywords();
          $ArrayCount = sizeof($keywordArray);
          $keywordArray2 = array();

          for ($i = 0; $i < $ArrayCount; $i++) {
          $keywordArray2[] = $keywordArray[$i]["keyword"];
          }

          sort($keywordArray2, SORT_STRING);

          for ($z = 0; $z < $ArrayCount; $z++) {
          $keywordSelect->addOption($keywordArray2[$z]);
          }
         */
        $content .= $themeSelect->show() . '
                                	<br>
                                    ' .
                $this->objLanguage->languageText('mod_oer_ctrl_theme', 'oer')
                . '
                                </div>
                            </div>
                        </fieldset>
                        <br>
                        <fieldset>
                            <legend>' .
                $this->objLanguage->languageText('mod_oer_adaptationtype', 'oer')
                . '</legend>

                            <div class="legendContent">
                                <div class="report_block">';


        $adaptationTypes = array("curriculum");
        foreach ($adaptationTypes as $adaptationType) {
            $checkbox = new checkbox('adaptationType[]');
            $checkbox->value = $adaptationType;
            $content .= $checkbox->show() . $adaptationType . ' ';
        }

        $content .= '</div>
                            </div>
                        </fieldset>
                        <br>
                        <fieldset>
                            <legend>' .
                $this->objLanguage->languageText('mod_oer_institutiontype_name', 'oer')
                . '</legend>
                            <div class="legendContent">
                                 <div class="report_block">';


        $dbInstitutionType = $this->getObject("dbinstitutiontypes", "oer");
        $institutionTypes = $dbInstitutionType->getInstitutionTypes();
        foreach ($institutionTypes as $institutionType) {
            $checkbox = new checkbox('InstitutionType[]');
            $checkbox->value = $institutionType['id'];
            $content .= $checkbox->show() . $institutionType['type'] . ' ';
        }

        $content .= '</div>
                            </div>
                        </fieldset>
                        <br>
                        <fieldset>
                            <legend>' .
                $this->objLanguage->languageText('mod_oer_language', 'oer')
                . '</legend>
                            <div class="legendContent">
                                <div class="report_block">';

        $langSelect = new dropdown('langDropdown[]');
        $langSelect->extra = ' multiple="1" size="4" style="width:200pt;" ';
        $langSelect->addOption('all', $this->objLanguage->languageText('word_all', 'system'));
        $langSelect->addOption('en', $this->objLanguage->languageText('mod_oer_english', 'oer'));


        $generateReportButton = new button('generate');
        $ButtonTitle = $this->objLanguage->languageText('mod_oer_generatereport', 'oer');
        $generateReportButton->setValue($ButtonTitle);
        $generateReportButton->setToSubmit();

       
        $resetButton = new button('reset');
        $resetButton->setValue($this->objLanguage->languageText('mod_oer_reset', 'oer'));
        $resetButton->setToReset();


        $content .= $langSelect->show() . '<br>
                                    ' .
                $this->objLanguage->languageText('mod_oer_ctrl_language', 'oer')
                . '
                                </div>
                                <div class="report_block">&nbsp;</div>

                            </div>
                        </fieldset>
                        <br>
                        <fieldset>
                            <legend>' .
                $this->objLanguage->languageText('mod_oer_output', 'oer')
                . '</legend>
                            <div class="legendContent">
                                <div class="report_block">
                                	' .
                $this->objLanguage->languageText('mod_oer_reportformat', 'oer')
                . '<br>

                                    <input type="radio" name="radio" id=""> PDF<br>

                                </div>

                            </div>
                        </fieldset>

                        <div class="legendContent tenPixelTopPadding">
                            <div class="saveCancelButtonHolder">

                            </div>
                            <div class="saveCancelButtonHolder">
                                
                                ' . $generateReportButton->show() . ' ' . $resetButton->show() . '
                            </div>
                        </div>

                    </div>
                
                
                
                ';

        $objForm->addToForm($content);

        return $objForm->show();
    }
    
    
    /**
      This generates the filtered basic report
     */
    function generateFilteredBasicReport(){
        $countries=$this->getParam("countryDropdown");
        $regions=$this->getParam("regionDropdown");
        $themes=$this->getParam("themeDropdown");
        $adaptationType=$this->getParam("adaptationType");
        $languages=$this->getParam("langDropdown");
        /*
      echo "Countries</br>";
      print_r($countries);
      echo "<br/>regions<br/>";
      
      print_r($regions);
      echo "<br/>themes<br/>";
      
      print_r($themes);
      echo "<br/>adaptationType<br/>";
      
      print_r($adaptationType);
      echo "<br/>langs<br/>";
      
      print_r($languages);
        
        die();*/
    }

}

?>