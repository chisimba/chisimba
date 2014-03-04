<?php
$this->loadClass('htmltable','htmlelements');
$link = $this->getObject('link','htmlelements');
$form = $this->getObject('form', 'htmlelements');
$icon = $this->getObject('geticon', 'htmlelements');
$this->loadClass('textinput','htmlelements');
$this->loadClass('button','htmlelements');


$form->action = $this->uri(array('action' => 'validateflickusername'));
$form->displayType = 2;

$cnt =0 ;
$str = '';
$list ='';

//$table->cssId = "edittable";
//$table->width = '50%';

$username = new textinput('username');
$username->label = 'Flickr Username:';

$password = new textinput('password');
$password->label = 'Password:';
$button = new button();
$button->value = 'Add';
$button->setToSubmit();

$form->addToForm($username);
//$form->addToForm($password);
$form->addToForm($button);
if ($this->getParam('msg') != '' ) 
{
    $message = $this->getParam('msg');
    $this->setSession('displayconfirmationmessage', FALSE);
	    
    $timeoutMessage = $this->getObject('timeoutmessage', 'htmlelements');
    $timeoutMessage->setMessage($message);
    $timeoutMessage->timeout = 10000;

    echo '<p>'.$timeoutMessage->show().'</p>';
}
if(count($usernames) > 0)
{
 	$table = new htmltable();
	$table->cssClass = 'bordered';
	$table->startHeaderRow();
	$table->width = '50%';
	$table->addHeaderCell('Username');
	/*$table->addHeaderCell('View Albums/Sets',30);
	$table->addHeaderCell('Upload Images',30);
	$table->addHeaderCell('Add Comments',30);
	$table->addHeaderCell('Edit Images',30);
	*/
	$table->addHeaderCell('');
	$table->endHeaderRow();

	foreach($usernames as $username)
	{
	 	$table->startRow();
		$table->addCell($username['flickr_username']);		
		/*$table->addCell('');
		$table->addCell('');
		$table->addCell('');
		$table->addCell('');
		*/
		$table->addCell($icon->getDeleteIconWithConfirm($username['flickr_username'],array('action' => 'deleteflickrusername', 'username' => $username['flickr_username']),'photogallery'),null,'center');
		
		$table->endRow();
	}
	
	$list =  $table->show();
} else {
	//$list = 'No Flickr usernames available';
}
echo '<div id="main"><h1><img src="http://l.yimg.com/www.flickr.com/images/flickr_logo_gamma.gif.v1.5.7">My Flickr Usernames</h1>';
if(isset($msg))
{
	echo '<span class="warning>"'.$msg.'</span>';
}
echo '<div class="box" style="padding: 15px;">'.$form->show().$list.'</div>';
echo '</div>'
?>