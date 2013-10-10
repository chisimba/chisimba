<?php

echo '<h1>'.$this->objLanguage->languageText('mod_news_editmenuitem', 'news', 'Edit Menu Item').': '.$item['itemname'].'</h1>';

$returnAction = $this->getParam('returnaction', 'viewcategory');


echo $this->objNewsCategories->showCategoryForm($item['itemvalue'], $id, $returnAction);
?>