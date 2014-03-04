<?php

$link = $this->getObject('link','htmlelements');
$objThumbnail = & $this->getObject('thumbnails','filemanager');
$this->loadClass('htmltable','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$h = $this->getObject('htmlheading','htmlelements');
$form = $this->getObject('form', 'htmlelements');
$objDomTT = $this->getObject('domtt','htmlelements');
$objDomTT->putScripts();


$str = '';
$nav = '';
$strComment = '';


$scripts = '<script type="text/javascript" src="'.$this->_objConfig->getModuleURI().'photogallery/resources/lightbox/js/prototype.js"></script>
<script type="text/javascript" src="'.$this->_objConfig->getModuleURI().'photogallery/resources/lightbox/js/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="'.$this->_objConfig->getModuleURI().'photogallery/resources/lightbox/js/lightbox.js"></script>
<link rel="stylesheet" href="'.$this->_objConfig->getModuleURI().'photogallery/resources/lightbox/css/lightbox.css" type="text/css" media="screen" />';
$this->appendArrayVar('headerParams',$scripts);
$str = '<div id="image">';

 

$link->href = $this->_objFlickr->buildPhotoURL($image, "Medium");
$thumb = '<img  src="'.$this->_objFlickr->buildPhotoURL($image, "Medium").'">';
$link->link = $thumb;
$link->extra = ' rel="lightbox" ';
$str.=$link->show().'</div>';
//	print '<pre>';
//var_dump($this->_objFlickr->photos_getSizes($this->getParam('imageid')));
//var_dump($image);

$form->action = $this->uri(array('mode' => 'flickr', 'action' => 'addflickrcomment', 'imageid' => $this->getParam('imageid'), 'albumid' => $this->getParam('albumid')));

$name = new textinput('name');
$form->addRule('name','Please suppy a name!', 'required');

$table = new htmltable();
$table->width = '200';
$table->startRow();
$table->addCell('<label for="name">Name:</label>');
$table->addCell($name->show());
$table->endRow();

$email = new textinput('email');
$table->startRow();
$table->addCell('<label for="email">E-Mail:</label>');
$table->addCell($email->show());
$table->endRow();

$website = new textinput('website');
$table->startRow();
$table->addCell('<label for="website">Site:</label>');
$table->addCell($website->show());
$table->endRow();

$commentField = new textarea('comment');
$button = new button();
$button->value = 'Add Comment';
$button->setToSubmit();

$this->setVar('pageTitle', 'Photo Gallery - '.$albums['title'].' - '.$image['title']);

$form->addToForm('<h3>Add a comment</h3>'/*.$table->show()*/);
$form->addToForm($commentField->show().'<br/>'.$button->show());

//print '<pre>';
//var_dump($this->_objFlickr->photos_getAllContexts($image['id']));
if (array_key_exists('comment', $comments)) 
{
	
	
	if(count($comments['comment']) > 0)
	{
	 	$strComment = '<h3>Comments ('.count($comments['comment']).')</h3>';
		foreach($comments['comment'] as $comment)
		{
			$strComment .= '<div class="comment"><div class="commentmeta"><span class="commentauthor">'.$comment['authorname'].'</span> says:'; 
			$strComment .=	'</div>	<div class="commentbody">'.$comment['_content'].'</div><div class="commentdate">';
			$strComment .= date("l, j F Y , g:i A", $comment['datecreate']).'</div>	</div>';
			
		}
		
	
	} else {
		$strComment = '';
	}
}
$link->extra = '';
$link->href = $this->uri(array('action' => 'front'),'photogallery');
$link->link = 'Photo Gallery';
$galLink = $link->show();

$link->href = $this->uri(array('mode' => 'flickr','action' => 'viewalbum', 'albumid' =>$this->getParam('albumid') ),'photogallery');
$link->link = $albums['title'];
$albumLink = $link->show();

echo $nav;

$arrNav = $this->_objFlickr->photos_getContext($image['id']);
//print '<pre>';
//print_r($arrNav);
$nav = '<div class="imgnav">';
if($arrNav['prevphoto']['id'] != '')
{
	$nav .= '<div class="imgprevious">';
	$nav .='<a href="'.$this->uri(array('action' => 'viewimage','mode' => 'flickr', 'imageid' => $arrNav['prevphoto']['id'], 'albumid' => $this->getParam('albumid'))).'" 
	onmouseover="domTT_activate(this, event, \'content\', document.getElementById(\'previousimage\'));">&laquo; prev</a></div>';	
	
	$nav .= '<div  style="display: none">
							<div id="previousimage" >		
								<img src="'.$arrNav['prevphoto']['thumb'].'" />
							</div>
						</div>';
}
if($arrNav['nextphoto']['id'] != '')
{
 	$nav .= '<div class="imgnext">';
 	$nav .= '<a href="'.$this->uri(array('action' => 'viewimage','mode' => 'flickr', 'imageid' => $arrNav['nextphoto']['id'], 'albumid' => $this->getParam('albumid'))).'"
	 			 title="Next Image - '.$arrNav['nextphoto']['title'].'">next &raquo;</a></div>';	
	
}

$nav .= '</div>';

//print '<pre>';
//var_dump($arrNav);
			
$head = '<div id="main2">'.$nav.'<div id="gallerytitle">
		<h2><span>'.$galLink.' | </span> <span><img src="http://static.netvibes.com/img/flickr.png">'.$albumLink.'
		| </span>'.$image['title'].'
		</h2></div><a title="View this image on Flickr" href="'.$image['urls']['url'][0]['_content'].'"><img alt="View this image on Flickr" src="http://l.yimg.com/www.flickr.com/images/flickr_logo_gamma.gif.v1.5.7"></a>

	';

$desc  = '<div id="narrow"><div id="imageDesc" style="display: block;">'.$image['description'].'</div>';
	
print $head;
echo $desc;
echo $str;

echo $strComment;
echo $form->show().'</div></div>';

?>