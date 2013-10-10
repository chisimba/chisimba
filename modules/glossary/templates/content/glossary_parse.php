<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('button', 'htmlelements');


echo $header;

// Start of Form
$parseForm = new form('addWord', $this->uri(array('module'=>'glossary', 'action'=>'parse')));
$parseForm->displayType = 2;


$parseForm->addToForm($objLanguage->languageText('mod_glossary_textToParse', 'glossary'));
$parseForm->addToForm(new textarea('textToParse', $originalText));


$submitButton = new button('submit', $objLanguage->languageText('mod_glossary_submit', 'glossary'));
$submitButton->setToSubmit();
$parseForm->addToForm($submitButton);
echo $parseForm->show();

?>


<br />


<?php

echo ('<div style="border-style: dotted ">');
echo ('<br />');
echo ($outputText);
echo ('<br />&nbsp;');

echo ('</div>');

echo $footer;
?>