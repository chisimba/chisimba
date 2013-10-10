<?php
/* ------------iconrequest class extends controller ----------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

$this->loadClass('textinput','htmlelements');
$this->loadClass('button','htmlelements');

//initialise the form buttons
$objUpButton = new button('upload',$objLanguage->languageText('word_upload'));
$objUpButton->setToSubmit();
$objUpload = new textinput('idea_upload','','file',30);
$objClear = new button('clear',$objLanguage->languageText('word_clear'));
$returnUrl = $this->uri(array('action'=>'tempframe'));
$objClear->setOnClick("window.location = '$returnUrl'");

$reqId = $this->getParam('Id'); //unique request id linking the request with its files
$ic = $this->getParam('icon');

//create table
$table = $this->newObject('htmltable','htmlelements');
$table->width = '100%';
$table->cellspacing = '3';
$table->cellpadding = '2';

//upload icon row
$table->startRow();
$table->addCell($objUpload->show());
$table->endRow();

$buttonTable = $this->newObject('htmltable','htmlelements');
$buttonTable->width = '100%';
$buttonTable->cellspacing = '3';
$buttonTable->cellpadding = '2';
$buttonTable->startRow();
If ($ic != 1) { //dont show the upload button if an icon is already uploaded
	$buttonTable->addCell($objUpButton->show(),Null,Null,'right');
	$buttonTable->addCell($objClear->show(),Null,Null,'left');
}

$buttonTable->endRow();

//init form
$objForm = new form('upload_form',$this->uri(array('action'=>'tempframe','icon'=>'1','Id' => $reqId)));
$objForm->extra = "enctype='multipart/form-data'";
$objForm->setDisplayType(4);
//max upload size
$maxFile = new textinput('MAX_FILE_SIZE','3000000','hidden');
$res = Null;

if ($ic == 1) { //if the user has uploaded a file
	if ($this->getParam('case')=='edit') {
		$pic = &$this->getObject('image','htmlelements');
		$example = $this->dbFile->getRow('reqId',$reqId);
		$fileName = $example['filename'];
		$fileLink = $this->config->siteRoot()."usrfiles/assets/".$fileName;
		$pic->src = $fileLink;
		$pic->width = 70;
		$pic->height = 70;
		$thumb = $pic->show();
		$link = "<a href='$fileLink' target='_top'>{$thumb}</a>";
		$objDel = $this->newObject('link', 'htmlelements');//provide link to delete file
		$objDel->link($this->uri(array('action' => 'tempframe', 'delete' => '1', 'Id' => $reqId, 'file' => $this->config->contentBasePath().'assets/'.$fileName)));
		$objDel->link = $objLanguage->languageText('word_delete');
		$objDel->extra = 'class=pseudobutton';
		$res = ($link.' &nbsp;&nbsp; '.$objDel->show());
	} else {
		$user = $this->objUser->userName();
		$dir = "assets/";
		$dest = $this->config->contentBasePath().$dir;
		$fName = time().basename($_FILES['idea_upload']['name']); //destination filename with timestamp so it is unique
		$size = $_FILES['idea_upload']['size'];
		$type = $_FILES['idea_upload']['type'];
		$upload = $dest.$fName;
		//check if filesize is zero
		if ($size == 0) {
			$buttonTable->addCell($objUpButton->show(),Null,Null,'right');
			$buttonTable->addCell($objClear->show(),Null,Null,'left');
			$res = &$this->getObject('timeoutMessage','htmlelements');
			$res->setMessage($this->objLanguage->languageText("filesize_zero"));
		} else {	//not zero so upload file
			if (strncmp('image',$type,5) == 0) {
				if (move_uploaded_file($_FILES['idea_upload']['tmp_name'], $upload)) { //move the file from the tmp dir to the assets directory
   					$objDel = $this->newObject('link', 'htmlelements');//provide link to delete file
					$objDel->link($this->uri(array('action' => 'tempframe', 'delete' => '1', 'Id' => $reqId, 'file' => $upload)));
					$objDel->link = $objLanguage->languageText('word_delete');
					$objDel->extra = 'class=pseudobutton';
					$this->dbFile->insertFile($reqId,$fName,$size,$user); //insert link to file into database
					$fileLink=$this->config->siteRoot()."usrfiles/assets/".$fName;
					$pic = &$this->getObject('image','htmlelements');
					$pic->src = $fileLink;
					$pic->width = 70;
					$pic->height = 70;
					$thumb = $pic->show();
					$link = "<a href='$fileLink' target='_top'>{$thumb}</a>";
					$res = $link.' '.$objDel->show();
   				} else {
  					$buttonTable->addCell($objUpButton->show(),Null,Null,'right');
					$buttonTable->addCell($objClear->show(),Null,Null,'left');
					$res = &$this->getObject('timeoutMessage','htmlelements');
					$res->setMessage($this->objLanguage->languageText("file_attack"));
				}
			} else {
				$buttonTable->addCell($objUpButton->show(),Null,Null,'right');
				$buttonTable->addCell($objClear->show(),Null,Null,'left');
				$res = &$this->getObject('timeoutMessage','htmlelements');
				$res->setMessage($this->objLanguage->languageText("invalid_filetype"));
			}
		}
	}
}

$objForm->addToFormEx($table,$maxFile);
$objForm->addToFormEx($buttonTable);
$objForm->addToFormEx($res);

if ($this->getParam('delete') == 1) { //delete the file
	unlink($this->getParam('file')); //delete from server
	$this->dbFile->deleteFile($this->getParam('Id')); //delete from database
}

//rule to ensure a filename is inserted
$objForm->addRule('idea_upload',$objLanguage->languageText('filename_req_msg'),'required');

$content = $objForm->show();
echo $content;

?>
