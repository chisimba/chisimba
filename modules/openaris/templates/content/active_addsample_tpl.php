<?php
/**
 * ahis Active Survaillance add new samples screen Template
 *
 * Template for capturing active surveillance new samples of herd 
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





$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('layer','htmlelements');
$this->objNewherd = $this->getObject('newherd');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objConfirm = $this->loadClass('confirm', 'utilities');
$message = $this->objLanguage->languageText('mod_ahis_confirmdel','openaris');


if ($id) {
    $hstr = $this->objLanguage->languageText('phrase_active')." ".$this->objLanguage->languageText('word_edit')."  ".$this->objLanguage->languageText('word_sample');
    $formUri = $this->uri(array('action'=>'sampleview_insert', 'id'=>$id));
    $record = $this->objSampledetails->getRow('id', $id);
    
} else {
    $hstr = $this->objLanguage->languageText('phrase_active')." ".$this->objLanguage->languageText('word_add')."  ".$this->objLanguage->languageText('word_sample');
    $formUri = $this->uri(array('action'=>'sampleview_insert'));
    $record['sampleid'] = '';
    $record['animalid'] = '';
    $record['species'] = '';
    $record['age'] = '';
    $record['sex'] = '';
    $record['sampletype'] = '';
    $record['testtype'] = '';
    $record['testresult'] = '';
    $record['specification'] = '';
    $record['vachist'] = '';
    $record['number'] = '';
    $record['remarks'] = '';
    $record['newherdid']='';

}

$tab = "&nbsp;&nbsp;&nbsp;&nbsp;";

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $hstr;
$objHeading->type = 2;
$objHeading->align = 'center';

$addButton = new button('next', $this->objLanguage->languageText('phrase_addsample'));
$addButton->setCSS('addSampleButton');
$addButton->setToSubmit();



$backButton = $this->uri(array('action'=>'active_addsample'));
$backButton = new button('back', $this->objLanguage->languageText('word_back'), "javascript: document.location='$backButton'");
$backButton->setCSS('backButton');

$add2Button = new button('next', $this->objLanguage->languageText('word_enter'));
$add2Button->setCSS('nextButton');
$add2Button->setToSubmit();

$campBox = new textinput('campname',$campName,'text',20);
$campBox->extra = "readonly";
//$farmBox = new textinput('farm',$farm);
//$farmBox->extra = "readonly";
//$farmsysBox = new textinput('farmingsystem',$farmingsystem);
//$farmsysBox->extra = "readonly";


$inputDate = $this->getObject('datepicker','htmlelements');
$inputDate->setDefaultDate($calendardate);

//$testDate = $this->newObject('datepicker','htmlelements');
//$testDate->setName('dateTest');
//$testDate->setDefaultDate($calenderdate);


$reporterBox = new dropdown('reporter');
$reporterBox->addFromDB($userList, 'name', 'userid');
$reporterBox->setSelected($reporter);
$reporterBox->extra = 'disabled';

$geo2Box  = new dropdown('geolevel2');
$geo2Box->addFromDB($arraygeo2,'name', 'id');
$geo2Box->setSelected($geo2);
$geo2Box->extra = 'disabled';

$diseaseBox = new textinput('disease',$disease,'text',20);
$diseaseBox->extra ="readonly";
//$reporterBox = new textinput('reporter',$reporter);
$surveyBox = new textinput('survey',$survey,'text',20);
$surveyBox->extra ="readonly";
$reportdateBox = new textinput('reportdate',$reportdate,'text',10);
$reportdateBox->extra ="readonly";


$farmDrop = new dropdown('farm');
$farmDrop->addFromDB($newherd, 'farmname', 'id');
$farmDrop->setSelected($record['newherdid']);
//print_r($newherd);
$speciesDrop = new dropdown('species');
$speciesDrop->addFromDB($arraySpecies, 'name', 'name');
$speciesDrop->setSelected($record['species']);
$ageDrop = new dropdown('age');
$ageDrop->addFromDB($arrayAge, 'name', 'name');
$ageDrop->setSelected($record['age']);

$sexDrop = new dropdown('sex');
$sexDrop->addFromDB($arraySex, 'name', 'name');
$sexDrop->setSelected($record['sex']);
$sampletypeDrop = new dropdown('sampletype');
$sampletypeDrop->addFromDB($arraySample, 'name', 'name');
$sampletypeDrop->setSelected($record['sampletype']);
$testtypeDrop = new dropdown('testtype');
$testtypeDrop->addFromDB($arrayTest, 'name', 'name');
$testtypeDrop->setSelected($record['testtype']);
$testresultDrop = new dropdown('testresult');
$testresultDrop->addFromDB($arrayTestresult, 'name', 'name');
$testresultDrop->setSelected($record['testresult']);
$vachistoryDrop = new dropdown('vachistory');
$vachistoryDrop->addFromDB($arrayVac, 'name', 'name');
$vachistoryDrop->setSelected($record['vachist']);


$specArea = new textarea('spec',$record['specification'],0,25);

$sampleidBox = new textinput('sampleid', $record['sampleid']);
$animalidBox = new textinput('animalid', $record['animalid']);
$numberBox = new textinput('number', $record['number']);
$remarksBox = new textarea('remarks', $record['remarks'],4,25);

$objTable = $this->getObject('htmltable','htmlelements');
$objTable->cellpadding =4;
$objTable->cellspacing = 2;
$objTable->width = NULL;
//$objTable->cssClass = 'min50';

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('word_campaign').": $tab");
$objTable->addCell($campBox->show().$tab);
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
$objTable->addCell($campBox->show());
$objTable->addCell($diseaseBox->show());
$objTable->addCell($surveyBox->show());
$objTable->addCell($reportdateBox->show());
$objTable->endRow();*/

