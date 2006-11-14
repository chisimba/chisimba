<?php

//step 2 add<?php

//add step 1 template
$objH = & $this->newObject('htmlheading','htmlelements');
$objForm = & $this->newObject('form','htmlelements');

$inpContextCode =  & $this->newObject('textinput','htmlelements');
$inpMenuText = & $this->newObject('textinput','htmlelements');
$inpAbout = & $this->newObject('htmlarea','htmlelements');
$inpButton =  $this->newObject('button','htmlelements');

$objH->str = 'Step 2: About the Course';
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
$inpAbout->rows = 3;
$inpAbout->cssClass = 'f-comments';

$inpButton->setToSubmit();
$inpButton->cssClass = 'f-submit';
$inpButton->value = 'Next';


//validation
//$objForm->addRule('about','About is a required field!', 'required');


$objForm->addToForm('<div class="req"><b>*</b> Indicates required field</div>');
$objForm->addToForm('<fieldset>');

$objForm->addToForm($objH->show());

$objForm->addToForm('</fieldset><b><span class="req">*</span>About:</b>');
$objForm->addToForm($inpAbout->show());


$objForm->addToForm('<div class="f-submit-wrap">'.$inpButton->show().'<br /></div>');
print $objForm->show().'<br/>';

?>
