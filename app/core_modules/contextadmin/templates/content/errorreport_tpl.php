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
		$message = 'Permissions denied or directory doesn\'t exist';
	break;
	case 'fileReadError':
		$message = 'Can\'t read zipfile';
	break;
	case 'imsReadError':
		$message = 'Can\'t read IMS manifest file';
	break;
	case 'simpleXmlError':
		$message = 'Can\'t load IMS manifest file';
	break;
	case 'domReadError':
		$message = 'Can\'t load IMS manifest file';
	break;
	case 'xpathSetError':
		$message = 'Can\'t load IMS manifest file';
	break;
	case 'courseReadError':
		$message = 'Can\'t access Course information from database';
	break;
	case 'initializeError':
		$message = 'Cant\'t initialize directory locations';
	break;
	case 'courseWriteError':
		$message = 'Course duplication exists or database can\'t be accessed';
	break;
	case 'writeResourcesError':
		$message = 'Permissions denied or directory doesn\'t';
	break;
	case 'noStructureError':
		$message = 'Can\'t read IMS manifest file';
	break;
	case 'loadDataError':
		$message = 'Can\'t access database or duplication in database';
	break;
	case 'uploadError':
		$message = 'Can\'t access database or duplication in database';
	break;
	case 'rebuildHtmlError':
		$message = 'Cant\'t modify pages or permissions denied';
	break;
	case 'success':
		$message = 'Successful Upload';
	break;
	case 'unknownPackage':
		$message = 'Unknown Package type';
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