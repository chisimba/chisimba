<?php
//Create an instance of the css layout class
$cssLayout = $this->newObject('csslayout', 'htmlelements');
//Set columns to 2
$cssLayout->setNumColumns(2);


//Initialize NULL content for the left side column
$leftSideColumn = "";
//Get the menu creator
$objMenu = $this->getObject("leftmenu", "timeline");
//Add the left menu
$leftSideColumn = $objMenu->show();

//Add the templage heading to the main layer
$objH = $this->getObject('htmlheading', 'htmlelements');
//Heading H3 tag
$objH->type=3; 
$mode = $this->getParam("mode", NULL);
switch ($mode) {
	case 'add':
		$objH->str = $objLanguage->languageText("mod_timeline_title_addtl", 'timeline');
		break;
	case 'edit':
		$objH->str = $objLanguage->languageText("mod_timeline_title_edittl", 'timeline');
		break;
	default:
		break;			
}
//Add the heading to the output string for the main display area
$rightSideColumn = $objH->show();

//Load the form object
$this->loadClass('form', 'htmlelements');
//Load the button object
$this->loadClass('button', 'htmlelements');
//Load the textinput object
$this->loadClass('textinput', 'htmlelements');
//Load the textinput object
$this->loadClass('textarea', 'htmlelements');

//Create a form
$objForm = new form('editform');
$objForm->action = $this->uri(array(
  'action' => 'savetimeline',
  'mode' => $mode), "timeline");
$objForm->addToForm("<table>");

//Retrieve the data if the array of data is set
if (isset($ar)) {
    $id = $ar['id'];
    $timelineid = $ar['timelineid'];
    $start = $ar['start'];
    $image = $ar['image'];
    $url = $ar['url'];
    $end = $ar['end'];
    $title = $ar['title'];
    $timelinetext = $ar['timelinetext'];
    $created = $ar['created'];
    $modified = $ar['modified'];
} else {
    $id = "";
    $timelineid = "";
    $start = "";
    $image = "";
    $url ="";
    $end = "";
    $title = "";
    $timelinetext = "";
    $created =  "";
    $modified =  "";
} 

//--------------Generated edit form--------------//

//Set the value to the primary key: id
//Create an element for the hidden text input for the id PK field
$objElement = new textinput("id");
$objElement->setValue($id);
//Set the field type to hidden for the primary key
$objElement->fldType="hidden";
//Add the hidden PK field to the form
$objForm->addToForm($objElement->show());


//Set the value of the element to timelineid

//Create a text input element for $timelineid
$objElement = new textinput("timelineid");
$objElement->setValue($timelineid);
$objElement->size=70;
//Add the $timelineid element to the form
$objForm->addToForm("<tr><td width=\"200\">" . $this->objLanguage->languageText("mod_timeline_fieldname_timelineid", 
  "timeline") . "</td><td>" . $objElement->show() . "</td></tr>");


//Set the value of the element to start
//Create a text input element for $start
$objElement = new textinput("start");
$objElement->setValue($start);
$objElement->size=70;
//Add the $start element to the form
$objForm->addToForm("<tr><td width=\"200\">" . $this->objLanguage->languageText("mod_timeline_fieldname_start", 
  "timeline") . "</td><td>" . $objElement->show() . "</td></tr>");



//Set the value of the element to image
//Create a text area element for image
$objElement = new textinput("image");
$objElement->setValue($image);
$objElement->size=70;
//Add the $image element to the form
$objForm->addToForm("<tr><td width=\"200\">" . $this->objLanguage->languageText("mod_timeline_fieldname_image", 
  "timeline") . "</td><td>" . $objElement->show() . "</td></tr>");

//Set the value of the element to url
//Create a text area element for url
$objElement = new textinput("url");
$objElement->size=70;
$objElement->setValue($url);
//Add the $url element to the form
$objForm->addToForm("<tr><td width=\"200\">" . $this->objLanguage->languageText("mod_timeline_fieldname_url", 
  "timeline") . "</td><td>" . $objElement->show() . "</td></tr>");

//Set the value of the element to end
//Create a text input element for $end
$objElement = new textinput("end");
$objElement->size=70;
$objElement->setValue($end);
//Add the $end element to the form
$objForm->addToForm("<tr><td width=\"200\">" . $this->objLanguage->languageText("mod_timeline_fieldname_end", 
  "timeline") . "</td><td>" . $objElement->show() . "</td></tr>");


//Set the value of the element to title
//Create a text input element for $title
$objElement = new textinput("title");
$objElement->setValue($title);
$objElement->size=70;
//Add the $title element to the form
$objForm->addToForm("<tr><td width=\"200\">" . $this->objLanguage->languageText("mod_timeline_fieldname_title", 
  "timeline") . "</td><td>" . $objElement->show() . "</td></tr>");


//Set the value of the element to timelinetext
//Create a text input element for $timelinetext
$objElement = new textarea("timelinetext");
$objElement->setContent($timelinetext);
$objElement->cols=60;
//Add the $timelinetext element to the form
$objForm->addToForm("<tr><td width=\"200\">" . $this->objLanguage->languageText("mod_timeline_fieldname_timelinetext", 
  "timeline") . "</td><td>" . $objElement->show() . "</td></tr>");

//Create a submit button
$objElement = new button('submit');
// Set the button type to submit
$objElement->setToSubmit();
// Use the language object to add the word save
$objElement->setValue(' '.$this->objLanguage->languageText("word_save").' ');
// Add the button to the form
$objForm->addToForm("<tr><td width=\"200\">&nbsp;</td><td>" . ''.$objElement->show() . "</td></tr>");

$objForm->addToForm("</table>"); 

//----------End of generated edit form-----------//

//Add the form to the main display area
$rightSideColumn .= $objForm->show();
//Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);
// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);
//Output the content to the page
echo $cssLayout->show();

?>
