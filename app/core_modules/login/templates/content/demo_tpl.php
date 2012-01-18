<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixTwo();
?>
<div id="twocolumn">
    <div id="Canvas_Content_Body_Region1">
        {
            "display" : "block",
            "module" : "login",
            "block" : "ajaxlogin"
        }
        
        This interface is for developer testing only.
    </div>
   <div id="Canvas_Content_Body_Region2">
        {
            "display" : "block",
            "module" : "login",
            "block" : "loginmain"
        }
    </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>
