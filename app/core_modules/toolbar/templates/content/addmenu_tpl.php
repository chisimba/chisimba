<?php
/**
* @package toolbar
*/

/**
* Template to add or edit a module link on the toolbar
* @param string $moduleName The name of the module
* @param string $edit Add or edit action
* @param bool $page Page or Side menu
* @param array $data The module data
*/

$this->setLayoutTemplate('admin_layout_tpl.php');

// set up html elements
$objTable = $this->loadClass('htmltable', 'htmlelements');
$objHead = $this->loadClass('htmlheading', 'htmlelements');
$objForm = $this->loadClass('form', 'htmlelements');
$objInput = $this->loadClass('textinput', 'htmlelements');
$objLabel = $this->loadClass('label', 'htmlelements');
$objDrop = $this->loadClass('dropdown', 'htmlelements');
$objCheck = $this->loadClass('checkbox', 'htmlelements');
$objLink = $this->loadClass('link', 'htmlelements');
$objButton = $this->loadClass('button', 'htmlelements');

// set up language items
$objLanguage = $this->getObject('language', 'language');
$addHeading = $objLanguage->languageText('mod_toolbar_addnewlink','toolbar','Add New Link');
$editHeading = $objLanguage->languageText('mod_toolbar_editlink','toolbar', 'Edit Link');
$moduleLabel = $objLanguage->languageText('mod_toolbar_module','toolbar', 'Module');
$linkLabel = $objLanguage->languageText('mod_toolbar_linksettings','toolbar', 'Link Settings');
$settingsLabel = $objLanguage->languageText('mod_toolbar_modulesettings','toolbar', 'Module Settings');
$linkPermLabel = $objLanguage->languageText('mod_toolbar_linkpermissions','toolbar', 'Link Permissions');
$sidemenuLabel = $objLanguage->languageText('mod_toolbar_sidemenu','toolbar', 'Side Menu');
$pageLabel = $objLanguage->languageText('mod_toolbar_page','toolbar', 'Page');
$menuLabel = $objLanguage->languageText('mod_toolbar_menu','toolbar', 'Menu');
$categoryLabel = $objLanguage->languageText('mod_toolbar_selectcategory','toolbar', 'Select Category');
$positionLabel = $objLanguage->languageText('mod_toolbar_positioninmenu','toolbar', 'Position in Menu');
$topLabel = $objLanguage->languageText('mod_toolbar_top','toolbar', 'Top');
$uppermiddleLabel = $objLanguage->languageText('mod_toolbar_uppermiddle','toolbar', 'Upper Middle');
$middleLabel = $objLanguage->languageText('mod_toolbar_middle','toolbar', 'Middle');
$lowermiddleLabel = $objLanguage->languageText('mod_toolbar_lowermiddle', 'toolbar','Lower Middle');
$bottomLabel = $objLanguage->languageText('mod_toolbar_bottom','toolbar', 'Bottom');
$actionLabel = $objLanguage->languageText('mod_toolbar_action','toolbar', 'Action');
$iconLabel = $objLanguage->languageText('mod_toolbar_icon','toolbar', 'Icon');
$langCodeLabel = $objLanguage->languageText('mod_toolbar_langcode','toolbar', 'Language Code');
$adminLabel = $objLanguage->languageText('mod_toolbar_adminonly','toolbar', 'Admin Only');
$dependsLabel = $objLanguage->languageText('mod_toolbar_dependscontext','toolbar', 'Depends Context');
$permLabel = $objLanguage->languageText('mod_toolbar_permissions','toolbar', 'Permissions');
$siteLabel = $objLanguage->languageText('mod_toolbar_settosite', 'toolbar','Display to everyone');
$setPermLabel = $objLanguage->languageText('mod_toolbar_confperm','toolbar', 'Configure link Permissions');
$saveLabel = $objLanguage->languageText('word_save', 'security','Save');
$backLabel = $objLanguage->languageText('word_back','security', 'Back');

