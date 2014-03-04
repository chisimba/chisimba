<?php

$fileId = $this->getParam('fileid', NULL);
$flashUrl =  $this->objConfig->getsiteRoot()
  . $this->objConfig->getcontentPath()
  .'webpresent/'  .$fileId .'/'. $fileId.'.swf';



$snippetText = '<item> 
    <![CDATA[
    <div style="border: 1px solid #000; width: 534px; height: 402px; text-align: center;">
    <object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ' .
  'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" ' .
  'width="540" height="400">
    <param name="movie" value="' . $flashUrl . '">
    <param name="quality" value="high">
    <embed src="'.$flashUrl.'" quality="high" ' .
  'pluginspage="http://www.macromedia.com/go/getflashplayer" ' .
  'type="application/x-shockwave-flash" width="534" ' .
  'height="402"></embed>
     </object>
     </div>]]>
</item>';
echo $snippetText;
?>