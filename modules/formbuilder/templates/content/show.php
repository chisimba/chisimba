<?php

/*! \file show.php
 * \brief The template file exists so developers can test small pieces of code.
 * This template file called by the action show. This template file is not
 * being used and consists or a lot of dead expermintal code. If you want to experiment
 * with some extra stuff, then this is place to do it. If your code works, then push
 * it in to the actual module.
 */
$jqueryUILoader = $this->getObject('jqueryui_loader','formbuilder');
$this->appendArrayVar('headerParams', $jqueryUILoader->includeJqueyUI());
$string = "Here! is some text, and numbers 12345,  !@#$%&**()__++--={}|[]\:><?,./ and symbols !�$%^&";

$new_string = preg_replace("/[^a-zA-Z0-9s]/", "", $string);

echo $new_string;

//echo "formnumber is".$currentformNumber."<br>";

//echo $formNumber = $this->getParam('formNumber',NULL)."<br>";
echo $formTitle = $this->getParam('formTitle',NULL)."<br>";
echo $formLabel = $this->getParam('formLabel',NULL)."<br>";
echo $formEmail = $this->getParam('formEmail',NULL)."<br>";
echo $submissionOption = $this->getParam('formSubmissionRadio',NULL)."<br>";
echo $formDescription = $this->getParam('formCaption',NULL)."<br>";

//echo $formTitle = $this->getParam('formTitle',NULL)."<br>";
//echo $formLabel = $this->getParam('formLabel',NULL)."<br>";
//echo $formEmail = $this->getParam('formEmail',NULL)."<br>";
//echo $submissionOption = $this->getParam('submissionOption',NULL)."<br>";
//echo $formDescription = $this->getParam('formDescription',NULL)."<br>";


//$objFormEntityHandler = $this->getObject('dbformbuilder_submit_results','formbuilder');
//$submitnumber = $objFormEntityHandler->getNextSubmitNumber();
//                            $this->setVar('nameOfSubmitter',$nameOfSubmitter);
//                                            $this->setVar('staffnumberOfSubmitter',$staffnumberOfSubmitter);
//                                                            $this->setVar('emailOfSubmitter',$emailOfSubmitter);
//    echo "submitTime  ".$submitTime."<br>";
//                                                            echo "staffnumberOfSubmitter ".$staffnumberOfSubmitter  ."<br>";
//       echo "emailOfSubmitter ".$emailOfSubmitter ."<br>";
//echo "anme fo ubmitter".$nameOfSubmitter."<br>";
//echo "emaikl address is ".$formEmail."<br>";
//echo "test si ".$test;
//echo "submit number is: ".$submitnumber;
//echo $formNumber."<br>";
//echo $formElementNameList ."<BR>";
//echo $formElementTypeList."<BR>";
//$formElementNameArray = explode(",", $formElementNameList);
//$formElementTypeArray = explode(",", $formElementTypeList);
//
//echo $lengthOfFormElementNameArray= count($formElementNameArray)."<br>";
//echo $lengthOfFormElementTypeArray= count($formElementTypeArray)."<br>";
//if ($lengthOfFormElementNameArray == $lengthOfFormElementTypeArray)
//{
//    for ($i=0; $i<=($lengthOfFormElementNameArray-1); $i++)
//    {
//      echo  $formElementNameArray[$i]."&nbsp;&nbsp;&nbsp;&nbsp;".$formElementTypeArray[$i]."<br>";
//    }
//}
//else
//{
//    echo "Internal Error. Number of form element types and form element names do not match.";
//}
////for ($i=1; $i<=5; $i++)
////{
////
////}
////foreach ($formElementNameArray as $formElementName)
////{
////    echo $formElementName ."<BR>";
////    foreach ($formElementTypeArray as $formElementType)
////    {
////
////                echo $formElementType ."<BR>";
////    }
////}
?>
