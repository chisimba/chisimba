<?php
    /**
     *create a template display the output for claimant details and itinerary information
     */
     
     /**
      *load all classes
      */
      
      $this->loadClass('htmlheading', 'htmlelements');
      $this->loadClass('label','htmlelements');
      $this->loadClass('form','htmlelements');
      $this->loadClass('htmltable','htmlelements');
      $this->loadClass('tabbedbox', 'htmlelements');
      $this->loadClass('button','htmlelements');
      $this->loadClass('checkbox','htmlelements');
       //$heading = $this->new
/****************************************************************************************************************************/      
      /**
       *define all language elements
       */
       
       $expensesheet  = $this->objLanguage->languageText('mod_onlineinvoice_travelsheet','onlineinvoice');
       $mainheading = $this->objLanguage->languageText('mod_onlineinvoice_claimantdetails','onlineinvoice');  
       $beginDate = $objLanguage->languageText('phrase_begindate');
       $endDate  = $objLanguage->languageText('phrase_enddate');
       $name = $this->objLanguage->languageText('phrase_claimantname');
       $title  = $this->objLanguage->languageText('phrase_claimanttitle');
       $address  = $this->objLanguage->languageText('phrase_mailingaddress');
       $city = $this->objLanguage->languageText('word_city');
       $province = $this->objLanguage->languageText('word_province');
       $postalcode = $this->objLanguage->languageText('phrase_postalcode');
       $country  = $this->objLanguage->languageText('word_country');
       $travelpurpose = $this->objLanguage->languageText('mod_onlineinvoice_travelpurpose','onlineinvoice');
       $btnEdit  = $this->objLanguage->languageText('word_edit');
       $btnSave = $this->objLanguage->languageText('word_save');
       /********************************************************************/
       $deptDate  = $this->objLanguage->languageText('phrase_departuredate');
       $str1  = strtoupper($deptDate);
       $deptTime  = $this->objLanguage->languageText('phrase_departuretime');
       $str2  = strtoupper($deptTime);
       $deptCity  = $this->objLanguage->languageText('phrase_departurecity');
       $str3  = strtoupper($deptCity);
       $arrivalDate  = $this->objLanguage->languageText('phrase_arrivaldate');
       $str4  = strtoupper($arrivalDate);
       $arrivalTime  = $this->objLanguage->languageText('phrase_arrivaltime');
       $str5  = strtoupper($arrivalTime);
       $arrivalCity  = $this->objLanguage->languageText('phrase_arrivalcity');
       $str6  = strtoupper($arrivalCity);
       /**********************************************************************/
       $expensesdate = $this->objLanguage->languageText('word_date');
       $strdate = strtoupper($expensesdate);
       $breakfast  = $this->objLanguage->languageText('word_breakfast');
       $strbreakfast  = strtoupper($breakfast);
       $lunch  = $this->objLanguage->languageText('word_lunch');
       $strlunch  = strtoupper($lunch);
       $dinner = $this->objLanguage->languageText('word_dinner');
       $strdinner = strtoupper($dinner);
       //$location = $this->objLanguage->languageText('word_location');
    
       
/****************************************************************************************************************************/       
       /**
        *create all heading elements
        */    
        
         $strheading  = strtoupper($expensesheet);
         $objtravelsheet  = new htmlHeading();
         $objtravelsheet->type  = 1;
         $objtravelsheet->str = $strheading;
        
         $str = strtoupper($mainheading); 
         $objoutputheading  = new htmlHeading();  
         $objoutputheading->type = 4;
         $objoutputheading->str = $str;
/****************************************************************************************************************************/
      /**
       *create button elements
       */

       
       $objeditbutton  = new button('edit', $btnEdit);
       $objeditbutton->setToSubmit();
    
       $objsavebutton  = new button('save', $btnSave);
       $objsavebutton->setToSubmit();
       

       
                    
/****************************************************************************************************************************/
         /**
          *call the session variable that contains array of information entered by the user
          *assign the array session to a variable $dateinfo
          *loop through the array variable and assign each element to a variable                    
          */
          $dateinfo []    =  $this->getSession('invoicedata');
          while (list ($count, $values)  = each ($dateinfo)) {
              if($count == 'begindate') {
              $bdate = $values;
              }
              if($count ==  'enddate') {
              $edate  = $values;
              }
          }
