<?php

echo '<h1>'.$this->objLanguage->languageText('mod_languagetextgenerator_name', 'languagetextgenerator', 'Language Text Generator').'</h1>';

$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$form = new form ('searchtext', $this->uri(NULL));

$table = $this->newObject('htmltable', 'htmlelements');

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_languagetextgenerator_english_text_required', 'languagetextgenerator', 'English Text (Required)'), 150);
$search = new textinput ('search', $this->getParam('search'));
$search->size = 120;
$table->addCell($search->show());
$table->endRow();

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_languagetextgenerator_yourmodule', 'languagetextgenerator', 'Your Module'));
$urmodule = new textinput ('urmodule', $this->getParam('urmodule'));
$urmodule->size = 120;
$table->addCell($urmodule->show());
$table->endRow();

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_toolbar_langcode', 'toolbar', 'Language Code'));
$langcode = new textinput ('langcode', $this->getParam('langcode'));
$langcode->size = 120;
$table->addCell($langcode->show());
$table->endRow();

$table->startRow();
$table->addCell($this->objLanguage->languageText('word_description', 'system', 'Description'));
$langcode = new textinput ('description', $this->getParam('description'));
$langcode->size = 120;
$table->addCell($langcode->show());
$table->endRow();

$form->addToForm($table->show());

$button = new button ('submitform', $this->objLanguage->languageText('mod_languagetextgenerator_submitenglishtext', 'languagetextgenerator', 'Submit English Text'));
$button->setToSubmit();

$form->addToForm('<p align="center"><br />'.$button->show().'</p>');

echo $form->show();

if (isset($results)) {
    
    echo '<h3>'.$this->objLanguage->languageText('phrase_self_add', 'languagetextgenerator', 'Self Add').'</h3>';
    
    $description = $this->getParam('description', $this->getParam('search'));
    
    echo '<p>'.htmlentities('TEXT: mod_'.$this->getParam('urmodule').'_'.$this->getParam('langcode').'|'.$description.'|'.$this->getParam('search')).'</p>';
    
    if (preg_match('/\[-.*?-\]/', $this->getParam('search'))) {
        echo '<p>'.htmlentities('$this->objLanguage->code2Txt(\'mod_'.$this->getParam('urmodule').'_'.$this->getParam('langcode').'\', \''.$this->getParam('urmodule').'\', NULL, \''.$this->getParam('search').'\');').'</p>';
    } else {
        echo '<p>'.htmlentities('$this->objLanguage->languageText(\'mod_'.$this->getParam('urmodule').'_'.$this->getParam('langcode').'\', \''.$this->getParam('urmodule').'\', \''.$this->getParam('search').'\');').'</p>';
    }
    echo '<h3>'.$this->objLanguage->languageText('mod_useradmin_searchresultsfor', 'useradmin', 'Search Results for').': <em>'.$this->getParam('search').'</em></h3><br />';
    
    if (count($results) == 0) {
        echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_languagetextgenerator_nomatchingresultsfound', 'languagetextgenerator', 'No matching results found').'.</div>';
    } else {
        
        $divider = '';
        
        foreach ($results as $result)
        {
            //print_r($result);
            
            echo $divider;
            
            echo '<p><strong>'.$this->objLanguage->languageText('mod_systext_text', 'systext', 'Text').':</strong> '.$result['en'].'<br />';
            echo '<strong>'.$this->objLanguage->languageText('word_module', 'system', 'Module').':</strong> '.$result['pageid'].'<br />';
            echo '<strong>'.$this->objLanguage->languageText('mod_toolbar_langcode', 'toolbar', 'Language Code').':</strong> '.$result['id'].'<br />';
            echo '<strong>'.$this->objLanguage->languageText('word_description', 'system', 'Description').':</strong> '.$result['description'].'</p>';
            
            echo '<p><strong>'.$this->objLanguage->languageText('mod_languagetextgenerator_languagecoderesuse', 'languagetextgenerator', 'Language Code Reuse').':</strong></p>';
            echo '<p>USES: '.$result['id'].'|'.$result['description'].'|'.$result['en'].'</p>';
            
            echo '<p><strong>'.$this->objLanguage->languageText('mod_languagetextgenerator_languageobjectresuse', 'languagetextgenerator', 'Language Object Reuse').':</strong></p>';
            
            if (preg_match('/\[-.*?-\]/', $this->getParam('search'))) {
                echo '<p>$this->objLanguage->code2Txt(\''.$result['id'].'\', \''.$result['pageid'].'\', NULL, \''.$result['en'].'\');</p>';
            } else {
                echo '<p>$this->objLanguage->languageText(\''.$result['id'].'\', \''.$result['pageid'].'\', \''.$result['en'].'\');</p>';
            }
            //echo '<p>$this->objLanguage->languageText(\''.$result['id'].'\', \''.$result['pageid'].'\', \''.$result['en'].'\');</p>';
            
            
            $divider = '<hr /><br />';
        }
    }
}

?>