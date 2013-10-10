<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>

<div id="threecolumn">
    <div id="Canvas_Content_Body_Region1">
        {
            "display" : "block",
            "module" : "security",
            "block" : "login"
        }
        Context wall cannot be rendered outside of a context. <br />
        <a href="index.php?module=wall">Wall</wall>
    </div>
    <div id="Canvas_Content_Body_Region3">
The wall module is not meant to be accessed by users. It provides
a developer testing interface for use in working on the wall module.
Rather the wall should be accessed by providing one of its blocks to
another module.
    </div>
    <div id="Canvas_Content_Body_Region2">
You cannot render a context wall in this way. Sorry. You have to be in a context,
for example a course, and use it as a block in the context.
    </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>