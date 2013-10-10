<?php
if(!isset($stats))
{
	$stats = '';
}

$h = $this->getObject('htmlheading','htmlelements');
$link = $this->getObject('link','htmlelements');

$h->type = 2;
$h->str = 'My Gallery Administration';
echo $h->show();

$str = '<ul id="home-actions">';

$link->href = $this->uri(array('action' => 'uploadsection'));
$link->link = '<strong>Upload</strong> pictures.';
$str .= '<li>'.$link->show().'</li>';

$link->href = $this->uri(array('action' => 'editsection'));
$link->link = '<strong>Edit</strong> titles, descriptions, and other metadata.';
$str .= '<li>'.$link->show().'</li>';

$link->href = $this->uri(array('action' => 'comments'));
$link->link = ' Edit or delete <strong>comments</strong>.';
$str .= '<li>'.$link->show().'</li>';

$link->href = $this->uri(array('action' => 'front'));
$link->link = ' Browse my <strong>gallery</strong>';
$str .= '<li>'.$link->show().'</li></ul><hr />';

$str .='<br style="clear:both"><div class="box" id="overview-comments">
        <h2>10 Most Recent Comments</h2><ul>';
    
foreach($tencomments as $tencomment)
{
	$image = $this->_objDBImage->getRow('id', $tencomment['file_id']);
		//var_dump($image);
	$albumTitle = $this->_objDBAlbum->getAlbumTitle($image['album_id']);
		
	$link->href = $this->uri(array('action' => 'viewimage', 'albumid' => $image['album_id'], 'imageid' => $image['id']));
	$link->link = $albumTitle.' / '.$image['title']; 
	$str .=' <li><div class="commentmeta">'.$tencomment['name'].' commented on ';
	$str .=  $link->show();
	$str .= ':</div><div class="commentbody">'.$tencomment['comment'].'</div></li>';
	
}


      
echo '<div id="main">'.$str.'</ul></div><br style="clear:both"></div>';      

?>