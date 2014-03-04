<?php
      /**
       *create a template for lodge expenses the files
       */
       

$this->loadClass('windowpop','htmlelements');
$this->objPop=&new windowpop;
$this->objPop->set('window_name','Exchange Rate Information');
$this->objPop->set('location','<a href=http://www.oanda.com/convert/classic/>www.oanda.com</a>');
$this->objPop->set('linktext','www.oanda.com');
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

$exchangeRate = $this->objPop->show();

/*create template for lodging expenses*/
$this->objsupplyHeading = $this->newObject('htmlheading','htmlelements');
$this->objsupplyHeading->type = 2;
$this->objsupplyHeading->str=$objLanguage->languageText('mod_onlineinvoice_suppliesexpenses','onlineinvoice');
/*********************************************************************************************************************************************************************/

$exchangeRate  = '<a href=http://www.oanda.com/convert/classic/>www.oanda.com/convert/classic</a>';
$suppliesHint  = $this->objLanguage->languageText('mod_onlineinvoice_pleasemouseover','onlineinvoice');
$suppliesExchangeRate = $this->objLanguage->languageText('mod_onlineinvoice_anyexchangerate','onlineinvoice');
$suppliesSuggestedExRate = $this->objLanguage->languageText('mod_onlineinvoice_suggestedexchangerate','onlineinvoice')  . ' ' . $exchangeRate;
$suppliesExpenditure = $this->objLanguage->languageText('mod_onlineinvoice_suppliesexpenditures','onlineinvoice');
//$verifyRate = $this->objLanguage->LanguageText('mod_onlineinvoice_verifyexchangerate', 'onlineinvoice');
$expensesdate = $this->objLanguage->languageText('word_date');
$vendor = $this->objLanguage->languageText('word_vendor');
$descriptionSupplies = $this->objLanguage->LanguageText('mod_onlineinvoice_descriptionofsupplies', 'onlineinvoice');
$currency = $this->objLanguage->languageText('word_currency');
$cost = $this->objLanguage->languageText('word_cost');
$rate = $this->objLanguage->languageText('mod_onlineinvoice_exchangerate','onlineinvoice');
$next = $this->objLanguage->languageText('phrase_next');
$exit = $this->objLanguage->languageText('phrase_exit');
$back = $this->objLanguage->LanguageText('word_back');
$add = $this->objLanguage->LanguageText('word_add');
$receipt  = $this->objLanguage->languageText('mod_onlineinvoice_uploadreceipts','onlineinvoice');
$attachreceipt  = $this->objLanguage->languageText('mod_onlineinvoice_attachreceipt','onlineinvoice');
$createaffidavit  = $this->objLanguage->languageText('mod_onlineinvoice_createaffidavit','onlineinvoice');
$attachaffidavit  = $this->objLanguage->languageText('mod_onlineinvoice_attachaffidavit','onlineinvoice');
$uploadrate = $this->objLanguage->languageText('mod_onlineinvoice_uploadbutton','onlineinvoice');

$link = '<a href=http://www.myAffidavit.com/>www.myAffidavit.com</a>';
$createaffidavit = ucfirst($this->objLanguage->languageText('phrase_create'));
//$this->objHelp=& $this->getObject('helplink','help');
//$displayhelp  = $this->objHelp->show('mod_onlineinvoice_helpinstruction');
/**--**/

$this->objInfoIcon = $this->newObject('geticon','htmlelements');
$this->objInfoIcon->setModuleIcon('freemind');
  

/**
 *help information
 */
$suppliesinstruc  = $this->objLanguage->languageText('mod_onlineinvoice_suppliesinstruc','onlineinvoice');  
$suppliesinformation  = $this->objLanguage->languageText('mod_onlineinvoice_suppliesinformation','onlineinvoice');
$supplies  = $this->objLanguage->languageText('mod_onlineinvoice_supplies','onlineinvoice');
$suppliesexample  = $this->objLanguage->languageText('mod_onlineinvoice_suppliesexample','onlineinvoice');
$verifyexchangerate  = $this->objLanguage->languageText('mod_onlineinvoice_verifyexchangerate','onlineinvoice');
$exp  = $this->objLanguage->languageText('mod_onlineinvoice_exp','onlineinvoice');
$options  = $this->objLanguage->languageText('mod_onlineinvoice_options','onlineinvoice');
$suppliesreceiptinfo  = $this->objLanguage->languageText('mod_onlineinvoice_suppliesreceiptinfo','onlineinvoice');
$receiptexplanation  = $this->objLanguage->languageText('mod_onlineinvoice_receiptexplanation','onlineinvoice');
$receiptexp  = $this->objLanguage->languageText('mod_onlineinvoice_receiptexp','onlineinvoice');
$affidavitinstruc  = $this->objLanguage->languageText('mod_onlineinvoice_affidavitinstruc','onlineinvoice');

