<?php

/**
* Setting a locale
* ================
*
* Because Un*x and Windows use different locale codes, PHPs setLocale() is not 
* easily portable - I18Nv2::setLocale() attempts to provide this portability.
* 
* With I18Nv2 you can use standard locale codes like 'en_US' on both, Linux
* and Windows, though the list is far not complete yet, so if you stumble
* over a not covered locale (I18Nv2::$locales in I18Nv2::_main()), just drop
* a mail to <mike(@)php.net> with the missing locale and its corresponding
* Win32 code.
* 
* $Id$
*/

require_once 'I18Nv2.php';

$locale = 'en_US';

if (!I18Nv2::setLocale($locale)) {
    die("Locale '$locale' not available!\n");
}

?>