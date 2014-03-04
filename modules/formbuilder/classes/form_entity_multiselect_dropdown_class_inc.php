<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!  \class form_entity_multiselect_dropdown
 *
 *  \brief This class models all the content and functionality for the
 * dropdown form element.
 * \note multi-select is abbreviated to be "ms".
 * \brief It provides functionality to insert new ms dropdown lists, create them for the
 * WYSIWYG form editor and render them in the actual construction of the form.
 * It also allows you to delete ms dropdown lists from forms.
 *  \brief This is a child class that belongs to the form entity heirarchy.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
include_once 'form_entity_handler_class_inc.php';

class form_entity_multiselect_dropdown extends form_entity_handler {

    private $formNumber;
    /*!
     * \brief Private data member that stores a ms drop down object for the WYSIWYG
     * form editor.
     */
    private $objMSDD;

    /*!
     * \brief This data member stores the form element identifier or ID that can
     * be used anywhere in this class.
     */
    private $msddName;

    /*!
     * \brief This data member stores the label for the ms drop down list.
     */
    private $msddLabel;

    /*!
     * \brief This data member stores all the options with their values and labels
     * for the ms drop down list if they are selected by default.
     */
    private $defaultMSValuesArray;

    /*!
     * \brief This data member stores all the options with their values and labels
     * for the ms drop down list.
     */
    private $msddLabelnOptionArray;

    /*!
     * \brief Private data member from the class \ref dbformbuilder_multiselect_dropdown_entity that stores all
     * the properties of this class in an usable object.
     * \note This object is used to add, get or delete ms dropdown form elements.
     */
    protected $objDBmsddEntity;

    /*!
     * \brief Standard constructor that loads classes for other modules and initializes
     * and instatiates private data members.
     * \note The dropdown and label classes are from the htmlelements module
     * inside the chisimba core modules.
     */
    public function init() {
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->msddName = NULL;
        $this->msddLabelnOptionArray = array();
        $this->defaultMSValuesArray = array();
        $this->msddLabel = NULL;
        $this->formNumber = NULL;
        $this->objDBmsddEntity = $this->getObject('dbformbuilder_multiselect_dropdown_entity', 'formbuilder');
    }

    /*!
     * \brief This member function initializes some of the private data members for the
     * ms dropdown object.
     * \parm elementName A string for the form element identifier.
     */
    public function createFormElement($formNumber,$elementName="") {
        $this->formNumber = $formNumber;
        $this->msddName = $elementName;
        $this->objMSDD = new dropdown($elementName);
    }

    /*!
     * \brief This member function gets the ms dropdown form element name if the private
     * data member msddName is set already.
     * \note This member function is not used in this module.
     * \return A string.
     */
    public function getWYSIWYGMultiSelectDropdownName() {
        return $this->msddName;
    }

    /*!
     * \brief This member function gets the ms dropdown name if it has already
     * been saved in the database.
     * \param msdropdownName A string containing the form element indentifier.
     * \return A string.
     */
    protected function getMultiSelectDropdownName($formNumber,$msDropDownName) {
        $msddParameters = $this->objDBmsddEntity->listMultiSelectDropdownParameters($formNumber,$msDropDownName);

        return $msddName = $msddParameters["0"]['multiselectdropdownname'];
    }

    /*!
     * \brief This member function contructs the html content for the form that
     * allows you to insert the ms drop down parameters to insert
     * a ms drop down object form element.
     * \note This member function uses member functions
     * from the parent class \ref form_entity_handler to
     * construct this form.
     * \param formName A string.
     * \return A constructed ms drop down insert form.
     */
    public function getWYSIWYGMSDropDownInsertForm($formName) {
        $WYSIWYGDropDownInsertForm = "<b>Multi-Selectable Drop Down List HTML ID and Name Menu</b>";
        $WYSIWYGDropDownInsertForm.="<div id='msddIDContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGDropDownInsertForm.= $this->buildInsertIdForm('dropdown', $formName, "70") . "";
        $WYSIWYGDropDownInsertForm.= "</div>";
        $WYSIWYGDropDownInsertForm.= "<b>Multi-Selectable Drop Down List Label Menu</b>";
        $WYSIWYGDropDownInsertForm.= "<div id='msdropdownLabelContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGDropDownInsertForm.= $this->insertFormLabelOptions("msdropdown", "labelOrientation", NULL, NULL);
        $WYSIWYGDropDownInsertForm.= "</div>";
        $WYSIWYGDropDownInsertForm.="<b>Multi-Selectable Drop Down List Size Menu</b>";
        $WYSIWYGDropDownInsertForm.="<div id='msdropdownSizeContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGDropDownInsertForm .= $this->insertMSDropDownSizeForm(NULL);
        $WYSIWYGDropDownInsertForm.= "</div>";
        $WYSIWYGDropDownInsertForm.="<b>Insert Multi-Selectable Drop Down List Options Menu</b>";
        $WYSIWYGDropDownInsertForm.="<div id='msddOptionAndValueContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGDropDownInsertForm.= $this->insertOptionAndValueForm('multi-selectable drop down list', 0) . "<br>";
        $WYSIWYGDropDownInsertForm.= "</div>";
        return $WYSIWYGDropDownInsertForm;
    }

