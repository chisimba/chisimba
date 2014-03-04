<?php
/**
 * ahis admin Template
 *
 * Administration home template
 * 
 * PHP version 5
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
 * @category  Chisimba
 * @package   ahis
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2008 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: admin_tpl.php 13733 2009-06-23 11:04:26Z nic $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

$this->loadClass('layer', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$objHeading = $this->getObject('htmlheading', 'htmlelements');
$objHeading->type = 2;
$objHeading->str = $this->objLanguage->languageText('mod_ahis_adminheading', 'openaris');

$objSubHeading = $this->newObject('htmlheading', 'htmlelements');
$objSubHeading->type = 3;
$objSubHeading->str = $this->objLanguage->languageText('mod_ahis_arisusers', 'openaris');

$objDataHeading = $this->newObject('htmlheading', 'htmlelements');
$objDataHeading->type = 3;
$objDataHeading->str = $this->objLanguage->languageText('mod_ahis_editdataentry', 'openaris');

$employeeLink = new link($this->uri(array('action' => 'employee_admin')));
$employeeLink->link = $this->objLanguage->languageText('mod_ahis_employeeadmin', 'openaris');

$userList = "<strong>".$this->objLanguage->languageText('mod_ahis_userfields', 'openaris')."</strong><ul class='admin'>";
$link = new link($this->uri(array('action' => 'department_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_departmentadmin', 'openaris');
$userList .= "<li>".$link->show()."</li>";
//$link = new link($this->uri(array('action' => 'role_admin')));
//$link->link = $this->objLanguage->languageText('mod_ahis_roleadmin', 'openaris');
//$userList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'status_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_statusadmin', 'openaris');
$userList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'title_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_titleadmin', 'openaris');
$userList .= "<li>".$link->show()."</li>";
$userList .= "</ul>";

$dataList = "<strong>".$this->objLanguage->languageText('mod_ahis_datafields', 'openaris')."</strong><ul class='admin'>";
/*$link = new link($this->uri(array('action' => 'age_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_ageadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'animalproduction_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_animalproductionadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'diagnosis_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_diagnosisadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'breed_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_breedadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'causative_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_causativeadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'control_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_controladmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'disease_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_diseaseadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'farmingsystem_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_farmingsystemadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'geography_level2_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_geo2admin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'geography_level3_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_geo3admin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'territory_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_locationadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'outbreak_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_outbreakstatusadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'production_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_productionadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'quality_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_qualityadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'report_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_reportadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'sample_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_sampleadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'sex_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_sexadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'species_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_speciesadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'survey_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_surveyadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";$link = new link($this->uri(array('action' => 'test_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_testadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'testresult_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_testresultadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'vaccinationhistory_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_vaccinationadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";
*/
$link = new link($this->uri(array('action' => 'language_admin')));
$link->link = "Language Admin";
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'currency_admin')));
$link->link = "Currency Admin";
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'exchangerates_admin')));
$link->link = "Exchange rates admin";
$dataList .= "<li>".$link->show()."</li>";

$link = new link($this->uri(array('action' => 'exchangeratedetails_admin')));
$link->link = "Exchange rate details admin";
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'unit_of_area_admin')));
$link->link = "Unit Of Area Admin";
$dataList .= "<li>".$link->show()."</li>";
$link = new link($this->uri(array('action' => 'country_admin')));
$link->link = "Country Admin";
$dataList .= "<li>".$link->show()."</li>";

//partition categories
$link = new link($this->uri(array('action' => 'partitioncategory_view')));
$link->link = $this->objLanguage->languageText('mod_ahis_partitioncategory', 'openaris');
$dataList .= "<li>".$link->show()."</li>";

//partition levels
$link = new link($this->uri(array('action' => 'partitionlevel_view')));
$link->link = $this->objLanguage->languageText('mod_ahis_partitionlevel', 'openaris');
$dataList .= "<li>".$link->show()."</li>";

