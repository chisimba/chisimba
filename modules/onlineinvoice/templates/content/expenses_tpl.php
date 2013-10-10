<?php

  /** create template for per diem expenses of the travel journey **/

   $this->loadClass('windowpop','htmlelements');
   $this->objPop=&new windowpop;
   $this->objPop->set('window_name','Foreign Per Diem Rates');
   $this->objPop->set('location','<a href=http://www.state.gov/m/a/als/prdm/>www.state.gov/m/a/als/prdm</a>');
   $this->objPop->set('linktext','www.state.gov/m/a/als/prdm');
   $this->objPop->set('width','600');
   $this->objPop->set('height','300');
   $this->objPop->set('left','300');
   $this->objPop->set('top','400');
   $this->objPop->set('menubar','yes');
   $this->objPop->set('resizable','yes');
   $this->objPop->set('scrollbars','yes');
   $this->objPop->set('status','yes');
   $this->objPop->set('toolbar','yes');
   $this->objPop->putJs();

   $foreignRate = $this->objPop->show();


  /**
   *create all form headings
   */

    $this->objMainheading = $this->newObject('htmlheading','htmlelements');
    $this->objMainheading->type = 1;
    $this->objMainheading->str=$objLanguage->languageText('mod_onlineinvoice_travelperdiemexpenses','onlineinvoice');

    //$foreignRate  = '<a href=http://www.state.gov/m/a/als/prdm/>www.state.gov/m/a/als/prdm</a>';
    $this->objforeignheading = $this->newObject('htmlheading','htmlelements');
    $this->objforeignheading->type = 5;
    $this->objforeignheading->str=$objLanguage->languageText('mod_onlineinvoice_foreignrateperdiem','onlineinvoice')." " .$foreignRate;

    $domesticRate  = '<a href=http://www.gsa.gov/>www.gsa.gov</a>';
    $this->objdomesticheading = $this->newObject('htmlheading','htmlelements');
    $this->objdomesticheading->type = 5;
    $this->objdomesticheading->str=$objLanguage->languageText('mod_onlineinvoice_domesticrateperdiem','onlineinvoice') .' ' .$domesticRate;

    $exit  = $this->objLanguage->languageText('phrase_exit');
    $next = $this->objLanguage->languageText('phrase_next');
    $add  = $this->objLanguage->languageText('mod_onlineinvoice_addexpense','onlineinvoice');
    $back = ucfirst($this->objLanguage->languageText('word_back'));

    $perdieminstruction = $this->objLanguage->languageText('mod_onlineinvoice_perdieminstruction','onlineinvoice');
    $perdiemdate  = $this->objLanguage->languageText('mod_onlineinvoice_perdiemdate','onlineinvoice');
    $fordomrate = $this->objLanguage->languageText('mod_onlineinvoice_fordomrate','onlineinvoice');
    $rateexpl = $this->objLanguage->languageText('mod_onlineinvoice_rateexpl','onlineinvoice');
    $perdiemloc = $this->objLanguage->languageText('mod_onlineinvoice_perdiemloc','onlineinvoice');
    $perdiemadd = $this->objLanguage->languageText('mod_onlineinvoice_perdiemadd','onlineinvoice');
    $perdiemaction  = $this->objLanguage->languageText('mod_onlineinvoice_perdiemaction','onlineinvoice');

    $helpstring = $perdieminstruction . '<br />'  . $perdiemdate  . '<br />'  . $fordomrate . '<br />'  . $rateexpl . '<br />'  . $perdiemloc . '<br />'  . $perdiemadd . '<br />'  . $perdiemaction;

    $this->objHelp=& $this->getObject('helplink','help');
    $displayhelp  = $this->objHelp->show($helpstring);

/**************************************************************************************************************/
/**
 *create all form labels
 */

$expensesdate = $this->objLanguage->languageText('word_date');
$lblDate = 'lbldate';
$this->objdate  = $this->newObject('label','htmlelements');
$this->objdate->label($expensesdate,$lblDate);

$breakfast  = $this->objLanguage->languageText('word_breakfast');
$lblBreakfast = 'lblbreakfast';
$this->objbreakfast  = $this->newObject('label','htmlelements');
$this->objbreakfast->label($breakfast,$lblBreakfast);

$lunch  = $this->objLanguage->languageText('word_lunch');
$lblLunch = 'lblLunch';
$this->objlunch  = $this->newObject('label','htmlelements');
$this->objlunch->label($lunch,$lblLunch);

$dinner = $this->objLanguage->languageText('word_dinner');
$lblDinner = 'lblDinner';
$this->objdinner = $this->newObject('label','htmlelements');
$this->objdinner->label($dinner,$lblDinner);