$catUsers = $objLanguage->languageText('mod_toolbar_users','toolbar', 'Manage Users');
$catDevelop = $objLanguage->languageText('mod_toolbar_develop','toolbar', 'For Developers');
$catSite = $objLanguage->languageText('mod_toolbar_site','toolbar', 'Manage Site');
$catOrganise = $objLanguage->languageText('mod_toolbar_organise','toolbar', 'Manage Organisers');
$catContent = $objLanguage->languageText('mod_toolbar_content', 'toolbar','Manage Content');
$catAssign = $objLanguage->languageText('mod_contextadmin_assignment','toolbar', 'Manage Assignments and Tests');

$javascript = "<script language='javascript'>
    function changeOptions()
    {
        var selected = document.menulink.menu.options[document.menulink.menu.selectedIndex].value;
        if(selected == 'admin'){
            document.menulink.position.options[3] = null;
            document.menulink.position.options[3] = new Option('".$catSite."', 'site');
            document.menulink.position.options[4] = new Option('".$catDevelop."', 'develop');
        }else if(selected == 'manage'){
            document.menulink.position.options[3] = null;
            document.menulink.position.options[4] = null;
            document.menulink.position.options[3] = new Option('".$catSite."', 'site');
        }else{
            document.menulink.position.options[3] = null;
            document.menulink.position.options[4] = null;
            document.menulink.position.options[3] = new Option('".$catAssign."', 'assign');
        }
    }
</script>";

echo $javascript;

if($mode == 'edit'){
    $admin = $data['adminonly'];
    $depends = $data['dependscontext'];
    $permissions = $data['permissions'];
    $allSite = 0;

    $perms = explode('|', $data['permissions']);
    if(isset($perms[1]) && !empty($perms[1])){
        if($perms[1] == 'site'){
            $allSite = 1;
        }
    }
    
    $groups = NULL;
    $congroups = NULL;
    $acls = NULL;
    
    $array = explode('|', $data['category']);
    
    if($page){
        $action = $array[1];
        $icon = $array[2];
        $code = $array[3];
        
        $array2 = explode('_', $array[0]);
        $menu = $array2[1];
        $category = $array2[2];        
    }else{   
        $perm = $array[1];
        $action = $array[2];
        $icon = $array[3];
        $code = $array[4];
        
        $array2 = explode('-', $array[0]);
        $menu = str_replace('menu_', '', $array2[0]);
        $position = $array2[1];
    }
}else{
    // set default values
    $admin = 0;
    $depends = 0;
    $menu = '';
    $category = '';
    $position = 2;
    $perm = '';
    $action = '';
    $icon = '';
    $code = '';
    $allSite = 0;
    $groups = NULL;
    $congroups = NULL;
    $acls = NULL;
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
        $admin = $modData['isAdmin'];
        if(isset($modData['isContext'])){
            $depends = $modData['isContext'];
        }
    }
}
$objHead = new htmlHeading();

$objHead->str = $addHeading;
if($mode == 'edit'){
    $objHead->str = $editHeading;
}
$objHead->type = 1;

$str = $objHead->show();

$str .= '<p><b>'.$moduleLabel.':</b>&nbsp;&nbsp;'.$moduleName.'</p>';

$objTable = new htmlTable();
$objTable->width = '99%';
$objTable->cellpadding = 5;

$objHead->str = $linkLabel;
$objHead->type = 3;

$objTable->startRow();
$objTable->addCell($objHead->show(), '', '','','','colspan="4"');
$objTable->endRow();

// Available side menus
$objDrop = new dropdown('menu');

if($page){
    $objLabel = new label($pageLabel, 'input_menu');

    $objDrop->extra = 'onChange = "javascript:changeOptions()"';
    $objDrop->addOption('lecturer', 'lecturer');
    $objDrop->addOption('admin', 'admin');
    $objDrop->addOption('manage', 'manage');
}else{
    $objLabel = new label($sidemenuLabel, 'input_menu');

    $objDrop->addOption('user', 'user');
    $objDrop->addOption('alumni', 'alumni');
    $objDrop->addOption('context', 'context');
    $objDrop->addOption('postlogin', 'postlogin');
    $objDrop->addOption('postgrad', 'postgrad');
}
$objDrop->setSelected($menu);

$label1 = $objLabel->show();
$drop1 = $objDrop->show();

