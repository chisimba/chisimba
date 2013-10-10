<?php

$headerParams = $this->getJavascriptFile('scripts.js', 'cmsadmin');
$this->appendArrayVar('headerParams', $headerParams);

print $addEditSectionPermissionsForm;

?>