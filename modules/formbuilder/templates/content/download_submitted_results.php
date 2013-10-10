<?php

/*! \file download_submitted_results.php
 * \brief The template file is called by an action downloadCSVSubmitResultsFile which is
 * called by the AJAX functions inside the view_submitted_results.php template file.
 * \brief This template file constructs the content for all the submitted results
 * for that particular form in CSV format and writes it into a CSV file in the
 * resources folder.
 * \note This template does not actually give the functionality to download the
 * CSV file. That is done in the javascript but it constructs the download content
 * in thas case which is a CSV file.
 * \section sec Template Code Explanation
 * - Request the form number of the desired form
 * from the post from the Ajax function and store it into a temporary variable. Note
 * this code for getting the form number is done in the controller of the action
 * that called this template file.
 * - Construct the CSV file.
 * - Spit out a failure boolean if any failures occured.
*/

 $objSubmitResultsHandler = $this->getObject('form_submit_results_handler','formbuilder');
echo ($objSubmitResultsHandler->downloadCSVSubmitResultsFile($formNumber));


 ?>
