<?php

/*! \file list_all_forms_paginated.php
 * \brief The template file is called by the action listAllFormsPaginated in the controller.php
 * file called through by AJAX functions within the list_all_forms.php template file. This template file
 * gets more results for pagination and the pagination indicator.
 * \section sec Template Code Explanation
 * - Request the pagination request number, the batch size and the search value and store them into
 * temporary variables.
 * - If the search value is not null then get all the results with batch size
 * specified and the pagination request number. Otherwise only get the entries
 * that match to the search entry.
 * - Get the pagination indicator.
 * - Spit out both of them.
 */

$paginationRequestNumber = $this->getParam('paginationRequestNumber', NULL);
$paginationbatchSize = $this->getParam("paginationbatchSize", NULL);
$searchValue = $this->getParam("searchValue", NULL);
$objFormList = $this->getObject('view_form_list', 'formbuilder');
$objFormList->setNumberOfEntriesInPaginationBatch($paginationbatchSize);
if ($searchValue != NULL) {
    echo $objFormList->showPaginationIndicator($paginationRequestNumber, $searchValue);
    echo $objFormList->show($paginationRequestNumber, $searchValue);
} else {
    echo $objFormList->showPaginationIndicator($paginationRequestNumber, NULL);
    echo $objFormList->show($paginationRequestNumber, NULL);
}
?>