$helpstring = $suppliesinstruc . '<br />' .$suppliesinformation .'<br />' .$supplies . '<br />'  .$suppliesexample .'<br />'  . $verifyexchangerate . '<br />'  . $exp  . '<br />'  .$options.'<br />'.$suppliesreceiptinfo.'<br />'.$receiptexplanation.'<br />'.$receiptexp.'<br />'.$receiptexp.'<br />'.$affidavitinstruc;

$this->objHelp=& $this->getObject('helplink','help');
$displayhelp  = $this->objHelp->show($helpstring);



/*********************************************************************************************************************************************************************/

$lblDate = lbldate;
$this->objdate  = $this->newObject('label','htmlelements');
$this->objdate->label($expensesdate,$lblDate);

$initialdate [] = $this->getSession('invoicedata');
		if(!empty($initialdate)){
		
				foreach($initialdate as $sesSupp){
					$datevalue	=	$sesSupp['begindate'];
				}
			}	

$this->objsupplydate = $this->newObject('datepicker','htmlelements');
$name = 'txtsuppliesdate';
$date = $datevalue;
$format = 'YYYY-MM-DD';
$this->objsupplydate->setName($name);
$this->objsupplydate->setDefaultDate($date);
$this->objsupplydate->setDateFormat($format);

/**
 *create all text input boxes
 */ 

$this->objtxtvendor = $this->newobject('textinput','htmlelements');
$this->objtxtvendor->textinput("txtvendor","",'text');

$this->objtxtdescriptionSupplies = $this->newObject('textinput','htmlelements');
$this->objtxtdescriptionSupplies->textinput("txtdescriptionSupplies","",'text');

$this->objtxtcost  = $this->newobject('textinput','htmlelements');
$this->objtxtcost->textinput("txtcost","","text");

$this->objtxtexchange = $this->newobject('textinput','htmlelements');
$this->objtxtexchange->textinput("txtexchange","","text");

//added 13 sep
//$this->objtxtattachfile  = new textinput('upload', ' ','FILE');
//$this->objtxtattachfile->id = 'txttxtattachfile';
$objUploadFile = $this->newObject('selectfile', 'filemanager');
$objUploadFile->name = 'uploadrate'; 
$objUploadFile->context = false;
$objUploadFile->workgroup = false;

$objReceiptFile = $this->newObject('selectfile', 'filemanager');
$objReceiptFile->name = 'receiptfile'; 
$objReceiptFile->context = false;
$objReceiptFile->workgroup = false;
 
$objAffidavitFile = $this->newObject('selectfile', 'filemanager');
$objAffidavitFile->name = 'affidavitfile'; 
$objAffidavitFile->context = false;
$objAffidavitFile->workgroup = false;


$this->objtxtquotesource  = new textinput('txtquotesource', ' ','text');
$this->objtxtquotesource->id = 'txtquotesource';

