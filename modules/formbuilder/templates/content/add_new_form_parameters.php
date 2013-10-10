<?php

/*! \file add_new_form_parameters.php
 * \brief The template file is called by a POST action from a metadata form constructed
 * in the add_edit_publishing_data.php. If the action is set to add form metadata
 * for a new form. This template is called.
 * \brief This template file inserts a new metadata entry into a database.
 * \section sec Template Code Explanation
 * - Request all the parameters from the post from the
metadata form and store them into temporary variables.
 * - From the formlabel, create a unique form name by concatanating a random number
 * between a 1000 anf 99999 and micro time.
 * - Check if there is a duplicate entry that already exists with this form name
 * - If the form name is unique then insert this new entry
 * - If there was a successful insertion of the form entry, then create a new
 * form with a post action designWYSIWYGForm. This form will contain hidden inputs
 * with all the form metadata.
 * - This form will be posted by jQuery so the form metadata can be passed to the
 * form element editor template file.
*/

$this->loadClass('form', 'htmlelements');
$this->loadClass('hiddeninput', 'htmlelements');
//echo $formTitle = $this->getParam('formTitle',NULL);
echo $formLabel = $this->getParam('formLabel',NULL);
echo $formEmail = $this->getParam('formEmail',NULL);
echo $submissionOption = $this->getParam('submissionOption',NULL);
echo $formDescription = $this->getParam('formDescription',NULL);

$stripedFormLabel = preg_replace("/[^a-zA-Z0-9s]/", "", $formLabel);
$formTitle = $stripedFormLabel.rand(1000,99999) .time();
$postSuccessBoolean = 0;

$objDBNewFormParameters = $this->getObject('dbformbuilder_form_list', 'formbuilder');

if ($objDBNewFormParameters->checkDuplicateFormEntry(NULL, $formTitle) == TRUE) {
    $formNumber = $objDBNewFormParameters->insertSingle($formTitle, $formLabel, $formDescription,$formEmail,$submissionOption);
    $postSuccessBoolean = 1;
} else {
    $postSuccessBoolean = 0;
}
?>

<div id="insertFormDetailsSuccessParameter">
<?php
echo $postSuccessBoolean;
?>
</div>
<div id="insertFormNumber">
<?php
if ($postSuccessBoolean == 1) {
//echo $formNumber;
    $objForm = new form('formDetails', $this->uri(array("action" => "designWYSIWYGForm"), "formbuilder"));
    $formNumber = new hiddeninput('formNumber', $formNumber);
    $objForm->addToForm($formNumber->show());

    $formTitle = new hiddeninput('formTitle', $formTitle);
    $objForm->addToForm($formTitle->show());

    $formLabel = new hiddeninput('formLabel', $formLabel);
    $objForm->addToForm($formLabel->show());

    $formEmail = new hiddeninput('formEmail', $formEmail);
    $objForm->addToForm($formEmail->show());

    $submissionOption = new hiddeninput('submissionOption',$submissionOption);
    $objForm->addToForm($submissionOption->show());

    $formDescription = new hiddeninput('formDescription', $formDescription);
    $objForm->addToForm($formDescription->show());
    echo $objForm->show();
} else {
    echo "Post Failure";
}
?>
</div>