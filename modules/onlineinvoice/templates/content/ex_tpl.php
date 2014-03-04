<?php

/*$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');*/

//display the label for user logged in user logged in
$name = $this->objLanguage->languageText('mod_onlineinvoice_webbasedinvoicingsystem');
$word = $this->objLanguage->languageText('word_name');

//display contents on screen
echo $name;
echo '<br>'.$word  . ' '  . $fullname;


?>
