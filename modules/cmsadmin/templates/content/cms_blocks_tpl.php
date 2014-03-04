<?php

/**
* Template for added removing blocks from a page
*
* @author Warren Windvogel
* @package cmsadmin
*/

// Suppress normal page elements and layout
$this->setVar('pageSuppressIM', FALSE);
$this->setVar('pageSuppressBanner', FALSE);
$this->setVar('pageSuppressToolbar', FALSE);
$this->setVar('suppressFooter', FALSE);

//Set layout template
$this->setLayoutTemplate('cms_blocks_layout_tpl.php');

// set up close button
$objButton = new button('close', $this->objLanguage->languageText('word_close'));
$objButton->setOnClick( "javascript:window.close()");
$closeButton = $objButton->show();

if($closePage) {
    echo $closeButton;
} else {
    echo $blockForm;
    echo $closeButton;
}

?>
