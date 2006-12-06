<?php

//add step 1 template
$objH = & $this->newObject('htmlheading','htmlelements');
$objForm = & $this->newObject('form','htmlelements');

$inpContextCode =  & $this->newObject('textinput','htmlelements');
$inpMenuText = & $this->newObject('textinput','htmlelements');
$inpTitle = & $this->newObject('textinput','htmlelements');
$inpButton =  $this->newObject('button','htmlelements');
$dropAccess = $this->newObject('dropdown','htmlelements');

$objH->str = 'Step 1: Add a Course';
$objH->type = 3;

//setup the form
$objForm->name = 'addfrm';
$objForm->action = $this->uri(array('action' => 'savestep1'));
$objForm->extra = 'class="f-wrap-1"';
$objForm->displayType = 3;

$inpContextCode->name = 'contextcode';
$inpContextCode->id = 'contextcode';
$inpContextCode->value = '';
$inpContextCode->cssClass = 'f-name';

$inpTitle->name = 'title';
$inpTitle->id = 'title';
$inpTitle->value = '';
$inpTitle->cssClass = 'f-name';

$inpMenuText->value = '';
$inpMenuText->name = 'menutext';
$inpMenuText->id = 'menutext';
$inpMenuText->cssClass = 'f-name';

//status
$dropAccess->name = 'status';
$dropAccess->addOption('Published', 'Published');
$dropAccess->addOption('Unpublished', 'Unpublished');

$dropAccess->setSelected('Published');

$drop = '<fieldset class="f-radio-wrap">
		
			<b>Access:</b>

			
				<fieldset>
				
				<label for="Public">
				<input id="Public" type="radio" name="access" value="Public" class="f-radio" tabindex="8" />
				Public</label>
				
				<label for="Open">
				<input id="Open" type="radio" name="access" value="Open" class="f-radio" tabindex="9" />
				Open</label>
				
				<label for="Private">

				<input id="Private" type="radio" name="access" value="Private" class="f-radio" tabindex="10" />
				Private</label>
	
				</fieldset>
			
			</fieldset>';

$inpButton->setToSubmit();
$inpButton->cssClass = 'f-submit';
$inpButton->value = 'Next';


//validation
$objForm->addRule('contextcode','[-context-] Code is a required field!', 'required');
$objForm->addRule('menutext','Menu Text is a required field', 'required!');
$objForm->addRule('title','Title is a required field', 'required!');

$objForm->addToForm('<div class="req"><b>*</b> Indicates required field</div>');
$objForm->addToForm('<fieldset>');
if($error)
{
    $objForm->addToForm('<p class="error">'.$error.'</p>');
}
$objForm->addToForm($objH->show());

$objForm->addToForm('<label for="contextcode"><b><span class="req">*</span>[-context-] Code:</b>');
$objForm->addToForm($inpContextCode->show().'<br /></label>');

$objForm->addToForm('<label for="title"><b><span class="req">*</span>Title:</b>');
$objForm->addToForm($inpTitle->show().'<br /></label>');

$objForm->addToForm('<label for="menutext"><b><span class="req">*</span>Menu Text:</b>');
$objForm->addToForm($inpMenuText->show().'<br /></label>');

//$objForm->addToForm('&nbsp;<br/>');


$objForm->addToForm('<label for="access"><b><span class="req">*</span>Status:</b>');
$objForm->addToForm($dropAccess->show().'<br /></label>');

$objForm->addToForm($drop);

$objForm->addToForm('<br/><div class="f-submit-wrap">'.$inpButton->show().'</div></fieldset>');
print $objForm->show().'<br/>';

?>