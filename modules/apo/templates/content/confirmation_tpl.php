<?php


$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('iframe', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');

$this->setVar('pageSuppressXML', TRUE);


$action = 'showreview';
$form = new form('forwardtoAPOform', $this->uri(array('action' => $action, 'id' => $id, 'formname'=>'forwardtoAPO')));

$xtitle = $this->objLanguage->languageText('mod_wicid_newdocument', 'wicid', 'Forwarded Documents for Comments');

$header = new htmlheading();
$header->type = 2;
$header->str = $xtitle;

echo $header->show();

//$legend = "<b>Forward Document to APO</b>";

/*$legend = "Faculty";

$fs = new fieldset();
$fs->setLegend($legend);
$fs->addContent("Engineering");

echo $fs->show() . '<br/>';*/

$table = $this->newObject('htmltable', 'htmlelements');

/*$table->startRow();
$table->boarder='1';
$table->addCell('Current editor:&nbsp;' . $this->objUser->fullname($document['currentuserid']));
$table->endRow();*/

$table->startRow();
$table->width="50%";
$table->addCell("<b> Your Document has been send for Comments </b>");
$table->endRow();

//$legend = "Foward to APO";

$fs = new fieldset();
//$fs->setLegend($legend);
$fs->addContent($table->show());
$form->addToForm($fs->show());

$button = new button('ok', $this->objLanguage->languageText('word_ok'));
$uri = $this->uri(array('action' => 'home'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm('<br/>' .$button->show().'&nbsp');
$form->extra = 'class="sections"';

echo $form->show();
?>