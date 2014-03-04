<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
$this->loadClass('htmlheading', 'htmlelements');
$header = new htmlheading();
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_wicid_rejected', 'wicid', 'Rejected Documents');

echo $header->show();


// Create a Register New Document Button
//$button = new button("button1", $this->objLanguage->languageText('mod_wicid_registernewdoc', 'wicid', "Register New Document"));
//$button->issubmitbutton = false;
$newdoclink = new link($this->uri(array("action" => "newdocument", "selected" => $selected)));
//$newdoclink->link = $button->showDefault();
$newdoclink->link = $this->objLanguage->languageText('mod_wicid_registernewdoc', 'wicid', "Register New Document");

// Create a Unapproved/New documents Button
//$button = new button("button2", $this->objLanguage->languageText('mod_wicid_newunapproved', 'wicid', "Unapproved/New documents"));
$unapproveddocs = new link($this->uri(array("action" => "unapproveddocs")));
//$button->issubmitbutton = false;
//$unapproveddocs->link = $button->showDefault();
$unapproveddocs->link = $this->objLanguage->languageText('mod_wicid_newunapproved', 'wicid', "Unapproved/New documents");

$links = $newdoclink->show() . '&nbsp;|&nbsp;' . $unapproveddocs->show() . '<br/>';

//Add navigation to fieldset
$fs = new fieldset();
$fs->setLegend($this->objLanguage->languageText('mod_wicid_navigation', 'wicid', 'Navigation'));
$fs->addContent($links);

echo $fs->show();

echo "<br />";
$table = $this->getObject("htmltable", "htmlelements");
//Get no of rows
$doccount = $documents['count'];
if ($doccount > 0) {
    $table->startHeaderRow();
    $table->addHeaderCell($this->objLanguage->languageText('mod_wicid_owner', 'wicid', "Owner"));
    $table->addHeaderCell($this->objLanguage->languageText('mod_wicid_refno', 'wicid', "Ref No"));
    $table->addHeaderCell($this->objLanguage->languageText('mod_wicid_title', 'wicid', "Title"));
    $table->addHeaderCell($this->objLanguage->languageText('mod_wicid_topic', 'wicid', "Topic"));
    $table->addHeaderCell($this->objLanguage->languageText('mod_wicid_telephone', 'wicid', "Telephone"));
    $table->addHeaderCell($this->objLanguage->languageText('mod_wicid_attachment', 'wicid', "Attachment"));
    $table->endHeaderRow();
    $count = 0;
    foreach ($documents as $document) {
        if (count($document) > 1) {
            if (($count % 2) == 0) {
                $table->startRow("even");
            } else {
                $table->startRow("odd");
            }
            $table->addCell($document['owner']);
            $table->addCell($document['refno']);
            $table->addCell($document['filename']);
            $table->addCell($document['topic']);
            $table->addCell($document['telephone']);
            $table->addCell($document['attachmentstatus']);
            $table->endRow();
            $count++;
        }
    }
} else {
    $table->startRow();
    $table->addCell($this->objLanguage->languageText('mod_wicid_norecords', 'wicid', "There are no records found"));
    $table->endRow();
}

$contenttable = $table->show();

//Add Navigations
if ($doccount > 0) {
    //Compute new start val
    $newstart = $start + $rows;
    $newprev = $start - $rows;
    //Navigation Flag
    $str = "";
    //total row count
    $totalrowcount = $documents['count'];
    //Create table to hold buttons(forms)
    $table = &$this->newObject("htmltable", "htmlelements");
    $table->width = '100%';
    $table->startRow();
    $nextflag = "nonext";

    //Store count
    $textinput2 = new textinput('rcount');
    $textinput2->size = 1;
    $textinput2->value = $rows;
    $textinput2->setType('hidden');

    //Add prev button
    if ($newprev >= 0) {
        $str .= "prev";
        $button = new button('submit', $this->objLanguage->languageText('mod_wicid_wordprevious', 'wicid', 'Previous'));
        $button->setToSubmit();
        //Add Form
        $prevform = new form('prevform', $this->uri(array('action' => 'rejecteddocuments', 'mode' => $mode, 'start' => $newprev, 'rowcount' => $totalrowcount)));

        $prevform->addToForm("</ br> " . $button->show() . $textinput2->show() . " </ br>");

        $table->addCell($prevform->show(), "50%", 'top', 'right');
    }
    //Add Next button
    if ($newstart < $totalrowcount && $start != $totalrowcount && $totalrowcount > $rows) {

        $button = new button('submit', $this->objLanguage->languageText('mod_wicid_wordnext', 'wicid', 'Next'));
        $button->setToSubmit();
        //Add Form
        $nextform = new form('nextform', $this->uri(array('action' => 'rejecteddocuments', 'mode' => $mode, 'start' => $newstart, 'rowcount' => $totalrowcount)));

        $nextform->addToForm("</ br> " . $button->show() . $textinput2->show() . " </ br>");
        if (!empty($str)) {
            $table->addCell($nextform->show(), "50%", 'top', 'left');
        } else {
            $table->addCell(" ", "50%", 'top', 'left');
            $table->addCell($nextform->show(), "50%", 'top', 'left');
        }
        $str .= "next";
        $nextflag = "next";
    }
    if ($nextflag == "nonext") {
        $table->addCell(" ", "50%", 'top', 'left');
    }
    $table->endRow();
    $navtable = $table->show();
}
$dd = &new dropdown('rcount');
$dd->addOption('50', '50');
$dd->addOption('100', '100');
$dd->addOption('150', '150');
$dd->addOption('200', '200');
$dd->addOption('250', '250');
$dd->addOption('300', '300');
$dd->addOption('350', '350');
$dd->addOption('400', '400');
$dd->addOption('450', '450');
$dd->addOption('500', '500');
$dd->selected = $rows;
$dd->onchangeScript = 'onchange="document.forms[\'totalrowcount\'].submit();"';

//Select no of records to display
$rcountform = new form('totalrowcount', $this->uri(array('action' => 'rejecteddocuments', 'mode' => $mode, 'active' => 'Y', 'start' => 0)));
$button = new button('submit', $this->objLanguage->languageText('mod_wicid_wordgo', 'wicid', 'List'));
$button->setToSubmit();
$rcountform->addToForm("</ br> " . $button->show() . " " . $dd->show() . " records. </ br>");

//Create table to hold the rowcount
$table = &$this->newObject("htmltable", "htmlelements");
$table->width = '100%';
$table->startRow();
$table->endRow();
$table->startRow();
$table->addCell($rcountform->show(), "50%", 'top', 'left', Null, 'colspan="2"');
$table->endRow();
$rcounttable = $table->show();

//Add rejected documents table to fieldset
$fs = new fieldset();
$fs->setLegend($this->objLanguage->languageText('mod_wicid_registeredocs', 'wicid', "Rejected documents"));
//Check if str is empty
if (!empty($str)) {
    $fs->addContent($rcounttable . "<br/>" . $contenttable . "<br/>" . $navtable);
} else {
    $fs->addContent($rcounttable . "<br/>" . $contenttable);
}
echo $fs->show();
?>
