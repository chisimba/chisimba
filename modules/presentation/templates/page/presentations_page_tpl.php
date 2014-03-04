<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
//Get the theme
if (!isset($title)) {
    $title = "S5 Presentation";
}

//Get the UI path
if (!isset($uiPath)) {
    die("uiPath is not set. This error should only occur during development.");
}

//Get the theme
if (!isset($theme)) {
    $theme = "default";
} else {
    $theme = $theme;
}

//Get the footer
if (!isset($footer)) {
    $footer =  "Powered by: Chisimba/S5";
}


?>
<head>
<title><?php echo $title; ?></title>
<!-- metadata -->
<meta name="generator" content="S5" />
<meta name="version" content="S5 1.2a2" />
<meta name="author" content="Eric A. Meyer" />
<meta name="company" content="Complex Spiral Consulting" />
<!-- configuration parameters -->
<meta name="defaultView" content="slideshow" />
<meta name="controlVis" content="hidden" />
<!-- style sheet links -->
<link rel="stylesheet" href="<?php echo $uiPath; ?>ui/<?php echo $theme; ?>/slides.css" type="text/css" media="projection" id="slideProj" />
<link rel="stylesheet" href="<?php echo $uiPath; ?>ui/<?php echo $theme; ?>/outline.css" type="text/css" media="screen" id="outlineStyle" />
<link rel="stylesheet" href="<?php echo $uiPath; ?>ui/<?php echo $theme; ?>/print.css" type="text/css" media="print" id="slidePrint" />
<link rel="stylesheet" href="<?php echo $uiPath; ?>ui/<?php echo $theme; ?>/opera.css" type="text/css" media="projection" id="operaFix" />
<!-- embedded styles -->
<style type="text/css" media="all">
.imgcon {width: 525px; margin: 0 auto; padding: 0; text-align: center;}
#anim {width: 270px; height: 320px; position: relative; margin-top: 0.5em;}
#anim img {position: absolute; top: 42px; left: 24px;}
img#me01 {top: 0; left: 0;}
img#me02 {left: 23px;}
img#me04 {top: 44px;}
img#me05 {top: 43px;left: 36px;}
</style>
<!-- S5 JS -->
<script src="<?php echo $uiPath; ?>ui/default/slides.js" type="text/javascript"></script>
</head>
<body>
<!-- the layout setup for title and footer --->
<div class="layout">
<div id="controls"><!-- DO NOT EDIT --></div>
<div id="currentSlide"><!-- DO NOT EDIT --></div>
<div id="header"></div>
<div id="footer">
<h1><?php echo$title ?></h1>
<h2><?php echo $footer ?></h2>
</div>
</div>

<!--body content here--->
<?php echo $this->getLayoutContent(); ?> 

</body>
</html>
