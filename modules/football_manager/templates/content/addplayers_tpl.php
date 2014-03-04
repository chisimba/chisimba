<?php

/*impoting classes*/
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('htmltable','htmlelements');
$this->loadClass('dropdown','htmlelements');

/*declare objects*/
//$CH = new htmlheading($this->objLanguage->languageText('mod_formC_Heading','coursequestions', 2));
$CH = new htmlheading('Football Management system');
$FN = new textinput('firstname','','',15);
$LN = new textinput('lastname','','',15);
$age = new dropdown('age');
for($i=15;$i<46;$i++) {
	$age->addOption($i,$i);
}
$pos=&new dropdown('position');
$pos->addOption('gk','GK');
$pos->addOption('rb','RB');
$pos->addOption('lb','LB');
$pos->addOption('cd','CD');
$pos->addOption('rw','RW');
$pos->addOption('lw','LW');
$pos->addOption('cm','CM');
$pos->addOption('acm','ACM');
$pos->addOption('af','AF');
$pos->addOption('st','ST');
$pos->addOption('cdm','CDM');
$pos->addOption('lm','LM');
$pos->addOption('rm','RM');

$OI  = new textarea('other','',15,50);
$TF = new textinput('transferfee','','',15);
$TS = new dropdown('status');
$TS->addOption("0","Not Listed");
$TS->addOption("1","Listed");

$btnSubmit = new button('submitbutton', 'Submit');
$btnCancel = new button('cancel','Cancel');
$btnSubmit->setToSubmit();
$btnCancel->setOnClick("window.location='".$this->uri(NULL)."';");

/* Adding to the form */
$form = new form('form',$this->uri(array('action'=>'submitform')));
$form->addRule("firstname","First Name required","required");
$form->addRule("lastname","Surname required","required");
$form->addRule("transferfee","Transfer Fee required","required");
$form->addRule("transferfee","Transfer Fee must be numeric","numeric");
$form->addToForm("<table><th>".$CH->show()."</th></table>");
$form->addToForm("<table><tr><td>First Name:&nbsp;".$FN->show()."</td><td>Last Name:&nbsp;".$LN->show()."</td></tr>");
$form->addToForm("<table><tr><td>Age:&nbsp;".$age->show()."</td></tr>");
$form->addToForm("<table><tr><td>Position:&nbsp;".$pos->show()."</td></tr>");
$form->addToForm("<tr><td>Transfer Fee:&nbsp;R".$TF->show()."</td><td>Transfer Status:&nbsp;".$TS->show()."</td></tr>");
$form->addToForm("<tr><td colspan=\"2\">Other Info:<br>".$OI->show()."</td></tr>");
$form->addToForm("<tr><td>".$btnSubmit->show()."</td><td>".$btnCancel->show()."</td></tr></table>");
$pos->show();
echo $form->show();
?>
