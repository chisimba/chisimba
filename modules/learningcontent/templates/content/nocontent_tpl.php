<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->code2Txt('mod_learningcontent_nocontentincourse','learningcontent');

echo $header->show();

echo '<p>'.$this->objLanguage->code2Txt('mod_learningcontent_lecturershavenotuploadedcontent','learningcontent').'</p>';

$homelink = new link ($this->uri(NULL, '_default'));
$homelink->link = $this->objLanguage->code2Txt('mod_learningcontent_returntohomepage','learningcontent');

$courselink = new link ($this->uri(NULL, 'context'));
$courselink->link = $this->objLanguage->code2Txt('mod_learningcontent_returntocoursehome','learningcontent');

echo '<p>'.$courselink->show().' / '.$homelink->show().'</p>';

?>
