<?php
$objSysConfig = $this->getObject ('dbsysconfig','sysconfig');

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('loader');

$contextexists = $this->objLanguage->code2Txt('mod_contextadmin_contextcodeexists', 'contextadmin');
if ($mode == 'edit') {
    $fixup = NULL;

    $formAction = 'updatecontext';
    $headerTitle = ucwords($this->objLanguage->code2Txt('mod_context_updatecontext', 'context', NULL, 'Update [-context-]')) . ': ' . $context['title'];
} else {
    $formAction = 'savestep1';
    $headerTitle = $this->objLanguage->code2Txt('mod_contextadmin_createnewcontext', 'contextadmin', NULL, 'Create New [-context-]');
    $fixup = $this->getSession('fixup', NULL);

    $this->appendArrayVar('headerParams', '
    <script type="text/javascript">

        // Flag Variable - Update message or not
        var doUpdateMessage = false;

        // Var Current Entered Code
        var currentCode;

        // Action to be taken once page has loaded
        jQuery(document).ready(function(){
            jQuery("#input_contextcode").bind(\'keyup\', function() {
                checkCode(jQuery("#input_contextcode").attr(\'value\'));
            });
        });

        // Function to check whether context code is taken
        function checkCode(code)
        {
            // Messages can be updated
            doUpdateMessage = true;

            // If code is null
            if (code == null) {
                // Remove existing stuff
                jQuery("#contextcodemessage").html("");
                jQuery("#contextcodemessage").removeClass("error");
                jQuery("#input_contextcode").removeClass("inputerror");
                jQuery("#contextcodemessage").removeClass("success");
                doUpdateMessage = false;

            // If code is root - Reserved. Saves Ajax Call
            } else if (code.toLowerCase() == "root") {

                currentCode = code;

                jQuery("#contextcodemessage").html("This code has been reserved and cannot be used as a context code.");
                jQuery("#contextcodemessage").addClass("error");
                jQuery("#input_contextcode").addClass("inputerror");
                jQuery("#contextcodemessage").removeClass("success");
                doUpdateMessage = false;

            // Else Need to do Ajax Call
            } else {



                // Check that existing code is not in use
                if (currentCode != code) {

                    // Set message to checking
                    jQuery("#contextcodemessage").removeClass("success");
                    jQuery("#contextcodemessage").html("<span id=\"contextcodecheck\">' . addslashes($objIcon->show()) . ' Checking ...</span>");


                    // Set current Code
                    currentCode = code;

                    // DO Ajax
                    jQuery.ajax({
                        type: "GET",
                        url: "index.php",
                        data: "module=contextadmin&action=checkcode&code="+code,
                        success: function(msg){

                            // Check if messages can be updated and code remains the same
                            if (doUpdateMessage == true && currentCode == code) {

                                // IF code exists
                                if (msg == "exists") {
                                    jQuery("#contextcodemessage").html("' . $contextexists . '");
                                    jQuery("#contextcodemessage").addClass("error");
                                    jQuery("#input_contextcode").addClass("inputerror");
                                    jQuery("#contextcodemessage").removeClass("success");
                                    jQuery("#savebutton").attr("disabled", "disabled");

                                // Else
                                } else {
                                    jQuery("#contextcodemessage").html("Available");
                                    jQuery("#contextcodemessage").addClass("success");
                                    jQuery("#contextcodemessage").removeClass("error");
                                    jQuery("#input_contextcode").removeClass("inputerror");
                                    jQuery("#savebutton").removeAttr("disabled");
                                }

                            }
                        }
                    });
                }
            }
        }
    </script>');
}


$objStepMenu = $this->newObject('stepmenu', 'navigation');
if ($mode == 'edit') {
    $objStepMenu->addStep(str_replace('[-num-]', 1, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')) . ' - ' . ucwords($this->objLanguage->code2Txt('mod_context_contextsettings', 'context', NULL, '[-context-] Settings')), ucwords($this->objLanguage->code2Txt('mod_contextadmin_updatecontextitlesettings', 'contextadmin', NULL, 'Update [-context-] Title and Settings')));
} else {
    $objStepMenu->addStep(str_replace('[-num-]', 1, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')) . ' - ' . ucwords($this->objLanguage->code2Txt('mod_context_contextsettings', 'context', NULL, '[-context-] Settings')), $this->objLanguage->code2Txt('mod_contextadmin_checkcontextcodeavailable', 'contextadmin', NULL, 'Enter [-context-] settings and check whether [-context-] code is available'));
}
$objStepMenu->addStep(str_replace('[-num-]', 2, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')) . ' - ' . ucwords($this->objLanguage->code2Txt('mod_contextadmin_contextinformation', 'contextadmin', NULL, '[-context-] Information')), $this->objLanguage->code2Txt('mod_contextadmin_enterinfoaboutcontext', 'contextadmin', NULL, 'Enter more information about your [-context-] and select a [-context-] image'));

$objStepMenu->addStep(str_replace('[-num-]', 3, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')) . ' - ' . ucwords($this->objLanguage->code2Txt('mod_contextadmin_courseoutcome', 'contextadmin', NULL, '[-context-] Outcomes')), $this->objLanguage->code2Txt('mod_context_enteroutcomecontext', 'contextadmin', NULL, 'Enter the main Outcomes / Goals of the [-context-]'));


$objStepMenu->addStep(str_replace('[-num-]', 4, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')) . ' - ' . ucwords($this->objLanguage->code2Txt('mod_context_contextpluginsabs', 'context', array('plugins' => 'plugins'), '[-context-] [-plugins-]')), $this->objLanguage->code2Txt('mod_contextadmin_selectpluginsforcontextabs', 'contextadmin', array('plugins' => 'plugins'), 'Select the [-plugins-] you would like to use in this [-context-]'));
$objStepMenu->current = 1;
echo $objStepMenu->show();


$header = new htmlheading();
$header->type = 1;
$header->str = ucwords($headerTitle);

echo '<br />' . $header->show();


// CREATE FORM
$form = new form('createcontext', $this->uri(array('action' => $formAction)));


$code = new textinput('contextcode');

if ($mode == 'add' && is_array($fixup)) {
    if ($fixup['contextcode'] == '') {
        $contextCodeMessage = '<span class="warning">' . $this->objLanguage->code2Txt('mod_contextadmin_didnotentercontextcode', 'contextadmin', NULL, 'You did not enter a [-context-] code') . '</span>';
    } else {
        $contextCodeMessage = '<span class="warning">' . $this->objLanguage->languageText('mod_contextadmin_youentered', 'contextadmin', 'You entered') . ' <strong><u>' . $fixup['contextcode'] . '</u></strong> ' . $this->objLanguage->languageText('mod_contextadmin_buthasalreadybeentaken', 'contextadmin', 'but that has been taken already') . '</span>';
    }
} else {
    $contextCodeMessage = '';
}

$codeLabel = new label(ucwords($this->objLanguage->code2Txt('mod_context_contextcode', 'context', NULL, '[-context-] Code')), 'input_contextcode');


$title = new textinput('title');
$title->size = 50;

if ($mode == 'add' && is_array($fixup)) {
    $title->value = $fixup['title'];
} else if ($mode == 'edit') {
    $title->value = $context['title'];
}

$titleLabel = new label($this->objLanguage->languageText('word_title', 'system', 'Title'), 'input_title');
$objConfig = $this->getObject('altconfig', 'config');
$skinName = $objConfig->getdefaultSkin();

$validCanvases = array_map('basename', glob('usrfiles/context/' . $this->objContext->getContextCode() . '/canvases/*', GLOB_ONLYDIR));


$canvas = new dropdown('canvas');
//$status->setBreakSpace('<br />');

$canvas->addOption('None', 'None');
foreach ($validCanvases as $validCanvas) {
    $canvas->addOption($validCanvas, $validCanvas);
}

//$canvas->size = 50;

if ($mode == 'add' && is_array($fixup)) {
    $canvas->setSelected($fixup['canvas']);
} else if ($mode == 'edit') {
    $canvas->setSelected($context['canvas']);
}

$canvasLabel = new label($this->objLanguage->languageText('mod_contextadmin_theme', 'contextadmin', 'Theme'), 'input_canvas');


$status = new dropdown('status');
//$status->setBreakSpace('<br />');
$status->addOption('Published', $this->objLanguage->languageText('word_published', 'system', 'Published'));
$status->addOption('Unpublished', $this->objLanguage->languageText('word_unpublished', 'system', 'Unpublished'));

if ($mode == 'add' && is_array($fixup)) {
    $status->setSelected($fixup['status']);
} else if ($mode == 'edit') {
    $status->setSelected($context['status']);
}

    //$access = new hiddeninput('access', 'Private');
//} else {
if ($objSysConfig->getValue('context_access_private_only', 'context', 'false') == 'false') {
    $access = new radio('access');
    $access->setBreakSpace('<br />');
    $access->addOption('Public', '<strong>' . $this->objLanguage->languageText('word_public', 'system', 'Public') . '</strong> - <span class="caption">' . $this->objLanguage->code2Txt('mod_context_publichelp', 'context', NULL, '[-context-] can be accessed by all users, including anonymous users') . '</span>');
    $access->addOption('Open', '<strong>' . $this->objLanguage->languageText('word_open', 'system', 'Open') . '</strong> - <span class="caption">' . $this->objLanguage->code2Txt('mod_context_opencontextdescription', 'context', NULL, '[-context-] can be accessed by all users that are logged in') . '</span>');
    $access->addOption('Private', '<strong>' . $this->objLanguage->languageText('word_private', 'system', 'Private') . '</strong> - <span class="caption">' . $this->objLanguage->code2Txt('mod_context_privatecontextdescription', 'context', NULL, 'Only [-context-] members can enter the [-context-]') . '<span class="caption">');


    if ($mode == 'add' && is_array($fixup)) {
        $access->setSelected($fixup['access']);
    } else if ($mode == 'add') {
        $access->setSelected('Public');
    } else if ($mode == 'edit') {
        $access->setSelected($context['access']);
    }
}

$table = $this->newObject('htmltable', 'htmlelements');

if ($mode == 'edit') {
    $table->startRow();
    $table->addCell($this->objLanguage->code2Txt('mod_context_contextcode', 'context', NULL, '[-context-] Code'), 100);
    $table->addCell('<strong title="' . $this->objLanguage->code2Txt('mod_contextadmin_contextcodecannotbechanged', 'contextadmin', NULL, '[-context-] code can not be changed') . '">' . strtoupper($context['contextcode']) . '</strong>');
    $table->endRow();
    $hiddenInput = new hiddeninput('editcontextcode', $context['contextcode']);
    $form->addToForm($hiddenInput->show());
} else {
    $table->startRow();
    $table->addCell($codeLabel->show(), 100);
    $table->addCell($code->show() . ' <span id="contextcodemessage">' . $contextCodeMessage . '</span>');
    $table->endRow();
}

$table->startRow();
$table->addCell($titleLabel->show());
$table->addCell($title->show());
$table->endRow();


$uploadlink = new link($this->uri(array("action" => "uploadtheme")));
$uploadlink->link = '<strong>' . $this->objLanguage->languageText('mod_contextadmin_upload', 'contextadmin', 'Upload') . '</strong>';

if ($mode == 'edit') {
    $table->startRow();
    $table->addCell($canvasLabel->show());
    $table->addCell($canvas->show() . $uploadlink->show());
}
$table->endRow();

$table->startRow();
$table->addCell('&nbsp;');
$table->addCell('&nbsp;');
$table->endRow();

$table->startRow();
$table->addCell($this->objLanguage->languageText('word_status', 'system', 'Status'));
$table->addCell($status->show());
$table->endRow();

$table->startRow();
$table->addCell('&nbsp;');
$table->addCell('&nbsp;');
$table->endRow();

$showcomment = new dropdown('showcomment');
//$status->setBreakSpace('<br />');
$showcomment->addOption('1', $this->objLanguage->languageText('word_yes', 'system', 'No') . " ");
$showcomment->addOption('0', $this->objLanguage->languageText('word_no', 'system', 'No') . " ");

if ($mode == 'add' && is_array($fixup)) {
    $showcomment->setSelected($fixup['showcomment']);
} else if ($mode == 'edit') {
    $showcomment->setSelected($context['showcomment']);
}

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_contextadmin_comment', 'contextadmin', 'Comment'));
$table->addCell($showcomment->show() . " *" . $this->objLanguage->languageText('mod_contextadmin_comments', 'contextadmin', 'Enable or Disable users to post comments on page content'));
$table->endRow();

$table->startRow();
$table->addCell('&nbsp;');
$table->addCell('&nbsp;');
$table->endRow();

if ($objSysConfig->getValue('context_access_private_only', 'context', 'false') == 'false') {
    $table->startRow();
    $table->addCell($this->objLanguage->languageText('word_access', 'system', 'Access'));
    $table->addCell($access->show());
    $table->endRow();
}

$emailAlert = new checkbox('emailalertopt', $this->objLanguage->languageText('mod_contextadmin_emailalertwhat', 'contextadmin', 'Send email alerts'), true);  // this will checked

if ($mode == 'add' && is_array($fixup)) {
    $emailAlert->setChecked($fixup['alerts']);
} elseif($mode == 'add') {
    $emailAlert->setChecked('0');
} else if ($mode == 'edit') {
    $emailAlert->setChecked($context['alerts']);
}

$table->startRow();
$table->addCell($this->objLanguage->languageText('mod_contextadmin_emailalert', 'contextadmin', 'Alerts'));
$table->addCell($emailAlert->show());
$table->endRow();

$button = new button('savecontext', $this->objLanguage->languageText('mod_contextadmin_gotonextstep', 'contextadmin', 'Go to Next Step'));
$button->cssId = 'savebutton';
$button->setToSubmit();

//$table_ = $table->show();
//if ($objSysConfig->getValue('context_access_private_only', 'context', 'false') == 'true') {
//    $table_ .= $access->show();
//}
$form->addToForm( $table->show() . '<p><br />' . $button->show() . '</p>');

$hiddenInput = new hiddeninput('mode', $mode);
$form->addToForm($hiddenInput->show());

if ($mode == 'add') {
    $form->addRule('contextcode', $this->objLanguage->code2Txt('mod_contextadmin_pleaseentercontextcode', 'contextadmin', NULL, 'Please enter a [-context-] code'), 'required');
}
$form->addRule('title', $this->objLanguage->languageText('mod_contextadmin_pleaseentertitle', 'contextadmin', 'Please enter a title'), 'required');

echo $form->show();
?>
