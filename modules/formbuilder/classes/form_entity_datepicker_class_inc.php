<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!  \class form_entity_datepicker
 *
 *  \brief This class models all the content and functionality for the
 * datepicker form element.
 * \brief It provides functionality to insert new datepicker objects, create them for the
 * WYSIWYG form editor and render them in the actual construction of the form.
 * It also allows you to delete datepicker objects from forms.
      * \bug The member function buildWYSIWYGDatepickerEntity is called by through by AJAX.
  * There is a bug in the datepicker class inside the htmlelements module. Chisimba craps out
     * when you insert a datepicker object throught ajax. The chisimba community will
 * have to fix this grave problem. For now this member function cannot be used.
 *  \brief This is a child class that belongs to the form entity heirarchy.
 *  \author Salman Noor
 *  \author CNS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 1.00
 *  \date    November 3, 2010
 */
include_once 'form_entity_handler_class_inc.php';

class form_entity_datepicker extends form_entity_handler
{
 
    private $formNumber;
        /*!
     * \brief Private data member that stores a datepicker object for the WYSIWYG
     * form editor.
     */
    private $objDP;

        /*!
     * \brief This data member stores the form element identifier or ID that can
     * be used anywhere in this class.
     */
    private $dpName;

        /*!
     * \brief This data member stores the html name of the checkbox.
     */
        private $dpValue;

                /*!
     * \brief This data member stores a set date as a default or a real time
                 * date as default for the datepicker object.
     */
        private $defaultDate;

                        /*!
     * \brief This data member stores in what format the date is stored when
                         * it is selected and submitted by the user of the form.
     */
        private $dpDateFormat;

    /*!
     * \brief Private data member from the class \ref dbformbuilder_datepicker_entity that stores all
     * the properties of this class in an usable object.
     * \note This object is used to add, get or delete datepicker form elements.
     */
    protected  $objDBdpEntity;
  
    /*!
     * \brief Standard constructor that loads classes for other modules and initializes
     * and instatiates private data members.
     * \note The datepicker class is from the htmlelements module
     * inside the chisimba core modules.
     */
    public function  init()
    {
        $this->loadClass('datepicker','htmlelements');
        $this->dpName = NULL;
        $this->dpValue=NULL;
        $this->dpDateFormat=NULL;
        $this->defaultDate=NULL;
        $this->formNumber=NULL;
        $this->objDBdpEntity = $this->getObject('dbformbuilder_datepicker_entity','formbuilder');
                }

    /*!
     * \brief This member function initializes the private data members for the
     * datepicker object.
     * \parm elementName A string for the form element identifier.
     * \pram elementValue A string for the html name for the datepicker object.
     */
    public function createFormElement($formNumber,$elementName="",$elementValue="")
    {
        $this->formNumber = $formNumber;
        $this->dpName = $elementName;
        $this->dpValue = $elementValue;
        $this->objDP = $this->newObject('datepicker', 'htmlelements'); 
    }

        /*!
     * \brief This member function gets the datepicker form element name if the private
     * data member dpName is set already.
     * \note This member function is not used in this module.
     * \return A string.
     */
public function getWYSIWYGDatePickerName()
{
    return $this->dpName;
}

    /*!
     * \brief This member function gets the datepicker name if it has already
     * been saved in the database.
     * \param dpFormName A string containing the form element indentifier.
     * \return A string.
     */
protected function getDatePickerName($formNumber,$dpFormName)
{
$dpParameters = $this->objDBdpEntity->listDatePickerParameters($formNumber,$dpFormName);
 $dpNameArray= array();
  foreach($dpParameters as $thisDPParameter){
 $dpName = $thisDPParameter["datepickervalue"];
 $dpNameArray[]= $dpName;
  }
  return $dpNameArray;
}

        /*!
     * \brief This member function gets the datepicker html name if the private
     * data member dpValue is set already.
     * \note This member function is not used in this module.
     * \return A string.
     */
public function getDatePickerValue()
{
    return $this->dpValue;
}