$location = $this->objLanguage->languageText('word_location');
$lblLocation = 'lblLocation';
$this->objLocation = $this->newObject('label','htmlelements');
$this->objLocation->label($location,$lblLocation);

$rate = $this->objLanguage->languageText('word_rate');
$lblRate = 'lblRate';
$this->objrate = $this->newObject('label','htmlelements');
$this->objrate->label($rate,$lblRate);

/**************************************************************************************************************/
/**
 *get the inital departure date from 1st leg of itinerary and to display on the form
 */
//$this->unsetSession('addmultiitinerary');
$itineraryinfo = $this->getSession('addmultiitinerary');
$initial =  $itineraryinfo[0]['departuredate'];

/**
 *get the arrival date from the itinerary from the last leg of the itinerary
 */
$count = count($itineraryinfo);
$num = $count - 1;
$last = $itineraryinfo[$num]['departuredate'];

//get the days in between initial and last date of departure
$this->objDateFunctions = $this->getObject('dateandtime','utilities');
$datesBetween = array();

for($i = $initial; $i <= $last; $i = $this->objDateFunctions->nextDay($i)){
  $datesBetween[] = $i;
}

//Create a dropdown list and populate with all values of expenses for each day of the travel leg
$this->loadClass('dropdown', 'htmlelements');
$expDates = new dropdown('expdate');
//Get exp dates already in sesion
$sessionDates = $this->getSession('perdiemdetails');
//Create arrays for dates not in session
$notInSession = array();

if(!empty($sessionDates)){
  foreach($datesBetween as $dbtwn){
     $isIn = 'no';
     foreach($sessionDates as $sd){
        if($dbtwn == $sd['date']){
          $isIn = 'yes';
        }
     }
     if($isIn == 'no'){
       $notInSession[] = $dbtwn;
     }
  }
} else {
    $notInSession = $datesBetween;
}
//Add dates not yet in session to dropdown
foreach($notInSession as $ar){
   $expDates->addOption($ar, $this->objDateFunctions->reformatDateSmallMonth($ar));
}
/*************************************************************************************************************************************************************/

//create all form buttons
$next  = $this->objLanguage->languageText('phrase_next');
$strnext = ucfirst($next);
$this->loadclass('button','htmlelements');
$this->objButtonNext  = new button('saveperdiem', $strnext);
$this->objButtonNext->setToSubmit();

$exit  = $this->objLanguage->languageText('phrase_exit');
$strexit = ucfirst($exit);

$this->objButtonExit  = new button('exitform', $strexit);
$this->objButtonExit->setToSubmit();

$this->objBack  = new button('back', $back);
$this->objBack->setToSubmit();

$btnadd  = $this->objLanguage->LanguageText('mod_onlineinvoice_addperdiem','onlineinvoice');
$stradd = ucfirst($btnadd);
$this->objAddperdiem  = new button('addperdiem', $stradd);
$this->objAddperdiem->setToSubmit();
/**************************************************************************************************************/

//create all checkboxes

$this->loadClass('checkbox', 'htmlelements');
$objB = new checkbox('b');
$objB->checkbox('breakfast',$breakfast,$ischecked=true);
$checkbreak= $objB->show();

$objL = new checkbox('l');
$objL->checkbox('lunch',$lunch,$ischecked=false);
$checklunch= $objL->show();

$objD = new checkbox('d');
$objL->checkbox('dinner',$dinner,$ischecked=false);
$checkdinner= $objD->show();
/*****************************************************************************************************************/

//$this->objexpensesdate = $this->newObject('datepicker','htmlelements');
//$name = 'txtexpensesdate';
//$date = date('Y-m-d');
//$format = 'YYYY-MM-DD';
//$this->objexpensesdate->setName($name);
//$this->objexpensesdate->setDefaultDate($date);
//$this->objexpensesdate->setDateFormat($format);

/************************************************************************************************************/
  /**
   *create all text inputs elements
   */

  //get the depature city form itinerary and use as default location for the 1st leg only


  //$valdeptlocation  = $this->getParam('txtdeptcity');

$this->objtxtbreakfastloc = $this->newObject('textinput','htmlelements');
$this->objtxtbreakfastloc->name   = "txtbreakfastLocation";
$this->objtxtbreakfastloc->value  = "";

$this->objtxtbreakfastrate = $this->newObject('textinput','htmlelements');
$this->objtxtbreakfastrate->name   = "txtbreakfastRate";
$this->objtxtbreakfastrate->value  = "0.00";

$this->objtxtlunchloc = $this->newObject('textinput','htmlelements');
$this->objtxtlunchloc->name   = "txtlunchLocation";
$this->objtxtlunchloc->value  = "";

