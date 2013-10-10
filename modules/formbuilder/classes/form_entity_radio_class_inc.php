<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!  \class form_entity_radio
 *
 *  \brief This class models all the content and functionality for the
 * radio form element.
 * \brief It provides functionality to insert new radio buttons, create them for the
 * WYSIWYG form editor and render them in the actual construction of the form.
 * It also allows you to delete radio buttons from forms.
 *  \brief This is a child class that belongs to the form entity heirarchy.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
include_once 'form_entity_handler_class_inc.php';

class form_entity_radio extends form_entity_handler {
    
    private $formNumber;
    /*!
     * \brief Private data member that stores a radio button object for the WYSIWYG
     * form editor.
     */
    private $objRadio;

    /*!
     * \brief This data member stores the form element identifier or ID that can
     * be used anywhere in this class.
     */
    private $radioName;

    /*!
     * \brief This data member stores three possibilities. The radio button can be
     * put in a new line or a double space can be placed before it or
     * no spaces at all.
     */
    private $breakSpaceType;

    /*!
     * \brief This data member stores all the options with their values and labels
     * for the radio form element.
     */
    private $labelnOptionArray;

    /*!
     * \brief This data member stores whether or not an option is checked by default.
     */
    private $tempWYSIWYGBoolDefaultSelected;

    /*!
     * \brief This data member stores three possibilities. The radio button can be
     * put in a new line or a double space can be placed before it or
     * no spaces at all.
     */
    private $tempWYSIWYGLayoutOption;

    /*!
     * \brief Private data member from the class \ref dbformbuilder_radio_entity that stores all
     * the properties of this class in an usable object.
     * \note This object is used to add, get or delete radio form elements.
     */
    protected $objDBRadioEntity;

    /*!
     * \brief Standard constructor that loads classes for other modules and initializes
     * and instatiates private data members.
     * \note The radio class is from the htmlelements module
     * inside the chisimba core modules.
     */
    public function init() {
        $this->loadClass('radio', 'htmlelements');
        $this->breakSpaceType = NULL;
        $this->radioName = NULL;
        $this->formNumber = NULL;
        $this->labelnOptionArray = array();
        $this->objDBRadioEntity = $this->getObject('dbformbuilder_radio_entity', 'formbuilder');
        $this->tempWYSIWYGBoolDefaultSelected = FALSE;
    }

    /*!
     * \brief This member function initializes some of the private data members for the
     * radio object.
     * \parm elementName A string for the form element identifier.
     */
    public function createFormElement($formNumber,$elementName="") {
        $this->formNumber = $formNumber;
        $this->radioName = $elementName;
        $this->objRadio = new radio($elementName);
    }

    /*!
     * \brief This member function gets the radio form element name if the private
     * data member radioName is set already.
     * \note This member function is not used in this module.
     * \return A string.
     */
    public function getWYSIWYGRadioName() {
        return $this->radioName;
    }

    /*!
     * \brief This member function contructs the html content for the form that
     * allows you to insert the radio button parameters to insert
     * a radio button option form element.
     * \note This member function uses member functions
     * from the parent class \ref form_entity_handler to
     * construct this form.
     * \param formName A string.
     * \return A constructed radio button insert form.
     */
    public function getWYSIWYGRadioInsertForm($formName) {
        $WYSIWYGRadioInsertForm = "<b>Radio HTML ID and Name Menu</b>";
        $WYSIWYGRadioInsertForm.="<div id='radioIDContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGRadioInsertForm.=$this->buildInsertIdForm('radio', $formName, "70") . "";
        $WYSIWYGRadioInsertForm.="</div>";
        $WYSIWYGRadioInsertForm.= "<b>Radio Label Menu</b>";
        $WYSIWYGRadioInsertForm.="<div id='radioLabelContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGRadioInsertForm.= $this->insertFormLabelOptions("radio", "labelOrientation",NULL,NULL);
        $WYSIWYGRadioInsertForm.= "</div>";
        $WYSIWYGRadioInsertForm.="<b>Radio Option Layout Menu</b>";
        $WYSIWYGRadioInsertForm.="<div id='radioLayoutContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGRadioInsertForm.=$this->buildLayoutForm('radio option', $formName, "radio",NULL) . "";
        $WYSIWYGRadioInsertForm.="</div>";
        $WYSIWYGRadioInsertForm.="<b>Insert Radio Options Menu</b>";
        $WYSIWYGRadioInsertForm.="<div id='radioOptionAndValueContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGRadioInsertForm.=$this->insertOptionAndValueForm('radio', 0) . "";
        $WYSIWYGRadioInsertForm.="</div>";

        return $WYSIWYGRadioInsertForm;
    }
    
