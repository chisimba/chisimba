<?php
$this->loadClass('htmlheading', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objFaculties = $this->newObject('faculties');
$header = new htmlheading();
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_apo_facultymanagement', 'apo');

echo $header->show();

echo $objFaculties->showCreateFacultiesForm();

$table = $this->getObject("htmltable", "htmlelements");
$table->startHeaderRow();
$table->addHeaderCell("Faculty");
$table->addHeaderCell("Administrator");
$table->addHeaderCell("Telephone");
$table->addHeaderCell("Actions");
$table->endHeaderRow();

$objIcon->setIcon('edit');
$objIcon->alt = $this->objLanguage->languageText('mod_apo_editfaculty', 'apo', 'Edit Faculty');
$objIcon->title = $this->objLanguage->languageText('mod_apo_editfaculty', 'apo', 'Edit Faculty');
$editIcon = $objIcon->show();

$objIcon->setIcon('delete');
$objIcon->alt = $this->objLanguage->languageText('mod_apo_deletefaculty', 'apo', 'Delete Faculty');
$objIcon->title = $this->objLanguage->languageText('mod_apo_deletefaculty', 'apo', 'Delete Faculty');
$deleteIcon = $objIcon->show();

if (count($faculties) > 0) {
    foreach ($faculties as $faculty) {
        $table->startRow();
        $table->addCell($faculty['name']);
        $table->addCell($faculty['contact_person']);
        $table->addCell($faculty['telephone']);

        $editOption = new link ($this->uri(array('action'=>'editfaculty', "selected" => $selected, 'id'=>$faculty['id'])));
        $editOption->link = $editIcon;
        $edit = $editOption->show();

        $deleteLink = new link ($this->uri(array('action'=>'deletefaculty', "selected" => $selected, 'id'=>$faculty['id'])));
        $deleteLink->link = $deleteIcon;
        $delete = $deleteLink->show();

        $table->addCell($edit.' &nbsp; '.$delete, 100);
        $table->endRow();
    }
}
echo "<br><br>";
echo $table->show();
?>