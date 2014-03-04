<?php
      /**
       *create a template for uplading the files
       */
       

/*create template for lodging expenses*/
$this->objlodgeHeading = $this->newObject('htmlheading','htmlelements');
$this->objlodgeHeading->type = 2;
$this->objlodgeHeading->str=$objLanguage->languageText('mod_onlineinvoice_travellodgingexpenses','onlineinvoice');
/*********************************************************************************************************************************************************************/
$exchangeRate  = '<a href=http://www.oanda.com/convert/classic/>www.oanda.com/convert/classic</a>';
$lodgeHint  = $this->objLanguage->languageText('mod_onlineinvoice_pleasemouseover','onlineinvoice');
$lodgeExchangeRate = $this->objLanguage->languageText('mod_onlineinvoice_anyexchangerate','onlineinvoice');
$lodgeSuggestedExRate = $this->objLanguage->languageText('mod_onlineinvoice_suggestedexchangerate','onlineinvoice')  . ' ' . $exchangeRate;
$lodgeExpenditures = $this->objLanguage->languageText('mod_onlineinvoice_itemizedexpenditures','onlineinvoice');

$lblchoice  = $this->objLanguage->languageText('mod_onlineinvoice_verifyexchangerate','onlineinvoice');
$addreceipt = $this->objLanguage->languageText('mod_onlineinvoice_addreceipt','onlineinvoice');
$quotesource  = $this->objLanguage->languageText('mod_onlineinvoice_quotesource','onlineinvoice');
$strquote = ucfirst($quotesource);
$receipt  = $this->objLanguage->languageText('mod_onlineinvoice_uploadreceipts','onlineinvoice');
$upload = $this->objLanguage->languageText('mod_onlineinvoice_uploadbutton','onlineinvoice');
$create = $this->objLanguage->languageText('mod_onlineinvoice_create','onlineinvoice');
$next = $this->objLanguage->languageText('phrase_next');
$exit = $this->objLanguage->languageText('phrase_exit');

/*********************************************************************************************************************************************************************/
$this->objtxtbrowse = $this->newobject('textinput','htmlelements');
$this->objtxtbrowse->textinput("txtexchange","","text");
$this->objtxtbrowse->size = 40;

$this->loadClass('textinput', 'htmlelements');
$this->objtxtfilereceipt = new textinput('txtfilereceipt',' ','FILE');
$this->objtxtfilereceipt->id = 'txtfilereceipt';

$this->loadClass('textinput', 'htmlelements');
$this->objtxtfilerate = new textinput('txtfilerate',' ','FILE');
$this->objtxtfilerate->id = 'txtfilerate';

$strupload = ucfirst($upload);
$this->objtxtquotesource  = new textinput('txtquotesource', ' ','text');
$this->objtxtquotesource->id = 'txtquotesource';

$this->objtxtcreateaffidavit  = new textinput('txtcreateaffidavit', ' ','text');
$this->objtxtcreateaffidavit->id = 'txtcreateaffidavit';

/*********************************************************************************************************************************************************************/
$this->loadclass('button','htmlelements');

$strfile = ucfirst($addreceipt);
$this->objButtonAdd  = new button('addreceipt', $strfile);
$this->objButtonAdd->setToSubmit();

$strquote = ucfirst($quotesource);
$this->objButtonQuote  = new button('quotesource', $strquote);
$this->objButtonQuote->setToSubmit();

$strupload = ucfirst($upload);
$this->objButtonUploadReceipt  = new button('uploadfiles', $strupload);
$this->objButtonUploadReceipt->setToSubmit();

$btnsave  = $this->objLanguage->languageText('word_save');
$strsave  = ucfirst($btnsave);
$this->objButtonSubmit  = new button('submit', $strsave);
$this->objButtonSubmit->setToSubmit();

/*$btnsave  = $this->objLanguage->languageText('word_save');
$strsave  = ucfirst($btnsave);
$this->objButtonSubmit  = new button('submit', $strsave);
$this->objButtonSubmit->setToSubmit();*/

$this->objnext  = new button('next', $next);
$this->objnext->setToSubmit();

$this->objexit  = new button('exit', $exit);
$this->objexit->setToSubmit();

