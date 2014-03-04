<?php

$link = $this->getObject('link','htmlelements');
$objThumbnail = & $this->getObject('thumbnails','filemanager');
$this->loadClass('htmltable','htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('textarea','htmlelements');
$this->loadClass('dropdown','htmlelements');
$this->loadClass('button','htmlelements');
$h = $this->getObject('htmlheading','htmlelements');
$icon = $this->getObject('geticon','htmlelements');
$form = $this->getObject('form', 'htmlelements');

$table = new htmltable();
$table->cssClass = 'bordered';
$table->startHeaderRow();
$table->addHeaderCell('&nbsp;');
$table->addHeaderCell('Image');
$table->addHeaderCell('Author');
$table->addHeaderCell('Date/Time');
$table->addHeaderCell('Comment');
$table->addHeaderCell('Email');
$table->addHeaderCell('&nbsp;');
$table->endHeaderRow();

if(count($comments) > 0)
{

	foreach($comments as $comment)
	{
		$table->startRow();
		$table->addCell('&nbsp;');
		
		$image = $this->_objDBImage->getRow('id', $comment['file_id']);
		//var_dump($image);
		$albumTitle = $this->_objDBAlbum->getAlbumTitle($image['album_id']);
		
		$link->href = $this->uri(array('action' => 'viewimage', 'albumid' => $image['album_id'], 'imageid' => $image['id']));
		$link->link = $albumTitle.' / '.$image['title'];
		$table->addCell($link->show());
		$table->addCell($comment['name']);
		$table->addCell($comment['commentdate']);
		$table->addCell($comment['comment']);
		$link->href = 'mailto:'.$comment['email'].'?body='.$comment['comment'];
		$link->link = 'Reply';
		$table->addCell($link->show());
		
		$editIcon = $icon->getEditIcon($this->uri(array('action' => 'editcomment' ,'commentid' => $comment['id'])));
		$delIcon = $icon->getDeleteIconWithConfirm($comment['id'], array('action' => 'deletecomment', 'commentid' => $comment['id']), 'photogallery');
		$table->addCell($editIcon.' '.$delIcon);
		$table->endRow();
	}
}

echo '<div id="main"><h2>Comments</h2>

You can edit or delete comments on your photos.';

echo $table->show().'</div>';
?>