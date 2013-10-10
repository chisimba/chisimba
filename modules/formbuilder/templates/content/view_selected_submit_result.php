<?php

/*! \file view_selected_submit_result.php
 * \brief The template file is called by the action viewSelectedSubmitResult in the controller.php
 * file called through by AJAX functions within the view_submitted_results.php template file. This template file
 * gets the content of the specfied submit result for a specified form.
 * \section sec Template Code Explanation
 * - Request the unique submit number for the desired submission store it into
 * a temporary variable.
 * - Get it form the database and spit it out.
 */
 $objSubmitResultsHandler = $this->getObject('form_submit_results_handler','formbuilder');
 echo $objSubmitResultsHandler->getParticularSubmitResult($submitNumber);

?>
