<?php
/*impoting classes*/
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmltable','htmlelements');

/*declare objects*/
//$CH = new htmlheading($this->objLanguage->languageText('mod_formC_Heading','coursequestions', 2));
$CH = new htmlheading('Search for a player');
$FN = new textinput('firstname','','',15);
$LN = new textinput('lastname','','',15);
$btnSearch = new button('submitbutton','Search');
$btnSearch->setToSubmit();
$btnMenu = new button('cancel','Cancel');
$btnMenu->setOnClick("window.location='".$this->uri(NULL)."';");

/* Adding to the form */
$form = new form('form',$this->uri(array('action'=>'result')));
$form->addToForm("<table><th>".$CH->show()."</th></table>");
$form->addToForm("<table><tr><td>First Name:&nbsp;".$FN->show()."</td><td>Last Name:&nbsp;".$LN->show()."</td></tr>");
$form->addToForm("<tr><td>".$btnSearch->show()."</td><td>".$btnMenu->show()."</td></tr></table>");

echo $form->show();
?>
