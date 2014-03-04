<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');


$header = new htmlheading();
$header->type = 1;
$header->cssClass = 'error';
$header->str = $this->objLanguage->languageText('mod_hotels_errornowcategories', 'hotels', 'Error - No Categories available');

echo $header->show();

$link = new link ($this->uri(array('action'=>'addmenuitem')));
$link->link = $this->objLanguage->languageText('mod_hotels_addcategory', 'hotels', 'Add Category');

echo '<p>'.$this->objLanguage->languageText('mod_hotels_explainnocategorieserror', 'hotels', 'You need to first add some categories/sections before you can add hotels').'</p>';
echo '<p>'.$link->show().'</p>';

?>