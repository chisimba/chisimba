<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>

<div id="threecolumn">
    <div id="Canvas_Content_Body_Region1">
The wall module is not meant to be accessed by users. It provides
a developer testing interface for use in working on the wall module.
Rather the wall should be accessed by providing one of its blocks to
another module.
        {
            "display" : "block",
            "module" : "security",
            "block" : "login"
        }
    </div>
    <div id="Canvas_Content_Body_Region3">
        {
            "display" : "block",
            "module" : "blog",
            "block" : "latest"
        }
    </div>
    <div id="Canvas_Content_Body_Region2">
        {
            "display" : "block",
            "module" : "wall",
            "block" : "genericwall"
        }
    </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>
