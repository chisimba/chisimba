<?php
/**
*  A main content template for Splash screen nostalgia
*  Author: Derek Keats derek@localhost.local
*  Date: March 10, 2012, 10:55 pm
*
*/
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>

<div id="onecolumn">
    <div id="Canvas_Content_Body_Region2">
        <div class="splashscreenwrapper">
            <div class="splashscreen">
                <div class="splashcontents">
                {
                    "display" : "block",
                    "module" : "security",
                    "block" : "login"
                }
                </div>
            </div>
        </div>
    </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>