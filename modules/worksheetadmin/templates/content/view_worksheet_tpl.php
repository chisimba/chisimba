<?php
/**
* Template for viewing a worksheet.
* @package worksheetadmin
*/

/**
* @param $sheet The worksheet information.
* @param $questions The details of the questions on the worksheet.
*/
$this->setLayoutTemplate('worksheetadmin_layout_tpl.php');

// Classes used in this module
$this->loadClass('link', 'htmlelements');
$objLayer = $this->newObject('layer','htmlelements');
$objIcon = $this->newObject('geticon','htmlelements');
$objField = $this->newObject('fieldset','htmlelements');
$objMsg = $this->newObject('timeoutmessage','htmlelements');
$objConfirm = $this->newObject('confirm','utilities');

// set up language items
$head=$objLanguage->languageText('mod_worksheetadmin_worksheet','worksheetadmin');
$editLabel=$objLanguage->languageText('word_edit');
$contextLabel=ucwords($objLanguage->languageText('mod_context_context','worksheetadmin'));
$chapterLabel=$objLanguage->languageText('mod_worksheetadmin_chapter','worksheetadmin');
$statusLabel=$objLanguage->languageText('mod_worksheetadmin_activitystatus','worksheetadmin');
$percentLabel=$objLanguage->languageText('mod_worksheetadmin_percentageoffinalmark','worksheetadmin');
$dateLabel=$objLanguage->languageText('mod_worksheetadmin_closingdate','worksheetadmin');
$questionsLabel=$objLanguage->languageText('mod_worksheetadmin_questionsfor','worksheetadmin');
$questionLabel=$objLanguage->languageText('mod_worksheetadmin_question','worksheetadmin');
$questionLabelWithNo=$objLanguage->languageText('mod_worksheetadmin_question_no','worksheetadmin');//Question with number
$markLabel=$objLanguage->languageText('mod_worksheetadmin_mark','worksheetadmin');
$worthLabel=$objLanguage->languageText('mod_worksheetadmin_allocated','worksheetadmin').' '.$markLabel;
$totalLabel=$objLanguage->languageText('mod_worksheetadmin_total','worksheetadmin');
$actionLabel=$objLanguage->languageText('mod_worksheetadmin_actions','worksheetadmin');
$addLabel=$objLanguage->languageText('mod_worksheetadmin_adda','worksheetadmin').' '.$questionLabel;
$backLabel=$objLanguage->languageText('mod_worksheetadmin_worksheet','worksheetadmin').' '.$objLanguage->languageText('word_home');
$assignLabel=$objLanguage->languageText('mod_assignment_name','worksheetadmin');
$confirm=$objLanguage->languageText('word_delete').' '.$questionLabel;
$confirmWithNo=$objLanguage->languageText('word_delete').' '.$questionLabelWithNo;
$deleteLabel=$objLanguage->languageText('word_delete').' '.$sheet['name'];
$deleteConfirm=$objLanguage->languageText('word_delete').' ';
$detailsLabel = $objLanguage->languageText('mod_worksheetadmin_details','worksheetadmin');
$noRecords = $objLanguage->languageText('mod_worksheetadmin_noquestionsset','worksheetadmin');

// Heading for work sheet
$editUrl = $this->uri(array( 'module'=> 'worksheet', 'action' => 'editworksheet', 'id' => $sheet['id']));
$editLink = $objIcon->getEditIcon($editUrl);

if($sheet['activity_status']=='inactive' || $sheet['activity_status']=='marked'){
    $objIcon->setIcon('delete');
    $objIcon->title=$deleteLabel;
    $objConfirm->setConfirm($objIcon->show(),$this->uri(array('action'=>'deleteworksheet'
    , 'id'=>$sheet['id'])),$deleteConfirm.$sheet['name'].'?');
    $editLink.='&nbsp;'.$objConfirm->show();
}