    public function getWYSIWYGMSDropDownEditForm($formNumber, $formElementName) {
        $msddParameters = $this->objDBmsddEntity->listMultiSelectDropdownParameters($formNumber, $formElementName);
        if (empty($msddParameters)) {
            return 0;
        } else {
            $msddsize = NULL;
            $formElementLabel = NULL;
            $labelOrientation = NULL;
            foreach ($msddParameters as $thisMSDDParameter) {

                //$msddName = $thisMSDDParameter["multiselectdropdownname"];
                //$msddOptionLabel = $thisMSDDParameter["msddoptionlabel"];
                //$msddOptionValue = $thisMSDDParameter["msddoptionvalue"];
                //$defaultValue = $thisMSDDParameter["defaultvalue"];
                $msddsize = $thisMSDDParameter["msddsize"];
                $formElementLabel = $thisMSDDParameter["label"];
                $labelOrientation = $thisMSDDParameter["labelorientation"];
            }
            $WYSIWYGMSDropDownEditForm = "<div id='editFormElementTabs'>	
         <ul>
		<li><a href='#editFormElementPropertiesContainer'>Edit Multi-Select Drop Down List Properties</a></li>
		<li><a href='#editFormElementOptionsContainer'>Edit Multi-Select Drop Down List Options</a></li>
	</ul>";
            $WYSIWYGMSDropDownEditForm.= "<div id='editFormElementPropertiesContainer'>";

            $WYSIWYGMSDropDownEditForm.= "<b>Multi-Selectable Drop Down List Label Properties</b>";
            $WYSIWYGMSDropDownEditForm.= "<div id='msdropdownLabelContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
            $WYSIWYGMSDropDownEditForm.= $this->insertFormLabelOptions("msdropdown", "labelOrientation", $formElementLabel, $labelOrientation);
            $WYSIWYGMSDropDownEditForm.= "</div>";
            $WYSIWYGMSDropDownEditForm.="<b>Multi-Selectable Drop Down List Size Properties</b>";
            $WYSIWYGMSDropDownEditForm.="<div id='msdropdownSizeContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
            $WYSIWYGMSDropDownEditForm .= $this->insertMSDropDownSizeForm($msddsize);
            $WYSIWYGMSDropDownEditForm.= "</div>";
            $WYSIWYGMSDropDownEditForm.= "</div>";

            $WYSIWYGMSDropDownEditForm.= "<div id='editFormElementOptionsContainer'>";
            $WYSIWYGMSDropDownEditForm.= "<b>Edit Multi-Select Drop Down List Options</b>";
            $WYSIWYGMSDropDownEditForm.="<div id='dropdownOptionsContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
            $WYSIWYGMSDropDownEditForm.="<style>
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
width: 180px;
float:left;
}

.optionLabelContainer{
width: 180px;
float:left;
}

.defaultOptionContainer{
width: 130px;
float:left;
}

a:link, a:visited {
    text-decoration: underline;
}

.deleteOptionLink, .editOptionLink {
    display: block;
    float: right;
    width: 80px;
}

        </style>";

            $optionNumber = 1;
            $WYSIWYGMSDropDownEditForm.= "<div class='formOptionsListContainer'>";
            $WYSIWYGMSDropDownEditForm.= "<div class='singleOptionContainer' id='optionTitle'>";
            $WYSIWYGMSDropDownEditForm.= "<div class='optionValueContainer'><b>Option Value</b></div>";
            $WYSIWYGMSDropDownEditForm.= "<div class='optionLabelContainer'><b>Option Label</b></div>";
//                $WYSIWYGDropDownEditForm.= "<div class='optionBreakSpaceContainer'><b>Break Space</b></div>";
            $WYSIWYGMSDropDownEditForm.= "<div class='defaultOptionContainer'><b>Default Selected</b></div>";
            $WYSIWYGMSDropDownEditForm.= "</div>";

            foreach ($msddParameters as $thisMSDDParameter) {

                //$msddName = $thisMSDDParameter["multiselectdropdownname"];
                $msddOptionLabel = $thisMSDDParameter["msddoptionlabel"];
                $msddOptionValue = $thisMSDDParameter["msddoptionvalue"];
                $defaultValue = $thisMSDDParameter["defaultvalue"];
                $msddsize = $thisMSDDParameter["msddsize"];
                $formElementLabel = $thisMSDDParameter["label"];
                $labelOrientation = $thisMSDDParameter["labelorientation"];
                $id = $thisMSDDParameter["id"];

                $WYSIWYGMSDropDownEditForm.= "<div class='singleOptionContainer' id='option" . $optionNumber . "' optionID='" . $id . "' formNumber='" . $formNumber . "' formElementName='" . $formElementName . "' optionLabel='" . $msddOptionLabel . "' optionValue='" . $msddOptionValue . "' defaultValue='" . $defaultValue . "' formElementSize='".$msddsize."' formElementLabel='" . $formElementLabel . "' labelOrientation='" . $labelOrientation . "'>";
                $WYSIWYGMSDropDownEditForm.= "<div class='optionValueContainer'>" . $msddOptionValue . "</div>";
                $WYSIWYGMSDropDownEditForm.= "<div class='optionLabelContainer'>" . $msddOptionLabel . "</div>";
                if ($defaultValue == TRUE) {
                    $WYSIWYGMSDropDownEditForm.= "<div class='defaultOptionContainer'>yes</div>";
                } else {
                    $WYSIWYGMSDropDownEditForm.= "<div class='defaultOptionContainer'>no</div>";
                }
                $WYSIWYGMSDropDownEditForm.= "<a class='deleteOptionLink' href='#delete'>Delete</a>";
                $WYSIWYGMSDropDownEditForm.= "<a class='editOptionLink' href='#edit'>Edit</a>";
                $WYSIWYGMSDropDownEditForm.= "</div>";
                $optionNumber++;
            }

            $WYSIWYGMSDropDownEditForm.= "</div>";
            $WYSIWYGMSDropDownEditForm.= "</div>";

            $WYSIWYGMSDropDownEditForm.= "</div>";
            $WYSIWYGMSDropDownEditForm.= "</div>";


            $WYSIWYGMSDropDownEditForm.= "<style>
            
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
height:390px;
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
min-height:390px;
}
</style>";
            $WYSIWYGMSDropDownEditForm.="<div class='formElementOptionsFormSuperContainer'>";
            $WYSIWYGMSDropDownEditForm.= "<div class='editFormElementOptionsSideSeperator'></div>";
            $WYSIWYGMSDropDownEditForm.="<div class='editFormElementOptionsHeadingSpacer'><div class='formElementOptionUpdateHeading'>Update Multi-Select Drop Down List Option</div></div>";
            $WYSIWYGMSDropDownEditForm.= "<div class='editFormElementFormContainer'>";
            $WYSIWYGMSDropDownEditForm.="<b>Multi-Selectable Drop Down Options Menu</b>";
            $WYSIWYGMSDropDownEditForm.="<div id='ddOptionAndValueContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
            $WYSIWYGMSDropDownEditForm.= $this->insertOptionAndValueForm('multi-selectable drop down list', 0) . "<br>";
            $WYSIWYGMSDropDownEditForm.= "</div>";
            $WYSIWYGMSDropDownEditForm.= "<div class='formElementOptionsFormButtonsContainer'></div>";
            $WYSIWYGMSDropDownEditForm.= "</div>";
            $WYSIWYGMSDropDownEditForm.= "</div>";
            return $WYSIWYGMSDropDownEditForm;
        }
    }

    /*!
     * \brief This member function allows you to insert a ms drop down list option in a form with
     * a form element identifier.
     * \brief Before a new ms drop down list option gets inserted into the database,
     * duplicate entries are checked if there is another ms dropdown option.
     * in this same form with the same form element identifier.
     * \param option A string for the label for the option.
     * \param value A string for the actual value of the option.
     * \param defaultSelected A boolean to determine whether or not is option
     * is selected by default.
     * \param msddsize An integer that determines how many options are visible
     * to the user of the form.
     * \param formElementLabel A string for the actual label text for the entire drop down
     * list form element.
     * \param labelLayout A string that stores whether the form element label gets
     * put on top, bottom, left or right of the dropdown list.
     * \return A boolean value on successful storage of the ms dropdown form element.
     */
    public function insertOptionandValue($formNumber,$formElementName, $option, $value, $defaultSelected, $msddsize, $formElementLabel, $labelLayout) {

        if ($this->objDBmsddEntity->checkDuplicateMultiSelectDropdownEntry($formNumber,$formElementName, $value) == TRUE) {
            $this->objDBmsddEntity->updateMenuSize($formNumber,$formElementName, $msddsize);
            $this->objDBmsddEntity->insertSingle($formNumber,$formElementName, $option, $value, $defaultSelected, $msddsize, $formElementLabel, $labelLayout);

            $this->msddName = $formElementName;


            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function updateOptionandValue($optionID,$formNumber,$formElementName, $option, $value, $defaultSelected,$msddsize, $formElementLabel, $labelOrientation) {
              if ($this->objDBmsddEntity->checkIfOptionExists($optionID) == TRUE) {
            $this->objDBmsddEntity->updateSingle($optionID,$formNumber,$formElementName, $option, $value, $defaultSelected,$msddsize, $formElementLabel, $labelOrientation);
           $this->msddName = $formElementName;
            $this->formNumber = $formNumber;
            
            return TRUE;
        } else {
            return FALSE;
        }  
    }
    

    public function updateMetaData($formNumber,$formElementName,$formElementLabel,$formElementLabelLayout,$formElementSize){
        $this->objDBmsddEntity->updateMetaData($formNumber,$formElementName,$formElementLabel,$formElementLabelLayout,$formElementSize);
            
        $this->formNumber = $formNumber;
        $this->msddName = $formElementName;
    }

    /*!
     * \brief This member function deletes an existing ms dropdown list form element with
     * the form element identifier.
     * \param formElementName A string that contains the
     * form element identifier.
     * \note This member function is protected so it is
     * only called by the \ref form_entity_handler class. To
     * delete a ms drop down list or any other form element call the
     * deleteExisitngFormElement member function which will
     * automatically call this member function.
     * \return A boolean value for a successful delete.
     */
    protected function deleteMultiSelectDropDownEntity($formNumber,$formElementName) {
        $deleteSuccess = $this->objDBmsddEntity->deleteFormElement($formNumber,$formElementName);
        return $deleteSuccess;
    }

    /*!
     * \brief This member function constructs a ms dropdown for a the actual form
     * rendering from the database.
     * \param msDropDownName A string that contains the
     * form element identifier.
     * \note The member function is only called by the
     * parent class member function buildForm to build a form.
     * \return A constructed ms dropdown object.
     */
    protected function constructMultiSelectDropDownEntity($msDropDownName,$formNumber) {

        $msddParameters = $this->objDBmsddEntity->listMultiSelectDropdownParameters($formNumber,$msDropDownName);

        $msddName = $msddParameters["0"]['multiselectdropdownname'];
        $msddUnderConstruction = new dropdown($msddName);
        $msddLabel=NULL;
        $labelOrientation=NULL;
        foreach ($msddParameters as $thisMSDDParameter) {

            $msddName = $thisMSDDParameter["multiselectdropdownname"];
            $msddOptionLabel = $thisMSDDParameter["msddoptionlabel"];
            $msddOptionValue = $thisMSDDParameter["msddoptionvalue"];
            $defaultValue = $thisMSDDParameter["defaultvalue"];
            $msddsize = $thisMSDDParameter["msddsize"];
            $msddLabel = $thisMSDDParameter["label"];
            $labelOrientation = $thisMSDDParameter["labelorientation"];


            $msddUnderConstruction->addOption($msddOptionValue, $msddOptionLabel);
            if ($defaultValue == TRUE) {
                $this->defaultMSValuesArray[] = $msddOptionValue;
            }
        }
        $msddUnderConstruction->setMultiSelected($this->defaultMSValuesArray);
        $msddUnderConstruction->multiple = true;
        $msddUnderConstruction->size = $msddsize;

        if ($msddLabel == NULL) {
            $constructedmsdd = $msddUnderConstruction->show();
        } else {
            $msddLabels = new label($msddLabel, $msddName);
            switch ($labelOrientation) {
                case 'top':
                    $constructedmsdd = "<div id='" . $msddName . "'><div class='msddLabelContainer' style='clear:both;'> " . $msddLabels->show() . "</div>"
                            . "<div class='msddContainer'style='clear:left;'> " . $msddUnderConstruction->show() . "</div></div>";
                    break;
                case 'bottom':
                    $constructedmsdd = "<div id='" . $msddName . "'><div class='msddContainer'style='clear:both;'> " . $msddUnderConstruction->show() . "</div>" .
                            "<div class='msddLabelContainer' style='clear:both;'> " . $msddLabels->show() . "</div></div>";
                    break;
                case 'left':
                    $constructedmsdd = "<div id='" . $msddName . "'><div style='clear:both;overflow:auto;'>" . "<div class='msddLabelContainer' style='float:left;clear:left;'> " . $msddLabels->show() . "</div>"
                            . "<div class='msddContainer'style='float:left; clear:right;'> " . $msddUnderConstruction->show() . "</div></div></div>";
                    break;
                case 'right':
                    $constructedmsdd = "<div id='" . $msddName . "'><div style='clear:both;overflow:auto;'>" . "<div class='msddContainer'style='float:left;clear:left;'> " . $msddUnderConstruction->show() . "</div>" .
                            "<div class='msddLabelContainer' style='float:left;clear:right;'> " . $msddLabels->show() . "</div></div></div>";
                    break;
            }
        }
        return $constructedmsdd;
    }

    /*!
     * \brief This member function constructs a ms dropdown for a the WYSIWYG form editor.
     * \note The member function uses the private data members
     * that are already initialized by the createFormElement and the insertOptionandValue member
     *  functions which should be always called first to create a form element in the database before
     * displaying it with this member function.
     * \return A constructed ms dropdown list form element.
     */
    private function buildWYSIWYGMultiSelectDropdownEntity() {

        $msddParameters = $this->objDBmsddEntity->listMultiSelectDropdownParameters($this->formNumber,$this->msddName);
        $msddLabel=NULL;
        $labelOrientation=NULL;
        foreach ($msddParameters as $thisDDParameter) {

    //        $msddName = $thisDDParameter["multiselectdropdownname"];
            $msddOptionLabel = $thisDDParameter["msddoptionlabel"];
            $msddOptionValue = $thisDDParameter["msddoptionvalue"];
            $defaultValue = $thisDDParameter["defaultvalue"];
            $msddsize = $thisDDParameter["msddsize"];
            $msddLabel = $thisDDParameter["label"];
            $labelOrientation = $thisDDParameter["labelorientation"];

            $this->objMSDD->addOption($msddOptionValue, $msddOptionLabel);
            if ($defaultValue == TRUE) {
// $this->objDD->setSelected($ddValue);
                $this->defaultMSValuesArray[] = $msddOptionValue;
            }
        }

        $this->objMSDD->setMultiSelected($this->defaultMSValuesArray);
        $this->objMSDD->multiple = true;
        $this->objMSDD->size = $msddsize;

        if ($msddLabel == NULL) {
            return "<div id='" . $this->msddName . "'>" . $this->objMSDD->show() . "</div>";
        } else {
            $msddLabels = new label($msddLabel, $this->msddName);
            switch ($labelOrientation) {
                case 'top':
                    return "<div id='" . $this->msddName . "'><div class='msddLabelContainer' style='clear:both;'> " . $msddLabels->show() . "</div>"
                    . "<div class='msddContainer'style='clear:left;'> " . $this->objMSDD->show() . "</div></div>";
                    break;
                case 'bottom':
                    return "<div id='" . $this->msddName . "'><div class='msddContainer'style='clear:both;'> " . $this->objMSDD->show() . "</div>" .
                    "<div class='msddLabelContainer' style='clear:both;'> " . $msddLabels->show() . "</div></div>";
                    break;
                case 'left':
                    return "<div id='" . $this->msddName . "'><div style='clear:both;overflow:auto;'>" . "<div class='msddLabelContainer' style='float:left;clear:left;'> " . $msddLabels->show() . "</div>"
                    . "<div class='msddContainer'style='float:left; clear:right;'> " . $this->objMSDD->show() . "</div></div></div>";
                    break;
                case 'right':
                    return "<div id='" . $this->msddName . "'><div style='clear:both;overflow:auto;'>" . "<div class='msddContainer'style='float:left;clear:left;'> " . $this->objMSDD->show() . "</div>" .
                    "<div class='msddLabelContainer' style='float:left;clear:right;'> " . $msddLabels->show() . "</div></div></div>";
                    break;
            }
        }
    }

    /*!
     * \brief This member function allows you to get a ms dropdown for a the WYSIWYG form editor
     * that is already saved in the database.
     * \return A constructed ms dropdown list.
     */
    public function showWYSIWYGMultiSelectDropdownEntity() {
        return $this->buildWYSIWYGMultiSelectDropdownEntity();
    }

}

?>
