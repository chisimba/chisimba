<?php

/* ! \file form_successful_submission_diverter.php
 * \brief The template file is called by the javascript in the template file
 * form_successful_submission.php if the advanced publishing parameters are
 * selected.
 * \section sec Template Code Explanation
 * - Request from the form number and submit number from the URI and
 * store them into temporray variables.
 * - Get the next action and module parameters from the publishing entry in the
 * database.
 * - If the submit results are selected to be passed over with action, then
 * prepare the array with the form element names and their values.
 * - Go to the next chisimba action.
 */

echo $formNumber= $this->getParam("formNumber",NULL);
echo $submitNumber= $this->getParam("submitNumber",NULL);
 $objDBFormPublishingOptions = $this->getObject('dbformbuilder_publish_options','formbuilder');
 $objDBFormSubmitResults = $this->getObject('dbformbuilder_submit_results','formbuilder');
   $formPublishingData =  $objDBFormPublishingOptions->getFormPublishingData($formNumber);
 $submitResultArray = $objDBFormSubmitResults->getParticularSubmitResults($submitNumber);

$formPublishingOption=  $formPublishingData["0"]['publishoption'];
$chisimbaParameters =  $formPublishingData["0"]['chisimbaparameters'];
$chisimbaModule= $formPublishingData["0"]['chisimbamodule'];
$chisimbaAction= $formPublishingData["0"]['chisimbaaction'];
if ($formPublishingOption != 'advanced')
{
 echo "Critical Internal Error. This form has been submitted illegally or publishing parameters are
     corrupted. Please contact your software administrator.";
}
 else {
    if ($chisimbaParameters == "yes")
    {
    $nextActionParameterArray=array();
        foreach ( $submitResultArray as $thisSubmitResult)
{
$formElementName= $thisSubmitResult['formelementname'];
  $formElementValue= $thisSubmitResult['formelementvalue'];
$nextActionParameterArray[$formElementName]=$formElementValue;
}
$this->nextAction($chisimbaAction,$nextActionParameterArray,$chisimbaModule);
    }
 else {
      $this->nextAction($chisimbaAction,'',$chisimbaModule);
    }

}

 









//$this->nextAction($chisimbaAction,$nextActionParameterArray,$chisimbaModule);

?>
