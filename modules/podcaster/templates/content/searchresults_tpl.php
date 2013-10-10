<script type="text/javascript" >
    function updateChangeList(item)
    {
        document.getElementById('input_changedItems').value += ','+item;
        alert(item);
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
$this->loadClass('checkbox', 'htmlelements');
$this->appendArrayVar('headerParams', $this->getJavascriptFile('highlightLabels.js', 'htmlelements'));
$css = '<style type="text/css" title="text/css">
.checked img {
    background-color:yellow;border: 1px solid green;
    padding: 3px;
}
</style>';
$this->appendArrayVar('headerParams', $css);
$this->appendArrayVar('bodyOnLoad', 'setUpLabelHighlight();');
//Get Group Name
$groupId = $this->getSession('groupId', $groupId);
$groupName = $this->_objGroupAdmin->getName($groupId);
$groupName = explode("^", $groupName);
$groupName = $groupName[1];
$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_podcaster_searchuserstoadd', "podcaster", "Search for users to add to") . " " . $groupName;
echo $header->show();
$table = $this->newObject('htmltable', 'htmlelements');
$table->cellpadding = 5;
$table->startRow();
$searchLabel = new label($this->objLanguage->languageText('mod_contextgroups_searchby', 'contextgroups', 'Search by') . ': ', 'input_field');
$searchdropdown = new dropdown('field');
$searchdropdown->addOption('firstName', $this->objLanguage->languageText('phrase_firstname', 'system', 'First name'));
$searchdropdown->addOption('surname', $this->objLanguage->languageText('word_surname', 'system', 'Surname'));
$searchdropdown->addOption('userId', $this->objLanguage->languageText('word_userid', 'system', 'Userid'));
$searchdropdown->setSelected($resultsArr['field']);
//$table->addCell();
$label = new label($this->objLanguage->languageText('mod_contextgroups_startswith', 'contextgroups', 'Starts with') . ': ', 'input_search');
$input = new textinput('search');
$input->value = htmlentities(stripslashes($resultsArr['searchfor']));
$input->size = 20;
$table->addCell($searchLabel->show() . $searchdropdown->show() . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $label->show() . $input->show());
$table->endRow();
$table->startRow();
//Ehb-added-begin
$label = new label($this->objLanguage->languageText('mod_contextgroups_choosecourse', 'contextgroups', 'Users of'), 'input_course');
$courseDropdown = new dropdown('course');
$courseDropdown->addOption('all', $this->objLanguage->languageText('mod_contextgroups_allcourses', 'contextgroups', 'All courses'));
for ($i = 0; $i < count($resultsArr['data']); $i++) {
    $courseDropdown->addOption($resultsArr['data'][$i]['contextcode'], $resultsArr['data'][$i]['title']);
}
$courseDropdown->setSelected($resultsArr['course']);
$label2 = new label($this->objLanguage->languageText('mod_contextgroups_choosegroup', 'contextgroups', 'Users Group'), 'input_group');
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
$groupDropdown->setSelected($resultsArr['group']);
$table->addCell($label2->show() . $groupDropdown->show() . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $label->show() . $courseDropdown->show());
$table->endRow();
//Ehb-added-End
$table->startRow();
$orderLabel = new label($this->objLanguage->languageText('mod_contextgroups_orderresultsby', 'contextgroups', 'Order results by222') . ': ', 'input_order');
$searchdropdown->name = 'order';
$searchdropdown->cssId = 'input_order';
$searchdropdown->setSelected($resultsArr['order']);
//$table->addCell($orderLabel->show().$searchdropdown->show());
$label = new label($this->objLanguage->languageText('mod_contextgroups_noofresults', 'contextgroups', "No. of results") . ': ', 'input_results');
$dropdown = new dropdown('results');
$dropdown->addOption('20', '20');
$dropdown->addOption('30', '30');
$dropdown->addOption('50', '50');
$dropdown->addOption('75', '75');
$dropdown->addOption('100', '100');
//$dropdown->addOption('all', 'All Results');
$dropdown->setSelected($resultsArr['numresults']);
$table->addCell($orderLabel->show() . $searchdropdown->show() . "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . $label->show() . $dropdown->show());
$table->endRow();
$button = new button('searchbutton');
$button->value = $this->objLanguage->languageText('word_search', 'system', 'Search');
$button->setToSubmit();
$table->addCell($button->show());
$table->addCell('&nbsp;');
$table->endRow();
$form = new form('searchforusers', $this->uri(array(
                    'action' => 'searchforusers'
                )));
$form->addToForm($table->show());
echo $form->show();
// --------------------------------
if (count($resultsArr['results']) == 0) {
    echo '<div class="noRecordsMessage">' . $this->objLanguage->languageText('mod_contextgroups_nousersfoundsearchcriteria', 'contextgroups', 'No Users found with search criteria') . '</div>';
} else {
    $start = ($resultsArr['page'] - 1) * $resultsArr['numresults'] + 1;
    if ($resultsArr['page'] * $resultsArr['numresults'] > $resultsArr['countResults']) {
        $end = $resultsArr['countResults'];
    } else {
        $end = $resultsArr['page'] * $resultsArr['numresults'];
    }
    $header = new htmlheading();
    $header->type = 1;
    // if ($resultsArr['numresults'] == 'all') {
    // $header->str = 'All Search Results ('.$resultsArr['countResults'].') for: <em>'.$resultsArr['searchfor'].'</em>';
    // } else {
    if (!empty($resultsArr['searchfor'])) {
        $string = $this->objLanguage->languageText('mod_podcaster_searchresultsfor', 'podcaster', 'Search results for');
        $string = str_replace('[-START-]', $start, $string);
        $string = str_replace('[-END-]', $end, $string);
        $string = str_replace('[-TOTAL-]', $resultsArr['countResults'], $string);
        $header->str = $string . ': <em>' . $resultsArr['searchfor'] . '</em> ';
    } else {
        $string = $this->objLanguage->languageText('mod_podcaster_searchresults', 'podcaster', 'Search results');
        $header->str = $string;
    }
    //}
    echo $header->show();
    $table = $this->newObject('htmltable', 'htmlelements');
    $table->cellpadding = 5;
    $table->startHeaderRow();
    //$table->addHeaderCell($this->objLanguage->languageText('word_userid', 'system', 'Userid'));
    $table->addHeaderCell($this->objLanguage->languageText('mod_security_staffstudentnumber', 'security', 'Staff/Student Number'));
    $table->addHeaderCell($this->objLanguage->languageText('word_name', 'system', "Name"));
    //$table->addHeaderCell($this->objLanguage->languageText('phrase_firstname', 'system', "First name"));
    //$table->addHeaderCell($this->objLanguage->languageText('word_surname', 'system', 'Surname'));
    //$table->addHeaderCell('Sex');
    //$table->addHeaderCell('Email Address');
    $table->addHeaderCell($this->objLanguage->languageText('mod_podcaster_removeadd', 'podcaster', 'Add/Remove'));
    $table->endHeaderRow();
    //Store the members in an array
    $existingMembers = ",";

    foreach ($resultsArr['results'] as $result) {
        $table->row_attributes = 'onmouseover="this.className=\'tbl_ruler\';" onmouseout="this.className=\'none\'; "';
        $table->startRow();
        //$table->addCell($result['userid']);
        $table->addCell($result['staffnumber']);
        //$table->addCell($result['title']);
        //$table->addCell($result['firstname']);
        $table->addCell($result['title'] . ". " . $result['firstname'] . " " . $result['surname']);
        $objCheck = new checkbox('user[]');
        $objCheck->value = $result['userid'];
        if (in_array($result['userid'], $resultsArr['guests'])) {
            $existingMembers .= "," . $result['userid'];
            $objCheck->ischecked = True;
        }
        $table->addCell($objCheck->show());
        $table->endRow();
    }
    $addUsersForm = new form('addusers', $this->uri(array(
                        'action' => 'addusers'
                    )));
    $button = new button('submitform', $this->objLanguage->languageText('mod_contextgroups_updateuserroles', 'contextgroups', 'Update user roles'));
    $button->setToSubmit();
    $button->extra = 'style="margin-right: 50px;"';
    $hiddenInput = new hiddeninput('context', $resultsArr['contextCode']);
    $existingMembersHI = new hiddeninput('existingMembers', $existingMembers);
    $addUsersForm->addToForm($hiddenInput->show());
    $hiddenInput = new hiddeninput('changedItems', '');
    // $hiddenInput->cssId = 'changedItems';
    $hiddenInput->extra = 'id="changedItems"';
    $addUsersForm->addToForm($hiddenInput->show() . $existingMembersHI->show());
    $addUsersForm->addToForm($table->show());
    $addUsersForm->addToForm('<p align="left">' . $button->show() . '</p>');
    echo $addUsersForm->show();
    echo '<p>' . $this->objLanguage->languageText('mod_contextgroups_browseresults', 'contextgroups', 'Browse Results') . ': ' . $resultsArr['paging'] . '</p>';
}
$returnLink = new link($this->uri(array('action' => 'configure_events')));
$returnLink->link = $this->objLanguage->languageText('mod_podcaster_backtoevents', 'podcaster', 'Back to events');
echo '<p align="center">' . $returnLink->show() . '</p>';
?>