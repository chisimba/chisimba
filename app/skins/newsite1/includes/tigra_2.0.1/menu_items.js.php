<?php

// items structure
// each item is the array of one or more properties:
// [text, link, settings, subitems ...]
// use the builder to export errors free structure if you experience problems with the syntax

$userObj = $this->newObject('user','security');
?>
var MENU_ITEMS = [
        ['Home', '?module=cms', null],
        ['User', null, null,
                ['CMS', '?module=cms'],
                ['File Manager', '?module=filemanager']
        ],
        ['Admin', null, null,

<?php

if (!$userObj->isAdmin()){
?>
                ['CMS Admin', '?module=cmsadmin'],
                ['Logger', '?module=logger'],
                ['Module Catalogue', '?module=modulecatalogue'],
                ['Site Admin', '?module=toolbar']
<?php
} else {
?>
                ['CMS Admin', '?module=cmsadmin']
<?php
}
?>
        ],
        ['Logout', "javascript: if(confirm('Are you sure you want to logout?')) {document.location= '?module=security&action=logoff'};"]
];

<?php


?>

