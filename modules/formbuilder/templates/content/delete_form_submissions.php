<?php

/*! \file delete_form_submissions.php
 * \brief The template file is called by an action deleteAllFormSubmissions which is
 * called by the AJAX functions inside the list_all_forms.php template file.
 * \brief This template file deletes all submission results saved to the database for a
 * particualar form with a form number.
 * \section sec Template Code Explanation
 * - Request the form number of the desired form
 * from the post from the Ajax function and store it into a temporary variable.
 * - Delete all submission results for that form number.
 * - Spit out a success boolean to see if it is successful or not.
*/

$formNumber = $this->getParam('formNumber',NULL);

if (isset($formNumber))
{
$objDBFormSubmitResults = $this->getObject('dbformbuilder_submit_results','formbuilder');
echo $objDBFormSubmitResults->deleteAllSubmissions($formNumber);

}
?>
