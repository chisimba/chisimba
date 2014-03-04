<?php
/**
 * ahis Active Survaillance add Herd screen Template
 *
 * Template for capturing active surveillance for new herd 
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
 * @author    Rosina Ntow <rntow@ug.edu.gh>
 * @copyright 2009 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: active_herdview_tpl.php 
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

if ($id) {
    $hstr = $this->objLanguage->languageText('phrase_active')." ".$this->objLanguage->languageText('word_edit')."  ".$this->objLanguage->languageText('word_farm');
    $formUri = $this->uri(array('action'=>'newherd_insert', 'id'=>$id));
    $record = $this->objNewherd->getRow('id', $id);
    
} else {
    $hstr = $this->objLanguage->languageText('phrase_active')." ".$this->objLanguage->languageText('word_add')."  ".$this->objLanguage->languageText('word_farm');
    $formUri = $this->uri(array('action'=>'newherd_insert'));
    
    $record['territory'] = $record['farm'] = $record['farmingtype'] = 
	$record['latdeg'] = $record['latmin'] = $record['longdeg'] =
	$record['longmin'] = $record['longdirec'] = $record['latdirec'] = '';


}

$tab = "&nbsp;&nbsp;&nbsp;&nbsp;";

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $hstr;
$objHeading->type = 2;
$objHeading->align = 'center';


$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('layer','htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objConfirm = $this->loadClass('confirm', 'utilities');

$message = $this->objLanguage->languageText('mod_ahis_confirmdel','openaris');

$objConfirm = new confirm();
$objConfirm->setConfirm($objIcon->show() , $this->uri(array(
            'action' => 'newherd_delete',
            'id' => $line['id'],
        )) , $message);

if($hdata == NULL){

$nextButton = $this->uri(array('action'=>'newherd_insert','alt'=>'yes'));
$nextButton = new button('next', $this->objLanguage->languageText('word_finished'), "javascript: document.location='$nextButton'");
$nextButton->setCSS('finishedButton');
}

$backButton = $this->uri(array('action'=>'active_addherd'));
$backButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backButton'");
$backButton->setCSS('backButton');

$addButton = new button('next', $this->objLanguage->languageText('phrase_addfarm'));
$addButton->setCSS('addFarmButton');
$addButton->setToSubmit();

$add2Button = new button('next', $this->objLanguage->languageText('word_enter'));
$add2Button->setCSS('nextButton');
$add2Button->setToSubmit();
//$addUri = $this->uri(array('action'=>'active_newherd'));
//$addButton = new button('cancel', $this->objLanguage->languageText('word_add'), "javascript: document.location='$addUri'");

$reporterBox = new dropdown('reporter');
$reporterBox->addFromDB($userList, 'name', 'userid');
$reporterBox->setSelected($reporter);
$reporterBox->extra = 'disabled';

$geo2Box  = new dropdown('geolevel2');
$geo2Box->addFromDB($arraygeo2,'name', 'id');
$geo2Box->setSelected($geo2);
$geo2Box->extra = 'disabled';

$campNameBox = new textinput('campname',$campName,'text',20);
$campNameBox->extra ="readonly";
$diseaseBox = new textinput('disease',$disease,'text',20);
$diseaseBox->extra ="readonly";
//$reporterBox = new textinput('reporter',$reporter);
$surveyBox = new textinput('survey',$survey,'text');
$surveyBox->extra ="readonly";
//$geo2Box = new textinput('geo2',$geo2);
$reportdateBox = new textinput('reportdate',$reportdate,'text',10);
$reportdateBox->extra ="readonly";

$territoryDrop = new dropdown('territory');
$territoryDrop->addFromDB($arrayTerritory, 'name', 'name');
$territoryDrop->setSelected($record['territory']);
print_r($arrayTerritory);

$farmsystemDrop = new dropdown('farmingsystem');
$farmsystemDrop->addFromDB($arrayFarmingsystem, 'name', 'name');
$farmsystemDrop->setSelected($record['farmingtype']);

$farmBox = new textinput('farm', $record['farmname']);

$activeBox = new textinput('activeid',$activeid,'hidden');
$objTable = $this->getObject('htmltable','htmlelements');
$objTable->cellspacing = 2;
$objTable->width = NULL;

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_campaign').": $tab");
$objTable->addCell($campNameBox->show().$tab);
$objTable->addCell($this->objLanguage->languageText('word_disease').": $tab");
$objTable->addCell($diseaseBox->show().$tab);
$objTable->addCell($this->objLanguage->languageText('mod_ahis_reportofficer','openaris').": $tab");
$objTable->addCell($reporterBox->show().$tab);
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_surveytype').": $tab");
$objTable->addCell($surveyBox->show().$tab);
$objTable->addCell($this->objLanguage->languageText('phrase_geolevel2').": $tab");
$objTable->addCell($geo2Box->show().$tab);
$objTable->addCell($this->objLanguage->languageText('mod_ahis_reportdate','openaris').": $tab");
$objTable->addCell($reportdateBox->show().$tab);
$objTable->endRow();

/*$objTable->startRow();
$objTable->addCell($campNameBox->show());
$objTable->addCell($diseaseBox->show());
$objTable->addCell($surveyBox->show());
$objTable->addCell($reportdateBox->show());
$objTable->endRow();*/

