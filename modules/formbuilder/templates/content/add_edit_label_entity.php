<?php
/*! \file add_edit_label_entity.php
 * \brief The template file is called by an AJAX function to insert a label element
 * into the database and produce the html content for this form element in the div WYSIWYGLabel
 * \section sec Explanation
 * - Request all the parameters from the post from the
  Ajax function and store them into temporary variables.
 * - Create a new form element and insert these parameters into the database.
 * - If there was a successful insertion of the new form element then construct
 * this new form element in the div WYSIWYGLabel so its content
 * can be passed back into WYSIWYG editor through jQuery.
 */

//$this->appendArrayVar('headerParams', $this->getJavascriptFile('1.4.2/jquery-1.4.2.min.js', 'jquery'));
$formNumber = $this->getParam("formNumber");
$labelValue = $this->getParam('labelValue');

$formElementName = $this->getParam('formElementName');

$layoutOption = $this->getParam('layoutOption');
$update = $this->getParam("update");
$layoutOption = trim("$layoutOption");

$objLabelEntity = $this->getObject('form_entity_label', 'formbuilder');
$postSuccessBoolean = 0;
if (isset($update) && $update == "1") {
        if ($objLabelEntity->updateFormElement($formNumber, $formElementName, $labelValue, $layoutOption) == TRUE) {
        $postSuccessBoolean = 1;
    } else {
        $postSuccessBoolean = 0;
    }
} else {
    if ($objLabelEntity->createFormElement($formNumber, $formElementName, $labelValue, $layoutOption) == TRUE) {
        $postSuccessBoolean = 1;
    } else {
        $postSuccessBoolean = 0;
    }
}

?>

<div id="WYSIWYGLabel">
<?php
if ($postSuccessBoolean == 1) {
    echo $objLabelEntity->showWYSIWYGLabelEntity();
} else {
    echo $postSuccessBoolean;
}
?>
</div>
