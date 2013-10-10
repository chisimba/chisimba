<?php
  /**
   *create a template to display the the travellers iteninary
   */
   
   
   /**
    *create all languge elements for all form labels
    */
           
    $deptDate  = $this->objLanguage->languageText('phrase_departuredate');
    $deptTime  = $this->objLanguage->languageText('phrase_departuretime');
    $deptCity  = $this->objLanguage->languageText('phrase_departurecity');
    $arrivalDate  = $this->objLanguage->languageText('phrase_arrivaldate');
    $arrivalTime  = $this->objLanguage->languageText('phrase_arrivaltime');
    $arrivalCity  = $this->objLanguage->languageText('phrase_arrivalcity');
    $btnsubmit  = $this->objLanguage->languageText('word_submit');
    $return = $this->objLanguage->languageText('mod_onlineinvoice_returntotravelexpense','onlineinvoice');
    $exit  = $this->objLanguage->languageText('phrase_exit');
    $next = $this->objLanguage->languageText('phrase_next');
/************************************************************************************************************************************************/
  /**
  *create heading -- travel itenirary
  */  
    $this->objIteninary =& $this->newObject('htmlheading','htmlelements');
    $this->objIteninary->type = 3;
    $this->objIteninary->str=$objLanguage->languageText('phrase_traveleritenirary');

/************************************************************************************************************************************************/
   
   /**
    *create all label elements
    */
           
    $lblDeparturedate = $Departuredate;
    $this->objdeparturedate  = $this->newObject('label','htmlelements');
    $this->objdeparturedate->label($deptDate,$lblDeparturedate);

    $lblDeparturetime= $Time;
    $this->objdeparturetime  = $this->newObject('label','htmlelements');
    $this->objdeparturetime->label($deptTime,$lblDeparturetime);

    $lblDeparturecity= $City;
    $this->objdeparturecity  = $this->newObject('label','htmlelements');
    $this->objdeparturecity->label($deptCity,$lblDeparturetime);

    $lblArrivaldate = $Adate;
    $this->objarrivaldate  = $this->newObject('label','htmlelements');
    $this->objarrivaldate->label($arrivalDate,$lblArrivaldate);
  
    $lblArrivaltime= $ATime;
    $this->objarrivaltime  = $this->newObject('label','htmlelements');
    $this->objarrivaltime->label($arrivalTime,$lblArrivaltime);

    $lblArrivalecity  = $ACity;
    $this->objarrivalcity  = $this->newObject('label','htmlelements');
    $this->objarrivalcity->label($arrivalCity,$lblArrivalecity);
/************************************************************************************************************************************************/
  /**
   *  create an instance of the datepicker class
   */
   
    $this->objdeptdate = $this->newObject('datepicker','htmlelements');
    $name = 'txtdeptddate';
    $date = '01-01-2006';
    $format = 'DD-MM-YYYY';
    $this->objdeptdate->setName($name);
    $this->objdeptdate->setDefaultDate($date);
    $this->objdeptdate->setDateFormat($format);

    $this->objarrivaldateobj = $this->newObject('datepicker','htmlelements');
    $name = 'txtarraivaldate';
    $date = '01-01-2006';
    $format = 'DD-MM-YYYY';
    $this->objarrivaldateobj->setName($name);
    $this->objarrivaldateobj->setDefaultDate($date);
    $this->objarrivaldateobj->setDateFormat($format);
