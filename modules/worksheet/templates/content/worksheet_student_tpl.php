<?php
/**
* Template displaying a list of worksheets in context to students.
* @package worksheet
*/

/**
* @param array $ar The list of worksheets and their details.
*/

$this->setLayoutTemplate('worksheet_layout_tpl.php');

// set up html elements
$objLink =& $this->newObject('link', 'htmlelements');
$objTable =& $this->newObject('htmltable', 'htmlelements');
$objIcon =& $this->newObject('geticon','htmlelements');
$objLayer =& $this->newObject('layer', 'htmlelements');

// set up language items
$worksheet=$objLanguage->languageText('mod_worksheet_worksheet','worksheet');
$assignLabel=$objLanguage->languageText('mod_assignment_name','worksheet');
$tableHd[]=$worksheet.' '.$objLanguage->languageText('mod_worksheet_wordname','worksheet');
$tableHd[]=$objLanguage->languageText('mod_worksheet_chapter','worksheet');
$tableHd[]=$objLanguage->languageText('mod_worksheet_questions','worksheet');
$tableHd[]=$objLanguage->languageText('mod_worksheet_activitystatus','worksheet');
$tableHd[]=$objLanguage->languageText('mod_worksheet_percentage','worksheet').' '.$objLanguage->languageText('mod_worksheet_of','worksheet')
.' '.$objLanguage->languageText('mod_worksheet_yearmark','worksheet');
$tableHd[]=$objLanguage->languageText('mod_worksheet_total','worksheet').' '
.$objLanguage->languageText('mod_worksheet_mark','worksheet');
$tableHd[]=$objLanguage->languageText('mod_worksheet_closingdate','worksheet');
$answerLabel=$objLanguage->languageText('mod_worksheet_selecttoanswer','worksheet');
$markedLabel=$objLanguage->languageText('mod_worksheet_viewmarked','worksheet');
$closedLabel=$objLanguage->languageText('mod_worksheet_closed','worksheet');
$submittedLabel=$objLanguage->languageText('mod_worksheet_submitted','worksheet');
$heading=$objLanguage->languageText('mod_worksheet_name','worksheet').' '.$objLanguage->languageText('mod_worksheet_in','worksheet')
.' '.$contextTitle;

$this->setVarByRef('heading',$heading);

//Create a table
$objTable->cellpadding='3';
$objTable->cellspacing='2';
$objTable->width='99%';

//Create the table header for display
$objTable->addHeader($tableHd, 'heading');

$i=0;
$rows=array();
foreach($ar as $line){
    if($line['activity_status'] != 'inactive'){
        $class = (($i++%2)==0) ? 'odd' : 'even';

        $link = $line['name'];
        $date = $closedLabel;

        if($line['activity_status'] == 'marked'){
            $objLink->link($this->uri(array('action' => 'viewmarked',
            'worksheet_id'=>$line['id'])));
            $objLink->link = $line['name'];
            $objLink->title = $markedLabel.' '.$worksheet;
            $link = $objLink->show();
        }else{
            $closeTime=strtotime($line['closing_date']);
            $curTime=time();
            $completed = 0;

            if(isset($line['completed'])){
                $completed = $line['completed'];
            }
            if($completed){
                $date=$worksheet.' '.$submittedLabel;
            }else if($curTime < $closeTime){
                $objLink->link($this->uri(array('action' => 'selectforanswer',
                'id'=>$line['id'])));
                $objLink->link = $line['name'];
                $objLink->title = $answerLabel;
                $link = $objLink->show();
                $date = $line['date'];
            }
        }
        
        $rows[] = $link;
        $rows[] = $line['node'];
        $rows[] = $line['questions'];
        $rows[] = $objLanguage->languageText('mod_worksheet_activity'.$line['activity_status'],'worksheet');
        $rows[] = $line['percentage'];
        $rows[] = $line['total_mark'];
        $rows[] = $date;

        $objTable->addRow($rows, $class);
        $rows=array();
    }
}

$objTable->addRow(array('&nbsp;'),'');

echo $objTable->show();

if($this->assignment){
    $objLink->link($this->uri(array(''),'assignment'));
    $objLink->link = $assignLabel;

    $objLayer->str = $objLink->show();
    $objLayer->align = 'center';

    echo $objLayer->show();
}
?>