$this->objtxtlunchrate = $this->newObject('textinput','htmlelements');
$this->objtxtlunchrate->name   = "txtlunchRate";
$this->objtxtlunchrate->value  = "0.00";

$this->objtxtdinnerloc = $this->newObject('textinput','htmlelements');
$this->objtxtdinnerloc->name   = "txtdinnerLocation";
$this->objtxtdinnerloc->value  = "";

$this->objtxtdinnerrate = $this->newObject('textinput','htmlelements');
$this->objtxtdinnerrate->name   = "txtdinnerRate";
$this->objtxtdinnerrate->value  = "0.00";

/**************************************************************************************************************/
//Radio button Group -- used to determine whether rate is foreign or domestic
  $this->loadClass('radio','htmlelements');

    $objRates = new radio('rates_radio');
    $objRates->addOption('foreign','Foreign Rate');
    $objRates->addOption('domestic','Domestic Rate');
    $objRates->setSelected('foreign');

/**************************************************************************************************************/
//create a table to place form LABELS in

        $myTablabel  = $this->newObject('htmltable','htmlelements');
        $myTablabel->width='80%';
        $myTablabel->border='0';
        $myTablabel->cellspacing = '1';
        $myTablabel->cellpadding ='10';

        $myTablabel->startRow();
        $myTablabel->addCell("<div align=\"left\">" .$this->objforeignheading->show() . "</div>");
        $myTablabel->endRow();
        $myTablabel->startRow();
        $myTablabel->addCell("<div align=\"left\">" .$this->objdomesticheading->show() . "</div>");
        $myTablabel->endRow();

/**************************************************************************************************************/

        //create a table to place form elements in

       $myTabExpenses  = $this->newObject('htmltable','htmlelements');
       $myTabExpenses->width='100%';
       $myTabExpenses->border='0';
       $myTabExpenses->cellspacing = '10';
       $myTabExpenses->cellpadding ='10';

       $myTabExpenses->startRow();
       $myTabExpenses->addCell(' ');
       $myTabExpenses->addCell(' ');
       $myTabExpenses->addCell(ucfirst('<b />' . 'First Date'));
       $myTabExpenses->addCell("<div class=\"warning\">" . $this->objDateFunctions->reformatDateSmallMonth($initial));
       $myTabExpenses->addCell(ucfirst('<b />'.'Last Date'));
       $myTabExpenses->addCell("<div class=\"warning\">" .$this->objDateFunctions->reformatDateSmallMonth($last));
       $myTabExpenses->addCell(' ');
       $myTabExpenses->addCell(' ');
       $myTabExpenses->addCell($displayhelp);
       $myTabExpenses->endRow();

       $myTabExpenses->startRow();
       $myTabExpenses->addCell(' ');
       $myTabExpenses->addCell(' ');
       $myTabExpenses->addCell('<b />' .'Date:');
//display dates dates in drop down if any dates else if no more dates then display msg
       if(count($notInSession) > '0'){
         $myTabExpenses->addCell($expDates->show());
       } else {
           $myTabExpenses->addCell('All dates added');
       }
       $myTabExpenses->endRow();

       $myTabExpenses->startRow();
       $myTabExpenses->endRow();

       $myTabExpenses->startRow();
       $myTabExpenses->addCell('');
       $myTabExpenses->addCell('');
       $myTabExpenses->addCell("<div align=\"left\">" .$objRates->show(), '', '', '', '', 'colspan = 3' );
       $myTabExpenses->endRow();

       $myTabExpenses->startRow();
       $myTabExpenses->endRow();

       $myTabExpenses->startRow();
       $myTabExpenses->endRow();

       $myTabExpenses->startRow();
       $myTabExpenses->addCell($this->objbreakfast->show());
       $myTabExpenses->addCell($checkbreak);
       $myTabExpenses->addCell($this->objLocation->show());
       $myTabExpenses->addCell($this->objtxtbreakfastloc->show());
       $myTabExpenses->addCell($this->objrate->show());
       $myTabExpenses->addCell($this->objtxtbreakfastrate->show());
       $myTabExpenses->endRow();

       $myTabExpenses->startRow();
       $myTabExpenses->addCell($this->objlunch->show());
       $myTabExpenses->addCell($checklunch);
       $myTabExpenses->addCell($this->objLocation->show());
       $myTabExpenses->addCell($this->objtxtlunchloc->show());
       $myTabExpenses->addCell($this->objrate->show());
       $myTabExpenses->addCell($this->objtxtlunchrate->show());
       $myTabExpenses->endRow();

       $myTabExpenses->startRow();
       $myTabExpenses->addCell($this->objdinner->show());
       $myTabExpenses->addCell($checkdinner);
       $myTabExpenses->addCell($this->objLocation->show());
       $myTabExpenses->addCell($this->objtxtdinnerloc->show());
       $myTabExpenses->addCell($this->objrate->show());
       $myTabExpenses->addCell($this->objtxtdinnerrate->show());
       $myTabExpenses->endRow();

       $myTabExpenses->startRow();
       $myTabExpenses->addCell('');
       $myTabExpenses->addCell('');
       $myTabExpenses->addCell('');
       $myTabExpenses->addCell('');
       $myTabExpenses->addCell('');
       if(count($notInSession) > '0'){
         $myTabExpenses->addCell($this->objAddperdiem->show());
       } else {
           $myTabExpenses->addCell('');
       }
       $myTabExpenses->endRow();

       /**
        *table form buttons
        */

        $myTabButtons  = $this->newObject('htmltable','htmlelements');
        $myTabButtons->width='20%';
        $myTabButtons->border='0';
        $myTabButtons->cellspacing = '10';
        $myTabButtons->cellpadding ='10';

        $myTabButtons->startRow();
        $myTabButtons->addCell($this->objButtonNext->show());
        $myTabButtons->addCell($this->objAddperdiem->show());
        $myTabButtons->endRow();

