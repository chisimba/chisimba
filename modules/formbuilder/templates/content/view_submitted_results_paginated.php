<?php

/*! \file view_submitted_rsults_paginated.php
 * \brief The template file is called by the action viewSubmittedResultsPaginated in the controller.php
 * file called through by AJAX functions within the view_submitted_results.php template file. This template file
 * gets more submit results metadata content for pagination.
 * \section sec Template Code Explanation
 * - Request the pagination request number, the batch size and the latest or all results choice and store them into
 * temporary variables.
 * - If the lastest value is selected then get all latest the results with batch size
 * specified and the pagination request number. Otherwise only get all the entries.
 * - Spit out the results.
 */

$paginationRequestNumber = $this->getParam('paginationRequestNumber', NULL);
$numberOfEntriesInPaginationBatch = $this->getParam('numberOfEntriesInPaginationBatch', NULL);
$latestOrAllResults = $this->getParam('latestOrAllResults', NULL);
$formNumber = $this->getParam('formNumber', NULL);
if (isset($paginationRequestNumber)) {
    $objSubmitResultsHandler = $this->getObject('form_submit_results_handler', 'formbuilder');
    $objSubmitResultsHandler->setFormNumber($formNumber);
    $objSubmitResultsHandler->setNumberOfEntriesInPaginationBatch($numberOfEntriesInPaginationBatch);
    echo $objSubmitResultsHandler->getMoreResultsPerPaginationRequest($latestOrAllResults, $paginationRequestNumber);
}
?>
