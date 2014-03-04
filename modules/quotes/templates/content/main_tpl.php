<?php
//Get the window width and height
$width = $this->getParam('width', NULL);
$height = $this->getParam('height', NULL);

//Apply the height and width if set
if ($width && $height) {
    //Set the header JS
    $js="<SCRIPT LANUAGE=\"JavaScript 1.2\">\n"
      . "<!--\nfunction rSiz() {\n    window.resizeTo("
      . $width . "," . $height . ")\n}\n"
      . "//-->\n</SCRIPT>";
    $this->appendArrayVar('headerParams',$js);
    $bodyLoad = " onload=\"rSiz();\"";
    $this->setVarByRef('bodyParams', $bodyLoad);
}

   
    //Set up the output
    $out = "<div align=\"center\" style=\"line-height: +1\">"
    . "<font size=\"+6\">" . $ar['quote'] . "</font>"
    . "<br /><font size=\"+3\">--" . $ar['whosaidit']. "</font>"
    . "</div>";

//Output the content to the page
echo $out;

?>