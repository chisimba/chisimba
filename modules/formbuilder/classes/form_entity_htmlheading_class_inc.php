<?php

/*!  \class form_entity_htmlheading
 *
 *  \brief This class models all the content and functionality for the
 * hmtl heading form element.
 * \brief It provides functionality to insert new html headings, create them for the
 * WYSIWYG form editor and render them in the actual construction of the form.
 * It also allows you to delete html headings from forms.
 *  \brief This is a child class that belongs to the form entity heirarchy.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
include_once 'form_entity_handler_class_inc.php';

class form_entity_htmlheading extends form_entity_handler {
    
    private $formNumber;
    /*!
     * \brief Private data member that stores a html heading object for the WYSIWYG
     * form editor.
     */
    private $objHTMLHeading;

    /*!
     * \brief This data member stores the form element identifier or ID that can
     * be used anywhere in this class.
     */
    private $headingName;

    /*!
     * \brief This data member stores the actual text for the html heading.
     */
    private $headingValue;

    /*!
     * \brief This data member stores the alignment for the html heading text either
     * top, bottom, left or right.
     */
    private $textAlignment;

    /*!
     * \brief This data member stores the sizes for the html heading text. Sizes 1
     * through 6 exist with 1 being the biggest and 6 being the smallest.
     */
    private $fontSize;

    /*!
     * \brief Private data member from the class \ref dbformbuilder_htmlheading_entity that stores all
     * the properties of this class in an usable object.
     * \note This object is used to add, get or delete html heading form elements.
     */
    protected $objDBHTMLHeadingEntity;

    /*!
     * \brief Standard constructor that loads classes for other modules and initializes
     * and instatiates private data members.
     * \note The htmlheading class is from the htmlelements module
     * inside the chisimba core modules.
     */
    public function init() {
        $this->loadClass('htmlheading', 'htmlelements');
        $this->objDBHTMLHeadingEntity = $this->getObject('dbformbuilder_htmlheading_entity', 'formbuilder');
    }

    /*!
     * \brief This member function allows you to insert a new html heading in a form with
     * a form element identifier.
     * \brief Before a new html heading gets inserted into the database,
     * duplicate entries are checked if there is another html heading
     * with the same form element identifier.
     * \param headingName A string for the form element identifier.
     * \param heading A string for the actual html heading text.
     * \param fontSize An integer. From 1-6 with 1 being the biggest.
     * \param textAlignment A string that stores the alignment for the text. Either
     * left, right or center.
     * \return A boolean value on successful storage of the html heading form element.
     */
    public function createFormElement($formNumber,$headingName, $heading, $fontSize, $textAlignment) {

        if ($this->objDBHTMLHeadingEntity->checkDuplicateHTMLheadingEntry($formNumber,$headingName) == TRUE) {
            $this->formNumber = $formNumber;
            $this->headingName = $headingName;
            $this->headingValue = $heading;
            $this->fontSize = $fontSize;
            $this->textAlignment = $textAlignment;
            $this->objDBHTMLHeadingEntity->insertSingle($formNumber,$headingName, $heading, $fontSize, $textAlignment);

            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function updateFormElement($formNumber,$headingName, $heading, $fontSize, $textAlignment){
               if ($this->objDBHTMLHeadingEntity->checkDuplicateHTMLheadingEntry($formNumber,$headingName) == FALSE) {
            $this->formNumber = $formNumber;
            $this->headingName = $headingName;
            $this->headingValue = $heading;
            $this->fontSize = $fontSize;
            $this->textAlignment = $textAlignment;
            $this->objDBHTMLHeadingEntity->updateSingle($formNumber,$headingName, $heading, $fontSize, $textAlignment);

            return TRUE;
        } else {
            return FALSE;
        } 
    }

    /*!
     * \brief This member function gets the html heading form element name if the private
     * data member headingName is set already.
     * \note This member function is not used in this module.
     * \return A string.
     */
    public function getWYSIWYGHeadingName() {
        return $this->headingName;
    }

    /*!
     * \brief This member function gets the html heading name if it has already
     * been saved in the database.
     * \param HTMLHeadingFormName A string containing the form element indentifier.
     * \return A string.
     */
    protected function getHeadingName($formNumber,$HTMLHeadingFormName) {
        $HTMLHeadingParameters = $this->objDBHTMLHeadingEntity->listHTMLHeadingParameters($formNumber,$HTMLHeadingFormName);
        $HTMLHeadingNameArray = array();
        foreach ($HTMLHeadingParameters as $thisHTMLHeadingParameter) {

//$checkboxName = $thisCheckboxParameter["checkboxname"];
            $headingName = $thisHTMLHeadingParameter["headingname"];
            $HTMLHeadingNameArray[] = $headingName;
        }
        return $HTMLHeadingNameArray;
    }

    /*!
     * \brief This member function contructs the html content for the form that
     * allows you to insert the html heading parameters to insert
     * a html heading form element.
     * \note This member function uses member functions
     * from the parent class \ref form_entity_handler to
     * construct this form.
     * \param formName A string.
     * \return A constructed html heading insert form.
     */
    public function getWYSIWYGHTMLHeadingInsertForm($formName) {
        $WYSIWYGLabelInsertForm = "<b>Text Heading HTML ID and Name Menu</b>";
        $WYSIWYGLabelInsertForm.="<div id='HeadingNameAndIDContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGLabelInsertForm.= $this->buildInsertIdForm('Heading', $formName, "70") . "";
        $WYSIWYGLabelInsertForm.= "</div>";
        $WYSIWYGLabelInsertForm.="<b>Text Heading Properties Menu</b>";
        $WYSIWYGLabelInsertForm.="<div id='HeadingPropertiesContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGLabelInsertForm.= $this->insertFontSizeForm(NULL) . "<br><br>";
        $WYSIWYGLabelInsertForm.= $this->insertTextAlignmentType(NULL) . "<br><br>";
        $WYSIWYGLabelInsertForm.= $this->insertTextForm('HTML Heading', 2, 68,NULL);
        $WYSIWYGLabelInsertForm.= "</div>";
        return $WYSIWYGLabelInsertForm;
    }
    
    public function getWYSIWYGHTMLHeadingEditForm($formNumber,$HTMLHeadingFormName){
         $HTMLHeadingParameters = $this->objDBHTMLHeadingEntity->listHTMLHeadingParameters($formNumber,$HTMLHeadingFormName);
        if (empty($HTMLHeadingParameters)) {
            return 0;
        } else {
            $headingText = "";
            $headingSize = "";
            $textAlignment = "";
            foreach ($HTMLHeadingParameters as $thisHTMLHeadingParameter) {

            //$headingName = $thisHTMLHeadingParameter["headingname"];
            $headingText = $thisHTMLHeadingParameter["heading"];
            $headingSize = $thisHTMLHeadingParameter["size"];
            $textAlignment = $thisHTMLHeadingParameter["alignment"];

        }
        $WYSIWYGLabelEditForm="<b>Edit Text Heading Properties</b>";
        $WYSIWYGLabelEditForm.="<div id='HeadingPropertiesContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
        $WYSIWYGLabelEditForm.= $this->insertFontSizeForm($headingSize) . "<br><br>";
        $WYSIWYGLabelEditForm.= $this->insertTextAlignmentType($textAlignment) . "<br><br>";
        $WYSIWYGLabelEditForm.= $this->insertTextForm('HTML Heading', 2, 68,$headingText);
        $WYSIWYGLabelEditForm.= "</div>";
        return $WYSIWYGLabelEditForm;

        }
    }

    /*!
     * \brief This member function deletes an existing html heading list form element with
     * the form element identifier.
     * \param formElementName A string that contains the
     * form element identifier.
     * \note This member function is protected so it is
     * only called by the \ref form_entity_handler class. To
     * delete a html heading or any other form element call the
     * deleteExisitngFormElement member function which will
     * automatically call this member function.
     * \return A boolean value for a successful delete.
     */
    protected function deleteHTMLHeadingEntity($formNumber,$formElementName) {
        $deleteSuccess = $this->objDBHTMLHeadingEntity->deleteFormElement($formNumber,$formElementName);
        return $deleteSuccess;
    }

    /*!
     * \brief This member function constructs a html heading for a the actual form
     * rendering from the database.
     * \param HTMLHeadingName A string that contains the
     * form element identifier.
     * \note The member function is only called by the
     * parent class member function buildForm to build a form.
     * \return A constructed html heading.
     */
    protected function constructHTMLHeadingEntity($HTMLHeadingName,$formNumber) {

        $HTMLHeadingParameters = $this->objDBHTMLHeadingEntity->listHTMLHeadingParameters($formNumber,$HTMLHeadingName);
        $constructedHTMLHeading= "";
        foreach ($HTMLHeadingParameters as $thisHTMLHeadingParameter) {

            //$headingName = $thisHTMLHeadingParameter["headingname"];
            $headingText = $thisHTMLHeadingParameter["heading"];
            $headingSize = $thisHTMLHeadingParameter["size"];
            $textAlignment = $thisHTMLHeadingParameter["alignment"];

            $HTMLHeadingUnderConstruction = new htmlheading($headingText, $headingSize);
            $HTMLHeadingUnderConstruction->align = $textAlignment;
            $currentConstructedHTMLHeading = $HTMLHeadingUnderConstruction->show();

            $constructedHTMLHeading .=$currentConstructedHTMLHeading;
        }

        return $constructedHTMLHeading;
    }

    /*!
     * \brief This member function constructs a html heading for a the WYSIWYG form editor.
     * \note The member function uses the private data members
     * that are already initialized by the createFormElement member
     *  function which should be always called first to create a form element in the database before
     * displaying it with this member function.
     * \return A constructed html heading form element.
     */
    private function buildWYSIWYGHTMLHeadingEntity() {

        $this->objHTMLHeading = new htmlheading($this->headingValue, $this->fontSize);
        $this->objHTMLHeading->align = $this->textAlignment;
        return $this->objHTMLHeading->show();
    }

    /*!
     * \brief This member function allows you to get a html heading for a the WYSIWYG form editor
     * that is already saved in the database.
     * \return A constructed html heading.
     */
    public function showWYSIWYGHTMLHeadingEntity() {
        return $this->buildWYSIWYGHTMLHeadingEntity();
    }

}

?>
