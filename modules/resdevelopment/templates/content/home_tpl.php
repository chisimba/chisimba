<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
$this->loadclass('link','htmlelements');

// to create a link for adding students
$studentImportUrl = new link();
$studentImportUrl->link($this->uri(array('action'=>'addstudent')));
$studentImportUrl->link = "Add Students";
echo $studentImportUrl->show()."<br>";

// to create a link adding groups
$groupImportUrl = new link();
$groupImportUrl->link($this->uri(array('action'=>'addgroup')));
$groupImportUrl->link = "Add Group";
echo $groupImportUrl->show()."<br>";

// to create a link for Capturing attendance
$captureAttendanceImportUrl = new link();
$captureAttendanceImportUrl->link($this->uri(array('action'=>'captureAttendance')));
$captureAttendanceImportUrl->link = "Add Attendance";
echo $captureAttendanceImportUrl->show();



?>
