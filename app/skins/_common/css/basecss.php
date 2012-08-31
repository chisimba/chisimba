<?php

header('Content-type: text/css');
ob_start("compress");

function compress($buffer)
{
  // remove comments
  $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
  // remove tabs, spaces, newlines, etc.
  $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
  return $buffer;
}


$offset = 60 * 60 * 24 * 3;
$ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
header($ExpStr);

$cssArray = array(
    "common.css",
    "cssdropdownmenu.css",
    "htmlelements.css",
    "creativecommons.css",
    "forum.css",
    "calendar.css",
    "cms.css",
    "stepmenu.css",
    "switchmenu.css",
    "colorboxes.css",
    "manageblocks.css",
    "facebox.css",
    "modernbrickmenu.css",
    "jquerytags.css",
    "glossytabs.css",
);

foreach ($cssArray as $cssFile)
{
    echo "\r\n\r\n";
    include("{$cssFile}");
    echo "\r\n\r\n";
}

ob_end_flush();

?>