if($sheet['activity_status']=='closed' || $sheet['activity_status']=='marked'){
    $objIcon->setIcon('comment');
    $objIcon->title=$markLabel.' '.$head;
    $markLink = new link($this->uri(array('action'=>'listworksheet', 'id'=>$sheet['id'])));
    $markLink->link=$objIcon->show();
    $editLink.='&nbsp;'.$markLink->show();
}

// Show Heading
$heading = $head.': '.$sheet['name'].'&nbsp;&nbsp;'.$editLink;
$this->setVarByRef('heading',$heading);

// Create Table for the worksheet information
$table= $this->newObject('htmltable', 'htmlelements');
$table->cellpadding='5';
$table->cellspacing='2';
$table->width='99%';

// Add Context and Name of Chapter
$table->startRow();
$table->addCell('<b>'.$contextLabel.'</b>: '.$contextTitle);
$table->addCell('<b>'.$chapterLabel.'</b>: '.$sheet['node']);
$table->endRow();

// Add Activity Status and percentage of mark
$table->startRow();
$table->addCell('<b>'.$statusLabel.'</b>: '.$objLanguage->languageText('mod_worksheetadmin_activity'.$sheet['activity_status'],'worksheetadmin'));
$table->addCell('<b>'.$percentLabel.'</b>: '.$sheet['percentage'].' %');
$table->endRow();

// Add Cosing date
$table->startRow();
$table->addCell('<b>'.$dateLabel.'</b>: '.$sheet['date']);
$table->addCell('<b>'.$totalLabel.' '.$markLabel.'</b>: '.$sheet['total_mark']);
$table->endRow();

// Description
$table->startRow();
$table->addCell($sheet['description'], NULL, "top", NULL, NULL, ' colspan="2"'); // colspans to two
$table->endRow();

// Show Table
$contentTable= $table->show();

// Confirmation message
$msgStr = '';
if(isset($msg)){
    $objMsg->setMessage($msg.'&nbsp;'.date('d/m/Y H:i'));
    $objMsg->setTimeOut(10000);
    $msgStr = '<p>'.$objMsg->show().'</p>';
}

/* *** Questions Section *** */

// add a new question
$numQuestions = count($questions);
$objIcon->title=$addLabel;
$addQ=$objIcon->getAddIcon($this->uri(array( 'module'=> 'worksheet', 'action' => 'addquestion',
'id' => $sheet['id'], 'count' => $numQuestions)));

// Questions Header
$objHeading = $this->getObject('htmlheading', 'htmlelements');
$objHeading->type=4;
$objHeading->str=$questionsLabel.': '.$sheet['name'].' ('.$numQuestions.')
&nbsp;&nbsp;&nbsp;';
if($sheet['activity_status']=='inactive')
{
	$objHeading->str .=$addQ;
}
$qHeading= $objHeading->show();

// Create a New table for the questions
$table= $this->newObject('htmltable', 'htmlelements');
$table->cellpadding='5';
$table->cellspacing='2';
$table->width='99%';

// Counter
$questionCounter=0;

