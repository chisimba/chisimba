<?php

  /**
   *create tev template
   */   
/***********************************************************************************************************************************************/   

  /**  create the heading -- Travel expense voucher
   *   the heading is created by creating an instance of the heading class
   *   there after the heading is set to type 2
   *   and is then assigned the title -- travel expense voucher
   */

  $this->objMainheading = $this->newObject('htmlheading','htmlelements');
  $this->objMainheading->type = 1;
  $this->objMainheading->str=$objLanguage->languageText('mod_onlineinvoice_travelexpensevoucher','onlineinvoice');

  /**
   *create heading -- traveler details
   */
   
  $this->objheading =& $this->newObject('htmlheading','htmlelements');
  $this->objheading->type = 3;
  $this->objheading->str=$objLanguage->languageText('mod_onlineinvoice_travelerinformation','onlineinvoice');

  
  /**
   *getIcon for information
   */     
  $this->objInfoIcon = $this->newObject('geticon','htmlelements');
  $this->objInfoIcon->setModuleIcon('freemind');
    
  /**
    *help information
    */   
    
    $tevinfo  = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_tevinfo','onlineinvoice'));
    $example  = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_example','onlineinvoice'));
    $selectcountry  = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_country','onlineinvoice'));
    $travpurpose  = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_travpurpose','onlineinvoice'));
    $helpstring = $tevinfo . '<br />' . $example . '<br />' . $travpurpose  . '<br />'  . $selectcountry;
    
    $this->objHelp=& $this->getObject('helplink','help');
    $displayhelp  = $this->objHelp->show($helpstring);
    

/************************************************************************************************************************************************/

  /**
   *create all language elements for labels
   */
  $name = $this->objLanguage->languageText('phrase_claimantname');
  $title  = $this->objLanguage->languageText('phrase_claimanttitle');
  $address  = $this->objLanguage->languageText('phrase_mailingaddress');
  $city = $this->objLanguage->languageText('word_city');
  $province = $this->objLanguage->languageText('word_province');
  $postalcode = $this->objLanguage->languageText('phrase_postalcode');
  $country  = $this->objLanguage->languageText('word_country');
  $btnsave  = $this->objLanguage->languageText('word_save');
  $strsave  = ucfirst($btnsave);
  $btnEdit  = $this->objLanguage->languageText('word_edit');
  $description = $this->objLanguage->languageText('mod_onlineinvoice_descriptionoftravelpurpose','onlineinvoice'); 
  $exit  = $this->objLanguage->languageText('phrase_exit');
  $next = $this->objLanguage->languageText('phrase_next');
  $itenirary  = $this->objLanguage->languageText('mod_onlineinvoice_completeitinerary','onlineinvoice');
  $iteniraryM  = $this->objLanguage->languageText('mod_onlineinvoice_completeitinerarym','onlineinvoice');
  $showitenirary = $this->objLanguage->languageText('word_itinerary');
  $showitenirarymulti = $this->objLanguage->languageText('phrase_itinerarymulti');
  $oneway = $this->objLanguage->languageText('phrase_oneway');
  $multidestination = $this->objLanguage->languageText('phrase_multidestination');
  $information  = $this->objLanguage->languageText('mod_onlineinvoice_requiredfields','onlineinvoice');
  $strinfo  = strtoupper($information);
  $itineraryinfo  = $this->objLanguage->languageText('mod_onlineinvoice_itineraryinfo','onlineinvoice');
  $valname  = $this->objLanguage->languageText('mod_onlineinvoice_entername','onlineinvoice');
  $valtitle  = $this->objLanguage->languageText('mod_onlineinvoice_entertitle','onlineinvoice');
  $valaddress = $this->objLanguage->languageText('mod_onlineinvoice_enteraddress','onlineinvoice');
  $valcity  = $this->objLanguage->languageText('mod_onlineinvoice_entercity','onlineinvoice');
  $valprovince  = $this->objLanguage->languageText('mod_onlineinvoice_enterprovince','onlineinvoice');
  $valpostal  = $this->objLanguage->languageText('mod_onlineinvoice_enterpostal','onlineinvoice');
  $valcountry = $this->objLanguage->languageText('mod_onlineinvoice_entercountry','onlineinvoice');
  $travpurpose  = $this->objLanguage->languageText('mod_onlineinvoice_enterpurpose','onlineinvoice');
  $strnext  = ucfirst($next);
