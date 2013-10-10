<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!  \class form_entity_button
 *
 *  \brief This class models all the content and functionality for the
 * button form element.
 * \brief It provides functionality to insert new buttons, create them for the
 * WYSIWYG form editor and render them in the actual construction of the form.
 * It also allows you to delete buttons from forms.
 *  \brief This is a child class that belongs to the form entity heirarchy.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
include_once 'form_entity_handler_class_inc.php';

class form_entity_button extends form_entity_handler {
    
    private $formnumber;
    /*!
     * \brief Private data member that stores a button object for the WYSIWYG
     * form editor.
     */
    private $objButton;

    /*!
     * \brief This data member stores the form element identifier or ID that can
     * be used anywhere in this class.
     */
    private $buttonFormName;

    /*!
     * \brief This data member stores the html name of the button.
     */
    private $buttonName;

    /*!
     * \brief This data member stores the label for the button
     */
    private $buttonLabel;

    /*!
     * \brief This data member stores a string to determine whether the button
     * object is a submit or reset button.
     */
    private $isSetOrResetChoice;

        /*!
     * \brief Private data member from the class \ref dbformbuilder_button_entity that stores all
     * the properties of this class in an usable object.
     * \note This object is used to add, get or delete button form elements.
     */
    private $objDBbuttonEntity;

    /*!
     * \brief Standard constructor that loads classes for other modules and initializes
     * and instatiates private data members.
     * \note The button class are from the htmlelements module
     * inside the chisimba core modules.
     */
    public function init() {
        $this->loadClass('button', 'htmlelements');
        $this->objDBbuttonEntity = $this->getObject('dbformbuilder_button_entity', 'formbuilder');
        $this->buttonFormName = NULL;
        $this->buttonName = NULL;
        $this->buttonLabel = NULL;
        $this->isSetOrResetChoice = NULL;
        $this->formnumber = NULL;
    }
    
    public function setInitParams($formElementName,$formNumber){
        $this->buttonFormName=$formElementName;
        $this->formnumber=$formNumber;
    }

