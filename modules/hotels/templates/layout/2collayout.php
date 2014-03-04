<?php

$cssLayout = $this->getObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

$leftContent = $this->objNewsMenu->generateMenu();
$leftContent .= '<div id="newsfeeds">'.$this->objNewsStories->getFeedLinks().'</div>';

$adminOptions = array();

if ($this->isValid('managecategories')) {
    $newsCategoriesLink = new link ($this->uri(array('action'=>'managecategories')));
    $newsCategoriesLink->link = 'Manage Hotel Categories';
    $adminOptions[] = '<li>'.$newsCategoriesLink->show().'</li>';
}

if ($this->isValid('addstory')) {
    $addNewsStoryLink = new link ($this->uri(array('action'=>'addstory')));
    $addNewsStoryLink->link = 'Add Hotel';
    $adminOptions[] = '<li>'.$addNewsStoryLink->show().'</li>';
}

if (count($adminOptions) > 0) {

    $leftContent .= '<h3>Hotel Options</h3>';

    $leftContent .= '<ul>';

    foreach ($adminOptions as $option)
    {
        $leftContent .= $option;
    }

    $leftContent .= '</ul>';

}

$cssLayout->setLeftColumnContent($leftContent);
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();

?>