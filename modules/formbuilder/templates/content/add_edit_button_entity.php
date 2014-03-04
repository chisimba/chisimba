<?php
///Request all the parameters from the post from the
///Ajax function and store them into temporary variables.
$formNumber = $this->getParam("formNumber");
$formElementName = $this->getParam('formElementName');
$buttonName = $this->getParam('buttonName');
$buttonLabel = $this->getParam('buttonLabel');
$submitOrResetButtonChoice = $this->getParam('submitOrResetButtonChoice');
$update = $this->getParam("update");

$objButtonEntity = $this->getObject('form_entity_button', 'formbuilder');
$objButtonEntity->setInitParams($formElementName,$formNumber);


if ($update != "2") {


    if (isset($update) && $update == "1") {
        //$objCheckboxEntity->updateMetaData($formNumber, $formElementName, $formElementLabel, $formElementLabelLayout);
        //$postSuccessBoolean = 1;
    } else {
        if ($objButtonEntity->createFormElement($formNumber, $formElementName, $buttonName, $buttonLabel, $submitOrResetButtonChoice) == TRUE) {
            $postSuccessBoolean = 1;
        } else {
            $postSuccessBoolean = 0;
        }
    }
} else {
    $postSuccessBoolean = 1;
}
?>
<div id="WYSIWYGButton">
<?php
if ($postSuccessBoolean == 1) {
    echo $objButtonEntity->showWYSIWYGButtonEntity();
} else {
    echo $postSuccessBoolean;
}
?>
</div>