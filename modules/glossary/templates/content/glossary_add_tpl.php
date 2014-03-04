<?php
/* A PHP template for the Home Page of the Glossary Module */

// Classes being used
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('label', 'htmlelements');
//$this->loadClass('fieldset', 'htmlelements');


echo $header;

// Create Header Tag ' Add a word
$this->titleAddTerm =& $this->getObject('htmlheading', 'htmlelements');
$this->titleAddTerm->type=3;
$this->titleAddTerm->str=$this->objLanguage->languageText('mod_glossary_addTermTitle', 'glossary');
echo $this->titleAddTerm->show();

// Start of Form
$addForm = new form('addWord', $this->uri(array('module'=>'glossary', 'action'=>'addconfirm')));

$addTable = $this->getObject('htmltable', 'htmlelements');
$addTable->width='500';
$addTable->cellpadding = 10;

$addTable->startRow();
$termLabel = new label($this->objLanguage->languageText('mod_glossary_term', 'glossary'), 'input_term');
$addTable->addCell($termLabel->show(), 100);

$termInput = new textinput('term');
$termInput->size = 50;
$addTable->addCell($termInput->show(), 400);

$addTable->endRow();
$addTable->startRow();

$definitionLabel = new label($this->objLanguage->languageText('mod_glossary_definition', 'glossary'), 'input_definition');
$addTable->addCell($definitionLabel->show().':', null);
$definition = new textarea('definition');
$addTable->addCell($definition->show(), null);

$addTable->endRow();

$addTable->startRow();
$urlLabel = new label($this->objLanguage->languageText('mod_glossary_url', 'glossary').': <em>('.$this->objLanguage->languageText('mod_glossary_optional', 'glossary').')</em>', 'input_url');
$addTable->addCell($urlLabel->show(), '300');

$urlInput = new textinput('url');
$urlInput->size = 50;
$urlInput->value = 'http://';
$addTable->addCell($urlInput->show(), null);

$addTable->endRow();
$addForm->addRule('url', $this->objLanguage->languageText('mod_glossary_urlnotvalid'), 'alphanumeric');


if ($numRecords > 0)
{
	$addTable->startRow();
	
	$seeAlsoLabel = new label('<nobr>'.$this->objLanguage->languageText('mod_glossary_seeAlso', 'glossary').': <em>('.$this->objLanguage->languageText('mod_glossary_optional', 'glossary').')</em></nobr>', 'input_seealso');
$addTable->addCell($seeAlsoLabel->show(), '300');
	
	$seeAlso = new dropdown('seealso');
	$seeAlso->addOption(null, $this->objLanguage->languageText('mod_glossary_selectOne', 'glossary').'...');
	
	foreach ($others as $element) {
	
		$seeAlso->addOption($element['item_id'], $element['term']);
	
	}
	
	$addTable->addCell($seeAlso->show(), null);
	
	$addTable->endRow();
}

$addTable->startRow();

$submitButton = new button('submit', $this->objLanguage->languageText('mod_glossary_addWordToGlossary', 'glossary'));
$submitButton->setToSubmit();

$addTable->addCell(' ', null);
$addTable->addCell($submitButton->show(), null);
$addTable->endRow();

$addForm->addRule('term',$this->objLanguage->languageText('mod_glossary_termRequired', 'glossary'),'required');
$addForm->addRule('definition',$this->objLanguage->languageText('mod_glossary_defnRequired', 'glossary'),'required');
$addForm->addRule('url',$this->objLanguage->languageText('mod_glossary_badurl', 'glossary'),'url');

$addForm->addToForm($addTable);



//$addForm->addToForm($image);

echo $addForm->show();


?>