    public function getWYSIWYGRadioEditForm($formNumber, $formElementName) {
        $radioParameters = $this->objDBRadioEntity->listRadioParameters($formNumber, $formElementName);
        if (empty($radioParameters)) {
            return 0;
        } else {
            $formElementLabel = "";
            $labelOrientation = "";
            foreach ($radioParameters as $thisradioParameter) {

                //$radioName = $thisradioParameter["radioname"];
                //$radioOptionLabel = $thisradioParameter["radiooptionlabel"];
                //$radioOptionValue = $thisradioParameter["radiooptionvalue"];
                //$defaultValue = $thisradioParameter["defaultvalue"];
                //$breakspace = $thisradioParameter["breakspace"];
                $formElementLabel = $thisradioParameter["label"];
                $labelOrientation = $thisradioParameter["labelorientation"];
            }
            $WYSIWYGRadioEditForm = "<div id='editFormElementTabs'>	
         <ul>
		<li><a href='#editFormElementPropertiesContainer'>Edit Radio Button Properties</a></li>
		<li><a href='#editFormElementOptionsContainer'>Edit Radio Button Options</a></li>
	</ul>";
            $WYSIWYGRadioEditForm.= "<div id='editFormElementPropertiesContainer'>";
            $WYSIWYGRadioEditForm.= "<b>Edit Radio Label Properties</b>";
            $WYSIWYGRadioEditForm.="<div id='radioLabelContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
            $WYSIWYGRadioEditForm.= $this->insertFormLabelOptions("radio", "labelOrientation", $formElementLabel, $labelOrientation);
            $WYSIWYGRadioEditForm.= "</div>";
            $WYSIWYGRadioEditForm.= "</div>";

            $WYSIWYGRadioEditForm.= "<div id='editFormElementOptionsContainer'>";
            $WYSIWYGRadioEditForm.= "<b>Edit Radio Button Options</b>";
            $WYSIWYGRadioEditForm.="<div id='radioOptionsContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
            $optionNumber = 1;

            $WYSIWYGRadioEditForm.="<style>
            .singleOptionContainer{
            color: #222222;
            font-size: 72.5%;
            font-family: 'lucida grande',tahoma,verdana,arial,sans-serif;
            background: none repeat scroll 0 0 #EEEEEE;
            border-top: 1px solid #CCCCCC;
            padding: 10px 20px;
            width: 700px;
            display:block;
            overflow: hidden;
            }
            
.singleOptionContainer:hover {

    -moz-box-shadow: 0 0 5px rgba(0,0,0,0.5);
	-webkit-box-shadow: 0 0 5px rgba(0,0,0,0.5);
	box-shadow: 0 0 5px rgba(0,0,0,0.5);
       background: none repeat scroll 0 0 #CFCFCF;

	}
        
.optionValueContainer{
width: 150px;
float:left;
}

.optionLabelContainer{
width: 150px;
float:left;
}

.optionBreakSpaceContainer{
width: 120px;
float:left;
}

.defaultOptionContainer{
width: 100px;
float:left;
}

a:link, a:visited {
    text-decoration: underline;
}

.deleteOptionLink, .editOptionLink {
    display: block;
    float: right;
    width: 60px;
}

        </style>";
//        #CFCFCF, #F6F6F6
            $WYSIWYGRadioEditForm.= "<div class='formOptionsListContainer'>";
            $WYSIWYGRadioEditForm.= "<div class='singleOptionContainer' id='optionTitle'>";
            $WYSIWYGRadioEditForm.= "<div class='optionValueContainer'><b>Option Value</b></div>";
            $WYSIWYGRadioEditForm.= "<div class='optionLabelContainer'><b>Option Label</b></div>";
            $WYSIWYGRadioEditForm.= "<div class='optionBreakSpaceContainer'><b>Break Space</b></div>";
            $WYSIWYGRadioEditForm.= "<div class='defaultOptionContainer'><b>Default Selected</b></div>";
            $WYSIWYGRadioEditForm.= "</div>";
            foreach ($radioParameters as $thisradioParameter) {

                //$radioName = $thisradioParameter["radioname"];
                $radioOptionLabel = $thisradioParameter["radiooptionlabel"];
                $radioOptionValue = $thisradioParameter["radiooptionvalue"];
                $defaultValue = $thisradioParameter["defaultvalue"];
                $breakspace = $thisradioParameter["breakspace"];
                $formElementLabel = $thisradioParameter["label"];
                $labelOrientation = $thisradioParameter["labelorientation"];
                $id = $thisradioParameter["id"];
                $WYSIWYGRadioEditForm.= "<div class='singleOptionContainer' id='option" . $optionNumber . "' optionID='" . $id . "' formNumber='" . $formNumber . "' formElementName='" . $formElementName . "' optionLabel='" . $radioOptionLabel . "' optionValue='" . $radioOptionValue . "' defaultValue='" . $defaultValue . "' breakspace='" . $breakspace . "' formElementLabel='" . $formElementLabel . "' labelOrientation='" . $labelOrientation . "'>";
                $WYSIWYGRadioEditForm.= "<div class='optionValueContainer'>" . $radioOptionValue . "</div>";
                $WYSIWYGRadioEditForm.= "<div class='optionLabelContainer'>" . $radioOptionLabel . "</div>";
                $WYSIWYGRadioEditForm.= "<div class='optionBreakSpaceContainer'>" . $breakspace . "</div>";
                if ($defaultValue == TRUE) {
                    $WYSIWYGRadioEditForm.= "<div class='defaultOptionContainer'>yes</div>";
                } else {
                    $WYSIWYGRadioEditForm.= "<div class='defaultOptionContainer'>no</div>";
                }

                $WYSIWYGRadioEditForm.= "<a class='deleteOptionLink' href='#delete'>Delete</a>";
                $WYSIWYGRadioEditForm.= "<a class='editOptionLink' href='#edit'>Edit</a>";
                $WYSIWYGRadioEditForm.= "</div>";
                $optionNumber++;
            }
            $WYSIWYGRadioEditForm.= "</div>";
            $WYSIWYGRadioEditForm.= "</div>";

            $WYSIWYGRadioEditForm.= "</div>";
            $WYSIWYGRadioEditForm.= "</div>";
            $WYSIWYGRadioEditForm.= "<style>
            
.editFormElementOptionsHeadingSpacer{
height:150px;
border-bottom: 2px solid #666666;
overflow: hidden;
}

.formElementOptionUpdateHeading{
float:right;
margin-top:110px;
margin-right:50px;
position:relative;
color: #222222;
font-family: 'Droid Serif',Cambria,Georgia,Palatino,'Palatino Linotype','Myriad Pro',Serif;
font-size: 3.0em;
}

.editFormElementOptionsSideSeperator{
float:right;
clear:none;
width:2px;
height:650px;
margin-right:100px;
background:#666666;
}

.editFormElementFormContainer{
width:780px;
margin-left:10px;
}

.formElementOptionsFormButtonsContainer{
    border-top-width: 3px;
    border-bottom-width: 3px;
    border-top-style: double;
    border-bottom-style: double;
    border-top-color: #CCCCCC;
    border-bottom-color: #CCCCCC;
    padding:5px;
    margin-bottom:5px;
    margin-top:5px;
    height:30px;
}

.formElementOptionsFormSuperContainer{
min-height:650px;
}
</style>";
            $WYSIWYGRadioEditForm.="<div class='formElementOptionsFormSuperContainer'>";
            $WYSIWYGRadioEditForm.= "<div class='editFormElementOptionsSideSeperator'></div>";
            $WYSIWYGRadioEditForm.="<div class='editFormElementOptionsHeadingSpacer'><div class='formElementOptionUpdateHeading'>Update Radio Button Option</div></div>";
            $WYSIWYGRadioEditForm.= "<div class='editFormElementFormContainer'>";
            $WYSIWYGRadioEditForm.="<b>Radio Option Layout Menu</b>";
            $WYSIWYGRadioEditForm.="<div id='radioLayoutContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
            $WYSIWYGRadioEditForm.=$this->buildLayoutForm('radio option', "", "radio", NULL) . "";
            $WYSIWYGRadioEditForm.="</div>";
            $WYSIWYGRadioEditForm.="<b>Radio Options Menu</b>";
            $WYSIWYGRadioEditForm.="<div id='radioOptionAndValueContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
            $WYSIWYGRadioEditForm.=$this->insertOptionAndValueForm('radio', 0) . "";
            $WYSIWYGRadioEditForm.="</div>";
            $WYSIWYGRadioEditForm.= "<div class='formElementOptionsFormButtonsContainer'></div>";
            $WYSIWYGRadioEditForm.= "</div>";
            $WYSIWYGRadioEditForm.= "</div>";
            return $WYSIWYGRadioEditForm;
        }
    }

