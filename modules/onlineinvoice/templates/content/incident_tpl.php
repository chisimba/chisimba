<?php


/*create template for lodging expenses*/
$this->objlodgeHeading = $this->newObject('htmlheading','htmlelements');
$this->objlodgeHeading->type = 2;
$this->objlodgeHeading->str=$objLanguage->languageText('mod_onlineinvoice_incidentexpense','onlineinvoice');
/*********************************************************************************************************************************************************************/

$exchangeRate  = '<a href=http://www.oanda.com/convert/classic/>www.oanda.com/convert/classic</a>';
$lodgeHint  = $this->objLanguage->languageText('mod_onlineinvoice_pleasemouseover','onlineinvoice');
$lodgeExchangeRate = $this->objLanguage->languageText('mod_onlineinvoice_anyexchangerate','onlineinvoice');
$lodgeSuggestedExRate = $this->objLanguage->languageText('mod_onlineinvoice_suggestedexchangerate','onlineinvoice')  . ' ' . $exchangeRate;
$lodgeExpenditures = $this->objLanguage->languageText('mod_onlineinvoice_itemizedexpenditures','onlineinvoice');
$vendor = $this->objLanguage->languageText('word_vendor');
$currency = $this->objLanguage->languageText('word_currency');
$cost = $this->objLanguage->languageText('word_cost');
$rate = $this->objLanguage->languageText('mod_onlineinvoice_exchangerate','onlineinvoice');  
$lblchoice  = $this->objLanguage->languageText('mod_onlineinvoice_verifyexchangerate','onlineinvoice');
$attachfile = $this->objLanguage->languageText('mod_onlineinvoice_attachfile','onlineinvoice');
$quotesource  = $this->objLanguage->languageText('mod_onlineinvoice_quotesource','onlineinvoice');
$strquote = ucfirst($quotesource);
$receipt  = $this->objLanguage->languageText('mod_onlineinvoice_uploadreceipts','onlineinvoice');
$uploadreceipt = $this->objLanguage->languageText('mod_onlineinvoice_uploadbutton','onlineinvoice');
$create = $this->objLanguage->languageText('mod_onlineinvoice_create','onlineinvoice');
$next = $this->objLanguage->languageText('phrase_next');

/*********************************************************************************************************************************************************************/
$expensesdate = $this->objLanguage->languageText('word_date');
$lblDate = lbldate;
$this->objdate  = $this->newObject('label','htmlelements');
$this->objdate->label($expensesdate,$lblDate);



$this->objlodgedate = $this->newObject('datepicker','htmlelements');
$name = 'txtlodgedate';
$date = '01-01-2006';
$format = 'DD-MM-YYYY';
$this->objlodgedate->setName($name);
$this->objlodgedate->setDefaultDate($date);
$this->objlodgedate->setDateFormat($format);



$this->objtxtvendor = $this->newobject('textinput','htmlelements');
$this->objtxtvendor->textinput("txtvendor","",'text');

//$this->objtxtcurrency  = $this->newobject('textinput','htmlelements');
//$this->objtxtcurrency->textinput("txtcurrency","","text");

$this->objtxtcost  = $this->newobject('textinput','htmlelements');
$this->objtxtcost->textinput("txtcost","","text");

$this->objtxtexchange = $this->newobject('textinput','htmlelements');
$this->objtxtexchange->textinput("txtexchange","","text");

$this->objtxtbrowse = $this->newobject('textinput','htmlelements');
$this->objtxtbrowse->textinput("txtexchange","","text");
$this->objtxtbrowse->size = 40;

