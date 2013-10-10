<?php
/* ! \file add_edit_checkbox_entity.php
 * \brief The template file is called by an AJAX function to insert a new check box element
 * into the database and produce the html content for this form element in the div WYSIWYGCheckbox
 * \section sec Explanation
 * - Request all the parameters from the post from the
  Ajax function and store them into temporary variables.
 * - Create a new form element and insert these parameters into the database.
 * - If there was a successful insertion of the new form element then construct
 * this new form element in the div WYSIWYGCheckbox so its content
 * can be passed back into WYSIWYG editor through jQuery.
 */
$formNumber = $this->getParam("formNumber");
$datePickerName = $this->getParam('datePickerName');
$datePickerValue = $this->getParam('datePickerValue');
$dateFormat = $this->getParam('dateFormat');
$defaultCustomDate = $this->getParam('defaultCustomDate');
$update = $this->getParam("update");

$objDPEntity = $this->getObject('form_entity_datepicker', 'formbuilder');
$postSuccessBoolean = 0;
$objDPEntity->createFormElement($formNumber, $datePickerName, $datePickerValue);

if (isset($update) && $update) {
    if ($objDPEntity->updateDatePickerParameters($formNumber, $datePickerName, $datePickerValue, $defaultCustomDate, $dateFormat) == TRUE) {
        $postSuccessBoolean = 1;
    } else {
        $postSuccessBoolean = 0;
    }
} else {
    if ($objDPEntity->insertDatePickerParameters($formNumber, $datePickerName, $datePickerValue, $defaultCustomDate, $dateFormat) == TRUE) {
        $postSuccessBoolean = 1;
    } else {
        $postSuccessBoolean = 0;
    }
}
?>

<div id="WYSIWYGDatepicker">
    <?php
    if ($postSuccessBoolean == 1) {
//!!!Problem Code!!!
        echo $objDPEntity->showWYSIWYGDatepickerEntity();
// $datePicker = $this->newObject('datepicker', 'htmlelements');
// $datePicker->name = 'storydate';
// //$datePicker->setName("storydate");
// $datePicker->setDateFormat("Aug-06-1996");
//  $datePicker->setDefaultDate("2010/02/02");
// echo $datePicker->show();
//       echo $postSuccessBoolean;
//
//       echo "fweljfklwejfklejflejfl;wejf";
    } else {
        echo $postSuccessBoolean;
    }
    ?>
</div>
