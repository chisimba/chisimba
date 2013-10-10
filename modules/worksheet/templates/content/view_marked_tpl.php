<?php
/**
* Template for viewing a marked worksheet.
* @package worksheet
*/

/**
* Template for viewing a marked worksheet.
*/
$this->setLayoutTemplate('worksheet_layout_tpl.php');
$this->loadClass('textinput', 'htmlelements');

// Set up html elements
$objTable =& $this->newObject('htmltable','htmlelements');
$objForm =& $this->newObject('form','htmlelements');
//$objInput =& $this->newObject('textinput','htmlelements');
$objLayer =& $this->newObject('layer','htmlelements');
$objLink =& $this->newObject('link','htmlelements');
$objImage =& $this->newObject('image','htmlelements');

// Set up language items
$viewLabel = $objLanguage->languageText('mod_worksheet_viewmarked','worksheet');
$worksheetLabel = $objLanguage->languageText('mod_worksheet_worksheet','worksheet');
$chapterLabel = $objLanguage->languageText('mod_worksheet_chapter','worksheet');
$totalLabel = $objLanguage->languageText('mod_worksheet_totalmarks','worksheet');
$percentLabel = $objLanguage->languageText('mod_worksheet_percentageoffinalmark','worksheet');
$markLabel = $objLanguage->languageText('mod_worksheet_mark','worksheet');
$questionLabel = $objLanguage->languageText('mod_worksheet_question','worksheet');
$answerLabel = $objLanguage->languageText('mod_worksheet_answer','worksheet');
$commentLabel = $objLanguage->languageText('mod_worksheet_comment','worksheet');
$gottoLabel = $objLanguage->languageText('mod_worksheet_goto','worksheet');
$outofLabel = $objLanguage->languageText('mod_worksheet_outof','worksheet');
$exitLabel = $objLanguage->languageText('word_exit');
$noWorksheet = $objLanguage->languageText('mod_worksheet_noworksheetsubmitted','worksheet');

$heading = $viewLabel.' '.$worksheetLabel.' '.$worksheet['name'];
$this->setVarByRef('heading',$heading);

// Page Head
$objTable->width='99%';
$objTable->cellpadding='2';
$objTable->row_attributes='height="20"';
$objTable->startRow();
$objTable->addCell('<b>'.$chapterLabel.':</b> '.$worksheet['node'],'35%');
$objTable->addCell('<b>'.$totalLabel.':</b> '.$worksheet['total_mark'],'30%');
$objTable->addCell('<b>'.$percentLabel.':</b> '.$worksheet['percentage'].'%','35%');
$objTable->endRow();

$total=$data[0]['totalmark'];

$mark=round($total/$worksheet['total_mark']*100);

$objTable->startRow();
$objTable->addCell("<b>$markLabel:</b> $total &nbsp;($mark%)",'','','','','colspan="3"');
$objTable->endRow();

$objTable->startRow();
$objTable->addCell('');
$objTable->endRow();

// Questions and comments
if(!empty($data)){
    foreach($data as $line){
        $objTable->startRow();
        $pQuestion = $this->objWashout->parseText($line['question']);
        $objTable->addCell('<b>'.$questionLabel.' '.$line['question_order'].':</b> '.$pQuestion,'','','','odd','colspan="3"');
        $objTable->endRow();

        // Display image if set
        if(!empty($line['imageName'])){
            $objImage = new image();
            $objImage->src = $this->uri(array('action'=>'viewimage', 'fileid'=>$line['imageId']));

            $objTable->startRow();
            $objTable->addCell($objImage->show(),'','','','odd','colspan="3"');
            $objTable->endRow();
        }

        $objTable->startRow();
        $objTable->addCell('<b>'.$answerLabel.':</b> '.$line['answer'],'','','','even','colspan="3"');
        $objTable->endRow();

        $objTable->startRow();
        $objTable->addCell('<b>'.$markLabel.':</b> '.$line['mark'].' <b>/</b> '.$line['question_worth'],'','','','odd','colspan="3"');
        $objTable->endRow();

        $objTable->startRow();
        $objTable->addCell('<b>'.$commentLabel.':</b> '.$line['comments'],'','','','even','colspan="3"');
        $objTable->endRow();

        $objTable->startRow();
        $objTable->addCell('');
        $objTable->endRow();
    }
}else{
    $objTable->startRow();
    $objTable->addCell($noWorksheet,'','','','odd','colspan="3"');
    $objTable->endRow();

    $objTable->startRow();
    $objTable->addCell('');
    $objTable->endRow();
}

$javascript="<script language=\"javascript\" type=\"text/javascript\">
        function submitform(val){
        document.getElementById('input_num').value=val;
        document.getElementById('form_navWS').submit();
    }
    </script>";

$objInput = new textinput('num','');
$objInput->fldType='hidden';
$nav = $objInput->show();

if(!empty($data)){
    $nav .= '<b>'.$gottoLabel.' '.$questionLabel.':</b><br /><br />';
    $nav .= $this->generateLinks($data[0]['question_order'], $worksheet['count'], 5);
    $nav .= '&nbsp;&nbsp;|&nbsp;&nbsp;';
}

$objLink->link("javascript:submitform('exit');");
$objLink->link=$exitLabel;
$nav .= $objLink->show();

$objLayer->align='center';
$objLayer->str=$nav;

$objForm->form('navWS',$this->uri(array('action'=>'viewmarked','worksheet_id'=>$worksheet['id'])));
$objForm->addToForm($objLayer->show());

echo $javascript;
echo $objTable->show();
echo $objForm->show();
?>