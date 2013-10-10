<?php

//Use Doxygen to extract comments into a technical reference manual.
//Download Doxygen from www.doxygen.org

/*!  \class form_submit_results_handler
 *
 *  \brief Class that models the all the objects and functionality that is used
 * to view submitted results.
 * \note This class is not used to save submitted results into the database or
 * email them. That functionality is on the main \ref formbuilder class
 * \note This class is used extensively by the the view_submit_results.php template file
 *   \author Salman Noor
 *  \author BIS Intern
 *  \author School of Electrical Engineering, WITS Unversity
 *  \version 0.01
 *  \date    July 9, 2010
 * \warning This class utilizes the functionality of its parent class
 * object from the chisimba core to manage class objects with ease.
 * If the object class is altered in the future, this class may not function.
 */
class form_submit_results_handler extends object {

    /*!
     * \brief Private data member to store the form number for the form that can
     * be readily used anywhere in this class
     */
    private $formNumber;

    /*!
     * \brief Private data member from the class \ref dbformbuilder_submit_results that stores all
     * the properties of this class in an usable object.
     * \note This object is used to get all of the submitted results from the
     * database.
     */
    private $objDBFormSubmitResults;

    /*!
     * \brief Private data member to store the number of form elements of a particular
     * form which can be used readily anywhere inside this class.
     * \note This object is used to get all of the submitted results form the
     * database.
     */
    private $numberofSubmissionElements;

    /*!
     * \brief Private data member to store the number submission results per one
     * page for pagination.
     */
    private $noOfEntriesInPaginationBatch;

//Dead Variable
// private $initialEntryOffset;

    /*!
     * \brief Standard constructor that sets up all the private data members
     *  of this class.
     */
    public function init() {
        $this->formNumber = NULL;
        $this->noOfEntriesInPaginationBatch = NULL;
//   $this->initialEntryOffset = NULL;
        $this->objDBFormSubmitResults = $this->getObject('dbformbuilder_submit_results', 'formbuilder');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
    }

    /*!
     * \brief This member function sets the private data member
     * formNumber
     * \param formNumber An integer. Make sure you insert the right form
     * number or you will pull wrong submit results.
     */
    public function setFormNumber($formNumber) {
        $this->formNumber = $formNumber;
    }

    /*!
     * \brief This member function gets the private data member
     * formNumber
     * \return An integer with the form number.
     */
    public function getFormNumber() {
        return $this->formNumber;
    }

    /*!
     * \brief This member function gets the number of form elements inside a
     * particular form and sets it in the private data member
     * numberofSubmissionElements
     */
    public function setNumberOfSubmissionElements() {
        $submitResultsParameters = $this->objDBFormSubmitResults->getAllFormResults($this->formNumber);
        $formNumber = $submitResultsParameters['0']['formnumber'];
        $submitNumber = $submitResultsParameters['0']["submitnumber"];
        $this->numberofSubmissionElements = $this->objDBFormSubmitResults->getNumberofSubmissionElements($formNumber, $submitNumber);
    }

    /*!
     * \brief This member function allows you to set a customizable number of
     * entries in a pagination batch.
     * \note When a user scrolls down to view more results,
     * this number will determine how many more results will
     * load at one time.
     * \param noOfEntries An integer. Make sure you dont put
     * crazy stuff as an argument.
     */
    public function setNumberOfEntriesInPaginationBatch($noOfEntries=2) {
        $this->noOfEntriesInPaginationBatch = $noOfEntries;
    }

    /*!
     * \brief This member function allows you to get the customizable number of
     * entries in a pagination batch.
     * \return An integer.
     */
    public function getNumberOfEntriesInPaginationBatch() {
        return $this->noOfEntriesInPaginationBatch;
    }

