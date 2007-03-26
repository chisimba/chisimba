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

$inpTitle->fldType="hidden";

//setup the form
$objForm->name = 'addfrm';
$objForm->action = $this->uri(array('action' => 'savestep1'));
$objForm->extra = 'class="f-wrap-1"';
$objForm->displayType = 3;

$inpContextCode->name = 'contextcode';
$inpContextCode->cssId = 'input_contextcode';
$inpContextCode->value = '';
$inpContextCode->cssClass = 'f-name';

$inpTitle->name = 'title';
$inpTitle->cssId = 'input_title';
$inpTitle->value = '';
$inpTitle->cssClass = 'f-name';

$inpMenuText->value = '';
$inpMenuText->name = 'menutext';
$inpMenuText->cssId = 'input_menutext';
$inpMenuText->cssClass = 'f-name';

//status
$dropAccess->name = 'status';
			$dropAccess->addOption('Published',$this->_objLanguage->languageText("mod_context_published",'context'));
			$dropAccess->addOption('Unpublished',$this->_objLanguage->languageText("mod_context_unpublished",'context'));

$dropAccess->setSelected('Published');

$drop = '<fieldset class="f-radio-wrap">
		
						<b>'.$this->_objLanguage->languageText("mod_context_access",'context').':</b>

			
				<fieldset>
				
				<label for="Public">
				<input id="Public" type="radio" checked="checked" name="access" value="Public" class="f-radio" tabindex="8" />
				
							'.$this->_objLanguage->languageText("mod_context_public",'context').' <span class="caption">  -  '.$this->_objLanguage->code2Txt("mod_context_publichelp",'context',array('context'=>'Course')).'</span></label>
				
				<label for="Open">
				<input id="Open" type="radio" name="access" value="Open" class="f-radio" tabindex="9" />
				'.$this->_objLanguage->languageText("mod_context_open",'context').' <span class="caption">  -  '.$this->_objLanguage->code2Txt("mod_context_openhelp",'context',array('context'=>'Course')).'</span></label>

				
				<label for="Private">

				<input id="Private" type="radio" name="access" value="Private" class="f-radio" tabindex="10" />
				'.$this->_objLanguage->languageText("mod_context_private",'context').' <span class="caption">  -  '.$this->_objLanguage->code2Txt("mod_context_privatehelp",'context',array('context'=>'course')).'</span></label>
				
	
				</fieldset>
			
			</fieldset>';

$inpButton->setToSubmit();
$inpButton->cssClass = 'f-submit';
$inpButton->value = $this->_objLanguage->languageText("word_next");


//validation
$objForm->addRule('contextcode','[-context-] Code is a required field!', 'required');
$objForm->addRule('menutext','Menu Text is a required field', 'required!');
$objForm->addRule('title','Title is a required field', 'required!');

$objForm->addToForm('<div class="req"><b>*</b> Indicates required field</div>');
$objForm->addToForm('<fieldset>');
if($error)
{
    $objForm->addToForm('<br/><p class="error">'.$error.'</p>');
}
$objForm->addToForm($objH->show());

$objForm->addToForm('<label for="contextcode"><b><span class="req">*</span>'.$this->_objLanguage->code2Txt("mod_context_contextcode",'context',array('context'=>'Course')).':</b>');
$objForm->addToForm($inpContextCode->show().'<br /></label>');

$objForm->addToForm('<label for="title"><b><span class="req">*</span>'.$this->_objLanguage->languageText("word_title").':</b>');
$objForm->addToForm($inpTitle->show().'<br /></label>');

$objForm->addToForm('<label for="menutext"><b><span class="req">*</span>'.$this->_objLanguage->languageText("mod_context_menutext",'context').':</b>');
$objForm->addToForm($inpMenuText->show().'<br /></label>');

//$objForm->addToForm('&nbsp;<br/>');


$objForm->addToForm('<label for="access"><b><span class="req">*</span>'.$this->_objLanguage->languageText("mod_context_status",'context').':</b>');
$objForm->addToForm($dropAccess->show().'<br /></label>');

$objForm->addToForm($drop);


print $objForm->show().'<br/>';

?>
