<?php

// Get language strings.
$headingText = $this->objLanguage->languageText('mod_filemanager_access_denied_heading', 'filemanager');
$messageText = $this->objLanguage->languageText('mod_filemanager_access_denied_message', 'filemanager');

// Create and output page heading.
$heading = $this->newObject('htmlheading', 'htmlelements');
$heading->htmlheading($headingText, 1);
echo $heading->show();

// Create and output message.
echo '<p>'.htmlspecialchars($messageText).'</p>';
