<?php
// ----------------------------------------------------------------------------------
// RDFDBUtils : Header
// ----------------------------------------------------------------------------------

/** 
 * Header - at the top of each page
 * 
 * @version $Id: header.php 268 2006-05-15 05:28:09Z tgauss $
 * @author   Gunnar AAstrand Grimnes <ggrimnes@csd.abdn.ac.uk>
 *
 **/
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
  <head>
    <title><?php print (isset($title)?$title:"")?></title>
    <link rel="stylesheet" href="style.css" type="text/css" />
<?php if (isset($externalscript)) { ?>
<script language="javascript" src="<?php print $externalscript?>"></script>
<?php } ?>
<?php if (isset($script)) { ?>
    <script type="text/javascript">
<?php print $script?>
    </script>
<?php }?>
 
  </head>
 
  <body<?php print (isset($onBodyLoad)?" onLoad=\"$onBodyLoad\"":"")?>>

