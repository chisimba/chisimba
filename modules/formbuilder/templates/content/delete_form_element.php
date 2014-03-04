<?php

/*! \file delete_form_element.php
 * \brief The template file is called by an action deleteFormElement which is
 * called by the AJAX functions inside the form_editor.php template file.
 * \brief This template file deletes a speciifc form element with a form element
 * identifier inside a particular form with a form number.
 * \section sec Template Code Explanation
 * - Request the form number and the form element identifier of the desired form
 * from the post from the Ajax function and store them into temporary variables.
 * - Delete the desired form element.
 * - Spit out a success boolean to see if it is successful or not.
*/
$formElementName = $this->getParam('formElementName',NULL);
$formNumber = $this->getParam('formNumber',NULL);

if (isset($formElementName))
{
$objFormEntityHandler = $this->getObject('form_entity_handler','formbuilder');
echo $objFormEntityHandler->deleteExisitngFormElement($formElementName,$formNumber);
}
?>

