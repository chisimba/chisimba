<?php
/**
* @package toolbar
*/

/**
* Template to display where links are placed, toolbar, sidemenus, pages with options to
* add, edit or delete the links.
* @param string moduleName The name of the module.
* @param array data The link data for the module.
* @param array moduleList A list of all the modules.
*/

$this->setLayoutTemplate('admin_layout_tpl.php');

// Set up html elements
$objTable =& $this->newObject('htmltable', 'htmlelements');
$objHead =& $this->newObject('htmlheading', 'htmlelements');
$objIcon =& $this->newObject('geticon', 'htmlelements');
$objLink =& $this->newObject('link', 'htmlelements');
$objForm =& $this->newObject('form', 'htmlelements');
$objInput =& $this->newObject('textinput', 'htmlelements');
$objDrop =& $this->newObject('dropdown', 'htmlelements');
$objLabel =& $this->newObject('label', 'htmlelements');

// Set up language items
$objLanguage =& $this->getObject('language', 'language');
$heading = $objLanguage->languageText('mod_toolbar_confmodulelinks');
$moduleLabel = $objLanguage->languageText('mod_toolbar_module', 'Module');
$toolbarLabel = $objLanguage->languageText('mod_toolbar_toolbar');
$sidemenuLabel = $objLanguage->languageText('mod_toolbar_sidemenu');
$pageLabel = $objLanguage->languageText('mod_toolbar_page');
$categoryLabel = $objLanguage->languageText('mod_toolbar_category');
$menuLabel = $objLanguage->languageText('mod_toolbar_menu');
$actionLabel = $objLanguage->languageText('mod_toolbar_action');
$iconLabel = $objLanguage->languageText('mod_toolbar_icon');
$langCodeLabel = $objLanguage->languageText('mod_toolbar_langcode');
$permLabel = $objLanguage->languageText('mod_toolbar_permissions');
$noLinksLabel = $objLanguage->languageText('mod_toolbar_nolinks');
$restoreLabel = $objLanguage->languageText('mod_toolbar_restoredefaults');
$selectLabel = $objLanguage->languageText('mod_toolbar_selectmodule');
$modName = $objLanguage->code2Txt('mod_'.$moduleName.'_name');

$menu = array();
$page = array();
$toolBar = array();

// Organise links in $data array
$i=0; $j=0; $k=0; $idList = '';
if(!empty($data)){
    foreach($data as $item){
        $idList .= $item['id'].',';
        if(!(strpos($item['category'], 'menu_') === FALSE)){
        // side menu links
            $menu[$i]['id'] = $item['id'];
            $menu[$i]['permissions'] = $item['permissions'];
            $menu[$i++]['menu'] = $item['category'];
        }else if(!(strpos($item['category'], 'page_') === FALSE)){
        // page links
            $page[$j]['id'] = $item['id'];
            $page[$j++]['page'] = $item['category'];    
        }else{
        // toolbar links
            $toolBar[$k]['id'] = $item['id'];
            $toolBar[$k++]['category'] = $item['category'];
        }
    }
}

$objHead->str = $heading;
$objHead->type = 1;

$str = $objHead->show();
$str .= '<p><b>'.$moduleLabel.':</b>&nbsp;&nbsp;'.$modName.'</p>';

// Drop down to select a different module
$objLabel = new label($selectLabel, 'input_modulename');

$objDrop = new dropdown('modulename');
$objDrop->extra = 'onChange = "document.modselect.submit()"';
foreach($moduleList as $item){
    $modName = $objLanguage->code2Txt('mod_'.$item['module_id'].'_name');
    $objDrop->addOption($item['module_id'], $modName);
}
$objDrop->setSelected($moduleName);

$objForm = new form('modselect', $this->uri(array('action'=>'editlinks')));
$objForm->addToForm('<b>'.$objLabel->show().':</b>&nbsp;&nbsp;&nbsp;'.$objDrop->show());

$str .= '<p>'.$objForm->show().'</p>';

// Toolbar links
$addTool = $objIcon->getAddIcon($this->uri(array('action'=>'addtool', 'modulename'=>$moduleName)));

$objHead->str = $toolbarLabel.'&nbsp;&nbsp;'.$addTool;
$objHead->type = 3;

$str .= $objHead->show();

$objTable->init();
$objTable->width = '99%';
$objTable->cellspacing = 2;
$objTable->cellpadding = 5;

$tableHd = array();

$tableHd[] = $categoryLabel;
$tableHd[] = '';

$objTable->addHeader($tableHd, 'heading');

