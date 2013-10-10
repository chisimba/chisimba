<?php
/* ! \file add_edit_textinput_entity.php
 * \brief The template file is called by an AJAX function to insert a new text input
 * into the database and produce the html content for this form element in the div WYSIWYGTextInput
 * \section sec Explanation
 * - Request all the parameters from the post from the
  Ajax function and store them into temporary variables.
 * - Create a new form element and insert these parameters into the database.
 * - If there was a successful insertion of the new form element then construct
 * this new form element in the div WYSIWYGTextInput so its content
 * can be passed back into WYSIWYG editor through jQuery.
 */

$formNumber = $this->getParam('formNumber');
$formElementName = $this->getParam('formElementName');
$textInputName = $this->getParam('textInputName');

$textInputValue = $this->getParam('textInputValue');
$textInputType = $this->getParam('textInputType');

$textInputSize = $this->getParam('textInputSize');

$maskedInputChoice = $this->getParam('maskedInputChoice');
$formElementLabel = $this->getParam('formElementLabel');
$formElementLabelLayout = $this->getParam('formElementLabelLayout');
$update = $this->getParam("update");

$objTextInputEntity = $this->getObject('form_entity_textinput', 'formbuilder');
$postSuccessBoolean = 0;
 $objTextInputEntity->createFormElement($formNumber, $formElementName, $textInputName);
if (isset($update) && $update == "1") {
    if ($objTextInputEntity->updateTextInputParameters($formNumber, $formElementName, $textInputName, $textInputValue, $textInputType, $textInputSize, $maskedInputChoice, $formElementLabel, $formElementLabelLayout) == TRUE) {
        $postSuccessBoolean = 1;
    } else {
        $postSuccessBoolean = 0;
    }
} else {
   

    if ($objTextInputEntity->insertTextInputParameters($formNumber, $formElementName, $textInputName, $textInputValue, $textInputType, $textInputSize, $maskedInputChoice, $formElementLabel, $formElementLabelLayout) == TRUE) {
        $postSuccessBoolean = 1;
    } else {
        $postSuccessBoolean = 0;
    }
}
?>
<div id="WYSIWYGTextInput">
    <?php
    if ($postSuccessBoolean == 1) {
        echo $objTextInputEntity->showWYSIWYGTextInputEntity();
    } else {
        echo $postSuccessBoolean;
    }
    ?>
</div>