/**************************************************************************************************************/
/*create tabbox*/

$this->loadClass('tabbedbox', 'htmlelements');
$objtabbedbox = new tabbedbox();
$objtabbedbox->addTabLabel('Per Diem Expenses');
$objtabbedbox->addBoxContent($myTabExpenses->show(). "<div align=\"center\">".$this->objBack->show()  . " " .$this->objButtonNext->show().'<br>'.'<br>' . "</div>") ;

/**************************************************************************************************************/

$this->objButtonExit  = new button('exitform', $strexit);
$this->objButtonExit->setToSubmit();

$this->loadClass('form','htmlelements');
$objForm = new form('expenses',$this->uri(array('action'=>'submitexpenses')));
$objForm->displayType = 3;
$objForm->addToForm($objtabbedbox->show());
$objForm->addRule('txtbreakfastRate','Rate must be numeric ','numeric');
$objForm->addRule('txtlunchRate','Rate must be numeric ','numeric');
$objForm->addRule('txtdinnerRate','Rate must be numeric ','numeric');


/**************************************************************************************************************/

if(!empty($sessionDates)){
//Create table to display dates in session and the rates for breakfast, lunch and dinner and the total rate
  $objExpByDateTable =& $this->newObject('htmltable', 'htmlelements');
  $objExpByDateTable->cellspacing = '2';
  $objExpByDateTable->cellpadding = '2';
  $objExpByDateTable->border='1';
  $objExpByDateTable->width = '100%';

  $objExpByDateTable->startHeaderRow();
  $objExpByDateTable->addHeaderCell('Date ');
  $objExpByDateTable->addHeaderCell('Breakfast Location' );
  $objExpByDateTable->addHeaderCell('Breakfast Rate');
  $objExpByDateTable->addHeaderCell('Lunch Location');
  $objExpByDateTable->addHeaderCell('Lunch Rate');
  $objExpByDateTable->addHeaderCell('Dinner Location');
  $objExpByDateTable->addHeaderCell('Dinner Rate');
  $objExpByDateTable->addHeaderCell('Total');
  $objExpByDateTable->endHeaderRow();


  $rowcount = '0';

  foreach($sessionDates as $sesDat){

     $oddOrEven = ($rowcount == 0) ? "odd" : "even";

     $objExpByDateTable->startRow();
     $objExpByDateTable->addCell($sesDat['date'], '', '', '', $oddOrEven);
     $objExpByDateTable->addCell($sesDat['blocation'], '', '', '', $oddOrEven);
     $objExpByDateTable->addCell($sesDat['btrate'], '', '', '', $oddOrEven);
     $objExpByDateTable->addCell($sesDat['llocation'], '', '', '', $oddOrEven);
     $objExpByDateTable->addCell($sesDat['lRate'], '', '', '', $oddOrEven);
     $objExpByDateTable->addCell($sesDat['dlocation'], '', '', '', $oddOrEven);
     $objExpByDateTable->addCell($sesDat['drrate'], '', '', '', $oddOrEven);
     $objExpByDateTable->addCell($sesDat['total'], '', '', '', $oddOrEven);
     $objExpByDateTable->endRow();
  }
}
//Display out to the screen

echo  "<div align=\"center\">" . $this->objMainheading->show() . "</div>";
echo  "<div align=\"center\">" . $myTablabel->show() . "</div>";
echo '<br />'.  "<div align=\"left\">"  . $objForm->show() . "</div>";

if(!empty($sessionDates)){
  echo "<div align=\"left\">" . $objExpByDateTable->show() . "</div>";
}



?>

