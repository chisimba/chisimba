<?php
$objBlocks = $this->getObject('blockfilter', 'dynamiccanvas');
$pageContent = $this->getVar('pageContent');
$pageContent = $objBlocks->parse($pageContent);

$objLanguage = $this->getObject('language', 'language');
/*
if (isset($errors)) {
    $errorTitle = "";
    if (isset($fieldsrequired)) {
        $errorTitle = $objLanguage->languageText("mod_oer_fieldsrequired", "oer");
    }
    echo '<div id="error" class="error">';
    echo '<h2>' . $errorTitle . '</h2>';
    echo '<ul>';
    foreach ($errors as $error) {
        echo '<li>' . $error . '</li>';
    }
    echo '</ul>';
    echo '</div>';
}*/
echo $pageContent;
?>