/************************************************************************************************************************************************/
  /**
   *Create all labels-- for Claimant details
   */
  
  $lblName  = 'lblcname';
  $this->objcname  = $this->newObject('label','htmlelements');
  $this->objcname->setLabel($name);
  $this->objcname->setForId($lblName);

  $lblTitle = 'lbltitle';
  $this->objtitle  = $this->newObject('label','htmlelements');
  $this->objtitle->label($title,$lblTitle);

  $lblAddress = 'lbladdress';
  $this->objaddress  = $this->newObject('label','htmlelements');
  $this->objaddress->label($address,$lblAddress);

  $lblCity = 'lblcity';
  $this->objcity  = $this->newObject('label','htmlelements');
  $this->objcity->label($city,$lblCity);

  $lblProvince = 'lblprovince';
  $this->objprovince  = $this->newObject('label','htmlelements');
  $this->objprovince->label($province,$lblProvince);

  $lblPostalcode = 'lblpostalcode';
  $this->objpostalcode  = $this->newObject('label','htmlelements');
  $this->objpostalcode->label($postalcode,$lblPostalcode);

  $lblCountry = 'lblCountry';
  $this->objCountry  = $this->newObject('label','htmlelements');
  $this->objCountry->label($country,$lblCountry);

  $lblDescription = strtoupper($description);
  $this->objDescription  = $this->newObject('label','htmlelements');
  $this->objDescription->label($description,$lblDescription);

/************************************************************************************************************************************************/

  /**
   *create all text input boxes
   */
   
   //$claimantinfo = array();
   
   $claimantinfo  = $this->getSession('claimantdata');
        $claimantname = '';
        $claimanttitle  = '';
        $claimantaddress = '';
        $claimantcity = '';
        $claimantprovince = '';
        $claimantpostalcode = '';
        $country  = '';
        $purposearea  = '';
        
        
        if(!empty($claimantinfo)){         
              while(list($subkey,$subval) = each($claimantinfo))
              {
                  if($subkey == 'name') {
                 $claimantname = $subval;
                  }
                  if($subkey == 'title') {
                  $claimanttitle = $subval;
                  }
                  if($subkey == 'address') {
                  $claimantaddress = $subval;
                  }
                  if($subkey == 'city') {
                  $claimantcity = $subval;
                  }
                  if($subkey == 'province') {
                  $claimantprovince = $subval;
                  }
                  if($subkey == 'postalcode') {
                  $claimantpostalcode = $subval;
                  }
                  if($subkey == 'country') {
                  $country = $subval;
                  }
                  if($subkey == 'travelpurpose') {
                  $purposearea = $subval;
                  }
              }
          }
 
  $this->loadClass('textinput', 'htmlelements');

  $this->objtxtname = new textinput('txtClaimantName');
  //$this->objtxtname->id = 'txtClaimantName';
  $this->objtxtname->value = $claimantname ;
  
  $this->objtxttitle = new textinput('txtTitle');
  $this->objtxttitle->value  = $claimanttitle ;

  $this->objtxtcity = new textinput('txtCity');
  $this->objtxtcity->value  = $claimantcity;

  $this->objtxtprovince = new textinput('txtprovince');
  $this->objtxtprovince->value  = $claimantprovince;

  $this->objtxtpostalcode = new textinput('txtpostalcode');
  $this->objtxtpostalcode->value  = $claimantpostalcode;

  //$this->objtxtcountry = new textinput('txtcountry');
  //$this->objtxtcountry->value  = $country;

