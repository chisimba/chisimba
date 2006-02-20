<?php 

// Make a container layer
$this->helpMain = $this->newObject('layer','htmlelements');
$this->helpMain->cssClass = "debug-layers";
$this->helpMain->height = "360px";


// Create an instance of the layer object
$this->helpDiv = $this->newObject('layer','htmlelements');
// Make the layer a tabbed box
$this->helpDiv->cssClass = "box";

//Create an instance of the htmlHeading object
$this->objHeading = &$this->newObject('htmlheading', 'htmlelements');
//Set the heading to 5 which displays the tab
$this->objHeading->type="5";
//Add the label to the tab
$this->objHeading->str=$this->objLanguage->languageText("word_help");

// Create an instance of the layer object
$this->helpCont = $this->newObject('layer','htmlelements');
// Make the layer a tabbed box content area
$this->helpCont->cssClass = "box-content";
//Add help text to the content area
$this->helpCont->str = $this->help_text;

//Add the tab
$this->helpDiv->str = $this->objHeading->show();
//Add the content
$this->helpDiv->str .= $this->helpCont->show(); 

//Add the output to the main container layer
$this->helpMain->str =  "<br />".$this->helpDiv->show();

//Add the output to the container
echo $this->helpMain->show();

echo $richhelp;
?>