$this->loadClass('textinput', 'htmlelements');
$this->objtxtfile = new textinput('txtfile',' ','FILE');
$this->objtxtfile->id = 'txtfile';
/*********************************************************************************************************************************************************************/

   $currencyvals  = 'currency';
   $this->objcurrencydropdown  = $this->newObject('dropdown','htmlelements');
   $this->objcurrencydropdown->dropdown($currencyvals);
   $this->objcurrencydropdown->addOption('AED','AED') ;
   $this->objcurrencydropdown->addOption('ALL','ALL') ;
   $this->objcurrencydropdown->addOption('AMD','AMD') ;
   $this->objcurrencydropdown->addOption('ARS','ARS') ;
   $this->objcurrencydropdown->addOption('AUD','AUD') ;
   $this->objcurrencydropdown->addOption('AZM','AZM') ;
   $this->objcurrencydropdown->addOption('BGL','BGL') ;
   $this->objcurrencydropdown->addOption('BHD','BHD') ;
   $this->objcurrencydropdown->addOption('BND','BND') ;
   $this->objcurrencydropdown->addOption('BOB','BOB') ;
   $this->objcurrencydropdown->addOption('BRL','BRL') ;
   $this->objcurrencydropdown->addOption('BYB','BYB') ;
   $this->objcurrencydropdown->addOption('BZD','BZD') ;
   $this->objcurrencydropdown->addOption('CAD','CAD') ;
   $this->objcurrencydropdown->addOption('CHF','CHF') ;
   $this->objcurrencydropdown->addOption('CLP','CLP') ;
   $this->objcurrencydropdown->addOption('BRL','BRL') ;
   $this->objcurrencydropdown->addOption('CNY','CNY') ;
   $this->objcurrencydropdown->addOption('COP','COP') ;
   $this->objcurrencydropdown->addOption('CRC','CRC') ;
   $this->objcurrencydropdown->addOption('CZK','CZK') ;
   $this->objcurrencydropdown->addOption('DKK','DKK') ;
   $this->objcurrencydropdown->addOption('DOP','DOP') ;
   $this->objcurrencydropdown->addOption('DZD','DZD') ;
   $this->objcurrencydropdown->addOption('ECS','ECS') ;
   $this->objcurrencydropdown->addOption('EEK','EEK') ;
   $this->objcurrencydropdown->addOption('EGP','EGP') ;
   $this->objcurrencydropdown->addOption('EUR','EUR') ;
   $this->objcurrencydropdown->addOption('GBP','GBP') ;
   $this->objcurrencydropdown->size = 50;
   

/***********************************************************************************************************************************************************************/   

/*create all button elements*/


$this->loadclass('button','htmlelements');

$strfile = ucfirst($attachfile);
$this->objButtonAttach  = new button('attachfile', $strfile);
$this->objButtonAttach->setToSubmit();

$strquote = ucfirst($quotesource);
$this->objButtonQuote  = new button('quotesource', $strquote);
$this->objButtonQuote->setToSubmit();

$strupload = ucfirst($uploadreceipt);
$this->objButtonUploadReceipt  = new button('uploadreceipt', $strupload);
$this->objButtonUploadReceipt->setToSubmit();

$btnSubmit  = $this->objLanguage->languageText('word_submit');
$strsubmit  = ucfirst($btnSubmit);
$this->objButtonSubmit  = new button('submit', $strsubmit);
$this->objButtonSubmit->setToSubmit();

$strcreate  = ucfirst($create);
$this->objButtonCreate  = new button('create', $strcreate);
$this->objButtonCreate->setToSubmit();
/*********************************************************************************************************************************************************************/
//$this->objnext->link($this->uri(array('action'=>'createlodging')));
$this->objnext  =& $this->newobject('link','htmlelements');
$this->objnext->link($this->uri(array('action'=>'showclaimantoutput')));
$this->objnext->link = $next;

/*********************************************************************************************************************************************************************/
/*create a table for lodge details*/

        $myTabLodgeheading  = $this->newObject('htmltable','htmlelements');
        $myTabLodgeheading->width='80%';
        $myTabLodgeheading->border='0';
        $myTabLodgeheading->cellspacing = '10';
        $myTabLodgeheading->cellpadding ='10';

        $myTabLodgeheading->startRow();
        $myTabLodgeheading->addCell("<div align=\"left\">" .$lodgeHint. "</div>");
        $myTabLodgeheading->endRow();
        
        $myTabLodgeheading->startRow();
        $myTabLodgeheading->addCell("<div align=\"left\">" .$lodgeExchangeRate. "</div>");
        $myTabLodgeheading->endRow();
        
        $myTabLodgeheading->startRow();
        $myTabLodgeheading->addCell("<div align=\"left\">" .$lodgeSuggestedExRate. "</div>");
        $myTabLodgeheading->endRow();
        
        $myTabLodgeheading->startRow();
        $myTabLodgeheading->addCell("<div align=\"left\">" .$lodgeExpenditures. "</div>");
        $myTabLodgeheading->endRow();
/*********************************************************************************************************************************************************************/
/*create a table for lodge details*/

        $myTabLodge  = $this->newObject('htmltable','htmlelements');
        $myTabLodge->width='80%';
        $myTabLodge->border='0';
        $myTabLodge->cellspacing = '10';
        $myTabLodge->cellpadding ='10';

        $myTabLodge->startRow();
        $myTabLodge->addCell($expensesdate);
        $myTabLodge->addCell($this->objlodgedate->show());
        $myTabLodge->endRow();

        $myTabLodge->startRow();
        $myTabLodge->addCell($vendor);
        $myTabLodge->addCell($this->objtxtvendor->show());
        $myTabLodge->endRow();

        $myTabLodge->startRow();
        $myTabLodge->addCell('Description');
        $myTabLodge->addCell($this->objtxtcost->show());
        $myTabLodge->endRow();
        
        $myTabLodge->startRow();
        $myTabLodge->addCell($currency);
        $myTabLodge->addCell($this->objcurrencydropdown->show());
        $myTabLodge->endRow();
                
        $myTabLodge->startRow();
        $myTabLodge->addCell($cost);
        $myTabLodge->addCell($this->objtxtcost->show());
        $myTabLodge->endRow();


        

