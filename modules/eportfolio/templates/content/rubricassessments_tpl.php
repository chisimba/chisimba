<?php
$pageTitle = $this->newObject('htmlheading', 'htmlelements');
$pageTitle->type = 1;
$pageTitle->align = 'left';
$pageTitle->str = $objLanguage->languageText('rubric_rubric', 'rubric') . " : " . $title;
// Show Title
echo $pageTitle->show();
// Show Description
echo '<p>' . $description . '</p>';
$tblclass = $this->newObject('htmltable', 'htmlelements');
$tblclass->width = '99%';
$tblclass->border = '0';
$tblclass->cellspacing = '1';
$tblclass->cellpadding = '5';
$tblclass->startHeaderRow();
if ($showStudentNames == "yes") {
    $tblclass->addHeaderCell($objLanguage->languageText('rubric_name', 'rubric') , 60);
}
$tblclass->addHeaderCell($objLanguage->languageText('rubric_score', 'rubric') , 60);
$tblclass->addHeaderCell(ucfirst($objLanguage->languageText('rubric_teacher', 'rubric')) , 60);
$tblclass->addHeaderCell($objLanguage->languageText('rubric_date', 'rubric') , 60);
$tblclass->endHeaderRow();
// Display the assessments.
//var_dump($assessments);
//var_dump($this->objUser->isContextStudent());
$oddOrEven = "odd";
foreach($assessments as $assessment) {
    $tblclass->startRow();
    $oddOrEven = ($oddOrEven == "even") ? "odd" : "even";
    $tblclass->addCell($assessment['studentno'], "null", "top", "left", $oddOrEven, null);
    // $tblclass->addCell("<b>" . $assessment['studentNo'] . "</b>", "null", "top", "left", $oddOrEven, null);
    $scores = explode(",", $assessment['scores']);
    $total = 0;
    foreach($scores as $score) {
        $total+= $score;
    }
    $tblclass->addCell("<b>" . "$total/$maxtotal" . "</b>", "null", "top", "left", $oddOrEven, null);
    $tblclass->addCell("<b>" . $assessment['teacher'] . "</b>", "null", "top", "left", $oddOrEven, null);
    $tblclass->addCell("<b>" . $assessment['timestamp'] . "</b>", "null", "top", "left", $oddOrEven, null);
    $tblclass->endRow();
}
echo $tblclass->show();
//Get Object
$this->objIcon = &$this->newObject('geticon', 'htmlelements');
$objLayer3 = $this->newObject('layer', 'htmlelements');
$this->objIcon->setIcon('close');
$this->objIcon->extra = " onclick='javascript:window.close()'";
$objLayer3->align = 'center';
$objLayer3->str = $this->objIcon->show();
echo $objLayer3->show();
?>
