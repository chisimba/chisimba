<?php
/*
 * This file is required for version 3 skins (there are counterparts
 * for version 1 & 2 skins). It sets up the page parameters so that they
 * can be used or rendered in the skin page template.
 *
 * This is adapted from skinpagedheader2-0.php by Derek Keats for use
 * with the Chisimba skin+canvas system.
 *
 */


/**
 * 
 * Function to fix the buffer by changnig > to /> where necessary
 * for better HTML compliance.
 * 
 */
function fix_code($buffer)
{
    return (preg_replace("!\s*/>!", ">", $buffer));
}

//-------------------------




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

// Create a variable to hold the Javascripts
 $javascripts =  $objSkin->putJavaScript($mime, $headerParams, $bodyOnLoad);

 // Set a variable holding the character set to UTF-8 unless it has already been set.
 if (!isset($charset)) {
     $charset = "utf-8";
 }

 // Set a variable holding the mime to text/html unless it has already been set.
if (!isset($mime)) {
    $mime = "text/html";
}

// Set up the variable for the page language.
if (!isset($pageLanguage)) {
    $languageClass =& $this->getObject('language', 'language');
    $languageCode =& $this->getObject('languagecode', 'language');
    $pageLanguage = $languageCode->getISO($languageClass->currentLanguage());
}

// Fix the buffer by changnig > to /> where necessaryfor better HTML compliance.
ob_start("fix_code");

// If we are doing HTML5, then print a HTML5 doctype otherwise do HTML 4.01 transitional.
if (isset($html5)) {
    print "<!DOCTYPE html>";
} else {
    $prolog_type = "<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">\n<html lang=\"$pageLanguage\">\n";
    header("Content-Type: $mime;charset=$charset");
    header("Vary: Accept");
    print $prolog_type;
}
?>