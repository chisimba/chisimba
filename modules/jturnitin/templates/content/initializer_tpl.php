<?php

 $objExtJS = $this->getObject('extjs','ext');
$objExtJS->show();
 $this->appendArrayVar('headerParams', '
	        	<script language="JavaScript" src="'.$this->getResourceUri('alerts.js').'" type="text/javascript"></script>');



?>
