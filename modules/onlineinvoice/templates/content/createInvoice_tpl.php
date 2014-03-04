<?php

/**
 *this template contains the content for creating the initial invoice
 */
  

/**
 *create the heading of the template
 *the heading is created by creating an instance of the heading class
  there after the heading is set to type 2
  and is then assigned the title -- WEB BASED INVOICING SYSTEM
 */
$this->objMainheading =& $this->getObject('htmlheading','htmlelements');
$this->objMainheading->type=1;
$this->objMainheading->str=$objLanguage->languageText('mod_onlineinvoice_webbasedinvoicingsystem','onlineinvoice');

/**
  *help information
  */   
$instruction    = strtoupper($this->objLanguage->languageText('mod_onlineinvoice_instruction','onlineinvoice'));
$dateselection  = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_selectdates','onlineinvoice'));
$datesubmission = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_submitdates','onlineinvoice'));
$editdates  = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_editdates','onlineinvoice')); 
$explanationTEV  = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_tevexplanation','onlineinvoice'));
$category = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_catexplanation','onlineinvoice'));   

$helpstring = $instruction . '<br />' . $dateselection . '<br />' .$datesubmission . '<br />' .$editdates . '<br />'  . $explanationTEV . '<br />'  . $category;
$this->objHelp=& $this->getObject('helplink','help');
$displayhelp  = $this->objHelp->show($helpstring);


/**************************************************************************************************************************************************************/
/**
 *determine if the session contianing inv dates is empty or not
 *if empty then user cannot move to next pg or cat before submitting inv dates
 */  
//$invoicedata = array(); 
$invoicedata  = $this->getSession('invoicedata');
if(!empty($invoicedata)){
  $hasSubmitted = 'yes';
} else {
    $hasSubmitted = 'no'; 
}
/**************************************************************************************************************************************************************/
/**
 *create all language elements for labels
 */
$dateRange = $this->objLanguage->languageText('mod_onlineinvoice_whatisthedaterangeofyourinvoice','onlineinvoice') ;
$beginDate = $this->objLanguage->languageText('phrase_begindate');
$endDate  = $this->objLanguage->languageText('phrase_enddate');
$travelExpenses = $this->objLanguage->languageText('mod_onlineinvoice_travelexpensesupdate','onlineinvoice');
$btnSubmit  = $this->objLanguage->languageText('word_submit');
$str1 = ucfirst($btnSubmit);
$btnEdit  = $this->objLanguage->languageText('word_edit');
$str2 = ucfirst($btnEdit);
$createTEV  = $this->objLanguage->languageText('phrase_yescreatenewtev');
$nextCategory  = $this->objLanguage->languageText('phrase_nomovetonextcategory');
$error_message = $this->objLanguage->languageText('phrase_dateerror');
$strerror  =  ucfirst($error_message);
$strsucessfull = $this->objLanguage->languageText('mod_onlineinvoice_valuessubmitted','onlineinvoice');
$sucessfull = ucfirst($strsucessfull);
$tooltipcontent = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_tooltipcontent','onlineinvoice'));
$nextcatcontent = $this->objLanguage->languageText('mod_onlineinvoice_nextcaption','onlineinvoice');
$alertmsg = $this->objLanguage->languageText('mod_onlineinvoice_alertmsg','onlineinvoice');

/*********************************************************************************************************************************************************/
/**
 *create new instance of the label class for each form label
 */
 
//Label -- What is the date range of your invoice
$lblName  = 'lbldate';
$this->objDateRange  = $this->newObject('label','htmlelements');
$this->objDateRange->setLabel($dateRange);
$this->objDateRange->setForId($lblName);
//Label --  Begin Date
$lblBName  = 'lblBegin';
$this->objBegin  = $this->newObject('label','htmlelements');
$this->objBegin->setLabel($beginDate);
$this->objBegin->setForId($lblBName);
//label --  End Date
$lblTravelID = 'lblEnd';
$this->objEndDate  = $this->newObject('label','htmlelements');
$this->objEndDate->label($endDate,$lblTravelID);
//Label -- Do you have travel expenses to input at this time
$lblTravelID = 'blTravel';
$this->objtravelExpenses  = $this->newObject('label','htmlelements');
$this->objtravelExpenses->label($travelExpenses,$lblTravelID);

