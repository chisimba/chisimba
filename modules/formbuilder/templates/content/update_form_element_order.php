<?php
/*! \file update_form_element_order.php
 * \brief The template file is called by the action updateFormElementOrder in the controller.php
 * file called through by AJAX functions within the form_editor.php template file. This template file
 * gets the form element order of specific form with a form number and updates them to
 * specified order in the database.
 * \section sec Template Code Explanation
 * - Request the form number store it into
 * a temporary variable.
 * - Request the form element order string seperated by commas of the desired order
 * of the form elements and store it into a temporary variable.
 * -Convert the string seperated by commas into an array.
 * -Update the form element order for that form.
 */
$formElementOrderString = $this->getParam('formElementOrderString',NULL);
$formNumber = $this->getParam('formNumber',NULL);

if (isset($formElementOrderString))
{
$objFormEntityHandler = $this->getObject('form_entity_handler','formbuilder');
$formElementOrderArray = explode(",", $formElementOrderString);
$objFormEntityHandler->updateExistingFormElementOrder($formElementOrderArray,$formNumber);
}
?>
