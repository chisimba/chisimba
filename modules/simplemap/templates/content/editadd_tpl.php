<?php

//Create an instance of the css layout class
$cssLayout = $this->newObject('csslayout', 'htmlelements');
//Set columns to 2
$cssLayout->setNumColumns(2);


//Initialize NULL content for the left side column
$leftSideColumn = "";
//Get the menu creator
$objMenu = $this->getObject("leftmenu", "simplemap");
//Add the left menu
$leftSideColumn = $objMenu->show();

//Add the templage heading to the main layer
$objH = $this->getObject('htmlheading', 'htmlelements');
//Heading H3 tag
$objH->type=3; 
$mode = $this->getParam("mode", NULL);
switch ($mode) {
	case 'add':
		$objH->str = $objLanguage->languageText("mod_simplemap_title_add", 'simplemap');
		break;
	case 'edit':
		$objH->str = $objLanguage->languageText("mod_simplemap_title_edit", 'simplemap');
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
  'action' => 'save',
  'mode' => $mode), "simplemap");
  
//Retrieve the data if the array of data is set
if (isset($ar)) {
    $id = $ar['id'];
    $title = $ar['title'];
    $description = $ar['description'];
    $url = $ar['url'];
    $glat = $ar['glat'];
    $glong = $ar['glong'];
    $magnify = $ar['magnify'];
    $width = $ar['width'];
    $height = $ar['height'];
    $maptype = $ar['maptype'];
    $created = $ar['created'];
    $modified = $ar['modified'];
} else {
    $id = "";
    $title = "";
    $description = "";
    $url="";
    $glat = "";
    $glong = "";
    $magnify = "6";
	$width = "800";
    $height = "600";
    $maptype = "";
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
$objForm->addRule('title',$this->objLanguage->languageText("mod_simplemap_valrule_titreq", "simplemap"),'required');
//Add the $title element to the form
$objForm->addToForm("<tr><td>" 
. $this->objLanguage->languageText("mod_simplemap_fieldname_title", 
  "simplemap") . "</td><td>" . $objElement->show() . "</td></tr>");


//Set the value of the element to description
//Create a text input element for $description
$objElement = new textarea("description");
$objElement->setValue($description);
$objElement->cols=60;
//Add the $description element to the form
$objForm->addToForm("<tr><td valign=\"top\">" 
. $this->objLanguage->languageText("mod_simplemap_fieldname_description", 
  "simplemap") . "</td><td>" . $objElement->show() . "</td></tr>");


//Set the value of the element to title
//Create a text input element for $url
$objElement = new textinput("url");
$objElement->setValue($url);
$objElement->size=70;
$objForm->addRule('url',$this->objLanguage->languageText("mod_simplemap_valrule_urlreq", "simplemap"),'required');
//Add the $title element to the form
$objForm->addToForm("<tr><td>" 
. $this->objLanguage->languageText("mod_simplemap_fieldname_url", 
  "simplemap") . "</td><td>" . $objElement->show() . "</td></tr>");

//Set the value of the element to glat
//Create a text input element for $glat
$objElement = new textinput("glat");
$objElement->setValue($glat);
$objForm->addRule('glat',$this->objLanguage->languageText("mod_simplemap_valrule_glatreq", "simplemap"),'required');
$objElement->size=10;
//Add the $glong element to the form
$objForm->addToForm("<tr><td>" 
  . $this->objLanguage->languageText("mod_simplemap_fieldname_glat", 
  "simplemap") . "</td><td>" . $objElement->show() . "</td></tr>");


//Set the value of the element to glong
//Create a text input element for $glong
$objElement = new textinput("glong");
$objElement->size=10;
$objElement->setValue($glong);
$objForm->addRule('glong',$this->objLanguage->languageText("mod_simplemap_valrule_glongreq", "simplemap"),'required');
//Add the $glong element to the form
$objForm->addToForm("<tr><td>" . $this->objLanguage->languageText("mod_simplemap_fieldname_glong", 
  "simplemap") . "</td><td>" . $objElement->show() . "</td></tr>");


//Set the value of the element to magnify
//Create a text input element for $magnify
$objElement = new textinput("magnify");
$objElement->size=10;
$objElement->setValue($magnify);
//Add the $magnify element to the form
$objForm->addToForm("<tr><td>" . $this->objLanguage->languageText("mod_simplemap_fieldname_magnify", 
  "simplemap") . "</td><td>" . $objElement->show() . "</td></tr>");
  
//Set the value of the element to width
//Create a text input element for $width
$objElement = new textinput("width");
$objElement->setValue($width);
$objForm->addRule('width',$this->objLanguage->languageText("mod_simplemap_valrule_widthreq", "simplemap"),'required');
$objElement->size=10;
//Add the $glong element to the form
$objForm->addToForm("<tr><td>" 
  . $this->objLanguage->languageText("mod_simplemap_fieldname_width", 
  "simplemap") . "</td><td>" . $objElement->show() . "</td></tr>");
  
//Set the value of the element to height
//Create a text input element for $height
$objElement = new textinput("height");
$objElement->setValue($height);
$objForm->addRule('height',$this->objLanguage->languageText("mod_simplemap_valrule_heightreq", "simplemap"),'required');
$objElement->size=10;
//Add the $glong element to the form
$objForm->addToForm("<tr><td>" 
  . $this->objLanguage->languageText("mod_simplemap_fieldname_height", 
  "simplemap") . "</td><td>" . $objElement->show() . "</td></tr>");
  

//Set the value of the element to maptype
//Create a text input element for $maptype
$objElement = new dropdown("maptype");
$objElement->addOption('G_NORMAL_MAP', $this->objLanguage->languageText("mod_simplemap_normalmap", "simplemap"));
$objElement->addOption('G_SATELLITE_MAP', $this->objLanguage->languageText("mod_simplemap_satmap", "simplemap"));
$objElement->addOption('G_HYBRID_MAP', $this->objLanguage->languageText("mod_simplemap_hybridmap", "simplemap"));
//$objElement = new textinput("maptype");
//$objElement->setValue($maptype);
//$objElement->size=10;
//Add the $maptype element to the form
$objForm->addToForm("<tr><td>" . $this->objLanguage->languageText("mod_simplemap_fieldname_maptype", 
  "simplemap") . "</td><td>" . $objElement->show() . "</td></tr>");

//Set the value of the element to created
if ($mode == "edit") {
	if (isset($created)) {
	    //Add the $created element to the form
	    $objForm->addToForm("<tr><td>" . $this->objLanguage->languageText("mod_timeline_fieldname_created", 
	      "timeline") . "</td><td>" . $created . "</td></tr>");
	}
}

//Create a submit button
$objElement = new button('submit');
// Set the button type to submit
$objElement->setToSubmit();
// Use the language object to add the word save
$objElement->setValue(' '.$this->objLanguage->languageText("word_save").' ');
// Add the button to the form
$objForm->addToForm("<tr><td></td><td>" . ''.$objElement->show() . "</td></tr>");

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