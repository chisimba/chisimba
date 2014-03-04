<?php
$headerParams = $this->getJavascriptFile('new_sorttable.js', 'htmlelements');
$this->appendArrayVar('headerParams', $headerParams);
$headerParams = $this->getJavascriptFile('selectall.js', 'htmlelements');
$this->appendArrayVar('headerParams', $headerParams);
$this->loadClass('form', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$objTabbedbox = &$this->newObject('tabbedbox', 'htmlelements');
$objIcon = $this->getObject('geticon', 'htmlelements');
if (isset($showconfirmation) && $showconfirmation) {
    echo '<div id="confirmationmessage">';
    if ($this->getParam('message') == 'userdeletedfromgroup') {
        echo '<ul><li><span class="confirm">' . $this->objLanguage->languageText('phrase_eportfolio_membersuccessfullyremoved', 'eportfolio') . '</span></li>';
        echo '</ul>';
    }
    if ($this->getParam('message') == 'usersdeletedfromgroup') {
        echo '<ul><li><span class="confirm">' . $this->objLanguage->languageText('phrase_eportfolio_memberssuccessfullyremoved', 'eportfolio') . '</span></li>';
        echo '</ul>';
    }
    if ($this->getParam('message') == 'nouseridprovidedfordelete') {
        echo '<ul><li><span class="confirm">' . $this->objLanguage->languageText('phrase_eportfolio_nouseridprovidedfordelete', 'eportfolio') . '</span></li>';
        echo '</ul>';
    }
    if ($this->getParam('message') == 'nouseridsprovidedfordelete') {
        echo '<ul><li><span class="confirm">' . $this->objLanguage->languageText('phrase_eportfolio_nouseridsprovidedfordelete', 'eportfolio') . '</span></li>';
        echo '</ul>';
    }
    if ($this->getParam('message') == 'usersupdated') {
        echo '<ul><li><span class="confirm">' . $this->objLanguage->languageText('phrase_eportfolio_usersupdated', 'eportfolio') . '</span></li>';
        echo '</ul>';
    }
    echo '</div>';
    echo '
    <script type="text/javascript">

    function hideConfirmation()
    {
        document.getElementById(\'confirmationmessage\').style.display="none";
    }

    setTimeout("hideConfirmation()", 10000);
    </script>
    ';
}
//Get Group Name
$groupName = $this->_objGroupAdmin->getName($group);
$groupName = explode("^", $groupName);
$groupName = $groupName[1];
$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_eportfolio_wordManage', "eportfolio") . " " . $groupName . " " . $this->objLanguage->languageText('mod_eportfolio_wordGroup', "eportfolio");
if ($this->isValid('addusers')) {
    $objIcon->setIcon('add');
    $link = new link($this->uri(array(
        'action' => 'viewsearchresults'
    )));
    $link->link = $objIcon->show();
    $header->str.= ' ' . $link->show();
}
echo $header->show();
// Group Member
// Prepare Group Members List
$filter = " ORDER BY surname ";
$grpMembers = $this->_objGroupAdmin->getGroupUsers($group, array(
    'userid',
    'firstName',
    'surname',
    'title',
    'emailAddress',
    'country',
    'sex',
    'staffnumber'
) , $filter);
$grpmembersArray = array();
if (count($grpMembers) > 0) {
    foreach($grpMembers as $grpMember) {
        $grpmembersArray[] = $grpMember['userid'];
    }
}
$header = new htmlheading();
$header->type = 3;
$header->str = $groupName;
//$header->align = 'center';
//echo $header->show();
$objTable = $this->newObject('htmltable', 'htmlelements');
$objTable->cellpadding = 5;
$objTable->cellspacing = 1;
$objTable->id = "lecturerTable";
$objTable->css_class = "sorttable";
$objTable->startRow();
if ($this->isValid('removeallusers') && count($grpMembers) > 0) {
    $objTable->addCell('', 30, '', '', 'heading', '');
}
$objTable->addCell("<i>" . $this->objLanguage->languageText('word_userid') . "</i>", 100, 'bottom', '', 'heading', '');
$objTable->addCell("<i>" . $this->objLanguage->languageText('mod_security_staffstudentnumber', 'security', 'Staff/Student Number') . "</i>", 100, 'bottom', '', 'heading', '');
$objTable->addCell("<i>" . $this->objLanguage->languageText('word_title') . "</i>", 30, 'bottom', '', 'heading', '');
$objTable->addCell("<i>" . $this->objLanguage->languageText('phrase_firstname') . "</i>", '20%', 'bottom', '', 'heading', '');
$objTable->addCell("<i>" . $this->objLanguage->languageText('word_surname') . "</i>", '20%', 'bottom', '', 'heading', '');
$objTable->addCell("<i>" . $this->objLanguage->languageText('phrase_emailaddress') . "</i>", '', 'bottom', '', 'heading', '');
if ($this->isValid('removeuser') && count($grpMembers) > 0) {
    $objTable->addCell('', 30, '', '', 'heading', '');
}
$objTable->endRow();
if (count($grpMembers) > 0) {
    $objTable->row_attributes = 'onmouseover="this.className=\'tbl_ruler\';" onmouseout="this.className=\'none\'; "';
    foreach($grpMembers as $grpMember) {
        $objCheck = new checkbox('userId[]');
        $objCheck->value = $grpMember['userid'];
        $objTable->startRow();
        if ($this->isValid('removeallusers')) {
            if ($grpMember['userid'] != $this->userId) {
                $objTable->addCell($objCheck->show());
            } else {
                $objTable->addCell('');
            }
        }
        $objTable->addCell($grpMember['userid']);
        $objTable->addCell($grpMember['staffnumber']);
        $objTable->addCell($grpMember['title']);
        $objTable->addCell($grpMember['firstname']);
        $objTable->addCell($grpMember['surname']);
        $objTable->addCell($grpMember['emailaddress']);
        if ($this->isValid('removeuser')) {
            $string = str_replace('[-user-]', ucwords($this->objLanguage->languageText('word_lecturer', 'system', NULL, '[-author-]')) , $this->objLanguage->languageText('phrase_eportfolio_confirmdeleteuser', 'eportfolio'));
            //    public function getDeleteIconWithConfirm($id, $deleteArray=NULL, $callingModule=NULL, $deletephrase='phrase_confirmdelete')
            $deleteicon = $objIcon->getDeleteIconWithConfirm(NULL, array(
                'action' => 'removeuser',
                'userid' => $grpMember['userid'],
                'group' => $group
            ) , 'eportfolio', $string);
            $objTable->addCell($deleteicon);
        }
        $objTable->endRow();
    }
} else {
    $objTable->startRow();
    $objTable->addCell($this->objLanguage->languageText('mod_eportfolio_norecords', 'eportfolio') , NULL, NULL, NULL, 'noRecordsMessage', 'colspan="6"');
    $objTable->endRow();
}
$objButton = new button('select', $this->objLanguage->languageText('phrase_selectall'));
$objButton->extra = 'onclick="javascript:SetAllCheckBoxes(\'removeusers\', \'userId[]\', true)"';
$buttons = $objButton->show();
$objButton = new button('unselect', $this->objLanguage->languageText('mod_contextgroups_unselectall', 'contextgroups', 'Unselect All'));
$objButton->extra = 'onclick="javascript:SetAllCheckBoxes(\'removeusers\', \'userId[]\', false)"';
$buttons.= '&nbsp;&nbsp;&nbsp;&nbsp;' . $objButton->show();
$objButton = new button('delete', $this->objLanguage->languageText('mod_contextgroups_deleteselected', 'eportfolio', 'Delete Selected'));
$objButton->extra = 'onclick="javascript:if(confirm(\'' . $this->objLanguage->languageText('phrase_eportfolio_confirmdeleteusers', 'eportfolio', NULL, 'Are you sure you want to delete these [-authors-]?') . '\')){document.removeusers.submit();}else{return false;}"';
$buttons.= '&nbsp;&nbsp;&nbsp;&nbsp;' . $objButton->show();
$objForm = new form('removeusers', $this->uri(array(
    'action' => 'removeallusers',
    'group' => $group
    //'mode' => 'lecturer'
)));
$objForm->addToForm($objTable->show());
if (count($grpMembers) > 0) {
    $objForm->addToForm($buttons);
}
//echo $objForm->show();
$objTabbedbox = new tabbedbox();
$objTabbedbox->addTabLabel("<b>" . $groupName . "</b>");
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
    $header->str = $this->objLanguage->languageText('phrase_searchforuserstoadd', 'eportfolio');
    echo $header->show();
    $table = $this->getObject('htmltable', 'htmlelements');
    $table->cellpadding = 5;
    $table->startRow();
    $searchLabel = new label($this->objLanguage->languageText('mod_contextgroups_searchby', 'contextgroups') . ': ', 'input_field');
    $searchdropdown = new dropdown('field');
    $searchdropdown->addOption('firstname', $this->objLanguage->languageText('phrase_firstname'));
    $searchdropdown->addOption('surname', $this->objLanguage->languageText('word_surname'));
    $searchdropdown->addOption('userid', $this->objLanguage->languageText('word_userid'));
    $searchdropdown->setSelected($field);
    //$table->addCell();
    $label = new label($this->objLanguage->languageText('mod_contextgroups_startswith', 'contextgroups') . ': ', 'input_search');
    $input = new textinput('search');
    $input->value = htmlentities(stripslashes($searchfor));
    $input->size = 20;
    $table->addCell($searchLabel->show() . $searchdropdown->show() . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $label->show() . $input->show());
    $table->endRow();
    $table->startRow();
    //Ehb-added-begin
    $label = new label($this->objLanguage->languageText('mod_contextgroups_choosecourse', 'contextgroups') , 'input_course');
    $courseDropdown = new dropdown('course');
    $courseDropdown->addOption('all', $this->objLanguage->languageText('mod_contextgroups_allcourses', 'contextgroups'));
    for ($i = 0; $i < count($data); $i++) {
        $courseDropdown->addOption($data[$i]['contextcode'], $data[$i]['title']);
    }
    $courseDropdown->setSelected($course);
    $label2 = new label($this->objLanguage->languageText('mod_contextgroups_choosegroup', 'contextgroups') , 'input_group');
    $groupDropdown = new dropdown('group');
    $groupDropdown->addOption('all', 'All groups');
    $groups = array(
        "Lecturers",
        "Students",
        "Guest"
    );
    for ($i = 0; $i < count($groups); $i++) {
        $groupDropdown->addOption($groups[$i], $groups[$i]);
    }
    $groupDropdown->setSelected($group);
    $table->addCell($label2->show() . $groupDropdown->show() . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $label->show() . $courseDropdown->show());
    $table->endRow();
    //Ehb-added-End
    $table->startRow();
    $orderLabel = new label($this->objLanguage->languageText('mod_contextgroups_orderresultsby', 'contextgroups') . ': ', 'input_order');
    $searchdropdown->name = 'order';
    $searchdropdown->cssId = 'input_order';
    //$table->addCell($orderLabel->show().$searchdropdown->show());
    $label = new label($this->objLanguage->languageText('mod_contextgroups_noofresults', 'contextgroups') . ': ', 'input_results');
    $dropdown = new dropdown('results');
    $dropdown->addOption('20', '20');
    $dropdown->addOption('30', '30');
    $dropdown->addOption('50', '50');
    $dropdown->addOption('75', '75');
    $dropdown->addOption('100', '100');
    //$dropdown->addOption('all', 'All Results');
    $table->addCell($orderLabel->show() . $searchdropdown->show() . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $label->show() . $dropdown->show());
    $table->endRow();
    $button = new button('searchbutton');
    $button->value = $this->objLanguage->languageText('word_search');
    $button->setToSubmit();
    $table->addCell($button->show());
    $table->addCell('&nbsp;');
    $table->endRow();
    $form = new form('searchforusers', $this->uri(array(
        'action' => 'searchforusers'
    )));
    $form->addToForm($table->show());
    echo $form->show();
}
?>
