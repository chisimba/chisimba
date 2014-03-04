<?php
/**
* Default layout for maillistadmin
*
* @package maillistadmin
*/

$cssLayout =& $this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(2);

$leftColumn =& $this->newObject('sidemenu','toolbar');

$cssLayout->setLeftColumnContent($leftColumn->show('user'));
$cssLayout->setMiddleColumnContent($this->getContent());

echo $cssLayout->show();

?>
