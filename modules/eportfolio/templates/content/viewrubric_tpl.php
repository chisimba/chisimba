<?php
$pageTitle = $this->newObject('htmlheading', 'htmlelements');
$pageTitle->type = 1;
$pageTitle->align = 'left';
$pageTitle->str = $objLanguage->languageText('rubric_rubric', 'rubric') . " : " . $title;
echo $pageTitle->show();
$labelDescription = "<p>" . $description . "</p>";
echo $labelDescription;
// If this is an assessment then display details.
if (isset($IsAssessment)) {
    $objTable = &$this->newObject('htmltable', 'htmlelements');
    $objTable->border = '0';
    $objTable->width = '40%';
    $objTable->cellspacing = '2';
    $objTable->cellpadding = '2';
    $objTable->startRow();
    $objTable->addCell("<b>" . ucfirst($objLanguage->languageText("rubric_teacher", "rubric")) . "</b>");
    $objTable->addCell($teacher);
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell("<b>" . ucfirst($objLanguage->languageText("rubric_studentno", "rubric")) . "</b>");
    $objTable->addCell($studentNo);
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell("<b>" . ucfirst($objLanguage->languageText("rubric_student", "rubric")) . "</b>");
    $objTable->addCell($student);
    $objTable->endRow();
    $objTable->startRow();
    $objTable->addCell("<b>" . $objLanguage->languageText("rubric_datesubmitted", "rubric") . "</b>");
    $objTable->addCell($date);
    $objTable->endRow();
    echo $objTable->show();
}
$table = &$this->newObject("htmltable", "htmlelements");
$table->border = '0';
$table->width = '40%';
$table->cellspacing = '2';
$table->cellpadding = '2';
$table->startRow();
$table->addHeaderCell($objLanguage->languageText("word_objectives", "rubric"));
// Display performances.
for ($j = 0; $j < $cols; $j++) {
    if (!empty($performances[$j])) $table->addHeaderCell($performances[$j]);
}
if (isset($IsAssessment)) {
    $table->addHeaderCell("Score");
}
$table->endRow();
$class = 'odd';
for ($i = 0; $i < $rows; $i++) {
    $table->startRow($class);
    // Display objective.
    $table->addCell($objectives[$i]);
    // Display cells.
    for ($j = 0; $j < $cols; $j++) {
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
    for ($j = 0; $j < ($cols-1); $j++) {
        $table->addCell("&nbsp;");
    }
    $table->addCell($objLanguage->languageText("rubric_total", "rubric") . "&nbsp;", Null, Null, "right");
    $table->addCell("$total/$maxtotal");
    $table->endRow();
}
echo $table->show();
//Get Object
$this->objIcon = &$this->newObject('geticon', 'htmlelements');
$objLayer3 = $this->newObject('layer', 'htmlelements');
$this->objIcon->setIcon('close');
$this->objIcon->extra = " onclick='javascript:window.close()'";
$objLayer3->align = 'center';
$objLayer3->str = $this->objIcon->show();
echo $objLayer3->show();
?>
