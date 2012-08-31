<?php
$this->loadClass('link', 'htmlelements');
echo $message;
$link = new link ($this->uri(NULL, 'context'));
$link->link = ucwords($this->objLanguage->code2Txt('phrase_backhome', 'system', null,'Back to home'));

echo '<p><br />'.$link->show().'</p>';

?>
