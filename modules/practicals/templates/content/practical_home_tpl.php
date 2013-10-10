<?php

$openLabel = $this->objLanguage->languageText('mod_practicals_open', 'practicals');
$closedLabel = $this->objLanguage->languageText('mod_practicals_closed', 'practicals');
$viewLabel = $this->objLanguage->languageText('mod_practicals_view', 'practicals');
$uploadLabel = $this->objLanguage->languageText('mod_practicals_upload', 'practicals');
$onlineLabel = $this->objLanguage->languageText('mod_practicals_online', 'practicals');

// Set up html elements
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objTimeOut = $this->newObject('timeoutMessage', 'htmlelements');

$objTrim = $this->getObject('trimstr', 'strings');
$createButton = new button('submit', $this->objLanguage->languageText('mod_practicals_createpracticals', 'practicals', 'Create Practical'));
//$createButton->setToSubmit();

$objHead = new htmlheading();
$objHead->str = $this->objLanguage->languageText('mod_practicals_practicals', 'practicals', 'practicals');
$objHead->type = 1;

if ($this->isValid('add')) {

    $objIcon->setIcon('add');
    $link = new link($this->uri(array('action' => 'add')));
    $link->link =$objIcon->show();// $createButton->show();
    $objHead->str .= ' ' . $link->show();
}

echo $objHead->show();

$objTable = $this->newObject('htmltable', 'htmlelements');

$objTable->startHeaderRow();
$objTable->addHeaderCell($this->objLanguage->languageText('word_name', 'system', 'Name'), '20%');
$objTable->addHeaderCell($this->objLanguage->languageText('mod_practicals_practicalstype', 'practicals', 'Practical Type'), '13%');
//$objTable->addHeaderCell($this->objLanguage->languageText('word_description', 'system', 'Description'));
$objTable->addHeaderCell(ucfirst($this->objLanguage->code2Txt('mod_practicals_lecturer', 'practicals', NULL, '[-author-]')), '15%');
$objTable->addHeaderCell($this->objLanguage->languageText('mod_practicals_closingdate', 'practicals', 'Closing Date'), '15%');
$objTable->addHeaderCell($this->objLanguage->languageText('word_status', 'system', 'Status'), '8%');

if ($this->isValid('edit') && count($practicals) > 0) {
    $objTable->addHeaderCell('&nbsp;', '60');
}

$objTable->endHeaderRow();

if (count($practicals) == 0) {



    $objTable->startRow();
    $objTable->addCell($this->objLanguage->languageText('mod_practicals_nopracticals', 'practicals', 'No practicals'), '', '', '', 'noRecordsMessage', 'colspan="6"');
    $objTable->endRow();
} else {

    $i = 0;
    $status = '';

    $objIcon->setIcon('edit');
    $editIcon = $objIcon->show();

    $objIcon->setIcon('delete');
    $deleteIcon = $objIcon->show();

    $counter = 0;


    foreach ($practicals as $practical) {
        $class = ($i++ % 2 == 0) ? 'odd' : 'even';

        if ($practical['closing_date'] > date('Y-m-d H:i')) {
            if (($practical['opening_date'] < date('Y-m-d H:i')) || $practical['opening_date'] == NULL) {
                $status = $openLabel;
            } else {
                $status = $this->objLanguage->languageText('mod_practicals_notopenforentry', 'practicals', 'Not Open for Entry');
            }
        } else {
            $status = $closedLabel;
        }

        $objLink = new link($this->uri(array('action' => 'view', 'id' => $practical['id'])));
        $objLink->title = $viewLabel . ' ' . $practical['name'];
        $objLink->link = $practical['name'];


        // Display whether the practicals is online or uploadable
        if ($practical['format'] == 1) {
            $format = $uploadLabel;
        } else {
            $format = $onlineLabel;
        }

        $okToShow = FALSE;

        if (($practical['opening_date'] < date('Y-m-d H:i')) || $practical['opening_date'] == NULL) {
            $okToShow = TRUE;
        }

        if ($practical['visibility'] == '0') {
            $okToShow = FALSE;
            $groups = $this->objPracticalGroups->getWorkgroups($practical['id']);
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
            //$objTable->addCell($objTrim->strTrim(strip_tags($practicals['description']), 50),'','','',$class);
            $objTable->addCell($this->objUser->fullname($practical['userid']), '15%', '', '', $class);
            $objTable->addCell($this->objDate->formatDate($practical['closing_date']), '15%', '', '', $class);
            $objTable->addCell($status, '8%', '', '', $class);

            if ($this->isValid('edit')) {
                $editLink = new link($this->uri(array('action' => 'edit', 'id' => $practical['id'])));
                $editLink->link = $editIcon;

                $deleteLink = new link($this->uri(array('action' => 'delete', 'id' => $practical['id'])));
                $deleteLink->link = $deleteIcon;

                $objTable->addCell($editLink->show() . '&nbsp;' . $deleteLink->show(), '60');
            }
            $objTable->endRow();
        }
    }

    if ($counter == 0) {
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText('mod_practicals_nopracticals', 'practicals', 'No Practicals'), '', '', '', 'noRecordsMessage', 'colspan="6"');
        $objTable->endRow();
    }
}

echo $objTable->show();


if ($this->isValid('add')) {
    $link = new link($this->uri(array('action' => 'add')));
    $link->link = $this->objLanguage->languageText('mod_practicals_addpracticals', 'practicals', 'Add Practical');

    echo '<p>' . $link->show() . '</p>';
}


if ($this->objUser->isContextStudent($this->contextCode)) {
    $this->objLink->link($this->uri(array('action' => 'displaylist')));
    $this->objLink->link = $this->objLanguage->languageText('mod_practicals_submittedpracticalslist', 'practicals');

    echo $this->objLink->show();
}
?>