<?php

/*! \file list_current_form_options.php
 * \brief The template file is called by the action listCurrentFormPublishingData in the controller.php
 * file called through by AJAX functions within the list_all_forms.php or the form_editor.php template files. This template file
 * gets html content for adding and editing publishing parameters for a specific form with a
 * form number.
 * \section sec Template Code Explanation
 * - Request the form number store it into
 * a temporary variable.
 * - Construct the publishing indicator and the simple and advanced publishing forms.
 * - Spit them out.
 */

$formNumber=$this->getParam("formNumber",NULL);
if (isset($formNumber))
{
$objFormMenuConstructor = $this->getObject('view_form_list','formbuilder');


echo ($objFormMenuConstructor->showFormOptionsMenu($formNumber));
}
?>
