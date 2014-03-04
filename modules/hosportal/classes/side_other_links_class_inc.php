<?php
//include_once 'set_message_options_handler_class_inc.php';
include_once 'side_navigation_links_handler_class_inc.php';
class side_other_links extends side_navigation_links_handler
{

//protected $noOfDesiredMessagesPerPage;
//private $objSearchForm;
//public function init()
//{
//
// side_navigation_links_handler::init();
//
//}

//public function setNoOfDesiredMessagesPerPage($no_of_desired_messages_per_page)
//{
//    $this->noOfDesiredMessagesPerPage = $no_of_desired_messages_per_page;
//
//
//}

protected  function buildNavigationSwitchMenu()
{
      $this->objForm=$this->objbuildform->createNewObjectFromModule('navigationLinks',$this->getFormAction());

     $switchMenuForumHeading = $this->setGoToForumLink();

$switchMenuTopHeading = $this->setGoToHome();

$switchReportingToolsHeading = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_reportingtoolsheading");
$switchReportingToolsBlockElements = $this->setGoToDashboard().'<br/>'.
        $this->setGoToQQR().'<br/>'.
        $this->setGoToHRIRInfo().'<br/>'.
        $this->setGoToFinanceTools().'<br/>'.
        $this->setGoToIntegrityAssurance().'<br/>'.
        $this->setGoToStudentsReports();

$switchMiscLinksHeading = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_mischeading");
$switchMiscLinksBlockElements = $this->setGoToEfficiencyRatios().'<br/>'.
        $this->setGoToGlossary().'<br/>'.
        $this->setGoToHEMIS().'<br/>'.
        $this->setGoToPolicies();

$switchMIUContactsHeading = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_miucontants");

$switchMIUContactsBlockElements = $this->setGoToMIUContacts().'<br/>'.
        $this->setGoToDVCContacts();

  $switchmenu = $this->objSwitchMenu->createNewObjectFromModule();



//  $blockText = $this->setGoToForumLink().'<br/>'.$this->setGoToHome().'<br/>'.
//  $this->setSortAuthorAscLink("sortnview",NULL,$this->noOfDesiredMessagesPerPage).'<br/>'.
//          $this->setSortAuthorDescLink("sortnview",NULL,$this->noOfDesiredMessagesPerPage).'<br/>'.
//          $this->setSortSubjectMatterAscLink("sortnview",NULL,$this->noOfDesiredMessagesPerPage).'<br/>'.
//          $this->setSortSubjectMatterDescLink("sortnview",NULL,$this->noOfDesiredMessagesPerPage).'<br/>'.
//          $this->setSortLatestModifiedLink("sortnview",NULL,$this->noOfDesiredMessagesPerPage).'<br/>'.
//          $this->setSortOldestModifiedLink("sortnview",NULL,$this->noOfDesiredMessagesPerPage).'<br/>'.
//          $this->setSortMostRepliesLink("sortnview",NULL,$this->noOfDesiredMessagesPerPage).'<br/>'.
//          $this->setSortLeastRepliesLink("sortnview",NULL,$this->noOfDesiredMessagesPerPage).' <br />';

    $switchmenu = $this->objSwitchMenu->addBlockToMenu($switchMenuTopHeading,NULL);
    $switchmenu = $this->objSwitchMenu->addBlockToMenu($switchMenuForumHeading,NULL);
    $switchmenu = $this->objSwitchMenu->addBlockToMenu($switchReportingToolsHeading,$switchReportingToolsBlockElements);
    $switchmenu = $this->objSwitchMenu->addBlockToMenu($switchMiscLinksHeading,$switchMiscLinksBlockElements);
    $switchmenu = $this->objSwitchMenu->addBlockToMenu($switchMIUContactsHeading,$switchMIUContactsBlockElements);
//  $switchmenu->addBlock($switchMenuTopHeading,
//  $link1Manage.'<br/>'.$link2Manage.'<br/>'.$link3Manage.'<br/>'. $link4Manage.'<br/>'. $link5Manage.'<br/>'. $link6Manage.'<br/>'. $link7Manage.'<br/>'. $link8Manage.' <br />');
  //$switchmenu = $this->objSwitchMenu->addBlockToMenu('Title 2', 'Block Text 2 <br /> Block Text 2 <br /> Block Text 2', 'confirm'); // Adds
  $this->objform= $this->objbuildform->addObjectToForm( $switchmenu = $this->objSwitchMenu->showSwitchMenu());
//$this->buildSwitchMenuDropDownOption();
        return $this->objform = $this->objbuildform->showBuiltForm();
}
//protected function buildSwitchMenuDropDownOption()
//{
//     $switchMenu1TopHeading = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_paginationheading");
//
//  //$switchmenu = $this->newObject('switchmenu', 'htmlelements');
//  $switchmenu1 = $this->objSwitchMenu->createNewObjectFromModule();
//$SaveButton = $this->objBuildButton->createNewObjectFromModule('save');
//       //new button('save');
//        //$objButton = new button('save');
//        // Set the button type to submit
//       $SaveButton = $this->objBuildButton->buttonSetToSubmit();
//       // $objButton->setToSubmit();
//        // Use the language object to label button
//        // with the word save
//        $SaveButton=$this->objBuildButton->setButtonLabel('  '.$this->objouputtext->insertTextFromConfigFile("mod_hosportal_paginationbutton").'  ');
//       // $objButton->setValue(' '.$this->objLanguage->languageText("mod_hosportal_savecomment", "hosportal").' ');
//      //  $this->objform= $this->objbuildform->addObjectToForm($this->objBuildButton->showButton());
//$SaveButton = $this->objBuildButton->showButton();
//
//  $blockText = '<P ALIGN = "center">'."View ".$this->setUpNoOfMessagesDropDownOption()." Messages Per Page".'<br/>'. $SaveButton. '</p>';
//
//    $switchmenu1 = $this->objSwitchMenu->addBlockToMenu($switchMenu1TopHeading,$blockText);
//  //$switchmenu->addBlock($switchMenuTopHeading,
// // $link1Manage.'<br/>'.$link2Manage.'<br/>'.$link3Manage.'<br/>'. $link4Manage.'<br/>'. $link5Manage.'<br/>'. $link6Manage.'<br/>'. $link7Manage.'<br/>'. $link8Manage.' <br />');
////  $switchmenu = $this->objSwitchMenu->addBlockToMenu('Title 2', 'Block Text 2 <br /> Block Text 2 <br /> Block Text 2', 'confirm'); // Adds
//  $this->objform= $this->objbuildform->addObjectToForm( $switchmenu1 = $this->objSwitchMenu->showSwitchMenu().'</Br>');
////return $this->objform = $this->objbuildform->showBuiltForm();
//
//
//}
//protected function buildSearchField()
//{
//      $this->objSearchForm=$this->objbuildform->createNewObjectFromModule('searchOptions',$this->getSearchFormAction());
//       $switchMenu1TopHeading = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_searchheading");
//
//  //$switchmenu = $this->newObject('switchmenu', 'htmlelements');
//  $switchmenu1 = $this->objSwitchMenu->createNewObjectFromModule();
//     $objTitleField = $this->objTitle->createNewObjectFromModule('comment',$comment);
//        //$objTitle = new textinput('title', $title);
//        //Create a new label for the text labels
//       //  $titlelabel = new label ()
//        //$titlelabel = new label ($this->objLanguage->languagetext("mod_hosportal_commenttitle","hosportal"),"title");
//       // $titlelabel = new label ($this->objouputtext->insertTextFromConfigFile("mod_hosportal_commenttitle"),"title");
//
//        $titlelabel = $this->objLabel->createNewObjectFromModule($this->objouputtext->insertTextFromConfigFile("mod_hosportal_searchlabel"),"comment");
//        //$titlelabel = new label ($this->objouputtext->insert_text_from_config_file(),"title");
//       // $titlelabel = new label ($this->objouputtext->insert_text_from_config_file("mod_hosportal_commenttext","hosportal"),"title");
//    $titlelabel=  $titlelabel->show();
//       // $objForm->addToForm($titlelabel->show() . "<br />");
//    $objTitleField =  $objTitleField->show();
//
//
//    $SaveButton = $this->objBuildButton->createNewObjectFromModule('save');
//       //new button('save');
//        //$objButton = new button('save');
//        // Set the button type to submit
//       $SaveButton = $this->objBuildButton->buttonSetToSubmit();
//       // $objButton->setToSubmit();
//        // Use the language object to label button
//        // with the word save
//        $SaveButton=$this->objBuildButton->setButtonLabel('  '.$this->objouputtext->insertTextFromConfigFile("mod_hosportal_searchbutton").'  ');
//       // $objButton->setValue(' '.$this->objLanguage->languageText("mod_hosportal_savecomment", "hosportal").' ');
//      //  $this->objform= $this->objbuildform->addObjectToForm($this->objBuildButton->showButton());
//$SaveButton = $this->objBuildButton->showButton();
//    $blockText =   '<P ALIGN = "center">'.$titlelabel.'<br/>'. $objTitleField.'<br/>'."View ".$this->setUpNoOfMessagesDropDownOption()." Results Per Page".'<br/>'.$SaveButton. '</p>';
//$switchmenu1 = $this->objSwitchMenu->addBlockToMenu($switchMenu1TopHeading,$blockText);
//$this->objSearchForm= $this->objbuildform->addObjectToForm( $switchmenu1 = $this->objSwitchMenu->showSwitchMenu().'</Br>');
////$this->objSearchForm= $this->objbuildform->addObjectToForm( $switchmenu1 = $this->objSwitchMenu->showSwitchMenu().'</Br>');
//return $this->objSearchForm = $this->objbuildform->showBuiltForm();
//
//}
//public function showBuiltSearchMenu()
//{
//return $this->buildSearchField();$this->buildSwitchMenu();
//
//}
public function showBuiltSwitchMenu()
{
      return $this->buildNavigationSwitchMenu();
}


//private function getSearchFormAction()
// {
//    return  $formAction = $this->uri(array("action" => "searchForComment", 'noOfMessages'=>$this->noOfDesiredMessagesPerPage,'searchBoolean'=> TRUE), "hosportal" );
// }
//
 private function getFormAction()
 {


  // return  $formAction = $this->uri(array("action" => "setNoOfMessagesPerPage", 'noOfMessages'=>$this->noOfDesiredMessagesPerPage), "hosportal" );


  //return $formAction;

 }

}
?>
