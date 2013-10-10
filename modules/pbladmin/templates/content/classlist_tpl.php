<?php
/**
* @package pbladmin
*/

/*
* Template for PBL Administration page.
* The page displays a list of classes with the ability to add, edit or delete classes.
* The user has the option to change the date and/or case assigned to a class.
* @param array $data The list of classes.
* @param array $cases The list of installed cases in the course.
*/

$this->setLayoutTemplate('admin_layout_tpl.php');

// set up html elements
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objLayer = $this->newObject('layer', 'htmlelements');

// Set up language items
$heading = $this->objLanguage->languageText('mod_pbladmin_pbladmin', 'pbladmin');
$classHead = $this->objLanguage->languageText('word_class');
$caseHead = $this->objLanguage->languageText('word_case');
$ownerHead = $this->objLanguage->languageText('word_owner');
$facilitatorHead = $this->objLanguage->languageText('word_facilitator');
$chairHead = $this->objLanguage->languageText('word_chair');
$statusHead = $this->objLanguage->languageText('word_status');
$dateHead = $this->objLanguage->languageText('phrase_startdate');
$viewLabel = $this->objLanguage->languageText('phrase_viewcases');
$virtualLabel = $this->objLanguage->languageText('word_virtual');
$moveLabel = $this->objLanguage->languageText('mod_pbladmin_moveclasstocase', 'pbladmin');
$noCaseLabel = $this->objLanguage->languageText('mod_pbladmin_nocaseinstalled', 'pbladmin');
$changeLabel = $this->objLanguage->languageText('mod_pbladmin_changedateonselected', 'pbladmin');
$pickLabel = $this->objLanguage->languageText('mod_pbladmin_datepick', 'pbladmin');
$goLabel = $this->objLanguage->languageText('word_go');

$addicon = $objIcon->getAddIcon($this->uri(array('action' => 'addclass')));
$heading = $heading.'&nbsp;&nbsp;&nbsp;'.$addicon;

$this->setVarByRef('heading', $heading);

$objLink = new link($this->uri(array('action'=>'showcase')));
$objLink->link = $viewLabel;

echo '<p>'.$objLink->show().'</p>';

$objTable = new htmltable();
$objTable->cellspacing = '2';
$objTable->cellpadding = '5';

$tableHd = array();
$tableHd[] = '';
$tableHd[] = $classHead;
$tableHd[] = $caseHead;
$tableHd[] = $ownerHead;
$tableHd[] = $facilitatorHead;
$tableHd[] = $chairHead;
$tableHd[] = $dateHead;
$tableHd[] = '';

$objTable->addHeader($tableHd);

if(!empty($data)){
    $i = 0;
    foreach($data as $line){
        $class = (($i++ % 2)==0) ? 'even':'odd';
        $facilitator = ''; $chair = '';
        
        // edit / delete icons
        $id = $line['id'];
        $icons = $objIcon->getEditIcon($this->uri(array('action' => 'editclass', 'id' => $id)));
        $icons .= $objIcon->getDeleteIconWithConfirm('', array('action' => 'deleteclass', 'id' => $id), 'pbladmin');
        
        $objLink = new link($this->uri(array('action' => 'editclass', 'id' => $id)));
        $objLink->link = $line['name'];
        $name = $objLink->show();
        // Display the name of the facilitator and chair
        if($line['facilitator'] == 'virtual'){
            $facilitator = $virtualLabel;
        }else if($line['facilitator']){
            $filter = " WHERE id='".$line['facilitator']."'";
            $users = $this->objGroupUser->getUsers(NULL, $filter);
            $facilitator = $users[0]['fullName'];
        }
        if($line['chair']){
            $filter = " WHERE id='".$line['chair']."'";
            $users = $this->objGroupUser->getUsers(NULL, $filter);
            $chair = $users[0]['fullName'];
        }
        
        // Add a checkbox for changing the date or case of a group of classes
        $objCheck = new checkbox('changecase[]');
        $objCheck->setValue($id);
        $checkBtn = $objCheck->show();
        
        $row = array();
        $row[] = $checkBtn;
        $row[] = $name;
        $row[] = $line['casename'];
        $row[] = $this->objUser->fullname($line['owner']);
        $row[] = $facilitator;
        $row[] = $chair;
        $row[] = $this->objDate->formatDate($line['opentime']);
        $row[] = $icons;
        
        $objTable->addRow($row, $class);
    }
}

// Form to change the date and / case for a group of classes
$str = '<p>';

// Change the date
$objLabel = new label($changeLabel.':', 'input_date');
$str .= $objLabel->show();
/*
$objInput = new textinput('date');
$objIcon->title = $pickLabel;
$url = "javascript:show_calendar('document.moveclass.date', document.moveclass.date.value);";
$dateIcon = $objIcon->getLinkedIcon($url, 'select_date');

$str .= '&nbsp;&nbsp;&nbsp;'.$objInput->show().$dateIcon;
*/

$this->objPopupcal = $this->getObject('datepickajax', 'popupcalendar');
$dateField = $this->objPopupcal->show('date', 'yes', 'no', '');
$str .= '&nbsp;&nbsp;&nbsp;'.$dateField;

$objButton = new button('saveDate', $goLabel);
$objButton->setToSubmit();
$objButton->setIconClass("next");
$str .= $objButton->show();

// Change the case
$objLabel = new label($moveLabel.':', 'input_case');
$str .= '<br />'.$objLabel->show();

$objDrop = new dropdown('case');
if(!empty($cases)){
    foreach($cases as $line){
        $objDrop->addOption($line['id'], $line['name']);
    }
}else{
    $objDrop->addOption(NULL, $noCaseLabel);
}

$str .= '&nbsp;&nbsp;&nbsp;'.$objDrop->show();

$objButton = new button('save', $goLabel);
$objButton->setToSubmit();
$objButton->setIconClass("next");
$str .= $objButton->show();

$objLayer->str = '&nbsp;'.$str.'</p>';

// set up form
$objForm = new form('moveclass', $this->uri(array('action'=>'moveclass')));
$objForm->addToForm($objTable->show());
$objForm->addToForm($objLayer->show());

echo $objForm->show();
?>