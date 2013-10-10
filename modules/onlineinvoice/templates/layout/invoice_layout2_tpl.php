<?php



/**
 * create objects of classes to be user
 */
   
$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$this->sideMenuBar=& $this->getObject('sidemenu','toolbar');
$cssLayout->setNumColumns(3);

$this->objHelp=& $this->getObject('helplink','help');
$objskin  = & $this->newObject('skin','skin');
$showlinklogout = $objskin->putLogout();
/**
 *define all items to display within the help popup window
 */
 
$instructions = $this->objLanguage->languageText('mod_onlineinvoice_helpinstruction','onlineinvoice');
$createinv  = $this->objLanguage->languageText('mod_onlineinvoice_createinv','onlineinvoice');
$tev  = $this->objLanguage->languageText('mod_onlineinvoice_tev','onlineinvoice');
$itinerary  = $this->objLanguage->languageText('mod_onlineinvoice_itinerary','onlineinvoice');
$perdiem  = $this->objLanguage->languageText('mod_onlineinvoice_perdiem','onlineinvoice');
$lodge  = $this->objLanguage->languageText('mod_onlineinvoice_lodge','onlineinvoice');
$incident = $this->objLanguage->languageText('mod_onlineinvoice_incident','onlineinvoice');
$exit  = $this->objLanguage->languageText('phrase_exit');
$save  = $this->objLanguage->languageText('word_save');
$str2  =  ucfirst($save);  
$str1  = ucfirst($exit);


$displayhelp  = $this->objHelp->show('mod_onlineinvoice_helpinstruction');  


/**
 *create a log out link and add to rightcolumn
 */
$help = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_helptext','onlineinvoice'));
$helpinstruction  = ucfirst($this->objLanguage->languageText('mod_onlineinvoice_help','onlineinvoice'));

$this->loadClass('button','htmlelements'); 
$this->objButtonExit  = new button('exitform', $str1);
$this->objButtonExit->setToSubmit();	

$this->objButtonSave  = new button('save', $str2);
$this->objButtonSave->setToSubmit();

/* jameel added */

$urltext = 'Supplies expenses';
$content = 'complete a supplies invoice';
$caption = '';
$url = $this->uri(array('action'=>'showsupplies'));
$this->objinvsupplies  = & $this->newObject('mouseoverpopup','htmlelements');
$this->objinvsupplies->mouseoverpopup($urltext,$content,$caption,$url); 

$urltext = 'Equipment expenses';
$content = 'complete an equipment invoice';
$caption = '';
$url = $this->uri(array('action'=>'showequipment'));
$this->objinvequip  = & $this->newObject('mouseoverpopup','htmlelements');
$this->objinvequip->mouseoverpopup($urltext,$content,$caption,$url); 

$urltext = 'ODC expenses';
$content = 'complete an ODC invoice';
$caption = '';
$url = $this->uri(array('action'=>'showodcexpenses'));
$this->objinvodc  = & $this->newObject('mouseoverpopup','htmlelements');
$this->objinvodc->mouseoverpopup($urltext,$content,$caption,$url); 




$this->loadClass('featurebox','navigation');
$objfeaturehelp = new featurebox($val,$val);
$display = $objfeaturehelp->show($help  . " " .'<br />' . $helpinstruction . '<br />'.$displayhelp );

$objfeature = new featurebox($val,$val);
$displaybuttons = $objfeature->show("<div align=\"center\">" . $this->objButtonSave->show() . ' ' .$this->objButtonExit->show(). "</div>");

$objfeaturelinks = new featurebox($val,$val);
$displaylinks = $objfeaturelinks->show($this->objinvsupplies->show() . $this->objinvequip->show() . $this->objinvodc->show() );

$this->loadClass('form','htmlelements');
$objForm = new form('invlayout',$this->uri(array('action'=>'saveexit')));
$objForm->displayType = 3;
$objForm->addToForm($display .  '<br />' . $displaylinks . '<br />'. $displaybuttons);

$objuserdetails = new featurebox($val,$val);
$displayleft = $objuserdetails->show($this->sideMenuBar->userDetails());
        

/**
 * Set the Content of left side column and right side column and middle column
 */  
$cssLayout->setLeftColumnContent($displayleft );
//$cssLayout->setRightColumnContent($objForm->show());
$cssLayout->setMiddleColumnContent($this->getContent()); 

/**
 * Display the Layout
 */  
echo $cssLayout->show();

/****************************************************************************************************************/

?>