if($page){
    $objLabel = new label($categoryLabel, 'input_position');
    
    $objDrop = new dropdown('position');
    $objDrop->addOption('users', $catUsers);
    $objDrop->addOption('content', $catContent);
    $objDrop->addOption('organise', $catOrganise);
    
    if($menu == 'admin'){
        $objDrop->addOption('site', $catSite);
        $objDrop->addOption('develop', $catDevelop);
    }else if($menu == 'manage'){
        $objDrop->addOption('site', $catSite);
    }else{
        $objDrop->addOption('assign', $catAssign);
    }
    
    $objDrop->setSelected($category);
}else{
    $objLabel = new label($positionLabel, 'input_position');
    
    $objDrop = new dropdown('position');
    $objDrop->addOption(1, $topLabel);
    $objDrop->addOption(2, $uppermiddleLabel);
    $objDrop->addOption(3, $middleLabel);
    $objDrop->addOption(4, $lowermiddleLabel);
    $objDrop->addOption(5, $bottomLabel);
    
    $objDrop->setSelected($position);
}
$objTable->addRow(array($label1, $drop1, $objLabel->show(), $objDrop->show()));

// action
$objLabel = new label($actionLabel, 'input_actionName');
$objInput = new textinput('actionName', $action);

$objTable->addRow(array($objLabel->show(), $objInput->show()));

// icon
$objLabel = new label($iconLabel, 'input_icon');
$objInput = new textinput('icon', $icon);

$objTable->addRow(array($objLabel->show(), $objInput->show()));

// language code for link text
$objLabel = new label($langCodeLabel, 'input_code');
$objInput = new textinput('code', $code);

$objTable->addRow(array($objLabel->show(), $objInput->show()));

$objHead->str = $settingsLabel;
$objHead->type = 3;

$objTable->startRow();
$objTable->addCell($objHead->show(), '', '','','','colspan="4"');
$objTable->endRow();

// Admin only module
$objLabel = new label($adminLabel, 'input_adminOnly');
$objCheck = new checkbox('adminOnly');
$objCheck->setChecked($admin);
$objTable->addRow(array($objLabel->show(), $objCheck->show()));

// Context dependent module
$objLabel = new label($dependsLabel, 'input_dependsContext');

$objCheck = new checkbox('dependsContext');
$objCheck->setChecked($depends);
$objTable->addRow(array($objLabel->show(), $objCheck->show()));

// Link Permissions
$objHead->str = $linkPermLabel;
$objHead->type = 3;

$objTable->startRow();
$objTable->addCell($objHead->show(), '', '','','','colspan="4"');
$objTable->endRow();

// set available to whole site
if(!$page){
    $objLabel = new label($siteLabel, 'input_site');
    
    $objCheck = new checkbox('site');
    $objCheck->setChecked($allSite);
    $objTable->addRow(array($objLabel->show(), $objCheck->show()));
}

$objLink = new link('javascript:void(0)');
$objLink->link = $setPermLabel;
$objLink->extra = "onclick = \"javascript:window.open('". $this->uri(array('action'=>'setperm', 'modulename'=>$moduleName), '', '', TRUE)."', 'setperms', 'width=800, height=600, scrollbars')\"";

$objTable->startRow();
$objTable->addCell($objLink->show(), '', '','','','colspan="2"');
$objTable->endRow();

$formElements = '';

// hidden elements: id, module, dependsContext, adminOnly
if($mode == 'edit'){
    $objInput = new textinput('id', $data['id']);
    $objInput->fldType = 'hidden';
    $formElements .= $objInput->show();   
}    
$objInput = new textinput('moduleName', $moduleName);
$objInput->fldType = 'hidden';
$formElements .= $objInput->show();
    
$objInput = new textinput('permissions', $permissions);
$objInput->fldType = 'hidden';
$formElements .= $objInput->show(); 

$objButton = new button('save', $saveLabel);
$objButton->setToSubmit();

$formElements .= '<p>'.$objButton->show();

$objButton = new button('save', $backLabel);
$objButton->setToSubmit();

$formElements .= '&nbsp;&nbsp;&nbsp;'.$objButton->show().'</p>';

if($page){
    $formAction = 'savepage';
}else{
    $formAction = 'savemenu';
}

$objForm = new form('menulink', $this->uri(array('action'=>$formAction)));
$objForm->addToForm($objTable->show());
$objForm->addToForm($formElements);

$str .= $objForm->show();

echo $str;
?>