/************************************************************************************************************************************************/
/**
 *coutries
 */ 
    $countryvals  = 'coutryvals';           
   $this->objcountrydropdown  = $this->newObject('dropdown','htmlelements');
   $this->objcountrydropdown->dropdown($countryvals);
   $this->objcountrydropdown->addOption('Afghanistan','Afghanistan') ;
   $this->objcountrydropdown->addOption('Albania','Albania') ;
   $this->objcountrydropdown->addOption('Algeria','Algeria') ;
   $this->objcountrydropdown->addOption('Andorra','Andorra') ;
   $this->objcountrydropdown->addOption(' Angola',' Angola') ;
   $this->objcountrydropdown->addOption('Antigua and Barbuda ','Antigua and Barbuda') ;
   $this->objcountrydropdown->addOption('Argentina','Argentina') ;
   $this->objcountrydropdown->addOption('Armenia  ','Armenia ') ;
   $this->objcountrydropdown->addOption('Australia','Australia |') ;
   $this->objcountrydropdown->addOption('Austria','Austriat') ;
   $this->objcountrydropdown->addOption('Azerbaijan ','Azerbaijan ') ;
   $this->objcountrydropdown->addOption('Bahamas','Bahamas') ;
   $this->objcountrydropdown->addOption('Bahrain','Bahrain') ;
   $this->objcountrydropdown->addOption('Bangladesh','Bangladesh') ;
   $this->objcountrydropdown->addOption('Barbados','Barbados') ;
   $this->objcountrydropdown->addOption('Belarus','Belarus') ;
   $this->objcountrydropdown->addOption('Belize','Belize') ;
   $this->objcountrydropdown->addOption('Belgium','Belgium ') ;
   $this->objcountrydropdown->addOption('Benin','Benin') ;
   $this->objcountrydropdown->addOption('Bhutan','Bhutan') ;
   $this->objcountrydropdown->addOption('Bolivia','Bolivia') ;
   $this->objcountrydropdown->addOption('Botswana','Botswana') ;
   $this->objcountrydropdown->addOption('Bosnia and Herzegovina','Bosnia and Herzegovina') ;
   $this->objcountrydropdown->addOption('Brazil',' Brazil') ;
   $this->objcountrydropdown->addOption('Bulgaria','Bulgaria') ;
   $this->objcountrydropdown->addOption('Burkina Faso','Burkina Faso') ;
   $this->objcountrydropdown->addOption('Burundi','Burundi') ;
   $this->objcountrydropdown->addOption('Cambodia','Cambodia') ;
   $this->objcountrydropdown->addOption('Cameroon','Cameroon') ;
   $this->objcountrydropdown->addOption('Canada','Canada') ;
   $this->objcountrydropdown->addOption('Cape Verde','Cape Verde') ;
   $this->objcountrydropdown->addOption('Central African Republic','Central African Republic') ;
   $this->objcountrydropdown->addOption('China','China') ;
   $this->objcountrydropdown->addOption('Colombia','Colombia') ;
   $this->objcountrydropdown->addOption('Comoros','Comoros') ;
   $this->objcountrydropdown->addOption('Congo','Congo') ;
   $this->objcountrydropdown->addOption('Costa Rica','Costa Rica') ;
   $this->objcountrydropdown->addOption('Côte d Ivoire','Côte d Ivoire') ;  
   $this->objcountrydropdown->addOption('Croatia','Croatia') ;
   $this->objcountrydropdown->addOption('Cuba','Cuba') ;
   $this->objcountrydropdown->addOption('Cuba','Cuba') ;
   $this->objcountrydropdown->addOption('Cyprus','Cyprus') ;
   $this->objcountrydropdown->addOption('Czech Republic','Czech Republic') ;
   $this->objcountrydropdown->addOption('Denmark','Denmark') ;
   $this->objcountrydropdown->addOption('Djibouti','Djibouti') ;
   $this->objcountrydropdown->addOption('Dominica','Dominica') ;
   $this->objcountrydropdown->addOption('Dominican Republic','Dominican Republic') ;
   $this->objcountrydropdown->addOption('East Timor','East Timor') ;
  // $this->objcountrydropdown->addOption('East Caribbean Dollar . XCD','East Caribbean Dollar . XCD') ;
   $this->objcountrydropdown->addOption('Ecuador','Ecuador') ;
   $this->objcountrydropdown->addOption('Egypt','Egypt') ;
   $this->objcountrydropdown->addOption('El Salvador','El Salvador') ;
   $this->objcountrydropdown->addOption('Equatorial Guinea','Equatorial Guinea') ;
    $this->objcountrydropdown->addOption('Eritrea','Eritrea') ;
     $this->objcountrydropdown->addOption('Estonia','Estonia') ;
   $this->objcountrydropdown->addOption('Ethiopia','Ethiopia') ;
 //  $this->objcountrydropdown->addOption('Euro . EUR','Euro . EUR') ;
   $this->objcountrydropdown->addOption('Falkland Islands','Falkland Islands') ;
   $this->objcountrydropdown->addOption('Fiji','Fiji') ;
   $this->objcountrydropdown->addOption('Finland','Finland') ;
   $this->objcountrydropdown->addOption('France' , ' France') ;
   $this->objcountrydropdown->addOption('French Guiana' , 'French Guiana') ;
   $this->objcountrydropdown->addOption('Gabon','Gabon') ;
   $this->objcountrydropdown->addOption('The Gambia','The Gambia') ;
   $this->objcountrydropdown->addOption('Gaza Strip','Gaza Strip') ;
   $this->objcountrydropdown->addOption('Georgia','Georgia') ;
   $this->objcountrydropdown->addOption('Germany','Germany') ;
   $this->objcountrydropdown->addOption('Ghana','Ghana') ;
   $this->objcountrydropdown->addOption('Greece','Greece') ;
   $this->objcountrydropdown->addOption('Grenada','Grenada') ;
   $this->objcountrydropdown->addOption('Guatemala','Guatemala') ;
   $this->objcountrydropdown->addOption('Guinea','Guinea') ;
   $this->objcountrydropdown->addOption('Guinea-Bissau ','Guinea-Bissau ') ;
   $this->objcountrydropdown->addOption('Guyana','Guyana') ;
   $this->objcountrydropdown->addOption('Haiti','Haiti') ;
   $this->objcountrydropdown->addOption('Honduras','Honduras') ;
   $this->objcountrydropdown->addOption('Hong Kong','Hong Kong') ;
   $this->objcountrydropdown->addOption('Hungary','Hungary') ;
   $this->objcountrydropdown->addOption('Iceland','Iceland') ;
   $this->objcountrydropdown->addOption('India','India') ;
   $this->objcountrydropdown->addOption('Indonesia','Indonesia') ;
   $this->objcountrydropdown->addOption('Iran','Iran') ;
   $this->objcountrydropdown->addOption('Iraq','Iraq') ;
   $this->objcountrydropdown->addOption('Ireland','Ireland') ;
   $this->objcountrydropdown->addOption('Northern Ireland','Northern Ireland') ;
   $this->objcountrydropdown->addOption('Israel','Israel') ;
   $this->objcountrydropdown->addOption('Italy','Italy') ;
   $this->objcountrydropdown->addOption('Jamaica','Jamaica') ;
   $this->objcountrydropdown->addOption('Japan','Japan') ;
   $this->objcountrydropdown->addOption('Jordan','Jordan') ;
   $this->objcountrydropdown->addOption('Kazakhstan','Kazakhstan') ;
   $this->objcountrydropdown->addOption('Kenya','Kenya') ;
   $this->objcountrydropdown->addOption('Kuwait','Kuwait') ;
   $this->objcountrydropdown->addOption('Kyrgyzstan','Kyrgyzstan') ;
   $this->objcountrydropdown->addOption('Laos','Laos') ;
   $this->objcountrydropdown->addOption('Latvia','Latvia') ;
   $this->objcountrydropdown->addOption('Lebanon','Lebanon') ;
   $this->objcountrydropdown->addOption('Lesotho','Lesotho') ;
   $this->objcountrydropdown->addOption('Liberia','Liberia') ;
   $this->objcountrydropdown->addOption('Libya','Libya') ;
   $this->objcountrydropdown->addOption('Lithuania','Lithuania') ;
   $this->objcountrydropdown->addOption('Luxembourg','Luxembourg') ;
   $this->objcountrydropdown->addOption('Macau','Macau') ;
   $this->objcountrydropdown->addOption('Macedonia','Macedonia') ;
  // $this->objcountrydropdown->addOption('Malagasy Ariary . MGA','Malagasy Ariary . MGA') ;
 //  $this->objcountrydropdown->addOption('Malagasy Franc . MGF','Malagasy Franc . MGF') ;
   $this->objcountrydropdown->addOption('Malawi','Malawi') ;
   $this->objcountrydropdown->addOption('Malaysia','Malaysia') ;
   $this->objcountrydropdown->addOption('Maldives','Maldives') ;
   $this->objcountrydropdown->addOption('Mali','Mali') ;
   $this->objcountrydropdown->addOption('Malta','Malta') ;
   $this->objcountrydropdown->addOption('Marshall Islands','Mauritius Islands') ;
   $this->objcountrydropdown->addOption('Mauritania ','Mauritania ') ;
   $this->objcountrydropdown->addOption('Mauritius','Mauritius') ;
   $this->objcountrydropdown->addOption('Mexico','Mexico') ;
   $this->objcountrydropdown->addOption('Micronesia','Micronesia') ;
   $this->objcountrydropdown->addOption('Moldova','Moldova') ;
   $this->objcountrydropdown->addOption('Mongolia','Mongolia') ;
   $this->objcountrydropdown->addOption('Morocco','Morocco') ;
   $this->objcountrydropdown->addOption('Mozambique','Mozambique') ;
 //  $this->objcountrydropdown->addOption('Mozambique New Metical . MZN','Mozambique New Metical . MZN') ;
   $this->objcountrydropdown->addOption('Myanmar','Myanmar') ;
  // $this->objcountrydropdown->addOption('NL Antillian Guilder . ANG','NL Antillian Guilder . ANG') ;
   $this->objcountrydropdown->addOption('Namibia','Namibia') ;
   $this->objcountrydropdown->addOption('Nauru','Nauru') ;
   $this->objcountrydropdown->addOption('Nepal','Nepal') ;
   $this->objcountrydropdown->addOption('New Zealand','New Zealand') ;
   $this->objcountrydropdown->addOption('Nicaragua','Nicaragua') ;
   $this->objcountrydropdown->addOption('Nigeria','Nigeria') ;
   $this->objcountrydropdown->addOption('North Korea','North Korea') ;
   $this->objcountrydropdown->addOption('Norway','Norway') ;
   $this->objcountrydropdown->addOption('Oman','Oman') ;
   $this->objcountrydropdown->addOption('Panama','Panama') ;
   $this->objcountrydropdown->addOption('Peru','Peru') ;
   $this->objcountrydropdown->addOption('Pakistan','Pakistan') ;
 //  $this->objcountrydropdown->addOption('Palladium (oz.) . XPD','Palladium (oz.) . XPD') ;
