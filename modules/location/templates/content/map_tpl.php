<?php

$objGMapApi = $this->getVar('objGMapApi');

if (is_object($objGMapApi)) {
    $objGMapApi->printMapJS();
    $objGMapApi->printMap();
    $objGMapApi->printSidebar();
}
