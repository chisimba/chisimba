<?php

/**
 * 
 *
 * @version $Id$
 * @copyright 2003 
 **/


?>

<script src="modules/tree/resources/TreeMenu.js" language="JavaScript" type="text/javascript"></script>
<script language="JavaScript" type="text/javascript">
<!--
	a = new Date();
	a = a.getTime();
//-->
</script>

<?
echo $treemenu;
echo $listbox;
?>
<script language="JavaScript" type="text/javascript">
<!--
	b = new Date();
	b = b.getTime();
	
	document.write("Time to render tree: " + ((b - a) / 1000) + "s");
//-->
</script>