//partitions
$link = new link($this->uri(array('action' => 'partition_view','level'=>'01')));
$link->link = $this->objLanguage->languageText('mod_ahis_partition', 'openaris');
$dataList .= "<li>".$link->show()."</li>";

$link = new link($this->uri(array('action' => 'locality_type_admin')));
$link->link = "Locality Type Admin";
$dataList .= "<li>".$link->show()."</li>";

//occurence codes
$link = new link($this->uri(array('action' => 'occurencecode_view')));
$link->link = $this->objLanguage->languageText('mod_ahis_occurencecods', 'openaris');
$dataList .= "<li>".$link->show()."</li>";

$link = new link($this->uri(array('action' => 'infectionsource_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_infectionsourcesadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";

$link = new link($this->uri(array('action' => 'diagnostic_method_admin')));
$link->link = "Diagnostic Method Admin";
$dataList .= "<li>".$link->show()."</li>";

//farming systems
$link = new link($this->uri(array('action' => 'farmingsystem_view')));
$link->link = $this->objLanguage->languageText('mod_ahis_farmingsystem', 'openaris');
$dataList .= "<li>".$link->show()."</li>";

$link = new link($this->uri(array('action' => 'controlmeasure_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_controlmeasuresadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";

$link = new link($this->uri(array('action' => 'other_control_measures_admin')));
$link->link = "Other Control Measures Admin";
$dataList .= "<li>".$link->show()."</li>";

//species types
$link = new link($this->uri(array('action' => 'speciestype_view')));
$link->link = $this->objLanguage->languageText('mod_ahis_speciestype', 'openaris');
$dataList .= "<li>".$link->show()."</li>";

$link = new link($this->uri(array('action' => 'newspecies_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_speciesnewadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";

$link = new link($this->uri(array('action' => 'species_names_admin')));
$link->link = "Species Names Admin";
$dataList .= "<li>".$link->show()."</li>";

//species age group
$link = new link($this->uri(array('action' => 'speciesagegroup_view')));
$link->link = $this->objLanguage->languageText('mod_ahis_speciesagegroup', 'openaris');
$dataList .= "<li>".$link->show()."</li>";

$link = new link($this->uri(array('action' => 'speciescategory_admin')));
$link->link = 'Species categories admin ';
$dataList .= "<li>".$link->show()."</li>";

$link = new link($this->uri(array('action' => 'species_economic_function_admin')));
$link->link = "Species Economic Function Admin";
$dataList .= "<li>".$link->show()."</li>";


//species tropical Livestock unit
$link = new link($this->uri(array('action' => 'speciestropicallivestockunit_view')));
$link->link = $this->objLanguage->languageText('mod_ahis_speciestropicallivestockunit', 'openaris');
$dataList .= "<li>".$link->show()."</li>";

$link = new link($this->uri(array('action' => 'newagent_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_agentsadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";

$link = new link($this->uri(array('action' => 'diseases_admin')));
$link->link = "Diseases Admin";
$dataList .= "<li>".$link->show()."</li>";

//disease agents
$link = new link($this->uri(array('action' => 'diseaseagent_view')));
$link->link = $this->objLanguage->languageText('mod_ahis_diseaseagnts', 'openaris');
$dataList .= "<li>".$link->show()."</li>";

$link = new link($this->uri(array('action' => 'newdiseasespecies_admin')));
$link->link = $this->objLanguage->languageText('mod_ahis_diseasespeciesadmin', 'openaris');
$dataList .= "<li>".$link->show()."</li>";

$dataList .= "</ul>";


$content = $objHeading->show().$objSubHeading->show()."<div class='admin'>".$employeeLink->show()."<br /><br />";
$content .= $objDataHeading->show().$this->objLanguage->languageText('mod_ahis_selectformfield', 'openaris')."</div>";
$content .= "<br />$userList<br />$dataList";

echo $content;