    /*!
     * \brief This member function contructs the html content for the form that
     * allows you to insert the datepicker object parameters to insert
     * a datepicker object form element.
     * \note This member function uses member functions
     * from the parent class \ref form_entity_handler to
     * construct this form.
     * \param formName A string.
     * \return A constructed datepicker insert form.
     */
public function getWYSIWYGDatePickerInsertForm($formName)
    {
  $WYSIWYGDatePickerInsertForm="<b>Date Picker HTML ID and Name Menu</b>";
  $WYSIWYGDatePickerInsertForm.="<div id='dpIDContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
  $WYSIWYGDatePickerInsertForm.= $this->buildInsertIdForm('datepicker',$formName,"70")."<br>";
  $WYSIWYGDatePickerInsertForm.= $this->buildInsertFormElementNameForm('datepicker', "70",NULL)."<br>";
    $WYSIWYGDatePickerInsertForm.= "</div>";
    $WYSIWYGDatePickerInsertForm.="<b>Date Picker Date Settings</b>";
      $WYSIWYGDatePickerInsertForm.="<div id='dpPropertiesContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> " ;
  $WYSIWYGDatePickerInsertForm.= $this->insertDatePickerFormParameters(NULL,NULL);
  $WYSIWYGDatePickerInsertForm.= "</div>";
           return   $WYSIWYGDatePickerInsertForm;
    }
    
    public function getWYSIWYGDatePickerEditForm($formNumber, $formElementName) {
        $dpParameters = $this->objDBdpEntity->listDatePickerParameters($formNumber, $formElementName);
        if (empty($dpParameters)) {
            return 0;
        } else {
            $dpValue = "";
            $defaultDate = "";
            $dateFormat = "";
            foreach ($dpParameters as $thisDPParameter) {
                //$dpName = $thisDPParameter["datepickername"];
                $dpValue = $thisDPParameter["datepickervalue"];
                $defaultDate = $thisDPParameter["defaultdate"];
                $dateFormat = $thisDPParameter["dateformat"];
            }


            $WYSIWYGDatePickerEditForm = "<b>Edit Date Picker Settings</b>";
            $WYSIWYGDatePickerEditForm.="<div id='dpPropertiesContainer' class='ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;padding:10px 15px 10px 15px;margin:0px 0px 10px 0px;'> ";
            $WYSIWYGDatePickerEditForm .="<input type='hidden' name='uniqueFormElementName' id='uniqueFormElementName' value=" . $dpValue . " />";
            $WYSIWYGDatePickerEditForm.= $this->insertDatePickerFormParameters($dateFormat, $defaultDate);
            $WYSIWYGDatePickerEditForm.= "</div>";
            return $WYSIWYGDatePickerEditForm;
        }
    }

    /*!
     * \brief This member function allows you to insert a new datepicker in a form with
     * a form element identifier.
     * \brief Before a new datepicker gets inserted into the database,
     * duplicate entries are checked if there is another datepicker
     * in this same form with the same form element identifier.
     * \param formElementName A string for the form element identifier.
     * \param formElementValue A string for the actual html name for the datepicker.
     * \param defaultDate A string that stores a set date or a real time date set
                         * as a default.
     * \param dateFormat A string to choose the format the date is saved.
     * \return A boolean value on successful storage of the datepicker form element.
     */
public function insertDatePickerParameters($formNumber,$formElementName,$formElementValue,$defaultDate,$dateFormat)
{

    if ($this->objDBdpEntity->checkDuplicateDatepickerEntry($formNumber,$formElementName,$formElementValue) == TRUE)
    {
        $this->objDBdpEntity->insertSingle($formNumber,$formElementName,$formElementValue,$defaultDate,$dateFormat);
        $this->formNumber = $formNumber;
        $this->dpName = $formElementName;
        $this->dpValue = $formElementValue;
        $this->defaultDate=$defaultDate;
        $this->dpDateFormat=$dateFormat;

    return TRUE;
    }
 else {
        return FALSE;
    }
}

public function updateDatePickerParameters($formNumber, $formElementName, $datePickerValue, $defaultCustomDate, $dateFormat){
      if ($this->objDBdpEntity->checkDuplicateDatepickerEntry($formNumber,$formElementName,$datePickerValue) == FALSE)
    {
        $this->objDBdpEntity->updateSingle($formNumber,$formElementName,$datePickerValue,$defaultCustomDate,$dateFormat);
        $this->formNumber = $formNumber;
        $this->dpName = $formElementName;
        $this->dpValue = $datePickerValue;
        $this->defaultDate=$defaultCustomDate;
        $this->dpDateFormat=$dateFormat;

    return TRUE;
    }
 else {
        return FALSE;
    }  
}

