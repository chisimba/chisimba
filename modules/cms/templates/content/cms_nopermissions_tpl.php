<?php
/**
 * This template is used when user's without permissions attempt to edit content
 */


echo "<span class='error'>".$this->getVar('errMessage')."</span>";

$mustLogin = $this->getVar('mustlogin');
if ($mustLogin) {
	echo "<br/><a href='?module=security&message=needlogin&action=error'> Login </a>";
}

?>
