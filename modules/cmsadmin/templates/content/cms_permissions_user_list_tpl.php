<?php
/**
 *  Listing All User Specific Permissions
 */

    if (!isset($arrUserPermissions)) {
        $arrUserPermissions = array();
    }

    $middleColumnContent = $this->_objDisplay->getUserPermissionsTemplate($arrUserPermissions);
    echo $middleColumnContent;

?>