    /*!
     * \brief This member function gets the radio button name if it has already
     * been saved in the database.
     * \param radioFormName A string containing the form element indentifier.
     * \return A string.
     */
    protected function getRadioName($formNumber,$radioFormName) {
        $radioParameters = $this->objDBRadioEntity->listRadioParameters($formNumber,$radioFormName);
        return $radioName = $radioParameters["0"]['radioname'];
    }

    /*!
     * \brief This member function sets the break space type private data
     * member.
     * \param breakSpaceType A string. The radio button can be
     * put in a new line or a double space can be placed before it or
     * no spaces at all.
     */
    public function setBreakSpaceType($breakSpaceType) {
        $this->breakSpaceType = $breakSpaceType;
    }

    /*!
     * \brief This member function allows you to insert a radio button option in a form with
     * a form element identifier.
     * \brief Before a new radio button option gets inserted into the database,
     * duplicate entries are checked if there is another radio button option.
     * in this same form with the same form element identifier.
     * \param formElementName A string for the form element idenifier.
     * \param option A string for the label for the option.
     * \param value A string for the actual value of the option.
     * \param defaultSelected A boolean to determine whether or not is option
     * is checked by default.
     * \param breakSpace A string to store the break space or layout for the
     * checkbox. Three possibilities exist ie new line, no space or tab before the
     * radio button option.
     * \param formElementLabel A string for the actual label text for the entire drop down
     * list form element.
     * \param labelOrientation A string that stores whether the form element label gets
     * put on top, bottom, left or right of the radio button form element.
     * \return A boolean value on successful storage of the radio button form element.
     */
    public function insertOptionandValue($formNumber,$formElementName, $option, $value, $defaultSelected, $layoutOption, $formElementLabel, $labelOrientation) {

        if ($this->objDBRadioEntity->checkDuplicateRadioEntry($formNumber,$formElementName, $value) == TRUE) {
            $this->objDBRadioEntity->insertSingle($formNumber,$this->radioName, $option, $value, $defaultSelected, $layoutOption, $formElementLabel, $labelOrientation);
            $this->formNumber = $formNumber;
            $this->labelnOptionArray[$value] = $option;
            $this->tempWYSIWYGBoolDefaultSelected = $defaultSelected;
            $this->tempWYSIWYGLayoutOption = $layoutOption;

            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    
    public function updateOptionandValue($optionID,$formNumber,$formElementName, $option, $value, $defaultSelected, $layoutOption, $formElementLabel, $labelOrientation) {
              if ($this->objDBRadioEntity->checkIfOptionExists($optionID) == TRUE) {
            $this->objDBRadioEntity->updateSingle($optionID,$formNumber,$formElementName, $option, $value, $defaultSelected, $layoutOption, $formElementLabel, $labelOrientation);
            $this->formNumber = $formNumber;
            $this->labelnOptionArray[$value] = $option;
            $this->tempWYSIWYGBoolDefaultSelected = $defaultSelected;
            $this->tempWYSIWYGLayoutOption = $layoutOption;

            return TRUE;
        } else {
            return FALSE;
        }  
    }
    

    public function updateMetaData($formNumber,$formElementName,$formElementLabel,$formElementLabelLayout){
        $this->objDBRadioEntity->updateMetaData($formNumber,$formElementName,$formElementLabel,$formElementLabelLayout);
        $this->formNumber = $formNumber;
        $this->radioName = $formElementName;
    }

    /*!
     * \brief This member function deletes an existing radio button form element with
     * the form element identifier.
     * \param formElementName A string that contains the
     * form element identifier.
     * \note This member function is protected so it is
     * only called by the \ref form_entity_handler class. To
     * delete a radio button or any other form element call the
     * deleteExisitngFormElement member function which will
     * automatically call this member function.
     * \return A boolean value for a successful delete.
     */
    protected function deleteRadioEntity($formNumber,$formElementName) {
        $deleteSuccess = $this->objDBRadioEntity->deleteFormElement($formNumber,$formElementName);
        return $deleteSuccess;
    }

    /*!
     * \brief This member function constructs a radio form element for a the actual form
     * rendering from the database.
     * \param radioName A string that contains the
     * form element identifier.
     * \note The member function is only called by the
     * parent class member function buildForm to build a form.
     * \return A constructed radio object.
     */
    protected function constructRadioEntity($radioName,$formNumber) {
        $radioParameters = $this->objDBRadioEntity->listRadioParameters($formNumber,$radioName);
//$radioName = $radioParameters["radioname"]["0"];

$constructedRadio = "";
$formElementLabel=NULL;
$labelOrientation=NULL;
        foreach ($radioParameters as $thisradioParameter) {

            $radioName = $thisradioParameter["radioname"];
            $radioOptionLabel = $thisradioParameter["radiooptionlabel"];
            $radioOptionValue = $thisradioParameter["radiooptionvalue"];
            $defaultValue = $thisradioParameter["defaultvalue"];
            $breakspace = $thisradioParameter["breakspace"];
            $formElementLabel = $thisradioParameter["label"];
            $labelOrientation = $thisradioParameter["labelorientation"];

            $radioUnderConstruction = new radio($radioName);
            $radioUnderConstruction->addOption($radioOptionValue, $radioOptionLabel);
            if ($defaultValue == TRUE) {
                $radioUnderConstruction->setSelected($radioOptionValue);
            }

            $currentConstructedRadio = $this->getBreakSpaceType($breakspace) . $radioUnderConstruction->show();
            $constructedRadio .=$currentConstructedRadio;
        }

        if ($formElementLabel == NULL) {
            return "<div id='" . $radioName . "'>" . $constructedRadio . "</div>";
        } else {
            $radioLabel = new label($formElementLabel, $radioName);
            switch ($labelOrientation) {
                case 'top':
                    return "<div id='" . $radioName . "'><div class='radioLabelContainer' style='clear:both;'> " . $radioLabel->show() . "</div>"
                    . "<div class='radioContainer'style='clear:left;'> " . $constructedRadio . "</div></div>";
                    break;
                case 'bottom':
                    return "<div id='" . $radioName . "'><div class='radioContainer'style='clear:both;'> " . $constructedRadio . "</div>" .
                    "<div class='radioLabelContainer' style='clear:both;'> " . $radioLabel->show() . "</div></div>";
                    break;
                case 'left':
                    return "<div id='" . $radioName . "'><div style='clear:both;overflow:auto;'>" . "<div class='radioLabelContainer' style='float:left;clear:left;'> " . $radioLabel->show() . "</div>"
                    . "<div class='radioContainer'style='float:left; clear:right;'> " . $constructedRadio . "</div></div></div>";
                    break;
                case 'right':
                    return "<div id='" . $radioName . "'><div style='clear:both;overflow:auto;'>" . "<div class='radioContainer'style='float:left;clear:left;'> " . $constructedRadio . "</div>" .
                    "<div class='radioLabelContainer' style='float:left;clear:right;'> " . $radioLabel->show() . "</div></div></div>";
                    break;
            }
        }
    }

    /*!
     * \brief This member function constructs a radio for a the WYSIWYG form editor.
     * \note The member function uses the private data members
     * that are already initialized by the createFormElement and the insertOptionandValue member
     *  functions which should be always called first to create a form element in the database before
     * displaying it with this member function.
     * \return A constructed radio button form element.
     */
    private function buildWYSIWYGRadioEntity() {

        $radioParameters = $this->objDBRadioEntity->listRadioParameters($this->formNumber,$this->radioName);
        $constructedRadio = "";
        $formElementLabel="";
        $radioName="";
        $radioOptionLabel="";
        $radioOptionValue="";
        $labelOrientation="";
        $breakspace="";
        foreach ($radioParameters as $thisradioParameter) {
            $radioName = $thisradioParameter["radioname"];
            $radioOptionLabel = $thisradioParameter["radiooptionlabel"];
            $radioOptionValue = $thisradioParameter["radiooptionvalue"];
            $defaultValue = $thisradioParameter["defaultvalue"];
            $breakspace = $thisradioParameter["breakspace"];
            $formElementLabel = $thisradioParameter["label"];
            $labelOrientation = $thisradioParameter["labelorientation"];
            $this->objRadio = new radio($radioName);
            $this->objRadio->addOption($radioOptionValue, $radioOptionLabel);
            if ($defaultValue == TRUE) {
                $this->objRadio->setSelected($radioOptionValue);
            }



            $currentConstructedRadio = $this->getBreakSpaceType($breakspace) . $this->objRadio->show();
            $constructedRadio .=$currentConstructedRadio;
        }



        if ($formElementLabel == NULL) {
            return "<div id='" . $this->radioName . "'>" . $constructedRadio . "</div>";
        } else {
            $radioLabel = new label($formElementLabel, $this->radioName);
            switch ($labelOrientation) {
                case 'top':
                    return "<div id='" . $this->radioName . "'><div class='radioLabelContainer' style='clear:both;'> " . $radioLabel->show() . "</div>"
                    . "<div class='radioContainer'style='clear:left;'> " . $constructedRadio . "</div></div>";
                    break;
                case 'bottom':
                    return "<div id='" . $this->radioName . "'><div class='radioContainer'style='clear:both;'> " . $constructedRadio . "</div>" .
                    "<div class='radioLabelContainer' style='clear:both;'> " . $radioLabel->show() . "</div></div>";
                    break;
                case 'left':
                    return "<div id='" . $this->radioName . "'><div style='clear:both;overflow:auto;'>" . "<div class='radioLabelContainer' style='float:left;clear:left;'> " . $radioLabel->show() . "</div>"
                    . "<div class='radioContainer'style='float:left; clear:right;'> " . $constructedRadio . "</div></div></div>";
                    break;
                case 'right':
                    return "<div id='" . $this->radioName . "'><div style='clear:both;overflow:auto;'>" . "<div class='radioContainer'style='float:left;clear:left;'> " . $constructedRadio . "</div>" .
                    "<div class='radioLabelContainer' style='float:left;clear:right;'> " . $radioLabel->show() . "</div></div></div>";
                    break;
            }
        }
//dead code that creates a radio without the labels
//return $constructeddd;
//  foreach ($this->labelnOptionArray as $radioValue => $radioOptionLabel) {
//
//      $this->objRadio->addOption($radioValue,$radioOptionLabel);
//        if ($this->tempWYSIWYGBoolDefaultSelected==TRUE)
//  {
//      $this->objRadio->setSelected($radioValue);
//  }
//  }
//               return $this->getBreakSpaceType($this->tempWYSIWYGLayoutOption).$this->objRadio->show();
    }

    /*!
     * \brief This member function allows you to get a radio button for a the WYSIWYG form editor
     * that is already saved in the database.
     * \return A constructed radio button.
     */
    public function showWYSIWYGRadioEntity() {
        return $this->buildWYSIWYGRadioEntity();
    }

}

?>
