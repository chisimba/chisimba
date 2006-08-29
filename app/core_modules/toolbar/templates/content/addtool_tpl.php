<?php
/**
* @package toolbar
*/

/**
* Template to add or edit a module link on the toolbar
*/

$this->setLayoutTemplate('admin_layout_tpl.php');

// set up html elements
$objTable =& $this->newObject('htmltable', 'htmlelements');
$objHead =& $this->newObject('htmlheading', 'htmlelements');
$objForm =& $this->newObject('form', 'htmlelements');
$objInput =& $this->newObject('textinput', 'htmlelements');
$objLabel =& $this->newObject('label', 'htmlelements');
$objDrop =& $this->newObject('dropdown', 'htmlelements');
$objCheck =& $this->newObject('checkbox', 'htmlelements');
$objButton =& $this->newObject('button', 'htmlelements');

// set up language items
$objLanguage =& $this->getObject('language', 'language');
$addHeading = $objLanguage->languageText('mod_toolbar_addnewlink', 'toolbar','Add New Link');
$editHeading = $objLanguage->languageText('mod_toolbar_editlink', 'toolbar','Edit Link');
$moduleLabel = $objLanguage->languageText('mod_toolbar_module', 'toolbar','Module');
$settingsLabel = $objLanguage->languageText('mod_toolbar_modulesettings','toolbar', 'Module Settings');
$toolbarLabel = $objLanguage->languageText('mod_toolbar_toolbar', 'toolbar','Toolbar');
$selectLabel = $objLanguage->languageText('mod_toolbar_selectcategory','toolbar','Select Category');
$adminLabel = $objLanguage->languageText('mod_toolbar_adminonly', 'toolbar','Admin Only');
$dependsLabel = $objLanguage->languageText('mod_toolbar_dependscontext','toolbar', 'Depends Context');
$saveLabel = $objLanguage->languageText('word_save','security', 'Save');
$backLabel = $objLanguage->languageText('word_back','security', 'Back');
$setPermLabel = $objLanguage->languageText('mod_toolbar_confperm', 'toolbar','Configure link Permissions');
$linkPermLabel = $objLanguage->languageText('mod_toolbar_linkpermissions','toolbar', 'Link Permissions');

// categories for dropdown list
$organise = $objLanguage->languageText('category_organise', 'toolbar','Organise');
$communicate = $objLanguage->languageText('category_communicate','toolbar', 'Communicate');
$learn = $objLanguage->languageText('category_learn','toolbar', 'Learn');
$admin = $objLanguage->languageText('category_admin','toolbar', 'Admin');
$about = $objLanguage->languageText('category_about','toolbar', 'About');
$postgrad = $objLanguage->languageText('category_postgrad', 'toolbar','Postgraduate');

// Added for testing purposes.
$user = $objLanguage->languageText('category_user','security', 'User');
$course = $objLanguage->languageText('category_course','security', 'Course');
$assessment = $objLanguage->languageText('category_assessment','security', 'Assessment');
$site = $objLanguage->languageText('category_site','security', 'Site');

if($mode == 'edit'){
    $dependsContext = $data['dependscontext'];
    $adminOnly = $data['adminonly'];
    $permissions = $data['permissions'];
}else{
    $dependsContext = 0;
    $adminOnly = 0;
    $permissions = '';

    // set default permissions
    if(isset($modData) && !empty($modData)){
        $dAcl = ''; $dGroup = ''; $dCon = '';
        if(isset($modData['acls'][0]) && !empty($modData['acls'][0])){
            foreach($modData['acls'] as $val){
                if(!empty($dAcl)){
                    $dAcl .= ',';
                }
                $dAcl .= $val;
            }
        }
        if(isset($modData['groups'][0]) && !empty($modData['groups'][0])){
            foreach($modData['groups'] as $val){
                if(!empty($dGroup)){
                    $dGroup .= ',';
                }
                $dGroup .= $val;
            }
        }
        if(isset($modData['cons'][0]) && !empty($modData['cons'][0])){
            foreach($modData['cons'] as $val){
                if(!empty($dCon)){
                    $dCon .= ',';
                }
                $dCon .= $val;
            }
        }
        $permissions = $dAcl.'|'.$dGroup.'|_con_'.$dCon;
        if(isset($modData['isContext'])){
            $dependsContext = $modData['isContext'];
        }
        if(isset($modData['isAdmin'])){
            $adminOnly = $modData['isAdmin'];
        }
    }
}