/************************************************************************************************************************************************/
  /**
   *create instance of the dropdown list class
   */
   
   $name  = 'departuretime';
   $this->objdeparturetimedropdown  = $this->newObject('dropdown','htmlelements');
   $this->objdeparturetimedropdown->dropdown($name);
   $this->objdeparturetimedropdown->addOption('00:00 midnight','00:00 midnight') ;
   $this->objdeparturetimedropdown->addOption('1:00 am','1:00 am') ;
   $this->objdeparturetimedropdown->addOption('2:00 am','2:00 am') ;
   $this->objdeparturetimedropdown->addOption('3:00 am','3:00 am') ;
   $this->objdeparturetimedropdown->addOption('4:00 am','4:00 am') ;
   $this->objdeparturetimedropdown->addOption('5:00 am','5:00 am') ;
   $this->objdeparturetimedropdown->addOption('6:00 am','6:00 am') ;
   $this->objdeparturetimedropdown->addOption('7:00 am','7:00 am') ;    
   $this->objdeparturetimedropdown->addOption('8:00 am','8:00 am') ;
   $this->objdeparturetimedropdown->addOption('9:00 am','9:00 am') ;
   $this->objdeparturetimedropdown->addOption('10:00 am','10:00 am') ;
   $this->objdeparturetimedropdown->addOption('11:00 am','11:00 am') ;
   $this->objdeparturetimedropdown->addOption('12:00 noon','12:00 noon') ;
   $this->objdeparturetimedropdown->addOption('13:00','13:00') ;
   $this->objdeparturetimedropdown->addOption('14:00','14:00') ;
   $this->objdeparturetimedropdown->addOption('15:00','15:00') ;
   $this->objdeparturetimedropdown->addOption('16:00','16:00') ;
   $this->objdeparturetimedropdown->addOption('17:00','17:00') ;
   $this->objdeparturetimedropdown->addOption('18:00','18:00') ;
   $this->objdeparturetimedropdown->addOption('19:00','19:00') ;
   $this->objdeparturetimedropdown->addOption('20:00','20:00') ;
   $this->objdeparturetimedropdown->addOption('21:00','21:00') ;
   $this->objdeparturetimedropdown->addOption('22:00','22:00') ;
   $this->objdeparturetimedropdown->addOption('23:00','23:00') ;
   
   $arrivalname  = 'arrivaltime';
   $this->objarrivaltimedropdown  = $this->newObject('dropdown','htmlelements');
   $this->objarrivaltimedropdown->dropdown($arrivalname);
   $this->objarrivaltimedropdown->addOption('00:00 midnight','00:00 midnight') ;
   $this->objarrivaltimedropdown->addOption('1:00 am','1:00 am') ;
   $this->objarrivaltimedropdown->addOption('2:00 am','2:00 am') ;
   $this->objarrivaltimedropdown->addOption('3:00 am','3:00 am') ;
   $this->objarrivaltimedropdown->addOption('4:00 am','4:00 am') ;
   $this->objarrivaltimedropdown->addOption('5:00 am','5:00 am') ;
   $this->objarrivaltimedropdown->addOption('6:00 am','6:00 am') ;
   $this->objarrivaltimedropdown->addOption('7:00 am','7:00 am') ;    
   $this->objarrivaltimedropdown->addOption('8:00 am','8:00 am') ;
   $this->objarrivaltimedropdown->addOption('9:00 am','9:00 am') ;
   $this->objarrivaltimedropdown->addOption('10:00 am','10:00 am') ;
   $this->objarrivaltimedropdown->addOption('11:00 am','11:00 am') ;
   $this->objarrivaltimedropdown->addOption('12:00 noon','12:00 noon') ;
   $this->objarrivaltimedropdown->addOption('13:00','13:00') ;
   $this->objarrivaltimedropdown->addOption('14:00','14:00') ;
   $this->objarrivaltimedropdown->addOption('15:00','15:00') ;
   $this->objarrivaltimedropdown->addOption('16:00','16:00') ;
   $this->objarrivaltimedropdown->addOption('17:00','17:00') ;
   $this->objarrivaltimedropdown->addOption('18:00','18:00') ;
   $this->objarrivaltimedropdown->addOption('19:00','19:00') ;
   $this->objarrivaltimedropdown->addOption('20:00','20:00') ;
   $this->objarrivaltimedropdown->addOption('21:00','21:00') ;
   $this->objarrivaltimedropdown->addOption('22:00','22:00') ;
   $this->objarrivaltimedropdown->addOption('23:00','23:00') ;
   
   $minutes  = 'minutes';
   $this->objminutes  = $this->newObject('dropdown','htmlelements');
   $this->objminutes->addOption('00:00','00:00') ;
   $this->objminutes->addOption('00:01','00:01') ;
   $this->objminutes->addOption('00:02','00:02') ;
   $this->objminutes->addOption('00:03','00:03') ;
   $this->objminutes->addOption('00:04','00:04') ;
   $this->objminutes->addOption('00:05','00:05') ;
   $this->objminutes->addOption('00:06','00:06') ; 
   $this->objminutes->addOption('00:07','00:07') ; 
   $this->objminutes->addOption('00:08','00:08') ;
   $this->objminutes->addOption('00:09','00:09') ;
   $this->objminutes->addOption('00:10','00:10') ;
   $this->objminutes->addOption('00:11','00:11') ;
   $this->objminutes->addOption('00:12','00:12') ;
   $this->objminutes->addOption('00:13','00:13') ;
   $this->objminutes->addOption('00:14','00:14') ;
   $this->objminutes->addOption('00:15','00:15') ;
   $this->objminutes->addOption('00:16','00:16') ;
   $this->objminutes->addOption('00:17','00:17') ;
   $this->objminutes->addOption('00:18','00:18') ;
   $this->objminutes->addOption('00:19','00:19') ;
   $this->objminutes->addOption('00:20','00:20') ;
   $this->objminutes->addOption('00:21','00:21') ;
   $this->objminutes->addOption('00:22','00:22') ;
   $this->objminutes->addOption('00:23','00:23') ;
   $this->objminutes->addOption('00:24','00:24') ;
   $this->objminutes->addOption('00:25','00:25') ;
   $this->objminutes->addOption('00:26','00:26') ; 
   $this->objminutes->addOption('00:27','00:27') ; 
   $this->objminutes->addOption('00:28','00:28') ;
   $this->objminutes->addOption('00:29','00:29') ;
   $this->objminutes->addOption('00:30','00:30') ;
   $this->objminutes->addOption('00:31','00:31') ;
   $this->objminutes->addOption('00:32','00:32') ;
   $this->objminutes->addOption('00:33','00:33') ;
   $this->objminutes->addOption('00:34','00:34') ;
   $this->objminutes->addOption('00:35','00:35') ;
   $this->objminutes->addOption('00:36','00:36') ;
   $this->objminutes->addOption('00:37','00:37') ;
   $this->objminutes->addOption('00:38','00:38') ;
   $this->objminutes->addOption('00:39','00:39') ;
   $this->objminutes->addOption('00:40','00:40') ;
   $this->objminutes->addOption('00:41','00:41') ;
   $this->objminutes->addOption('00:42','00:42') ;
   $this->objminutes->addOption('00:43','00:43') ;
   $this->objminutes->addOption('00:44','00:44') ;
   $this->objminutes->addOption('00:45','00:45') ;
   $this->objminutes->addOption('00:46','00:46') ; 
   $this->objminutes->addOption('00:47','00:47') ; 
   $this->objminutes->addOption('00:48','00:48') ;
   $this->objminutes->addOption('00:49','00:49') ;
   $this->objminutes->addOption('00:50','00:50') ;
   $this->objminutes->addOption('00:51','00:51') ;
   $this->objminutes->addOption('00:52','00:52') ;
   $this->objminutes->addOption('00:53','00:53') ;
   $this->objminutes->addOption('00:54','00:54') ;
   $this->objminutes->addOption('00:55','00:55') ;
   $this->objminutes->addOption('00:56','00:56') ; 
   $this->objminutes->addOption('00:57','00:57') ; 
   $this->objminutes->addOption('00:58','00:58') ;
   $this->objminutes->addOption('00:59','00:59') ;
   $this->objminutes->addOption('00:60','00:60') ;
   
   
    
