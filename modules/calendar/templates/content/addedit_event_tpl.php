
<script language="JavaScript">

    if(!document.getElementById && document.all)
        document.getElementById = function(id){ return document.all[id]} 


    function toggleMultiDayInput()
    {
        if (document.eventform.multidayevent[1].checked)
        {
            showhide('cal_input2', 'visible');
            showhide('cal_label2', 'visible');
        } else{
            showhide('cal_input2', 'hidden');
            showhide('cal_label2', 'hidden');
        }
            
    }
    
    function showhide (id, visible)
    {
        var style = document.getElementById(id).style
        style.visibility = visible;
    }


</script>
<?php

$this->objHelp = & $this->getObject('help', 'help');



if ($mode == 'edit') {
    $action = 'updateevent';
    $title = $this->objLanguage->languageText('mod_calendarbase_editevent', 'calendarbase');
} else {
    $mode = 'add';
    $action = 'saveevent';
    $title = $this->objLanguage->languageText('mod_calendarbase_addevent', 'calendarbase') . $this->objHelp->show();
}

$this->loadClass('link', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('textarea', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('radio', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('iframe', 'htmlelements');
$bodyInput = & $this->newObject('htmlarea', 'htmlelements');

$headerParams = $this->getJavascriptFile('ts_picker.js', 'htmlelements');
$headerParams.="<script>/*Script by Denis Gritcyuk: tspicker@yahoo.com
Submitted to JavaScript Kit (http://javascriptkit.com)
Visit http://javascriptkit.com for this script*/ </script>";
$this->appendArrayVar('headerParams', $headerParams);


echo ('<h1>' . $title . '</h1>');



$table = $this->newObject('htmltable', 'htmlelements');


$table->width = '99%';

$table->startRow();
$multiDayChoice = new radio('multidayevent');
$multiDayChoice->addOption('0', $this->objLanguage->languageText('word_no'));
$multiDayChoice->addOption('1', $this->objLanguage->languageText('word_yes'));
if ($mode == 'edit' && $event['multiday_event'] == 1) {
    $multiDayChoice->setSelected('1');
} else {
    $multiDayChoice->setSelected('0');
}
$multiDayChoice->setBreakSpace(' / ');
$multiDayChoice->extra = 'onClick="toggleMultiDayInput()"';

$text = $this->objLanguage->languageText('mod_calendarbase_isthisamultidayevent', 'calendarbase', 'Is this a multiday event? ') . $multiDayChoice->show();

$table->addCell($text, NULL, NULL, NULL, NULL, 'colspan="4"');
$table->endRow();




$table->startRow();
$dateLabel = new label($this->objLanguage->languageText('mod_calendarbase_dateofevent', 'calendarbase') . ':', 'input_date');
$table->addCell($dateLabel->show(), 150);



$dateInput = $this->newObject('datepicker', 'htmlelements');
$dateInput->setName('date');
if ($mode == 'edit') {
    $dateInput->setDefaultDate($event['eventdate']);
} else {
    $dateInput->setDefaultDate(date('Y-m-d'));
}



$table->addCell($dateInput->show());

// --- SECOND DATE --- //

$dateLabel2 = new label($this->objLanguage->languageText('mod_calendarbase_dateofevent', 'calendarbase') . ':', 'input_date');

if ($mode == 'add' || $event['multiday_event'] != 1) {
    $style = ' style="visibility:hidden"';
} else {
    $style = '';
}
$table->addCell('<div id="cal_label2" ' . $style . '>' . $dateLabel2->show() . '</div>');

$dateInput2 = $this->newObject('datepicker', 'htmlelements');
$dateInput2->setName('date2');


if ($mode == 'edit') {
    if ($event['multiday_event'] == 1) {
        $dateInput2->setDefaultDate($event['eventdate2']);
    } else {
        $dateInput2->setDefaultDate($event['eventdate']);
    }
} else {
    $dateInput2->setDefaultDate(date('Y-m-d'));
}


$table->addCell('<div id="cal_input2" ' . $style . '>' . $dateInput2->show() . '</div>');

$table->endRow();


$table->startRow();
$timeLabel = new label($this->objLanguage->languageText('mod_calendarbase_time', 'calendar') . ':', 'input_time');





$timePicker = $this->newObject('timepicker', 'htmlelements');
$timePicker->name = 'timefrom';

if ($mode == 'edit') {

    $timePicker->setSelected($event['timefrom']);
}

$table->addCell($timeLabel->show());
$from = $timePicker->show();

$timePicker = $this->newObject('timepicker', 'htmlelements');
$timePicker->name = 'timeto';

if ($mode == 'edit') {
    $timePicker->setSelected($event['timeto']);
}

$to = $timePicker->show();

$table->addCell('From ' . $from . '&nbsp;To ' . $to, NULL, NULL, NULL, NULL, 'colspan="3"');
$table->endRow();
// end - date inputs //

/*
  This provides the option to add an event to either
  a) Personal Calendar
  b) Course Calendar
  c) Site Calendar

  Ability to add to Course Calendar and Site Calendar is based on permission.
  If the user only has permission to add to their own calendar, these options are not shown.
  It will default to the users personal calendar
 */


$eventfor = new radio('eventfor');
$eventfor->setBreakSpace('<br />');
$eventfor->addOption('0', $this->objLanguage->languageText('mod_calendarbase_mypersonalcalendar', 'calendarbase', 'My Personal Calendar'));
if ($isInContext && $isContextLecturer) {
    $courselabel = ucwords($this->objLanguage->code2Txt('mod_calendarbase_coursecalendar', 'calendarbase', NULL, '{COURSE} [-context-] Calendar'));
    $courselabel = str_replace('{COURSE}', $courseTitle, $courselabel);
    $eventfor->addOption('1', $courselabel);
    $eventfor->setSelected('1');
}


$objModules = $this->getObject('modules', 'modulecatalogue');


if ($mode == 'add')
{
    if (!is_null($groupid)) {
        
        $eventfor->addOption('2', $this->objLanguage->languageText('mod_calendar_groupcalendar', 'calendar', 'Group Calendar'));
    }
}


if ($isAdmin) {
    $eventfor->addOption('3', $this->objLanguage->languageText('mod_calendarbase_sitecalendar', 'calendarbase', 'Site Calendar'));
}

if (count($eventfor->options) > 1 && $mode != 'edit') { // Only show Radio buttons if more than one option is available
    $table->startRow();
    $titleLabel = new label($this->objLanguage->languageText('mod_calendarbase_addeventto', 'calendarbase', 'Add Event to') . ':', 'input_eventfor');
    $table->addCell($titleLabel->show());
    $table->addCell($eventfor->show(), NULL, NULL, NULL, NULL, 'colspan="3"');
    $table->endRow();
}



$table->startRow();
$titleLabel = new label($this->objLanguage->languageText('mod_calendarbase_eventtitle', 'calendarbase') . ':', 'input_title');
$table->addCell($titleLabel->show());

$titleInput = new textinput('title');
if ($mode == 'edit') {
    $titleInput->value = $event['eventtitle'];
}

$titleInput->size = '65';

$table->addCell($titleInput->show(), NULL, NULL, NULL, NULL, 'colspan="3"');
$table->endRow();

$table->startRow();
$detailsLabel = new label($this->objLanguage->languageText('mod_calendarbase_eventdetails', 'calendarbase') . ':', 'input_details');
$table->addCell($detailsLabel->show());

$detailsTextArea = & $this->newObject('htmlarea', 'htmlelements'); //new textarea('details');
$detailsTextArea->name = 'details';

if ($mode == 'edit') {
    $detailsTextArea->value = $event['eventdetails'];
}
$table->addCell($detailsTextArea->show(), NULL, NULL, NULL, NULL, 'colspan="3"');
$table->endRow();

$table->startRow();
$urlLabel = new label($this->objLanguage->languageText('mod_calendarbase_relatedwebsite', 'calendarbase') . ':', 'input_url');
$table->addCell($urlLabel->show());

$urlInput = new textinput('url');
if (($mode == 'edit') && ($event['eventurl'] != '')) {
    $urlInput->value = $event['eventurl'];
} else {
    $urlInput->value = 'http://';
}
$urlInput->size = '50';
$table->addCell($urlInput->show(), NULL, NULL, NULL, NULL, 'colspan="3"');
$table->endRow();


/*
  // Iframe Attachments
  $iframe = new iframe ();
  $iframe->src = $this->uri(array('action'=>'tempframe', 'id'=>$temporaryId, 'mode'=>$mode));
  $iframe->width = 600;
  $iframe->height = 150;
  $iframe->extra = ' frameborder="0"';

  $objSelectFile = $this->getObject('selectfile', 'filemanager');

  $table->startRow();
  $table->addCell($this->objLanguage->languageText('word_attachments', 'calendarbase', 'Attachments').':');
  $table->addCell($objSelectFile->show(), NULL, NULL, NULL, NULL, 'colspan="3"');
  $table->endRow();
 */
;
$form = new form('eventform', $this->uri(array('action' => $action)));
$form->addToForm($table->show());

if ($mode == 'add')
{
    $gInput = new textinput('groupid');
    $gInput->value = $groupid;
    $gInput->fldType = 'hidden';
    $form->addToForm($gInput->show());
}
if ($mode == 'edit') {
    $idInput = new textinput('id');
    $idInput->value = $event['id'];
    $idInput->fldType = 'hidden';
    $form->addToForm($idInput->show());

    if ($event['multiday_event'] == 1) {
        $multidayOriginalInput = new textinput('multiday_event_original');
        $multidayOriginalInput->value = $event['multiday_event_start_id'];
        $multidayOriginalInput->fldType = 'hidden';
        $form->addToForm($multidayOriginalInput->show());
    }
}

$submitButton = new button('submitform', $this->objLanguage->languageText('mod_calendarbase_saveevent', 'calendarbase'));
$submitButton->setId('input_submitform');
$submitButton->setToSubmit();

$cancelButton = new button('cancel', $this->objLanguage->languageText('word_cancel'));
$returnUrl = $this->uri(array('month' => $month, 'year' => $year, 'events' => $currentList));
$cancelButton->setOnClick("window.location='$returnUrl'");

$form->addToForm(//'<p>'
$submitButton->show(). ' / ' . $cancelButton->show()
//        '<input value="Save Event" type="submit" name="submitform" id="input_submitform" class="button" />'
        
//.'</p>'
);

// Temporary Id
$temporaryId = new hiddeninput('temporary_id', $temporaryId);
$form->addToForm($temporaryId->show());

echo $form->show();
?>
