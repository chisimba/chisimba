<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');


$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('loader');

$formAction = 'savestep2';
$headerTitle = $context['title'].' - '.$this->objLanguage->code2Txt('mod_contextadmin_contextinformation', 'contextadmin', NULL, '[-context-] Information');
$formButton = $this->objLanguage->languageText('mod_contextadmin_gotonextstep', 'contextadmin', 'Go to Next Step');


$objStepMenu = $this->newObject('stepmenu', 'navigation');
if ($mode == 'edit') {
    $objStepMenu->addStep(str_replace('[-num-]', 1, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_context_contextsettings', 'context', NULL, '[-context-] Settings')), ucwords($this->objLanguage->code2Txt('mod_contextadmin_updatecontextitlesettings', 'contextadmin', NULL, 'Update [-context-] Title and Settings')));
} else {
    $objStepMenu->addStep(str_replace('[-num-]', 1, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_context_contextsettings', 'context', NULL, '[-context-] Settings')), $this->objLanguage->code2Txt('mod_contextadmin_checkcontextcodeavailable', 'contextadmin', NULL, 'Enter [-context-] settings and check whether [-context-] code is available'));
}
$objStepMenu->addStep(str_replace('[-num-]', 2, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_contextadmin_contextinformation', 'contextadmin', NULL, '[-context-] Information')), $this->objLanguage->code2Txt('mod_contextadmin_enterinfoaboutcontext', 'contextadmin', NULL, 'Enter more information about your [-context-] and select a [-context-] image'));
$objStepMenu->addStep(str_replace('[-num-]', 3, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_context_contextpluginsabs', 'context', array('plugins'=>'plugins'), '[-context-] [-plugins-]')), $this->objLanguage->code2Txt('mod_contextadmin_selectpluginsforcontextabs', 'contextadmin', array('plugins'=>'plugins'), 'Select the [-plugins-] you would like to use in this [-context-]'));
$objStepMenu->setCurrent(2);
echo $objStepMenu->show();


$header = new htmlheading();
$header->type = 1;
$header->str = ucwords($headerTitle);

echo '<br />'.$header->show();




$objSelectImage = $this->getObject('selectimage', 'filemanager');
$htmlEditor = $this->newObject('htmlarea', 'htmlelements');
$htmlEditor->name = 'about';
$htmlEditor->value = $context['about'];


$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();

$objContextImage = $this->getObject('contextimage', 'context');
$hasContextImage = $objContextImage->getContextImage($contextCode);

$leftCol = '';

if ($hasContextImage != FALSE) {
    
    echo '
<script type="text/javascript">
function removeContextImage()
{
    jQuery.ajax({
        type: "GET", 
        url: "index.php", 
        data: "module=contextadmin&action=removeimage&contextcode='.$contextCode.'", 
        success: function(msg){
            if (msg == "ok") {
                jQuery("#contextexistingimage").remove();
            } else {
                alert("'.$this->objLanguage->languageText('mod_contextadmin_couldnotremoveimage', 'contextadmin', 'Could not remove image').'");
            }
        }
    });
}
</script>
    ';
    
    $leftCol .= '<div id="contextexistingimage"><p>'.$this->objLanguage->code2Txt('mod_contextadmin_existingcontextimage', 'contextadmin', NULL, 'Existing [-context-] Image').':</p>';
    
    $leftCol .= '<img src="'.$hasContextImage.'"><br />';
    
    $removeButton = new button('removeimage', $this->objLanguage->languageText('mod_contextadmin_removeimage', 'contextadmin', 'Remove Image'));
    $removeButton->setOnClick('removeContextImage();');
    
    $leftCol .= $removeButton->show();
    $leftCol .= '<br />&nbsp;';
    $leftCol .= '</div>';
    
}



$leftCol .= '<p>'.$this->objLanguage->code2Txt('mod_contextadmin_selectcontextimage', 'contextadmin', NULL, 'Select a [-context-] image').':</p>';

$leftCol .= $objSelectImage->show();

$table->addCell($leftCol, 200);


$table->addCell('<p>'.$this->objLanguage->code2Txt('mod_contextadmin_enterdescriptioncontext', 'contextadmin', NULL, 'Enter a description of the [-context-]').':</p>'.$htmlEditor->show());
$table->endRow();




$button = new button ('savecontext', $formButton);
$button->setToSubmit();



$form = new form ('createcontext', $this->uri(array('action'=>$formAction)));

$backUri = $this->uri(array('action'=>'edit','contextcode'=>$contextCode),'contextadmin');
$backButton = new button('back', $this->objLanguage->languageText('word_back'),"document.location='$backUri'");

$form->addToForm($table->show());
$form->addToForm($backButton->show()." ".$button->show());

$hiddenInput = new hiddeninput('mode', $mode);
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('contextCode', $contextCode);
$form->addToForm($hiddenInput->show());

echo $form->show();




?>