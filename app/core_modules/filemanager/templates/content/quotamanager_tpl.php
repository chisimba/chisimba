<script type="text/javascript">

function swapUsers(value)
{
    if (value == 'context') {
        jQuery('div.userform').each(function (i) {
            this.style.display = 'none';
        });
        jQuery('div.contextform').each(function (i) {
            this.style.display = 'block';
        });
    } else {
        jQuery('div.userform').each(function (i) {
            this.style.display = 'block';
        });
        jQuery('div.contextform').each(function (i) {
            this.style.display = 'none';
        });
        //jQuery('.userform').css('display', 'block');
        j//Query('.contextform').css('display', 'none');
    }
}

</script>
<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');

$header = new htmlHeading();
$header->str = $this->objLanguage->languageText('mod_filemanager_quotamanager', 'filemanager', 'Quota Manager');
$header->type = 1;

echo $header->show();

$searchType = $this->getParam('searchType', 'users');

if ($searchType == 'context') {
    $searchField = $this->getParam('searchField_context', 'name');
    $orderBy = $this->getParam('orderBy_context', 'quotausage_desc');
    $userDisplay = 'none';
    $contextDisplay = 'block';
} else {
    $searchType = 'users'; // Just to make it explicit!
    $searchField = $this->getParam('searchField_user', 'firstname');
    $orderBy = $this->getParam('orderBy_user', 'quotausage_desc');
    $userDisplay = 'block';
    $contextDisplay = 'none';
}


$form = new form ('quotasearch', $this->uri(array('action'=>'quotamanager')));
$form->method = 'GET';

$hiddenInput = new hiddeninput('module', 'filemanager');
$form->addToForm($hiddenInput->show());

$hiddenInput = new hiddeninput('action', 'quotamanager');
$form->addToForm($hiddenInput->show());

$table = $this->newObject('htmltable', 'htmlelements');

$table->startRow();

$type = new dropdown('searchType');
$type->addOption('users', $this->objLanguage->languageText('mod_filemanager_users', 'filemanager', 'Users'));
$type->addOption('context', ucwords($this->objLanguage->code2Txt('word_courses', 'security', NULL, '[-contexts-]')));
$type->setSelected($searchType);

$type->extra = 'onchange="swapUsers(this.value);"';

$table->addCell($this->objLanguage->languageText('word_type', 'system', 'Type'));
$table->addCell($type->show());

$search1 = new dropdown('searchField_user');
$search1->addOption('firstname', $this->objLanguage->languageText('phrase_firstname', 'system', 'First Name'));
$search1->addOption('surname', $this->objLanguage->languageText('word_surname', 'system', 'Surname'));
$search1->setSelected($this->getParam('searchField_user'));

$div1 = '<div class="userform" style="display: '.$userDisplay.'">'.$search1->show().'</div>';

$search2 = new dropdown('searchField_context');
$search2->addOption('title', ucwords($this->objLanguage->code2Txt('mod_context_contexttitle', 'context', NULL, '[-context-] Title')));
$search2->addOption('contextcode', ucwords($this->objLanguage->code2Txt('mod_context_contextcode', 'context', NULL, '[-context-] Code')));
$search2->setSelected($this->getParam('searchField_context'));

$div2 = '<div class="contextform" style="display: '.$contextDisplay.'">'.$search2->show().'</div>';

$table->addCell($this->objLanguage->languageText('mod_filemanager_searchfield', 'filemanager', 'Search Field'));
$table->addCell($div1.$div2);

$searchFor = new textinput('searchfor', $this->getParam('searchfor'));

$table->addCell($this->objLanguage->languageText('mod_forum_searchfor', 'system', 'Search for'));
$table->addCell($searchFor->show());

$orderBy1 = new dropdown('orderBy_user');
$orderBy1->addOption('quotausage_desc', $this->objLanguage->languageText('mod_filemanager_mostusage', 'filemanager', 'Most Usage'));
$orderBy1->addOption('quotausage', $this->objLanguage->languageText('mod_filemanager_leastusage', 'filemanager', 'Least Usage'));
$orderBy1->addOption('firstname', $this->objLanguage->languageText('phrase_firstname', 'system', 'First Name'));
$orderBy1->addOption('surname', $this->objLanguage->languageText('word_surname', 'system', 'Surname'));
$orderBy1->setSelected($this->getParam('orderBy_user'));

$div1 = '<div class="userform" style="display: '.$userDisplay.'">'.$orderBy1->show().'</div>';

$orderBy2 = new dropdown('orderBy_context');
$orderBy2->addOption('quotausage_desc', $this->objLanguage->languageText('mod_filemanager_mostusage', 'filemanager', 'Most Usage'));
$orderBy2->addOption('quotausage', $this->objLanguage->languageText('mod_filemanager_leastusage', 'filemanager', 'Least Usage'));
$orderBy2->addOption('title', ucwords($this->objLanguage->code2Txt('mod_context_contexttitle', 'context', NULL, '[-context-] Title')));
$orderBy2->addOption('contextcode', ucwords($this->objLanguage->code2Txt('mod_context_contextcode', 'context', NULL, '[-context-] Code')));
$orderBy2->setSelected($this->getParam('orderBy_context'));

$div2 = '<div class="contextform" style="display: '.$contextDisplay.'">'.$orderBy2->show().'</div>';

$table->addCell($this->objLanguage->languageText('mod_filemanager_orderby', 'filemanager', 'Order By'));
$table->addCell($div1.$div2);

$button = new button ('go', $this->objLanguage->languageText('word_go', 'system', 'Go'));
$button->setToSubmit();

$table->addCell($button->show());

$table->endRow();

$form->addToForm($table->show());

echo $form->show();

$objPagination = $this->newObject('pagination', 'navigation');

$numItemsPerPage = 20;

$objPagination->numPageLinks = $this->objQuotas->getNumPages($searchType, $searchField, $this->getParam('searchfor'), $orderBy, $numItemsPerPage);

$objPagination->module = 'filemanager';
$objPagination->action = 'ajaxgetquotas';
$objPagination->extra = array('searchType'=>$searchType, 'searchField'=>$searchField, 'searchFor'=>$this->getParam('searchfor'), 'orderBy'=>$orderBy);

echo $objPagination->show();

?>