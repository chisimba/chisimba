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
        {
            "display" : "block",
            "module" : "textblock",
            "block" : "text1"
        }
    </div>
    <div id="Canvas_Content_Body_Region3">
        Welcome to the demodata module. This module populates your system with
        data that can be used for testing, or for putting on a demo of a system
        with some reasonable data that can be used.
    </div>
    <div id="Canvas_Content_Body_Region2">
        Welcome to the demodata module. This module populates your system with
        data that can be used for testing, or for putting on a demo of a system
        with some reasonable data that can be used.
        {
            "display" : "block",
            "module" : "textblock",
            "block" : "text2"
        }
    </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>
