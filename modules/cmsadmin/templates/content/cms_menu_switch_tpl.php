<?php
/**
* Template to display the list of menu items
*/

$objHead = $this->newObject('htmlheading', 'htmlelements');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objLayer =$this->newObject('layer','htmlelements');
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$this->loadClass('button', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('htmltable', 'htmlelements');
$this->loadClass('radio', 'htmlelements');

$head = $this->objLanguage->languageText('mod_cmsadmin_menu', 'cmsadmin');
$lbPreview = $this->objLanguage->languageText('mod_cmsadmin_menustylepreview', 'cmsadmin');
$hdStyles = $this->objLanguage->languageText('phrase_menustyles');
$lbRootOnly = $this->objLanguage->languageText('mod_cmsadmin_rootnodesonly', 'cmsadmin');
$btnUpdate = $this->objLanguage->languageText('mod_cmsadmin_updatemenustyle', 'cmsadmin');

/* ** page heading ** */
$objIcon->setIcon('menu2_small', 'png', 'icons/cms/');

$objHead->str = $objIcon->show().'&nbsp;'.$head;
$objHead->type = 1;
$objLayer->str = $objHead->show();
//$objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_left';
$headStr = $objLayer->show();
$objLayer->str = $topNav;
//$objLayer->border = '; float:right; align:right; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_right';
$headStr .= $objLayer->show();

$objLayer->str = '';
//$objLayer->border = '; clear:both; margin:0px; padding:0px;';
$objLayer->id = 'cms_header_clear';
$objLayer->cssClass = 'clearboth';
$navStr = $objLayer->show();

$objLayer->str = '&nbsp;';
$objLayer->id = 'cmsheaderspacer';
$vspacer = $objLayer->show();


$str = $headStr.$navStr.$vspacer;

/* ** page content ** */

$activeStyle = 'tree';

// List of styles to switch between
$objHead->str = $hdStyles;
$objHead->type = 3;
$styleList = $objHead->show().'<br />';

if(!empty($data)){
    $objRadio = new radio('style');
    foreach($data as $item){
        $label = '&nbsp;&nbsp;&nbsp;'.$this->objLanguage->languageText('mod_cmsadmin_menu'.$item['menu_style'], 'cmsadmin');
        
        if($item['root_nodes'] == 1){
            $label .= '&nbsp; - &nbsp;<font class="warning">'.$lbRootOnly.'</font>';
        }

		//If the menu is editable then display the pencil icon
        if($item['editable'] == 1){
			//Edit Icon for editable menu's (namely page menu for now)       
			$icon = $this->getObject('geticon','htmlelements');
	        $icon->setIcon('edit');
	        //$icon->alt = $this->objLanguage->languageText('word_edit');
	        $icon->alt = 'Create/Edit the Custom Menu';
			
	        $link = $this->getObject('link','htmlelements');
	        $link->link($this->uri(array('action'=>'editmenu','menutype'=>$item['menu_style'])));
    	    $link->link = $icon->show();

			$label .= ' '.$link->show();
		}
 
        $objRadio->addOption($item['id'], $label);
        
        if($item['is_active'] == 1){
            $objRadio->setSelected($item['id']);
            $activeStyle = $item['menu_style'];
        }
    }
    $objRadio->setBreakSpace('<br />');
    $formStr = $objRadio->show();
}

$objButton = new button('save', $btnUpdate);
$objButton->setToSubmit();
$formStr .= '<p><br />'.$objButton->show().'</p>';

$objForm = new form('change', $this->uri(array('action' => 'updatemenustyle')));
$objForm->addToForm($formStr);
$styleList .= $objForm->show();

// Preview of the menu style

//Nic Appleby removed this as the script hangs for vary large menus (uwc portal)
//$menuPreview = $this->_objCMSLayouts->getMenu($activeStyle);

// Display
$objTable = new htmltable();
$objTable->startRow();
$objTable->addCell($styleList, '60%');
//$objTable->addCell('', '2%');
//$objTable->addCell($menuPreview, '38%');
$objTable->endRow();

$str .= $objTable->show();

$lnConfigureBlocks = $this->objLanguage->languageText('mod_cmsadmin_configureleftblocks', 'cmsadmin');
$objLink = new link($this->uri(array('action' => 'configleftblocks')));
$objLink->link = $lnConfigureBlocks;
$str .= '<p><br />'.$objLink->show().'</p>';

echo $str;
?>
