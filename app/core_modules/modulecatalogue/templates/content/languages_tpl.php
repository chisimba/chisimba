<?php
$this->loadClass('form','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('button','htmlelements');
//
$form = new form('language',$this->uri(array('action'=>'downloadpo')));
$textinput = new textinput('langname','');
$submit = new button('submit',$this->objLanguage->languageText('word_submit'));
$submit->setToSubmit();
$form->addToForm($this->objLanguage->languageText('mod_modulecatalogue_language','modulecatalogue').' : '.$textinput->show());
$form->addToForm('&nbsp;'.$submit->show());
echo $form->show();