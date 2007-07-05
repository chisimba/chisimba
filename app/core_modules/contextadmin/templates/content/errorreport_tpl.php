<?php

$objH = & $this->newObject('htmlheading','htmlelements');
$objForm = & $this->newObject('form','htmlelements');
$inpButton =  $this->newObject('button','htmlelements');

//Button
$inpButton->cssClass = 'f-submit';
$inpButton->setValue('Course Admin');
$inpButton->setToSubmit();
if($uploadStatus == '1')
	$uploadStatus = 'success';
switch ($uploadStatus) 
{
	case 'zipFileError':
		$message = 'Incorrect zipfile';
	break;
	case 'unzipError':
		$message = 'Permissions Error';
	break;
	case 'fileReadError':
		$message = 'Cant read zipfile';
	break;
	case 'imsReadError':
		$message = 'Cant read ims manifest file';
	break;
	case 'simpleXmlError':
		$message = '';
	break;
	case 'domReadError':
		$message = '';
	break;
	case 'xpathSetError':
		$message = '';
	break;
	case 'courseReadError':
		$message = '';
	break;
	case 'initializeError':
		$message = '';
	break;
	case 'courseWriteError':
		$message = 'Course duplication';
	break;
	case 'writeResourcesError':
		$message = '';
	break;
	case 'noStructureError':
		$message = '';
	break;
	case 'loadDataError':
		$message = '';
	break;
	case 'uploadError':
		$message = '';
	break;
	case 'rebuildHtmlError':
		$message = '';
	break;
	case 'success':
		$message = 'Successful Upload';
	break;
	default:
		$message = 'Unexpected Error';
	break;
}

echo "Message = ".$message."<br />";
echo "Debug Message = ".$uploadStatus."<br />";

//setup the form
$objForm->name = 'impfrm';
$objForm->action = $this->uri(array('action' => ''));

$objForm->addToForm($inpButton->show());

print $objForm->show().'<br/>';

?>