/*********************************************************************************************************************************************************/

/**
 *create link to start capturing travel expenses
 *checks that before moving to next section -- user needs to submit the invoice dates 1st 
 */ 

$urltext = $createTEV;
$content = $tooltipcontent;
$caption = '';
if($hasSubmitted == 'yes'){
  $url = $this->uri(array('action'=>'createtev'));
  $this->objcreatelink  = & $this->newObject('mouseoverpopup','htmlelements');
  $this->objcreatelink->mouseoverpopup($urltext,$content,$caption,$url);
} else {
  $url = $this->uri(array('action'=>'initialinvoice', 'submitdatesmsg' => 'yes'));
  $this->objcreatelink  = & $this->newObject('mouseoverpopup','htmlelements');
  $this->objcreatelink->mouseoverpopup($urltext,$content,$caption,$url);
}

$urltext = $nextCategory;
$content = $nextcatcontent;
$caption = '';
if($hasSubmitted == 'yes'){
  $url = $this->uri(array('action'=>'createservice'));
  $this->objnextlink  = & $this->newObject('mouseoverpopup','htmlelements');
  $this->objnextlink->mouseoverpopup($urltext,$content,$caption,$url);
} else {
  $url = $this->uri(array('action'=>'initialinvoice', 'submitdatesmsg' => 'yes'));
  $this->objnextlink  = & $this->newObject('mouseoverpopup','htmlelements');
  $this->objnextlink->mouseoverpopup($urltext,$content,$caption,$url);
}

/*********************************************************************************************************************************************************/


/**
  *create an instance of the datepicker class to create a calendar control
  *used for inv begin date and inv end date  
  */
  
$this->objbegindate = $this->newObject('datepicker','htmlelements');
$name = 'txtbegindate';
$date = date('Y-m-d');
$format = 'YYYY-MM-DD';
$this->objbegindate->setName($name);
$this->objbegindate->setDefaultDate($date);
$this->objbegindate->setDateFormat($format);

$this->objenddate = $this->newObject('datepicker','htmlelements');
$name = 'txtenddate';
$date = date('Y-m-d');
$format = 'YYYY-MM-DD';
$this->objenddate->setName($name);
$this->objenddate->setDefaultDate($date);
$this->objenddate->setDateFormat($format);

/*********************************************************************************************************************************************************/

/**
 *create all form buttons
 */
 
/*button -submit*/
$this->loadclass('button','htmlelements');
$this->objButtonSubmit  = new button('submit', $str1);
$this->objButtonSubmit->setToSubmit();

