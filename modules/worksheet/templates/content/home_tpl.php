<?php
//var_dump($worksheets);


$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');


$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('add');
$objIcon->alt = $this->objLanguage->languageText('mod_worksheet_createnewworksheet', 'worksheet', 'Create New Worksheet');
$objIcon->title = $this->objLanguage->languageText('mod_worksheet_createnewworksheet', 'worksheet', 'Create New Worksheet');

$addLink = new link ($this->uri(array('action'=>'add')));
$addLink->link = $objIcon->show();


$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_worksheet_name', 'worksheet'); //$this->objContext->getTitle().': '.

if ($this->isValid('add')) {
    $header->str .= ' '.$addLink->show();
}

echo $header->show();

if (count($worksheets) == 0) {
    echo '<div class="noRecordsMessage">No Worksheets at present</div>';
} else {
    $table = $this->newObject('htmltable', 'htmlelements');


    if ($this->isValid('worksheetinfo')) {
        $table->startHeaderRow();
            $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_worksheetname', 'worksheet', 'Worksheet Name'));
            $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_questions', 'worksheet', 'Questions'));
            $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_activitystatus', 'worksheet', 'Activity Status'));
            $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_percentage', 'worksheet', 'Percentage'));
            $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_totalmark', 'worksheet', 'Total Mark'));
            $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_closingdate', 'worksheet', 'Closing Date'));
            $table->addHeaderCell("&nbsp;");
        $table->endHeaderRow();

        foreach ($worksheets as $worksheet)
        {
            $table->startRow();
                $link = new link ($this->uri(array('action'=>'worksheetinfo', 'id'=>$worksheet['id'])));
                $link->link = $worksheet['name'];
                $table->addCell($link->show());
                $table->addCell($worksheet['questions']);
                $table->addCell($this->objWorksheet->getStatusText($worksheet['activity_status']));
                $table->addCell($worksheet['percentage']);
                $table->addCell($worksheet['total_mark']);
                $table->addCell($worksheet['closing_date']);

                // View icon
				$viewLink = new link ($this->uri(array('action'=>'preview', 'id'=>$worksheet['id'])));
				$objIcon->setIcon('view');
				$objIcon->alt = "";
				$objIcon->title = "";
                $viewLink->link = $objIcon->show();

                // Edit icon
				$editLink = new link ($this->uri(array('action'=>'edit', 'id' => $worksheet['id'] )));
				$objIcon->setIcon('edit');
				$objIcon->alt = "";
				$objIcon->title = "";
				$editLink->link = $objIcon->show();

                // Delete icon
                $deleteIcon = $objIcon->getDeleteIconWithConfirm(
                    NULL,
                    array(
                        'action'=>'deleteworksheet',
                        'id'=>$worksheet['id']
                    ),
                    'worksheet',
                    $this->objLanguage->languageText('mod_worksheet_confirmdeleteworksheet', 'worksheet')
                );

				$table->addCell($viewLink->show() . $editLink->show().$deleteIcon);

				$viewLink = null;
                $editLink = null;
                $deleteIcon = null;
            $table->endRow();
        }
    } else {
        $table->startHeaderRow();
            $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_worksheetname', 'worksheet', 'Worksheet Name'));
            $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_questions', 'worksheet', 'Questions'));
            $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_activitystatus', 'worksheet', 'Activity Status'));
            $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_percentage', 'worksheet', 'Percentage'));
            $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_totalmark', 'worksheet', 'Total Mark'));
            $table->addHeaderCell($this->objLanguage->languageText('mod_worksheet_closingdate', 'worksheet', 'Closing Date'));
        $table->endHeaderRow();

        $counter = 0;
        $studentViewStatus = array('open', 'closed', 'marked');

        foreach ($worksheets as $worksheet)
        {
            if (in_array($worksheet['activity_status'], $studentViewStatus)) {
                $counter++;
                $table->startRow();
                    switch($worksheet['activity_status'])
                    {
                        case 'marked':
                            $link = new link ($this->uri(array('action'=>'viewworksheet', 'id'=>$worksheet['id'])));
                            $link->link = $worksheet['name'];
                            $link = $link->show();
                            break;
                        case 'open':

                            // Fix automatic closure
                            /*if (strtotime(date('Y-m-d  H:i:s')) > strtotime($worksheet['closing_date'])) {
                                $worksheet['activity_status'] = 'closed';
                                $link = $worksheet['name'];
                            } else {*/
                                $link = new link ($this->uri(array('action'=>'viewworksheet', 'id'=>$worksheet['id'])));
                                $link->link = $worksheet['name'];
                                $link = $link->show();
                            //}

                            break;
                        default:
                            $link = $worksheet['name'];
                            break;
                    }

                    $table->addCell($link);
                    $table->addCell($worksheet['questions']);
                    $table->addCell($this->objWorksheet->getStatusText($worksheet['activity_status']));
                    $table->addCell($worksheet['percentage']);
                    $table->addCell($worksheet['total_mark']);
                    $table->addCell($worksheet['closing_date']);
                $table->endRow();
            }
        }

        if ($counter == 0) {

        }
    }

    echo $table->show();
}

if ($this->isValid('add')) {
    $addLink = new link ($this->uri(array('action'=>'add')));
    $addLink->link = $this->objLanguage->languageText('mod_worksheet_createnewworksheet', 'worksheet', 'Create New Worksheet');

    echo '<p>'.$addLink->show().'</p>';
}

?>