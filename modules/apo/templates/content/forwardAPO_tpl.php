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
$objUsers = $this->newObject('users');

$this->setVar('pageSuppressXML', TRUE);


$action = 'showreview';
$form = new form('forwardtoAPOform', $this->uri(array('action' => $action, 'id' => $id, 'formname' => 'forwardtoAPO')));

$xtitle = $this->objLanguage->languageText('mod_wicid_newdocument', 'wicid', 'Forward to APO');

$header = new htmlheading();
$header->type = 2;
$header->str = $xtitle;

echo $header->show();

//$legend = "<b>Forward Document to APO</b>";


$objApoUsers = $this->getObject('dbapousers');
$user = $objApoUsers->getDepartmentUser($document['department']);

$legend = "Faculty";
$fs = new fieldset();
$fs->setLegend($legend);

$fs->addContent($user[0]['department']);

echo $fs->show() . '<br/>';

$table = $this->newObject('htmltable', 'htmlelements');


    /* $table->startRow();
    $textinput = new textinput('name of faculty');
    $textinput->size = 60;
    $textinput->value = $faculty['name'];

    $table->addCell('<b>Name</b>');
    $table->addCell($textinput->show());
    $table->endRow();
    
      $table->endRow();
      $table->addCell($user['name']);
      $table->addCell($user['role']);
      $table->addCell($user['department']);
      $table->addCell($user['email']);
      $table->addCell($user['telephone']);

      $editOption = new link($this->uri(array('action' => 'edituser', "selected" => $selected, 'id' => $user['id'])));
      $editOption->link = $editIcon;
      $edit = $editOption->show();

      $deleteLink = new link($this->uri(array('action' => 'deleteuser', "selected" => $selected, 'id' => $user['id'])));
      $deleteLink->link = $deleteIcon;
      $delete = $deleteLink->show();

      $table->addCell($edit . ' &nbsp; ' . $delete, 100);
      $table->endRow(); */



 $table->startRow();
  $table->width = "50%";
  $table->addCell("Name:  ");
  $table->addCell($user[0]['name']);
  $table->endRow();

  $table->startRow();
  $table->width = "50%";
  $table->addCell("email adress:  ");
  $table->addCell($user[0]['email']);
  $table->endRow();

  $table->startRow();
  $table->width = "50%";
  $table->addCell("Telephone Number:  ");
  $table->addCell($user[0]['telephone']);
  $table->endRow(); 

$legend = "Foward to APO";

$fs = new fieldset();
$fs->setLegend($legend);
$fs->addContent($table->show());
$form->addToForm($fs->show());

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm('<br/>' . $button->show() . '&nbsp');
$form->extra = 'class="sections"';

$forwardText = $this->objLanguage->languageText('mod_apo_wicid', 'wicid', 'Send to APO');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action' => 'forwardDocAPO', 'from' => 'home_tpl.php', 'id' => $id, 'mode' => $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

echo $form->show();
?>