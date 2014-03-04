<?php
//Sending display to 1 column layout
ob_start();

$this->loadClass('link', 'htmlelements');

$link = new link ($this->uri(array('action'=>'forum', 'id'=>$forum['id'])));
$link->link = $forum['forum_name'];

echo '<h1>Mind Map View of '.$link->show().'</h1>';


$objFreeMindMap = $this->getObject('flashfreemind', 'freemind');
$objFreeMindMap->setMindMap($map.'&ext=.mm');

echo $objFreeMindMap->show();

$link = new link ($this->uri(array('action'=>'forum', 'id'=>$forum['id'])));
$link->link = 'Return to Forum - '.$forum['forum_name'];

echo '<p align="center">'.$link->show().'</p>';

$display = ob_get_contents();
ob_end_clean();

$this->setVar('middleColumn', $display);
?>
