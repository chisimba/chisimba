<?php
//Create an instance of the css layout class
$cssLayout = $this->newObject('csslayout', 'htmlelements');
//Set columns to 2
$cssLayout->setNumColumns(2);
//Put the caution in the left panel
$caution= "<h1>Under no circumstances should this be run on anything than a developer machine</h1>";
//Add the templage heading to the main layer
$objH = $this->getObject('htmlheading', 'htmlelements');
//Heading H3 tag
$objH->type=3; 
$objH->str = $objLanguage->languageText("mod_phpinfo_title", 'phpinfo');
$leftSideColumn = $objH->show() . "<br />" . $caution;

//Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);
// Add Right Column
ob_start();
phpinfo();
$str = ob_get_contents();
ob_end_clean();
$str = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">',"", $str);
$str = str_replace("<html>", "", $str);
$str = str_replace("<head>", "", $str);
$str = str_replace("</html>", "", $str);
$str = str_replace("</head>", "", $str);
$str = str_replace("<title>phpinfo()</title>", "", $str);
$str = str_replace("<body>", "", $str);
$str = str_replace("</body>", "", $str);
$css = "body {background-color: #ffffff; color: #000000;}
body, td, th, h1, h2 {font-family: sans-serif;}
pre {margin: 0px; font-family: monospace;}
a:link {color: #000099; text-decoration: none; background-color: #ffffff;}
a:hover {text-decoration: underline;}";
$str = str_replace($css, "", $str);
$css = "img {float: right; border: 0px;}";
$cssRep = "img {float: left; border: 0px;}";
$str = str_replace($css, $cssRep, $str);


$cssLayout->setMiddleColumnContent($str);
//Output the content to the page
echo $cssLayout->show();


?>
