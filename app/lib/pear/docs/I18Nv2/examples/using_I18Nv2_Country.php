<?php

/**
* Using I18Nv2_Country
* ====================
*
* I18Nv2 provides translated lists of country names.
* 
* $Id$
*/

require_once 'I18Nv2/Country.php';

$country = &new I18Nv2_Country('de', 'iso-8859-1');

echo "German name for United States: ",
    $country->getName('us'), "\n";

echo "German name for Italia:        ",
    $country->getName('it'), "\n";

?>