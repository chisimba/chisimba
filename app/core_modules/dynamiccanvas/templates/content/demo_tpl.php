<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>

<div id="threecolumn">
    <div id="Canvas_Content_Body_Region1">
        {test1}
        {filemanager:userfiles}
    </div>
    <div id="Canvas_Content_Body_Region3">
        {test2}
        {security:login}
        {blocks:wrapper}
        {blocks:table}
        {blocks:tabbedbox}
    </div>
    <div id="Canvas_Content_Body_Region2">
        {userdetails:userdetails}
        {nonexistentblock}
        {thirdtest}
    </div>
</div>

<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>