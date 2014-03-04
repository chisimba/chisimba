<?php
/****** Set up header parameters for javascript date picker ********/
$headerParams=$this->getJavascriptFile('ts_picker.js','htmlelements');
$headerParams.="\n\n<script>\n/*Script by Denis Gritcyuk: tspicker@yahoo.com
Submitted to JavaScript Kit (http://javascriptkit.com)
Visit http://javascriptkit.com for this script*/\n</script>\n\n";
$this->appendArrayVar('headerParams',$headerParams);


$this->loadClass('form','htmlelements');
$this->loadClass('textinput','htmlelements');


//Create the form class
$objForm = new form ('schedule', $this->uri(array('action'=>'saveschedule','id'=>$id)));
$objForm->method = 'POST';
$objForm->displayType=3;  //Free form

//Add the form title to the form
$objForm->addToForm('<h3> Schedule For Live Presentation</h3>');
$objLink=$this->newObject('link', 'htmlelements');

$filename='';
if (trim($file['title']) == '') {
$filename = $file['filename'];
} else {
$filename = htmlentities($file['title']);
}
$objTrim = $this->getObject('trimstr', 'strings');

$linkname = $objTrim->strTrim($filename, 45);

$fileLink = new link ($this->uri(array('action'=>'view', 'id'=>$id)));
$fileLink->link = $this->objFiles->getPresentationThumbnail($id).'<br />'.$linkname;
$fileLink->title = $filename;

$cell=$fileLink->show();
$presentationDate = date("Y-m-d H:i", mktime(0,0,0,date("m"),date("d")+1,date("Y"),date("H")));
$objTextInput = new textinput('presentationDate', $presentationDate);
$cell.=$objTextInput->show();

$objIcon=$this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('modules/calendar');
$objIcon->alt=('Select Date for Live Presentation');
$objLink->link("javascript:show_calendar('document.schedule.presentationDate', document.schedule.presentationDate.value);");
$objLink->link=$objIcon->show();
$cell .= $objLink->show();

$scheduleLink = new link ($this->uri(array('action'=>'tagcloud')));
$scheduleLink->link = 'Schedule';

$button = new button ('saveschedule', 'Schedule');
$button->setToSubmit();

$cell.='<br>'.$button->show();
$objFeatureBox = $this->newObject('featurebox', 'navigation');
$objIcon = $this->newObject('geticon', 'htmlelements');
$objIcon->setIcon('loading_circles');

$content = '<div id="loading_uploads" style="display:none;">'.$objIcon->show().'</div><div id="data_uploads">'.$cell.'</div>';

   

$statsTable = $this->newObject('htmltable', 'htmlelements');
$statsTable->startRow();
$statsTable->addCell($objFeatureBox->show('Select Date for Live Presentation', $content), '20%');
$statsTable->addCell('&nbsp;', '62%');

$statsTable->endRow();


// Make a tabbed box
$objTabs = $this->newObject('tabcontent', 'htmlelements');
$objTabs->width = '95%';

$objTabs->addTab('Scheduling Live Presentations', $statsTable->show());

//Add the current table to the form
$objForm->addToForm($objTabs->show());


//Add the table to the centered layer
echo $objForm->show();

?>