//   $this->objcountrydropdown->addOption('Panamanian Balboa . PAB','Panamanian Balboa . PAB') ;
   $this->objcountrydropdown->addOption('Papua New Guinea','Papua New Guinea') ;
   $this->objcountrydropdown->addOption('Paraguay','Paraguay') ;
   $this->objcountrydropdown->addOption('Palau','Palau') ;
   $this->objcountrydropdown->addOption('Peru','Peru') ;
   $this->objcountrydropdown->addOption('Philippines','Philippines') ;
//   $this->objcountrydropdown->addOption('Platinum (oz.) . XPT','Platinum (oz.) . XPT') ;
   $this->objcountrydropdown->addOption('Poland','Poland') ;
   $this->objcountrydropdown->addOption('Portugal','Portugal') ;
   $this->objcountrydropdown->addOption('Qatar','Qatar') ;
   $this->objcountrydropdown->addOption('Romania','Romania') ;
//   $this->objcountrydropdown->addOption('Romanian New Lei . RON','Romanian New Lei . RON') ;
   $this->objcountrydropdown->addOption('Russia','Russia') ;
   $this->objcountrydropdown->addOption('Rwanda','Rwanda') ;
   $this->objcountrydropdown->addOption('Samoa','Samoa') ;
   $this->objcountrydropdown->addOption('Sao Tome/Principe','Sao Tome/Principe') ;
   $this->objcountrydropdown->addOption('Saudi Arabia','Saudi Arabia') ;
   $this->objcountrydropdown->addOption('Senegal','Senegal') ;
   $this->objcountrydropdown->addOption('Serbia and Montenegro','Serbia and Montenegro') ;
   $this->objcountrydropdown->addOption('Seychelles','Seychelles') ;
   $this->objcountrydropdown->addOption('Sierra Leone','Sierra Leone') ;