/************************************************************************************************************************************************/

  /**
   *create all text inputs 
   */     
   
    /*$this->objtxtdepttime = $this->newObject('textinput','htmlelements');
    $this->objtxtdepttime->name   = "txtdepttime";
    $this->objtxtdepttime->value  = "";*/

    $this->objtxtdeptcity = $this->newObject('textinput','htmlelements');
    $this->objtxtdeptcity->name   = "txttxtdeptcity";
    $this->objtxtdeptcity->value  = "";

    $this->objtxtarrivtime = $this->newObject('textinput','htmlelements');
    $this->objtxtarrivtime->name   = "txtarrivtime";
    $this->objtxtarrivtime->value  = "";

    $this->objtxtarrivcity = $this->newObject('textinput','htmlelements');
    $this->objtxtarrivcity->name   = "txtarrivcity";
    $this->objtxtarrivcity->value  = "";
/************************************************************************************************************************************************/  
  /**
   *create all form buttons 
   */
   
    $this->objButtonSave  = $this->newobject('button','htmlelements');
    $this->objButtonSave->setValue($btnsubmit);
    $this->objButtonSave->setToSubmit();

    
    
   /**
   *create a link to return to the tev emplate
   */         
    
  /*$this->objreturn  =& $this->newobject('link','htmlelements');
  $this->objreturn->link($this->uri(array('action'=>'createtev'))); /*takes user to the next template -- per diem expense*/
 // $this->objreturn->link = $return;
 
  /**
   *create links to exit the template and got to the next template
   */     
    
  $this->objexit  =& $this->newobject('link','htmlelements');
  $this->objexit->link($this->uri(array('action'=>'NULL')));  /* -- returns banck to original invoice template*/
  $this->objexit->link = $exit;

  $this->objnext  =& $this->newobject('link','htmlelements');
  $this->objnext->link($this->uri(array('action'=>'createexpenses'))); /*takes user to the next template -- per diem expense*/
  $this->objnext->link = $next;