$this->loadClass('form','htmlelements');
$objForm = new form('reportForm', $formUri);
$objForm->addToForm($objTable->show());
$objForm->addRule('farm', $this->objLanguage->languageText('mod_ahis_valreq', 'openaris'), 'required');
$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$objForm->show());
echo $objLayer->show();

if(!$id){
$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str = $this->objLanguage->languageText('phrase_samplesummary');
$objHeading->type = 2;
$objTable = new htmlTable();
$objTable->cellpadding =4;
$objTable->cellspacing = 2;
$objTable->width = '90%';
$objTable->cssClass = 'min50';

$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_sampleid'),'','','','heading');
$objTable->addCell($this->objLanguage->languageText('word_species'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_farm'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('phrase_farmingsystem'), '', '', '', 'heading');

$objTable->addCell($this->objLanguage->languageText('word_location'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('phrase_testtype'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('phrase_dateoftest'), '', '', '', 'heading');
$objTable->addCell($this->objLanguage->languageText('word_action'), '', '', '', 'heading');
$objTable->endRow();

$count = 0;
foreach($datan as $line){
foreach($newherd as $var){
if($line['newherdid']==$var['id']){


$objTable->startRow();
$objTable->addCell($line['sampleid']);
$objTable->addCell($line['species']);
$objTable->addCell($var['farmname']);
$objTable->addCell($var['farmingtype']);
$objTable->addCell($var['territory']);
$objTable->addCell($line['testtype']);
$objTable->addCell($line['testdate']);
$count++;


 $editUrl = $this->uri(array(
            'action' => 'active_addsample',
            'id' => $line['id'],
            'newherdid' => $newherdid
        ));
 $icons = $objIcon->getEditIcon($editUrl);
 $objIcon->title = $objLanguage->languageText('word_delete');
 $objIcon->setIcon('delete');
 $objConfirm = new confirm();
 $objConfirm->setConfirm($objIcon->show() , $this->uri(array(
            'action' => 'sampleview_delete',
            'id' => $line['id'],
        )) , $message);
$icons.= $objConfirm->show();
$objTable->addCell($icons);
$objTable->endRow();
}
}
}
$rep=array(
'campName'=>$campName,

);
 if($count == 0){
$finButton = $this->uri(array('action'=>'sampleview_insert','alt'=> 'yes'));
$finButton = new button('next', $this->objLanguage->languageText('word_finished'), "javascript: document.location='$finButton'");
$finButton->setCSS('finishedButton');
//$nextButton->setToSubmit();
}else
{
$finButton = $this->uri(array('action'=>'active_feedback'));
//$finButton = new button('next', $this->objLanguage->languageText('word_finished'), "javascript: document.location='$finButton'");

}




$this->loadClass('form','htmlelements');
$objForm = new form('reportForm', $formUri);
$objForm->addToForm($objTable->show());
$objForm->addRule('farm', $this->objLanguage->languageText('mod_ahis_valreq', 'openaris'), 'required');
$objLayer = new layer();
$objLayer->addToStr("<br/><b>".$this->objLanguage->code2Txt('mod_ahis_addsamplecomment2','openaris',$rep)."</b>");
$objLayer->addToStr("<br/>");
$objLayer->addToStr($objHeading->show()."<hr class='openaris' /><br/>".$objForm->show()."<br/>");
echo $objLayer->show();
}
$rep=array(
'campName'=>$campName,

);
$objTable = new htmlTable();
$objTable->cellpadding =4;
$objTable->cellspacing = 2;
$objTable->width = '90%';
$objTable->cssClass = 'min50';
$objTable->startRow();

$objTable->addCell($this->objLanguage->languageText('word_farm').":");
$objTable->addCell($farmDrop->show());
$objTable->addCell($this->objLanguage->languageText('word_species'));
$objTable->addCell($speciesDrop->show());
$objTable->addCell($this->objLanguage->languageText('phrase_sampletype'));
$objTable->addCell($sampletypeDrop->show());
$objTable->endRow();

//$objTable->startRow();
//$objTable->addCell($this->objLanguage->languageText('word_farming')." ".$this->objLanguage->languageText('word_system').":");
//$objTable->addCell($farmsysBox->show());
//$objTable->endRow();


$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_sampleid'));
$objTable->addCell($sampleidBox->show());
$objTable->addCell($this->objLanguage->languageText('word_age'));
$objTable->addCell($ageDrop->show());
$objTable->addCell($this->objLanguage->languageText('phrase_testtype'));
$objTable->addCell($testtypeDrop->show());
$objTable->endRow();
$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_animalid'));
$objTable->addCell($animalidBox->show());
$objTable->addCell($this->objLanguage->languageText('word_sex'));
$objTable->addCell($sexDrop->show());
$objTable->addCell($this->objLanguage->languageText('phrase_testresult'));
$objTable->addCell($testresultDrop->show());
$objTable->endRow();


$objTable->startRow();
$objTable->addCell($this->objLanguage->languageText('phrase_vaccinationhistory'));
$objTable->addCell($vachistoryDrop->show());
 
$objTable->addCell($this->objLanguage->languageText('phrase_dateoftest'));
$objTable->addCell($inputDate->show());
$objTable->addCell($this->objLanguage->languageText('word_number'));
$objTable->addCell($numberBox->show());
$objTable->endRow();

$objTable->startRow();

$objTable->addCell($this->objLanguage->languageText('word_remarks'));
$objTable->addCell($remarksBox->show());
$objTable->endRow();
$objTable->startRow();

$objTable->endRow();
$objTable->startRow();

$objTable->endRow();
$objTable->startRow();
 
$objTable->endRow();
$objTable->startRow();
$objTable->addCell('&nbsp;');
$objTable->endRow();
$objTable->startRow();

if($id){
    $objTable->addCell($add2Button->show());
    $objTable->addCell($backButton->show());
} else {
    if($count == 0){
    $objTable->addCell($addButton->show());
    $objTable->addCell($finButton->show());
    } else {
        $objTable->addCell($addButton->show());
        $objTable->addCell("<input type=\"button\" onclick=\"confirmation()\" class='finishedButton' value=\"Finished\">");
        echo "<script type=\"text/javascript\">

                function confirmation() {
                	var answer = confirm(\"Have you finished adding the samples\")
                	if (answer){

                		document.location = \"index.php?module=ahis&action=active_feedback\";
                	}
                }
              </script> ";
    }
}
$objTable->endRow();


$this->loadClass('form','htmlelements');
$objForm = new form('reportForm', $formUri);
$objForm->addToForm($objTable->show());

$objForm->addRule('farm', $this->objLanguage->languageText('mod_ahis_valreq1', 'openaris'), 'required');
$objForm->addRule('sampleid', $this->objLanguage->languageText('mod_ahis_valsamp', 'openaris'), 'required');
$objForm->addRule('animalid', $this->objLanguage->languageText('mod_ahis_valanim', 'openaris'), 'required');
$objForm->addRule('number', $this->objLanguage->languageText('mod_ahis_valnum', 'openaris'), 'numeric');
$objForm->addRule('number', $this->objLanguage->languageText('mod_ahis_valnum', 'openaris'), 'required');
$objForm->addRule('calendardate', $this->objLanguage->languageText('mod_ahis_valdate', 'openaris'), 'datenotfuture');
echo "<hr class='openaris' /><br/>".$this->objLanguage->code2Txt('mod_ahis_addsamplecomment','openaris',$rep)."<br />".
     $this->objLanguage->languageText('mod_ahis_addsamplefinished', 'openaris')."<br />".$objForm->show();
if($prompt == 'yes'){
echo "<script type=\"text/javascript\">";
echo "  alert(\"Please add at least one Sample\")";


echo "</script>";

}
?>