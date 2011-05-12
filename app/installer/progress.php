<?php

/**
 * Progress
 *
 * Reads the progress file and echos its
 * contents. Also deletes it if required.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @copyright (C) 2011 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @author Jeremy O'Connor
 * @version   $Id$
 */

$deleteprogressfile = isset($_GET['deleteprogressfile']) && $_GET['deleteprogressfile'] == 'true';

$dir = dirname($_SERVER ['SCRIPT_FILENAME']);
//echo "[$dir]\n";
$dir = preg_replace('|/installer$|i', '', $dir);
//echo "[$dir]\n";
$filename = $dir . '/progress.xml';
//echo "[$filename]\n";

header('Content-Type: text/xml');

/**
* Return XML packet with status set to passed in string.
*
* @param string $s Status string
*/

function xml($s)
{
    return <<<EOT
<?xml version="1.0" encoding="ISO-8859-1"?>
<data>
    <percentage>0</percentage>
    <message>{$s}</message>
</data>
EOT;
}

if (!file_exists($filename)) {
    echo xml("Please wait...");
}
else {
    if (($ret = file_get_contents($filename)) === FALSE)
        echo xml("Failure!");
    else
        echo $ret;
}

if ($deleteprogressfile && file_exists($filename)) {
        unlink($filename);
}

?>