$objHead->str = $addHeading;
if($mode == 'edit'){
    $objHead->str = $editHeading;
}
$objHead->type = 1;

$str = $objHead->show();
$str .= '<p><b>'.$moduleLabel.':</b>&nbsp;&nbsp;'.$moduleName.'</p>';

$objTable->init();
$objTable->width = '99%';
$objTable->cellpadding = 5;

$objTable->startRow();
$objTable->addCell('', '30%');
$objTable->addCell('', '40%');
$objTable->addCell('', '10%');
$objTable->addCell('', '20%');
$objTable->endRow();

$objHead->str = $toolbarLabel;
$objHead->type = 3;

$objTable->startRow();
$objTable->addCell($objHead->show(), '', '','','','colspan="4"');
$objTable->endRow();

// List of available categories
$objLabel = new label($selectLabel, 'input_category');

$objDrop = new dropdown('category');
$objDrop->addOption('organise', $organise);
$objDrop->addOption('communicate', $communicate);
$objDrop->addOption('learn', $learn);
$objDrop->addOption('admin', $admin);
$objDrop->addOption('about', $about);
$objDrop->addOption('postgrad', $postgrad);
$objDrop->addOption('user', $user);
$objDrop->addOption('course', $course);
$objDrop->addOption('assessment', $assessment);
$objDrop->addOption('site', $site);

if($mode == 'edit'){
    $cat = $data['category'];
    $objDrop->setSelected($cat);
}
$objTable->addRow(array($objLabel->show(), $objDrop->show()));

$objHead->str = $settingsLabel;
$objHead->type = 3;

$objTable->startRow();
$objTable->addCell($objHead->show(), '', '','','','colspan="4"');
$objTable->endRow();

// Admin only module
$objLabel = new label($adminLabel, 'input_adminOnly');

$objCheck = new checkbox('adminOnly');
$objCheck->setChecked($adminOnly);
$objTable->addRow(array($objLabel->show(), $objCheck->show()));

// Context dependent module
$objLabel = new label($dependsLabel, 'input_dependsContext');

$objCheck = new checkbox('dependsContext');
$objCheck->setChecked($dependsContext);
$objTable->addRow(array($objLabel->show(), $objCheck->show()));

// Link Permissions
$objHead->str = $linkPermLabel;
$objHead->type = 3;

$objTable->startRow();
$objTable->addCell($objHead->show(), '', '','','','colspan="4"');
$objTable->endRow();

$objLink = new link('javascript:void(0)');
$objLink->link = $setPermLabel;
$objLink->extra = "onclick = \"javascript:window.open('". $this->uri(array('action'=>'setperm', 'modulename'=>$moduleName), '', '', TRUE)."', 'setperms', 'width=800, height=600, scrollbars')\"";

$objTable->startRow();
$objTable->addCell($objLink->show(), '', '','','','colspan="4"');
$objTable->endRow();

$formElements = '';

// hidden elements: id, module, permissions
if($mode == 'edit'){
    $objInput = new textinput('id', $data['id']);
    $objInput->fldType = 'hidden';
    $formElements .= $objInput->show();
}
$objInput = new textinput('permissions', $permissions);
$objInput->fldType = 'hidden';
$formElements .= $objInput->show();

$objInput = new textinput('moduleName', $moduleName);
$objInput->fldType = 'hidden';
$formElements .= $objInput->show();

// submit buttons
$objButton = new button('save', $saveLabel);
$objButton->setToSubmit();

$btns = '<p>'.$objButton->show();

$objButton = new button('save', $backLabel);
$objButton->setToSubmit();

$btns .= '&nbsp;&nbsp;&nbsp;'.$objButton->show().'</p>';
$objTable->addRow(array($btns));

// form
$objForm = new form('menulink', $this->uri(array('action'=>'savetool')));
$objForm->addToForm($formElements);
$objForm->addToForm($objTable->show());

$str .= $objForm->show();

echo $str;
?>