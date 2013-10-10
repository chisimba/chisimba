<?php
//Set up the button class to make the edit, add and delet icons
$objButtons = & $this->getObject('navbuttons', 'navigation');

$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');


// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

//Set the content of the left side column
$leftSideColumn = $this->objLanguage->languagetext("mod_maillist_intro");

// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);// Add the heading to the content
$this->objH =& $this->getObject('htmlheading', 'htmlelements');
$this->objH->type=1; //Heading <h3>
$this->objH->str=$objLanguage->languageText("mod_maillist_title");
$rightSideColumn = $this->objH->show();

//create a form to input the server details.
$objForm = new form('maildetails',$this->uri(array('action'=>'setinfo')));
$fieldset = $this->getObject('fieldset', 'htmlelements');
$fieldset->setLegend($objLanguage->languageText('maillist_details'));

$table = $this->getObject('htmltable', 'htmlelements');
$table->cellpadding = 5;

$table->startRow();
$label = new label($objLanguage->languageText('mail_server').':', 'input_server');
$server = new textinput('server');
$server->value=$this->getParam('server');
$table->addCell($label->show(), 150, NULL, 'right');
$table->addCell($server->show().' '.$objLanguage->languageText('mail_server'));
$table->endRow();

$table->startRow();
$label3 = new label($objLanguage->languageText('mail_username').':', 'input_username');
$username = new textinput('username');
$username->value=$this->getParam('username');
$table->addCell($label3->show(), 150, NULL, 'right');
$table->addCell($username->show().' '.$objLanguage->languageText('mail_username'));
$table->endRow();

$table->startRow();
$label1 = new label($objLanguage->languageText('mail_password').':', 'input_password');
$password = new textinput('password',null,'password');
$password->value=$this->getParam('password');
$table->addCell($label1->show(), 150, NULL, 'right');
$table->addCell($password->show().' '.$objLanguage->languageText('mail_password'));
$table->endRow();

$table->startRow();
$label2 = new label($objLanguage->languageText('mail_listemail').':', 'input_listemail');
$password = new textinput('listemail',null,'listemail');
$password->value=$this->getParam('listemail');
$table->addCell($label2->show(), 150, NULL, 'right');
$table->addCell($password->show().' '.$objLanguage->languageText('mail_listemail'));
$table->endRow();

$table->startRow();
$label4 = new label($objLanguage->languageText('mail_listname').':', 'input_listname');
$password = new textinput('listname',null,'listname');
$password->value=$this->getParam('listname');
$table->addCell($label4->show(), 150, NULL, 'right');
$table->addCell($password->show().' '.$objLanguage->languageText('mail_listname'));
$table->endRow();


$fieldset->addContent($table->show());

$objForm->addToForm($fieldset->show());

$objForm->addRule('server',$objLanguage->languageText('need_server'),'required');
$objForm->addRule('username',$objLanguage->languageText('need_username'),'required');
$objForm->addRule('password',$objLanguage->languageText('need_password'),'required');
$objForm->addRule('listemail',$objLanguage->languageText('need_listemail'),'required');
$objForm->addRule('listname',$objLanguage->languageText('need_listname'),'required');

$this->objButton=&new button('submitform');
$this->objButton->setValue($this->objLanguage->languageText('word_submit'));
$this->objButton->setToSubmit();

$objForm->addToForm('<p>'.$this->objButton->show().'</p>');

//Add the table to the centered layer
$rightSideColumn .= $objForm->show();  //the form


// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);

// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);

//Output the content to the page
echo $cssLayout->show();
?>