    /*!
     * \brief This member function deletes an existing datepicker object form element with
     * the form element identifier.
     * \param formElementName A string that contains the
     * form element identifier.
     * \note This member function is protected so it is
     * only called by the \ref form_entity_handler class. To
     * delete a datepicker or any other form element call the
     * deleteExisitngFormElement member function which will
     * automatically call this member function.
     * \return A boolean value for a successful delete.
     */
protected function deleteDatePickerEntity($formNumber,$formElementName)
{
    $deleteSuccess = $this->objDBdpEntity->deleteFormElement($formNumber,$formElementName);
    return $deleteSuccess;
}

    /*!
     * \brief This member function constructs a datepicker for a the actual form
     * rendering from the database.
     * \param dpName A string that contains the
     * form element identifier.
     * \note The member function is only called by the
     * parent class member function buildForm to build a form.
     * \return A constructed datepicker object.
     */
protected function constructDatePickerEntity($dpName,$formNumber)
{

$dpParameters = $this->objDBdpEntity->listDatePickerParameters($formNumber,$dpName);
$constructedDatePicker = "";
  foreach($dpParameters as $thisDPParameter){
 $dpName = $thisDPParameter["datepickername"];
 $dpValue = $thisDPParameter["datepickervalue"];
 $defaultDate = $thisDPParameter["defaultdate"];
$dateFormat = $thisDPParameter["dateformat"];
 $datePicker = $this->newObject('datepicker', 'htmlelements');
 $datePicker->name = $dpValue;
 if ($defaultDate != "Real Time Date")
 {
 $datePicker->setDefaultDate($defaultDate);
 }
 $datePicker->setDateFormat($dateFormat);
$currentConstructedDatePicker = $datePicker->show();
$constructedDatePicker .= $currentConstructedDatePicker;
  }


 return $constructedDatePicker;
             


}

    /*!
     * \brief This member function constructs a datepicker for a the WYSIWYG form editor.
     * \note The member function uses the private data members
     * that are already initialized by the createFormElement and the insertDatePickerParameters member
     *  functions which should be always called first to create a form element in the database before
     * displaying it with this member function.
     * \warning This member function is call by through by AJAX. There is a bug
     * in the datepicker class inside the htmlelements module. Chisimba craps out
     * when you insert a datepicker object throught ajax. For now, dont use this
     * member function.
     * \return A constructed datepicker form element.
     */
public function buildWYSIWYGDatepickerEntity()
{

//$dpParameters = $this->objDBdpEntity->listDatePickerParameters($this->dpName);

$this->objDP->name = $this->dpValue;
 if ($this->defaultDate != "Real Time Date")
 {
$this->objDP->setDefaultDate($this->defaultDate);
 }

$this->objDP->setDateFormat($this->dpDateFormat);
return $this->objDP->show();

//        $this->defaultDate=$defaultDate;
//        $this->dpDateFormat=$dateFormat;
//  foreach($dpParameters as $thisDPParameter){
//   //Store the values of the array in variables
//
//   $dpName = $thisDDParameter["datepickername"];
//   $dpValue = $thisDDParameter["datepickervalue"];
//   $defaultDate = $thisDDParameter["defaultdate"];
//   $dateFormat = $thisDDParameter["dateFormat"];
//


 // }

}

    /*!
     * \brief This member function allows you to get a datepicker for a the WYSIWYG form editor
     * that is already saved in the database.
     * \warning As per the bug, this member function cannot be used for now. You
     * will have to comment out the one line to make this member function to work.
     * \return The form element identifier.
     */
public function showWYSIWYGDatepickerEntity()
{
    return $this->dpName;
   // return $this->buildWYSIWYGDatepickerEntity();
}




}
?>
