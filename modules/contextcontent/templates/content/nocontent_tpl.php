<?php

$this->loadClass('link', 'htmlelements');
$this->loadClass('htmlheading', 'htmlelements');

$header = new htmlheading();
$header->type = 1;
$header->str = $this->objLanguage->code2Txt('mod_contextcontent_nocontentincourse','contextcontent');

echo $header->show();

echo '<p class="lecturershavenotuploadedcontent">'.$this->objLanguage->code2Txt('mod_contextcontent_lecturershavenotuploadedcontent','contextcontent').'</p>';

$homelink = new link ($this->uri(NULL, '_default'));
$homelink->link = $this->objLanguage->code2Txt('mod_contextcontent_returntohomepage','contextcontent');

$courselink = new link ($this->uri(NULL, 'context'));
$courselink->link = $this->objLanguage->code2Txt('mod_contextcontent_returntocoursehome','contextcontent');

echo '<p class="courseandhomelink">'.$courselink->show().' / '.$homelink->show().'</p>';

?>