<?php

/**
 *
 * Class to handle system configuration properties display
 * logic to support the templates.
 *
 * @author Derek Keats
 *
 *
 */
class sysconfiginterface extends object
{

    /**
    * Property to hold the user object
    *
    * @var object $objUser The user object
    */
    var $objUser;

    /**
    * Property to hold the language object
    *
    * @var object $objLanguage The language object
    */
    var $objLanguage;


    /**
    * Standard init function to set the database table and instantiate
    * common classes.
    */
    function init()
    {

        //Get an instance of the sysconfig object
        $this->objDbSysconfig = & $this->getObject('dbsysconfig');
        //Get an instance of the user object
        $this->objUser = & $this->getObject('user', 'security');
        //Get an instance of the language object
        $this->objLanguage = & $this->getObject('language', 'language');
        //Get the text abstract object
        //Kevin Cyster
        //$this -> objAbstract =& $this -> getObject('systext_facet', 'systext');
    }

    /**
    * Method to render an add form to a template
    *
    * @param string $module The module to add the parameter
    */
    function showEditAddForm($pmodule)
    {
        //Create a form
        $formAction=$this->uri(array(
          'action'=>'save'));
        //Load the form class
        $this->loadClass('form','htmlelements');
        //Create and instance of the form class
        $objForm = new form('sysconfig');
        //Set the action for the form to the uri with paramArray
        $objForm->setAction($formAction);
        //Set the displayType to 3 for freeform
        $objForm->displayType=3;

        //Create a heading for the title
        //$objHd = $this->newObject('htmlheading', 'htmlelements');

        //Load the textinput class
        $this->loadClass('textinput','htmlelements');
        //Load the label class
        $this->loadClass('label', 'htmlelements');

        //Load the dropdown class
        //Kevin Cyster
        $this->loadClass('dropdown', 'htmlelements');

        //Create an element for the input of module
        $objElement = new textinput ("pmodule");
        //Set the value of the element to $module
        if (isset($pmodule)) {
            $objElement->setValue($pmodule);
        }
        //Create label for input of module
        $label = new label($this->objLanguage->languageText("mod_sysconfig_modtxt",'sysconfig'), "input_pmodule");


        $objForm->addToForm("<p><strong>"
          . $this->objLanguage->languageText("mod_sysconfig_modtxt",'sysconfig')
          . "</strong>: " . $pmodule."</p>");

        //Get the pk value
        $id = $this->getParam('id');
        //Get the records for editing
        $ar = $this->objDbSysconfig->getRow('id', $id);
        //Get the two values needed
        if (isset($ar)) {
            $pname = $ar['pname'];
            $pvalue = $ar['pvalue'];
        } else {
            $pname = $this->getParam('id',NULL);
            $pvalue =$this->getParam('value',NULL);
        } #if
        //Create an element for the input of id
        $objElement = new textinput ("id");
        $objElement->fldType="hidden";
        $objElement->setValue($id);
        $objForm->addToForm($objElement->show());

        //Create an element for the input of id
        $objElement = new textinput ("pmodule");
        $objElement->fldType="hidden";
        $objElement->setValue($pmodule);
        $objForm->addToForm($objElement->show());

        //Add the $name element to the form
        $objForm->addToForm('<p><b>'. $this->objLanguage->languageText("mod_sysconfig_paramname",'sysconfig'). '</b>: ' . $pname.'</p>');

        // Check in Config folder if module is gives as _site_
       
        if ($pmodule == '_site_') {
            $moduleToCheck = 'config';
        } else {
            $moduleToCheck = $pmodule;
        }

        // Load object that checks if class exists
        $checkobject = $this->getObject('checkobject', 'utilities');
        // Check if class 'sysconfig_{pname}' exists in module.
        if ($checkobject->objectFileExists('sysconfig_'.str_replace('/', '_', str_replace('-', '_', $pname)), $moduleToCheck)) {
            // If yes, instantiate the object
            $objParamValue = $this->getObject(strtolower('sysconfig_'.str_replace('/', '_', str_replace('-', '_', $pname))), $moduleToCheck);
            // send it the current default value
            $objParamValue->setDefaultValue($pvalue);
        } else {
            $valueLabel = new label($this->objLanguage->languageText("mod_sysconfig_paramvalue",'sysconfig'), "input_pvalue");
            //Add the $value element to the form
            $objForm->addToForm("<b>". $valueLabel->show()."</b>: ");
            //Create an element for the input of value
            $objParamValue = new textinput ("pvalue");
            $objParamValue->size="70";
            //Set the value of the element to $value
            if (isset($pvalue)) {
                $objParamValue->setValue($pvalue);
            } #if
        }
        $objForm->addToForm($objParamValue->show()."<br /><br />");
        // Create an instance of the button object and add a save button to the form
        $this->loadClass('button', 'htmlelements');
        // Create a submit button
        $objElement = new button('submit');
        // Set the button type to submit
        $objElement->setToSubmit();
        // Use the language object to add the word save
        $objElement->setValue(' '.$this->objLanguage->languageText("word_save").' ');
        // Add the button to the form
        $objForm->addToForm('<br/>'.$objElement->show());

        //Add the form
        return $objForm->show();
    } #function showAddForm
} #class
?>