<?php

/*! \file construct_current_form.php
 * \brief The template file is called by an action buildCurrentForm.
 * \brief This template file constructs a form for user submission.
 * \note No javascript is included to prevent library conflicts.
 * \section sec Template Code Explanation
 * - Construct the form will the a speicified form number.
*/
$jqueryUILoader = $this->getObject('jqueryui_loader','formbuilder');
$this->appendArrayVar('headerParams', $jqueryUILoader->includeJqueyUI());
$formValidator = '<script language="JavaScript" src="' . $this->getResourceUri('js/validateConstructedForm.js', 'formbuilder') . '" type="text/javascript"></script>';
$this->appendArrayVar('headerParams', $formValidator);
$objFormConstructor = $this->getObject('form_entity_handler','formbuilder');

echo "<input type='hidden' id='formName' value=".($objFormConstructor->getFormName($formNumber)).">";
echo ($objFormConstructor->buildForm($formNumber));
?>