if(!empty($toolBar)){
    $i = 0;
    foreach($toolBar as $item){
        $class = ($i%2 == 0) ? 'odd' : 'even';
        
        $icons = $objIcon->getEditIcon($this->uri(array('action'=>'edittool', 'id'=>$item['id'])));
        $icons .= $objIcon->getDeleteIconWithConfirm('', array('action'=>'delete', 'id'=>$item['id'], 'modulename'=>$moduleName), 'toolbar');
        
        $objTable->addRow(array($item['category'], $icons), $class);
    }
}else{
    $objTable->addRow(array($noLinksLabel, ''), 'odd');
}

$str .= $objTable->show();

// Side menu links
$addMenu = $objIcon->getAddIcon($this->uri(array('action'=>'addmenu', 'modulename'=>$moduleName)));

$objHead->str = $sidemenuLabel.'&nbsp;&nbsp;'.$addMenu;
$objHead->type = 3;

$str .= $objHead->show();

$objTable->init();
$objTable->width = '99%';
$objTable->cellspacing = 2;
$objTable->cellpadding = 5;

$tableHd = array();

$tableHd[] = $menuLabel;
$tableHd[] = $permLabel;
$tableHd[] = $actionLabel;
$tableHd[] = $iconLabel;
$tableHd[] = $langCodeLabel;
$tableHd[] = '';

$objTable->addHeader($tableHd, 'heading');

if(!empty($menu)){
    $i = 0;
    foreach($menu as $item){
        $class = ($i%2 == 0) ? 'odd' : 'even';
        
        $icons = $objIcon->getEditIcon($this->uri(array('action'=>'editmenu', 'id'=>$item['id'])));
        $icons .= $objIcon->getDeleteIconWithConfirm('', array('action'=>'delete', 'id'=>$item['id'], 'modulename'=>$moduleName), 'toolbar');
        
        // explode link into attributes
        $array = explode('|', $item['menu']);
        $row = array_fill(0, 6, '');
        $row[0] = $array[0];
        $row[1] = $item['permissions'];
        if(isset($array[2])){
            $row[2] = $array[2];
        }
        if(isset($array[3])){
            $row[3] = $array[3];
        }
        if(isset($array[4])){
            $row[4] = $array[4];
        }
        $row[5] = $icons;
        
        $objTable->addRow($row, $class);
    }
}else{
    $objTable->addRow(array($noLinksLabel,'','','','',''), 'odd');
}

$str .= $objTable->show();

// Page links
$addPage = $objIcon->getAddIcon($this->uri(array('action'=>'addpage', 'modulename'=>$moduleName)));

$objHead->str = $pageLabel.'&nbsp;&nbsp;'.$addPage;
$objHead->type = 3;

$str .= $objHead->show();

$objTable->init();
$objTable->width = '99%';
$objTable->cellspacing = 2;
$objTable->cellpadding = 5;

$tableHd = array();

$tableHd[] = $pageLabel;
$tableHd[] = $actionLabel;
$tableHd[] = $iconLabel;
$tableHd[] = $langCodeLabel;
$tableHd[] = '';

$objTable->addHeader($tableHd, 'heading');

if(!empty($page)){
    $i = 0;
    foreach($page as $item){
        $class = ($i%2 == 0) ? 'odd' : 'even';
        
        $icons = $objIcon->getEditIcon($this->uri(array('action'=>'editpage', 'id'=>$item['id'])));
        $icons .= $objIcon->getDeleteIconWithConfirm('',array('action'=>'delete', 'id'=>$item['id'], 'modulename'=>$moduleName), 'toolbar');
        
        // explode link into attributes
        $array = explode('|', $item['page']);
        $row = array_fill(0, 6, '');
        $row[0] = $array[0];
        if(isset($array[1])){
            $row[1] = $array[1];
        }
        if(isset($array[2])){
            $row[2] = $array[2];
        }
        if(isset($array[3])){
            $row[3] = $array[3];
        }
        $row[4] = $icons;
        
        $objTable->addRow($row, $class);
    }
}else{
    $objTable->addRow(array($noLinksLabel,'','','',''), 'odd');
}

$str .= $objTable->show();

$objInput = new textinput('ids', $idList);
$objInput->fldType = 'hidden';

$objLink = new link('javascript:void(0)');
$objLink->extra = 'onclick="document.restore.submit();"';
$objLink->link = $restoreLabel;

$objForm = new form('restore', $this->uri(array('action'=>'restore', 'modulename'=>$moduleName)));
$objForm->addToForm($objInput->show());
$objForm->addToForm($objLink->show());

$str .= '<p>'.$objForm->show().'</p>';

echo $str;
?>