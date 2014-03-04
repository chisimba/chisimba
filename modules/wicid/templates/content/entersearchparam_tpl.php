<?php

/*
 * Template that captures the search parameters
 *
 */
$this->loadclass('link', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

//Get edit icon
$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('edit');

$table = &$this->newObject('htmltable', 'htmlelements');
//Add table to show results
$table = &$this->newObject('htmltable', 'htmlelements');
//Display results if search was positive
if (count($files) >= 1) {
    $count = 0;
    $table->startHeaderRow();
    $table->addHeaderCell("<b>" . $this->objLanguage->languageText('mod_wicid_title', 'wicid', 'Title') . "</b>");
    $table->addHeaderCell("<b>" . $this->objLanguage->languageText('mod_wicid_refno', 'wicid', 'Ref No') . "</b>");
    $table->addHeaderCell("<b>" . $this->objLanguage->languageText('mod_wicid_owner', 'wicid', 'Owner') . "</b>");
    $table->addHeaderCell("<b>" . $this->objLanguage->languageText('mod_wicid_topic', 'wicid', "Topic") . "</b>");
    $table->addHeaderCell("<b>" . $this->objLanguage->languageText('mod_wicid_telephone', 'wicid', "Telephone") . "</b>");
    $table->addHeaderCell("<b>" . $this->objLanguage->languageText('mod_wicid_attachment', 'wicid', "Attachment") . "</b>");
    $table->addHeaderCell("<b>" . $this->objLanguage->languageText('mod_wicid_date', 'wicid', "Date") . "</b>");
    $table->addHeaderCell("<b>" . $this->objLanguage->languageText('mod_wicid_type', 'wicid', "Type") . "</b>");
    $table->endHeaderRow();

    foreach ($files as $document) {
        if (!empty($document['id'])) {
            //Present depending on type of document (Approved, Unapproved, Rejected
            if ($document['doctype'] == "Approved") {
                $type = "";

                $dlink1 = new link($this->uri(array("action" => "downloadfile", "filepath" => $document['filepath'], "filename" => $document['fullfilename'])));
                $dlink1->link = $document['thumbnailpath'];

                $dlink2 = new link($this->uri(array("action" => "downloadfile", "filepath" => $document['filepath'], "filename" => $document['fullfilename'])));
                $dlink2->link = $document['filename'];

                //Show checkbox even without attachment
                //Add row to render the record data
                //Check if even
                if (($count % 2) == 0) {
                    $table->startRow("even");
                } else {
                    $table->startRow("odd");
                }
                $table->addCell($dlink2->show());
                $table->addCell($document['refno']);
                $table->addCell($document['owner']);
                $table->addCell($document['topic']);
                $table->addCell($document['telephone']);
                // w.setUrl(GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN + "?module=wicid&action=uploadfile&docname=" + document.getTitle()
                //         + "&docid=" + document.getId() + "&topic=" + document.getTopic());


                $uplink = new link($this->uri(array("action" => "uploadfile", "docname" => $document['filename'], "docid" => $document['id'], "topic" => $document['topic'])));
                $uplink->link = $objIcon->show();

                $table->addCell($dlink1->show());
                $table->addCell($document['date']);
                $table->addCell($document['doctype']);
                $table->endRow();
                //Increment count
                $count++;
            } elseif ($document['doctype'] == "Unapproved") {
                $link = new link($this->uri(array("action" => "editdocument", "id" => $document['id'])));
                $link->link = $document['filename'];

                //Show checkbox even without attachment
                //Add row to render the record data
                if (($count % 2) == 0) {
                    $table->startRow("even");
                } else {
                    $table->startRow("odd");
                }
                $table->addCell($link->show());
                $table->addCell($document['refno']);
                $table->addCell($document['owner']);
                $table->addCell($document['topic']);
                $table->addCell($document['telephone']);
                // w.setUrl(GWT.getHostPageBaseURL() + Constants.MAIN_URL_PATTERN + "?module=wicid&action=uploadfile&docname=" + document.getTitle()
                //         + "&docid=" + document.getId() + "&topic=" + document.getTopic());


                $uplink = new link($this->uri(array("action" => "uploadfile", "docname" => $document['filename'], "docid" => $document['id'], "topic" => $document['topic'])));
                $uplink->link = $objIcon->show();

                $table->addCell($document['attachmentstatus'] . $uplink->show());
                $table->addCell($document['date']);
                $table->addCell($document['doctype']);
                $table->endRow();
                //Increment count
                $count++;
            } elseif ($document['doctype'] == "Rejected") {
                //Add row to render the record data
                if (($count % 2) == 0) {
                    $table->startRow("even");
                } else {
                    $table->startRow("odd");
                }
                $table->addCell($document['filename']);
                $table->addCell($document['refno']);
                $table->addCell($document['owner']);
                $table->addCell($document['topic']);
                $table->addCell($document['telephone']);
                $table->addCell($document['attachmentstatus']);
                $table->addCell($document['date']);
                $table->addCell($document['doctype']);
                $table->endRow();
                //Increment count
                $count++;
            }
        }
    }
    //Create legend for the search results
    $fs = new fieldset();
    $fs->setLegend($this->objLanguage->languageText('mod_wicid_searchresults', 'wicid', 'Search Results'));
    $fs->addContent($table->show());

    echo $fs->show();
} elseif ($status == 1) {
    $table->startRow();
    $table->addCell($this->objLanguage->languageText('mod_wicid_norecords', 'wicid', "There are no records found"));
    $table->endRow();
    //Create legend for the search results
    $fs = new fieldset();
    $fs->setLegend($this->objLanguage->languageText('mod_wicid_searchresults', 'wicid', 'Search Results'));
    $fs->addContent($table->show());

    echo $fs->show();
}
?>