//   $this->objcountrydropdown->addOption('Silver (oz.) . XAG','Silver (oz.) . XAG') ;
   $this->objcountrydropdown->addOption('Singapore','Singapore') ;
   $this->objcountrydropdown->addOption('Slovakia','Slovakia') ;
   $this->objcountrydropdown->addOption('Slovenia','Slovenia') ;
   $this->objcountrydropdown->addOption('Solomon Islands','Solomon Islands') ;
   $this->objcountrydropdown->addOption('Somalia','Somalia') ;
   $this->objcountrydropdown->addOption('South Africa','South Africa') ;
   $this->objcountrydropdown->addOption('South-Korea','South-Korean') ;
   $this->objcountrydropdown->addOption('Spain','Spain') ;
   $this->objcountrydropdown->addOption('Sri Lanka','Sri Lanka') ;
//   $this->objcountrydropdown->addOption('St. Helena Pound . SHP','St. Helena Pound . SHP') ;
   $this->objcountrydropdown->addOption('Sudan','Sudan') ;
//   $this->objcountrydropdown->addOption('Sudanese Pound . SDP','Sudanese Pound . SDP') ;
   $this->objcountrydropdown->addOption('Suriname','Suriname') ;
//   $this->objcountrydropdown->addOption('Suriname Guilder . SRG','Suriname Guilder . SRG') ;
   $this->objcountrydropdown->addOption('Swaziland','Swaziland') ;
   $this->objcountrydropdown->addOption('Sweden','Sweden') ;
   $this->objcountrydropdown->addOption('Switzerland','Switzerland') ;
   $this->objcountrydropdown->addOption('Syria','Syria') ;
   $this->objcountrydropdown->addOption('Taiwan','Taiwan') ;
   $this->objcountrydropdown->addOption('Tajikistan','Tajikistan') ;
   $this->objcountrydropdown->addOption('Tanzania','Tanzania') ;
   $this->objcountrydropdown->addOption('Thailand','Thailand') ;