    /*!
     * \brief This member contructs content with all the submit results for a
     * certain form with a form number and writes it into
     * a CSV.
     * \param formNumber An integer. Make sure you insert the right form
     * number or you will pull wrong submit results.
     * \return The CSV file content.
     */
    public function downloadCSVSubmitResultsFile($formNumber) {
///Point to the CSV file
        $myFile = $this->getResourceUri('textfiles/submit_results.csv', 'formbuilder');
///Open that file for writing
        $fh = fopen($myFile, 'w') or die("can't open file");
///Get all the submit numbers for the form and store it into an array
        $submitNumbersArray = $this->objDBFormSubmitResults->getOnlyFormSubmitNumbers($formNumber);
        $formPartCSVFileHeader = "\"Submit Number\"~\"Name of Submitter\"~\"Email Address\"~\"Time of Submission\"~";
        $relativeSubmitNumber = 1;
//print_r($submitNumbersArray);
//Old Code for Back Up
foreach($submitNumbersArray as $indexOfSubmitNumber=>$thisSubmitNumber){
   //Store the values of the array in variables

      $submitNumber = $thisSubmitNumber["submitnumber"];

    if ($indexOfSubmitNumber==0)
{
 $previousSubmitNumber =$submitNumber;
 //print_r($previousSubmitNumber);
$formElementTypesArray = $this->objDBFormSubmitResults->getOnlyFormElementTypes($previousSubmitNumber);
 $numItems = count($formElementTypesArray);
 //print_r($formElementTypesArray);
foreach($formElementTypesArray as $key=>$thisFormElementType){

 $formElementName= $thisFormElementType["formelementname"];
 $formElementType = $thisFormElementType["formelementtype"];

$singleHeaderCSVField = "\"".$formElementName." ( ". $formElementType ." )\"";
$formFieldNamesAndTypeHeaderCSVField .= $singleHeaderCSVField;
if ($key!=$numItems-1)
{
 $formFieldNamesAndTypeHeaderCSVField .="~";
}

}

$CSVFileContent.=$formPartCSVFileHeader.$formFieldNamesAndTypeHeaderCSVField."\n";
}



   $formElementTypesArray = $this->objDBFormSubmitResults->getOnlyFormElementTypes($submitNumber);
    $numItems = count($formElementTypesArray);
$previousFormElementTypesArray = $this->objDBFormSubmitResults->getOnlyFormElementTypes($previousSubmitNumber);
if ($formElementTypesArray!=$previousFormElementTypesArray)
{
foreach($formElementTypesArray as $key=>$thisFormElementType){

 $formElementName= $thisFormElementType["formelementname"];
 $formElementType = $thisFormElementType["formelementtype"];

$singleHeaderCSVField = $formElementName." ( ". $formElementType ." )";
$changedFormFieldNamesAndTypeHeaderCSVField .= " ".$singleHeaderCSVField;
if ($key!=$numItems-1)
{
 $changedFormFieldNamesAndTypeHeaderCSVField .="~";
}
}
$CSVFileContent.="\n\"Form Element Content Has Been Changed.\"\n\n".$formPartCSVFileHeader.$changedFormFieldNamesAndTypeHeaderCSVField."\n";
}

      $previousSubmitNumber =$submitNumber;

         $submitResultsParameters = $this->objDBFormSubmitResults->getParticularSubmitResults($submitNumber);
        $userIDOfFormSubmitter = $submitResultsParameters["0"]["useridofformsubmitter"];
        $timeOfSubmission = $submitResultsParameters["0"]["timeofsubmission"];

$metaDataResults= "\"".$relativeSubmitNumber."\"~"
        ."\"".$this->objDBFormSubmitResults->getSubmitUsersFullName($userIDOfFormSubmitter)."\"~"
        ."\"".$this->objDBFormSubmitResults->getSubmitUsersEmail($userIDOfFormSubmitter)."\"~"
        ."\"".$timeOfSubmission."\"~";
$resultsContent=null;
    $numItems = count($submitResultsParameters);
foreach ($submitResultsParameters as $key=>$thisSubmitResultParameter) {
            $formNumber = $thisSubmitResultParameter['formnumber'];
            $submitNumber = $thisSubmitResultParameter["submitnumber"];
            $formElementName = $thisSubmitResultParameter["formelementname"];
            $formElementType = $thisSubmitResultParameter["formelementtype"];
            $formElementValue = $thisSubmitResultParameter["formelementvalue"];
            $userIDOfFormSubmitter = $thisSubmitResultParameter["useridofformsubmitter"];
            $timeOfSubmission = $thisSubmitResultParameter["timeofsubmission"];



            $resultsContent .=  "\"".$formElementValue."\"";
if ($key!=$numItems-1)
{
  $resultsContent .="~";
}
        }

$CSVFileContent.=$metaDataResults.$resultsContent."\n";

$relativeSubmitNumber++;
}
///Construct the Header for the CSV file content
//        //COMMENTING POINT
//        foreach ($submitNumbersArray as $indexOfSubmitNumber => $thisSubmitNumber) {
////Store the values of the array in variables
//
//            $submitNumber = $thisSubmitNumber["submitnumber"];
//
//            if ($indexOfSubmitNumber == 0) {
//                $previousSubmitNumber = $submitNumber;
//                $formElementTypesArray = $this->objDBFormSubmitResults->getOnlyFormElementTypes($submitNumber);
//                $numOfFormElements = count($formElementTypesArray);
//                foreach ($formElementTypesArray as $key => $thisFormElementType) {
//
//                    $formElementName = $thisFormElementType["formelementname"];
//                    $formElementType = $thisFormElementType["formelementtype"];
//
//                    $singleHeaderCSVField = "\"" . $formElementName . " ( " . $formElementType . " )\"";
//                    $formFieldNamesAndTypeHeaderCSVField .= $singleHeaderCSVField;
//                    if ($key != $numOfFormElements - 1) {
//                        $formFieldNamesAndTypeHeaderCSVField .="~";
//                    }
//                }
//                $CSVFileContent.=$formPartCSVFileHeader . $formFieldNamesAndTypeHeaderCSVField . "\n";
//            }
//
//
//
////   $formElementTypesArray = $this->objDBFormSubmitResults->getOnlyFormElementTypes($submitNumber);
////    $currentumItems = count($formElementTypesArray);
////$previousFormElementTypesArray = $this->objDBFormSubmitResults->getOnlyFormElementTypes($previousSubmitNumber);
////if ($formElementTypesArray!=$previousFormElementTypesArray)
////{
////foreach($formElementTypesArray as $key=>$thisFormElementType){
////
//// $formElementName= $thisFormElementType["formelementname"];
//// $formElementType = $thisFormElementType["formelementtype"];
////
////$singleHeaderCSVField = $formElementName." ( ". $formElementType ." )";
////$changedFormFieldNamesAndTypeHeaderCSVField .= " ".$singleHeaderCSVField;
////if ($key!=$numItems-1)
////{
//// $changedFormFieldNamesAndTypeHeaderCSVField .="~";
////}
////}
////$CSVFileContent.="\n\"Form Element Content Has Been Changed.\"\n\n".$formPartCSVFileHeader.$changedFormFieldNamesAndTypeHeaderCSVField."\n";
////}
////
////      $previousSubmitNumber =$submitNumber;
//        }
//        $index = 0;
/////Contruct the content for the actual submit results.
//        foreach ($submitNumbersArray as $indexOfSubmitNumber => $thisSubmitNumber) {
////Store the values of the array in variables
//
//            $submitNumber = $thisSubmitNumber["submitnumber"];
//
//            $submitResultsParameters = $this->objDBFormSubmitResults->getParticularSubmitResults($submitNumber);
//            $userIDOfFormSubmitter = $submitResultsParameters["0"]["useridofformsubmitter"];
//            $timeOfSubmission = $submitResultsParameters["0"]["timeofsubmission"];
//
//            $metaDataResults = "\"" . $relativeSubmitNumber . "\"~"
//                    . "\"" . $this->objDBFormSubmitResults->getSubmitUsersFullName($userIDOfFormSubmitter) . "\"~"
//                    . "\"" . $this->objDBFormSubmitResults->getSubmitUsersEmail($userIDOfFormSubmitter) . "\"~"
//                    . "\"" . $timeOfSubmission . "\"~";
//            $resultsContent = null;
//            $numItems = count($submitResultsParameters);
//            $nullFormElements = 0;
//            foreach ($submitResultsParameters as $key => $thisSubmitResultParameter) {
//                $formNumber = $thisSubmitResultParameter['formnumber'];
//                $submitNumber = $thisSubmitResultParameter["submitnumber"];
//                $formElementName = $submitResultsParameters[$key - $nullFormElements]["formelementname"];
//                $formElementType = $thisSubmitResultParameter["formelementtype"];
//                $formElementValue = $submitResultsParameters[$key - $nullFormElements]["formelementvalue"];
//                $userIDOfFormSubmitter = $thisSubmitResultParameter["useridofformsubmitter"];
//                $timeOfSubmission = $thisSubmitResultParameter["timeofsubmission"];
//
//
//                $formElementTypesArray = $this->objDBFormSubmitResults->getOnlyFormElementTypes($formNumber);
//                $formElementNameHeader = $formElementTypesArray[$key]["formelementname"];
//                if ($formElementName == $formElementNameHeader) {
//                    $resultsContent .= "\"" . $formElementValue . "\"";
//                } else {
////     $resultsContent .="|$formElementNameHeader"." NULL".$formElementName."|";
//                    $resultsContent .="NULL";
//                    $nullFormElements++;
//                }
//
//                if ($key != $numItems - 1) {
//                    $resultsContent .="~";
//                } else {
//
//                    if (($numOfFormElements - $numItems) > 0) {
//                        $difference = $numOfFormElements - $numItems;
//                        $resultsContent .="~";
//                        for ($i = 0; $i < $difference; $i++) {
//                            $formElementTypesArray = $this->objDBFormSubmitResults->getOnlyFormElementTypes($formNumber);
//                            end($formElementTypesArray);         // move the internal pointer to the end of the array
//                            $keyOfFormElementTypesArray = key($formElementTypesArray);  // fetches the key of the element pointed to by the internal
//                            $formElementNameHeader = $formElementTypesArray[($keyOfFormElementTypesArray + 1) - $difference + $i]["formelementname"];
//                            end($submitResultsParameters);         // move the internal pointer to the end of the array
//                            $keyOfSubmitResultsParameters = key($submitResultsParameters);  // fetches the key of the element pointed to by the internal
//                            $formElementName = $submitResultsParameters[($keyOfSubmitResultsParameters + 1) - $nullFormElements + $i]["formelementname"];
//                            $formElementValue = $submitResultsParameters[($keyOfSubmitResultsParameters + 1) - $nullFormElements + $i]["formelementvalue"];
////  $resultsContent .=" $formElementNameHeader"."   ".$formElementName."^";
//                            if ($formElementName == $formElementNameHeader) {
//                                $resultsContent .= "\"" . $formElementValue . "\"";
//                            } else {
////     $resultsContent .="|$formElementNameHeader"." NULL".$formElementName."|";
//                                $resultsContent .="NULL";
//                                $nullFormElements++;
//                            }
//
//                            if ($i < ($difference - 1)) {
//                                $resultsContent .="~";
//                            }
//                        }
////     for ($i=1; $i<=$difference; $i++)
////  {
////  $resultsContent .="\"NULL\"";
////  if($i == $difference){break;}
////  $resultsContent .="~";
////  }
//                    }
//                }
//                $index++;
//            }
//
//            $CSVFileContent.=$metaDataResults . $resultsContent . "\n";
//
//            $relativeSubmitNumber++;
//        }
///Write to content to the file
        fwrite($fh, $CSVFileContent);
///Close the CSV file
        fclose($fh);

///Return the CSv file content.
        return $CSVFileContent;
    }

