<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');


$header = new htmlheading();
$header->type = 1;
$header->cssClass = 'error';
$header->str = $this->objLanguage->languageText('mod_news_errornowcategories', 'news', 'Error - No Categories available');

echo $header->show();

$link = new link ($this->uri(array('action'=>'addmenuitem')));
$link->link = $this->objLanguage->languageText('mod_news_addcategory', 'news', 'Add Category');

echo '<p>'.$this->objLanguage->languageText('mod_news_explainnocategorieserror', 'news', 'You need to first add some categories/sections before you can add news stories').'</p>';
echo '<p>'.$link->show().'</p>';

?>