/****************************************************************************************************************************/
         /**
          *call the session variable that contains array of information entered by the user
          *assign the array session to a variable $claimantinfo
          *loop through the array variable and assign each element to a variable                    
          */   
         //$claimantinfo  = array();                       
         $claimantinfo = $this->getSession('claimantdata');
           while (list ($key, $val)  = each ($claimantinfo)) {
              if($key == 'claimanantname') {
              $n = strtoupper($val);
              }
              if($key ==  'title') {
              $t  = strtoupper($val);
              }
              if($key ==  'mailaddress')  {
              $a  = strtoupper($val);
              }
              if($key ==  'city')  {
              $ct = strtoupper($val);
              }
              if($key ==  'province')  {
              $p  = strtoupper($val);
              }
              if($key ==  'postalcode')  {
              $po = strtoupper($val);
              }
              if($key ==  'country')  {
              $coun = strtoupper($val);
              }
              if($key ==  'travelpurpose') {
              $purpose  = strtoupper($val);
              }
              
                 
           }
           
             
/****************************************************************************************************************************/
        /**
         *call the session variable that contains array of information entered by the user
         *assign the array session to a variable $itineraryinfo
         *loop through the array variable and assign each element to a variable
         */         
         $itinerarydetails  = $this->getSession('addmultiitinerary');
            while(list($key,$val)  = each($itinerarydetails)) 
            {
              while(list($subkey,$subval) = each($val))
              {
                  //echo 'hi';
                  //die;
                  if($subkey == 'departuredate') {
                  $displaydepdate = $subval;
                  //echo $displaydepdate;
                  //die; 
                  }
                  if($subkey == 'departuretime') {
                  $displaydepttime == $subval;
                  }
                  if($subkey == 'departurecity') {
                  $displaydeptcity == $subval;
                  }
                  if($subkey == 'arrivaledate') {
                  $displayarrivdate = $subval;
                  }
                  if($subkey == 'arrivaltime') {
                  $displayarrivtime = $subval;
                  }
                  if($subkey == 'arrivalcity') {
                  $displayarrivcity = $subval;
                  }
              }
              
            
           }                                  
/****************************************************************************************************************************/
      /**
       *call the session variable that contains the array o information entered by the user
       *assign the array session to a variable $itineraryinfo
       *loop through the array variable and assign each element to a varaible
       */
       
       $perdiemdetails = $this->getSession('perdiemdetails'); 
          while(list($num,$listval) = each($perdiemdetails))  {
          if($num ==  'date') {
          $showperdiemdate  = $listval;
          }
          if($num == 'breakfastchoice')  {
          $breakfastchoice = $listval;
          }
          if($num == 'breakfastlocation')  {
          $breakfastloc   = $listval;
          }
          if($num ==  'breakfastrate') {
          $breakfastrate  = $listval;
          }
          if($num == 'lunchchoice') {
          $lunchchoice  = $listval;
          }
          if($num == 'lunchlocation') {
          $lunchlocation = $listval;
          }
          if($num ==  'txtlunchRate')  {
          $txtlunchRate = $listval;
          }
          if($num == 'dinnerchoice') {
          $dinnerchoice = $listval;
          }
          if($num == 'dinnerlocation') {
          $dinnerlocation = $listval;
          }
          if($num == 'dinnerrate') {
          $dinnerrate = $listval;
          }    
       
       }              
/****************************************************************************************************************************/         
         /**
          *get dedailts from the lodge info by calling the session variable
          */
          
         // $lodgedetails = array();
          $lodgeinfo [] = $this->getSession('lodgedetails');
             while(list($lodgekey,$lodgeval) = each($lodgeinfo)) {
               while(list($subkey,$subval) = each($lodgeval))
                {
                 
                if($lodgekey  ==  'date'){
                $lodgedate  = $subval;
                //echo $lodgedate;
                //  die;
                }
                if($lodgekey  ==  'vendor'){
                $lodgevendor  = $subval;
                //echo $lodgevendor;
                //die;
                }
                if($lodgekey  ==  'currency'){
                $lodgecurrency  = $subval;
                }
                if($lodgekey  ==  'cost'){
                $lodgecost  = $subval;
                }
                if($lodgekey  ==  'exchangerate'){
                $lodgerate  = $subval;
                }
              }
            }
                             
         
/****************************************************************************************************************************/ 
       /**
        *create a table to place all lodge info in
        */
         $myTableLodge =  new htmlTable;      
         $myTableLodge->width='70%';
         $myTableLodge->border='10';
         $myTableLodge->cellspacing = '5';
         $myTableLodge->cellpadding ='5';

         $myTableLodge->startRow();
         $myTableLodge->addCell('Date');
         $myTableLodge->addCell($lodgedate);
         $myTableLodge->endRow();
         
         $myTableLodge->startRow();
         $myTableLodge->addCell('Vendor');
         $myTableLodge->addCell($lodgevendor);
         $myTableLodge->endRow();
         
         $myTableLodge->startRow();
         $myTableLodge->addCell('Currency');
         $myTableLodge->addCell($lodgecurrency);
         $myTableLodge->endRow();

         $myTableLodge->startRow();
         $myTableLodge->addCell('Cost');
         $myTableLodge->addCell($lodgecost);
         $myTableLodge->endRow();
         
         $myTableLodge->startRow();
         $myTableLodge->addCell('Exchange Rate');
         $myTableLodge->addCell($lodgerate);
         $myTableLodge->endRow();
       
