<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('edit');

$heading = new htmlheading();

if ($file['title'] == '') {
    $heading->str = 'Delete Presentation - '.$file['filename'];
} else {
    $heading->str = $file['title'];
}


$heading->type = 1;

echo $heading->show();

$flashFile = $this->objConfig->getcontentBasePath().'webpresent/'.$file['id'].'/'.$file['id'].'.swf';

if (file_exists($flashFile)) {

    $flashFile = $this->objConfig->getcontentPath().'webpresent/'.$file['id'].'/'.$file['id'].'.swf';
    $flashContent = '
    <div style="border: 1px solid #000; width: 540px; height: 402px; text-align: center;"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="540" height="402">

  <param name="movie" value="'.$flashFile.'">
  <param name="quality" value="high">
  <embed src="'.$flashFile.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="540" height="402"></embed>
</object></div>';
} else {
    $flashContent = '<div class="noRecordsMessage" style="border: 1px solid #000; width: 540px; height: 302px; text-align: center;">Flash Version of Presentation being converted</div>';
}

$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();

$form = new form ('deleteconfirm', $this->uri(array('action'=>'deleteconfirm')));

$form->addToForm('<h3 class="warning">Are you sure you want to delete this presentation?</h3>');

$radio = new radio ('confirm');
$radio->setBreakSpace('<br /><br />');

$radio->addOption('no', '<strong>No</strong> - Do not delete the presentation');
$radio->addOption('yes', '<strong>Yes</strong> - Delete the presentation');

$radio->setSelected('no');

$form->addToForm($radio->show());

$button = new button ('submitform', 'Submit');
$button->setToSubmit();

$form->addToForm('<br />'.$button->show());

$hiddenInput = new hiddeninput('id', $file['id']);
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('deletevalue', $randNum);
$form->addToForm($hiddenInput->show());

$table->addCell($form->show(), 400);



$table->addCell($flashContent);

if ($mode == 'submodal')
{
    $form->extra = ' target="_top"';

    $cancelButton = new button ('cancelButton', 'Cancel');
    $cancelButton->setOnClick("parent.hidePopWin(false);");
    $form->addToForm(' '.$cancelButton->show());

    echo $form->show();

} else {

    echo $table->show();

    $homeLink = new link ($this->uri(NULL));
    $homeLink->link = 'Back to Home';

    echo '<p>'.$homeLink->show().'</p>';
}

?>