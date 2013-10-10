<?php
/**
* Template to display a list of student submissions for a worksheet.
* @package worksheetadmin
*/

/**
* @param array $results The list of students submitted worksheets.
* @param string $worksheetName The name of the worksheet.
*/
$this->setLayoutTemplate('worksheetadmin_layout_tpl.php');

// set up html elements
$objTable =& $this->newObject('htmltable','htmlelements');
$objLayer =& $this->newObject('layer','htmlelements');
$objLink =& $this->newObject('link','htmlelements');
$objIcon =& $this->newObject('geticon','htmlelements');
$objPop =& $this->newObject('windowpop','htmlelements');
$objConfirm =& $this->newObject('confirm', 'utilities');

$studentLabel = ucwords($objLanguage->languageText('mod_context_readonly','worksheetadmin'))
.' '.$objLanguage->languageText('mod_worksheetadmin_wordname','worksheetadmin');
$markLabel = $objLanguage->languageText('mod_worksheetadmin_mark','worksheetadmin');
$dateLabel = $objLanguage->languageText('mod_worksheetadmin_submitdate','worksheetadmin');
$markWsLabel = $markLabel.' '.$objLanguage->languageText('mod_worksheetadmin_worksheet','worksheetadmin');
$backLabel = $objLanguage->languageText('mod_worksheetadmin_backto','worksheetadmin').' '.
$objLanguage->languageText('mod_worksheetadmin_name','worksheetadmin');
$noStudentsLabel = $objLanguage->languageText('mod_worksheetadmin_noworksheets','worksheetadmin').' '
.$objLanguage->languageText('mod_worksheetadmin_submitted','worksheetadmin');
$rubricLabel = $objLanguage->languageText('mod_rubric_name');
$reopenLabel = $this->objLanguage->languageText('mod_worksheetadmin_reopenworksheet','worksheetadmin');
$heading = $markWsLabel.': '.$worksheetName;
$this->setVarByRef('heading',$heading);

// set up table with list of students
$objTable->cellpadding='5';
$objTable->cellspacing='2';
$objTable->width='99%';

$tableHd=array();
$tableHd[]=$studentLabel;
$tableHd[]=$markLabel.' (%)';
$tableHd[]=$dateLabel;
$tableHd[]='&nbsp;';

$objTable->addHeader($tableHd, 'heading');

if(!empty($results)){
    $i=0;
    foreach($results as $line){
        $class = (($i++%2)==0) ? 'even' : 'odd';
        $tableRow=array();
        $fullName = $this->objUser->fullname($line['userid']);
        $tableRow[] = $fullName;
        if($line['mark']!= -1){
            $tableRow[]=$line['mark'];
        }else $tableRow[]='';
        $tableRow[]=$this->formatDate($line['last_modified']);

        if($status == 'open'){
            $arrCon = array('studentname' => $fullName);
            $conReopen = $objLanguage->code2Txt('mod_worksheetadmin_confirmreopenworksheet','worksheetadmin',$arrCon);
            $arrReopen = array('action' => 'reopenworksheet', 'id' => $line['worksheet_id'], 'userid' => $line['userid']);
            $objConfirm->setConfirm($reopenLabel, $this->uri($arrReopen), $conReopen);
            $openLink = $objConfirm->show();
        }else{
            $objLink->link($this->uri(array('action'=>'markworksheet','worksheet'=>$line['worksheet_id'],'student'=>$line['userid'])));
            $objLink->link = $markWsLabel;
            $openLink = $objLink->show();
        }

        $tableRow[] = $openLink;
        $objTable->addRow($tableRow, $class);
    }
} else {
    $tableRow[]=$noStudentsLabel;
    $tableRow[]='';
    $tableRow[]='';
    $tableRow[]='';
    $objTable->addRow($tableRow, 'even');
}
$str = '';

if($this->rubric){
    $objPop->resizable = 'yes';
    $objPop->scrollbars = 'yes';
    $objPop->set('location',$this->uri('','rubric'));
    $objPop->set('linktext',$rubricLabel);
    $str .= '<p>'.$objPop->show();
}

$str.=$objTable->show();

$objLink->link($this->uri(''));
$objLink->link=$backLabel;
$links = '<p>'.$objLink->show();

$objLayer->align='center';
$objLayer->str=$links;
$str .= $objLayer->show();

echo $str;
?>