/*create table for exchangerate information*/

        $myTabExchange  = $this->newObject('htmltable','htmlelements');
        $myTabExchange->width='100%';
        $myTabExchange->border='0';
        $myTabExchange->cellspacing = '10';
        $myTabExchange->cellpadding ='10';

        $myTabExchange->startRow();
        $myTabExchange->addCell($rate);
        $myTabExchange->addCell($this->objtxtexchange->show());
        $myTabExchange->endRow();

        $myTabExchange->startRow();
        $myTabExchange->addCell(ucfirst($attachfile));
        $myTabExchange->addCell($this->objtxtfile->show());
        //$myTabExchange->addCell($this->objbuttonbrowse->show());
        $myTabExchange->addCell($this->objButtonAttach->show());
        $myTabExchange->endRow();
        
        $myTabExchange->startRow();
        $myTabExchange->addCell($strquote);
       // $myTabExchange->addCell($this->objtxtfile->show());
        //$myTabExchange->addCell($this->objbuttonbrowse->show());
        $myTabExchange->addCell($this->objButtonQuote->show());
        $myTabExchange->endRow();


/*create table for exchangerate information*/        

        $myTabReceipt  = $this->newObject('htmltable','htmlelements');
        $myTabReceipt->width='100%';
        $myTabReceipt->border='0';
        $myTabReceipt->cellspacing = '10';
        $myTabReceipt->cellpadding ='10';

        $myTabReceipt->startRow();
        $myTabReceipt->addCell('Upload Receipt');
        $myTabReceipt->addCell($this->objtxtfile->show());
        $myTabReceipt->addCell($this->objButtonUploadReceipt->show());
        $myTabReceipt->endRow();
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell('Create an Affidavit');
        $myTabReceipt->addCell($this->objButtonCreate->show());
        $myTabReceipt->endRow();
        
        //$myTabReceipt->startRow();
        //$myTabReceipt->addCell($rate);
       // $myTabReceipt->addCell($this->objtxtdate->show());
        //$myTabReceipt->endRow();        



//$this->objButtonbrowse->setToSubmit();

/*********************************************************************************************************************************************************************/        

/*create tabbox for lodge information*/
$this->loadClass('tabbedbox', 'htmlelements');
$objtabbedbox = new tabbedbox();
$objtabbedbox->addTabLabel('Incident Information');
$objtabbedbox->addBoxContent($myTabLodge->show());



/*create tabbox for attaching lodge echange rate file*/
$this->loadClass('tabbedbox', 'htmlelements');
$objtabexchange = new tabbedbox();
$objtabexchange->addTabLabel('Exchange Rate Information');
$objtabexchange->addBoxContent($myTabExchange->show());// . $lblchoice  . '<br>'. "<div align=\"left\">" . $this->objButtonAttach->show() . " " . $this->objButtonQuote->show() . "</div>" );



/*create tabbox for receipt information*/

$this->loadClass('tabbedbox', 'htmlelements');
$objtabreceipt = new tabbedbox();
$objtabreceipt->addTabLabel('Receipt Information');
$objtabreceipt->addBoxContent('<br>'  . $receipt  . '<br>' ."<div align=\"left\">"  .$myTabReceipt->show() );//. " " . $this->objButtonCreate->show(). "</div>" );

/*********************************************************************************************************************************************************************/

$this->loadClass('form','htmlelements');
//$formlodging = '<form name="lodging" id="lodging" enctype="multipart/form-data" method="post" action="'.$this->formaction.'">';
$objLodgeForm = new form('lodging',$this->uri(array('action'=>'submitlodgeexpenses')));

$objLodgeForm->displayType = 3;

$objLodgeForm->addToForm($objtabbedbox->show()  .  '<br>' . $objtabexchange->show()  . '<br>'  . $objtabreceipt->show() . '<br>' . $this->objButtonSubmit->show());	
//$formlodging = '<form name="lodging" id="lodging" enctype="multipart/form-data" method="post" action="'.$this->formaction.'">';
//$objLodgeForm->addRule('txtDate', 'Must be number','required'); 









//display screen content

echo "<div align=\"center\">" . $this->objlodgeHeading->show()  . "</div>";

echo "<div align=\"center\">" .'<br>'  . $myTabLodgeheading->show() . "</div>";
echo  "<div align=\"left\">"  . $objLodgeForm->show() . "</div";

//echo  '<br>'  . $this->objnext->show();;



?>

