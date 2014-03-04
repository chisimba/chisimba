<?php
/* ! \file add_edit_textarea_entity.php
 * \brief The template file is called by an AJAX function to insert a new text area
 * into the database and produce the html content for this form element in the div WYSIWYGTextArea
 * \section sec Explanation
 * - Request all the parameters from the post from the
  Ajax function and store them into temporary variables.
 * - Create a new form element and insert these parameters into the database.
 * - If there was a successful insertion of the new form element then construct
 * this new form element in the div WYSIWYGTextArea so its content
 * can be passed back into WYSIWYG editor through jQuery.
 */
$formNumber = $this->getParam("formNumber");
$formElementName = $this->getParam('formElementName');
$textAreaName = $this->getParam('textAreaName');
$textAreaValue = $this->getParam('textAreaValue');
$ColumnSize = $this->getParam('ColumnSize');
$RowSize = $this->getParam('RowSize');
$simpleOrAdvancedHAChoice = $this->getParam('simpleOrAdvancedHAChoice');
$toolbarChoice = $this->getParam('toolbarChoice');
$formElementLabel = $this->getParam('formElementLabel');
$labelLayout = $this->getParam('labelLayout');
$update = $this->getParam('update');


$objTextAreaEntity = $this->getObject('form_entity_textarea', 'formbuilder');
$objTextAreaEntity->createFormElement($formNumber, $textAreaName, $textAreaName);
$postSuccessBoolean = 0;

if (isset($update) && $update == "1") {
    if ($objTextAreaEntity->updateTextAreaParameters($formNumber, $formElementName, $textAreaName, $textAreaValue, $ColumnSize, $RowSize, $simpleOrAdvancedHAChoice, $toolbarChoice, $formElementLabel, $labelLayout) == TRUE) {
        $postSuccessBoolean = 1;
    } else {
        $postSuccessBoolean = 0;
    }
} else {
    if ($objTextAreaEntity->insertTextAreaParameters($formNumber, $formElementName, $textAreaName, $textAreaValue, $ColumnSize, $RowSize, $simpleOrAdvancedHAChoice, $toolbarChoice, $formElementLabel, $labelLayout) == TRUE) {
        $postSuccessBoolean = 1;
    } else {
        $postSuccessBoolean = 0;
    }
}
?>
<div id="WYSIWYGTextArea">
<?php
if ($postSuccessBoolean == 1) {
    echo $objTextAreaEntity->showWYSIWYGTextAreaEntity();
} else {
    echo $postSuccessBoolean;
}
?>
</div>