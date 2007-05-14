<?php
// $Id$

require_once 'HTML/TagCloud.php';

$basefontsize = 36;
$fontsizerange = 24;
// Tag size range in ($basefontsize - $fontsizerange) to ($basefontsize + $fontsizerange).
$tags = new HTML_TagCloud($basefontsize, $fontsizerange);
$name = 'a';
// set a item without timestamp
foreach(range(0,15) as $i){
    $arr[$i]['name'] = $name;
    $arr[$i]['url']  = '#';
    $arr[$i]['count'] = $i;
    $name++;
}
// set many item at once by array
$tags->addElements($arr);
$tags->addElement('H', '#', 16);
$tags->addElement('P', '#', 18);

// CSS part only
$css = $tags->buildCSS();
// html part only
$taghtml = $tags->buildHTML();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=EUC-JP">
<meta http-equiv="Content-Script-Type" content="text/javascript">
<title></title>
<style type="text/css">
<?php 
print $css;
?>
</style>
</head>
<body>
<?php
print $taghtml;
?>
</body>
</html>
