<?php
/*! \file add_edit_radio_entity.php
 * \brief The template file is called by an AJAX function to insert a new radio
 * into the database and produce the html content for this form element in the div WYSIWYGRadio
 * \section sec Explanation
 * - Request all the parameters from the post from the
  Ajax function and store them into temporary variables.
 * - Create a new form element and insert these parameters into the database.
 * - If there was a successful insertion of the new form element then construct
 * this new form element in the div WYSIWYGRadio so its content
 * can be passed back into WYSIWYG editor through jQuery.
 */
$formNumber = $this->getParam("formNumber");
$optionValue = $this->getParam('optionValue');
$optionLabel = $this->getParam('optionLabel');
$formElementName = $this->getParam('formElementName');
$update = $this->getParam("update");
$layoutOption = $this->getParam('layoutOption');
$defaultSelected = $this->getParam('defaultSelected');
$formElementLabelLayout = $this->getParam('formElementLabelLayout');
$formElementLabel = $this->getParam('formElementLabel');

if ($defaultSelected == "on") {
    $defaultSelected = true;
} else {
    $defaultSelected = false;
}
$objRadioEntity = $this->getObject('form_entity_radio', 'formbuilder');
$objRadioEntity->createFormElement($formNumber, $formElementName);

if ($update != "2") {




    if (isset($update) && $update == "1") {
        $objRadioEntity->updateMetaData($formNumber, $formElementName, $formElementLabel, $formElementLabelLayout);
        $postSuccessBoolean = 1;
    } else {
        if ($objRadioEntity->insertOptionandValue($formNumber, $formElementName, $optionLabel, $optionValue, $defaultSelected, $layoutOption, $formElementLabel, $formElementLabelLayout) == TRUE) {
            $postSuccessBoolean = 1;
        } else {
            $postSuccessBoolean = 0;
        }
    }
} else {
    $postSuccessBoolean = 1;
}
?>

<div id="WYSIWYGRadio">
<?php
if ($postSuccessBoolean == 1) {
    echo $objRadioEntity->showWYSIWYGRadioEntity();
} else {
    echo $postSuccessBoolean;
}
?>
</div>
