<?php
$albumInfo = $this->_objFlickr->photosets_getInfo($this->getParam('albumid'));


$str = '';
$link = $this->newObject('link','htmlelements');
$objThumbnail = & $this->getObject('thumbnails','filemanager');

$slide = $this->newObject('link','htmlelements');
$slide->href = $this->uri(array('action' => 'viewslideshowalbum', 'albumid' =>$albumInfo['id'], 'owner' => $albumInfo['owner']));	
$slide->link = 'View Slide Show';

$link->href = $this->uri(null,'photogallery');
$link->link = 'Photo Gallery';


$this->setVar('pageTitle', 'Photo Gallery - '. $albumInfo['title']);


             

$slideshow = '<div id="nextimage" >'.$slide->show().'</div></div>';

$head = '<div id="main2"><div class="imgnav">'.$slideshow.'<div id="gallerytitle">
		<h2><span>'.$link->show().' | </span><img src="http://static.netvibes.com/img/flickr.png"> '.$albumInfo['title'].'
</h2></div>
<div id="albumDesc" style="display: block;">'.$albumInfo['description'].'</div>
	';
print $head;

if($images)
{
	
	foreach($images['photo'] as $image)
	{
		$str.='<div class="image"><div class="imagethumb">';
	
	 	$link->href = $this->uri(array('action' => 'viewimage', 'albumid' => $this->getParam('albumid'),'imageid' => $image["id"], 'mode' => 'flickr'));
	  	$link->link = '<img  src="'.$this->_objFlickr->buildPhotoURL($image, "Square").'">';
	 	//$link->extra = ' rel="lightbox" ';
		$str.=$link->show().'</div></div>';
	}
	
	print $str.'
	
	<div class="pagelist">
<ul class="pagelist">
  
</ul>
</div></div>';
}else {
	
	print 'No Photos for '.$albumInfo['title'];
} 
?>