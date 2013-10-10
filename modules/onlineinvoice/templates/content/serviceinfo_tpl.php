<?php

  /**
   *create a template for service information
   */
   $this->objHelp=& $this->getObject('helplink','help');
   $displayhelp  = $this->objHelp->show('mod_onlineinvoice_helpinstruction');

/***************************************************************************************************************************************************************/   
   /**
    *create main heading
    */
     $this->objMainheading =& $this->getObject('htmlheading','htmlelements');
     $this->objMainheading->type=1;
    $this->objMainheading->str=ucfirst($objLanguage->languageText('mod_onlineinvoice_serviceinformation','onlineinvoice'));   
/***************************************************************************************************************************************************************/
  /**
   *language items
   */     
   
   $empname     = $this->objLanguage->languageText('mod_onlinveinvoice_empname','onlineinvoice');
   $monthlyrate = $this->objLanguage->languageText('mod_onlinveinvoice_monthlyrate','onlineinvoice');
   $fte         = $this->objLanguage->languageText('mod_onlinveinvoice_fte','onlineinvoice');
   $formonth    = $this->objLanguage->languageText('mod_onlinveinvoice_formonth','onlineinvoice');
   $receiptdate  = $this->objLanguage->languageText('mod_onlinveinvoice_receiptdate','onlineinvoice');
   //$displayhelp = $this->objLanguage->languageText('mod_onlinveinvoice_formonth','onlineinvoice');
   $vendor = $this->objLanguage->languageText('word_vendor');
   $currency = $this->objLanguage->languageText('word_currency');
   $rate = $this->objLanguage->languageText('mod_onlineinvoice_exchangerate','onlineinvoice');

   $ratevalue = $this->objLanguage->languageText('word_rate');
   
   
   $description  = $this->objLanguage->languageText('word_description');
   //$begindate   = 
/***************************************************************************************************************************************************************/
/**
 *dropdown list for emps
 */
 
 $employeenames  = 'empnames';           
 $this->objempname  = $this->newObject('dropdown','htmlelements');
 $this->objempname->dropdown($employeenames);
 $this->objempname->addOption('jane','jane') ;
 $this->objempname->addOption('jack','jack') ;
 $this->objempname->addOption('kim','kim') ;
 $this->objempname->addOption('candice','candice') ;
 $this->objempname->addOption('mike','mike') ;
 $this->objempname->size  = 50;
 
 
/**
 *create textboxes
 */
 
$this->objtxtmnthrate = $this->newobject('textinput','htmlelements');
$this->objtxtmnthrate->textinput("txtmnthrate","",'text',15);

$this->objtxtfte = $this->newobject('textinput','htmlelements');
$this->objtxtfte->textinput("txtfte","",'text',15);

$this->objtxtvendor = $this->newobject('textinput','htmlelements');
$this->objtxtvendor->textinput("txtvendor","",'text',30);

$this->objtxtcost  = $this->newobject('textinput','htmlelements');
$this->objtxtcost->textinput("txtcost","0.00","text",30);

$this->objtxtexchange = $this->newobject('textinput','htmlelements');
$this->objtxtexchange->textinput("txtexchange","","text",30);

$this->objtxtdescription  = new textinput('txtdescription', ' ','text',30);
$this->objtxtdescription->id = 'txtdescription';

 
/**
 *dates
 */
 
$this->objdate = $this->newObject('datepicker','htmlelements');
$name = 'txtbegindate';
$date = date('Y-m-d');
$format = 'YYYY-M-DD';
$this->objdate->setName($name);
$this->objdate->setDefaultDate($date);
$this->objdate->setDateFormat($format);
     
$this->objenddate = $this->newObject('datepicker','htmlelements');
$name = 'enddate';
$date = date('Y-m-d');
$format = 'YYYY-M-DD';
$this->objenddate->setName($name);
$this->objenddate->setDefaultDate($date);
$this->objenddate->setDateFormat($format);

