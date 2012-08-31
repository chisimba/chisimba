<?php
/*
 * This file is required for version 3 skins (there are counterparts
 * for version 1 & 2 skins). It sets up the page parameters so that they
 * can be used or rendered in the skin page template.
 *
 * It should be loaded into the page templage of a canvas / skin using
 * require($objConfig->getsiteRootPath().'skins/_common/templates/skinpageheader3-0.php');
 *
 * This is adapted from skinpagedheader2-0.php by Derek Keats for use
 * with the Chisimba skin+canvas system.
 *
 */

// Set the page title to the site name if it is not already set to something else.
if (!isset($pageTitle)) {
    $pageTitle = $objConfig->getSiteName();
}

// Create a variable to hold the Toolbar if not suppressed
if (!isset($pageSuppressToolbar)) {
    $menu = $this->getObject('menu','toolbar');
    $toolbar = $menu->show();
    // Get any header params or body onload parameters for objects on the toolbar.
    $menu->getParams($headerParams, $bodyOnLoad);
}

// Create a variable to hold the Footer depending on the login status.
if (isset($footerStr)) {
    $this->footerNav->str = $footerStr;
} elseif ($objUser->isLoggedIn()) {
    $this->loadClass('link', 'htmlelements');
    $link = new link ($this->URI(array('action'=>'logoff'),'security'));
    $link->link=$objLanguage->languageText("word_logout");
    $footerStr=$objLanguage->languageText("mod_context_loggedinas", 'context')
      .' <strong>'.$objUser->fullname().'</strong>  ('.$link->show().')';
}
		
// Create an empty array for the header paramseters unless it is already set.
if (!isset($headerParams)) {
    $headerParams = array();
}

// Create an empty array for the body onload paramseters unless it is already set.
if (!isset($bodyOnLoad)) {
    $bodyOnLoad = array();
}

// Set the header style depending on whether we must suppress banner.
if (isset($pageSuppressBanner)) {
	$headerStyle = "header_no_banner";
} else {
	$headerStyle = "header";
}
	  
// Set Number of Columns if not defined.
if (!isset($numColumns)) {
    $numColumns = 0;
}

 // Set a variable holding the character set to UTF-8 unless it has already been set.
 if (!isset($charset)) {
     $charset = "utf-8";
 }

 // Set a variable holding the mime to text/html unless it has already been set.
if (!isset($mime)) {
    $mime = "text/html";
}

// Create a variable to hold the Javascripts
 $javascripts =  $objSkin->putJavaScript($mime, $headerParams, $bodyOnLoad);

// Set up the variable for the page language.
if (!isset($pageLanguage)) {
    $languageClass =& $this->getObject('language', 'language');
    $languageCode =& $this->getObject('languagecode', 'language');
    $pageLanguage = $languageCode->getISO($languageClass->currentLanguage());
}

// Print a HTML5 doctype and header
header("Content-Type: $mime; charset=$charset");
header("Vary: Accept");
echo "<!DOCTYPE html>\n";
echo "<html lang=\"$pageLanguage\" xmlns=\"http://www.w3.org/1999/xhtml\"
xmlns:og=\"http://opengraphprotocol.org/schema/\"
xmlns:fb=\"http://www.facebook.com/2008/fbml\">\n";
echo "<!--This site is powered by Chisimba-->\n";
?>
