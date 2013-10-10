<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('fieldset', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$header = new htmlheading();
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_wicid_unapproved', 'wicid', 'Unapproved Documents') . ' (' . count($documents) . ')';

echo $header->show();

$newdoclink = new link($this->uri(array("action" => "newdocument", "selected" => $selected)));
$newdoclink->link = "New Course Proposal";

$unapproveddocs = new link($this->uri(array("action" => "unapproveddocs")));
$unapproveddocs->link = "Unapproved/New documents";


$rejecteddocuments = new link($this->uri(array("action" => "rejecteddocuments")));
$rejecteddocuments->link = "Rejected documents";


$links = $newdoclink->show();
$fs = new fieldset();
$fs->setLegend('Navigation');
$fs->addContent($links);

echo $fs->show() . '<br/>';


$table = $this->getObject("htmltable", "htmlelements");
$table->startHeaderRow();
$table->addHeaderCell("Title");
//$table->addHeaderCell("Ref No");
$table->addHeaderCell("Department");
$table->addHeaderCell("Owner");
$table->addHeaderCell("Telephone");
$table->addHeaderCell("Date");
$table->addHeaderCell("Print");
$table->addHeaderCell("Forward to APO");
$table->endHeaderRow();

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('edit');

if (count($documents) > 0) {
    foreach ($documents as $document) {
        $makepdf = new link($this->uri(array("action"=>"makepdf", "id"=>$document['id'], "all"=>"on")));
        $forwardDocument = new link($this->uri(array("action"=>"forwardtoAPO", "id"=>$document['id'], "all"=>"on")));
        $ForwardDocument = new link($this->uri(array("action"=>"forwarding", "id"=>$document['id'], "all"=>"on")));
        $objIcon->setIcon('pdf');
        $makepdf->link = $objIcon->show();
        $forwardDocument->link  = "<img src=".$this->getResourceUri('images/', 'apo')."forward.png>";
        $ForwardDocument->link  = "<img src=".$this->getResourceUri('images/', 'apo')."Forward.png>";
       //print_r($document['currentuserid']);die();
        if ($document['currentuserid'] == "Administrative User") {
            $link = new link($this->uri(array("action" => "showeditdocument", "id" => $document['id'])));

            $link->link = $document['filename'];

            $table->startRow();
            $table->addCell($link->show());
            $table->addCell($document['department']);
            //$table->addCell($document['refNo']);
            $table->addCell($document['owner']);
            $table->addCell($document['telephone']);
            $table->addCell($document['date']);
            $table->addCell($makepdf->show());
            //$table->addCell();
            $table->addCell($forwardDocument->show());
           // $table->addCell($ForwardDocument->show());
            $table->endRow();
        } else if ($document['contact_person'] == '' && $document['currentuserid'] != '') {
            $link = new link($this->uri(array("action" => "reclaimdocumentform", "id" => $document['id'])));
            $link->link = $document['filename'];

            $table->startRow();
            $table->addCell($link->show());
            $table->addCell($document['department']);
            //$table->addCell($document['refno']);
            $table->addCell($document['owner']);
            $table->addCell($document['telephone']);
            $table->addCell($document['date']);
            $table->addCell($makepdf->show());
            $table->endRow();
        } else {
            $table->startRow();
            $table->addCell($document['filename']);
            $table->addCell($document['department']);
            //$table->addCell($document['refno']);
            $table->addCell($document['owner']);
            $table->addCell($document['telephone']);
            $table->addCell($document['date']);
            $table->addCell($makepdf->show());
            $table->endRow();
        }
    }
}

echo $table->show();
?>