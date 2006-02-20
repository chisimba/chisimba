<?
$objLanguage=&$this->getObject('language','language');

$objTblclass=$this->newObject('htmltable','htmlelements');
$objTblclass->width='';
$objTblclass->attributes=" align='center' border=2";
$objTblclass->cellspacing='2';
$objTblclass->cellpadding='2';

$objTblclass->startRow();
$objTblclass->addCell("Congratulations New User!", "", NULL, 'center', 'heading', 'colspan=2');
$objTblclass->endRow();

$row=array('Username',$newdata['username']);
$objTblclass->addRow($row,'even');

$row=array($objLanguage->languageText('phrase_firstname'),$newdata['firstname']);
$objTblclass->addRow($row,'even');

$row=array($objLanguage->languageText('word_surname'),$newdata['surname']);
$objTblclass->addRow($row,'even');

$row=array($objLanguage->languageText('word_password'),$newpassword);
$objTblclass->addRow($row,'even');


$objTblclass->startRow();
$objTblclass->addCell($objLanguage->languageText('mod_useradmin_welcome',"Welcome to KEWL NextGen!"), "", NULL, 'center', NULL, 'colspan=2');
$objTblclass->endRow();

print $objTblclass->show();

print "<a href='".$this->uri('','_default')."'>".$objLanguage->languageText('phrase_goto_login','Go to Login Page')."</a>\n";
