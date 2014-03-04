<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('form', 'htmlelements');
$this->loadClass('textinput', 'htmlelements');
$this->loadClass('button', 'htmlelements');
$this->loadClass('label', 'htmlelements');
$this->loadClass('dropdown', 'htmlelements');


$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->languageText('mod_hotels_addmenuitem', 'hotels', 'Add Menu Item');

echo $header->show();

$objCache = $this->getObject('cache', 'utilities');
$objCache->setup('addmenuitemform', 'hotels', 1000);

// Add Divider Form 

$objCache->setup('adddividerform', 'hotels', 1000);

$addDividerForm = $objCache->get();

if ($addDividerForm == FALSE)  {

    $addDividerForm = new form ('adddivider', $this->uri(array('action'=>'adddividertomenu')));
    $button = new button ('adddividerbutton', $this->objLanguage->languageText('mod_hotels_adddividingruler', 'hotels', 'Add Dividing Ruler to Menu'));
    $button->setToSubmit();
    $addDividerForm->addToForm('<hr />'.$button->show());
    
    $addDividerForm = $addDividerForm->show();
}

// Add URL Form 

$objCache->setup('addurlform', 'hotels', 1000);

$addURLForm = $objCache->get();

if ($addURLForm == FALSE)  {
    $addURLForm = new form ('addurl', $this->uri(array('action'=>'addurltomenu')));
    $label1 = new label ($this->objLanguage->languageText('phrase_websitetitle', 'phrase', 'Website Title').': ', 'input_urlmenutitle');
    $urlMenuTitle = new textinput('urlmenutitle');
    $label2 = new label ($this->objLanguage->languageText('phrase_websiteurl', 'phrase', 'Website URL').': ', 'input_websiteurl');
    $websiteUrl = new textinput('websiteurl');
    $websiteUrl->value = 'http://';
    
    $button = new button ('adddividerbutton', $this->objLanguage->languageText('phrase_addurltomenu', 'phrase', 'Add URL to Menu'));
    $button->setToSubmit();
    $addURLForm->addToForm($label1->show().$urlMenuTitle->show().'<br />');
    $addURLForm->addToForm($label2->show().$websiteUrl->show().'<br />');
    $addURLForm->addToForm($button->show());
    
    $addURLForm = $addURLForm->show();
}

// Add Text Form 

$objCache->setup('addtextform', 'hotels', 1000);

$addTextForm = $objCache->get();

if ($addTextForm == FALSE)  {
    $addTextForm = new form ('addtext', $this->uri(array('action'=>'addtexttomenu')));
    $label = new label ($this->objLanguage->languageText('mod_systext_text', 'system', 'Text').': ', 'input_text');
    $text = new textinput('text');
    
    
    $button = new button ('adddividerbutton', $this->objLanguage->languageText('mod_phrase_addtexttomenu', 'phrase', 'Add Text to Menu'));
    $button->setToSubmit();
    $addTextForm->addToForm($label->show().$text->show().'<br />');
    $addTextForm->addToForm($button->show());
    
    $addTextForm = $addTextForm->show();
}
// END - Add Text Form 

// Add Module Form

$objModules = $this->getObject('modules', 'modulecatalogue');
$modules = $objModules->getModules(3);

if (count($modules) == 0) {
    $addModuleFormStr = '<div class="noRecordsMessage">'.$this->objLanguage->languageText('phrase_nomodulesfound', 'phrase', 'No Modules found').'</div>';
} else {
    $addModuleForm = new form ('addmodule', $this->uri(array('action'=>'addmoduletomenu')));
    $label = new label ($this->objLanguage->languageText('word_module', 'system', 'Module').': ', 'input_themodule');
    
    $moduleDropdown = new dropdown('themodule');
    foreach ($modules as $module)
    {
        $moduleDropdown->addOption($module['module_id'], $module['title']);
    }
    
    
    $button = new button ('adddividerbutton', $this->objLanguage->languageText('mod_phrase_addmoduletomenu', 'phrase', 'Add Module to Menu'));
    $button->setToSubmit();
    $addModuleForm->addToForm($label->show().$moduleDropdown->show().'<br />');
    $addModuleForm->addToForm($button->show());
    
    $addModuleFormStr = $addModuleForm->show();
}

// END - Add Module Form


$objBlocks = $this->getObject('dbmoduleblocks', 'modulecatalogue');
$blocks = $objBlocks->getBlocks('normal', 'site|prelogin');

if (count($blocks) == 0) {
    $addBlocksFormStr = '<div class="noRecordsMessage">'.$this->objLanguage->languageText('mod_hotels_noblocksavailable', 'hotels', 'No blocks available').'</div>';
} else {
    $addBlocksForm = new form ('addmodule', $this->uri(array('action'=>'addblocktomenu')));
    $label = new label ($this->objLanguage->languageText('word_block', 'system', 'Block').': ', 'input_block');
    
    $blockDropdown = new dropdown('theblock');
    foreach ($blocks as $block)
    {
        $blockDropdown->addOption($block['moduleid'].'|'.$block['blockname'], $block['moduleid'].'|'.$block['blockname']);
    }
    
    
    $button = new button ('addblockbutton', $this->objLanguage->languageText('mod_hotels_addblocktomenu', 'hotels', 'Add Block to Menu'));
    $button->setToSubmit();
    $addBlocksForm->addToForm($label->show().$blockDropdown->show().'<br />');
    $addBlocksForm->addToForm($button->show());
    
    $addBlocksFormStr = $addBlocksForm->show();
}

$switchmenu = $this->newObject('switchmenu', 'htmlelements');
$switchmenu->addBlock($this->objLanguage->languageText('mod_hotels_addnewscategory', 'hotels', 'Add hotels Category'), $this->objNewsCategories->showCategoryForm());
$switchmenu->addBlock($this->objLanguage->languageText('mod_hotels_addlinktomenu', 'hotels', 'Add Link to Module'), $addModuleFormStr); 
$switchmenu->addBlock($this->objLanguage->languageText('mod_hotels_addtext', 'hotels', 'Add Text'), $addTextForm); 
$switchmenu->addBlock($this->objLanguage->languageText('mod_hotels_addlinktowebsite', 'hotels', 'Add Link to Website'), $addURLForm);
$switchmenu->addBlock($this->objLanguage->languageText('mod_hotels_adddivider', 'hotels', 'Add Divider'), $addDividerForm);
$switchmenu->addBlock($this->objLanguage->languageText('mod_prelogin_addblock', 'system', 'Add Block'), $addBlocksFormStr);

echo $switchmenu->show();
    
$homeLink = new link ($this->uri(NULL));
$homeLink->link = $this->objLanguage->languageText('mod_hotels_returntonewshome', 'hotels', 'Return to Hotels Home');
echo $homeLink->show();

?>
