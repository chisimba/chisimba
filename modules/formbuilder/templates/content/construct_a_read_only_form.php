<?php

/*! \file construct_a_read_only_form.php
 * \brief The template file is called by an action buildAReadOnlyForm called by
 * AJAX within the list_all_forms.php template file.
 * \brief This template file simply creates a WYSIWYG form with a form number.
 * \section sec Template Code Explanation
 * - Request all the form Number of the form you want to get from the post from the
AJAX call and store it into a temporary variables.
 * - Build the form with that form number and display it. The javascript will take the
 * html from this template file and display it accordingly.
*/
$formNumber=$this->getParam("formNumber",NULL);
if (isset($formNumber))
{
$objFormConstructor = $this->getObject('form_entity_handler','formbuilder');


echo ($objFormConstructor->buildWYSIWYGForm($formNumber));
}
?>