    /*!
     * \brief This member contructs the div in which contains a button to dowload
     * a CSV file.

     * \return The div content for the view_submitted_results.php template file.
     */
    public function buildCSVFileDownloadLink() {
        $pageContent = "<div id='downloadCSVLinkContainer' class='ui-accordion-content ui-widget-content ui-corner-all'style='border:1px solid #CCCCCC;margin:10px 55px 10px 55px;'> ";
        $pageContent .= "<h5 align='center'>Click on the button below to download a CSV file of all sumbitted results for this form.</h5>";

        $pageContent .="<p align='center'>

<button class='downloadCSVFileLink'>Download CSV Submit Results File</button>
</p>";
        $pageContent .= "</div>";
        return $pageContent;
    }

    /*!
     * \brief This member is called by an AJAX call which constructs the submit result
     * content for a particular submit number.
     * \param submitNumber An integer. Make sure you insert
     * the right submit number to pull the right submit results.
     * \return The submit results for that submit number.
     */
    public function getParticularSubmitResult($submitNumber) {

        $submitResultsParameters = $this->objDBFormSubmitResults->getParticularSubmitResults($submitNumber);
        $userIDOfFormSubmitter = $submitResultsParameters["0"]["useridofformsubmitter"];
        $timeOfSubmission = $submitResultsParameters["0"]["timeofsubmission"];


        $resultsContent = "<b>Name of Person Submitting Form:  </b>" . $this->objDBFormSubmitResults->getSubmitUsersFullName($userIDOfFormSubmitter) . "<br>";
        $resultsContent .= "<b>Email Address of Person Submitting Form:  </b>" . $this->objDBFormSubmitResults->getSubmitUsersEmail($userIDOfFormSubmitter) . "<br>";
        $resultsContent .= "<b>Time of Submission:  </b>" . $timeOfSubmission . "<br>";
        $resultsContent .= "<h2>Results</h2>";

        foreach ($submitResultsParameters as $thisSubmitResultParameter) {
            $formNumber = $thisSubmitResultParameter['formnumber'];
            $submitNumber = $thisSubmitResultParameter["submitnumber"];
            $formElementName = $thisSubmitResultParameter["formelementname"];
            $formElementType = $thisSubmitResultParameter["formelementtype"];
            $formElementValue = $thisSubmitResultParameter["formelementvalue"];
            $userIDOfFormSubmitter = $thisSubmitResultParameter["useridofformsubmitter"];
            $timeOfSubmission = $thisSubmitResultParameter["timeofsubmission"];

            $resultsContent .= "<b>" . $formElementType . " : " . $formElementName . " : </b><br>" . $formElementValue . "<br>";
        }
        return $resultsContent;
    }

