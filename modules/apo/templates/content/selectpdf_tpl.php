<?
    $this->loadClass("checkbox", "htmlelements");
    $this->loadClass('fieldset', 'htmlelements');
    $this->loadClass('button', 'htmlelements');

    $displayMessage = $this->objLanguage->languageText('mod_apo_sectionsMessage', 'apo');
    $labelAllSections = $this->objLanguage->languageText('mod_apo_allsection', 'apo');
    $labelOverview = $this->objLanguage->languageText('mod_apo_wordOverview', 'apo');
    $labelRulesandSyllabusOne = $this->objLanguage->languageText('mod_apo_rulesandsyllabusone', 'apo');
    $labelRulesandSyllabusTwo = $this->objLanguage->languageText('mod_apo_rulesandsyllabustwo', 'apo');
    $labelSubsidy = $this->objLanguage->languageText('mod_apo_subsidy', 'apo');
    $labelOutcomesandAssessmentsOne = $this->objLanguage->languageText('mod_apo_outcomesandassessmentsone', 'apo');
    $labelOutcomesandAssessmentsTwo = $this->objLanguage->languageText('mod_apo_outcomesandassessmentstwo', 'apo');
    $labelOutcomesandAssessmentsThree = $this->objLanguage->languageText('mod_apo_outcomesandassessmentsthree', 'apo');
    $labelResources = $this->objLanguage->languageText('mod_apo_resources', 'apo');
    $labelCollaborationsandContracts = $this->objLanguage->languageText('mod_apo_collaborationsandcontracts', 'apo');
    $labelReview = $this->objLanguage->languageText('mod_apo_review', 'apo');
    $labelComments = $this->objLanguage->languageText('mod_apo_comments', 'apo');
    $labelFeedback = $this->objLanguage->languageText('mod_apo_feedback', 'apo');

    $edit = new link($this->uri(array("action"=>"showeditdocument", "id"=>$id)));
    $objIcon = $this->newObject("geticon", "htmlelements");
    $objIcon->setIcon('edit');
    $edit->link = $objIcon->show();
    
    echo "<h2>".$document['docname']."&nbsp;&nbsp;".$edit->show()."</h2>";
    
    // select which sections the user would like to print in the document.
    //print_r($document);

    
    $table = $this->newObject('htmltable', 'htmlelements');
    $table->border = 0;
    $table->cellspacing = '3';
    //$table->width = "30%";

    //Overview
    $checkbox = new checkbox("all");
    $table->startRow();
    $table->addCell($checkbox->show(), "20");
    $table->addCell($labelAllSections);
    $table->endRow();

    //Overview
    $checkbox = new checkbox("overview");
    $table->startRow();
    $table->addCell($checkbox->show(), "20");
    $table->addCell($labelOverview);
    $table->endRow();


    //Rules and Syllabus - Page One
    $checkbox->name = "rulesandsyllabusone";
    $table->startRow();
    $table->addCell($checkbox->show(), "20");
    $table->addCell($labelRulesandSyllabusOne);
    $table->endRow();

    //Rules and Syllabus - Page Two
    $checkbox->name = "rulesandsyllabustwo";
    $table->startRow();
    $table->addCell($checkbox->show(), "20");
    $table->addCell($labelRulesandSyllabusTwo);
    $table->endRow();

    // Subsidy Requirements
    $checkbox->name = "subsidy";
    $table->startRow();
    $table->addCell($checkbox->show(), "20");
    $table->addCell($labelSubsidy);
    $table->endRow();

    // Outcomes and Assessments - Page One
    $checkbox->name = "outcomesandassessmentone";
    $table->startRow();
    $table->addCell($checkbox->show(), "20");
    $table->addCell($labelOutcomesandAssessmentsOne);
    $table->endRow();

    // Outcomes and Assessments - Page Two
    $checkbox->name = "outcomesandassessmenttwo";
    $table->startRow();
    $table->addCell($checkbox->show(), "20");
    $table->addCell($labelOutcomesandAssessmentsTwo);
    $table->endRow();

    // Outcomes and Assessments - Page Three
    $checkbox->name = "outcomesandassessmentthree";
    $table->startRow();
    $table->addCell($checkbox->show(), "20");
    $table->addCell($labelOutcomesandAssessmentsThree);
    $table->endRow();

    // Resources
    $checkbox->name = "resources";
    $table->startRow();
    $table->addCell($checkbox->show(), "20");
    $table->addCell($labelResources);
    $table->endRow();

    // Collaborations and Contracts
    $checkbox->name = "collaborations";
    $table->startRow();
    $table->addCell($checkbox->show(), "20");
    $table->addCell($labelCollaborationsandContracts);
    $table->endRow();

    // Review
    $checkbox->name = "review";
    $table->startRow();
    $table->addCell($checkbox->show(), "20");
    $table->addCell($labelReview);
    $table->endRow();

    // Comments
    $checkbox->name = "comments";
    $table->startRow();
    $table->addCell($checkbox->show(), "20");
    $table->addCell($labelComments);
    $table->endRow();

    // Feedback
    $checkbox->name = "feedback";
    $table->startRow();
    $table->addCell($checkbox->show(), "20");
    $table->addCell($labelFeedback);
    $table->endRow();

    $button = new button();

    $button = new button('Print Document', $this->objLanguage->languageText('mod_apo_printdf', 'apo', 'Print Document'));
    $button->setToSubmit();

    $table->startRow();
    $table->addCell($button->show());
    $table->endRow();

    $myFieldset = new fieldset();
    $myFieldset->width = "50%";
    $myFieldset->setLegend($displayMessage);
    $myFieldset->addContent($table->show());

    $action = "makepdf";
    $form = new form('makepdf', $this->uri(array('action' => $action, 'id' => $document['id'])));
    $form->addToForm($myFieldset->show());
    echo $form->show();
?>