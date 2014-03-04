<?php
/*! \file add_edit_multiselect_dropdown_entity.php
 * \brief The template file is called by an AJAX function to insert a new ms drop down
 * into the database and produce the html content for this form element in the div WYSIWYGMSDropdown
 * \section sec Explanation
 * - Request all the parameters from the post from the
  Ajax function and store them into temporary variables.
 * - Create a new form element and insert these parameters into the database.
 * - If there was a successful insertion of the new form element then construct
 * this new form element in the div WYSIWYGMSDropdown so its content
 * can be passed back into WYSIWYG editor through jQuery.
 */
$formNumber = $this->getParam("formNumber");
$optionValue = $this->getParam('optionValue');
$optionLabel = $this->getParam('optionLabel');
$formElementName = $this->getParam('formElementName');
$update = $this->getParam("update");
$defaultSelected = $this->getParam('defaultSelected');
$menuSize = $this->getParam('menuSize');
$formElementLabel = $this->getParam('formElementLabel');
$formElementLabelLayout = $this->getParam('formElementLabelLayout');
if ($defaultSelected == "on") {
    $defaultSelected = true;
} else {
    $defaultSelected = false;
}

$objMSDDEntity = $this->getObject('form_entity_multiselect_dropdown', 'formbuilder');
$objMSDDEntity->createFormElement($formNumber, $formElementName);


if ($update != "2") {


    if (isset($update) && $update == "1") {
        $objMSDDEntity->updateMetaData($formNumber, $formElementName, $formElementLabel, $formElementLabelLayout, $menuSize);
        $postSuccessBoolean = 1;
    } else {
        if ($objMSDDEntity->insertOptionandValue($formNumber, $formElementName, $optionLabel, $optionValue, $defaultSelected, $menuSize, $formElementLabel, $formElementLabelLayout) == TRUE) {
            $postSuccessBoolean = 1;
        } else {
            $postSuccessBoolean = 0;
        }
    }
} else {
    $postSuccessBoolean = 1;
}
?>

<div id="WYSIWYGMSDropdown">
<?php
if ($postSuccessBoolean == 1) {
    echo $objMSDDEntity->showWYSIWYGMultiSelectDropdownEntity();
} else {
    echo $postSuccessBoolean;
}
?>
</div>
