<?php
// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');
// Set columns to 3
$cssLayout->setNumColumns(2);

$this->loadClass("iframe","htmlelements");
$iframe = new iframe();
$iframe->name = "lists";
$iframe->width = "100%";
$iframe->height = "1600";
$iframe->src = $uri;            
$content=$iframe->show();

//Set up the leftside column depending on Joomla status
$objJoomla = $this->getObject('joomlabridge', 'joomla');
$jStatus = $objJoomla->getJoomlaStatus();
//Set up the title
$leftSideColumn = "<h3>" 
  . $this->objLanguage->languageText("mod_joomla_title", "joomla") 
  . "</h3>";

switch ($jStatus) {
    case "NOTINSTALLED":
        //Joomla is not installed
        $leftSideColumn .= $this->objLanguage->languageText("mod_joomla_notinstalled", "joomla")
          . "<br /><br />" 
          . $this->objLanguage->languageText("mod_joomla_databasetoinstall", "joomla");
        //Set up the confirm install link
        $installUri = $this->uri(array("action" => "confirminstallation"), 'joomla');
        $leftSideColumn .= "<br /><br /><a href=\"" . $installUri . "\">" 
          . $this->objLanguage->languageText("mod_joomla_confirminstall", "joomla") 
          . "</a>";
        break;
        
    case "STAGE1_INSTALL":
        //Joomla is at stage 1 install
        $leftSideColumn .= $this->objLanguage->languageText("mod_joomla_copyusers", "joomla")
          . "<br /><br />";
        $usersUri = $this->uri(array("action" => "copyusers"), 'joomla');
        $leftSideColumn .= "<br /><a href=\"" . $usersUri . "\">" 
          . $this->objLanguage->languageText("mod_joomla_copyusrsgo", "joomla") . "</a>"
          . "<br /><br />" 
          . $this->objLanguage->languageText("mod_joomla_takesometime", "joomla");
        break;
        
    case "COMPLETED":
        $leftSideColumn .= $this->objLanguage->languageText("mod_joomla_instructions", "joomla")
          . "<br /><br /><a href=\"" . $uri . "\">" 
          . $this->objLanguage->languageText("mod_joomla_fullscreen", "joomla") 
          . "</a>";
        $objJoomla->loginJoomla();
        break;
    
    default:
        $leftSideColumn .= "WORKING HERE";
        break;    
}



// Add Left column
$cssLayout->setLeftColumnContent($leftSideColumn);
// Add the main Column
$cssLayout->setMiddleColumnContent($content);

//Output the content to the page
echo $cssLayout->show();
?>