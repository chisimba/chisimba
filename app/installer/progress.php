<?php

/**
 * Progress
 *
 * @version $Id$
 * @copyright 2011 AVOIR
 */

$deleteprogressfile = isset($_GET['deleteprogressfile']) && $_GET['deleteprogressfile'] == 'true';

$dir = dirname($_SERVER ['SCRIPT_FILENAME']);
//echo "[$dir]\n";
$dir = preg_replace('|/installer$|i', '', $dir);
//echo "[$dir]\n";
$filename = $dir . '/progress';
//echo "[$filename]\n";

if (!file_exists($filename)) {
    echo "Please wait...";
}
else {
    if (($ret = file_get_contents($filename)) === FALSE)
        echo "Failure!";
    else
        echo $ret;
}

if ($deleteprogressfile && file_exists($filename)) {
        unlink($filename);
}

?>