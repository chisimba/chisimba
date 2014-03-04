<?php

/**
 * default_tpl.php
 *
 * @version $Id: default_tpl.php 11681 2008-12-03 14:37:08Z charlvn $
 * @copyright (c) 2007 Avoir
 */

/* ------------icon request template----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check

// Get the nick name
if ($objUser->isLoggedIn()) {
    $userName = $this->objUser->userName();
} else {
    $userName = "Guest";
}
// Get the context
$objDbContext = $this->getObject('dbcontext','context');
$contextCode = $objDbContext->getContextCode();
// Are we in a context ?
if ($contextCode == NULL) {
    $context = "Lobby";
}
else {
    $context = $objDbContext->getTitle();
}
// Get the module URI
$objConfig = $this->getObject('altconfig', 'config');
$uri = $objConfig->getModuleURI();

?>
<div id="main">
</div>
<script type="text/javascript">
// <![CDATA[
if ( navigator.javaEnabled() ) {
    //window.location="<?= $this->uri(array('action'=>'enabled')) ?>";
    document.getElementById('main').innerHTML =
'<applet '
+'codebase="<?= $uri ?>ircchat/resources/" '
+'code="IRCApplet.class" '
+'archive="'
+'    irc.jar,'
+'    pixx.jar'
+'" '
+'width="640" '
+'height="400" '
+'>'
+'<param name="CABINETS" '
+'value="'
+'    irc.cab,'
+'    securedirc.cab,'
+'    pixx.cab'
+'" '
+' />'
+'<param name="nick" value="<?= $userName ?>" />'
+'<param name="alternatenick" value="Guest" />'
+'<param name="name" value="Java User" />'
+'<param name="host" value="irc.uwc.ac.za" />'
+'<param name="gui" value="pixx" />'
+'<param name="command1" value="join #<?= $context ?>" />'
+'</applet>';
}
else {
    //window.location="<?= $this->uri(array('action'=>'notenabled')) ?>";
    document.getElementById('main').innerHTML = 'The JRE is not installed! Please install the JRE by downloading it from <a href="http://www.java.com/en/download/manual.jsp">here</a>.';
}
// ]]>
</script>