/****************************************************************************************************************************/
        /**
         *create a table to place all claimant form elements in
         */ 
         
         $myTable =  new htmlTable;      
         $myTable->width='70%';
         $myTable->border='10';
         $myTable->cellspacing = '5';
         $myTable->cellpadding ='5';

         
/*        $myTable->startHeaderRow();
        $myTable->addHeaderCell($beginDate);
        $myTable->addHeaderCell($endDate);
        $myTable->addHeaderCell($name);
        $myTable->addHeaderCell($title);
        $myTable->addHeaderCell($address);
        $myTable->addHeaderCell($city);
        $myTable->addHeaderCell($province);
        $myTable->addHeaderCell($postalcode);
        $myTable->addHeaderCell($country);
        $myTable->addHeaderCell($travelpurpose);
        $myTable->endHeaderRow();
        
        $myTable->startRow();
        $myTable->addCell($bdate);
        $myTable->addCell($edate);
        $myTable->addCell($n);
        $myTable->addCell($t);
        $myTable->addCell($p);
        $myTable->addCell($a);
        
        $myTable->addCell($ct);
        $myTable->addCell($p);
        $myTable->addCell($po);
        $myTable->addCell($coun);
        $myTable->addCell($purpose);
        $myTable->endRow();*/
         
         
         
         $myTable->startRow();
         $myTable->addCell($beginDate);
         $myTable->addCell($bdate);
         $myTable->endRow();
         
         $myTable->startRow();
         $myTable->addCell($endDate);
         $myTable->addCell($edate);
         $myTable->endRow();
         
         $myTable->startRow();
         $myTable->addCell($name);
         $myTable->addCell($n);
         $myTable->endRow();
         
         $myTable->startRow();
         $myTable->addCell($title);
         $myTable->addCell($t);
         $myTable->endRow();
         
         $myTable->startRow();
         $myTable->addCell($address);
         $myTable->addCell($a);
         $myTable->endRow();
         
         $myTable->startRow();
         $myTable->addCell($city);
         $myTable->addCell($ct);
         $myTable->endRow();
         
         $myTable->startRow();
         $myTable->addCell($province);
         $myTable->addCell($p);
         $myTable->endRow();
         
         $myTable->startRow();
         $myTable->addCell($postalcode);
         $myTable->addCell($po);
         $myTable->endRow();
         
         $myTable->startRow();
         $myTable->addCell($country);
         $myTable->addCell($coun);
         $myTable->endRow();
         
         $myTable->startRow();
         $myTable->addCell(ucfirst($travelpurpose));
         $myTable->addCell($purpose);
         $myTable->endRow(); 
/******************************************************************************************************************************************************/         
         /**
          *create a table for form buttons
          */                    
        
         $myTabbuttons =  new htmlTable;      
         $myTabbuttons->width='20%';
         $myTabbuttons->border='0';
         $myTabbuttons->cellspacing = '10';
         $myTabbuttons->cellpadding ='10';
         
         $myTabbuttons->startRow();
         $myTabbuttons->addCell($objsavebutton->show());
         $myTabbuttons->addCell($objeditbutton->show());
         $myTabbuttons->endRow();
