<?php
/**
 * This template is used when user's without permissions attempt to edit content
 */

if (!isset($action)) {
    $action = '';
}

if (!isset($message)) {
    $message = 'No Access';
}

switch ($action) {
    default:
        echo "<span class='error'>".$message."</span>";
    break;

    case 'frontpages':
        echo "<span class='error'>".$message."</span>";
    break;

    case 'addcontent':
        echo "<span class='error'>".$this->objLanguage->languageText('mod_cmsadmin_nopermissions','cmsadmin')."</span>";
    break;

    case 'editcontent':
        echo "<span class='error'>".$this->objLanguage->languageText('mod_cmsadmin_nopermissions','cmsadmin')."</span>";
    break;
}

?>