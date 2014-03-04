<?php
//Calendar template
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadclass('checkbox','htmlelements');

$heading = new htmlheading();
$heading->str = str_replace('[someone]', $fullname, $title);
$heading->type = 3;

echo $heading->show();
//Navigation
$previous = $this->objDate->previousMonthYear($month, $year);
$prevLink = new link ($this->uri(array('action' => 'calendar', 'month'=>$previous['month'], 'year'=>$previous['year'])));
$prevLink->link = $this->objDate->monthFull($previous['month']).' '.$previous['year'];

$next = $this->objDate->nextMonthYear($month, $year);
$nextLink = new link ($this->uri(array('action' => 'calendar', 'month'=>$next['month'], 'year'=>$next['year'])));
$nextLink->link = $this->objDate->monthFull($next['month']).' '.$next['year'];

$this->loadClass('htmlheading', 'htmlelements');
$header = new htmlheading();
$header->str = $this->objDate->monthFull($month).' '.$year;
//nextMonthYear
$table = $this->newObject('htmltable', 'htmlelements');
$table->startRow();
$table->addCell($prevLink->show(), '20%', NULL, 'left');
$table->addCell($this->objDate->monthFull($month).' '.$year, '30%', NULL, 'center', 'bigDayNum');
$table->addCell($nextLink->show(), '20%', NULL, 'right');
$table->endRow();
//Navigation

echo $table->show().'<br>';
echo $eventsCalendar.'<br>';
echo $eventsList;
$addEventLink = new link($this->uri(array('action' => 'addevent')));
$addEventLink->link = 'Add event';

//echo '<p>'.$addEventLink->show().'</p>';
echo $this->homeAndBackLink.'</br>';
?>
