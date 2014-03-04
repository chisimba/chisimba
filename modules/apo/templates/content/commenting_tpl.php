<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('iframe', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$objUsers = $this->newObject('users');

$this->setVar('pageSuppressXML', TRUE);


$action = 'showreview';
$form = new form('Commentingform', $this->uri(array('action' => $action, 'id' => $id, 'formname' => 'Commenting')));

$xtitle = $this->objLanguage->languageText('mod_wicid_newdocument', 'wicid', 'Forward Document for Comments');

$header = new htmlheading();
$header->type = 2;
$header->str = $xtitle;

echo $header->show();

//$legend = "<b>Forward Document to APO</b>";


$objApoUsers = $this->getObject('dbapousers');
$user = $objApoUsers->getCommentsUsers($document['department']);

 $legend = "Faculty";
  $fs = new fieldset();
  $fs->setLegend($legend);

  $fs->addContent($user[0]['department']);

  echo $fs->show() . '<br/>';

$table = $this->newObject('htmltable', 'htmlelements');

//print_r($user); die();

$userlist = "";
foreach ($user as $users) {
    $checkbox = new checkbox('selectedusers[]', $users['userid']);
    $checkbox->value = $users['userid'];
    $checkbox->cssId = 'user_' . $users['id'];
    $checkbox->cssClass = 'user_option';

    $label = new label(' ' . $users['role'] .'<br>Name : ' . $users['name'] . '<br>Email :' . $users['email'] . '<br>Telephone :' . $users['telephone']);

    $userlist .= ' ' . $checkbox->show() . $label->show() . '<br />';

}

$table->startRow();
$table->addCell($userlist);
$table->endRow();

$legend = "Send Document for Comment TO:";

$fs = new fieldset();
$fs->setLegend($legend);
$fs->addContent($table->show());
$form->addToForm($fs->show());

$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$uri = $this->uri(array('action' => 'home'));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm('<br/>' . $button->show() . '&nbsp');
$form->extra = 'class="sections"';

$forwardText = $this->objLanguage->languageText('mod_apo_wicid', 'wicid', 'Send Document');

$button = new button('forward', $forwardText);
$uri = $this->uri(array('action' => 'forwardDocAPO', 'from' => 'home_tpl.php', 'id' => $id, 'mode' => $mode));
$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($button->show());

echo $form->show();
?>
