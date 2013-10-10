<?php
$ret = "";
$openLabel = $this->objLanguage->languageText('mod_assignment_open', 'assignment');
$closedLabel = $this->objLanguage->languageText('mod_assignment_closed', 'assignment');
$viewLabel = $this->objLanguage->languageText('mod_assignment_view', 'assignment');
$uploadLabel = $this->objLanguage->languageText('mod_assignment_upload', 'assignment');
$onlineLabel = $this->objLanguage->languageText('mod_assignment_online', 'assignment');

// Set up html elements
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objTimeOut = $this->newObject('timeoutMessage', 'htmlelements');

$objTrim = $this->getObject('trimstr', 'strings');
$createButton = new button('submit', $this->objLanguage->languageText('mod_assignment_createassignments', 'assignment', 'Create Assignment'));
//$createButton->setToSubmit();

$objHead = new htmlheading();
$objHead->str = $this->objLanguage->languageText('mod_assignment_assignments', 'assignment', 'Assignments');
$objHead->type = 1;

if ($this->isValid('add')) {

    $objIcon->setIcon('add');
    $link = new link($this->uri(array('action' => 'add')));
    $link->link =$objIcon->show();// $createButton->show();
    $objHead->str .= ' ' . $link->show();
}

$ret .= $objHead->show();

$objTable = $this->newObject('htmltable', 'htmlelements');

$objTable->startHeaderRow();
$objTable->addHeaderCell($this->objLanguage->languageText('word_name', 'system', 'Name'), '20%');
$objTable->addHeaderCell($this->objLanguage->languageText('mod_assignment_assignmenttype', 'assignment', 'Assignment Type'), '13%');
//$objTable->addHeaderCell($this->objLanguage->languageText('word_description', 'system', 'Description'));
$objTable->addHeaderCell(ucfirst($this->objLanguage->code2Txt('mod_assignment_lecturer', 'assignment', NULL, '[-author-]')), '15%');
$objTable->addHeaderCell($this->objLanguage->languageText('mod_assignment_closingdate', 'assignment', 'Closing Date'), '15%');
$objTable->addHeaderCell($this->objLanguage->languageText('word_status', 'system', 'Status'), '8%');

if ($this->isValid('edit') && count($assignments) > 0) {
    $objTable->addHeaderCell('&nbsp;', '60');
}

$objTable->endHeaderRow();

if (count($assignments) == 0) {



    $objTable->startRow();
    $objTable->addCell($this->objLanguage->languageText('mod_assignment_noassignments', 'assignment', 'No Assignments'), '', '', '', 'noRecordsMessage', 'colspan="6"');
    $objTable->endRow();
} else {

    $i = 0;
    $status = '';

    $objIcon->setIcon('edit');
    $editIcon = $objIcon->show();

    $objIcon->setIcon('delete');
    $deleteIcon = $objIcon->show();

    $counter = 0;


    foreach ($assignments as $assignment) {
        $class = ($i++ % 2 == 0) ? 'odd' : 'even';

        if ($assignment['closing_date'] > date('Y-m-d H:i')) {
            if (($assignment['opening_date'] < date('Y-m-d H:i')) || $assignment['opening_date'] == NULL) {
                $status = $openLabel;
            } else {
                $status = $this->objLanguage->languageText('mod_assignment_notopenforentry', 'assignment', 'Not Open for Entry');
            }
        } else {
            $status = $closedLabel;
        }

        $objLink = new link($this->uri(array('action' => 'view', 'id' => $assignment['id'])));
        $objLink->title = $viewLabel . ' ' . $assignment['name'];
        $objLink->link = $assignment['name'];


        // Display whether the assignment is online or uploadable
        if ($assignment['format'] == 1) {
            $format = $uploadLabel;
        } else {
            $format = $onlineLabel;
        }

        $okToShow = FALSE;

        if (($assignment['opening_date'] < date('Y-m-d H:i')) || $assignment['opening_date'] == NULL) {
            $okToShow = TRUE;
        }

        if ($assignment['visibility'] == '0') {
            $okToShow = FALSE;
            $groups = $this->objAssignmentGroups->getWorkgroups($assignment['id']);
            $mc = $this->getObject('modules', 'modulecatalogue');
            if ($mc->checkIfRegistered('workgroup')) {
                $objUser = $this->getObject('user', 'security');
                $objGroups = $this->getObject('dbWorkgroupUsers', 'workgroup');
                foreach ($groups as $group) {

                    if ($objGroups->memberOfWorkGroup($objUser->userid(), $group['workgroup_id'])) {
                        $okToShow = TRUE;
                    }
                }
            }

        }
        if ($this->isValid('edit')) {
            $okToShow = TRUE;
        }

        if ($okToShow) {

            $counter++;

            $objTable->startRow();
            $objTable->addCell($objLink->show(), '20%', '', '', $class);
            $objTable->addCell($format, '13%', '', '', $class);
            //$objTable->addCell($objTrim->strTrim(strip_tags($assignment['description']), 50),'','','',$class);
            $objTable->addCell($this->objUser->fullname($assignment['userid']), '15%', '', '', $class);
            $objTable->addCell($this->objDate->formatDate($assignment['closing_date']), '15%', '', '', $class);
            $objTable->addCell($status, '8%', '', '', $class);

            if ($this->isValid('edit')) {
                $editLink = new link($this->uri(array('action' => 'edit', 'id' => $assignment['id'])));
                $editLink->link = $editIcon;

                $deleteLink = new link($this->uri(array('action' => 'delete', 'id' => $assignment['id'])));
                $deleteLink->link = $deleteIcon;

                $objTable->addCell($editLink->show() . '&nbsp;' . $deleteLink->show(), '60', NULL, NULL, $class);
            }
            $objTable->endRow();
        }
    }

    if ($counter == 0) {
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_assignment_noassignments', 'assignment', 'No Assignments'), '', '', '', 'noRecordsMessage', 'colspan="6"');
        $objTable->endRow();
    }
}

$ret .= $objTable->show();


if ($this->isValid('add')) {
    $link = new link($this->uri(array('action' => 'add')));
    $link->link = $this->objLanguage->languageText('mod_assignment_addassignment', 'assignment', 'Add Assignment');

    $ret .= '<p class="assignment_link_add">' . $link->show() . '</p>';
}


if ($this->objUser->isContextStudent($this->contextCode)) {
    $this->objLink->link($this->uri(array('action' => 'displaylist')));
    $this->objLink->link = $this->objLanguage->languageText('mod_assignment_submittedassignmentslist', 'assignment');
    $ret .= '<p class="assignment_link_submittedlist">' . $this->objLink->show() . '</p>';
}

echo "<div class='assignment_main'>$ret</div>";
?>