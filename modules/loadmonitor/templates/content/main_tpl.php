<?php

/**
 *
 *
 * @version $Id: dump_tpl.php 21037 2011-03-29 13:20:11Z joconnor $
 * @copyright (C) 2009, 2011 AVOIR
 */

$objSiteLoad = $this->newObject('siteload');
echo '<h2>User log in statistics</h2>';
$count = $objSiteLoad->getCountLoggedIn();
if ($count == 1) {
    echo $count.' user has logged in during the last 1 hour.';
} else {
    echo $count.' users have logged in during the last 1 hour.';
}
echo '<br />';
echo '<br />';
echo '<h2>User activity statistics</h2>';
for ($offset = 5; $offset >= 5; $offset -= 5) {
    $start = $offset;
    $finish = $offset - 5;
    $count = $objSiteLoad->getCountActive($offset, 5);
    if ($count == 1) {
        echo $count." user has been active during the period {$start} minutes ago to {$finish} minutes ago.";
    } else {
        echo $count." users have been active during the period {$start} minutes ago to {$finish} minutes ago.";
    }
    echo '<br />';
}
echo '<br />';

?>