    /*!
     * \brief This member determines submit results offset. When more results are
     * paginated or loaded, this number is the entry in the
     * database where entry start to get loaded.
     * \param paginationRequestNumber An integer. This number
     * is how many times a pagination request has occured.
     * \return An integer.
     */
    private function determinePaginationEntryOffset($paginationRequestNumber) {
        return $this->noOfEntriesInPaginationBatch * $paginationRequestNumber;
    }

    /*!
     * \brief When the user scrolls down to load more results, this is the member
     * function that gets called to load more results.
     * \param latestOrAllResultsChoice Two possilities exist. This
     * member function can either give all the submit results
     * or the latest from each submitter.
     * \param paginationRequestNumber An integer. This number
     * is how many times a pagination request has occured.
     * \return An array with the submit results.
     */
    public function getMoreResultsPerPaginationRequest($latestOrAllResultsChoice = "allResults", $paginationRequestNumber) {
        if ($latestOrAllResultsChoice == "allResults") {
            $submitResultsParameters = $this->objDBFormSubmitResults->getOnlyDistinctFormResults($this->formNumber, $this->getNumberOfEntriesInPaginationBatch(), $this->determinePaginationEntryOffset($paginationRequestNumber));
        }
        if ($latestOrAllResultsChoice == "latestResults") {
            $submitResultsParameters = $this->objDBFormSubmitResults->getLatestSubmitResult($this->formNumber, $this->getNumberOfEntriesInPaginationBatch(), $this->determinePaginationEntryOffset($paginationRequestNumber));
        }

        if (empty($submitResultsParameters)) {
            return 0;
        }
        $resultsTable = &$this->newObject("htmltable", "htmlelements");
//Define the table border
        $resultsTable->border = 0;
//Set the table spacing
        $resultsTable->cellspacing = '12';
//Set the table width
        $resultsTable->width = "87%";

        foreach ($submitResultsParameters as $thisSubmitResultParameter) {
            $formNumber = $thisSubmitResultParameter['formnumber'];
            $submitNumber = $thisSubmitResultParameter["submitnumber"];
            $formElementName = $thisSubmitResultParameter["formelementname"];
            $formElementType = $thisSubmitResultParameter["formelementtype"];
            $formElementValue = $thisSubmitResultParameter["formelementvalue"];
            $userIDOfFormSubmitter = $thisSubmitResultParameter["useridofformsubmitter"];
            $timeOfSubmission = $thisSubmitResultParameter["timeofsubmission"];

            $viewResultsSelect = $this->getObject('geticon', 'htmlelements');

            $viewResultsSelect->setIcon('view');
            $viewResultsSelect->alt = "View Submitted Results";

            $mngViewResultsLink = new link($this->uri(array(
                                'module' => 'formbuilder',
                                'action' => 'viewSubmitNumberResult',
                                'submitNumber' => $submitNumber
                            )));
            if ($latestOrAllResultsChoice == "allResults") {
                $mngViewResultsLink->title = "viewParticularResult";
            }
            if ($latestOrAllResultsChoice == "latestResults") {
                $mngViewResultsLink->title = "viewLatestParticularResult";
            }

            $mngViewResultsLink->link = $viewResultsSelect->show();
            $linkViewResultManage = $mngViewResultsLink->show();

            $resultsTable->startRow();
            $resultsTable->addCell($this->objDBFormSubmitResults->getSubmitUsersFullName($userIDOfFormSubmitter));
            $resultsTable->addCell($this->objDBFormSubmitResults->getSubmitUsersEmail($userIDOfFormSubmitter));
            $resultsTable->addCell($timeOfSubmission);
            $resultsTable->addCell($linkViewResultManage);
            $resultsTable->endRow();
        }

        return $resultsTable->show();
    }

//old pagination code: here for backup.
//public function setIntitialEntryOffset($entryOffset)
//{
//    $this->initialEntryOffset=$entryOffset;
//}
//
//public function getInitialEntryOffset()
//{
//    return $this->initialEntryOffset;
//}

