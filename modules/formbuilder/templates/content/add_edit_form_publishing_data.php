<?php

    /*! \file add_edit_form_publishing_data.php
 * \brief The template file is called by the action addEditFormPublishingData.
     * This action is called through by an AJAX call.
     * \brief This template file either updates existing publishing data, creates
     * new publishing data or unpublishes a form.
 * \section sec Explanation
 * - Request all the parameters from the post from the
Ajax function and store them into temporary variables.
     * - Check if the publishing data form that form exists.
     * - If the publishing data does not exist then add a new entry in the
     * database otherwise update the existing entry.
     * - If the publishing option is set to NULL then unpublish the form by updating
     * the existing publishing data.
*/
echo $formNumber = $this->getParam('formNumber',NULL);
echo $publishingOption = $this->getParam('publishingOption',NULL);
echo $urlChoice = $this->getParam('urlChoice',NULL);
echo $chisimbaAction = $this->getParam('chisimbaAction',NULL);
echo $chisimbaModule = $this->getParam('chisimbaModule',NULL);
echo $formParameters = $this->getParam('formParameters',NULL);
echo $divertDelay = $this->getParam('divertDelay',NULL);


if (isset($formNumber))
{
$objDBFormMetadata= $this->getObject('dbformbuilder_form_list','formbuilder');

    $objDBFormPublishingOptions = $this->getObject('dbformbuilder_publish_options','formbuilder');
if ($objDBFormPublishingOptions->checkIfPushlishingDataExists($formNumber) ==FALSE)
{
  $formName = $objDBFormMetadata->getFormName($formNumber);
    $objDBFormPublishingOptions->insertSingle($formNumber,$formName,$publishingOption, $urlChoice,$chisimbaModule,$chisimbaAction,$formParameters,$divertDelay);
}
else
{
         switch ($publishingOption)
        {

            case NULL:
                  $objDBFormPublishingOptions->unpublishForm($formNumber,$publishingOption);
                echo "right";
            break;

case 'simple':
     $objDBFormPublishingOptions->updateSimplePublishingParameters($formNumber,$publishingOption, $urlChoice,$divertDelay);
    break;

case 'advanced':
    $objDBFormPublishingOptions->updateAdvancedPublishingParameters($formNumber,$publishingOption,$chisimbaModule,$chisimbaAction,$formParameters,$divertDelay);
    break;

    default:
                $postSuccess = 0;
            break;
        }
}
}

?>
