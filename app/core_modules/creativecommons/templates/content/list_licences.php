<?php

$objIcon = $this->newObject('geticon', 'htmlelements');
$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$header = new htmlHeading();
$header->str = $objLanguage->languageText('mod_creativecommons_listoflicenses', 'creativecommons');
$header->type = 1;

echo $header->show();

$table = $this->newObject('htmltable', 'htmlelements');
$table->cellpadding = 5;
foreach ($licences as $license)
{
    $icons = explode(',', $license['images']);
    
    $iconList = '';
    foreach ($icons as $icon)
    {
        $objIcon->setIcon ($icon, NULL, 'icons/creativecommons');
        $iconList .= $objIcon->show();

    }
    
    if ($license['url'] == '') {
        $url = '';
    } else {
        $url = new link ($license['url']);
        $url->link = $license['url'];
        $url = '<br />'.$url->show();
    }
    
    $table->startRow();
    $table->addCell('&nbsp;');
    $table->addCell('<h3>'.$license['title'].'</h3>');
    $table->endRow();
    
    $table->startRow();
    $table->addCell($iconList, 10, 'top', 'right', 'nowrap');
    $table->addCell($license['description'].$url);
    $table->endRow();
    
    $table->startRow();
    $table->addCell('&nbsp;');
    $table->addCell('&nbsp;');
    $table->endRow();
}

echo $table->show();

?>