$strcreate  = ucfirst($create);
$this->objButtonCreate  = new button('create', $strcreate);
$this->objButtonCreate->setToSubmit();/*create table for exchangerate information*/
/***************************************************************************************************************************/
        /**
 *create a table for lodge details  - place all form headings in*/

        $myTabLodgeheading  = $this->newObject('htmltable','htmlelements');
        $myTabLodgeheading->width='80%';
        $myTabLodgeheading->border='0';
        $myTabLodgeheading->cellspacing = '10';
        $myTabLodgeheading->cellpadding ='10';

        $myTabLodgeheading->startRow();
        $myTabLodgeheading->addCell("<div align=\"left\">"  . $lodgeHint . "</div>");
        $myTabLodgeheading->endRow();
        
        $myTabLodgeheading->startRow();
        $myTabLodgeheading->addCell("<div align=\"left\">"  . $lodgeExchangeRate  . "</div>");
        $myTabLodgeheading->endRow();
        
        $myTabLodgeheading->startRow();
        $myTabLodgeheading->addCell("<div align=\"left\">"  . $lodgeSuggestedExRate . "</div>");
        $myTabLodgeheading->endRow();
        
        $myTabLodgeheading->startRow();
        $myTabLodgeheading->addCell("<div align=\"left\">"  . $lodgeExpenditures  . "</div>");
        $myTabLodgeheading->endRow();


/***************************************************************************************************************************/

        $myTabExchange  = $this->newObject('htmltable','htmlelements');
        $myTabExchange->width='90%';
        $myTabExchange->border='0';
        $myTabExchange->cellspacing = '10';
        $myTabExchange->cellpadding ='10';

        $myTabExchange->startRow();
        $myTabExchange->addCell(ucfirst('Attach file')); 
        $myTabExchange->addCell($this->objtxtfilerate->show());
        $myTabExchange->endRow();
        
        $myTabExchange->startRow();
        $myTabExchange->addCell($strquote);
        $myTabExchange->addCell($this->objtxtquotesource->show());
        $myTabExchange->endRow();


/*create table for exchangerate information*/        

        $myTabReceipt  = $this->newObject('htmltable','htmlelements');
        $myTabReceipt->width='75%';
        $myTabReceipt->border='0';
        $myTabReceipt->cellspacing = '10';
        $myTabReceipt->cellpadding ='10';
        
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell('Upload Receipt');
        $myTabReceipt->addCell($this->objtxtfilereceipt->show());
        $myTabReceipt->endRow();
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell('Create an Affidavit');
        $myTabReceipt->addCell($this->objtxtcreateaffidavit->show());
        $myTabReceipt->endRow();
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell(' ');
        $myTabReceipt->addCell($this->objButtonUploadReceipt->show());
        $myTabReceipt->endRow();
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell($this->objnext->show() . ' ' . $this->objexit->show()  );
        //$myTabReceipt->addCell($this->objexit->show());
        $myTabReceipt->endRow();
        
        

/*********************************************************************************************************************************************************************/        
/*create tabbox for attaching lodge echange rate file*/
$this->loadClass('tabbedbox', 'htmlelements');
$objtabexchange = new tabbedbox();
$objtabexchange->addTabLabel('Exchange Rate Information and Receipt Information');
$objtabexchange->addBoxContent($myTabExchange->show().'<br>'  . $receipt  . '<br>' ."<div align=\"left\">"  .$myTabReceipt->show());


/*********************************************************************************************************************************************************************/
//ceate a seperate form to upload all receipt and exchange rate information

//$formlodging = '<form name="lodging" id="lodging" enctype="multipart/form-data" method="post" action="'.$this->formaction.'">';
$objLodgeFormFiles = new form('lodging',$this->uri(array('action'=>'submitlodgefiles')));
$objLodgeFormFiles->displayType = 3;
$objLodgeFormFiles->addToForm('<br>' . $objtabexchange->show());	
$objLodgeFormFiles->addRule('txtvendor', 'Must be number','required');
  
/**********************************************************************************************************************************************************************/
//display screen content
echo "<div align=\"center\">" . $this->objlodgeHeading->show()  . "</div>";
echo "<div align=\"center\">" . '<br>'  . $myTabLodgeheading->show() . "</div>";
echo  "<div align=\"left\">"  . $objLodgeFormFiles->show() . "</div";
?>
