<?php

	$objLayer =& $this->newObject('layer', 'htmlelements');
	$objLayer->str = $this->getContent();
	echo $objLayer->show();

	
?>