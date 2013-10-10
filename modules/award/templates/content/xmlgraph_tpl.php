<?php
$xmlTemplate = $this->getResourceURI('graphtemplate.xml');
$xmlFile = $this->getResourceURI('graph.xml');
$xmlString = file_get_contents($xmlTemplate);

$content =
"		<row>
            <null />
";
for ($i = $start; $i < ($start+$period); $i++) {
    $content .=
"           <string>$i</string>
";
}
$content .=
"       </row>
";

foreach ($data as $industry => $values) {
    $content .= 
"       <row>
            <string>$industry</string>
";
    foreach ($values as $value) {
        $content .=
"           <number shadow='low' tooltip='$value'>$value</number>
";
        if ($value != '') {
            $min = isset($min)? ($value < $min)? $value : $min : $value;
        }
    }

    $content .=
"       </row>
";
}

$buffer = round($min*10/100);
if (isset($min)) {
    $min = ($buffer < 2)? $min - 2 : $min - $buffer;
} else {
    $min = 0;
}
$xmlString = str_replace('[CONTENT]', $content, $xmlString);
$xmlString = str_replace('[TITLE]', $title, $xmlString);
$xmlString = str_replace('[MIN]', $min, $xmlString);
$fp = fopen($xmlFile, 'wb');
fwrite($fp, $xmlString);
fclose($fp);

$folder = $this->getResourceURI('xmlswfcharts');

$scripts = '<script language="javascript">AC_FL_RunContent = 0;</script>
<script language="javascript"> DetectFlashVer = 0; </script>
<script src="'.$folder.'/AC_RunActiveContent.js" language="javascript"></script>
<script language="JavaScript" type="text/javascript">
<!--
var requiredMajorVersion = 9;
var requiredMinorVersion = 0;
var requiredRevision = 45;
-->
</script>';

$this->appendArrayVar('headerParams', $scripts);
$this->setVar('bodyParams', "style='width:800px;'");

echo "<script language='JavaScript' type='text/javascript'>
<!--
if (AC_FL_RunContent == 0 || DetectFlashVer == 0) {
	alert('This page requires AC_RunActiveContent.js.');
} else {
	var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
	if(hasRightVersion) { 
		AC_FL_RunContent(
			'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,45,0',
			'width', '790',
			'height', '435',
			'scale', 'noscale',
			'salign', 'TL',
			'bgcolor', '#FFF',
			'wmode', 'opaque',
			'movie', '$folder/charts',
			'src', '$folder/charts',
			'FlashVars', 'library_path=$folder/charts_library&xml_source=$xmlFile', 
			'id', 'my_chart',
			'name', 'my_chart',
			'menu', 'true',
			'allowFullScreen', 'true',
			'allowScriptAccess','sameDomain',
			'quality', 'high',
			'align', 'middle',
			'pluginspage', 'http://www.macromedia.com/go/getflashplayer',
			'play', 'true',
			'devicefont', 'false'
			); 
	} else { 
		var alternateContent = 'This content requires the Adobe Flash Player. '
		+ '<u><a href=http://www.macromedia.com/go/getflash/>Get Flash</a></u>.';
		document.write(alternateContent); 
	}
}
// -->
</script>
<noscript>
	<P>This content requires JavaScript.</P>
</noscript>";
echo "<span class='error'>".$this->objLanguage->languageText('mod_award_printchart', 'award')."</span>";
?>