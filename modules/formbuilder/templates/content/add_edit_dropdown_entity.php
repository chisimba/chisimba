<?php
/* ! \file add_edit_dropdown_entity.php
 * \brief The template file is called by an AJAX function to insert a new drop down element
 * into the database and produce the html content for this form element in the div WYSIWYGDropDown
 * \section sec Explanation
 * - Request all the parameters from the post from the
  Ajax function and store them into temporary variables.
 * - Create a new form element and insert these parameters into the database.
 * - If there was a successful insertion of the new form element then construct
 * this new form element in the div WYSIWYGDropDown so its content
 * can be passed back into WYSIWYG editor through jQuery.
 */
$formNumber = $this->getParam("formNumber");
$optionValue = $this->getParam('optionValue');
$optionLabel = $this->getParam('optionLabel');
$formElementName = $this->getParam('formElementName');
$update = $this->getParam("update");
$defaultSelected = $this->getParam('defaultSelected');

$formElementLabel = $this->getParam('formElementLabel');
$formElementLabelLayout = $this->getParam('formElementLabelLayout');

if ($defaultSelected == "on") {
    $defaultSelected = true;
} else {
    $defaultSelected = false;
}
$objDDEntity = $this->getObject('form_entity_dropdown', 'formbuilder');
$objDDEntity->createFormElement($formNumber,$formElementName);



if ($update != "2") {


    if (isset($update) && $update == "1") {
        $objDDEntity->updateMetaData($formNumber,$formElementName,$formElementLabel,$formElementLabelLayout);
        $postSuccessBoolean = 1;
    } else {
        if ($objDDEntity->insertOptionandValue($formNumber,$formElementName, $optionLabel, $optionValue, $defaultSelected, $formElementLabel, $formElementLabelLayout) == TRUE) {
            $postSuccessBoolean = 1;
        } else {
            $postSuccessBoolean = 0;
        }
    }
} else {
    $postSuccessBoolean = 1;
}

?>

<div id="WYSIWYGDropdown">
<?php
if ($postSuccessBoolean == 1) {
    echo $objDDEntity->showWYSIWYGDropdownEntity();
} else {
    echo $postSuccessBoolean;
}
?>
</div>
