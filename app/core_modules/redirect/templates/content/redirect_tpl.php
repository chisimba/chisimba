<?php
/**
* @package redirect
*/

/**
* Display template to redirect the user.
*/

$this->setLayoutTemplate('redirect_layout_tpl.php');

$objHead = $this->newObject('htmlheading', 'htmlelements');

if(isset($heading)){
    $objHead->str = $heading;
    $objHead->type = 1;
    echo $objHead->show();
}

if(isset($subhead)){
    echo $subhead;
}

if(isset($actions)){
    echo '<p>'.$actions . '</p>';
}
?>