//   $this->objcountrydropdown->addOption('Tonga Paanga . TOP','Tonga Paanga . TOP') ;
   $this->objcountrydropdown->addOption('Trinidad/Tobago','Trinidad/Tobago') ;
   $this->objcountrydropdown->addOption('Tunisia','Tunisia') ;
   $this->objcountrydropdown->addOption('Turky','Turky') ;
 //  $this->objcountrydropdown->addOption('Turkish New Lira . TRY','Turkish New Lira . TRY') ;
   $this->objcountrydropdown->addOption('Turkmenistan','Turkmenistan') ;
   $this->objcountrydropdown->addOption('Tuvalu','Tuvalu') ;
   $this->objcountrydropdown->addOption('Uganda','Uganda') ;
   $this->objcountrydropdown->addOption('Ukraine','Ukraine') ;
   $this->objcountrydropdown->addOption('United States of America','United States of America') ;
   $this->objcountrydropdown->addOption('Uruguay','Uruguay') ;
   $this->objcountrydropdown->addOption('Utd. Arab Emir','Utd. Arab Emir') ;
   $this->objcountrydropdown->addOption('Uzbekistan ','Uzbekistan ') ;
//   $this->objcountrydropdown->addOption('Vanuatu Vatu . VUV','Vanuatu Vatu . VUV') ;
   $this->objcountrydropdown->addOption('Venezuela','Venezuela') ;
   $this->objcountrydropdown->addOption('Vietnamese','Vietnamese') ;
   $this->objcountrydropdown->addOption('West Bank','West Bank') ;
   $this->objcountrydropdown->addOption('Western Sahara','Western Sahara') ;
   $this->objcountrydropdown->addOption('Yemen','Yemen') ;
//  $this->objcountrydropdown->addOption('Yugoslavia','Yugoslavia') ;
   $this->objcountrydropdown->addOption('Zambia','Zambia') ;
   $this->objcountrydropdown->addOption('Zimbabwe','Zimbabwe') ;
//   $this->objcountrydropdown->addOption('Zimbabwe New Dollar . ZWN','Zimbabwe New Dollar . ZWN') ;
   $this->objcountrydropdown->size = 50;

/************************************************************************************************************************************************/
  $this->objButtonNext  = $this->newobject('button','htmlelements');
  $this->objButtonNext->setValue($strnext);
  $this->objButtonNext->name = 'next';
  $this->objButtonNext->setToSubmit();

