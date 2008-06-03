<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('loader');

if ($mode == 'edit') {
    $fixup = NULL;
    
    $formAction = 'updatecontext';
    $headerTitle = ucwords($this->objLanguage->code2Txt('mod_context_updatecontext', 'context', NULL, 'Update [-context-]')).': '.$context['title'];
    
    
    
} else {
    $formAction = 'savestep1';
    $headerTitle = $this->objLanguage->code2Txt('mod_contextadmin_createnewcontext', 'contextadmin', NULL, 'Create New [-Context-]');
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
                    jQuery("#contextcodemessage").html("<span id=\"contextcodecheck\">'.addslashes($objIcon->show()).' Checking ...</span>");
                    
                    
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
                                    jQuery("#contextcodemessage").html("Context Code exists. Please try another one.");
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
    $objStepMenu->addStep(str_replace('[-num-]', 1, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_context_contextsettings', 'context', NULL, '[-context-] Settings')), ucwords($this->objLanguage->code2Txt('mod_contextadmin_updatecontextitlesettings', 'contextadmin', NULL, 'Update [-context-] Title and Settings')));
} else {
    $objStepMenu->addStep(str_replace('[-num-]', 1, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_context_contextsettings', 'context', NULL, '[-context-] Settings')), $this->objLanguage->code2Txt('mod_contextadmin_checkcontextcodeavailable', 'contextadmin', NULL, 'Enter [-context-] settings and check whether [-context-] code is available'));
}
$objStepMenu->addStep(str_replace('[-num-]', 2, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_contextadmin_contextinformation', 'contextadmin', NULL, '[-context-] Information')), $this->objLanguage->code2Txt('mod_contextadmin_enterinfoaboutcontext', 'contextadmin', NULL, 'Enter more information about your [-context-] and select a [-context-] image'));
$objStepMenu->addStep(str_replace('[-num-]', 3, $this->objLanguage->code2Txt('mod_contextadmin_stepnumber', 'contextadmin', NULL, 'Step [-num-]')).' - '.ucwords($this->objLanguage->code2Txt('mod_context_contextplugins', 'context', NULL, '[-context-] Plugins')), $this->objLanguage->code2Txt('mod_contextadmin_selectpluginsforcontext', 'contextadmin', NULL, 'Select the plugins you would like to use in this [-context-]'));
$objStepMenu->current = 1;
echo $objStepMenu->show();


$header = new htmlheading();
$header->type = 1;
$header->str = ucwords($headerTitle);

echo '<br />'.$header->show();


// CREATE FORM
$form = new form ('createcontext', $this->uri(array('action'=>$formAction)));


$code = new textinput('contextcode');

if ($mode == 'add' && is_array($fixup)) {
    if ($fixup['contextcode'] == '') {
        $contextCodeMessage = '<span class="warning">'.$this->objLanguage->code2Txt('mod_contextadmin_didnotentercontextcode', 'contextadmin', NULL, 'You did not enter a [-context-] code').'</span>';
    } else {
        $contextCodeMessage = '<span class="warning">'.$this->objLanguage->languageText('mod_contextadmin_youentered', 'contextadmin', 'You entered').' <strong><u>'.$fixup['contextcode'].'</u></strong> '.$this->objLanguage->languageText('mod_contextadmin_buthasalreadybeentaken', 'contextadmin', 'but that has been taken already').'</span>';
    }
} else {
    $contextCodeMessage = '';
}

$codeLabel = new label (ucwords($this->objLanguage->code2Txt('mod_context_contextcode', 'context', NULL, '[-context-] Code')), 'input_contextcode');


$title = new textinput('title');
$title->size = 50;

if ($mode == 'add' && is_array($fixup)) {
    $title->value = $fixup['title'];
} else if ($mode == 'edit') {
    $title->value = $context['title'];
}

$titleLabel = new label ($this->objLanguage->languageText('word_title', 'system', 'Title'), 'input_title');

$status = new dropdown ('status');
//$status->setBreakSpace('<br />');
$status->addOption('Published', $this->objLanguage->languageText('word_published', 'system', 'Published'));
$status->addOption('Unpublished', $this->objLanguage->languageText('word_unpublished', 'system', 'Unpublished'));

if ($mode == 'add' && is_array($fixup)) {
    $status->setSelected($fixup['status']);
} else if ($mode == 'edit') {
    $status->setSelected($context['status']);
}

$access= new radio ('access');
$access->setBreakSpace('<br />');
$access->addOption('Public', '<strong>'.$this->objLanguage->languageText('word_public', 'system', 'Public').'</strong> - <span class="caption">'.$this->objLanguage->code2Txt('mod_context_publichelp', 'context', NULL, '[-context-] can be accessed by all users, including anonymous users').'</span>');
$access->addOption('Open', '<strong>'.$this->objLanguage->languageText('word_open', 'system', 'Open').'</strong> - <span class="caption">'.$this->objLanguage->code2Txt('mod_context_opencontextdescription', 'context', NULL, '[-context-] can be accessed by all users that are logged in').'</span>');
$access->addOption('Private', '<strong>'.$this->objLanguage->languageText('word_private', 'system', 'Private').'</strong> - <span class="caption">'.$this->objLanguage->code2Txt('mod_context_privatecontextdescription', 'context', NULL, 'Only [-context-] members can enter the [-context-]').'<span class="caption">');

if ($mode == 'add' && is_array($fixup)) {
    $access->setSelected($fixup['access']);
} else if ($mode == 'add') {
    $access->setSelected('Public');
} else if ($mode == 'edit') {
    $access->setSelected($context['access']);
}

$table = $this->newObject('htmltable', 'htmlelements');

if ($mode == 'edit') {
    $table->startRow();
    $table->addCell($this->objLanguage->code2Txt('mod_context_contextcode', 'context', NULL, '[-context-] Code'), 100);
    $table->addCell('<strong title="'.$this->objLanguage->code2Txt('mod_contextadmin_contextcodecannotbechanged', 'contextadmin', NULL, '[-context-] code can not be changed').'">'.strtoupper($context['contextcode']).'</strong>');
    $table->endRow();
    $hiddenInput = new hiddeninput('editcontextcode', $context['contextcode']);
    $form->addToForm($hiddenInput->show());
} else {
    $table->startRow();
    $table->addCell($codeLabel->show(), 100);
    $table->addCell($code->show().' <span id="contextcodemessage">'.$contextCodeMessage.'</span>');
    $table->endRow();
}

$table->startRow();
$table->addCell($titleLabel->show());
$table->addCell($title->show());
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
$table->addCell($this->objLanguage->languageText('word_access', 'system', 'Access'));
$table->addCell($access->show());
$table->endRow();





$button = new button ('savecontext', $this->objLanguage->languageText('mod_contextadmin_gotonextstep', 'contextadmin', 'Go to Next Step'));
$button->cssId = 'savebutton';
$button->setToSubmit();

$form->addToForm($table->show().'<p><br />'.$button->show().'</p>');

$hiddenInput = new hiddeninput('mode', $mode);
$form->addToForm($hiddenInput->show());

if ($mode == 'add') {
    $form->addRule('contextcode', $this->objLanguage->code2Txt('mod_contextadmin_pleaseentercontextcode', 'contextadmin', NULL, 'Please enter a [-context-] code'), 'required');
}
$form->addRule('title', $this->objLanguage->languageText('mod_contextadmin_pleaseentertitle', 'contextadmin', 'Please enter a title'), 'required');

echo $form->show();

?>