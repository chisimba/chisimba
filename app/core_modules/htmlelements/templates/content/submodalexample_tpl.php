<?php

$objSubModalWindow = $this->getObject('submodalwindow');

echo '<h1> Sub Modal Window Examples</h1>';


echo $objSubModalWindow->show('Click this Button', $this->uri(array('action'=>'submodalexample_content')), 'button');

echo '<br /><br /><br />';

echo $objSubModalWindow->show('Click this Link', $this->uri(array('action'=>'submodalexample_content')), 'link', 400, 200);

?>