/************************************************************************************************************************************************/

  /**
   *create text area for travel purpose
   */

  $textArea = 'travel';
  $this->objPurposeArea =& $this->newobject('textArea','htmlelements');
  $this->objPurposeArea->setRows(1);
  $this->objPurposeArea->setColumns(16);
  $this->objPurposeArea->setName($textArea);
  $this->objPurposeArea->setContent($purposearea);

  $textAreaaddy = 'address';
  $this->objAdressArea = $this->newobject('textArea','htmlelements');
  $this->objAdressArea->setRows(1);
  $this->objAdressArea->setColumns(16);
  $this->objAdressArea->setName($textAreaaddy);
  $this->objAdressArea->setContent($claimantaddress);

/************************************************************************************************************************************************/

  /**
   *create table to place form elements in for travel expense voucher "tev-template"   
   */

        $myTable=& $this->newObject('htmltable','htmlelements');
        $myTable->width='100%';
        $myTable->border='0';
        $myTable->cellspacing='1';
        $myTable->cellpadding='10';

        $myTable->startRow();
        $myTable->addCell($this->objcname->show());
        $myTable->addCell($this->objtxtname->show());
        $myTable->endRow();

        $myTable->startRow();
        $myTable->addCell($this->objtitle->show());
        $myTable->addCell($this->objtxttitle->show());
        $myTable->endRow();

        $myTable->startRow();
        $myTable->addCell($this->objaddress->show());
        $myTable->addCell($this->objAdressArea->show());
        $myTable->endRow();

        $myTable->startRow();
        $myTable->addCell($this->objcity->show());
        $myTable->addCell($this->objtxtcity->show());
        $myTable->endRow();

        $myTable->startRow();
        $myTable->addCell($this->objprovince->show());
        $myTable->addCell($this->objtxtprovince->show());
        $myTable->endRow();

        $myTable->startRow();
        $myTable->addCell($this->objpostalcode->show());
        $myTable->addCell($this->objtxtpostalcode->show());
        $myTable->endRow();

        $myTable->startRow();
        $myTable->addCell($this->objCountry->show());
        $myTable->addCell($this->objcountrydropdown->show());
        $myTable->endRow();

        $myTable->startRow();
        $myTable->addCell($this->objDescription->show());
        $myTable->addCell($this->objPurposeArea->show());
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
        $myTable->endRow();

        $myTable->startRow();
        $myTable->addCell(" " );
        $myTable->addCell("<div align=\"left\">".$this->objButtonNext->show()."</div>" );
        $myTable->endRow();
        
/************************************************************************************************************************************************/        
   

$striconinfo = $information ; 
$this->loadClass('tabbedbox', 'htmlelements');
$objtraveler = new tabbedbox();
$objtraveler->addTabLabel('Traveler Information');
$objtraveler->addBoxContent('<br />' ."<div align=\"center\">".  "<div class=\"error\">".$this->objInfoIcon->show() .$striconinfo .' '. $displayhelp. "</div>" . "</div>".'<br />'  . $myTable->show()  . '<br/>' );
/************************************************************************************************************************************************/
  /**
   *create form to place all elements in
   *create validation on these fields, required and maxlength   
   */
              
  $this->loadClass('form','htmlelements');
  $objtevForm = new form('tev',$this->uri(array('action'=>'submitclaimantinfo')));
  $objtevForm->id = 'tev';
  $objtevForm->displayType = 3;
  $objtevForm->addToForm($objtraveler->show()); //. '<br>'  . $objitinerary->show());
  $objtevForm->addRule('txtClaimantName',$valname,'required');
  $objtevForm->addRule('txtTitle', $valtitle,'required');
  $objtevForm->addRule('address',$valaddress,'required');
  $objtevForm->addRule('txtCity',$valcity,'required');
  $objtevForm->addRule('txtprovince',$valprovince,'required');
  $objtevForm->addRule('txtpostalcode',$valpostal,'required');
  $objtevForm->addRule('travel',$travpurpose,'required');
  
  
/************************************************************************************************************************************************/ 



  /**
   *display output to screen
   */

  echo  "<div align=\"center\">" . $this->objMainheading->show() . "</div>";
  echo  $objtevForm->show();

?>
