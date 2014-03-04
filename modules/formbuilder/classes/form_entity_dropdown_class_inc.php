<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!  \class form_entity_dropdown
 *
 *  \brief This class models all the content and functionality for the
 * dropdown form element.
 * \brief It provides functionality to insert new dropdown lists, create them for the
 * WYSIWYG form editor and render them in the actual construction of the form.
 * It also allows you to delete dropdown lists from forms.
 *  \brief This is a child class that belongs to the form entity heirarchy.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
include_once 'form_entity_handler_class_inc.php';

class form_entity_dropdown extends form_entity_handler {
    
    private $formNumber;
    /*!
     * \brief Private data member that stores a drop down object for the WYSIWYG
     * form editor.
     */
    private $objDD;

    /*!
     * \brief This data member stores the form element identifier or ID that can
     * be used anywhere in this class.
     */
    private $ddName;

    /*!
     * \brief This data member stores the label for the drop down list.
     */
    private $ddLabel;

    /*!
     * \brief This data member stores all the options with their values and labels
     * for the drop down list.
     */
    private $ddLabelnOptionArray;

    /*!
     * \brief Private data member from the class \ref dbformbuilder_dropdown_entity that stores all
     * the properties of this class in an usable object.
     * \note This object is used to add, get or delete dropdown form elements.
     */
    protected $objDBddEntity;

    /*!
     * \brief Standard constructor that loads classes for other modules and initializes
     * and instatiates private data members.
     * \note The dropdown and label classes are from the htmlelements module
     * inside the chisimba core modules.
     */
    public function init() {
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->ddName = NULL;
        $this->ddLabelnOptionArray = array();
        $this->ddLabel = NULL;
        $this->formNumber = NULL;
        $this->objDBddEntity = $this->getObject('dbformbuilder_dropdown_entity', 'formbuilder');
    }

    /*!
     * \brief This member function initializes some of the private data members for the
     * dropdown object.
     * \parm elementName A string for the form element identifier.
     */
    public function createFormElement($formNumber,$elementName="") {
        $this->formNumber = $formNumber;
        $this->ddName = $elementName;
        $this->objDD = new dropdown($elementName);
    }

    /*!
     * \brief This member function gets the dropdown form element name if the private
     * data member ddName is set already.
     * \note This member function is not used in this module.
     * \return A string.
     */
    public function getWYSIWYGDropdownName() {
        return $this->ddName;
    }

    /*!
     * \brief This member function gets the dropdown name if it has already
     * been saved in the database.
     * \param dropdownName A string containing the form element indentifier.
     * \return A string.
     */
    protected function getDropdownName($formNumber,$dropdownName) {
        $ddParameters = $this->objDBddEntity->listDropdownParameters($formNumber,$dropdownName);

        return $ddName = $ddParameters["0"]['dropdownname'];
    }

    /*!
     * \brief This member function contructs the html content for the form that
     * allows you to insert the drop down parameters to insert
     * a drop down object form element.
     * \note This member function uses member functions
     * from the parent class \ref form_entity_handler to
     * construct this form.
     * \param formName A string.
     * \return A constructed drop down insert form.
     */
    public function getWYSIWYGDropDownInsertForm($formName) {
        $WYSIWYGDropDownInsertForm = "<b>Drop Down HTML ID and Name Menu</b>";
        $WYSIWYGDropDownInsertForm.="<div id='ddIDContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGDropDownInsertForm.= $this->buildInsertIdForm('dropdown', $formName, "70") . "";
        $WYSIWYGDropDownInsertForm.= "</div>";
        $WYSIWYGDropDownInsertForm.= "<b>Drop Down Label Menu</b>";
        $WYSIWYGDropDownInsertForm.= "<div id='ddLabelContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGDropDownInsertForm.= $this->insertFormLabelOptions("dropdown", "labelOrientation",NULL,NULL);
        $WYSIWYGDropDownInsertForm.= "</div>";
        $WYSIWYGDropDownInsertForm.= "<div id='optionAndValueContainer'>";
        $WYSIWYGDropDownInsertForm.="<b>Insert Drop Down Options Menu</b>";
        $WYSIWYGDropDownInsertForm.="<div id='ddOptionAndValueContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGDropDownInsertForm.= $this->insertOptionAndValueForm('drop down list', 0) . "<br>";
        $WYSIWYGDropDownInsertForm.= "</div>";
        $WYSIWYGDropDownInsertForm.= "</div>";
        return $WYSIWYGDropDownInsertForm;
    }
    
