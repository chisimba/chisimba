<?php
$headerParams=$this->getJavascriptFile('new_sorttable.js','htmlelements');
$this->appendArrayVar('headerParams',$headerParams);
$headerParams=$this->getJavascriptFile('selectall.js','htmlelements');
$this->appendArrayVar('headerParams',$headerParams);

$this->loadClass('form', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$objTabbedbox=&$this->newObject('tabbedbox','htmlelements');

$objIcon = $this->getObject('geticon', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objContext->getTitle();

if ($this->isValid('addusers')) {
    $objIcon->setIcon('add');

    $link = new link($this->uri(array('action'=>'viewsearchresults')));
    $link->link = $objIcon->show();
    
    $header->str .= ' '.$link->show();
}

echo $header->show();

// Lecturers

$header = new htmlheading();
$header->type = 3;
$header->str = ucwords($this->objLanguage->code2Txt('word_lecturers', 'system', NULL, '[-authors-]'));
//$header->align = 'center';

//echo $header->show();

$objTable = $this->newObject('htmltable', 'htmlelements');
$objTable->cellpadding = 5;
$objTable->cellspacing = 1;
$objTable->id="lecturerTable";
$objTable->css_class="sorttable";

$objTable->startRow();
if ($this->isValid('removeallusers') && count($lecturerDetails) > 0) {
    $objTable->addCell('', 30, '', '', 'heading', '');
}
$objTable->addCell($this->objLanguage->languageText('word_userid'), 100, '', '', 'heading', '');
$objTable->addCell($this->objLanguage->languageText('mod_security_staffstudentnumber', 'security', 'Staff/Student Number'), 100, '', '', 'heading', '');
$objTable->addCell($this->objLanguage->languageText('word_title'), 30, '', '', 'heading', '');
$objTable->addCell($this->objLanguage->languageText('phrase_firstName'), '20%', '', '', 'heading', '');
$objTable->addCell($this->objLanguage->languageText('word_surname'), '20%', '', '', 'heading', '');
$objTable->addCell($this->objLanguage->languageText('phrase_emailaddress'), '', '', '', 'heading', '');
if ($this->isValid('removeuser') && count($lecturerDetails) > 0) {
    $objTable->addCell('', 30, '', '', 'heading', '');
}
$objTable->endRow();

if (count($lecturerDetails) > 0) {
    
    $objTable->row_attributes='onmouseover="this.className=\'tbl_ruler\';" onmouseout="this.className=\'none\'; "';
    
    foreach ($lecturerDetails as $lecturer)
    {
        $objCheck=new checkbox('lecturerId[]');
        $objCheck->value=$lecturer['userid'];

        $objTable->startRow();
        if($this->isValid('removeallusers')){
            if($lecturer['userid'] != $this->userId){
                $objTable->addCell($objCheck->show());		
            }else{
                $objTable->addCell('');
            }
        }
        $objTable->addCell($lecturer['userid']);
        $objTable->addCell($lecturer['staffnumber']);
        $objTable->addCell($lecturer['title']);
        $objTable->addCell($lecturer['firstname']);
        $objTable->addCell($lecturer['surname']);
        $objTable->addCell($lecturer['emailaddress']);
        if ($this->isValid('removeuser')) {
            $string = str_replace(
                '[-user-]', 
                ucwords($this->objLanguage->code2txt('word_lecturer', 'system', NULL, '[-author-]')), 
                $this->objLanguage->languageText('mod_contextgroups_confirmdeleteuser', 'contextgroups')
            );
            
            $deleteicon = $objIcon->getDeleteIconWithConfirm(NULL, array('action'=>'removeuser', 'userid'=>$lecturer['userid'], 'group'=>'Lecturers'), 'contextgroups', $string);
            
            $objTable->addCell($deleteicon);
        }
        $objTable->endRow();
    }
} else {
    $objTable->startRow();
        $objTable->addCell($this->objLanguage->code2txt('mod_contextgroups_nolecturers', 'contextgroups'), NULL, NULL, NULL, 'noRecordsMessage', 'colspan="6"');
    $objTable->endRow();
}



$objButton = new button('select', $this->objLanguage->languageText('phrase_selectall'));
$objButton->extra='onclick="javascript:SetAllCheckBoxes(\'removelecturers\', \'lecturerId[]\', true)"';
$buttons = $objButton->show();

$objButton = new button('unselect', $this->objLanguage->languageText('mod_contextgroups_unselectall', 'contextgroups', 'Unselect All'));
$objButton->extra='onclick="javascript:SetAllCheckBoxes(\'removelecturers\', \'lecturerId[]\', false)"';
$buttons .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$objButton->show();

$objButton = new button('delete', $this->objLanguage->languageText('mod_contextgroups_deleteselected', 'contextgroups', 'Delete Selected'));
$objButton->extra='onclick="javascript:if(confirm(\''.$this->objLanguage->code2Txt('mod_contextgroups_confirmlecturer', 'contextgroups', NULL, 'Are you sure you want to delete these [-authors-]?').'\')){document.removelecturers.submit();}else{return false;}"';
$buttons .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$objButton->show();

$objForm = new form('removelecturers', $this->uri(array('action'=>'removeallusers', 'mode'=>'lecturer')));
$objForm->addToForm($objTable->show());
if (count($lecturerDetails) > 1) {
    $objForm->addToForm($buttons);
}
//echo $objForm->show();


$objTabbedbox=new tabbedbox();
$objTabbedbox->addTabLabel("<b>".ucfirst(strtolower($this->objLanguage->code2Txt('word_lecturers', 'system', NULL, '[-authors-]')))."</b>");
if ($this->isValid('removeallusers')) {
    $objTabbedbox->addBoxContent($objForm->show());
} else {
    $objTabbedbox->addBoxContent($objTable->show());
}
echo $objTabbedbox->show();





// Students

$header = new htmlheading();
$header->type = 3;
$header->str = ucwords($this->objLanguage->code2Txt('word_students', 'system', NULL, '[-readonlys-]'));
//$header->align = 'center';

//echo $header->show();

$objTable = $this->newObject('htmltable', 'htmlelements');
$objTable->cellpadding = 5;
$objTable->cellspacing = 1;
$objTable->id="studentTable";
$objTable->css_class="sorttable";
$objTable->startRow();
if ($this->isValid('removeallusers') && count($studentDetails) > 0) {
    $objTable->addCell('', 30, '', '', 'heading', '');
}
$objTable->addCell($this->objLanguage->languageText('word_userid'), 100, '', '', 'heading', '');
$objTable->addCell($this->objLanguage->languageText('mod_security_staffstudentnumber', 'security', 'Staff/Student Number'), 100, '', '', 'heading', '');
$objTable->addCell($this->objLanguage->languageText('word_title'), 30, '', '', 'heading', '');
$objTable->addCell($this->objLanguage->languageText('phrase_firstName'), '20%', '', '', 'heading', '');
$objTable->addCell($this->objLanguage->languageText('word_surname'), '20%', '', '', 'heading', '');
$objTable->addCell($this->objLanguage->languageText('phrase_emailaddress'), '', '', '', 'heading', '');
if ($this->isValid('removeuser') && count($studentDetails) > 0) {
    $objTable->addCell('', 30, '', '', 'heading', '');
}
$objTable->endRow();

if (count($studentDetails) > 0) {
    
    $objTable->row_attributes='onmouseover="this.className=\'tbl_ruler\';" onmouseout="this.className=\'none\'; "';
    
    foreach ($studentDetails as $student)
    {
        $objCheck=new checkbox('studentId[]');
        $objCheck->value=$student['userid'];

        $objTable->startRow();
        if($this->isValid('removeallusers')){
            if($student['userid'] != $this->userId){
                $objTable->addCell($objCheck->show());		
            }else{
                $objTable->addCell('');
            }
        }
        $objTable->addCell($student['userid']);
        $objTable->addCell($student['staffnumber']);
        $objTable->addCell($student['title']);
        $objTable->addCell($student['firstname']);
        $objTable->addCell($student['surname']);
        $objTable->addCell($student['emailaddress']);
        if ($this->isValid('removeuser')) {
            $string = str_replace(
                '[-user-]', 
                ucwords($this->objLanguage->code2txt('word_student', 'system', NULL, '[-readonly-]')), 
                $this->objLanguage->languageText('mod_contextgroups_confirmdeleteuser', 'contextgroups')
            );
            $deleteicon = $objIcon->getDeleteIconWithConfirm(NULL, array('action'=>'removeuser', 'userid'=>$student['userid'], 'group'=>'Students'), 'contextgroups', $string);
            
            $objTable->addCell($deleteicon);
        }
        $objTable->endRow();
    }
} else {
    $objTable->startRow();
        $objTable->addCell($this->objLanguage->code2txt('mod_contextgroups_nostudents', 'contextgroups'), NULL, NULL, NULL, 'noRecordsMessage', 'colspan="6"');
    $objTable->endRow();
}

$objButton = new button('select', $this->objLanguage->languageText('phrase_selectall'));
$objButton->extra='onclick="javascript:SetAllCheckBoxes(\'removestudents\', \'studentId[]\', true)"';
$buttons = $objButton->show();

$objButton = new button('unselect', $this->objLanguage->languageText('mod_contextgroups_unselectall', 'contextgroups', 'Unselect All'));
$objButton->extra='onclick="javascript:SetAllCheckBoxes(\'removestudents\', \'studentId[]\', false)"';
$buttons .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$objButton->show();

$objButton = new button('delete', $this->objLanguage->languageText('mod_contextgroups_deleteselected', 'contextgroups', 'Delete Selected'));
$objButton->extra='onclick="javascript:if(confirm(\''.$this->objLanguage->code2Txt('mod_contextgroups_confirmstudent', 'contextgroups', NULL, 'Are you sure you want to delete these [-readonlys-]?').'\')){document.removestudents.submit();}else{return false;}"';
$buttons .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$objButton->show();

$objForm = new form('removestudents', $this->uri(array('action'=>'removeallusers', 'mode'=>'student')));
$objForm->addToForm($objTable->show());
if (count($studentDetails) > 0) {
    $objForm->addToForm($buttons);
}
//echo $objForm->show();


$objTabbedbox=new tabbedbox();
$objTabbedbox->addTabLabel("<b>".ucfirst(strtolower($this->objLanguage->code2Txt('word_students', 'system', NULL, '[-readonlys-]')))."</b>");
if ($this->isValid('removeallusers')) {
    $objTabbedbox->addBoxContent($objForm->show());
} else {
    $objTabbedbox->addBoxContent($objTable->show());
}
echo $objTabbedbox->show();



// Guests

$header = new htmlheading();
$header->type = 3;
$header->str = ucwords($this->objLanguage->languageText('mod_contextadmin_guests', 'contextadmin', 'Guests'));
//$header->align = 'center';

//echo $header->show();

$objTable = $this->newObject('htmltable', 'htmlelements');
$objTable->cellpadding = 5;
$objTable->cellspacing = 1;
$objTable->id="guestsTable";
$objTable->css_class="sorttable";

$objTable->startRow();
if ($this->isValid('removeallusers') && count($guestDetails) > 0) {
    $objTable->addCell('', 30, '', '', 'heading', '');
}
$objTable->addCell($this->objLanguage->languageText('word_userid'), 100, '', '', 'heading', '');
$objTable->addCell($this->objLanguage->languageText('mod_security_staffstudentnumber', 'security', 'Staff/Student Number'), 100, '', '', 'heading', '');
$objTable->addCell($this->objLanguage->languageText('word_title'), 30, '', '', 'heading', '');
$objTable->addCell($this->objLanguage->languageText('phrase_firstName'), '20%', '', '', 'heading', '');
$objTable->addCell($this->objLanguage->languageText('word_surname'), '20%', '', '', 'heading', '');
$objTable->addCell($this->objLanguage->languageText('phrase_emailaddress'), '', '', '', 'heading', '');
if ($this->isValid('removeuser') && count($guestDetails) > 0) {
    $objTable->addCell('', 30, '', '', 'heading', '');
}
$objTable->endRow();

if (count($guestDetails) > 0) {
    
    $objTable->row_attributes='onmouseover="this.className=\'tbl_ruler\';" onmouseout="this.className=\'none\'; "';
    
    foreach ($guestDetails as $guest)
    {
        $objCheck=new checkbox('guestId[]');
        $objCheck->value=$guest['userid'];

        $objTable->startRow();
        if($this->isValid('removeallusers')){
            if($guest['userid'] != $this->userId){
                $objTable->addCell($objCheck->show());		
            }else{
                $objTable->addCell('');
            }
        }
        $objTable->addCell($guest['userid']);
        $objTable->addCell($guest['staffnumber']);
        $objTable->addCell($guest['title']);
        $objTable->addCell($guest['firstname']);
        $objTable->addCell($guest['surname']);
        $objTable->addCell($guest['emailaddress']);
        if ($this->isValid('removeuser')) {
            $string = str_replace(
                '[-user-]', 
                ucwords($this->objLanguage->languageText('word_guest', 'system', 'guest')), 
                $this->objLanguage->languageText('mod_contextgroups_confirmdeleteuser', 'contextgroups')
            );
            $deleteicon = $objIcon->getDeleteIconWithConfirm(NULL, array('action'=>'removeuser', 'userid'=>$guest['userid'], 'group'=>'Guest'), 'contextgroups', $string);
            
            $objTable->addCell($deleteicon);
        }
        $objTable->endRow();
    }
} else {
    $objTable->startRow();
        $objTable->addCell($this->objLanguage->code2txt('mod_contextgroups_noguests', 'contextgroups'), NULL, NULL, NULL, 'noRecordsMessage', 'colspan="6"');
    $objTable->endRow();
}

$objButton = new button('select', $this->objLanguage->languageText('phrase_selectall'));
$objButton->extra='onclick="javascript:SetAllCheckBoxes(\'removeguests\', \'guestId[]\', true)"';
$buttons = $objButton->show();

$objButton = new button('unselect', $this->objLanguage->languageText('mod_contextgroups_unselectall', 'contextgroups', 'Unselect All'));
$objButton->extra='onclick="javascript:SetAllCheckBoxes(\'removeguests\', \'guestId[]\', false)"';
$buttons .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$objButton->show();

$objButton = new button('delete', $this->objLanguage->languageText('mod_contextgroups_deleteselected', 'contextgroups', 'Delete Selected'));
$objButton->extra='onclick="javascript:if(confirm(\''.$this->objLanguage->languageText('mod_contextgroups_confirmguest', 'contextgroups', 'Are you sure you want to delete these guests?').'\')){document.removeguests.submit();}else{return false;}"';
$buttons .= '&nbsp;&nbsp;&nbsp;&nbsp;'.$objButton->show();

$objForm = new form('removeguests', $this->uri(array('action'=>'removeallusers', 'mode'=>'guest')));
$objForm->addToForm($objTable->show());
if (count($guestDetails) > 0) {
    $objForm->addToForm($buttons);
}
//echo $objForm->show();

$objTabbedbox=new tabbedbox();
$objTabbedbox->addTabLabel("<b>".ucfirst(strtolower($this->objLanguage->languageText('mod_contextadmin_guests', 'contextadmin', 'Guests')))."</b>");
if ($this->isValid('removeallusers')) {
    $objTabbedbox->addBoxContent($objForm->show());
} else {
    $objTabbedbox->addBoxContent($objTable->show());
}
echo $objTabbedbox->show();


// echo '<pre>';
// print_r($studentDetails);
// echo '<pre>';

if ($this->isValid('addusers')) {

    $header = new htmlheading();
    $header->type = 3;
    $header->str = $this->objLanguage->code2Txt('phrase_searchforuserstoadd', 'contextgroups');

    echo $header->show();

    $table = $this->getObject('htmltable', 'htmlelements');
    $table->cellpadding = 5;

    $table->startRow();

    $searchLabel = new label ($this->objLanguage->languageText('mod_contextgroups_searchby', 'contextgroups').': ', 'input_field');
    $searchdropdown = new dropdown('field');
    $searchdropdown->addOption('firstname', $this->objLanguage->languageText('phrase_firstName'));
    $searchdropdown->addOption('surname', $this->objLanguage->languageText('word_surname'));
    $searchdropdown->addOption('userid', $this->objLanguage->languageText('word_userid'));
    $searchdropdown->setSelected($field);
   //$table->addCell();


    $label = new label ($this->objLanguage->languageText('mod_contextgroups_startswith', 'contextgroups').': ', 'input_search');
    $input = new textinput ('search');
    $input->value = htmlentities(stripslashes($searchfor));
    $input->size = 20;
    $table->addCell($searchLabel->show().$searchdropdown->show()."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$label->show().$input->show());

   

    $table->endRow();

    $table->startRow();
    
        //Ehb-added-begin
            $label=new label($this->objLanguage->languageText('mod_contextgroups_choosecourse', 'contextgroups'),'input_course');		
            $courseDropdown=new dropdown('course');
            $courseDropdown->addOption('all',$this->objLanguage->languageText('mod_contextgroups_allcourses', 'contextgroups'));
            for($i=0; $i<count($data); $i++){
          $courseDropdown->addOption($data[$i]['contextcode'], $data[$i]['title']);
        }
        $courseDropdown->setSelected($course);
        
            $label2=new label($this->objLanguage->languageText('mod_contextgroups_choosegroup', 'contextgroups'),'input_group');		
            $groupDropdown=new dropdown('group');
            $groupDropdown->addOption('all','All groups');
            $groups=array("Lecturers","Students","Guest");
        
                for($i=0; $i<count($groups); $i++){
          $groupDropdown->addOption($groups[$i],$groups[$i]);
        }
        
        $groupDropdown->setSelected($group);
        
        $table->addCell($label2->show().$groupDropdown->show()."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$label->show().$courseDropdown->show());
        $table->endRow();
   //Ehb-added-End

            $table->startRow();
            
        $orderLabel = new label ($this->objLanguage->languageText('mod_contextgroups_orderresultsby', 'contextgroups').': ', 'input_order');
    $searchdropdown->name = 'order';
    $searchdropdown->cssId = 'input_order';
    //$table->addCell($orderLabel->show().$searchdropdown->show());

            
     $label = new label ($this->objLanguage->languageText('mod_contextgroups_noofresults', 'contextgroups').': ', 'input_results');
    $dropdown = new dropdown('results');
    $dropdown->addOption('20', '20');
    $dropdown->addOption('30', '30');
    $dropdown->addOption('50', '50');
    $dropdown->addOption('75', '75');
    $dropdown->addOption('100', '100');
    //$dropdown->addOption('all', 'All Results');
    $table->addCell($orderLabel->show().$searchdropdown->show()."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$label->show().$dropdown->show());
        $table->endRow();

    $button = new button ('searchbutton');
    $button->value = $this->objLanguage->languageText('word_search');
    $button->setToSubmit();
    $table->addCell ($button->show());

    $table->addCell('&nbsp;');
    $table->endRow();

    $form = new form ('searchforusers', $this->uri(array('action'=>'searchforusers')));
    $form->addToForm($table->show());
    echo $form->show();
}


?>