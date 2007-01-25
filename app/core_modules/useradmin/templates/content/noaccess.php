You do not have access to this module
<?php

$this->loadClass('link', 'htmlelements');

$link = new link ($this->uri(NULL, '_default'));
$link->link = 'Return to Home Page';

echo $link->show();

?>