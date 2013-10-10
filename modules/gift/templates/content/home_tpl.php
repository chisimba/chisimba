<?php

//load class
$this->loadclass('link', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');

$this->setVarByRef("selected", $departmentname);
$addGift = $this->uri(array('action' => 'submitadd'));
$userExists = $this->uri(array('action' => 'userexists'));
$saveUserUrl = $this->uri(array('action' => 'saveuser'));

$objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
$divisionLabel = $objSysConfig->getValue('DIVISION_LABEL', 'gift');


$objIcon->setIcon('edit');

$homeWelcome = $this->objHome->homePage();

// get the links on the left
$form = $this->objHome->getLeftContent($toSelect, $this->getParam('action'), $this->getParam('archived'));


$heading = new htmlheading($this->objLanguage->languageText('mod_homeWelcome_heading', 'gift'), 1);
$body = $this->objLanguage->languageText('mod_homeWelcome_body', 'gift');
$notice = $this->objLanguage->languageText('mod_homeWelcome_warning', 'gift');
$policy = $this->objLanguage->languageText('mod_home_policylink', 'gift');

$objpolicyLink = new link($this->uri(array('action' => 'viewpolicy')));
$objpolicyLink->link = 'Click here';
//$objLink->extra = 'onClick="showGiftPolicy()"';
$top = "";
$top.=$heading->show() . $objpolicyLink->show() . $policy . '<br/>';

if (isset($errormessage)) {
    $top.='<div class="error"><strong>' . $errormessage . '</strong></div>';
}

if ($this->objUser->isAdmin()) {
    $top.= $this->objGift->showCreateDepartmentForm($editdepartmentname);
}



$editdepartmentlink = new link($this->uri(array("action" => "editdepartment", "id" => $departmentid)));
$objIcon->setIcon('edit');
$editdepartmentlink->link = $objIcon->show();

$deletelink = new link($this->uri(array("action" => "confirmdeletedepartment", "id" => $departmentid)));
$objIcon->setIcon('delete');
$deletelink->link = $objIcon->show();


$edit = "";
$delete = "";
if ($this->objUser->isAdmin()) {
    $edit = $editdepartmentlink->show();
    $delete = $deletelink->show();
}

$top.='<h2 class="departmenthome">' . $departmentname . $edit . $delete . '</h2>';


$filter = new dropdown('filter');
$filter->addOption("By Date");
$filter->addOption("Gift Type");
$filter->addOption("Value");
$filter->addOption("Donor");

$filterbutton = new button('filterbydate', "Search");
$uri = $this->uri(array('action' => 'filter'));
//$button->setOnClick('javascript: window.location=\'' . $uri . '\'');
$filterbutton->setToSubmit();

$form = new form('filterform', $this->uri(array('action' => 'filter')));


$addbutton = new button('addgift', "Add gift");
$uri = $this->uri(array('action' => 'add'));
$addbutton->setOnClick('javascript: window.location=\'' . $uri . '\'');
$form->addToForm($addbutton->show());
$form->addToForm('&nbsp;/&nbsp;&nbsp;Search By:&nbsp;' . $filter->show());
$form->addToForm($filterbutton->show() . '&nbsp;/&nbsp;');


if ($this->objUser->isAdmin()) {

    $button = new button('spreadsheet', "Spreadsheet");
    $uri = $this->uri(array('action' => 'exportospreadsheet'));
    $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
    $form->addToForm('&nbsp;&nbsp;' . $button->show());

    $button = new button('pdf', "PDF");
    $uri = $this->uri(array('action' => 'exportopdf'));
    $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
    $form->addToForm('&nbsp;&nbsp;' . $button->show());


    $button = new button('audittrail', "Audit Trail");
    $uri = $this->uri(array('action' => 'showuseractivity'));
    $button->setOnClick('javascript: window.location=\'' . $uri . '\'');
    $form->addToForm('&nbsp;&nbsp;' . $button->show());
}


$top.=$form->show();

echo $top;

$table = $this->getObject('htmltable', 'htmlelements');
$table->startHeaderRow();
$table->addHeaderCell("Gift Name");
$table->addHeaderCell("Type");
$table->addHeaderCell("Description");

$table->addHeaderCell("Donor");
$table->addHeaderCell("Value (ZAR)&nbsp;", NULL, NULL, "right");
$table->addHeaderCell("Recipient");
$table->addHeaderCell("Date Recieved", NULL, NULL, "center");
$table->endHeaderRow();


if (count($gifts) > 0) {
    foreach ($gifts as $gift) {

        $objIcon->setIcon('edit');
        $editGift = new link($this->uri(array('action' => 'editgift', 'id' => $gift['id'])));
        $editGift->link = $objIcon->show();

        $objIcon->setIcon('delete');
        $deleteGift = new link($this->uri(array('action' => 'confirmdeletegift', 'id' => $gift['id'])));
        $deleteGift->link = $objIcon->show();

        $objIcon->setIcon('redflag');
        $deletedGift = new link($this->uri(array('action' => 'confirmdeletegift', 'id' => $gift['id'])));
        $deletedGift->link = $objIcon->show();

        $edit = "";
        $delete = "";
        if ($this->objUser->isAdmin()) {
            $edit = $editGift->show();
            $delete = $deleteGift->show();
        }
        $deleted = "";
        $class = "";
        if ($gift['deleted'] == 'Y') {
            $deleted=$deletedGift->show();
            $class = "error";
        }
        $viewDetailsLink = new link($this->uri(array('action' => 'view', 'id' => $gift['id'])));
        $viewDetailsLink->link = $gift['giftname'];
        $table->startRow($class);
        $table->addCell($deleted . $viewDetailsLink->show() . $edit );
        $table->addCell($gift['gift_type']);
        $table->addCell($gift['description']);
        $table->addCell($gift["donor"]);
        $value = $this->objGift->formatMoney($gift['value'], TRUE);
        $table->addCell("R" . $value . '&nbsp;&nbsp;', NULL, null, "right");
        $table->addCell($this->objUser->fullname($gift["recipient"]));

        $table->addCell($deleted . $gift["date_recieved"], null, NULL, "center");
        $table->endRow();
    }
}
echo '<br/>';
echo $table->show();
?>