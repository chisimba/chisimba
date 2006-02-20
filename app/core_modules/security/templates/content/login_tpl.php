<?php
/*Template for login*/
// Keep so long - but should ideally be moved to splashscreeninfo.php file
$this->setVar('suppressFooter', TRUE);

// Create an instance of the splash screen template, and output
$this->objSplash =& $this->getObject('splashscreen');
echo $this->objSplash->putSplashScreen();
?>