/*********************************************************************************************************************************************************************/
    /**
     *create a dropdown list to hold all currency values
     */         
   $currencyvals  = 'currency';           
   $this->objcurrencydropdown  = $this->newObject('dropdown','htmlelements');
   $this->objcurrencydropdown->dropdown($currencyvals);
   $this->objcurrencydropdown->addOption('Afghanistan Afghani . AFA','Afghanistan Afghani . AFA') ;
   $this->objcurrencydropdown->addOption('Albanian Lek . ALL','Albanian Lek . ALL') ;
   $this->objcurrencydropdown->addOption('Algerian Dinar . DZD','Algerian Dinar . DZD') ;
   $this->objcurrencydropdown->addOption('Andorran Franc . ADF','Andorran Franc . ADF') ;
   $this->objcurrencydropdown->addOption('Andorran Peseta . ADP','Andorran Peseta . ADP') ;
   $this->objcurrencydropdown->addOption('Angolan Kwanza . AOA','Angolan Kwanza . AOA') ;
   $this->objcurrencydropdown->addOption('Angolan New Kwanza . AON','Angolan New Kwanza . AON') ;
   $this->objcurrencydropdown->addOption('Argentine Peso . ARS','Argentine Peso . ARS') ;
   $this->objcurrencydropdown->addOption('Armenian Dram . AMD','Armenian Dram . AMD') ;
   $this->objcurrencydropdown->addOption('Aruban Florin . AWG','Aruban Florin . AWG') ;
   $this->objcurrencydropdown->addOption('Australian Dollar . AUD','Australian Dollar . AUD') ;
   $this->objcurrencydropdown->addOption('Azerbaijan Manat . AZM','Azerbaijan Manat . AZM') ;
   $this->objcurrencydropdown->addOption('Austrian Schilling . ATS','Austrian Schilling . ATS') ;
   $this->objcurrencydropdown->addOption('Azerbaijan New Manat . AZN','Azerbaijan New Manat . AZN') ;
   $this->objcurrencydropdown->addOption('Bahamian Dollar . BSD','Bahamian Dollar . BSD') ;
   $this->objcurrencydropdown->addOption('Bahraini Dinar . BHD','Bahraini Dinar . BHD') ;
   $this->objcurrencydropdown->addOption('Bangladeshi Taka . BDT','Bangladeshi Taka . BDT') ;
   $this->objcurrencydropdown->addOption('Barbados Dollar . BBD','Barbados Dollar . BBD') ;
   $this->objcurrencydropdown->addOption('Belarusian Ruble . BYR','Belarusian Ruble . BYR') ;
   $this->objcurrencydropdown->addOption('Belgian Franc . BEF','Belgian Franc . BEF') ;
   $this->objcurrencydropdown->addOption('Belize Dollar . BZD','Belize Dollar . BZD') ;
   $this->objcurrencydropdown->addOption('Bermudian Dollar . BMD','Bermudian Dollar . BMD') ;
   $this->objcurrencydropdown->addOption('Bhutan Ngultrum . BTN','Bhutan Ngultrum . BTN') ;
   $this->objcurrencydropdown->addOption('Bolivian Boliviano . BOB','Bolivian Boliviano . BOB') ;
   $this->objcurrencydropdown->addOption('Bosnian Mark . BAM','Bosnian Mark . BAM') ;
   $this->objcurrencydropdown->addOption('Botswana Pula . BWP','Botswana Pula . BWP') ;
   $this->objcurrencydropdown->addOption('Brazilian Real . BRL','Brazilian Real . BRL') ;
   $this->objcurrencydropdown->addOption('British Pound . GBP','British Pound . GBP') ;
   $this->objcurrencydropdown->addOption('Brunei Dollar . BND','Brunei Dollar . BND') ;
   $this->objcurrencydropdown->addOption('Bulgarian Lev . BGN','Bulgarian Lev . BGN') ;
   $this->objcurrencydropdown->addOption('Burundi Franc . BIF','Burundi Franc . BIF') ;
   $this->objcurrencydropdown->addOption('CFA Franc BCEAO . XOF','CFA Franc BCEAO . XOF') ;
   $this->objcurrencydropdown->addOption('CFA Franc BEAC . XAF','CFA Franc BEAC . XAF') ;
   $this->objcurrencydropdown->addOption('CFP Franc . XPF','CFP Franc . XPF') ;
   $this->objcurrencydropdown->addOption('Cambodian Riel . KHR','Cambodian Riel . KHR') ;
   $this->objcurrencydropdown->addOption('Canadian Dollar . CAD','Canadian Dollar . CAD') ;
   $this->objcurrencydropdown->addOption('Cape Verde Escudo . CVE','Cape Verde Escudo . CVE') ;
   $this->objcurrencydropdown->addOption('Cayman Islands Dollar . KYD','Cayman Islands Dollar . KYD') ;
   $this->objcurrencydropdown->addOption('Chilean Peso . CLP','Chilean Peso . CLP') ;
   $this->objcurrencydropdown->addOption('Chinese Yuan Renminbi . CNY','Chinese Yuan Renminbi . CNY') ;
   $this->objcurrencydropdown->addOption('Colombian Peso . COP','Colombian Peso . COP') ;
   $this->objcurrencydropdown->addOption('Comoros Franc . KMF','Comoros Franc . KMF') ;
   $this->objcurrencydropdown->addOption('Congolese Franc . CDF','Congolese Franc . CDF') ;
   $this->objcurrencydropdown->addOption('Costa Rican Colon . CRC','Costa Rican Colon . CRC') ;
   $this->objcurrencydropdown->addOption('Croatian Kuna . HRK','Croatian Kuna . HRK') ;
   $this->objcurrencydropdown->addOption('Cuban Convertible Peso . CUC','Cuban Convertible Peso . CUC') ;
   $this->objcurrencydropdown->addOption('Cuban Peso . CUP','Cuban Peso . CUP') ;
   $this->objcurrencydropdown->addOption('Cyprus Pound . CYP','Cyprus Pound . CYP') ;
   $this->objcurrencydropdown->addOption('Czech Koruna . CZK','Czech Koruna . CZK') ;
   $this->objcurrencydropdown->addOption('Danish Krone . DKK','Danish Krone . DKK') ;
   $this->objcurrencydropdown->addOption('Djibouti Franc . DJF','Djibouti Franc . DJF') ;
   $this->objcurrencydropdown->addOption('Dominican R. Peso . DOP','Dominican R. Peso . DOP') ;
   $this->objcurrencydropdown->addOption('Dutch Guilder . NLG','Dutch Guilder . NLG') ;
   $this->objcurrencydropdown->addOption('ECU . XEU','ECU . XEU') ;
   $this->objcurrencydropdown->addOption('East Caribbean Dollar . XCD','East Caribbean Dollar . XCD') ;
   $this->objcurrencydropdown->addOption('Ecuador Sucre . ECS','Ecuador Sucre . ECS') ;
   $this->objcurrencydropdown->addOption('Egyptian Pound . EGP','Egyptian Pound . EGP') ;
   $this->objcurrencydropdown->addOption('El Salvador Colon . SVC','El Salvador Colon . SVC') ;
   $this->objcurrencydropdown->addOption('Estonian Kroon . EEK','Estonian Kroon . EEK') ;
   $this->objcurrencydropdown->addOption('Ethiopian Birr . ETB','Ethiopian Birr . ETB') ;
   $this->objcurrencydropdown->addOption('Euro . EUR','Euro . EUR') ;
   $this->objcurrencydropdown->addOption('Falkland Islands Pound . FKP','Falkland Islands Pound . FKP') ;
   $this->objcurrencydropdown->addOption('Fiji Dollar . FJD','Fiji Dollar . FJD') ;
   $this->objcurrencydropdown->addOption('Finnish Markka . FIM','Finnish Markka . FIM') ;
   $this->objcurrencydropdown->addOption('French Franc . FRF','French Franc . FRF') ;
   $this->objcurrencydropdown->addOption('Gambian Dalasi . GMD','Gambian Dalasi . GMD') ;
   $this->objcurrencydropdown->addOption('Georgian Lari . GEL','Georgian Lari . GEL') ;
   $this->objcurrencydropdown->addOption('German Mark . DEM','German Mark . DEM') ;
   $this->objcurrencydropdown->addOption('Ghanaian Cedi . GHC','Ghanaian Cedi . GHC') ;
   $this->objcurrencydropdown->addOption('Gibraltar Pound . GIP','Gibraltar Pound . GIP') ;
   $this->objcurrencydropdown->addOption('Gold (oz.) . XAU','Gold (oz.) . XAU') ;
   $this->objcurrencydropdown->addOption('Greek Drachma . GRD','Greek Drachma . GRD') ;
   $this->objcurrencydropdown->addOption('Guatemalan Quetzal . GTQ','Guatemalan Quetzal . GTQ') ;
   $this->objcurrencydropdown->addOption('Guinea Franc . GNF','Guinea Franc . GNF') ;
   $this->objcurrencydropdown->addOption('Guyanese Dollar . GYD','Guyanese Dollar . GYD') ;
   $this->objcurrencydropdown->addOption('Haitian Gourde . HTG','Haitian Gourde . HTG') ;
   $this->objcurrencydropdown->addOption('Honduran Lempira . HNL','Honduran Lempira . HNL') ;
   $this->objcurrencydropdown->addOption('Hong Kong Dollar . HKD','Hong Kong Dollar . HKD') ;
   $this->objcurrencydropdown->addOption('Hungarian Forint . HUF','Hungarian Forint . HUF') ;
   $this->objcurrencydropdown->addOption('Iceland Krona . ISK','Iceland Krona . ISK') ;
   $this->objcurrencydropdown->addOption('Indian Rupee . INR','Indian Rupee . INR') ;
   $this->objcurrencydropdown->addOption('Indonesian Rupiah . IDR','Indonesian Rupiah . IDR') ;
   $this->objcurrencydropdown->addOption('Iranian Rial . IRR','Iranian Rial . IRR') ;
   $this->objcurrencydropdown->addOption('Iraqi Dinar . IQD','Iraqi Dinar . IQD') ;
   $this->objcurrencydropdown->addOption('Irish Punt . IEP','Irish Punt . IEP') ;
   $this->objcurrencydropdown->addOption('Israeli New Shekel . ILS','Israeli New Shekel . ILS') ;
   $this->objcurrencydropdown->addOption('Italian Lira . ITL','Italian Lira . ITL') ;
   $this->objcurrencydropdown->addOption('Jamaican Dollar . JMD','Jamaican Dollar . JMD') ;
   $this->objcurrencydropdown->addOption('Japanese Yen . JPY','Japanese Yen . JPY') ;
   $this->objcurrencydropdown->addOption('Jordanian Dinar . JOD','Jordanian Dinar . JOD') ;
   $this->objcurrencydropdown->addOption('Kazakhstan Tenge . KZT','Kazakhstan Tenge . KZT') ;
   $this->objcurrencydropdown->addOption('Kenyan Shilling . KES','Kenyan Shilling . KES') ;
   $this->objcurrencydropdown->addOption('Kuwaiti Dinar . KWD','Kuwaiti Dinar . KWD') ;
   $this->objcurrencydropdown->addOption('Kyrgyzstanian Som . KGS','Kyrgyzstanian Som . KGS') ;
   $this->objcurrencydropdown->addOption('Lao Kip . LAK','Lao Kip . LAK') ;
   $this->objcurrencydropdown->addOption('Latvian Lats . LVL','Latvian Lats . LVL') ;
   $this->objcurrencydropdown->addOption('Lebanese Pound . LBP','Lebanese Pound . LBP') ;
   $this->objcurrencydropdown->addOption('Lesotho Loti . LSL','Lesotho Loti . LSL') ;
   $this->objcurrencydropdown->addOption('Liberian Dollar . LRD','Liberian Dollar . LRD') ;
   $this->objcurrencydropdown->addOption('Libyan Dinar . LYD','Libyan Dinar . LYD') ;
   $this->objcurrencydropdown->addOption('Lithuanian Litas . LTL','Lithuanian Litas . LTL') ;
   $this->objcurrencydropdown->addOption('Luxembourg Franc . LUF','Luxembourg Franc . LUF') ;
   $this->objcurrencydropdown->addOption('Macau Pataca . MOP','Macau Pataca . MOP') ;
   $this->objcurrencydropdown->addOption('Macedonian Denar . MKD','Macedonian Denar . MKD') ;
   $this->objcurrencydropdown->addOption('Malagasy Ariary . MGA','Malagasy Ariary . MGA') ;
   $this->objcurrencydropdown->addOption('Malagasy Franc . MGF','Malagasy Franc . MGF') ;
   $this->objcurrencydropdown->addOption('Malawi Kwacha . MWK','Malawi Kwacha . MWK') ;
   $this->objcurrencydropdown->addOption('Malaysian Ringgit . MYRS','Malaysian Ringgit . MYR') ;
   $this->objcurrencydropdown->addOption('Maldive Rufiyaa . MVR','Maldive Rufiyaa . MVR') ;
   $this->objcurrencydropdown->addOption('Maltese Lira . MTL','Maltese Lira . MTL') ;
   $this->objcurrencydropdown->addOption('Mauritanian Ouguiya . MRO','Mauritanian Ouguiya . MRO') ;
   $this->objcurrencydropdown->addOption('Mauritius Rupee . MUR','Mauritius Rupee . MUR') ;
   $this->objcurrencydropdown->addOption('Mexican Peso . MXN','Mexican Peso . MXN') ;
   $this->objcurrencydropdown->addOption('Moldovan Leu . MDL','Moldovan Leu . MDL') ;
   $this->objcurrencydropdown->addOption('Mongolian Tugrik . MNT','Mongolian Tugrik . MNT') ;
   $this->objcurrencydropdown->addOption('Moroccan Dirham . MAD','Moroccan Dirham . MAD') ;
   $this->objcurrencydropdown->addOption('Mozambique Metical . MZM','Mozambique Metical . MZM') ;
   $this->objcurrencydropdown->addOption('Mozambique New Metical . MZN','Mozambique New Metical . MZN') ;
   $this->objcurrencydropdown->addOption('Myanmar Kyat . MMK','Myanmar Kyat . MMK') ;
   $this->objcurrencydropdown->addOption('NL Antillian Guilder . ANG','NL Antillian Guilder . ANG') ;
   $this->objcurrencydropdown->addOption('Namibia Dollar . NAD','Namibia Dollar . NAD') ;
   $this->objcurrencydropdown->addOption('Nepalese Rupee . NPR','Nepalese Rupee . NPR') ;
   $this->objcurrencydropdown->addOption('New Zealand Dollar . NZD','New Zealand Dollar . NZD') ;
   $this->objcurrencydropdown->addOption('Nicaraguan Cordoba Oro . NIO','Nicaraguan Cordoba Oro . NIO') ;
   $this->objcurrencydropdown->addOption('Nigerian Naira . NGN','Nigerian Naira . NGN') ;
   $this->objcurrencydropdown->addOption('North Korean Won . KPW','North Korean Won . KPW') ;
   $this->objcurrencydropdown->addOption('Norwegian Kroner . NOK','Norwegian Kroner . NOK') ;
   $this->objcurrencydropdown->addOption('Omani Rial . OMR','Omani Rial . OMR') ;
   $this->objcurrencydropdown->addOption('Pakistan Rupee . PKR','Pakistan Rupee . PKR') ;
   $this->objcurrencydropdown->addOption('Palladium (oz.) . XPD','Palladium (oz.) . XPD') ;
   $this->objcurrencydropdown->addOption('Panamanian Balboa . PAB','Panamanian Balboa . PAB') ;
   $this->objcurrencydropdown->addOption('Papua New Guinea Kina . PGK','Papua New Guinea Kina . PGK') ;
   $this->objcurrencydropdown->addOption('Paraguay Guarani . PYG','Paraguay Guarani . PYG') ;
   $this->objcurrencydropdown->addOption('Peruvian Nuevo Sol . PEN','Peruvian Nuevo Sol . PEN') ;
   $this->objcurrencydropdown->addOption('Philippine Peso . PHP','Philippine Peso . PHP') ;
   $this->objcurrencydropdown->addOption('Platinum (oz.) . XPT','Platinum (oz.) . XPT') ;
   $this->objcurrencydropdown->addOption('Polish Zloty . PLN','Polish Zloty . PLN') ;
   $this->objcurrencydropdown->addOption('Portuguese Escudo . PTE','Portuguese Escudo . PTE') ;
   $this->objcurrencydropdown->addOption('Qatari Rial . QAR','Qatari Rial . QAR') ;
   $this->objcurrencydropdown->addOption('Romanian Lei . ROL','Romanian Lei . ROL') ;
   $this->objcurrencydropdown->addOption('Romanian New Lei . RON','Romanian New Lei . RON') ;
   $this->objcurrencydropdown->addOption('Russian Rouble . RUB','Russian Rouble . RUB') ;
   $this->objcurrencydropdown->addOption('Rwandan Franc . RWF','Rwandan Franc . RWF') ;
   $this->objcurrencydropdown->addOption('Samoan Tala . WST','Samoan Tala . WST') ;
   $this->objcurrencydropdown->addOption('Sao Tome/Principe Dobra . STD','Sao Tome/Principe Dobra . STD') ;
   $this->objcurrencydropdown->addOption('Saudi Riyal . SAR','Saudi Riyal . SAR') ;
   $this->objcurrencydropdown->addOption('Serbian Dinar . CSD','Serbian Dinar . CSD') ;
   $this->objcurrencydropdown->addOption('Seychelles Rupee . SCR','Seychelles Rupee . SCR') ;
   $this->objcurrencydropdown->addOption('Sierra Leone Leone . SLL','Sierra Leone Leone . SLL') ;
   $this->objcurrencydropdown->addOption('Silver (oz.) . XAG','Silver (oz.) . XAG') ;
   $this->objcurrencydropdown->addOption('Singapore Dollar . SGD','Singapore Dollar . SGD') ;
   $this->objcurrencydropdown->addOption('Slovak Koruna . SKK','Slovak Koruna . SKK') ;
   $this->objcurrencydropdown->addOption('Slovenian Tolar . SIT','Slovenian Tolar . SIT') ;
   $this->objcurrencydropdown->addOption('Solomon Islands Dollar . SBD','Solomon Islands Dollar . SBD') ;
   $this->objcurrencydropdown->addOption('Somali Shilling . SOS','Somali Shilling . SOS') ;
   $this->objcurrencydropdown->addOption('South African Rand . ZAR','South African Rand . ZAR') ;
   $this->objcurrencydropdown->addOption('South-Korean Won . KRW','South-Korean Won . KRW') ;
   $this->objcurrencydropdown->addOption('Spanish Peseta . ESP','Spanish Peseta . ESP') ;
   $this->objcurrencydropdown->addOption('Sri Lanka Rupee . LKR','Sri Lanka Rupee . LKR') ;
   $this->objcurrencydropdown->addOption('St. Helena Pound . SHP','St. Helena Pound . SHP') ;
   $this->objcurrencydropdown->addOption('Sudanese Dinar . SDD','Sudanese Dinar . SDD') ;
   $this->objcurrencydropdown->addOption('Sudanese Pound . SDP','Sudanese Pound . SDP') ;
   $this->objcurrencydropdown->addOption('Suriname Dollar . SRD','Suriname Dollar . SRD') ;
   $this->objcurrencydropdown->addOption('Suriname Guilder . SRG','Suriname Guilder . SRG') ;
   $this->objcurrencydropdown->addOption('Swaziland Lilangeni . SZL','Swaziland Lilangeni . SZL') ;
   $this->objcurrencydropdown->addOption('Swedish Krona . SEK','Swedish Krona . SEK') ;
   $this->objcurrencydropdown->addOption('Swiss Franc . CHF','Swiss Franc . CHF') ;
   $this->objcurrencydropdown->addOption('Syrian Pound . SYP','Syrian Pound . SYP') ;
   $this->objcurrencydropdown->addOption('Taiwan Dollar . TWD','Taiwan Dollar . TWD') ;
   $this->objcurrencydropdown->addOption('Tanzanian Shilling . TZS','Tanzanian Shilling . TZS') ;
   $this->objcurrencydropdown->addOption('Thai Baht . THB','Thai Baht . THB') ;
   $this->objcurrencydropdown->addOption('Tonga Paanga . TOP','Tonga Paanga . TOP') ;
   $this->objcurrencydropdown->addOption('Trinidad/Tobago Dollar . TTD','Trinidad/Tobago Dollar . TTD') ;
   $this->objcurrencydropdown->addOption('Tunisian Dinar . TND','Tunisian Dinar . TND') ;
   $this->objcurrencydropdown->addOption('Turkish Lira . TRL','Turkish Lira . TRL') ;
   $this->objcurrencydropdown->addOption('Turkish New Lira . TRY','Turkish New Lira . TRY') ;
   $this->objcurrencydropdown->addOption('Turkmenistan Manat . TMM','Turkmenistan Manat . TMM') ;
   $this->objcurrencydropdown->addOption('Uganda Shilling . UGX','Uganda Shilling . UGX') ;
   $this->objcurrencydropdown->addOption('Ukraine Hryvnia . UAH','Ukraine Hryvnia . UAH') ;
   $this->objcurrencydropdown->addOption('Uruguayan Peso . UYU','Uruguayan Peso . UYU') ;
   $this->objcurrencydropdown->addOption('Utd. Arab Emir. Dirham . AED','Utd. Arab Emir. Dirham . AED') ;
   $this->objcurrencydropdown->addOption('Vanuatu Vatu . VUV','Vanuatu Vatu . VUV') ;
   $this->objcurrencydropdown->addOption('Venezuelan Bolivar . VEB','Venezuelan Bolivar . VEB') ;
   $this->objcurrencydropdown->addOption('Vietnamese Dong . VND','Vietnamese Dong . VND') ;
   $this->objcurrencydropdown->addOption('Yemeni Rial . YER','Yemeni Rial . YER') ;
   $this->objcurrencydropdown->addOption('Yugoslav Dinar . YUN','Yugoslav Dinar . YUN') ;
   $this->objcurrencydropdown->addOption('Zambian Kwacha . ZMK','Zambian Kwacha . ZMK') ;
   $this->objcurrencydropdown->addOption('Zimbabwe Dollar . ZWD','Zimbabwe Dollar . ZWD') ;
   $this->objcurrencydropdown->addOption('Zimbabwe New Dollar . ZWN','Zimbabwe New Dollar . ZWN') ;
   $this->objcurrencydropdown->size = 50;
   
   