$this->loadClass('form','htmlelements');
$objForm = new form('reportForm');
$objForm->addToForm($objTable->show());

$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr class='openaris' /><br/>".$objForm->show());
//$objLayer->align = 'center';

echo $objLayer->show();

if(!$id){
$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $this->objLanguage->languageText('phrase_farmsummary');
$objHeading->type = 2;
$objTable = new htmlTable();
$objTable->cellpadding =4;
$objTable->cellspacing = 2;
$objTable->width = '90%';
$objTable->cssClass = 'min50';

$objTable->startRow();

$objTable->addCell($this->objLanguage->languageText('word_farm')." ".$this->objLanguage->languageText('word_name'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_farming')." ".$this->objLanguage->languageText('word_system'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_location'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_action'), '', '', '', 'heading');
$objTable->endRow();
foreach($hdata as $line ){

$objTable->startRow();
$objTable->addCell($line['farmname']);
$objTable->addCell($line['farmingtype']);
$objTable->addCell($line['territory']);
 $editUrl = $this->uri(array(
            'action' => 'active_addherd',
            'id' => $line['id'],

        ));
 $icons = $objIcon->getEditIcon($editUrl);
 $objIcon->title = $objLanguage->languageText('word_delete');
 $objIcon->setIcon('delete');
 $objConfirm = new confirm();
 $objConfirm->setConfirm($objIcon->show() , $this->uri(array(
            'action' => 'newherd_delete',
            'id' => $line['id'],
        )) , $message);
$icons.= $objConfirm->show();
$objTable->addCell($icons);
$objTable->endRow();
}
$this->loadClass('form','htmlelements');
$objForm = new form('reportForm', $formUri);
$objForm->addToForm($objTable->show());
$objForm->addRule('farm', $this->objLanguage->languageText('mod_ahis_valreq', 'openaris'), 'required');
$objLayer = new layer();
$objLayer->addToStr("<br/><b>".$this->objLanguage->languageText('mod_ahis_addfarmcomment','openaris')."</b>");
$objLayer->addToStr("<br/>");
$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$objForm->show()."<br/>");
$objLayer->addToStr("<br/>");
echo $objLayer->show();
}
$rep=array(
'campName'=>$campName,

);

$latitudeDegBox = new textinput('latdeg', $record['latdeg'], 'text', 4);
$longitudeDegBox = new textinput('longdeg', $record['longdeg'], 'text', 4);
$latitudeMinBox = new textinput('latmin', $record['latmin'], 'text', 4);
$longitudeMinBox = new textinput('longmin', $record['longmin'], 'text', 4);
$latDrop = new dropdown('latdirection');
$latDrop->addOption('N','N');
$latDrop->addOption('S','S');
$latDrop->setSelected($record['latdirec']);
$longDrop = new dropdown('longdirection');
$longDrop->addOption('E','E');
$longDrop->addOption('W','W');
$longDrop->setSelected($record['longdirec']);
$degrees = $this->objLanguage->languageText('word_degrees');
$minutes = $this->objLanguage->languageText('word_minutes');

$objTable = new htmlTable();
$objTable->cellpadding =4;
$objTable->cellspacing = 2;
$objTable->width = NULL;
$objTable->cssClass = 'min50';

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_farm').": ");
$objTable->addCell($farmBox->show());
$objTable->addCell($this->objLanguage->languageText('word_location').": ");
$objTable->addCell($territoryDrop->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_farming')." ".$this->objLanguage->languageText('word_system').": $tab");
$objTable->addCell($farmsystemDrop->show());
$objTable->addCell($this->objLanguage->languageText('word_latitude').":$tab");
$objTable->addCell("$degrees: ".$latitudeDegBox->show()." $minutes: ".$latitudeMinBox->show().$latDrop->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();
$objTable->addCell("&nbsp;");
$objTable->addCell("&nbsp;");
$objTable->addCell($this->objLanguage->languageText('word_longitude').":$tab");
$objTable->addCell("$degrees: ".$longitudeDegBox->show()." $minutes: ".$longitudeMinBox->show().$longDrop->show(),NULL,'center');
$objTable->endRow();
$objTable->startRow();

$objTable->addCell($activeBox->show());
$objTable->endRow();



$objTable->startRow();
if($id) {
    $objTable->addCell($add2Button->show());
    $objTable->addCell($backButton->show());
} else {
    if($hdata == NULL){
        $objTable->addCell($addButton->show());
        $objTable->addCell($nextButton->show());
    } else {
        $objTable->addCell($addButton->show());
        $objTable->addCell("<input type=\"button\" onclick=\"confirmation()\" class='finishedButton' value=\"Finished\">");
        $nextPage = $this->uri(array('action'=>'active_addsample'));
        echo "<script type=\"text/javascript\">

                function confirmation() {
                	var answer = confirm(\"Have you finished adding the farms\")
                	if (answer){
    
                	document.location = \"index.php?module=ahis&action=active_addsample\";
                    }
                }
              </script> ";
    }
}
$objTable->endRow();




$this->loadClass('form','htmlelements');
$objForm = new form('reportForm', $formUri);
$objForm->addToForm($objTable->show());
//if($hdata == NULL){
//$objForm->addRule('farm', $this->objLanguage->languageText('mod_ahis_valreq', 'openaris'), 'required');
//}else
//{
$objForm->addRule('farm', $this->objLanguage->languageText('mod_ahis_valreq', 'openaris'), 'required');
$objForm->addRule('farm', $this->objLanguage->languageText('mod_ahis_valreq2', 'openaris'), 'nonnumeric');
$objForm->addRule('latdeg', $this->objLanguage->languageText('mod_ahis_vallatitude', 'openaris'), 'numeric');
$objForm->addRule('longdeg', $this->objLanguage->languageText('mod_ahis_vallongitude', 'openaris'), 'numeric');
$objForm->addRule('latmin', $this->objLanguage->languageText('mod_ahis_vallatitude', 'openaris'), 'numeric');
$objForm->addRule('longmin', $this->objLanguage->languageText('mod_ahis_vallongitude', 'openaris'), 'numeric');
//}
$objLayer = new layer();
if($id){
$objLayer->addToStr("<hr class='openaris' />".$this->objLanguage->languageText('mod_ahis_editfarmcomment','openaris')."<br/>".$objForm->show());
}else
$objLayer->addToStr("<hr class='openaris' />".$this->objLanguage->code2Txt('mod_ahis_addfarmcomment2','openaris',$rep)."<br/>".$objForm->show());
//$objLayer->align = 'center';


echo $objLayer->show();
if($prompt == 'yes'){
echo "<script type=\"text/javascript\">";
echo "  alert(\"Please add at least one Farm\")";


echo "</script>";

}

?>