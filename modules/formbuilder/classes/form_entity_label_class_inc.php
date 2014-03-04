<?php

/*!  \class form_entity_label
 *
 *  \brief This class models all the content and functionality for the
 * label form element.
 * \brief It provides functionality to insert new labels, create them for the
 * WYSIWYG form editor and render them in the actual construction of the form.
 * It also allows you to delete labels from forms.
 *  \brief This is a child class that belongs to the form entity heirarchy.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
include_once 'form_entity_handler_class_inc.php';

class form_entity_label extends form_entity_handler {

    private $formNumber;
    /*!
     * \brief Private data member that stores a label object for the WYSIWYG
     * form editor.
     */
    private $objLabel;

    /*!
     * \brief This data member stores the form element identifier or ID that can
     * be used anywhere in this class.
     */
    private $LabelName;

    /*!
     * \brief This data member stores the actual text for the label.
     */
    private $labelValue;

    /*!
     * \brief This data member stores three possibilities. The label can be
     * put in a new line or a double space can be placed before it or
     * no spaces at all.
     */
    private $breakspace;

    /*!
     * \brief Private data member from the class \ref dbformbuilder_label_entity that stores all
     * the properties of this class in an usable object.
     * \note This object is used to add, get or delete html heading form elements.
     */
    protected $objDBLabelEntity;

    /*!
     * \brief Standard constructor that loads classes for other modules and initializes
     * and instatiates private data members.
     * \note The label class is from the htmlelements module
     * inside the chisimba core modules.
     */
    public function init() {
        $this->loadClass('label', 'htmlelements');
        $this->objDBLabelEntity = $this->getObject('dbformbuilder_label_entity', 'formbuilder');
    }

    /*!
     * \brief This member function allows you to insert a new label in a form with
     * a form element identifier.
     * \brief Before a new label gets inserted into the database,
     * duplicate entries are checked if there is another label
     * with the same form element identifier.
     * \param labelName A string for the form element identifier.
     * \param A string for the actual label text.
     * \param breakSpace An string. A string that stores the space type before
     * the label. Either new line, double space or no spaces before
     * the label.
     * \return A boolean value on successful storage of the label form element.
     */
    public function createFormElement($formNumber,$labelName, $label, $breakSpace) {

        if ($this->objDBLabelEntity->checkDuplicateLabelEntry($formNumber,$labelName) == TRUE) {
            $this->formNumber = $formNumber;
            $this->LabelName = $labelName;
            $this->labelValue = $label;
            $this->breakspace = $breakSpace;
            $this->objDBLabelEntity->insertSingle($formNumber,$labelName, $label, $breakSpace);

            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function updateFormElement($formNumber,$labelName, $label, $breakSpace){
       if ($this->objDBLabelEntity->checkDuplicateLabelEntry($formNumber,$labelName) == FALSE) {
            $this->formNumber = $formNumber;
            $this->LabelName = $labelName;
            $this->labelValue = $label;
            $this->breakspace = $breakSpace;
            
            $this->objDBLabelEntity->updateSingle($formNumber,$labelName, $label, $breakSpace);

            return TRUE;    
        } else {
            return FALSE;
        } 
    }

    /*!
     * \brief This member function contructs the html content for the form that
     * allows you to insert the label parameters to insert
     * a label form element.
     * \note This member function uses member functions
     * from the parent class \ref form_entity_handler to
     * construct this form.
     * \param formName A string.
     * \return A constructed label insert form.
     */
    public function getWYSIWYGLabelInsertForm($formName) {
        $WYSIWYGLabelInsertForm = "";
        $WYSIWYGLabelInsertForm.="<b>Label HTML ID and Name Menu</b>";
        $WYSIWYGLabelInsertForm.="<div id='labelNameAndIDContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGLabelInsertForm.= $this->buildInsertIdForm('label', $formName, "70") . "<br>";
        $WYSIWYGLabelInsertForm.="</div>";
        $WYSIWYGLabelInsertForm.="<b>Label Properties Menu</b>";
        $WYSIWYGLabelInsertForm.="<div id='labelSizeContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGLabelInsertForm.= $this->buildLayoutForm('label', $formName, "label",NULL) . "<br><br>";
        $WYSIWYGLabelInsertForm.= $this->insertTextForm('label', 2, 68,NULL);
        $WYSIWYGLabelInsertForm.="</div>";
        return $WYSIWYGLabelInsertForm;
    }
    
    public function getWYSIWYGLabelEditForm($formNumber,$formElementName){
        $labelParameters = $this->objDBLabelEntity->listLabelParameters($formNumber,$formElementName);
        if (empty($labelParameters)) {
            return 0;
        } else {
            $labelFormName = "";
            $labelText = "";
            $labelBreakspace = "";
            
            foreach ($labelParameters as $thislabelParameter) {

            $labelFormName = $thislabelParameter["labelname"];
            $labelText = $thislabelParameter["label"];
            $labelBreakspace = $thislabelParameter["breakspace"];
        } 
        
        $WYSIWYGLabelEditForm = "";
        $WYSIWYGLabelEditForm.="<b>Edit Label Properties</b>";
        $WYSIWYGLabelEditForm.="<div id='labelSizeContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGLabelEditForm.= $this->buildLayoutForm('label', $labelFormName, "label",$labelBreakspace) . "<br><br>";
        $WYSIWYGLabelEditForm.= $this->insertTextForm('label', 2, 68,$labelText);
        $WYSIWYGLabelEditForm.="</div>";
        
        }
        return $WYSIWYGLabelEditForm;

     
    }

    /*!
     * \brief This member function gets the label form element name if the private
     * data member headingName is set already.
     * \note This member function is not used in this module.
     * \return A string.
     */
    public function getWYSIWYGLabelName() {
        return $this->LabelName;
    }

    /*!
     * \brief This member function constructs a label for a the actual form
     * rendering from the database.
     * \param labelName A string that contains the
     * form element identifier.
     * \note The member function is only called by the
     * parent class member function buildForm to build a form.
     * \return A constructed label.
     */
    protected function constructLabelEntity($labelName,$formNumber) {

        $labelParameters = $this->objDBLabelEntity->listLabelParameters($formNumber,$labelName);
        $constructedLabel = "";
        foreach ($labelParameters as $thislabelParameter) {

//            $labelFormName = $thislabelParameter["labelname"];
            $labelText = $thislabelParameter["label"];
            $labelBreakspace = $thislabelParameter["breakspace"];


            $labelUnderConstruction = new label($labelText, NULL);
            $currentConstructedLabel = $labelUnderConstruction->show() . $this->getBreakSpaceType($labelBreakspace);
            $constructedLabel .=$currentConstructedLabel;
        }
        return $constructedLabel;
    }

    /*!
     * \brief This member function deletes an existing label form element with
     * the form element identifier.
     * \param formElementName A string that contains the
     * form element identifier.
     * \note This member function is protected so it is
     * only called by the \ref form_entity_handler class. To
     * delete a label or any other form element call the
     * deleteExisitngFormElement member function which will
     * automatically call this member function.
     * \return A boolean value for a successful delete.
     */
    protected function deleteLabelEntity($formNumber,$formElementName) {
        $deleteSuccess = $this->objDBLabelEntity->deleteFormElement($formNumber,$formElementName);
        return $deleteSuccess;
    }

    /*!
     * \brief This member function constructs a label for a the WYSIWYG form editor.
     * \note The member function uses the private data members
     * that are already initialized by the createFormElement member
     *  function which should be always called first to create a form element in the database before
     * displaying it with this member function.
     * \return A constructed a label form element.
     */
    private function buildWYSIWYGLabelEntity() {
        $this->objLabel = new label($this->labelValue, NULL);
        return $this->getBreakSpaceType($this->breakspace) . $this->objLabel->show();
    }

    /*!
     * \brief This member function allows you to get a label for a the WYSIWYG form editor
     * that is already saved in the database.
     * \return A constructed label.
     */
    public function showWYSIWYGLabelEntity() {
        return $this->buildWYSIWYGLabelEntity();
    }

}

?>
