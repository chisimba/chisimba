<?php

// Load Classes needed
$this->loadClass('link', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');

$objIcon->setIcon('add');
$objIcon->align = 'top';
$objIcon->alt = 'Add User';
$objIcon->title = 'Add User';

$link = new link($this->uri(array('action' => 'create'), 'workgroupadmin'));
$link->link = $objIcon->show();

// Display the heading.    
$pageTitle = $this->newObject('htmlheading', 'htmlelements');
$pageTitle->type = 1;
$pageTitle->align = 'left';

$titleContent = ucwords($objLanguage->code2Txt("mod_workgroup_heading", 'workgroup'));

if ($this->objUser->isAdmin()) {
    $titleContent.= ' ' . $link->show();
} elseif ($this->objUser->isLecturer()) {
    $titleContent = ' ' . $link->show();
}
$pageTitle->str = $titleContent;
echo $pageTitle->show();

$tblclass = $this->newObject('htmltable', 'htmlelements');
$tblclass->width = '40%';
$tblclass->border = '0';
$tblclass->cellspacing = '1';
$tblclass->cellpadding = '5';

$tblclass->startRow();
$tblclass->addHeaderCell(ucwords($objLanguage->code2Txt('mod_workgroup_heading', 'workgroup')), "null", "top", "left", "", null);
$tblclass->addHeaderCell("&nbsp;");
$tblclass->endRow();

// Display available workgroups.
$oddOrEven = "odd";
foreach ($workgroups as $workgroup) {
    $tblclass->startRow();
    $oddOrEven = ($oddOrEven == "even") ? "odd" : "even";

    $tblclass->addCell($workgroup['description'], "null", "top", "left", $oddOrEven, null);
    // Rename workgroup.

    $options = "<a href=\"" .
            $this->uri(array(
                'module' => 'workgroups',
                'action' => 'joinworkgroup',
                'workgroup' => $workgroup['id']
            ))
            . "\">" . $objLanguage->languageText('mod_workgroup_wordjoin', 'workgroup', 'Join') . "</a>";
    $options .= "&nbsp;";
    $tblclass->addCell($options, "null", "top", "left", $oddOrEven, null);
    $tblclass->endRow();
}

$objContextCondition = &$this->getObject('contextcondition', 'contextpermissions');
$isContextLecturer = $objContextCondition->isContextMember('Lecturers');

if (empty($workgroups)) {
    if ($isContextLecturer) {

        $href = $this->uri(array('action' => 'create'), 'workgroupadmin');
        $url = "<a href=\"$href\">" . $objLanguage->languageText('word_here') . "</a>";
        echo($objLanguage->code2Txt('mod_workgroup_noworkgroups', 'workgroup', array('URL' => $url)));
    } else {
        $tblclass->startRow();
        $tblclass->addCell("<div class=\"noRecordsMessage\">" . $objLanguage->code2Txt('mod_workgroup_notamember', 'workgroup') . "</div>", "null", "top", "left", "", 'colspan="3"');
        $tblclass->endRow();
    }
}
echo $tblclass->show();

if ($isContextLecturer) {
    $manageLink = new link($this->uri(NULL, 'workgroupadmin'));
    $manageLink->link = $objLanguage->code2Txt('mod_workgroup_manageworkgroups', 'workgroup');
    echo '<p>' . $manageLink->show() . '</p>';
}
?>
