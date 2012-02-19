<?php

$objCssLayout = $this->newObject('csslayout', 'htmlelements');

$objNav = $this->getObject('contextadminnav');
$objCssLayout->setLeftColumnContent($objNav->show());
$objCssLayout->setMiddleColumnContent($this->getContent());

 $objModule = $this->getObject('modules', 'modulecatalogue');
$isRegistered = $objModule->checkIfRegistered('oer');
if ($isRegistered) {
    echo '<div id="onecolumn">' . $objCssLayout->show() . '</div>';
} else {
    echo $objCssLayout->show();
}
?>