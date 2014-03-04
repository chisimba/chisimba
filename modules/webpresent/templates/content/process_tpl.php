<?php
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

$heading = new htmlheading();
$heading->str = 'Update Details - '.$file['filename'];
$heading->type = 1;

echo $heading->show();

if ($mode == 'addfixup') {

    foreach ($problems as $problem)
    {
        $messages[] = $this->explainProblemsInfo($problem);
    }
}


if ($mode == 'addfixup' && count($messages) > 0) {
    echo '<ul><li><span class="error">'.$this->objLanguage->languageText('mod_userdetails_infonotsavedduetoerrors', 'userdetails').'</span>';

    echo '<ul>';
        foreach ($messages as $message)
        {
            if ($message != '') {
                echo '<li class="error">'.$message.'</li>';
            }
        }

    echo '</ul></li></ul>';
}

$form = new form ('updatedetails', $this->uri(array('action'=>'updatedetails')));
$form->extra = ' target="_top" ';

$table = $this->newObject('htmltable', 'htmlelements');

$title = new textinput ('title');
$title->size = 60;
$title->value = $file['title'];
$label = new label ('Title of Presentation:', 'input_title');
$table->startRow();
$table->addCell($label->show());
$table->addCell($title->show());
$table->endRow();

$description = new textarea ('description');
$description->size = 60;
$description->value = $file['description'];
$label = new label ('Description:', 'input_description');
$table->startRow();
$table->addCell($label->show());
$table->addCell($description->show());
$table->endRow();

$tagsInput = new textarea ('tags');
$tagsInput->size = 60;;
if (count($tags) > 0) {
    $divider = '';
    foreach ($tags as $tag)
    {
        $tagsInput->value .= $divider.$tag['tag'];
        $divider = ', ';
    }
}

$label = new label ('Tags: (Separated by commas)', 'input_tags');
$table->startRow();
$table->addCell($label->show());
$table->addCell($tagsInput->show());
$table->endRow();

$table->startRow();
$table->addCell('License');
$objLicenseChooser = $this->newObject('licensechooser', 'creativecommons');
$objLicenseChooser->icontype = 'small';

$defaultLicense = ($file['cclicense'] == '') ? 'copyright' : $file['cclicense'];

$objLicenseChooser->defaultValue = $defaultLicense;
$table->addCell($objLicenseChooser->show());
$table->endRow();

$form->addToForm($table->show());

$button = new button ('updatedetails', 'Update Presentation');
$button->setToSubmit();

$cancelButton = new button ('cancelButton', 'Cancel');

if (isset($mode) && $mode == 'submodal')
{
    $cancelButton->setOnClick("parent.hidePopWin(false);");
    $form->addToForm('<p align="center">'.$button->show().' '.$cancelButton->show().'</p>');
} else {
    $cancelButton->setOnClick("document.location='".$this->uri(array('action'=>'view', 'id'=>$file['id']))."'");
    $form->addToForm('<p>'.$button->show().' '.$cancelButton->show().'</p>');
}



$hiddeninput = new hiddeninput('id', $file['id']);
$form->addToForm($hiddeninput->show());

echo $form->show();



?>