/***********************************************************************************************************************************************************************/   

/*create all button elements*/


$this->loadclass('button','htmlelements');
$this->objnext  = new button('next', $next);
$this->objnext->setToSubmit();
$strerror = 'select file';

	$onClick = 'var exratefile = document.lodging.exchangeratefile;
					    var exqtesource = document.lodging.txtquotesource;
					 
					 
					 
					    var acceptance = true;
					   //value of the begin date
  					 var exchgfile = exratefile.text;
	   				 //value of the end date
		  			 var exchgsource = exqtesource.value;
					 
					 
					 //checks if dates are right
					 if(exchgsource == " "){
					 	acceptance = false;
						
					 }
					 
							 
					 //check final condition
					 if(!acceptance){
					 	alert(\''.$strerror .'\');
						acceptance = true;
						return false;
					 }else{
          // alert(\''.$sucessfull.'\')
           }';
	$this->objnext->extra = sprintf(' onClick ="javascript: %s"', $onClick );


$this->objexit  = new button('exit', $exit);
$this->objexit->setToSubmit();

$this->objBack  = new button('back', $back);
$this->objBack->setToSubmit();

$this->objaddlodge  = new button('add', $add);
$this->objaddlodge->setToSubmit();



/*$btnsave  = $this->objLanguage->languageText('word_save');
$strsave  = ucfirst($btnsave);
$this->objButtonSubmit  = new button('submit', $strsave);
$this->objButtonSubmit->setToSubmit();*/

/*********************************************************************************************************************************************************************/
/**
 *create a table for lodge details  - place all form headings in*/

        $myTabSuppliesheading  = $this->newObject('htmltable','htmlelements');
        $myTabSuppliesheading->width='80%';
        $myTabSuppliesheading->border='0';
        $myTabSuppliesheading->cellspacing = '10';
        $myTabSuppliesheading->cellpadding ='10';

        $myTabSuppliesheading->startRow();
        $myTabSuppliesheading->addCell("<div align=\"left\">"  . $suppliesHint . "</div>");
        $myTabSuppliesheading->endRow();
        
        $myTabSuppliesheading->startRow();
        $myTabSuppliesheading->addCell("<div align=\"left\">"  . $this->objInfoIcon->show() . $ExchangeRate  . "</div>");
        $myTabSuppliesheading->endRow();
        
        $myTabSuppliesheading->startRow();
        $myTabSuppliesheading->addCell("<div align=\"left\">"  . $suppliesSuggestedExRate . "</div>");
        $myTabSuppliesheading->endRow();
        
        $myTabSuppliesheading->startRow();
        $myTabSuppliesheading->addCell("<div align=\"left\">"  . $suppliesExpenditures  . "</div>");
        $myTabSuppliesheading->endRow();
/*********************************************************************************************************************************************************************/
/*create a table for lodge details*/

        $myTabSupplies  = $this->newObject('htmltable','htmlelements');
        $myTabSupplies->width='70%';
        $myTabSupplies->border='0';
        $myTabSupplies->cellspacing = '5';
        $myTabSupplies->cellpadding ='10';

        $myTabSupplies->startRow();
        $myTabSupplies->addCell($expensesdate);
        $myTabSupplies->addCell($this->objsupplydate->show());
        $myTabSupplies->addCell('');
        $myTabSupplies->addCell('');
        $myTabSupplies->addCell('');
        $myTabSupplies->addCell('');
        $myTabSupplies->addCell('');
        $myTabSupplies->addCell($displayhelp);
        $myTabSupplies->endRow();

        $myTabSupplies->startRow();
        $myTabSupplies->addCell($vendor);
        $myTabSupplies->addCell($this->objtxtvendor->show());
        $myTabSupplies->endRow();
        
        $myTabSupplies->startRow();
        $myTabSupplies->addCell($descriptionSupplies);
        $myTabSupplies->addCell($this->objtxtdescriptionSupplies->show());
        $myTabSupplies->endRow();

        $myTabSupplies->startRow();
        $myTabSupplies->addCell($currency);
        $myTabSupplies->addCell($this->objcurrencydropdown->show());
        $myTabSupplies->endRow();

        $myTabSupplies->startRow();
        $myTabSupplies->addCell($cost);
        $myTabSupplies->addCell($this->objtxtcost->show());
        $myTabSupplies->endRow();
        
        $myTabSupplies->startRow();
        $myTabSupplies->addCell($rate);
        $myTabSupplies->addCell($this->objtxtexchange->show());
        $myTabSupplies->endRow();
        
        //$myTabSupplies->startRow();
        //$myTabSupplies->addCell($cost);
        //$myTabSupplies->addCell($this->objButtonSubmit->show());
        //$myTabSupplies->endRow();


/*********************************************************************************************************************************************************************/        

/*create table for file attachment*/

        $myTabExchRate  = $this->newObject('htmltable','htmlelements');
        $myTabExchRate->width='70%';
        $myTabExchRate->border='0';
        $myTabExchRate->cellspacing = '5';
        $myTabExchRate->cellpadding ='10';
        
        $myTabExchRate->startRow();
        $myTabExchRate->addCell('Attach File');
        $myTabExchRate->addCell($objUploadFile->show());
		  $myTabExchRate->endRow();
		  
		  $myTabExchRate->startRow();
        $myTabExchRate->addCell('Quote Source');
        $myTabExchRate->addCell($this->objtxtquotesource->show());
        $myTabExchRate->endRow();
        
        $myTabExchRate->startRow();
        $myTabExchRate->addCell(' ');
//        $myTabExchRate->addCell($this->objButtonUploadReceipt->show());
        $myTabExchRate->endRow();
        
        $myTabExchRate->startRow();
        $myTabExchRate->endRow();
        
//******************************************************************************

 		  $myTabReceipt  = $this->newObject('htmltable','htmlelements');
        $myTabReceipt->width='80%';
        $myTabReceipt->border='0';
        $myTabReceipt->cellspacing = '5';
        $myTabReceipt->cellpadding ='10';
        
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell(ucfirst($attachreceipt));
        $myTabReceipt->addCell($objReceiptFile->show());
        
        $myTabReceipt->endRow();
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell(ucfirst($createaffidavit));
        $myTabReceipt->addCell($link);
        $myTabReceipt->addCell(' ');
        $myTabReceipt->addCell(' ');
        $myTabReceipt->addCell(' ');
        $myTabReceipt->addCell(' ');
        $myTabReceipt->addCell(' ');
        $myTabReceipt->addCell(' ');
        //$myTabReceipt->addCell($displayhelp);
        $myTabReceipt->endRow();
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell(ucfirst($attachaffidavit));
        $myTabReceipt->addCell($objAffidavitFile->show());
        $myTabReceipt->endRow();
        
        $myTabReceipt->startRow();
        $myTabReceipt->addCell(' ');
//        $myTabReceipt->addCell($this->objButtonUploadReceipt->show());
        $myTabReceipt->endRow();
        
        $myTabReceipt->startRow();
        $myTabReceipt->endRow();        
        
        
        
        
        

/*create tabbox for supplies information*/
$this->loadClass('tabbedbox', 'htmlelements');
$objtabbedbox = new tabbedbox();
$objtabbedbox->addTabLabel('Supplies Information');
$objtabbedbox->addBoxContent($myTabSupplies->show());

/*create tabbed box for file attachments*/
$objtabboxfile = new tabbedbox();
$objtabboxfile->addTabLabel('Verify Exchange Rate');
$objtabboxfile->addBoxContent($myTabExchRate->show()); 

/**
  *create a tabbed box to place table and elements in
  */
$this->loadClass('tabbedbox', 'htmlelements'); 
$objtabreceipt = new tabbedbox();
$objtabreceipt->addTabLabel('Receipt Information');
$objtabreceipt->addBoxContent("<div align=\"center\">" ."<div class=\"error\">" .'<br />' . '<b>' . $receipt .'<b/>' ."</div>".  '<br />' ."<div align=\"left\">"  .$myTabReceipt->show());
/*--*/      


/*********************************************************************************************************************************************************************/

$objSuppliesForm = new form('supplies',$this->uri(array('action'=>'createsupplies')));
$objSuppliesForm->displayType = 3;
$objSuppliesForm->addToForm($objtabbedbox->show());	
$objSuppliesForm->addToForm($objtabboxfile->show() . '<br />' . '<br />' . $objtabreceipt->show() . '<br />'."<div align=\"center\">".$this->objaddlodge->show() ."</div>".'<br />' . "<div align=\"center\">" . $this->objBack->show(). $this->objnext->show() . ' ' ."</div");

//$formlodging = '<form name="lodging" id="lodging" enctype="multipart/form-data" method="post" action="'.$this->formaction.'">';
$objSuppliesForm->addRule('txtvendor', 'Please enter vendor name','required');
$objSuppliesForm->addrule('txtdescriptionSupplies', 'Please enter supplies descriptionSupplies', 'reqiured');
$objSuppliesForm->addRule('txtcost', 'Please enter cost amount','required');
$objSuppliesForm->addRule('txtexchange', 'Please enter the exchange rate','required');

/*********************************************************************************************************************************************************************/
  

//display screen content
echo "<div align=\"center\">" . $this->objsupplyHeading->show()  . "</div>";
echo "<div align=\"right\">" .'<br>'  . $myTabSuppliesheading->show() . "</div>";
echo  "<div align=\"left\">"  . $objSuppliesForm->show() . "</div";





                    
?>
