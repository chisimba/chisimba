<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

//step 2 add

//add step 1 template
$objH = new htmlheading();
$objForm = new form();

$inpContextCode =  new textinput();
$inpMenuText = new textinput();
$inpAbout = & $this->newObject('htmlarea','htmlelements');
$inpButton =  new button();

$objH->str = $this->_objLanguage->languageText('word_step').' 2: '.$this->_objLanguage->code2Txt("mod_contextadmin_aboutthecontext",'contextadmin');
$objH->type = 3;

//setup the form
$objForm->name = 'addfrm';
$objForm->action = $this->uri(array('action' => 'savestep2'));
$objForm->extra = 'class="f-wrap-1"';
$objForm->displayType = 3;

$inpAbout->name = 'about';
$inpAbout->id = 'about';
$inpAbout->value = '';
$inpAbout->cols = 4;
$inpAbout->width = '450px';
$inpAbout->cssClass = 'f-comments';

$inpButton->setToSubmit();
$inpButton->cssClass = 'f-submit';
$inpButton->value = $this->_objLanguage->languageText("word_next");


//validation
//$objForm->addRule('about','About is a required field!', 'required');


//$objForm->addToForm('<div class="req"><b>*</b> Indicates required field</div>');
$objForm->addToForm('<fieldset>');

$objForm->addToForm($objH->show());

$objForm->addToForm('<b><span class="req"></span>'.$this->_objLanguage->languageText("mod_contextadmin_about","contextadmin").':</b>');
$objForm->addToForm($inpAbout->show());


$objForm->addToForm('<div class="f-submit-wrap">'.$inpButton->show().'<br /></div></fieldset>');
print $objForm->show().'<br/>';

?>
