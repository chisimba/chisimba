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
		$objH->str = $objLanguage->languageText("mod_timeline_title_add", 'timeline');
		break;
	case 'edit':
		$objH->str = $objLanguage->languageText("mod_timeline_title_edit", 'timeline');
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
//Load the textarea object
$this->loadClass('textarea', 'htmlelements');
//Create a form & set its action for saving
$objForm = new form('editform');
$objForm->action = $this->uri(array(
  'action' => 'savestructure',
  'mode' => $mode), "timeline");
  
//Retrieve the data if the array of data is set
if (isset($ar)) {
    $id = $ar['id'];
    $title = $ar['title'];
    $description = $ar['description'];
    $url = $ar['url'];
    $focusdate = $ar['focusdate'];
    $intervalpixels = $ar['intervalpixels'];
    $intervalunit = $ar['intervalunit'];
    $showbottomband = $ar['showbottomband'];
    $btmbandwidth = $ar['btmbandwidth'];
    $btminterval = $ar['btminterval'];
    $btmintervalpixels = $ar['btmintervalpixels'];
    $tlheight = $ar['tlheight'];
    $created = $ar['created'];
    $modified = $ar['modified'];
} else {
    $id = "";
    $title = "";
    $description = "";
    $url="";
    $focusdate = "1972";
    $intervalpixels = "70";
    $intervalunit = "YEAR";
    $tlheight = "400";
    $showbottomband = "FALSE";
    $btmbandwidth = "";
    $btminterval = "";
    $btmintervalpixels = "";
    $created = "";
    $modified = "";
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


$objForm->addToForm("<table>");

//Set the value of the element to title
//Create a text input element for $title
$objElement = new textinput("title");
$objElement->setValue($title);
$objElement->size=70;
$objForm->addRule('title',$this->objLanguage->languageText("mod_timeline_valrule_titreq", "timeline"),'required');
//Add the $title element to the form
$objForm->addToForm("<tr><td width=\"300\" class=\"even\">" 
. $this->objLanguage->languageText("mod_timeline_fieldname_title", 
  "timeline") . "</td><td class=\"even\">" . $objElement->show() . "</td></tr>");


//Set the value of the element to description
//Create a text input element for $description
$objElement = new textarea("description");
$objElement->setValue($description);
$objElement->cols=60;
//Add the $description element to the form
$objForm->addToForm("<tr><td valign=\"top\"  width=\"300\" class=\"odd\">" 
. $this->objLanguage->languageText("mod_timeline_fieldname_description", 
  "timeline") . "</td><td class=\"odd\">" . $objElement->show() . "</td></tr>");


//Set the value of the element to title
//Create a text input element for $url
$objElement = new textinput("url");
$objElement->setValue($url);
$objElement->size=70;
$objForm->addRule('url',$this->objLanguage->languageText("mod_timeline_valrule_urlreq", "timeline"),'required');
//Add the $title element to the form
$objForm->addToForm("<tr><td width=\"300\" class=\"even\">" 
. $this->objLanguage->languageText("mod_timeline_fieldname_url", 
  "timeline") . "</td><td class=\"even\">" . $objElement->show() . "</td></tr>");

//Set the value of the element to focusdate
//Create a text input element for $focusdate
$objElement = new textinput("focusdate");
$objElement->setValue($focusdate);
$objForm->addRule('focusdate',$this->objLanguage->languageText("mod_timeline_valrule_focdatreq", "timeline"),'required');
$objElement->size=10;
//Add the $focusdate element to the form
$objForm->addToForm("<tr><td width=\"300\" class=\"odd\">" 
  . $this->objLanguage->languageText("mod_timeline_fieldname_focusdate", 
  "timeline") . "</td><td class=\"odd\">" . $objElement->show() . "</td></tr>");


//Set the value of the element to intervalpixels
//Create a text input element for $intervalpixels
$objElement = new textinput("intervalpixels");
$objElement->size=10;
$objElement->setValue($intervalpixels);
$objForm->addRule('intervalpixels',$this->objLanguage->languageText("mod_timeline_valrule_invpxnum", "timeline"), 'numeric');
//Add the $intervalpixels element to the form
$objForm->addToForm("<tr><td width=\"300\" class=\"even\">" . $this->objLanguage->languageText("mod_timeline_fieldname_intervalpixels", 
  "timeline") . "</td><td class=\"even\">" . $objElement->show() . "</td></tr>");


//Set the value of the element to intervalunit
//Create a text input element for $intervalunit
$objElement = new textinput("intervalunit");
$objElement->size=10;
$objElement->setValue($intervalunit);
$objForm->addRule('intervalunit',$this->objLanguage->languageText("mod_timeline_valrule_intvaluntreq", "timeline"),'required');
//Add the $intervalunit element to the form
$objForm->addToForm("<tr><td width=\"300\" class=\"odd\">" . $this->objLanguage->languageText("mod_timeline_fieldname_intervalunit", 
  "timeline") . "</td><td class=\"odd\">" . $objElement->show() . "</td></tr>");

//Create a text input element for $tlheight
$objElement = new textinput("tlheight");
$objElement->setValue($tlheight);
$objElement->size=10;
$objForm->addRule('tlheight',$this->objLanguage->languageText("mod_timeline_valrule_tlhtreq", "timeline"),'required');
$objForm->addRule('tlheight',$this->objLanguage->languageText("mod_timeline_valrule_tlhtnum", "timeline"), 'numeric');
//Add the $tlheight element to the form
$objForm->addToForm("<tr><td width=\"300\" class=\"even\">" . $this->objLanguage->languageText("mod_timeline_fieldname_tlheight", 
  "timeline") . "</td><td class=\"even\">" . $objElement->show() . "</td></tr>");


// Create the checkbox object for $showbottomband
$objRdBtn = $this->loadClass('radio','htmlelements');
//Radio button Group
$objElement = new radio('showbottomband');
$wYes = $this->objLanguage->languageText("word_yes") . '&nbsp;&nbsp;';
$wNo = $this->objLanguage->languageText("word_no"); 
$objElement->addOption('TRUE', $wYes);
$objElement->addOption('FALSE',$wNo);
if ($showbottomband == "TRUE") {
    $objElement->setSelected('TRUE');
} else {
    $objElement->setSelected('FALSE');
}
$objForm->addToForm("<tr><td width=\"300\" class=\"odd\">" . $this->objLanguage->languageText("mod_timeline_fieldname_showbottomband", 
  "timeline") . "</td><td class=\"odd\">" . $objElement->show() . "</td></tr>");

 
//Create a text input element for $btmbandwidth
$objElement = new textinput("btmbandwidth");
$objElement->setValue($btmbandwidth);
$objElement->size=10;
//Add the $tlheight element to the form
$objForm->addToForm("<tr><td width=\"300\" class=\"even\">" . $this->objLanguage->languageText("mod_timeline_fieldname_btmbandwidth", 
  "timeline") . "</td><td>" . $objElement->show() . "</td></tr>");

//Create a text input element for $btminterval
$objElement = new textinput("btminterval");
$objElement->setValue($btminterval);
$objElement->size=10;
//Add the $tlheight element to the form
$objForm->addToForm("<tr><td width=\"300\" class=\"odd\">" . $this->objLanguage->languageText("mod_timeline_fieldname_btminterval", 
  "timeline") . "</td><td class=\"odd\">" . $objElement->show() . "</td></tr>");
  
//Create a text input element for $btmintervalpixels
$objElement = new textinput("btmintervalpixels");
$objElement->setValue($btmintervalpixels);
$objElement->size=10;
//Add the $tlheight element to the form
$objForm->addToForm("<tr><td width=\"300\" class=\"even\">" . $this->objLanguage->languageText("mod_timeline_fieldname_btmintervalpixels", 
  "timeline") . "</td><td class=\"even\">" . $objElement->show() . "</td></tr>");

//Set the value of the element to created
if ($mode == "edit") {
	if (isset($created)) {
	    //Add the $created element to the form
	    $objForm->addToForm("<tr><td width=\"300\" class=\"odd\">" . $this->objLanguage->languageText("mod_timeline_fieldname_created", 
	      "timeline") . "</td><td class=\"odd\">" . $created . "</td></tr>");
	}
}

//Create a submit button
$objElement = new button('submit');
// Set the button type to submit
$objElement->setToSubmit();
// Use the language object to add the word save
$objElement->setValue(' '.$this->objLanguage->languageText("word_save").' ');
// Add the button to the form
$objForm->addToForm("<tr><td width=\"300\"></td><td>" . ''.$objElement->show() . "</td></tr>");

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