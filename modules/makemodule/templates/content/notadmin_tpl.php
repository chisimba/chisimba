<?php
ob_start();
$objFix = $this->getObject('cssfixlength', 'htmlelements');
$objFix->fixThree();
?>

<div id="threecolumn">
    <div id="Canvas_Content_Body_Region1">
        <span class="error">You cannot create modules as the user
            <em>admin</em>. You must be logged in as a real user with
            a real email address and full name. The last thing we want
            in Chisimba is a lot of modules in subversion that say they
            were created by <em>Administrative user</em>.
        </span>
    </div>
    <div id="Canvas_Content_Body_Region3">
        {
            "display" : "block",
            "module" : "makemodule",
            "block" : "linksmakemodule"
        }
        <span class="error">Please logout, and login as a real user to continue.</span>
    </div>
    <div id="Canvas_Content_Body_Region2">
        {
            "display" : "block",
            "module" : "makemodule",
            "block" : "makemodulemain"
        }
    </div>
</div>
<?php
// Get the contents for the layout template
$pageContent = ob_get_contents();
ob_end_clean();
$this->setVar('pageContent', $pageContent);
?>
