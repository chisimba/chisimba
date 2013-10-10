<?php
$ret ="";
// Can change field
if ($mode == 'edit') {
    $canChangeField = $this->objAssignmentSubmit->getCountStudentSubmissions($assignment['id']) == 0;
} else { // Mode is add so we can always change the type of the assignment
    $canChangeField = true;
}
// JavaScript
if ($canChangeField) {
    $jsToggleType = '<script language="JavaScript" type="text/javascript">
function setTypeOptionsToDefault()
{
    var els = document.getElementsByName(\'filetypes[]\');
    var len = els.length;
    for (var i=0; i<len; ++i)
        els[i].checked = false;
    var els = document.getElementsByName(\'filenameconversion\');
    var len = els.length;
    for (var i=0; i<len; ++i)
    switch (els[i].value) {
        case \'0\':
            els[i].checked = false;
            break;
        case \'1\':
            els[i].checked = true;
            break;
        default: ;
    }
    return;
}
function toggleTypeOptions(val)
{
    var els = document.getElementsByName(\'filetypes[]\');
    var len = els.length;
    for (var i=0; i<len; ++i)
        els[i].disabled = !val;
    var els = document.getElementsByName(\'filenameconversion\');
    var len = els.length;
    for (var i=0; i<len; ++i)
        els[i].disabled = !val;
    return;
}
function toggleType(el)
{
    switch (el.value) {
    case \'0\':
        document.getElementById(\'uploadableOptions\').style.display = \'none\';
        setTypeOptionsToDefault();
        toggleTypeOptions(false);
        break;
    case \'1\':
        document.getElementById(\'uploadableOptions\').style.display = \'inline\';
        toggleTypeOptions(true);
        break;
    default: ;
    }
    return true;
}
</script>';
    echo $jsToggleType;
}

// Load classes
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');

$objGoals = $this->getObject('dbContext_learneroutcomes', 'context');
$goals = $objGoals->getContextOutcomes($this->objContext->getContextCode());
$objDbWorkgroup = $this->getObject('dbworkgroup', 'workgroup');
$groups = $objDbWorkgroup->getAll($this->contextCode);
//var_dump($groups);

// Heading
if ($mode == 'edit') {
    $headingStr = $this->objLanguage->languageText('mod_assignment_editassignment', 'assignment', 'Edit Assignment') . ': ' . $assignment['name'];
    $action = 'updateassignment';
} else {
    $headingStr = $this->objLanguage->languageText('mod_assignment_createassignment', 'assignment', 'Create a New Assignment');
    $action = 'saveassignment';
}
$heading = new htmlHeading();
$heading->type = 1;
$heading->str = $headingStr;
$ret .= $heading->show();
// Table
$generaltable = $this->newObject('htmltable', 'htmlelements');

// Name
$generaltable->startRow();
$label = new label($this->objLanguage->languageText('mod_assignment_assignmentname', 'assignment', 'Assignment Name'), 'input_name');
$textinput = new textinput('name');
$textinput->size = 60;
if ($mode == 'edit') {
    $textinput->value = $assignment['name'];
}
$generaltable->addCell($label->show(), 200);
$generaltable->addCell($textinput->show());
$generaltable->endRow();

//visibility

$generaltable->startRow();
$generaltable->addCell($this->objLanguage->languageText('mod_assignment_visibility', 'assignment', 'Visibility'));
$radio = new radio('visibility');
$radio->addOption(1, $this->objLanguage->languageText('mod_assignment_display', 'assignment', 'Display assignment'));
$radio->addOption(0, $this->objLanguage->languageText('mod_assignment_hide', 'assignment', 'Hide assignment until it is assigned
to an individual or group of Students'));
if ($mode == 'edit') {
    $radio->setSelected($assignment['visibility']);
} else {
    $radio->setSelected(1);
}
$radio->setBreakSpace('&nbsp;');
$generaltable->addCell($radio->show());
$generaltable->endRow();

// type
$typetable = $this->newObject('htmltable', 'htmlelements');

$typetable->startRow();
$typetable->addCell($this->objLanguage->languageText('mod_assignment_assignmenttype', 'assignment', 'Assignment Type'));
$radio = new radio($canChangeField?'type':'typeDisabled');
$radio->extra = 'onclick="toggleType(this);"';
$radio->addOption(0, $this->objLanguage->languageText('mod_assignment_online', 'assignment', 'Online'));
$radio->addOption(1, $this->objLanguage->languageText('mod_assignment_upload', 'assignment', 'Upload'));
if ($mode == 'edit') {
    $radio->setSelected($assignment['format']);
} else {
    $radio->setSelected(0);
}
if (!$canChangeField) {
    $radio->extra = 'disabled="disabled"';
}
$radio->setBreakSpace('&nbsp;');
if ($mode == 'edit' && !$canChangeField) {
    $textinput = new textinput('type');
    $textinput->value = $assignment['format'];
    $textinput->fldType = "hidden";
}
$typetable->addCell($radio->show().($canChangeField?'':($textinput->show().'<sup>1</sup>')));

$typetable->endRow();

// Uploadable options
if ($mode == 'edit') {
    $uploadableOptionsEnabled = $assignment['format'] == '1';
} else {
    $uploadableOptionsEnabled = FALSE;
}
//
//$headingUploadableOptions = new htmlHeading();
//$headingUploadableOptions->type = 3;
//$headingUploadableOptions->str = $this->objLanguage->languageText('mod_assignment_uploadoptions', 'assignment');
// Uploadable options table
$tableUploadableOptions = $this->newObject('htmltable', 'htmlelements');
// Uploadable file types
$tableUploadableOptions->startRow();
$tableUploadableOptions->addCell($this->objLanguage->languageText('mod_assignment_uploadablefiletypes', 'assignment'));
$objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
$allowedFileTypes = $objSysConfig->getValue('FILETYPES_ALLOWED', 'assignment');
if (is_null($allowedFileTypes)) {
    $arrAllowedFileTypes = array('doc', 'odt', 'rtf', 'txt', 'docx', 'mp3', 'ppt', 'pptx','odp', 'pdf', 'zip');
} else {
    $arrAllowedFileTypes = explode(',', $allowedFileTypes);
}
if ($uploadableOptionsEnabled) {
    if ($mode == 'edit') {
        $rs = $this->objAssignmentUploadablefiletypes->getFiletypes($assignment['id']);
    } else {
        $rs = array();
    }
    $arrAllowedFileTypesSelected = array();
    if (!empty($rs)) {
        foreach ($rs as $row) {
            $arrAllowedFileTypesSelected[] = $row['filetype'];
        }
    }
} else {
    //$arrAllowedFileTypesSelected = $arrAllowedFileTypes;
    // Set selected file types to all be deselected
    $arrAllowedFileTypesSelected = array();
}
$this->loadClass('checkbox', 'htmlelements');
$stringFiletypes = '';
$separator = '';
foreach ($arrAllowedFileTypes as $filetype) {
    $objCheckbox = new checkbox('filetypes[]', '', in_array($filetype, $arrAllowedFileTypesSelected));
    $objCheckbox->setValue($filetype);
    if (!$uploadableOptionsEnabled) {
        $objCheckbox->extra = 'disabled="disabled"';
    }
    $stringFiletypes .= $separator . $objCheckbox->show() . '&nbsp;' . $filetype;
    $separator = ' ';
    unset($objCheckbox);
}
$tableUploadableOptions->addCell($stringFiletypes);
$tableUploadableOptions->endRow();
//filename conversion
$tableUploadableOptions->startRow();
$tableUploadableOptions->addCell($this->objLanguage->languageText('mod_assignment_filenameconversion', 'assignment', 'Convert the Filename on Download?'));
$radio = new radio('filenameconversion');
$radio->addOption(1, $this->objLanguage->languageText('word_yes', 'system', 'Yes'));
$radio->addOption(0, $this->objLanguage->languageText('word_no', 'system', 'No'));
if ($uploadableOptionsEnabled) {
    if ($mode == 'edit') {
        $radio->setSelected($assignment['filename_conversion']);
    }
} else {
    $radio->setSelected(1);
    $radio->extra = 'disabled="disabled"';
}
$radio->setBreakSpace('&nbsp;');
$tableUploadableOptions->addCell($radio->show());
$tableUploadableOptions->endRow();
// Uploadable options fieldset
$fs = new fieldset();
$fs->setLegend($this->objLanguage->languageText('mod_assignment_uploadoptions', 'assignment'));
$fs->addContent($tableUploadableOptions->show());
// Uploadable options cell
$typetable->startRow();
$typetable->addcell('<span id="uploadableOptions"'.($uploadableOptionsEnabled?'':' style="display: none;"').'>'.$fs->show().'</span>',NULL, NULL, NULL, NULL, 'colspan="2"');
$typetable->endRow();
// Reflection
$typetable->startRow();
$typetable->addCell($this->objLanguage->languageText('mod_assignment_isreflection', 'assignment', 'Is it a Reflection?'));

$radio = new radio($canChangeField?'assesment_type':'assesment_typeDisabled');
$radio->addOption(1, $this->objLanguage->languageText('word_yes', 'system', 'Yes'));
$radio->addOption(0, $this->objLanguage->languageText('word_no', 'system', 'No'));
$radio->setBreakSpace('&nbsp;');
if ($mode == 'edit') {
    $radio->setSelected($assignment['assesment_type']);
}
if (!$canChangeField) {
    $radio->extra = 'disabled="disabled"';
}
$radio->setBreakSpace('&nbsp;');
if ($mode == 'edit' && !$canChangeField) {
    $textinput = new textinput('assesment_type');
    $textinput->fldType = "hidden";
    $textinput->value = $assignment['assesment_type'];
}

$typetable->addCell($radio->show().($canChangeField?'':($textinput->show().'<sup>1</sup>')));
$typetable->endRow();

// Multiple submissions
$typetable->startRow();
$typetable->addCell($this->objLanguage->languageText('mod_assignment_allowresubmit', 'assignment', 'Allow Multiple Submissions?'));
$radio = new radio('resubmit');
$radio->addOption(1, $this->objLanguage->languageText('word_yes', 'system', 'Yes'));
$radio->addOption(0, $this->objLanguage->languageText('word_no', 'system', 'No'));
if ($mode == 'edit') {
    $radio->setSelected($assignment['resubmit']);
}
$radio->setBreakSpace('&nbsp;');
$typetable->addCell($radio->show());
$typetable->endRow();

//email alerts
$commtable = $this->newObject('htmltable', 'htmlelements');

$commtable->startRow();
$commtable->addCell($this->objLanguage->languageText('mod_assignment_emailalerttostudents', 'assignment', 'Send email alert to students when assignment is created'));
$radio = new radio('emailalert');
$radio->addOption(1, $this->objLanguage->languageText('word_yes', 'system', 'Yes'));
$radio->addOption(0, $this->objLanguage->languageText('word_no', 'system', 'No'));
if ($mode == 'edit') {
    $radio->setSelected($assignment['email_alert']);
} else {
    $radio->setSelected(1);
}
$radio->setBreakSpace('&nbsp;');
$commtable->addCell($radio->show());

$commtable->endRow();

$commtable->startRow();
$commtable->addCell($this->objLanguage->languageText('mod_assignment_emailalertfromstudents', 'assignment', 'Send email alert to students when assignment is created'));
$radio = new radio('emailalertonsubmit');
$radio->addOption(1, $this->objLanguage->languageText('word_yes', 'system', 'Yes'));
$radio->addOption(0, $this->objLanguage->languageText('word_no', 'system', 'No'));
if ($mode == 'edit') {
    $radio->setSelected($assignment['email_alert_onsubmit']);
} else {
    $radio->setSelected(1);
}
$radio->setBreakSpace('&nbsp;');
$commtable->addCell($radio->show());

/*
  $table->addCell('<div id="emailAlert"></div>');
 */
$commtable->endRow();

// Mark
$gradetable = $this->newObject('htmltable', 'htmlelements');
$gradetable->startRow();
$label = new label($this->objLanguage->languageText('mod_assignment_mark', 'assignment', 'Mark'), 'input_mark');
$textinput = new textinput('mark');
if ($mode == 'edit') {
    $textinput->value = $assignment['mark'];
}
$gradetable->addCell($label->show());
$gradetable->addCell($textinput->show());
$gradetable->endRow();
// Percentage of year mark
$gradetable->startRow();
$label = new label($this->objLanguage->languageText('mod_assignment_percentyrmark', 'assignment', 'Percentage of year mark'), 'input_yearmark');
$textinput = new textinput('yearmark');
if ($mode == 'edit') {
    $textinput->value = $assignment['percentage'];
}
$gradetable->addCell($label->show());
$gradetable->addCell($textinput->show());
$gradetable->endRow();

//groups
$recstable = $this->newObject('htmltable', 'htmlelements');

$objGroups = new radio('groups_radio');
$objGroups->addOption('0', $this->objLanguage->languageText('mod_assignment_allstudents', 'assignment', 'All Students individually<br/>'));
$objGroups->addOption('1', $this->objLanguage->languageText('mod_assignment_groupsofstudents', 'assignment', 'Groups of Students'));
$objGroups->setSelected('0');

/*
$groupslink = new link($this->uri(array(), "workgroup"));
$groupslink->link = $this->objLanguage->languageText('mod_assignment_managegroups', 'assignment', "Manage Groups") . '<br/>';
$groupsList = $groupslink->show();
*/
$groupsList = '';

$groupstoselect = array();
if (is_array($workgroupsinassignment)) {
    foreach ($workgroupsinassignment as $row) {
        $groupstoselect[] = $row['workgroup_id'];
    }
}

if (count($groups) > 0) {

    foreach ($groups as $group) {
        $checkbox = new checkbox('groups[]', $group['id']);
        $checkbox->value = $group['id'];
        $checkbox->cssId = 'group_' . $group['id'];
        $checkbox->cssClass = 'group_option';
        if ($mode == 'edit') {
              $objGroups->setSelected($assignment['usegroups']);
            if (in_array($group['id'], $groupstoselect)) {
                $checkbox->ischecked = TRUE;

            }
        }
        $label = new label(' ' . $group['description'], 'group_' . $group['id']);

        $groupsList .= ' ' . $checkbox->show() . $label->show() . '<br />';
    }
}
$recstable->startRow();
$recstable->addCell('<b>' . $this->objLanguage->languageText('mod_assignment_reciepients', 'assignment', 'Assignment recipients') . '</b>');
//$recstable->addCell('<div id="groupsDiv"></div>');
$recstable->addCell($objGroups->show() . '<br>');
$recstable->endRow();

$recstable->startRow();
$label = new label($this->objLanguage->languageText('mod_assignment_selectgroups', 'assignment', "Select groups"));
$recstable->addCell('<div id="selectgroups">' . $label->show() . '</div>');
$recstable->addCell('<div id="groupslist">' . $groupsList . '</div>');
$recstable->endRow();



$objGoalsRadio = new radio('goals_radio');
$objGoalsRadio->addOption('0', $this->objLanguage->languageText('mod_assignment_excludelearningoutcomes', 'assignment', 'Do not use learning outcomes'));
$objGoalsRadio->addOption('1', $this->objLanguage->languageText('mod_assignment_uselearningoutcomes', 'assignment', 'Use learning outcomes'));
//$objGoalsRadio->setId('');
$objGoalsRadio->setSelected('0');


$goalstable = $this->newObject('htmltable', 'htmlelements');
$goalsList = $this->objLanguage->languageText('mod_assignment_none', 'assignment', "None");

$goalstoselect = array();
if (is_array($learningoutcomesinassignment)) {
    foreach ($learningoutcomesinassignment as $row) {
        $goalstoselect[] = $row['learningoutcome_id'];
    }
}

if (count($goals) > 0) {
    $goalsList = "";

    foreach ($goals as $goal) {
        $checkbox = new checkbox('goals[]', $goal['id']);
        $checkbox->value = $goal['id'];
        $checkbox->cssId = 'goal_' . $group['id'];
        $checkbox->cssClass = 'goal_option';

        if ($mode == 'edit') {
               $objGoalsRadio->setSelected($assignment['usegoals']);
            if (in_array($goal['id'], $goalstoselect)) {
                $checkbox->ischecked = TRUE;

            }
        }

        $label = new label(' ' . $goal['learningoutcome'], 'goal_' . $goal['id']);

        $goalsList .= ' ' . $checkbox->show() . $label->show() . '<br />';
    }
}
$label = new label($this->objLanguage->languageText('mod_assignment_selectgoals', 'assignment', "Select goals"));

$goalstable->startRow();
$goalstable->addCell($objGoalsRadio->show());
$goalstable->endRow();


$goalstable->startRow();
$goalstable->addCell('<div id="goalslist">' . $goalsList . '</div>');
$goalstable->endRow();

// Opening date
$datestable = $this->newObject('htmltable', 'htmlelements');
$datestable->startRow();
$datestable->addCell($this->objLanguage->languageText('mod_assignment_openingdate', 'assignment', 'Opening Date'));
$objDateTime = $this->getObject('dateandtime', 'utilities');
$objDatePicker = $this->newObject('datepicker', 'htmlelements');
$objDatePicker->name = 'openingdate';
if ($mode == 'edit') {
    $objDatePicker->setDefaultDate(substr($assignment['opening_date'], 0, 10));
}
$objTimePicker = $this->newObject('timepicker', 'htmlelements');
$objTimePicker->name = 'openingtime';
if ($mode == 'edit') {
    $objTimePicker->setSelected($objDateTime->formatTime($assignment['opening_date']));
}
$s_table = $this->newObject('htmltable', 'htmlelements');
$s_table->startRow();
$s_table->addCell($objDatePicker->show(), 200);
$s_table->addCell($objTimePicker->show());
$s_table->endRow();
$datestable->addCell($s_table->show());
$datestable->endRow();



// Closing date
$datestable->startRow();
$datestable->addCell($this->objLanguage->languageText('mod_assignment_closingdate', 'assignment', 'Closing Date'));
$objDatePicker = $this->newObject('datepicker', 'htmlelements');
$objDatePicker->name = 'closingdate';
if ($mode == 'edit') {
    $objDatePicker->setDefaultDate(substr($assignment['closing_date'], 0, 10));
}
$objTimePicker = $this->newObject('timepicker', 'htmlelements');
$objTimePicker->name = 'closingtime';
if ($mode == 'edit') {
    $objTimePicker->setSelected($objDateTime->formatTime($assignment['closing_date']));
}
$s_table = $this->newObject('htmltable', 'htmlelements');
$s_table->startRow();
$s_table->addCell($objDatePicker->show(), 200);
$s_table->addCell($objTimePicker->show());
$s_table->endRow();
$datestable->addCell($s_table->show());
$datestable->endRow();




// Description
$objEditor = $this->newObject('htmlarea', 'htmlelements');
$objEditor->init('description', NULL, '500px', '500px');
$objEditor->setDefaultToolBarSetWithoutSave();
if ($mode == 'edit') {
    $objEditor->value = $assignment['description'];
}
// Form
$form = new form('addeditassignment', $this->uri(array('action' => $action)));
//$form
if ($mode == 'edit') {
    $hiddenId = new hiddeninput('id', $assignment['id']);
    $form->addToForm($hiddenId->show());
}

$finaltable = $this->newObject('htmltable', 'htmlelements');

$finaltable->startRow();
$generalfs = new fieldset();
$generalfs->setLegend("General");
$generalfs->addContent($generaltable->show());
$finaltable->addCell($generalfs->show());
$finaltable->endRow();

$finaltable->startRow();
$fs = new fieldset();
$fs->setLegend("Assignment Type Options");
$fs->addContent($typetable->show());
$finaltable->addCell($fs->show());
$finaltable->endRow();

$finaltable->startRow();
$fs = new fieldset();
$fs->setLegend("Groups");
$fs->addContent($recstable->show());
$finaltable->addCell($fs->show());
$finaltable->endRow();

$finaltable->startRow();
$fs = new fieldset();
$fs->setLegend("Dates");
$fs->addContent($datestable->show());
$finaltable->addCell($fs->show());
$finaltable->endRow();

$finaltable->startRow();
$fs = new fieldset();
$fs->setLegend("Marks");
$fs->addContent($gradetable->show());
$finaltable->addCell($fs->show());
$finaltable->endRow();

$finaltable->startRow();
$fs = new fieldset();
$fs->setLegend("Communication");
$fs->addContent($commtable->show());
$finaltable->addCell($fs->show());
$finaltable->endRow();

$finaltable->startRow();
$fs = new fieldset();
$fs->setLegend("Learning outcomes");
$fs->addContent($goalstable->show());
$finaltable->addCell($fs->show());
$finaltable->endRow();

$form->addToForm($finaltable->show());
$form->addToForm('<b>' . $this->objLanguage->languageText('mod_assignment_description', 'assignment', "Description") . '</b>');
$form->addToForm($objEditor->show());
$button = new button('save', $this->objLanguage->languageText('mod_assignment_saveassignment', 'assignment', 'Save Assignment'));
$button->setToSubmit();
$form->addToForm($button->show());
$button = new button('cancel', $this->objLanguage->languageText('word_cancel'));
if ($mode == 'edit') {
    $uri = $this->uri(array('action'=>'view', 'id'=>$assignment['id']));
} else {
    $uri = $this->uri(array());
}
$button->setOnClick('javascript: window.location=\''.$uri.'\'');
$form->addToForm($button->show());
$form->addRule('name', $this->objLanguage->languageText('mod_assignment_val_title', 'assignment', 'Please enter title'), 'required');
$js_filetypes = '
<script language="JavaScript" type="text/javascript">
function val_filetypes(name)
{
    var els = document.getElementsByName(\'type\');
    //alert(els.length);
    var len = els.length;
//    if (len == 1)
//        alert(els[0].type);
    if (len == 1 && els[0].type == \'hidden\'&& els[0].value == \'0\')
        return true;
    else {
        for (var i=0; i<len; ++i) {
            if (els[i].value == \'0\'
                && els[i].checked)
            return true;
        }
    }
    //return false;
    //alert(name);
    var els = document.getElementsByName(name);
    var len = els.length;
    if (0 == len)
        return true;
    //alert(\'len==\'+len);
    var cnt = 0;
    for (var i=0; i<len; ++i)
        //alert(els[i].value);
        if (els[i].checked)
            ++cnt;
    var res = 0 != cnt;
    if (!res)
        els[0].focus();
    return res;
}
</script>
';
echo $js_filetypes;
$form->addRule('filetypes[]', $this->objLanguage->languageText('mod_assignment_selectatleastone', 'assignment'), 'custom', 'val_filetypes');
$form->addRule('mark', $this->objLanguage->languageText('mod_assignment_val_mark', 'assignment', 'Please enter mark'), 'required');
$form->addRule('mark', $this->objLanguage->languageText('mod_assignment_val_numreq', 'assignment', 'Has to be a number'), 'numeric');
$form->addRule('yearmark', $this->objLanguage->languageText('mod_assignment_val_yearmark', 'assignment', 'Please enter year mark'), 'required');
$form->addRule('yearmark', $this->objLanguage->languageText('mod_assignment_val_numreq', 'assignment', 'Has to be a number'), 'numeric');

$ret .= $form->show();
echo "<div class='assignment_main'>$ret</div>";


// Footer note
if (!$canChangeField) {
    echo '<sup>1</sup>' . $this->objLanguage->languageText('mod_assignment_cannotchangefield', 'assignment');
}

$hidegroups = "0";
if ($mode == 'edit') {
    $hidegroups = $assignment['usegroups'];
}

$hidegoals = "0";
if ($mode == 'edit') {
    $hidegoals = $assignment['usegoals'];
}
$groupsJs = '
var hidegroups ="' . $hidegroups . '";

var hidegoals="' . $hidegoals . '";
jQuery(document).ready(function() {

                   if(hidegroups == 0){
                   jQuery("#groupslist").hide();
                   jQuery("#selectgroups").hide();
                   }
                   if(hidegoals == 0){
                   jQuery("#goalslist").hide();
                   jQuery("#selectgoals").hide();
                   }

                    jQuery("input[name=\'groups_radio\']").change(function(){
                     var radiobuttonvalue = jQuery("input[name=\'groups_radio\']:checked").val();
                     if(jQuery.browser.msie){
                       if(radiobuttonvalue == 1)
                       radiobuttonvalue=0;
                       else
                       radiobuttonvalue=1;

                    }
                    if(radiobuttonvalue == 1){
                        jQuery("#groupslist").show();
                        jQuery("#selectgroups").show();
                    }else{
                        jQuery("#groupslist").hide();
                        jQuery("#selectgroups").hide();

                    }

 });


                    jQuery("input[name=\'goals_radio\']").change(function(){
                   var radiobuttonvalue = jQuery("input[name=\'goals_radio\']:checked").val();

           if(jQuery.browser.msie){
                       if(radiobuttonvalue == 1)
                       radiobuttonvalue=0;
                       else
                       radiobuttonvalue=1;

                    }
                    if(radiobuttonvalue == 1){
                       jQuery("#goalslist").show();
                        jQuery("#selectgoals").show();
                    }else{
                        jQuery("#goalslist").hide();
                        jQuery("#selectgoals").hide();

                    }

                  });

     });';

echo "<script type='text/javascript'>" . $groupsJs . "</script>";
?>
