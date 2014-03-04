<?php
// This template displays summary details for Graduating Masters Students

//Load HTMl Objet Classes

$objH = $this->newObject('htmlheading', 'htmlelements');
$link =  $this->newObject('link', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$objLayer =$this->newObject('layer','htmlelements');
$this->loadClass('htmltable', 'htmlelements');

//Define the for  Graduates by Department
$table = new htmltable();
$table->cellspacing = '2';
$table->cellpadding = '5';

//Define the for  Graduates by Faculty
$table2 = new htmltable();
$table2->cellspacing = '2';
$table2->cellpadding = '5';

//setup the table headings
$h3 = $this->getObject('htmlheading', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');

$h3->str =$this->objLanguage->languageText('mod_rimfhe_universitytotaloutput', 'rimfhe');

$objLayer->str = $h3->show();
$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
$header = $objLayer->show();

$display = '<p>'.$header.'</p><hr />';

//Show Header
echo $display;

//Description for Headings
$sumArticle = $this->objLanguage->languageText('mod_rimfhe_totaljournalarticle', 'rimfhe');
$sumBooks = $this->objLanguage->languageText('mod_rimfhe_totalbooks', 'rimfhe', '');
$sumChapterInBook = $this->objLanguage->languageText('mod_rimfhe_totalchapterinbook', 'rimfhe');
$sumDoctoral = $this->objLanguage->languageText('mod_rimfhe_totaldoctoralstudents', 'rimfhe');
$sumMasters = $this->objLanguage->languageText('mod_rimfhe_totalmastersstudents', 'rimfhe');
$OveralTotral = $this->objLanguage->languageText('mod_rimfhe_grandtotal', 'rimfhe', 'TOTAL');

$rowcount = 0;

//setup the tables rows  and loop though the records
//if (!empty($totalArticles) || !empty($totalBooks) || !empty($totalChapterInBook) || !empty($totalDoctoralStudents) || !empty($totalMastersStudents)) {
if (empty($totalArticles)){
    $totalArticles = 0;
}
if (empty($totalBooks)){
    $totalBooks = 0;
}
if (empty($totalChapterInBook)){
    $totalChapterInBook = 0;
}
if (empty($totalDoctoralStudents)){
    $totalDoctoralStudents = 0;
}
if (empty($totalMastersStudents)){
    $totalMastersStudents = 0;
}

$articles= '<span style="color:red;font-size:12px;">'.$sumArticle.'</span>:&nbsp;&nbsp;<strong>'.$totalArticles.'</strong><br />';
$books= '<span style="color:red;font-size:12px;">'.$sumBooks.'</span>:&nbsp;&nbsp;<strong>'.$totalBooks.'</strong><br />';
$chapters= '<span style="color:red;font-size:12px;">'.$sumChapterInBook.'</span>:&nbsp;&nbsp;<strong>'.$totalChapterInBook.'</strong><br />';
$doctoral= '<span style="color:red;font-size:12px;">'.$sumDoctoral.'</span>:&nbsp;&nbsp;<strong>'.$totalDoctoralStudents.'</strong><br />';
$masters= '<span style="color:red;font-size:12px;">'.$sumMasters.'</span>:&nbsp;&nbsp;<strong>'.$totalMastersStudents.'</strong><br />';

$total= '<span style="color:red;font-size:12px;">'.$OveralTotral.'</span>:&nbsp;&nbsp;<strong>'.($totalArticles+$totalBooks+$totalChapterInBook+$totalDoctoralStudents+$totalMastersStudents).'</strong><br />';
//}
/*else{
echo  '<div class="noRecordsMessage">'.$objLanguage->languageText('mod_rimfhe_norecord', 'rimfhe').'</div>';

}
*/
//Header for $table

echo $articles;


echo '<br /><br />';
echo $books;

echo '<br /><br />';
echo $chapters;

echo '<br /><br />';
echo $doctoral;

echo '<br /><br />';
echo $masters;

echo '<br /><br />';
echo $total;

echo '<br /><br />';
?>


