<?php
$str = '';
$link = $this->getObject('link','htmlelements');

$popTags ='<div style="padding: 10px; margin-left:5px; border: solid 2px #eee; width:550px; background: #f5f5f5;">
	<h3>  Popular Tags</h3>
	<div style = "position:static; padding: 10px; margin-top:1px; margin-left:5px; border: solid 2px #eee; width:500px; background: #f5f5f5;">';
$popTags .= $cloud;
$popTags .= "</div></div>";

$images =$imagelist;

//echo $str;

if(isset($taggedImages))
{
	$tagged = $taggedImages;
}
if(!isset($tagged))
{
	$tagged = NULL;
}

if($this->_objUser->isLoggedIn())
{
	
	$link->href = $this->uri(array('action'=>'front'),'photogallery');
	$link->link = 'My Gallery';
	$mygal = $link->show();
	
	$link->href = $this->uri(array('action'=>'front', 'mode' => 'shared'),'photogallery');
	$link->link = 'Shared Photos';
	$shared = $link->show();
	
	$head = '<div id="gallerytitle">
		<h2><span>'.$mygal.' | </span> <span>'.$shared.' | </span>Popular</h2></div>	';

	
} else {
	$link->href = $this->uri(array('action'=>'front'),'photogallery');
	$link->link = 'Photo Gallery';
	$head = '<div id="gallerytitle">
		<h2><span>'.$link->show().' | </span> Popular</h2></div>	';

}

echo ' <div id="main2" style="align:center;">
                '.$head.'
                '.$popTags.'
                
                '.$tagged.'         
                
                '.$images.'
			    </div>
		    
	   
';


?>