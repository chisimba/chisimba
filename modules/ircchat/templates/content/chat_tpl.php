<?php

/**
 *
 *
 * @version $Id: chat_tpl.php 11681 2008-12-03 14:37:08Z charlvn $
 * @copyright 2007
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
$objDbContext = &$this->getObject('dbcontext','context');
$contextCode = $objDbContext->getContextCode();
// Are we in a context ?
if ($contextCode == NULL) {
    $context = "Lobby";
}
else {
    $context = $objDbContext->getTitle();
}
$objConfig = $this->getObject('altconfig', 'config');
$uri = $objConfig->getModuleURI();

// Applet
?>
<applet
codebase="<?= $uri ?>/ircchat/resources/"
code="IRCApplet.class"
archive="
    irc.jar,
    pixx.jar
"
width="640"
height="400"
>
<param name="CABINETS"
value="
    irc.cab,
    securedirc.cab,
    pixx.cab
"
/>
<param name="nick" value="<?= $userName ?>"/>
<param name="alternatenick" value="Guest"/>
<param name="name" value="Java User"/>
<param name="host" value="irc.uwc.ac.za"/>
<param name="gui" value="pixx"/>
<param name="command1" value="join #<?= $context ?>"/>
</applet>