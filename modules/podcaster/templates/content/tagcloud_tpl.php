<?php

$this->loadClass('htmlheading', 'htmlelements');
$this->loadClass('link', 'htmlelements');

$heading = new htmlheading();
$heading->str = 'Tag Cloud';

$heading->type = 1;

echo $heading->show();

echo '<span style="text-align:center">' . $tagCloud . '</span>';

$homeLink = new link ($this->uri(NULL));
$homeLink->link = 'Back to Home';

echo '<p>'.$homeLink->show().'</p>';

?>