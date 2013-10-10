<?php
/*
* Template for the worksheet home page.
* @package worksheetadmin
*/

/**
* @param $ar The list of worksheets in the context
*/
$this->setLayoutTemplate('worksheetadmin_layout_tpl.php');

// set up html elements
$this->loadClass('link', 'htmlelements');
$objTable = $this->newObject('htmltable', 'htmlelements');
$objIcon = $this->newObject('geticon','htmlelements');
$objConfirm = $this->newObject('confirm','utilities');
$objLayer = & $this->newObject('layer', 'htmlelements');

// set up language items
$worksheet=$objLanguage->languageText('mod_worksheetadmin_worksheet','worksheetadmin');
$tableHd[]=$objLanguage->languageText('mod_worksheetadmin_wordname','worksheetadmin');
$tableHd[]=$objLanguage->languageText('mod_worksheetadmin_chapter','worksheetadmin');
$tableHd[]=$objLanguage->languageText('mod_worksheetadmin_questions','worksheetadmin');
$tableHd[]=$objLanguage->languageText('mod_worksheetadmin_activitystatus','worksheetadmin');
$tableHd[]=$objLanguage->languageText('mod_worksheetadmin_percentage','worksheetadmin');
$tableHd[]=$objLanguage->languageText('mod_worksheetadmin_total','worksheetadmin').' '
.$objLanguage->languageText('mod_worksheetadmin_mark','worksheetadmin');
$tableHd[]=$objLanguage->languageText('mod_worksheetadmin_closingdate','worksheetadmin');
$answerLabel=$objLanguage->languageText('mod_worksheetadmin_selecttoanswer','worksheetadmin');
$addLabel=$objLanguage->languageText('mod_worksheetadmin_add','worksheetadmin').' '.$objLanguage->languageText('word_new')
.' '.$worksheet;
$editLabel=$objLanguage->languageText('word_edit','worksheetadmin').' '.$worksheet;
$deleteLabel=$objLanguage->languageText('word_delete').' '.$worksheet;
$markLabel=$objLanguage->languageText('mod_worksheetadmin_mark','worksheetadmin').' '.$worksheet;
$deleteConfirm=$objLanguage->languageText('word_delete').' ';
$assignLabel = $objLanguage->languageText('mod_assignmentadmin_name','worksheetadmin');
$noRecords = $objLanguage->code2Txt('mod_worksheetadmin_noworksheetsset','worksheetadmin');

$objIcon->title=$addLabel;
$addIcon=$objIcon->getAddIcon($this->uri(array('action'=>'add')));

$heading=$objLanguage->languageText('mod_worksheetadmin_name','worksheetadmin').' '.$objLanguage->languageText('mod_worksheetadmin_in','worksheetadmin')
." <span class='coursetitle'>$contextTitle</span>";
$heading.='&nbsp;&nbsp;&nbsp;&nbsp;'.$addIcon;
$this->setVarByRef('heading',$heading);

//Create the centered area for display
$objLayer->align="center";

//Create a table
$objTable->cellpadding='5';
$objTable->cellspacing='2';
$objTable->width='99%';

//Create the array for the table header
$tableRow=array();

$allowAdmin = True; //You need to write your security here

$tableHd[] = '&nbsp;';
//Create the table header for display
$objTable->addHeader($tableHd, "heading");

//Loop through and display the records
$rowcount = 0;
if (!empty($ar)) {
    if (count($ar) > 0) {
        foreach ($ar as $line) {
            $oddOrEven = ($rowcount == 0) ? "odd" : "even";

            $viewWorksheetLink = new link($this->uri(array( 'module'=> 'worksheet',
            'action' => 'view', 'id'=>$line['id'])));
            $viewWorksheetLink->link = $line['name'];

            $tableRow[]=$viewWorksheetLink->show();
            $tableRow[]=$line['node'];
            $tableRow[]=$line['questions'];
            $tableRow[]=$objLanguage->languageText('mod_worksheetadmin_activity'.$line['activity_status'],'worksheetadmin');
            $tableRow[]=$line['percentage'];
            $tableRow[]=$line['total_mark'];
            $tableRow[]=$line['date'];

            $objIcon->title=$editLabel;
            $icons=$objIcon->getEditIcon($this->uri(array('action'=>'editworksheet',
            'id'=>$line['id'])));

            // Don't allow deletion if WS is open for use or closed for marking
            if($line['activity_status']=='inactive' || $line['activity_status']=='marked'){
                $objIcon->setIcon('delete');
                $objIcon->title=$deleteLabel;
                $objConfirm->setConfirm($objIcon->show(),$this->uri(array('action'=>'deleteworksheet'
                , 'id'=>$line['id'])),$deleteConfirm.$line['name'].'?');
                $icons.= $objConfirm->show();
            }

            // Allow lecturer access to completed WS's if status = closed for marking
            if($line['activity_status'] != 'inactive'){
                $objIcon->setIcon('comment');
                $objIcon->title=$markLabel;
                $arrMark = array('action'=>'listworksheet', 'id'=>$line['id'], 'status' => $line['activity_status']);
                $markLink = new link($this->uri($arrMark));
                $markLink->link=$objIcon->show();
                $icons.= $markLink->show();
            }

            $tableRow[]=$icons;

            //Add the row to the table for output
           $objTable->addRow($tableRow, $oddOrEven);
           $tableRow=array(); // clear it out               // Set rowcount for bitwise determination of odd or even
           $rowcount = ($rowcount == 0) ? 1 : 0;
        }
    }
}else{
    $objTable->startRow();
    $objTable->addCell($noRecords, '','','','noRecordsMessage','colspan= "8"');
    $objTable->endRow();
}

//Add the table to the centered layer
$objLayer->addToStr($objTable->show());

//Output the content to the page
echo '<p>'.$objLayer->show().'</p>';

$newWorksheetLink = new link($this->uri(array( 'module'=> 'worksheet', 'action' => 'add')));
$newWorksheetLink->link = $addLabel;
$links = "<div class='adminadd'></div><div class='adminaddlink'>".$newWorksheetLink->show()."</div>";

if($this->assignment){
    $assignmentLink = new link($this->uri(array(''),'assignmentadmin'));
    $assignmentLink->link = $assignLabel;
    $links .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$assignmentLink->show();
}

echo '<p align="center">'.$links.'</p>';

?>