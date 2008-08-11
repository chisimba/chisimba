<script type="text/javascript" >
function updateChangeList(item)
{
    document.getElementById('changedItems').value += ','+item;
}
</script>
<?php

$this->loadClass('form', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('radio', 'htmlelements');


$this->appendArrayVar('headerParams', $this->getJavascriptFile('highlightLabels.js', 'htmlelements'));

$css = '<style type="text/css" title="text/css">
.checked img {
    background-color:yellow;border: 1px solid green;
    padding: 3px;
}
</style>';

$this->appendArrayVar('headerParams', $css);

$this->appendArrayVar('bodyOnLoad', 'setUpLabelHighlight();');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objContext->getMenuText().' - '.$this->objLanguage->code2Txt('phrase_searchforuserstoadd', 'contextgroups');

echo $header->show();

$table = $this->newObject('htmltable', 'htmlelements');
$table->cellpadding = 5;

$table->startRow();

 $searchLabel = new label ($this->objLanguage->languageText('mod_contextgroups_searchby', 'contextgroups').': ', 'input_field');
    $searchdropdown = new dropdown('field');
    $searchdropdown->addOption('firstName', $this->objLanguage->languageText('phrase_firstName'));
    $searchdropdown->addOption('surname', $this->objLanguage->languageText('word_surname'));
    $searchdropdown->addOption('userId', $this->objLanguage->languageText('word_userid'));
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
    $searchdropdown->setSelected($order);
    //$table->addCell($orderLabel->show().$searchdropdown->show());

            
     $label = new label ($this->objLanguage->languageText('mod_contextgroups_noofresults', 'contextgroups').': ', 'input_results');
    $dropdown = new dropdown('results');
    $dropdown->addOption('20', '20');
    $dropdown->addOption('30', '30');
    $dropdown->addOption('50', '50');
    $dropdown->addOption('75', '75');
    $dropdown->addOption('100', '100');
    //$dropdown->addOption('all', 'All Results');
    $dropdown->setSelected($numresults);
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

// --------------------------------

if (count($results) == 0) {
    echo '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_contextgroups_nousersfoundsearchcriteria', 'contextgroups', 'No Users found with search criteria').'</div>';
} else {
    
    $start = ($page-1)*$numresults+1;
    
    if ($page*$numresults > $countResults) {
        $end = $countResults;
    } else {
        $end = $page*$numresults;
    }
    
    $header = new htmlheading();
    $header->type = 1;
    
    // if ($numresults == 'all') {
        // $header->str = 'All Search Results ('.$countResults.') for: <em>'.$searchfor.'</em>';
    // } else {
    
    $string = $this->objLanguage->languageText('mod_contextgroups_searchresultsfor', 'contextgroups');
    $string = str_replace('[-START-]', $start, $string);
    $string = str_replace('[-END-]', $end, $string);
    $string = str_replace('[-TOTAL-]', $countResults, $string);
        $header->str = $string.': <em>'.$searchfor.'</em> ';
    //}
    
    echo $header->show();
    
    $table = $this->newObject('htmltable', 'htmlelements');
    $table->cellpadding = 5;
    
    $table->startHeaderRow();
        $table->addHeaderCell($this->objLanguage->languageText('word_userid'));
        $table->addHeaderCell($this->objLanguage->languageText('mod_security_staffstudentnumber', 'security', 'Staff/Student Number'));
        //$table->addHeaderCell('Title');
        $table->addHeaderCell($this->objLanguage->languageText('phrase_firstName'));
        $table->addHeaderCell($this->objLanguage->languageText('word_surname'));
        //$table->addHeaderCell('Sex');
        //$table->addHeaderCell('Email Address');
        $table->addHeaderCell($this->objLanguage->languageText('mod_contextgroups_addusertogroup', 'contextgroups'));
    $table->endHeaderRow();
    
    $objIcon = $this->getObject('geticon', 'htmlelements');
    $objIcon->setIcon('not_applicable');
    $objIcon->alt = $this->objLanguage->code2Txt('mod_contextgroups_notmemberofcontext', 'contextgroups');
    $objIcon->title = $this->objLanguage->code2Txt('mod_contextgroups_notmemberofcontext', 'contextgroups');
    $noneIcon = $objIcon->show();
    
    $objIcon->setIcon('lecturer');
    $objIcon->alt = $this->objLanguage->code2Txt('mod_contextgroups_makelecturer', 'contextgroups');
    $objIcon->title = $this->objLanguage->code2Txt('mod_contextgroups_makelecturer', 'contextgroups');
    $lecturerIcon = $objIcon->show();
    
    $objIcon->setIcon('student');
    $objIcon->alt = $this->objLanguage->code2Txt('mod_contextgroups_makestudent', 'contextgroups');
    $objIcon->title = $this->objLanguage->code2Txt('mod_contextgroups_makestudent', 'contextgroups');
    $studentIcon = $objIcon->show();
    
    $objIcon->setIcon('guest');
    $objIcon->alt = $this->objLanguage->code2Txt('mod_contextgroups_makeguest', 'contextgroups');
    $objIcon->title = $this->objLanguage->code2Txt('mod_contextgroups_makeguest', 'contextgroups');
    $guestIcon = $objIcon->show();
    
    foreach ($results as $result)
    {
        $table->row_attributes = 'onmouseover="this.className=\'tbl_ruler\';" onmouseout="this.className=\'none\'; "';
        $table->startRow();
        $table->addCell($result['userid']);
        $table->addCell($result['staffnumber']);
        //$table->addCell($result['title']);
        $table->addCell($result['firstname']);
        $table->addCell($result['surname']);
        // $table->addCell($result['sex']);
        // $table->addCell($result['emailAddress']);
        
        $radio = new radio ($result['userid']);
        
        
        
        $radio->addOption('none', $noneIcon.' &nbsp; &nbsp;');
        $radio->addOption('lecturer', $lecturerIcon);
        $radio->addOption('student', $studentIcon);
        $radio->addOption('guest', $guestIcon);
        
        // Default Set to None
        $radio->setSelected('none');
        
        $radio->extra = 'onclick="updateChangeList(\''.$result['userid'].'\');"';
        
        // Check if Guest
        if (in_array($result['userid'], $guests)) {
            $radio->setSelected('guest');
        }
        
        // Check if Student
        if (in_array($result['userid'], $students)) {
            $radio->setSelected('student');
        }
        
        // Check if Lecturer
        if (in_array($result['userid'], $lecturers)) {
            $radio->setSelected('lecturer');
        }
        
        $table->addCell($radio->show());
        $table->endRow();
    }
    
    $addUsersForm = new form ('addusers', $this->uri(array('action'=>'addusers')));
    
    $button = new button ('submitform', $this->objLanguage->languageText('mod_contextgroups_updateuserroles', 'contextgroups'));
    $button->setToSubmit();
    $button->extra = 'style="margin-right: 50px;"';
    
    $hiddenInput = new hiddeninput('context', $contextCode);
    $addUsersForm->addToForm($hiddenInput->show());
    
    $hiddenInput = new hiddeninput('changedItems', '');
    // $hiddenInput->cssId = 'changedItems';
    $hiddenInput->extra = 'id="changedItems"';
    $addUsersForm->addToForm($hiddenInput->show());
    
    $addUsersForm->addToForm($table->show());
    $addUsersForm->addToForm('<p align="right">'.$button->show().'</p>');
    
    echo $addUsersForm->show();
    
    echo '<p>'.$this->objLanguage->languageText('mod_contextgroups_browseresults', 'contextgroups', 'Browse Results').': '.$paging.'</p>';

}

$returnLink = new link ($this->uri(NULL));
$returnLink->link = ucwords($this->objLanguage->code2Txt('phrase_returntocontextgroups', 'contextgroups'));

echo '<p align="center">'.$returnLink->show().'</p>';
?>