<?php
/*! \file add_edit_checkbox_entity.php
 * \brief The template file is called by an AJAX function to insert a new check box element
 * into the database and produce the html content for this form element in the div WYSIWYGCheckbox
 * \section sec Explanation
 * - Request all the parameters from the post from the
  Ajax function and store them into temporary variables.
 * - Create a new form element and insert these parameters into the database.
 * - If there was a successful insertion of the new form element then construct
 * this new form element in the div WYSIWYGCheckbox so its content
 * can be passed back into WYSIWYG editor through jQuery.
 */
$formNumber = $this->getParam("formNumber");
$optionValue = $this->getParam('optionValue');
$optionLabel = $this->getParam('optionLabel');
$formElementName = $this->getParam('formElementName');
$layoutOption = $this->getParam('layoutOption');
$defaultSelected = $this->getParam('defaultSelected');
$formElementLabel = $this->getParam('formElementLabel');
$formElementLabelLayout = $this->getParam('formElementLabelLayout');
$update = $this->getParam("update");

if ($defaultSelected == "on") {
    $defaultSelected = true;
} else {
    $defaultSelected = false;
}

$objCheckboxEntity = $this->getObject('form_entity_checkbox', 'formbuilder');
$objCheckboxEntity->setUpFormElement($formNumber, $formElementName);

if ($update != "2") {


    if (isset($update) && $update == "1") {
        $objCheckboxEntity->updateMetaData($formNumber, $formElementName, $formElementLabel, $formElementLabelLayout);
        $postSuccessBoolean = 1;
    } else {
        if ($objCheckboxEntity->createFormElement($formNumber, $formElementName, $optionValue, $optionLabel, $defaultSelected, $layoutOption, $formElementLabel, $formElementLabelLayout) == TRUE) {
            $postSuccessBoolean = 1;
        } else {
            $postSuccessBoolean = 0;
        }
    }
} else {
    $postSuccessBoolean = 1;
}
?>

<div id="WYSIWYGCheckbox">
<?php
if ($postSuccessBoolean == 1) {
    echo $objCheckboxEntity->showWYSIWYGCheckboxEntity();
} else {
    echo $postSuccessBoolean;
}
?>
</div>