$table->startRow();
$table->addHeaderCell('');
$table->addHeaderCell($questionLabel);
$table->addHeaderCell($worthLabel.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;('
.$totalLabel.': '.$sheet['total_mark'].')');
$table->addHeaderCell($actionLabel);
$table->endRow();

// Add questions to table
if(!empty($questions)){
    foreach ($questions AS $question)
    {
        $class = (($questionCounter++ % 2)==0) ? "odd" : "even";

        // Shorten the question to 100 characters for display
        $pos = FALSE;
        $len = strlen($question['question']);
        $strQuestion = $question['question'];
        if($len > 10){
            $pos = strpos($question['question'], '<br', 10);
        }

        if($len > 100 && $pos === FALSE){
            $pos = strpos($question['question'], ' ', 100);
        }

        if(!($pos === FALSE)){
            $strQuestion = substr($question['question'], 0, $pos).'...';
        }

        $table->startRow();
        $table->addCell($questionCounter,'3%','','',$class);
        $table->addCell($strQuestion,'47%','','',$class);
        $table->addCell($question['question_worth'],'38%','','',$class);

        $actions = '';
        if ($sheet['activity_status']=='inactive')
        {
        	if($questionCounter > 1){
            	$questionLink = new link($this->uri(array( 'module'=> 'worksheet', 'action' => 'questionup'
            	, 'id' => $question['id'], 'worksheet' => $sheet['id'])));
            	$questionIcon = $this->newObject('geticon', 'htmlelements');
            	$questionIcon->setIcon('mvup');
            	$questionLink->link = $questionIcon->show();
            	$actions = $questionLink->show().'&nbsp;';
        	}else{
            	$actions = '&nbsp;&nbsp;&nbsp;&nbsp;';
        	}

        	if($questionCounter < $numQuestions){
            	$questionLink = new link($this->uri(array( 'module'=> 'worksheet', 'action' => 'questiondown'
            	, 'id' => $question['id'], 'worksheet' => $sheet['id'])));
            	$questionIcon = $this->newObject('geticon', 'htmlelements');
            	$questionIcon->setIcon('mvdown');
            	$questionLink->link = $questionIcon->show();
            	$actions .= $questionLink->show().'&nbsp;';
        	}else{
            	$actions .= '&nbsp;&nbsp;&nbsp;&nbsp;';
        	}

        	$questionLink = new link($this->uri(array( 'module'=> 'worksheet', 'action' => 'editquestion'
        	, 'id' => $question['id'], 'worksheet' => $sheet['id'])));
        	$questionIcon = $this->newObject('geticon', 'htmlelements');
        	$questionIcon->setIcon('edit');
        	$questionLink->link = $questionIcon->show();
        	$actions .= $questionLink->show();

        	$questionIcon = $this->newObject('geticon', 'htmlelements');
        	$questionIcon->setIcon('delete');
        	$objConfirm->setConfirm($questionIcon->show(),$this->uri(array( 'module'=> 'worksheet', 'action' => 'deletequestion'
        	, 'id' => $question['id'], 'worksheet' => $sheet['id'], 'mark' => $question['question_worth'])),
        	$confirmWithNo.': '.$questionCounter.'?');
        	$actions .= $objConfirm->show();

	}
        	$table->addCell($actions,'12%','','',$class);

        	$table->endRow();
    	}
    	$qTable = $table->show();
}else{
    $qTable = '<p class="noRecordsMessage">'.$noRecords.'</p>';
}

if($sheet['activity_status']=='inactive')
{
	$addQUrl = $this->uri(array( 'module'=> 'worksheet', 'action' => 'addquestion', 'id' => $sheet['id'], 'count' => count($questions)));
	$objLink = new link($addQUrl);
	$objLink->link = $addLabel;
	$homeLink = "<div class='adminadd'></div><div class='adminaddlink'>".$objLink->show()."</div>";
}
else
{
	$homeLink = '';
}
$backHomeLink = new link($this->uri(array('')));
$backHomeLink->link = $backLabel;
$homeLink .= "<div class='adminicon'></div><div class='adminiconlink'>".$backHomeLink->show()."</div>";

$backParam = $this->getParam('mod');
if($backParam == 'back'){
    $assignmentLink = new link($this->uri(array('action'=>'viewbyletter')));
    $assignmentLink->link = $assignLabel;
    $homeLink .= "<div class='modulehome'></div><div class='modulehomelink'>".$assignmentLink->show()."</div>";
}

$objLayer->cssClass='';
$objLayer->align='center';
$objLayer->str=$homeLink;
$back=$homeLink;//$objLayer->show();

$objLayer->cssClass='even';
$objLayer->align='left';
$objLayer->str=$contentTable;

$detStr = $objLayer->show();

//$detStr .= $back.'<br>';

$objField->contents = $detStr;
$objField->legend = $detailsLabel;
$str = $objField->show();

$str .= $msgStr;

$str .= $qHeading;

$objLayer->cssClass='even';
$objLayer->align='left';
$objLayer->str=$qTable;

$str .= $objLayer->show();

$str .= $back;

echo $str;
?>