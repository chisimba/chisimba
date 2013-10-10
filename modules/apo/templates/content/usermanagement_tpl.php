<?php

$this->loadClass('htmlheading', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objUsers = $this->newObject('users');
$header = new htmlheading();
$header->type = 2;
$header->str = $this->objLanguage->languageText('mod_apo_usermanagement', 'apo');

echo $header->show();

echo $objUsers->showUserForm($departments);


$table = $this->getObject("htmltable", "htmlelements");
$table->startHeaderRow();
$table->addHeaderCell("Name");
$table->addHeaderCell("Role");
//$table->addHeaderCell("Department");
$table->addHeaderCell("Email");
$table->addHeaderCell("Telephone");
$table->addHeaderCell("Actions");
$table->endHeaderRow();

$objIcon->setIcon('edit');
$objIcon->alt = $this->objLanguage->languageText('mod_apo_edituser', 'apo', 'Edit User');
$objIcon->title = $this->objLanguage->languageText('mod_apo_edituser', 'apo', 'Edit User');
$editIcon = $objIcon->show();

$objIcon->setIcon('delete');
$objIcon->alt = $this->objLanguage->languageText('mod_apo_deleteuser', 'apo', 'Delete User');
$objIcon->title = $this->objLanguage->languageText('mod_apo_deleteuser', 'apo', 'Delete User');
$deleteIcon = $objIcon->show();

if (count($users) > 0) {
    foreach ($users as $user) {
        $table->startRow();
        $table->addCell($user['name']);
        $table->addCell($user['role']);
        //$table->addCell($user['department']);
        $table->addCell($user['email']);
        $table->addCell($user['telephone']);

        $editOption = new link($this->uri(array('action' => 'edituser', "selected" => $selected, 'id' => $user['id'])));
        $editOption->link = $editIcon;
        $edit = $editOption->show();

        $deleteLink = new link($this->uri(array('action' => 'deleteuser', "selected" => $selected, 'id' => $user['id'])));
        $deleteLink->link = $deleteIcon;
        $delete = $deleteLink->show();

        $table->addCell($edit . ' &nbsp; ' . $delete, 100);
        $table->endRow();
    }
}
echo "<br><br>";
echo $table->show();
?>