/************************************************************************************************************************************************/   
    /**
     *create table to place all form elements for the itenirary template 
    */
      
        $myTabIten  = $this->newObject('htmltable','htmlelements');
        $myTabIten->width='60%';
        $myTabIten->border='0';
        $myTabIten->cellspacing = '1';
        $myTabIten->cellpadding ='10';

            
        $myTabIten->startRow();
        $myTabIten->addCell($this->objdeparturedate->show());
        $myTabIten->addCell($this->objdeptdate->show());
        $myTabIten->endRow();

        $myTabIten->startRow();
        $myTabIten->addCell($this->objdeparturetime->show());
        $myTabIten->addCell($this->objdeparturetimedropdown->show() . ' ' . $this->objminutes->show());
        $myTabIten->endRow();

        $myTabIten->startRow();
        $myTabIten->addCell($this->objdeparturecity->show());
        $myTabIten->addCell($this->objtxtdeptcity->show());
        $myTabIten->endRow();

        $myTabIten->startRow();
        $myTabIten->addCell($this->objarrivaldate->show());
        $myTabIten->addCell($this->objarrivaldateobj->show());
        $myTabIten->endRow();

        $myTabIten->startRow();
        $myTabIten->addCell($this->objarrivaltime->show());
        $myTabIten->addCell($this->objarrivaltimedropdown->show().  ' ' . $this->objminutes->show());
        $myTabIten->endRow();

        $myTabIten->startRow();
        $myTabIten->addCell($this->objarrivalcity->show());
        $myTabIten->addCell($this->objtxtarrivcity->show());
        $myTabIten->endRow();

        $myTabIten->startRow();
        $myTabIten->addCell($this->objButtonSave->show());
        //$myTabIten->addCell($this->objreturn->show());
        $myTabIten->endRow();
        
        $myTabIten->startRow();
        $myTabIten->addCell($this->objexit->show());
        $myTabIten->addCell($this->objnext->show());
        $myTabIten->endRow();
        
        
/************************************************************************************************************************************************/
        
$this->loadClass('tabbedbox', 'htmlelements');
$objonewayitinerary = new tabbedbox();
$objonewayitinerary->addTabLabel('One Way Trip');
$objonewayitinerary->addBoxContent('<br>' . $myTabIten->show() . '<br>');               

        
/************************************************************************************************************************************************/
    /**
     *create the form for a one-way-trip itenirary template
     */         
      
      $this->loadClass('form','htmlelements');
      $objiteniraryForm = new form('itinerary',$this->uri(array('action'=>'submititinerary')));
      $objiteniraryForm->displayType = 3;
      $objiteniraryForm->addToForm($objonewayitinerary->show());	
      //$objLodgeForm->addRule('txtDate', 'Must be number','required'); 

/************************************************************************************************************************************************/    

    /**
    *display the form / output
    */

    echo  "<div align=\"center\">" . $this->objIteninary->show() . "</div>";        
    echo  $objiteniraryForm->show();  
?>