/******************************************************************************************************************************************************/         
        /**
         *create a table to place all itinerary elements in
         */                  
         $myTabItinerary =  new htmlTable;      
         $myTabItinerary->width='70%';
         $myTabItinerary->border='10';
         $myTabItinerary->cellspacing = '5';
         $myTabItinerary->cellpadding ='5';

         /*$myTabItinerary->startHeaderRow();
         $myTabItinerary->addHeaderCell($str1);
         $myTabItinerary->addHeaderCell($str2);
         $myTabItinerary->addHeaderCell($str3);
         $myTabItinerary->addHeaderCell($str4);
         $myTabItinerary->addHeaderCell($str5);
         $myTabItinerary->addHeaderCell($str6);
         $myTabItinerary->endHeaderRow();*/
         
         $myTabItinerary->startRow();
         $myTabItinerary->addCell($str1);
         $myTabItinerary->addCell($displaydepdate);
         $myTabItinerary->endRow();
         
         $myTabItinerary->startRow();
         $myTabItinerary->addCell($str2);
         $myTabItinerary->addCell($displaydepttime);
         $myTabItinerary->endRow();
         
         $myTabItinerary->startRow();
         $myTabItinerary->addCell($str3);
         $myTabItinerary->addCell($displaydeptcity);
         $myTabItinerary->endRow();
         
         $myTabItinerary->startRow();
         $myTabItinerary->addCell($str4);
         $myTabItinerary->addCell($displayarrivdate);
         $myTabItinerary->endRow();
         
         $myTabItinerary->startRow();
         $myTabItinerary->addCell($str5);
         $myTabItinerary->addCell($displayarrivtime);
         $myTabItinerary->endRow();
         
         $myTabItinerary->startRow();
         $myTabItinerary->addCell($str6);
         $myTabItinerary->addCell($displayarrivcity);
         $myTabItinerary->endRow();
         
         /**
          *create a table to place per diem expenses in
          */                   
          
         
        $myTablePerdiem = $this->newObject('htmltable','htmlelements');
        $myTablePerdiem->width='70%';
        $myTablePerdiem->border= '5';
        $myTablePerdiem->cellspacing='5';
        $myTablePerdiem->cellpadding='5';
            
        $myTablePerdiem->startHeaderRow();
        $myTablePerdiem->addHeader('Breakfast');
        $myTablePerdiem->addHeaderCell('Location');
        $myTablePerdiem->addHeaderCell('Rate');
        $myTablePerdiem->endHeaderRow();    

        $myTablePerdiem->startRow();
        $myTablePerdiem->addCell($breakfastloc);
        $myTablePerdiem->addCell($breakfastrate);
        $myTablePerdiem->endRow();
      /********************************************************************/  
        $myTablelunch = $this->newObject('htmltable','htmlelements');
        $myTablelunch->width='70%';
        $myTablelunch->border='5';
        $myTablelunch->cellspacing='5';
        $myTablelunch->cellpadding='5';
            
        $myTablelunch->startHeaderRow();
        $myTablelunch->addHeader('Lunch');
        $myTablelunch->addHeaderCell('Location');
        $myTablelunch->addHeaderCell('Rate');
        $myTablelunch->endHeaderRow();    

        $myTablelunch->startRow();
        $myTablelunch->addCell($lunchlocation);
        $myTablelunch->addCell($txtlunchRate);
        $myTablelunch->endRow();  

      /********************************************************************/  
        $myTabledinner = $this->newObject('htmltable','htmlelements');
        $myTabledinner->width='70%';
        $myTabledinner->border='5';
        $myTabledinner->cellspacing='5';
        $myTabledinner->cellpadding='5';
            
        $myTabledinner->startHeaderRow();
        $myTabledinner->addHeader('Dinner');
        $myTabledinner->addHeaderCell('Location');
        $myTabledinner->addHeaderCell('Rate');
        $myTabledinner->endHeaderRow();    

        $myTabledinner->startRow();
        $myTabledinner->addCell($dinnerlocation);
        $myTabledinner->addCell($dinnerrate);
        $myTabledinner->endRow();  
  
/****************************************************************************************************************************/
        /**
         *create a tabbed box element
         */
         
         $objtabbedbox = new tabbedbox();
         $objtabbedbox->addTabLabel('Claimant Information');
         $objtabbedbox->addBoxContent("<div align=\"center\">" . '<br />' . $myTable->show()  . "</div>"  . '<br />');        
         
         $objtabItinerary = new tabbedbox();
         $objtabItinerary->addTabLabel('Itinerary Information');
         $objtabItinerary->addBoxContent("<div align=\"center\">" . '<br />' .$myTabItinerary->show()  . "</div>". '<br />');        
         
         $objtabExpense = new tabbedbox();
         $objtabExpense->addTabLabel('Per Diem Expenses');
         $objtabExpense->addBoxContent("<div align=\"center\">" .'<br />' . $myTablePerdiem->show() . ' ' . $myTablelunch->show() . $myTabledinner->show()  . '<br / >'. "</div>");
         
         $objtablodgeExpense = new tabbedbox();
         $objtablodgeExpense->addTabLabel('Lodge Expenses');
         $objtablodgeExpense->addBoxContent("<div align=\"center\">" . '<br />' . $myTableLodge->show(). '<br / >' . "</div>");
               
/****************************************************************************************************************************/
                                
        /**
         *create a form element
         */                        
                  
        $objClaimantForm = new form('claimantoutput',$this->uri(array('action'=>'savealldetails')));
        $objClaimantForm->displayType = 3;
        $objClaimantForm->addToForm($objtabbedbox->show() . '<br>'  . $objtabItinerary->show() .  '<br>' .  '<br>'  .$objtabExpense->show() .$objtablodgeExpense->show(). $myTabbuttons->show());	
        //$objClaimantForm->addRule('txtDate', 'Must be number','required');             

/****************************************************************************************************************************/
        /**
         *display output to screen
         */
         echo  "<div align=\"center\">" . $objtravelsheet->show() . "</div>";
         echo  '<br>' . '<br>'  . $objoutputheading->show();
         echo  '<br>' . '<br>'  . $objClaimantForm->show();   
        // echo '<br>'  .                  
                                             

?>
