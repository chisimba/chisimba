<script language="JavaScript" type="text/javascript" >

 jQuery(document).ready(function(){
        setupCalendarCheckbox('userbox', 'event_user');
        setupCalendarCheckbox('contextbox', 'event_context');
        setupCalendarCheckbox('otherbox', 'event_othercontext');
        setupCalendarCheckbox('sitebox', 'event_site');
    });


function setupCalendarCheckbox(checkId, itemClass)
{
    jQuery("#"+checkId).livequery('click', function() {
            jQuery("."+itemClass).toggle();
        });
}
</script>


<?php
$ret = "";
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadclass('checkbox','htmlelements');

$this->appendArrayVar('headerParams', $this->getJavaScriptFile('jquery.livequery.js', 'jquery'));


$message = $this->getParam('message');
if (isset($message)) {
    switch ($message)
    {
        case 'eventadded' : $text = $this->objLanguage->languageText('mod_calendarbase_eventaddconfirm', 'calendarbase'); break;
        case 'eventupdated' : $text = $this->objLanguage->languageText('mod_calendarbase_eventeditconfirm', 'calendarbase'); break;
        case 'eventdeleted' : $text = $this->objLanguage->languageText('mod_calendarbase_eventdeleteconfirm', 'calendarbase'); break;

        default : $text = '';
    }

    if ($text != '') {
        $timeOutMessage =& $this->getObject('timeoutmessage', 'htmlelements');
        $timeOutMessage->setMessage($text);
        $timeOutMessage->setHideTypeToHidden();
    }
}

$addIcon = $this->getObject('geticon', 'htmlelements');
$addIcon->setIcon('add');
$addIcon->title = $this->objLanguage->languageText('mod_calendarbase_addevent', 'calendarbase');

$addEventLink = new link($this->uri(array('action' => 'add', 'month'=>$month, 'year'=>$year)));
$addEventLink->link = $addIcon->show();

$ical = new link($this->uri(array('action' => 'icalexport')));
$ical->link = '<img src="'. $this->getResourceUri('icalCalendar.png', 'calendar') .'" >';


$title = $this->objLanguage->languageText('mod_calendarbase_someonescalendar', 'calendarbase');

$heading = new htmlheading();
$heading->str = str_replace('[someone]', $fullname, $title).' '.$addEventLink->show().'  '.$ical->show();
$heading->type = 1;

$ret .= $heading->show()
        ;
$checkboxes = array();
$checkbox = new checkbox('userbox', NULL, TRUE);
$checkbox->cssId='userbox';
$label = new label($this->objLanguage->languageText('mod_calendar_personalevents','calendar').' ('.$userEvents.')', 'userbox');
$checkboxes[] = $checkbox->show().' '.$label->show();
if ($this->contextCode == 'root') {
    $checkbox = new checkbox('otherbox', NULL, TRUE);
    $checkbox->cssId='otherbox';
    $str=$this->objLanguage->code2Txt('mod_calendar_mycourses', 'calendar', NULL, 'My [-context-]');
    $label = new label($str.' ('.$otherContextEvents.')', 'otherbox');
    $checkboxes[] = $checkbox->show().' '.$label->show();
} else {
    $checkbox = new checkbox('contextbox', NULL, TRUE);
    $checkbox->cssId='contextbox';
    $str=$this->objLanguage->code2Txt('mod_calendar_currentcourses', 'calendar', NULL, 'Current [-contexts-]');
    $label = new label($str.' '.$this->contextTitle.' ('.$contextEvents.')', 'contextbox');
    $checkboxes[] = $checkbox->show().' '.$label->show();
    $checkbox = new checkbox('otherbox', NULL, TRUE);
    $checkbox->cssId='otherbox';
    $str=$this->objLanguage->code2Txt('mod_calendar_othercourses', 'calendar', NULL, 'Other [-contexts-]');
    $label = new label($str.' ('.$otherContextEvents.')', 'otherbox');
    $checkboxes[] = $checkbox->show().' '.$label->show();
}
$checkbox = new checkbox('sitebox', NULL, TRUE);
$checkbox->cssId='sitebox';
$label = new label($this->objLanguage->languageText('mod_calendar_siteevents','calendar').' ('.$siteEvents.')', 'sitebox');
$checkboxes[] = $checkbox->show().$label->show();
$divider = '';
foreach ($checkboxes as $option)
{
    $ret .= $divider.$option;
    $divider = ' &nbsp; &nbsp; &nbsp; ';
}
$ret .= $calendarNavigation.$eventsCalendar;
$ret .= $eventsList;
$addEventLink = new link($this->uri(array('action' => 'add','groupid'=>$groupid)));
$addEventLink->link = $this->objLanguage->languageText('mod_calendarbase_addevent', 'calendarbase');
$ret .= '<p class="calendar_addlink">' . $addEventLink->show() . '</p>';

echo "<div id='widecalendar'>$ret</div>";
?>
