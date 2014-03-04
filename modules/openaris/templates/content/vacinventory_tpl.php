<?php 


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

//load classes from coremodules 
$this->loadClass('layer','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$this->loadClass('form','htmlelements');

$objHeading = $this->getObject('htmlheading','htmlelements');
$objHeading->str =$this->objLanguage->languageText('phrase_vaccinationreport');
$objHeading->type = 2;

$tab = "&nbsp;&nbsp;&nbsp;&nbsp;";
$tabs = "$tab$tab$tab$tab";

//create clear all button
$nextButton = new button('next', $this->objLanguage->languageText('word_next'));
$nextButton->setToSubmit();
$nextButton->setCSS('nextButton');
//create next button 

$clearButton = $this->uri(array('action'=>'vacinventory_clear'));
$clearButton = new button('clear', $this->objLanguage->languageText('phrase_clearall'), "javascript: document.location='$clearButton'");
$clearButton->setCSS('clearButton');
//create fields for form
//text input for report officer 
$repOff = new dropdown('repOfficerId');
$repOff->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$repOff->addFromDB($arrayrepoff, 'name', 'userid');
$repOff->setSelected($officerId);
$repOff->extra = 'onchange = \'javascript:getOfficerInfo("rep");\'';

//text input for data entry officer 
$dataOff = new dropdown('dataOfficerId');
$dataOff->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$dataOff->addFromDB($arraydataoff, 'name', 'userid');
$dataOff->setSelected($dataoff);
$dataOff->extra = 'onchange = \'javascript:getOfficerInfo("data");\'';

//text input for vetofficer
$vetOff = new dropdown('vetOfficerId');
$vetOff->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$vetOff->addFromDB($arrayvetoff, 'name', 'userid');
$vetOff->setSelected($vetoff);
$vetOff->extra = 'onchange = \'javascript:getOfficerInfo("vet");\'';

//report date set default to today 
$reportDate = $this->newObject('datepicker','htmlelements');
$reportDate->setName('repdate');
$reportDate->setDefaultDate($repdate);


//IBAR date set default to today
$ibarDate = $this->newObject('datepicker','htmlelements');
$ibarDate->setName('ibardate');
$ibarDate->setDefaultDate($ibardate);

// drop down for Country
$country = new dropdown('countryId');
$country->addOption('-1','Select');
$country->addFromDB($arraycountry,'common_name','id');
$country->setSelected($count);
$country->extra =  'onchange="javascript:changeCountry();"';
 
//date picker for month and year 
//$dateMonth = new datepicker($datemonth);
//$dateYear = new datepicker($dateyear);
//drop down for month
$monthdate = new dropdown('month');
for ($i=1; $i<=12; $i++) {
    $date = strtotime("01-$i-01");
    $monthdate->addOption(date('m', $date), date('F', $date));
}
$monthdate->setSelected($month);
//dropdown for year

$year = date('Y',strtotime($dateyear));
$yeardate =new dropdown('year');
	for($i=$year;$i>=$year-10;$i--){
$date = strtotime("01-01-$i");
$yeardate->addOption(date('y',$date),date('Y',$date));
}
$yeardate->setSelected($year1);
//dropdown for admin1
$admin1 = new dropdown('partitionTypeId');
$admin1->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$admin1->addFromDB($arraypartitiontype, 'partitioncategory', 'id');
$admin1->setSelected($ptype);
$admin1->extra = 'onchange="javascript:changePartitionType();"';
//print_r($admin1);echo jl;exit;

 //text field for phone
 $phone= new textinput('dataOfficerTel',$phone);
 $phone->extra = 'disabled';
 //text field for fax
 $fax = new textinput('dataOfficerFax',$fax);
   $fax->extra = 'disabled';
  //text field for email
  $email = new textinput('dataOfficerEmail',$email);
   $email->extra = 'disabled';
  //text field for phone
 $phone1= new textinput('vetOfficerTel',$phone1);
  $phone1->extra = 'disabled';
 //text field for fax
 $fax1 = new textinput('vetOfficerFax',$fax1);
   $fax1->extra = 'disabled';
  //text field for email
  $email1 = new textinput('vetOfficerEmail',$email1); 
//get htmltable object
 $email1->extra = 'disabled';
//text field for phone
 $phone2= new textinput('repOfficerTel',$phone2);
  $phone2->extra = 'disabled';
 //text field for fax
 $fax2 = new textinput('repOfficerFax',$fax2);
   $fax2->extra = 'disabled';
  //text field for email
  $email2 = new textinput('repOfficerEmail',$email2);
   $email2->extra = 'disabled';
//dropdown for admin2
$admin2 = new dropdown('partitionLevelId');
$admin2->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$admin2->addFromDB($arraypartitionlevel, 'partitionlevel', 'id');
$admin2->setSelected($plevel);
$admin2->extra = 'onchange="javascript:changeNames();"';
//dropdown for admin3
$admin3 = new dropdown('partitionId');
$admin3->addOption('-1', $this->objLanguage->languageText('mod_ahis_selectdefault', 'openaris'));
$admin3->addFromDB($arraypartition, 'partitionname', 'id');
$admin3->setSelected($pname);

//textinput field for location type
$loctype = new textinput('loctype',$loctype);

//textinput field for location name
$locname = new textinput('locname',$locname);

//textinput field for lattitude and longitude
if(!isset($lattitude)){

$lattitude = 0;
}
$latt= new textinput('lattitude',$lattitude);
if(!isset($longitude)){

$longitude = 0;
}
$latt->extra = 'onchange = \'javascript:valdirection("latt");\'';
$long = new textinput('longitude',$longitude);
$long->extra = 'onchange = \'javascript:valdirection("long");\'';

$lataxes = new dropdown('lataxes');
$lataxes->addOption('N', 'N');
$lataxes->addOption('S', 'S');
$lataxes->setSelected($lataxis);


$longaxes = new dropdown('longaxes');
$longaxes->addOption('E', 'E');
$longaxes->addOption('W', 'W');
$longaxes->setSelected($longaxis);

//get htmltable object
$objTable = $this->getObject('htmltable','htmlelements');
$objTable->cellspacing = 2;
$objTable->width = NULL;

//create table rows and place text fields and labels 
$objTable->startRow();

$objTable->addCell($this->objLanguage->languageText('mod_ahis_reportdate','openaris').$tab);
$objTable->addCell($reportDate->show());
$objTable->endRow();
$objTable->startRow();

$objTable->addCell($this->objLanguage->languageText('mod_ahis_ibarrecdate','openaris').$tab);
$objTable->addCell($ibarDate->show());
$objTable->endRow();

//get htmltable object
$objTable4 = new htmlTable();
$objTable4->cellpadding = 2;
$objTable4->cellspacing = 2;
$objTable4->width = NULL;

//create table rows and place text fields and labels 
$objTable4->startRow();
$objTable4->addCell($this->objLanguage->languageText('mod_ahis_reportofficer','openaris'));
$objTable4->addCell($tab.$repOff->show());
$objTable4->endRow();
$objTable4->startRow();
$objTable4->addCell($this->objLanguage->languageText('mod_ahis_word_phone','openaris'));
$objTable4->addCell($tab.$phone2->show());
$objTable4->addCell($tab.$this->objLanguage->languageText('mod_ahis_word_fax','openaris'));
$objTable4->addCell($tab.$fax2->show());
$objTable4->addCell($tab.$this->objLanguage->languageText('mod_ahis_word_email','openaris'));
$objTable4->addCell($tab.$email2->show());
$objTable4->endRow();

$objTable4->startRow();
$objTable4->addCell($this->objLanguage->languageText('phrase_dataentryofficer'));
$objTable4->addCell($tab.$dataOff->show());
$objTable4->endRow();

$objTable4->startRow();
$objTable4->addCell($this->objLanguage->languageText('mod_ahis_word_phone','openaris'));
$objTable4->addCell($tab.$phone->show());
$objTable4->addCell($tab.$this->objLanguage->languageText('mod_ahis_word_fax','openaris'));
$objTable4->addCell($tab.$fax->show());
$objTable4->addCell($tab.$this->objLanguage->languageText('mod_ahis_word_email','openaris'));
$objTable4->addCell($tab.$email->show());
$objTable4->endRow();

$objTable4->startRow();
$objTable4->addCell($this->objLanguage->languageText('phrase_vetofficer'));
$objTable4->addCell($tab.$vetOff->show());
$objTable4->endRow();

$objTable4->startRow();
$objTable4->addCell($this->objLanguage->languageText('mod_ahis_word_phone','openaris'));
$objTable4->addCell($tab.$phone1->show());
$objTable4->addCell($tab.$this->objLanguage->languageText('mod_ahis_word_fax','openaris'));
$objTable4->addCell($tab.$fax1->show());
$objTable4->addCell($tab.$this->objLanguage->languageText('mod_ahis_word_email','openaris'));
$objTable4->addCell($tab.$email1->show());
$objTable4->endRow();

//get htmltable object
$objTable1 = new htmlTable();
$objTable1->cellpadding = 2;
$objTable1->cellspacing = 2;
$objTable1->width = NULL;//'90%';
//$objTable1->cssClass = 'min50';

$objTable1->startRow();
$objTable1->addCell($this->objLanguage->languageText('word_country'));
$objTable1->addCell($tab.$country->show());
$objTable1->addCell($tab.$this->objLanguage->languageText('phrase_partitiontype'));
$objTable1->addCell($tab.$admin1->show());
$objTable1->addCell($tab.$this->objLanguage->languageText('mod_ahis_localitytype','openaris'));
$objTable1->addCell($tab.$loctype->show());
$objTable1->endRow();

$objTable1->startRow();
$objTable1->addCell($this->objLanguage->languageText('word_month'));
$objTable1->addCell($tab.$monthdate->show());
$objTable1->addCell($tab.$this->objLanguage->languageText('mod_ahis_partitionlevel','openaris'));
$objTable1->addCell($tab.$admin2->show());
$objTable1->addCell($tab.$this->objLanguage->languageText('mod_ahis_localityname','openaris'));
$objTable1->addCell($tab.$locname->show());
$objTable1->endRow();

$objTable1->startRow();
$objTable1->addCell($this->objLanguage->languageText('word_year'));
$objTable1->addCell($tab.$yeardate->show());
$objTable1->addCell($tab.$this->objLanguage->languageText('mod_ahis_partitionname','openaris'));
$objTable1->addCell($tab.$admin3->show());
$objTable1->addCell($tab.$this->objLanguage->languageText('word_latitude'));
$objTable1->addCell($tab.$latt->show()." ".$lataxes->show());
$objTable1->endRow();




$objTable1->startRow();
$objTable1->addCell('&nbsp');
$objTable1->addCell('&nbsp');
$objTable1->addCell('&nbsp');
$objTable1->addCell('&nbsp');
$objTable1->addCell($tab.$this->objLanguage->languageText('word_longitude'));
$objTable1->addCell($tab.$long->show()." ".$longaxes->show());
$objTable1->endRow();

$objTable1->startRow();
$objTable1->addCell($clearButton->show().$tabs.$nextButton->show(), NULL, 'top', 'center', NULL, 'colspan="6"');
$objTable1->endRow();


$objForm = new form('vacForm', $this->uri(array('action' => 'vacinventory_add')));
$objForm->addToForm($objTable->show()."<hr class='openaris' />".$objTable4->show()."<hr class='openaris' />".$objTable1->show());

$objForm->addRule('repOfficerId',$this->objLanguage->languageText('mod_ahis_reportoffreq','openaris'),'select');
//$objForm->addRule('dataOfficerTel', $this->objLanguage->languageText('mod_ahis_validatedatatel', 'openaris'), 'required');
//$objForm->addRule('vetOfficerTel', $this->objLanguage->languageText('mod_ahis_validatevettel', 'openaris'), 'required');



$objForm->addRule('dataOfficerId', $this->objLanguage->languageText('mod_ahis_dataoffreq','openaris'),'select');
$objForm->addRule('vetOfficerId', $this->objLanguage->languageText('mod_ahis_vetoffreq','openaris'),'select');
$objForm->addRule('countryId', $this->objLanguage->languageText('mod_ahis_valcountry', 'openaris'), 'select');
$objForm->addRule('partitionTypeId', $this->objLanguage->languageText('mod_ahis_admin1req','openaris'),'select');
$objForm->addRule('partitionLevelId', $this->objLanguage->languageText('mod_ahis_admin2req','openaris'),'select');
$objForm->addRule('partitionId', $this->objLanguage->languageText('mod_ahis_admin3req','openaris'),'select');
$objForm->addRule('lattitude', $this->objLanguage->languageText('mod_ahis_lattitudereq','openaris'),'numeric');
$objForm->addRule('longitude', $this->objLanguage->languageText('mod_ahis_longitudereq','openaris'),'numeric');
//$objForm->addRule(array('lattitude','5'), $this->objLanguage->languageText('mod_ahis_validaterepdateibardate', 'openaris'), 'minlength');
$objForm->addRule('repdate', $this->objLanguage->languageText('mod_ahis_validaterepdate', 'openaris'), 'datenotfuture');
$objForm->addRule('ibardate', $this->objLanguage->languageText('mod_ahis_validateibardate', 'openaris'), 'datenotfuture');
$objForm->addRule(array('repdate','ibardate'), $this->objLanguage->languageText('mod_ahis_validaterepdateibardate', 'openaris'), 'datenotbefore');
$objForm->addRule('loctype', $this->objLanguage->languageText('mod_ahis_validateloctype', 'openaris'), 'nonnumeric');
$objForm->addRule('locname', $this->objLanguage->languageText('mod_ahis_validatelocname', 'openaris'), 'nonnumeric');
$scriptUri = $this->getResourceURI('util.js');
$this->appendArrayVar('headerParams', "<script type='text/javascript' src='$scriptUri'></script>");


$objLayer = new layer();
$objLayer->addToStr($objHeading->show()."<hr class='openaris' />".$objForm->show());


echo $objLayer->show();
//echo $status;
if($status ==1){
echo "<script type=\"text/javascript\">";
echo " alert(\"Please enter valid lattitude\")";
echo "</script>";
}
if($status ==2){
echo "<script type=\"text/javascript\">";
echo " alert(\"Please enter valid longitude\")";
echo "</script>";
}

if($status ==3){
echo "<script type=\"text/javascript\">";
echo " alert(\"Please enter valid month,date cannot be in the  future\")";
echo "</script>";
}
?>