$this->objreceiptdate = $this->newObject('datepicker','htmlelements');
$name = 'txtreceiptdate';
$date = date('Y-m-d');
$format = 'YYYY-M-DD';
$this->objreceiptdate->setName($name);
$this->objreceiptdate->setDefaultDate($date);
$this->objreceiptdate->setDateFormat($format);


/***************************************************************************************************************************************************************/   
   
   /**
    *create a table for all form elements
    */
    /*create table to place form elements in  --  date values*/
        $myTable=$this->newObject('htmltable','htmlelements');
        $myTable->width='40%';
        $myTable->border='0';
        $myTable->cellspacing='5';
        $myTable->cellpadding='10';

        $myTable->startRow();
        $myTable->addCell(ucfirst($empname));
        $myTable->addCell($this->objempname->show());
        $myTable->endRow();
        
        $myTable->startRow();
        $myTable->addCell(ucfirst($monthlyrate));
        $myTable->addCell($this->objtxtmnthrate->show());
        $myTable->endRow();
        
        $myTable->startRow();
        $myTable->addCell(ucfirst($fte));
        $myTable->addCell($this->objtxtfte->show());
        $myTable->endRow();
        
        $myTabledate=$this->newObject('htmltable','htmlelements');
        $myTabledate->width='70%';
        $myTabledate->border='0';
        $myTabledate->cellspacing='5';
        $myTabledate->cellpadding='10';
        
        $myTabledate->startRow();
        $myTabledate->addCell(ucfirst($formonth));
        $myTabledate->addCell($this->objdate->show());
        $myTabledate->addCell(ucfirst('to'));
        $myTabledate->addCell($this->objenddate->show());
        $myTabledate->endRow();
        
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

           
   
/***************************************************************************************************************************************************************/
$this->loadClass('tabbedbox', 'htmlelements');
$objcreatetab = new tabbedbox();
$objcreatetab->addTabLabel('Employees Information');
$objcreatetab->addBoxContent('<br />'  . $myTable->show() . '<br />' . $myTabledate->show());

/****************************************************************************************************************************************************************/

/*create a table for service details*/

        $myTabService  = $this->newObject('htmltable','htmlelements');
        $myTabService->width='90%';
        $myTabService->border='0';
        $myTabService->cellspacing = '5';
        $myTabService->cellpadding ='10';

        $myTabService->startRow();
        $myTabService->addCell(ucfirst($receiptdate));
        $myTabService->addCell($this->objreceiptdate->show());
        $myTabService->addCell(' ');
        $myTabService->addCell(' ');
        $myTabService->addCell(' ');
        $myTabService->addCell(' ');
        $myTabService->addCell(' ');
        $myTabService->addCell(' ');
        $myTabService->addCell($displayhelp);
        $myTabService->endRow();


        $myTabService->startRow();
        $myTabService->addCell($vendor);
        $myTabService->addCell($this->objtxtvendor->show());
        $myTabService->endRow();

        $myTabService->startRow();
        $myTabService->addCell(ucfirst($description));
        $myTabService->addCell($this->objtxtdescription->show());
        $myTabService->endRow();
        
        $myTabService->startRow();
        $myTabService->addCell($ratevalue);
        $myTabService->addCell($this->objtxtcost->show());
        $myTabService->endRow();
        
        $myTabService->startRow();
        $myTabService->addCell($currency);
        $myTabService->addCell($this->objcurrencydropdown->show());
        $myTabService->endRow();
        
        $myTabService->startRow();
        $myTabService->addCell($rate);
        $myTabService->addCell($this->objtxtexchange->show());
        $myTabService->endRow();
        
        
        
/***************************************************************************************************************************************************************/
$this->loadClass('tabbedbox', 'htmlelements');
$objtab = new tabbedbox();
$objtab->addTabLabel('Service Expenses');
$objtab->addBoxContent('<br />'  . $myTabService->show());

/****************************************************************************************************************************************************************/
   
   /**
    *display all screen outputs
    */
    echo  "<div align=\"center\">" . $this->objMainheading->show() . "</div>";
    echo  "<div align=\"left\">" . $objcreatetab->show() . '<br />' . $objtab->show()."</div>";
                   

?>
