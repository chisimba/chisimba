<?php


$str = '<center><iframe align=center src="'.$url.'" frameBorder="0" width=500 scrolling=no height=500></iframe>
</center>';

$slide = $this->newObject('link','htmlelements');
$slide->href = $this->uri(array('action' => 'viewalbum', 'albumid' =>$albumInfo['id'], 'mode' => 'flickr'));	
$slide->link = 'View Album';

$link = $this->newObject('link','htmlelements');
$link->href = $this->uri(null,'photogallery');
$link->link = 'Photo Gallery';

$slideshow = '<div id="nextimage" >'.$slide->show().'</div></div>';
$head = '<div id="main2"><div class="imgnav">'.$slideshow.'<div id="gallerytitle">
		<h2><span>'.$link->show().' | </span><img src="http://static.netvibes.com/img/flickr.png"> '.$albumInfo['title'].'
</h2></div>
<div id="albumDesc" style="display: block;">'.$albumInfo['description'].'</div>
	';
print $head;

echo $str;
?>