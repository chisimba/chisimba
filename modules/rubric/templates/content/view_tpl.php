<?php
$ret = "";
$pageTitle = $this->newObject('htmlheading','htmlelements');
$pageTitle->type=1;
$pageTitle->align='left';
$pageTitle->str=$objLanguage->languageText('rubric_rubric','rubric') . " : " . $title ;
$ret .= $pageTitle->show();

$labelDescription = "<p>" . $description . "</p>";

$ret .= $labelDescription;

// If this is an assessment then display details.
if (isset($IsAssessment)) {
    $objTable =& $this->newObject('htmltable','htmlelements');
    $objTable->border = '0';
    $objTable->width='40%';
    $objTable->cellspacing='2';
    $objTable->cellpadding='2';

    $objTable->startRow();
    $objTable->addCell("<b>".ucfirst($objLanguage->code2Txt("rubric_teacher","rubric"))."</b>");
    $objTable->addCell($teacher);
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell("<b>".ucfirst($objLanguage->code2Txt("rubric_studentno","rubric"))."</b>");
    $objTable->addCell($studentNo);
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell("<b>".ucfirst($objLanguage->code2Txt("rubric_student","rubric"))."</b>");
    $objTable->addCell($student);
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell("<b>".$objLanguage->languageText("rubric_datesubmitted","rubric")."</b>");
    $objTable->addCell($date);
    $objTable->endRow();
    $ret .= $objTable->show();
}
$table =& $this->newObject("htmltable","htmlelements");
$table->border = '0';
$table->width = '40%';
$table->cellspacing='2';
$table->cellpadding='2';
$table->startRow();
$table->addHeaderCell("&nbsp;");
// Display performances.
for ($j=0;$j<$cols;$j++) {
    $table->addHeaderCell($performances[$j]);
}
if (isset($IsAssessment)) {
    $table->addHeaderCell("Score");
}
$table->endRow();
$class = 'odd';
for ($i=0;$i<$rows;$i++) {
    $table->startRow($class);
    // Display objective.
    $table->addCell($objectives[$i]);
    // Display cells.
    for ($j=0;$j<$cols;$j++) {
        $table->addCell($cells[$i][$j]);
    }
    if (isset($IsAssessment)) {
        $table->addCell($scores[$i]);
    }
    $table->endRow();
    $class = $class == 'odd' ? 'even' : 'odd';
}
// If this is an assessment display the total score.
if (isset($IsAssessment)) {
    $table->startRow();
    $table->addCell("&nbsp;");
    for ($j=0;$j<($cols-1);$j++) {
        $table->addCell("&nbsp;");
    }
    $table->addCell($objLanguage->languageText("rubric_total","rubric") . "&nbsp;", Null, Null, "right");
    $table->addCell("$total/$maxtotal");
    $table->endRow();
}
$ret .= $table->show();
if ($noBanner == "yes") {
    $ret .= "<a href=\"javascript:history.back();\">" . "Back" . "</a>";
}
else {
    // Print the page.
    $ret .= "<a href=\"javascript:window.print();\">" . $objLanguage->languageText("word_print") . "</a>";
    $ret .= "&nbsp;";
    if (isset($IsAssessment)) {
        $ret .= "<a href=\"" .
            $this->uri(array(
                'module'=>'rubric',
                'action'=>'assessments',
                'tableId'=>$tableId
            ))
        . "\">" . $objLanguage->languageText("word_back") . "</a>"; //rubric_returntomainmenu
    }
    else {
        $ret .= "<a href=\"" .
            $this->uri(array(
                'module'=>'rubric',
            ))
        . "\">" . $objLanguage->languageText("word_back") . "</a>"; //rubric_returntomainmenu
    }
}
echo "<div class='rubric_view'>$ret</div>";
?>