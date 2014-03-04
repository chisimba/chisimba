<?php

/*! \file delete_form.php
 * \brief The template file is called by an action deleteForm which is
 * called by the AJAX functions inside the list_all_forms.php template file.
 * \brief This template file deletes all form contents for a chosen form which
 * includes actual form element content, publishing data, metadata and submission
 * results ie Everything.
 * \section sec Template Code Explanation
 * - Request the form number of the desired form from the post from the
Ajax function and store it into a temporary variable.
 * - Delete the form element content, publishing data and metadata.
 * - Delete all the submission results for this form.
 * \note There is very little error handling here. There is some error handliing inside
 * the deleteForm member function inside the \ref form_entity_handler class but
 * it is not being used fruitfully.
*/

$formNumber = $this->getParam('formNumber',NULL);

if (isset($formNumber))
{

$objFormEntityHandler = $this->getObject('form_entity_handler','formbuilder');
echo $objFormEntityHandler->deleteForm($formNumber);
$objDBFormSubmitResults = $this->getObject('dbformbuilder_submit_results','formbuilder');
$objDBFormSubmitResults->deleteAllSubmissions($formNumber);
}




?>