/**
  *validate the date fields using javascript
  *check that the start date of the invoice is not later than the end date
  */         
	$onClick = 'var list_from = document.invoice.txtbegindate;
					    var list_to = document.invoice.txtenddate;
					 
					 
					 
					    var acceptance = true;
					   //value of the begin date
  					 var value_begin = list_from.value;
	   				 //value of the end date
		  			 var value_end = list_to.value;
					 
					 
					 //checks if dates are right
					 if(value_begin > value_end){
					 	acceptance = false;
						
					 }
					 
							 
					 //check final condition
					 if(!acceptance){
					 	alert(\''.$strerror .'\');
						acceptance = true;
						return false;
					 }else{
       //    alert(\''.$sucessfull.'\')
           }';
	$this->objButtonSubmit->extra = sprintf(' onClick ="javascript: %s"', $onClick );
 
$this->objButtonEdit  = new button('edit', $str2);
$this->objButtonEdit->setToSubmit();
/*********************************************************************************************************************************************************/ 

/*create table to place form elements in  --  date values*/
        $myTable=$this->newObject('htmltable','htmlelements');
        $myTable->width='100%';
        $myTable->border='0';
        $myTable->cellspacing='1';
        $myTable->cellpadding='10';


        $myTable->startRow();
        $myTable->addCell($this->objBegin->show());
        $myTable->addCell($this->objbegindate->show());
        $myTable->addCell(' ');
        $myTable->addCell(' ');
        $myTable->addCell(' ');
        $myTable->addCell(' ');
        $myTable->addCell(' ');
        $myTable->addCell(' ');
        $myTable->addCell($displayhelp); 
        $myTable->endRow();

        $myTable->startRow();
        $myTable->addCell($this->objEndDate->show());
        $myTable->addCell($this->objenddate->show());
        $myTable->endRow();

        $myTable->startRow();
        $myTable->endRow();

        $myTable->startRow();
        $myTable->endRow();

        $myTable->startRow();
        $myTable->endRow();

        $myTable->startRow();
        $myTable->endRow();

        $myTable->startRow();
        $myTable->addCell($this->objButtonSubmit->show());
        if(!empty($invoicedata)){
          $myTable->addCell($this->objButtonEdit->show());
        }
        $myTable->endRow();

/*********************************************************************************************************************************************************/        

/*create table to place form elements in  --  create TEV OR move to next category*/        

        $myTab=$this->newObject('htmltable','htmlelements');
        $myTab->width='60%';
        $myTab->border='0';
        $myTab->cellspacing='10';
        $myTab->cellpadding='10';

        $myTab->startRow();
        $myTab->addCell($this->objcreatelink->show());
        $myTab->addCell($this->objnextlink->show());
        $myTab->endRow();

/*********************************************************************************************************************************************************/        

$this->loadClass('form','htmlelements');
$objcreateInvoiceForm = new form('invoice',$this->uri(array('action'=>'submitinvoicedates')));
$objcreateInvoiceForm->displayType = 3;
$objcreateInvoiceForm->addToForm($myTable->show()  .  '<br />' . '<br />' . $this->objtravelExpenses->show()  . '<br />'  . $myTab->show());


/*********************************************************************************************************************************************************/
/*create tabbox for intial invoice*/
$this->loadClass('tabbedbox', 'htmlelements');
$objcreateinvtab = new tabbedbox();
$objcreateinvtab->addTabLabel('Create Invoice');
$objcreateinvtab->addBoxContent('<br />'  . $this->objDateRange->show() . '<br />'. '<br />'  . $objcreateInvoiceForm->show());

/*********************************************************************************************************************************************************/
/**
 *determines if the session if the session variable containing the invoice dates is empty or not
 */  
 //$invdates  = $this->getSession('invoicedata');
 if(!empty($invoicedata)){
//Create table to display dates in session and the rates for breakfast, lunch and dinner and the total rate 
  $objDateTable =& $this->newObject('htmltable', 'htmlelements');
  $objDateTable->cellspacing = '2';
  $objDateTable->cellpadding = '2';
  $objDateTable->border='1';
  $objDateTable->width = '100%';
  
  $objDateTable->startHeaderRow();
  $objDateTable->addHeaderCell('Begin Date ');
  $objDateTable->addHeaderCell('End Date ');
  $objDateTable->endHeaderRow();
  
  
  $objDateTable->startRow();
  $objDateTable->addCell($invoicedata['begindate'], '', '', '', '');
  $objDateTable->addCell($invoicedata['enddate'], '', '', '', '');
  $objDateTable->endRow();
}

/***************************************************************************************************************************************************************/


/*Display output to screen*/
echo  "<div align=\"center\">" . $this->objMainheading->show() . "</div>";

/** 
 *Create timeout message to inform user that they need to submit inv dates
 */ 
//$submitdatesmsg = '';
if($submitdatesmsg == 'yes'){
  $tomsg =& $this->newObject('timeoutmessage', 'htmlelements');
  $tomsg->setMessage($alertmsg);
  
  echo $tomsg->show();
}

echo  '<br />';
echo  '<br />';
echo  '<br />'  . $objcreateinvtab->show();
if(!empty($invoicedata)){
//var_dump($invoicedata);
  echo "<div align=\"left\">" . $objDateTable->show() . "</div>";
}
?>



