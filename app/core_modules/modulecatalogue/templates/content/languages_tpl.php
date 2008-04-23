<?php
$this->loadClass('form','htmlelements');
//$this->loadClass('textinput','htmlelements');
//$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
//
$form = new form('language',$this->uri(array('action'=>'downloadpo')));
//$textinput = new textinput('langname','');
$dropdown = $this->newObject('dropdown','htmlelements');
$dropdown->name = 'langname';
$langs = $this->objLanguage->languagelist();
$objLanguageCode = $this->getObject('languagecode','language');
//echo '<pre>';
//var_dump($langs);
//var_dump($objLanguageCode->iso_639_2_tags->codes);
//echo '</pre>';
foreach ($langs as /*$key=>*/$lang) {
    $langName = $lang['languagename'];
    //$_langName = strtolower($langName);
    $langCode = 'unknown';
    foreach ($objLanguageCode->iso_639_2_tags->codes as $ISO=>$language) {
        if ($langName == $language) {
            $langCode = $ISO;
            break;
        }
    }
    $dropdown->addOption($langCode,$langName);
}
$submit = new button('submit',$this->objLanguage->languageText('word_submit'));
$submit->setToSubmit();
$form->addToForm($this->objLanguage->languageText('mod_modulecatalogue_language','modulecatalogue').' : '.$dropdown->show());
$form->addToForm('&nbsp;'.$submit->show());
echo $form->show();