    /*!
     * \brief This member function allows you to insert a new button in a form with
     * a form element identifier.
     * \brief Before a new button gets inserted into the database,
     * duplicate entries are checked if there is another button
     * with the same form element identifier.
     * \param buttonFormName A string for the form element identifier.
     * \param buttonName A string for the actual html name for the button.
     * \param buttonLabel A string.
     * \param isSetToResetOrSubmit A string. Two possibilties exist,either
     * submit or reset.
     * \return A boolean value on succesful storage of the button form element.
     */
    public function createFormElement($formNumber, $buttonFormName, $buttonName, $buttonLabel, $isSetToResetOrSubmit) {

        if ($this->objDBbuttonEntity->checkDuplicateButtonEntry($formNumber,$buttonFormName, $buttonName) == TRUE) {
            $this->formnumber = $formNumber;
            $this->buttonFormName = $buttonFormName;
            $this->buttonName = $buttonName;
            $this->buttonLabel = $buttonLabel;
            $this->isSetOrResetChoice = $isSetToResetOrSubmit;
            $this->objDBbuttonEntity->insertSingle($formNumber,$buttonFormName, $buttonName, $buttonLabel, $isSetToResetOrSubmit);
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function updateFormElement($optionID,$formNumber,$formElementName, $buttonName, $buttonLabel, $isSetToResetOrSubmit) {
              if ($this->objDBbuttonEntity->checkIfButtonExists($optionID) == TRUE) {
            $this->objDBbuttonEntity->updateSingle($optionID,$formNumber,$formElementName, $buttonName, $buttonLabel, $isSetToResetOrSubmit);
            $this->formnumber = $formNumber;
            $this->buttonFormName = $formElementName;
            $this->buttonName = $buttonName;
            $this->buttonLabel = $buttonLabel;
            $this->isSetOrResetChoice = $isSetToResetOrSubmit;
            
            return TRUE;
        } else {
            return FALSE;
        }  
    }

    /*!
     * \brief This member function gets the button name if the private
     * data member buttonName is set already.
     * \note This member function is not used in this module.
     * \return A string.
     */
    public function getWYSIWYGButtonName() {
        return $this->buttonName;
    }

    /*!
     * \brief This member function contructs the html content for the form that
     * allows you to insert the button parameters to insert
     * a button form element.
     * \note This member function uses member functions
     * from the parent class \ref form_entity_handler to
     * construct this form.
     * \param formName A string.
     * \return A constructed button insert form.
     */
    public function getWYSIWYGButtonInsertForm($formName) {
        $WYSIWYGButtonInsertForm = "<b>Button HTML ID and Name Menu</b>";
        $WYSIWYGButtonInsertForm.="<div id='labelNameAndIDContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGButtonInsertForm.= $this->buildInsertIdForm('button', $formName, "70") . "<br>";
        $WYSIWYGButtonInsertForm.= $this->buildInsertFormElementNameForm('button', "70",NULL) . "<br>";
        $WYSIWYGButtonInsertForm.="</div>";
        $WYSIWYGButtonInsertForm.="<b>Button Properties Menu</b>";
        $WYSIWYGButtonInsertForm.="<div id='buttonPropertiesContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGButtonInsertForm.= $this->insertButtonParametersForm() . "<br>";
        $WYSIWYGButtonInsertForm.="</div>";
        return $WYSIWYGButtonInsertForm;
    }
    
    public function getWYSIWYGButtonEditForm($formNumber,$formElementName){
        
      $buttonParameters = $this->objDBbuttonEntity->listButtonParameters($formNumber,$formElementName);
        if (empty($buttonParameters)) {
            return 0;
        } else {
          $WYSIWYGButtonEditForm="<div id='editFormElementTabs'>	
         <ul>
		<li><a href='#editFormElementPropertiesContainer'>Edit Button Group Properties</a></li>
		<li><a href='#editFormElementOptionsContainer'>Edit Individual Button Properties</a></li>
	</ul>";
        $WYSIWYGButtonEditForm.= "<div id='editFormElementPropertiesContainer'>";
        
        $WYSIWYGButtonEditForm.= "<b>There are no group button properties to edit. Individual button properties can be edited.</b>";

        $WYSIWYGButtonEditForm.= "</div>";
        
        $WYSIWYGButtonEditForm.= "<div id='editFormElementOptionsContainer'>";
        $WYSIWYGButtonEditForm.= "<b>Edit Individual Buttons</b>";
        $WYSIWYGButtonEditForm.="<div id='buttonOptionsContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
                
        $WYSIWYGButtonEditForm.="<style>
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
        
.buttonValueContainer{
width: 180px;
float:left;
}

.buttonLabelContainer{
width: 180px;
float:left;
}

.buttonTypeContainer{
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
        
        
                $WYSIWYGButtonEditForm.= "<div class='formOptionsListContainer'>";
                $WYSIWYGButtonEditForm.= "<div class='singleOptionContainer' id='optionTitle'>";
                $WYSIWYGButtonEditForm.= "<div class='buttonValueContainer'><b>Button Name</b></div>";
                $WYSIWYGButtonEditForm.= "<div class='buttonLabelContainer'><b>Button Label</b></div>";
//                $WYSIWYGDropDownEditForm.= "<div class='optionBreakSpaceContainer'><b>Break Space</b></div>";
                $WYSIWYGButtonEditForm.= "<div class='buttonTypeContainer'><b>Button Type</b></div>"; 
                $WYSIWYGButtonEditForm.= "</div>";
      

                        foreach ($buttonParameters as $thisbuttonParameter) {

//$checkboxName = $thisCheckboxParameter["checkboxname"];
//$buttonFormName = $thisbuttonParameter["buttonformname"];
            $buttonName = $thisbuttonParameter["buttonname"];
            $buttonLabel = $thisbuttonParameter["buttonlabel"];
            $isSetToResetOrSubmit = $thisbuttonParameter["issettoresetorsubmit"];
            $id = $thisbuttonParameter["id"];
                $WYSIWYGButtonEditForm.= "<div class='singleOptionContainer' id='option".$optionNumber."' optionID='".$id."' formNumber='".$formNumber."' formElementName='".$formElementName."' buttonName='".$buttonName."' buttonLabel='".$buttonLabel."' buttonType='".$isSetToResetOrSubmit."'>";
                $WYSIWYGButtonEditForm.= "<div class='buttonValueContainer'>".$buttonName."</div>";
                $WYSIWYGButtonEditForm.= "<div class='buttonLabelContainer'>".$buttonLabel."</div>";
                if ($isSetToResetOrSubmit == 'submit'){
                   $WYSIWYGButtonEditForm.= "<div class='buttonTypeContainer'>submit</div>";   
                }else{
                    $WYSIWYGButtonEditForm.= "<div class='buttonTypeContainer'>reset</div>";  
                }
                $WYSIWYGButtonEditForm.= "<a class='deleteOptionLink' href='#delete'>Delete</a>";
                $WYSIWYGButtonEditForm.= "<a class='editOptionLink' href='#edit'>Edit</a>";
                $WYSIWYGButtonEditForm.= "</div>";
                $optionNumber++;
        }
                   
        $WYSIWYGButtonEditForm.= "</div>";
        $WYSIWYGButtonEditForm.= "</div>";
        
        $WYSIWYGButtonEditForm.= "</div>";
        $WYSIWYGButtonEditForm.= "</div>";
        
        
         $WYSIWYGButtonEditForm.= "<style>
            
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
        $WYSIWYGButtonEditForm.="<div class='formElementOptionsFormSuperContainer'>";
        $WYSIWYGButtonEditForm.= "<div class='editFormElementOptionsSideSeperator'></div>";
        $WYSIWYGButtonEditForm.="<div class='editFormElementOptionsHeadingSpacer'><div class='formElementOptionUpdateHeading'>Update Single Button</div></div>";
        $WYSIWYGButtonEditForm.= "<div class='editFormElementFormContainer'>";
        
        
        $WYSIWYGButtonEditForm .= "<b>Button Name Menu</b>";
        $WYSIWYGButtonEditForm.="<div id='labelNameAndIDContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGButtonEditForm.= $this->buildInsertFormElementNameForm('button', "70",NULL) . "<br>";
        $WYSIWYGButtonEditForm.="</div>";
        $WYSIWYGButtonEditForm.="<b>Button Properties Menu</b>";
        $WYSIWYGButtonEditForm.="<div id='buttonPropertiesContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGButtonEditForm.= $this->insertButtonParametersForm() . "<br>";
        $WYSIWYGButtonEditForm.="</div>";
        $WYSIWYGButtonEditForm.= "<div class='formElementOptionsFormButtonsContainer'></div>";
        $WYSIWYGButtonEditForm.= "</div>";
        $WYSIWYGButtonEditForm.= "</div>";
        return $WYSIWYGButtonEditForm;     
        
        
        }
    }

    /*!
     * \brief This member function deletes an existing button form element with
     * the form element identifier.
     * \param formElementName A string that contains the
     * form element identifier.
     * \note This member function is protected so it is
     * only called by the \ref form_entity_handler class. To
     * delete a button or any other form element call the
     * deleteExisitngFormElement member function which will
     * automatically call this member function.
     * \return A boolean value for a successful delete.
     */
    protected function deleteButtonEntity($formNumber,$formElementName) {
        $deleteSuccess = $this->objDBbuttonEntity->deleteFormElement($formNumber,$formElementName);
        return $deleteSuccess;
    }

    /*!
     * \brief This member function constructs a button for a the actual form
     * rendering from the database.
     * \param buttonFormName A string that contains the
     * form element identifier.
     * \note The member function is only called by the
     * parent class member function buildForm to build a form.
     * \return A constructed button.
     */
    protected function constructButtonEntity($buttonFormName,$formNumber) {

        $buttonParameters = $this->objDBbuttonEntity->listButtonParameters($formNumber,$buttonFormName);

$constructedButton="";
        foreach ($buttonParameters as $thisbuttonParameter) {

//$checkboxName = $thisCheckboxParameter["checkboxname"];
//$buttonFormName = $thisbuttonParameter["buttonformname"];
            $buttonName = $thisbuttonParameter["buttonname"];
            $buttonLabel = $thisbuttonParameter["buttonlabel"];
            $isSetToResetOrSubmit = $thisbuttonParameter["issettoresetorsubmit"];

            $buttonUnderConstuction = new button($buttonName);
            $buttonUnderConstuction->setValue($buttonLabel);
            if ($isSetToResetOrSubmit == "reset") {
                $buttonUnderConstuction->setToReset();
            } else {
                $buttonUnderConstuction->setToSubmit();
            }
            $currentConstructedButton = $buttonUnderConstuction->show();
            $constructedButton .=$currentConstructedButton;
        }

        return $constructedButton;
    }

    /*!
     * \brief This member function constructs a button for a the WYSIWYG form editor.
     * \note The member function uses the private data members
     * that are already initialized by the createFormElement member function which
     * should be always called first to create a form element in the database before
     * displaying it with this member function.
     * \return A constructed button.
     */
    private function buildWYSIWYGButtonEntity() {
        return $this->constructButtonEntity($this->buttonFormName,$this->formnumber);
//        $this->objButton = new button($this->buttonName);
//        $this->objButton->setValue($this->buttonLabel);
//
//
//        if ($this->isSetOrResetChoice == "submit") {
//            $this->objButton->setToSubmit();  //If you want to make the button a submit button
//        } else {
//            $this->objButton->setToReset();
//        }
//        return $this->objButton->show();
    }

    /*!
     * \brief This member function allows you to get a button for a the WYSIWYG form editor
     * that is already saved in the database.
     * \return A constructed button.
     */
    public function showWYSIWYGButtonEntity() {
        return $this->buildWYSIWYGButtonEntity();
    }

}

?>