    public function getWYSIWYGDropDownEditForm($formNumber,$formElementName){
      $ddParameters = $this->objDBddEntity->listDropdownParameters($formNumber, $formElementName);
        if (empty($ddParameters)) {
            return 0;
        } else {

            $formElementLabel = "";
            $labelOrientation = "";
            foreach ($ddParameters as $thisDDParameter) {

                //$ddValue = $thisDDParameter["ddoptionvalue"];
                //$ddOptionLabel = $thisDDParameter["ddoptionlabel"];
                //$defaultValue = $thisDDParameter["defaultvalue"];
                $formElementLabel = $thisDDParameter["label"];
                $labelOrientation = $thisDDParameter["labelorientation"];
            }
              $WYSIWYGDropDownEditForm="<div id='editFormElementTabs'>	
         <ul>
		<li><a href='#editFormElementPropertiesContainer'>Edit Drop Down List Properties</a></li>
		<li><a href='#editFormElementOptionsContainer'>Edit Drop Down List Options</a></li>
	</ul>";
        $WYSIWYGDropDownEditForm.= "<div id='editFormElementPropertiesContainer'>";
        
        $WYSIWYGDropDownEditForm.= "<b>Drop Down Label Properties</b>";
        $WYSIWYGDropDownEditForm.= "<div id='ddLabelContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGDropDownEditForm.= $this->insertFormLabelOptions("dropdown", "labelOrientation",$formElementLabel,$labelOrientation);
        $WYSIWYGDropDownEditForm.= "</div>";
        $WYSIWYGDropDownEditForm.= "</div>";
        
        $WYSIWYGDropDownEditForm.= "<div id='editFormElementOptionsContainer'>";
        $WYSIWYGDropDownEditForm.= "<b>Edit Drop Down List Options</b>";
        $WYSIWYGDropDownEditForm.="<div id='dropdownOptionsContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $optionNumber = 1;
        
        $WYSIWYGDropDownEditForm.="<style>
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
        

                $WYSIWYGDropDownEditForm.= "<div class='formOptionsListContainer'>";
                $WYSIWYGDropDownEditForm.= "<div class='singleOptionContainer' id='optionTitle'>";
                $WYSIWYGDropDownEditForm.= "<div class='optionValueContainer'><b>Option Value</b></div>";
                $WYSIWYGDropDownEditForm.= "<div class='optionLabelContainer'><b>Option Label</b></div>";
//                $WYSIWYGDropDownEditForm.= "<div class='optionBreakSpaceContainer'><b>Break Space</b></div>";
                $WYSIWYGDropDownEditForm.= "<div class='defaultOptionContainer'><b>Default Selected</b></div>"; 
                $WYSIWYGDropDownEditForm.= "</div>";
      

            foreach ($ddParameters as $thisDDParameter) {

                $ddValue = $thisDDParameter["ddoptionvalue"];
                $ddOptionLabel = $thisDDParameter["ddoptionlabel"];
                $defaultValue = $thisDDParameter["defaultvalue"];
                $formElementLabel = $thisDDParameter["label"];
                $labelOrientation = $thisDDParameter["labelorientation"];
                $id = $thisDDParameter["id"];
                
                $WYSIWYGDropDownEditForm.= "<div class='singleOptionContainer' id='option".$optionNumber."' optionID='".$id."' formNumber='".$formNumber."' formElementName='".$formElementName."' optionLabel='".$ddOptionLabel."' optionValue='".$ddValue."' defaultValue='".$defaultValue."' formElementLabel='".$formElementLabel."' labelOrientation='".$labelOrientation."'>";
                $WYSIWYGDropDownEditForm.= "<div class='optionValueContainer'>".$ddValue."</div>";
                $WYSIWYGDropDownEditForm.= "<div class='optionLabelContainer'>".$ddOptionLabel."</div>";
//                $WYSIWYGDropDownEditForm.= "<div class='optionBreakSpaceContainer'>".$breakspace."</div>";
                if ($defaultValue == TRUE){
                  $WYSIWYGDropDownEditForm.= "<div class='defaultOptionContainer'>yes</div>";  
                }else{
                  $WYSIWYGDropDownEditForm.= "<div class='defaultOptionContainer'>no</div>";  
                }
                $WYSIWYGDropDownEditForm.= "<a class='deleteOptionLink' href='#delete'>Delete</a>";
                $WYSIWYGDropDownEditForm.= "<a class='editOptionLink' href='#edit'>Edit</a>";
                $WYSIWYGDropDownEditForm.= "</div>";
                $optionNumber++;
            }
            
        $WYSIWYGDropDownEditForm.= "</div>";
        $WYSIWYGDropDownEditForm.= "</div>";
        
        $WYSIWYGDropDownEditForm.= "</div>";
        $WYSIWYGDropDownEditForm.= "</div>";
        
   $WYSIWYGDropDownEditForm.= "<style>
            
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
height:425px;
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
min-height:425px;
}
</style>";
        $WYSIWYGDropDownEditForm.="<div class='formElementOptionsFormSuperContainer'>";
        $WYSIWYGDropDownEditForm.= "<div class='editFormElementOptionsSideSeperator'></div>";
        $WYSIWYGDropDownEditForm.="<div class='editFormElementOptionsHeadingSpacer'><div class='formElementOptionUpdateHeading'>Update Drop Down List Option</div></div>";
        $WYSIWYGDropDownEditForm.= "<div class='editFormElementFormContainer'>";
        

        $WYSIWYGDropDownEditForm.="<b>Drop Down Options Menu</b>";
        $WYSIWYGDropDownEditForm.="<div id='ddOptionAndValueContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGDropDownEditForm.= $this->insertOptionAndValueForm('drop down list', 0) . "<br>";
        $WYSIWYGDropDownEditForm.= "</div>";
        $WYSIWYGDropDownEditForm.= "<div class='formElementOptionsFormButtonsContainer'></div>";
        $WYSIWYGDropDownEditForm.= "</div>";
        $WYSIWYGDropDownEditForm.= "</div>";
        return $WYSIWYGDropDownEditForm;     
        }
    }

    /*!
     * \brief This member function allows you to insert a drop down list option in a form with
     * a form element identifier.
     * \brief Before a new drop down list option gets inserted into the database,
     * duplicate entries are checked if there is another dropdown option.
     * in this same form with the same form element identifier.
     * \param option A string for the label for the option.
     * \param value A string for the actual value of the option.
     * \param defaultSelected A boolean to determine whether or not is option
     * is selected by default.
     * \param label A string for the actual label text for the entire drop down
     * list form element.
     * \param labelOrientation A string that stores whether the form element label gets
     * put on top, bottom, left or right of the dropdown list.
     * \return A boolean value on successful storage of the dropdown form element.
     */
    public function insertOptionandValue($formNumber,$formElementName, $option, $value, $defaultSelected, $label, $labelOrientation) {
        if ($this->objDBddEntity->checkDuplicateDropdownEntry($formNumber,$formElementName, $value) == TRUE) {
            $this->objDBddEntity->insertSingle($formNumber,$formElementName, $option, $value, $defaultSelected, $label, $labelOrientation);

            $this->ddName = $formElementName;


            return TRUE;
        } else {
            return FALSE;
        }
    }
    
        public function updateOptionandValue($optionID,$formNumber,$formElementName, $option, $value, $defaultSelected, $formElementLabel, $labelOrientation) {
              if ($this->objDBddEntity->checkIfOptionExists($optionID) == TRUE) {
            $this->objDBddEntity->updateSingle($optionID,$formNumber,$formElementName, $option, $value, $defaultSelected, $formElementLabel, $labelOrientation);
              $this->ddName = $formElementName;
            $this->formNumber = $formNumber;
            
            return TRUE;
        } else {
            return FALSE;
        }  
    }
    

    public function updateMetaData($formNumber,$formElementName,$formElementLabel,$formElementLabelLayout){
        $this->objDBddEntity->updateMetaData($formNumber,$formElementName,$formElementLabel,$formElementLabelLayout);
                
        $this->formNumber = $formNumber;
        $this->ddName = $formElementName;
    }

    /*!
     * \brief This member function deletes an existing dropdown list form element with
     * the form element identifier.
     * \param formElementName A string that contains the
     * form element identifier.
     * \note This member function is protected so it is
     * only called by the \ref form_entity_handler class. To
     * delete a drop down list or any other form element call the
     * deleteExisitngFormElement member function which will
     * automatically call this member function.
     * \return A boolean value for a successful delete.
     */
    protected function deleteDropDownEntity($formNumber,$formElementName) {
        $deleteSuccess = $this->objDBddEntity->deleteFormElement($formNumber,$formElementName);
        return $deleteSuccess;
    }

    /*!
     * \brief This member function constructs a dropdown for a the actual form
     * rendering from the database.
     * \param dropdownName A string that contains the
     * form element identifier.
     * \note The member function is only called by the
     * parent class member function buildForm to build a form.
     * \return A constructed dropdown object.
     */
    protected function constructDropDownEntity($dropdownName,$formNumber) {
        $ddParameters = $this->objDBddEntity->listDropdownParameters($formNumber,$dropdownName);

        $ddName = $ddParameters["0"]['dropdownname'];
        $ddUnderConstruction = new dropdown($ddName);
        $ddLabel=NULL;
        $labelOrientation=NULL;
        foreach ($ddParameters as $thisDDParameter) {

            $ddValue = $thisDDParameter["ddoptionvalue"];
            $ddOptionLabel = $thisDDParameter["ddoptionlabel"];
            $defaultValue = $thisDDParameter["defaultvalue"];
            $ddLabel = $thisDDParameter["label"];
            $labelOrientation = $thisDDParameter["labelorientation"];

            $ddUnderConstruction->addOption($ddValue, $ddOptionLabel);
            if ($defaultValue == TRUE) {
                $ddUnderConstruction->setSelected($ddValue);
            }
        }
        if ($ddLabel == NULL) {
            $constructeddd = "<div id='" . $ddName . "'>" . $ddUnderConstruction->show() . "</div>";
        } else {
            $ddLabels = new label($ddLabel, $ddName);
            switch ($labelOrientation) {
                case 'top':
                    $constructeddd = "<div id='" . $ddName . "'><div class='ddLabelContainer' style='clear:both;'> " . $ddLabels->show() . "</div>"
                            . "<div class='ddContainer'style='clear:left;'> " . $ddUnderConstruction->show() . "</div></div>";
                    break;
                case 'bottom':
                    $constructeddd = "<div id='" . $ddName . "'><div class='ddContainer'style='clear:both;'> " . $ddUnderConstruction->show() . "</div>" .
                            "<div class='ddLabelContainer' style='clear:both;'> " . $ddLabels->show() . "</div></div>";
                    break;
                case 'left':
                    $constructeddd = "<div id='" . $ddName . "'><div style='clear:both;overflow:auto;'>" . "<div class='ddLabelContainer' style='float:left;clear:left;'> " . $ddLabels->show() . "</div>"
                            . "<div class='ddContainer'style='float:left; clear:right;'> " . $ddUnderConstruction->show() . "</div></div></div>";
                    break;
                case 'right':
                    $constructeddd = "<div id='" . $ddName . "'><div style='clear:both;overflow:auto;'>" . "<div class='ddContainer'style='float:left;clear:left;'> " . $ddUnderConstruction->show() . "</div>" .
                            "<div class='ddLabelContainer' style='float:left;clear:right;'> " . $ddLabels->show() . "</div></div></div>";
                    break;
            }
        }
        return $constructeddd;
    }

    /*!
     * \brief This member function constructs a dropdown for a the WYSIWYG form editor.
     * \note The member function uses the private data members
     * that are already initialized by the createFormElement and the insertOptionandValue member
     *  functions which should be always called first to create a form element in the database before
     * displaying it with this member function.
     * \return A constructed dropdown list form element.
     */
    private function buildWYSIWYGDropdownEntity() {

        $ddParameters = $this->objDBddEntity->listDropdownParameters($this->formNumber,$this->ddName);
        $this->objDD = new dropdown($this->ddName);
        $ddLabel=NULL;
        $ddValue=NULL;
        $ddOptionLabel=NULL;
        $defaultValue=NULL;
        $labelOrientation=NULL;
        foreach ($ddParameters as $thisDDParameter) {

            $ddValue = $thisDDParameter["ddoptionvalue"];
            $ddOptionLabel = $thisDDParameter["ddoptionlabel"];
            $defaultValue = $thisDDParameter["defaultvalue"];
            $ddLabel = $thisDDParameter["label"];
            $labelOrientation = $thisDDParameter["labelorientation"];



            $this->objDD->addOption($ddValue, $ddOptionLabel);
            if ($defaultValue == TRUE) {
                $this->objDD->setSelected($ddValue);
            }
        }
        $this->objDD->multiple = false;

        if ($ddLabel == NULL) {
            return "<div id='" . $this->ddName . "'>" . $this->objDD->show() . "</div>";
        } else {
            $ddLabels = new label($ddLabel, $this->ddName);
            switch ($labelOrientation) {
                case 'top':
                    return "<div id='" . $this->ddName . "'><div class='ddLabelContainer' style='clear:both;'> " . $ddLabels->show() . "</div>"
                    . "<div class='ddContainer'style='clear:left;'> " . $this->objDD->show() . "</div></div>";
                    break;
                case 'bottom':
                    return "<div id='" . $this->ddName . "'><div class='ddContainer'style='clear:both;'> " . $this->objDD->show() . "</div>" .
                    "<div class='ddLabelContainer' style='clear:both;'> " . $ddLabels->show() . "</div></div>";
                    break;
                case 'left':
                    return "<div id='" . $this->ddName . "'><div style='clear:both;overflow:auto;'>" . "<div class='ddLabelContainer' style='float:left;clear:left;'> " . $ddLabels->show() . "</div>"
                    . "<div class='ddContainer'style='float:left; clear:right;'> " . $this->objDD->show() . "</div></div></div>";
                    break;
                case 'right':
                    return "<div id='" . $this->ddName . "'><div style='clear:both;overflow:auto;'>" . "<div class='ddContainer'style='float:left;clear:left;'> " . $this->objDD->show() . "</div>" .
                    "<div class='ddLabelContainer' style='float:left;clear:right;'> " . $ddLabels->show() . "</div></div></div>";
                    break;
            }
        }
    }

    /*!
     * \brief This member function allows you to get a dropdown for a the WYSIWYG form editor
     * that is already saved in the database.
     * \return A constructed dropdown list.
     */
    public function showWYSIWYGDropdownEntity() {
        return $this->buildWYSIWYGDropdownEntity();
    }

}

?>