    /*!
     * \brief This member function gets initially called in PHP for the first batch
     * of results.
     * \param latestOrAllResultsChoice Two possilities exist. This
     * member function can either give all the submit results
     * or the latest from each submitter.
     * \return An array with the submit results.
     */
    public function getAllFormResults($latestOrAllResultsChoice = "allResults") {
        if ($latestOrAllResultsChoice == "allResults") {
            $submitResultsParameters = $this->objDBFormSubmitResults->getOnlyDistinctFormResults($this->formNumber, $this->getNumberOfEntriesInPaginationBatch(), 0);
        }
        if ($latestOrAllResultsChoice == "latestResults") {
            $submitResultsParameters = $this->objDBFormSubmitResults->getLatestSubmitResult($this->formNumber, $this->getNumberOfEntriesInPaginationBatch(), 0);
        }

        if ($submitResultsParameters == NULL) {
            return "No results have been submitted for this form yet.";
        }
        $resultsTable = &$this->newObject("htmltable", "htmlelements");
//Define the table border
        $resultsTable->border = 0;
//Set the table spacing
        $resultsTable->cellspacing = '12';
//Set the table width
        $resultsTable->width = "95%";

//Create the array for the table header
        $tableHeader = array();
        $tableHeader[] = "Name of Submitter";
        $tableHeader[] = "Email of Submitter";
        $tableHeader[] = "Time of Submission";
        $tableHeader[] = "View Result";
        $resultsTable->addHeader($tableHeader, "heading");



        foreach ($submitResultsParameters as $thisSubmitResultParameter) {
            //$formNumber = $thisSubmitResultParameter['formnumber'];
            $submitNumber = $thisSubmitResultParameter["submitnumber"];
           // $formElementName = $thisSubmitResultParameter["formelementname"];
           // $formElementType = $thisSubmitResultParameter["formelementtype"];
           // $formElementValue = $thisSubmitResultParameter["formelementvalue"];
            $userIDOfFormSubmitter = $thisSubmitResultParameter["useridofformsubmitter"];
            $timeOfSubmission = $thisSubmitResultParameter["timeofsubmission"];


            $viewResultsSelect = $this->getObject('geticon', 'htmlelements');

            $viewResultsSelect->setIcon('view');
            $viewResultsSelect->alt = "View Submitted Results";

            $mngViewResultsLink = new link($this->uri(array(
                                'module' => 'formbuilder',
                                'action' => 'viewSubmitNumberResult',
                                'submitNumber' => $submitNumber
                            )));
            if ($latestOrAllResultsChoice == "allResults") {
                $mngViewResultsLink->title = "viewParticularResult";
            }
            if ($latestOrAllResultsChoice == "latestResults") {
                $mngViewResultsLink->title = "viewLatestParticularResult";
            }

            $mngViewResultsLink->link = $viewResultsSelect->show();
            $linkViewResultManage = $mngViewResultsLink->show();

            $resultsTable->startRow();
            $resultsTable->addCell($this->objDBFormSubmitResults->getSubmitUsersFullName($userIDOfFormSubmitter));
            $resultsTable->addCell($this->objDBFormSubmitResults->getSubmitUsersEmail($userIDOfFormSubmitter));
            $resultsTable->addCell($timeOfSubmission);
            $resultsTable->addCell($linkViewResultManage);
            $resultsTable->endRow();
        }

        return $resultsTable->show();
    }

}

?>
