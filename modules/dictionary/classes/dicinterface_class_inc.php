<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
*
* This is a class to produce interfaces for dictionary
* lookup of words.
*
* @author Derek Keats
* @author  Jameel Sauls
* @version $Id: dicinterface_class_inc.php 5739 2007-02-08 12:32:53Z nitsckie $
* @copyright 2005 GNU GPL
*
**/
class dicinterface extends object
{

    /**
    *
    * @var array $languages The languages that babelfish supports
    *
    */
    public $languages;

    /**
    *
    * @var string $format The layour format, horizontal or vertical
    *
    */
    public $format;

    /**
    *
    * @var string $err The error code
    *
    */
    public $err;

    /**
    *
    * @var string $err The error message
    *
    */
    public $errMsg;

    /**
    *
    * Standard constructor which instantiates the language object
    *
    */
    public function init()
    {
        //Instantiate the language object
        $this->objLanguage = & $this->getObject("language", "language");
        //Set the default format to vertical
        $this->format = "vertical";
    }

    /**
    *
    * standard set function to set a parameter
    *
    */
    public function set($param, $value)
    {
        $this->$param = $value;
    }

    /**
    *
    * Standard get function to get a parameter
    *
    */
    public function get($param)
    {
        return $this->$param;
    }
    /**
    *
    * Method to make as search form for the dictionary module
    *
    */
    public function makeSearch()
    {
        $button = $this->objLanguage->LanguageText('mod_dictionary_lookupword','dictionary');
        //Check if the format is vertical or horizontal
        if ($this->format=="vertical") {
            $br="<br />";
        } else {
            $br = "&nbsp;";
        }

        //Load the form class
        $this->loadClass('form','htmlelements');
        //Set the form action for the lookup
        $paramArray=array(
          'action'=>'lookup');
        $formAction=$this->uri($paramArray);
        //Create and instance of the form class
        $objForm = new form('dictionary');
        //Set the action for the form to the uri with paramArray
        $objForm->setAction($formAction);
        //Set the displayType to 3 for freeform
        $objForm->displayType=3;

        //Add a text field for the word
        $this->loadClass('textinput','htmlelements');
        //Instantiate the textinput for word
        $objElement= new textinput("word");

        // Create an instance of the button object
        $this->loadClass('button', 'htmlelements');
        // Create a submit button
        $objElement2= new button('submit', $button);
        // Set the button type to submit
        $objElement2->setToSubmit();
        // Use the language object to add the word translate
     //   $objElement2->setValue(' '. $this->objLanguage->languageText("mod_dictionary_lookupword").' ');
        $objConfig = $this->getObject('altconfig', 'config');
        //get the dict.org image
        $image = $br . "<a href=\"http://www.dict.org/\" target=\"_blank\">"
          . "<img src=\"".$objConfig->getModuleURI()."/dictionary/resources/img/dictorg.gif\" "
          . "alt=\"dict.org\" align=\"middle\"  title=\"dict.org\" "
          . "border= '0' /></a>" . $br;

        //Return the formatted form
        $objForm->addToForm($image . $br
          . $objElement->show() . $br
          . $objElement2->show());
        return $objForm->show();
    }
}

?>