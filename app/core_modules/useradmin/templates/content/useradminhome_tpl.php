<?php
$this->setVar('pageSuppressXML', TRUE);

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('checkbox', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('button', 'htmlelements');

$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('ok', 'png');
$objIcon->alt = $this->objLanguage->languageText('word_yes', 'system');
$objIcon->title = $this->objLanguage->languageText('word_yes', 'system');
$yesIcon = $objIcon->show();

$objIcon->setIcon('failed', 'png');
$objIcon->alt = $this->objLanguage->languageText('word_no', 'system');
$objIcon->title = $this->objLanguage->languageText('word_no', 'system');
$noIcon = $objIcon->show();

$objIcon->setIcon('edit');
$objIcon->alt = 'Edit';
$objIcon->title = 'Edit';
$editIcon = $objIcon->show();

$objIcon->setIcon('add');
$objIcon->align = 'top';
$objIcon->alt = 'Add User';
$objIcon->title = 'Add User';

$link = new link($this->uri(array('action'=>'adduser')));
$link->link = $objIcon->show();

$header = new htmlheading();
$header->str = $headerTitle.' '.$link->show();
$header->type = 1;
echo $header->show();

$form = new form ('search', $this->uri(array('action'=>'searchusers')));
$form->method = 'get';

$module = new hiddeninput('module', $this->getParam('module'));
$form->addToForm($module->show());

$action = new hiddeninput('action', 'searchusers');
$form->addToForm($action->show());
$dropdown = new dropdown('searchfield');
$dropdown->addOption('staffnumber', 'Staff / Student No.');
$dropdown->addOption('username', 'Username');
$dropdown->addOption('firstname', 'Firstname');
$dropdown->addOption('surname', 'Surname');
$dropdown->setSelected($searchField);

$position = new dropdown('position');
$position->addOption('startswith', 'Starts with');
$position->addOption('endswith', 'Ends with');
$position->addOption('contains', 'Contains');
$position->setSelected($this->getSession('position', 'startswith'));

$label = new label('Search in ', 'input_searchfield');
$textinput = new textinput('searchquery');
$textinput->size = 50;
$textinput->value = $searchValue;
$textinputLabel = new label(' for ', 'input_searchquery'); 

$button = new button ('search', 'Search');
$button->setToSubmit();

$form->addToForm('<p><strong>Search for Users</strong>: '.$label->show().$dropdown->show().' &nbsp; '.$position->show().$textinput->show().' '.$button->show().'</p>');
echo $form->show();


$objAlphabet = $this->getObject('alphabet','navigation');

if ($mode == 'useradmin') {
    $objAlphabet->highlightedItem = $letter;
}

$linkarray=array('action'=>'viewbyletter', 'letter'=>'LETTER', 'field'=>$field);
$url=$this->uri($linkarray,'useradmin');

$dropdown = new dropdown('field');
$dropdown->addOption('firstname', 'Firstname');
$dropdown->addOption('surname', 'Surname');
$dropdown->addOption('username', 'Username');
$dropdown->setSelected($field);
$dropdown->extra = 'onchange="document.forms[\'changefield\'].submit();"';

$hiddeninput = new hiddeninput('letter', $letter);

$browserByLabel = new label ('Browse by', 'input_field');

$form = new form ('changefield', $this->uri(array('action'=>'changefield')));
    
$form->addToForm('<p>'.$browserByLabel->show().' :'.$dropdown->show().$hiddeninput->show().' '.$objAlphabet->putAlpha($url, TRUE, $this->objLanguage->languageText('mod_useradmin_listallusers','useradmin')).'</p>');	

echo $form->show();


$form = new form ('batchprocess', $this->uri(array('action'=>'batchprocess')));

$table = $this->newObject('htmltable', 'htmlelements');

$table->startHeaderRow();
    $table->addHeaderCell('&nbsp;');
    $table->addHeaderCell('Staff/Stud No.');
    $table->addHeaderCell('Username');
    $table->addHeaderCell('Title');
    $table->addHeaderCell('Firstname');
    $table->addHeaderCell('Surname');
    $table->addHeaderCell('Email');
    $table->addHeaderCell('LDAP', 50);
    $table->addHeaderCell('Active', 50);
    $table->addHeaderCell('&nbsp;', 25);
$table->endHeaderRow();

if (is_array($users) && count($users) > 0) {
    foreach ($users as $user)
    {
        $table->startRow();
            $checkbox = new checkbox('users[]');
            $checkbox->value = $user['id'];
            $checkbox->cssId = 'checkbox_'.sha1($user['userid']);
            $table->addCell($checkbox->show());
            
            $label = new label($user['staffnumber'], 'checkbox_'.sha1($user['userid']));
            $table->addCell($label->show());
            
            $link = new link ($this->uri(array('action'=>'userdetails', 'id'=>$user['id'])));
            $link->link = $user['username'];
            $table->addCell($link->show());
            
            $label = new label($user['title'], 'checkbox_'.sha1($user['userid']));
            $table->addCell($label->show());
            
            $label = new label($user['firstname'], 'checkbox_'.sha1($user['userid']));
            $table->addCell($label->show());
            
            $label = new label($user['surname'], 'checkbox_'.sha1($user['userid']));
            $table->addCell($label->show());
            
            $emailLink = new link('mailto:'.$user['emailaddress']);
            $emailLink->link = $user['emailaddress'];
            $table->addCell($emailLink->show());
            if ($user['howcreated'] == 'LDAP') {
                $table->addCell($yesIcon);
            } else {
                $table->addCell($noIcon);
            }
            if ($user['isactive'] == '0') {
                $table->addCell($noIcon);
            } else {
                $table->addCell($yesIcon);
            }
            
            
            $link->link = $editIcon;
            $table->addCell($link->show());
            
        $table->endRow();
    }
} else {
    $table->startRow();
    $table->addCell('No Users found matching search criteria', NULL, NULL, NULL, 'noRecordsMessage', 'colspan="7"');
    $table->endRow();
}

$form->addToForm($table->show());

// Do not show batch options if there is no user
if (is_array($users) && count($users) > 0) {
    $batchOptions = new dropdown('option');
    $batchOptions->addOption('-', '[-Select One-]');
    $batchOptions->addOption('inactive', 'Set Accounts as Inactive');
    $batchOptions->addOption('active', 'Set Accounts as Active');
    $batchOptions->addOption('delete', 'Delete Accounts');
    $batchOptions->addOption('ldap', 'Convert Accounts to use Network Identification');

    $batchLabel = new label ('With Selected: ', 'input_option');

    $button = new button ('process', 'Go');
    $button->setOnClick('doBatchProcess();');
    //$button->setToSubmit();

    $form->addToForm('<strong>'.$batchLabel->show().'</strong>'.$batchOptions->show().' '.$button->show());

    if ($mode == 'search') {
        $hiddenMode = new hiddeninput('mode', 'search');
    } else {
        $hiddenMode = new hiddeninput('mode', 'useradmin');
    }

    $form->addToForm($hiddenMode->show());
}
echo $form->show();

$addlink = new link($this->uri(array('action'=>'adduser')));
$addlink->link = 'Add New User';

echo '<p>'.$addlink->show().'</p>';

// Error Messages
if ($this->getParam('error') != '') {
    switch ($this->getParam('error'))
    {
        case 'noidgiven': 
            $this->addMessage('You tried to view details of a user without providing an id');
            break;
        case 'userviewdoesnotexist':
            $this->addMessage('You tried to view details of a user that does not exist');
            break;
        default: break;
    }
    // userdetailsupdate
    //resetimage
    //changepicture
    
    $this->putMessages();
}
?>
<script type="text/javascript" language="javascript">
//<![CDATA[
function doBatchProcess()
{
    if (document.getElementById('input_option').value == '-')
    {
        alert('Please select an action');
        document.getElementById('input_option').focus();
    } else if (document.getElementById('input_option').value == 'delete')
    {
        if(confirm('Are you sure you want to delete these users?'))
		{
            document.getElementById('form_batchprocess').submit();
        }
    } else {
        document.getElementById('form_batchprocess').submit();
    }
}
//]]>
</script>