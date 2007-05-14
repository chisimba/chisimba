<?php

/**
* Retrieving locale conventions
* =============================
*
* I18Nv2 holds locale conventions returned by localeConv() stored statically, 
* so they are easily accessible through I18Nv2::getInfo(). Have a look at
* the documentation of PHPs localeConv() for all available information.
* 
* $Id$
*/

require_once 'I18Nv2.php';

I18Nv2::setLocale('fr');

$dec_point = I18Nv2::getInfo('decimal_point');
echo "The decimal point for the french locale is '$dec_point'.\n";
echo "I18Nv2::getInfo() called without parameter returns all available information:\n";
print_r(I18Nv2::getInfo());
?>