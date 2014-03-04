<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
abstract class set_message_options_handler extends object
{

protected  $objbuildform;
 protected $objDBComments;
 protected $objouputtext;
 protected $objForm;
 protected $messagesTable;
 protected $objHTMLTable;
 protected $objIcon;
 protected $objConfirm;
 protected $objLink;
 protected $objUser;
 protected $objSwitchMenu;
 protected $objDropDownMenu;
 protected $objBuildButton;
// protected $noOfDesiredMessagesPerPage;
public function init()
    {
 $this->objouputtext = $this->getObject('language_module','hosportal');
 //Instantiate the language object
 $this->objDBComments = $this->getObject('dbhosportal_messages','hosportal');
   //instatiate form module object
  $this->objbuildform = $this->getObject('form_module','hosportal');
     //instatiate HTML table module object
  $this->objHTMLTable = $this->getObject('htmltable_module','hosportal');
  //instatiate a icon object
  $this->objIcon = $this->getObject('icon_module','hosportal');
  //instatiate confirm object
  $this->objConfirm = $this->getObject('confirm_module','hosportal');
  //instatiate link object
  $this->objLink = $this->getObject('link_module','hosportal');
  //instatiate a user object
 $this->objUser = $this->getObject('user_module','hosportal');
 $this->objSwitchMenu = $this->getObject('switchmenu_module','hosportal');

$this->objDropDownMenu = $this->getObject('dropdown_module', 'hosportal');
  //load button object
  $this->objBuildButton = $this->getObject('button_module','hosportal');
    //Load text input object
  $this->objTitle = $this->getObject('textinput_module','hosportal');
  //Load label object
  $this->objLabel = $this->getObject('label_module','hosportal');
    }

abstract protected  function buildSwitchMenu();
abstract protected  function buildSwitchMenuDropDownOption();
abstract protected function buildSearchField();
abstract public function showBuiltSwitchMenu();
abstract protected function getSearchFormAction();
abstract protected function getSortandPaginationFormAction();



protected function setUpNoOfMessagesDropDownOption()
{

   // $dd= $this->getObject('dropdown', 'htmlelements');
   $this->objDropDownMenu->createNewObjectFromModule('noOfMessagesDropDown');
   //=&new dropdown('noOfMessagesDropDown');
  // $dd->addOption()    will add a blank option
for ($x=2;$x<11;$x++)
{
     $this->objDropDownMenu->insertOptionIntoDropDown($x,$x);
}
//$this->objDropDownMenu->setBreakSpace('&nbsp;&nbsp;&nbsp;');
//$this->objDropDownMenu->addOption('1','2');
//   $this->objDropDownMenu->addOption('2','5');
   $this->objDropDownMenu->setDefaultOptionForDropDown(4);
  return $this->objDropDownMenu->showBuildDropDownMenu();

//$this->objDropDownMenu->setSelected($this->getSession('mydropdown'));
}

protected function setSortAuthorAscLink($action = 'sortnview', $idSubjectMatter=NULL, $noOfMessages = 1)
{
    $lnkSortAuthorAsc = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_sortauthoracs");

    $mngSortAuthorAscLink = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>$action,
    'sortOptions' => "sortAuthorsAscendingOrder",
     'idSubjectMatter'=> $idSubjectMatter,
        'pageNumber' => 0,
        'noOfMessages'=> $noOfMessages,
        'searchBoolean' => false
   )));
     $mngSortAuthorAscLink = $this->objLink->embedLinkToObject($lnkSortAuthorAsc) ;
   return  $link1Manage = $mngSortAuthorAscLink = $this->objLink->showLink();
}

protected function setSortAuthorDescLink($action = 'sortnview',$idSubjectMatter=NULL,$noOfMessages = 1)
{
     $lnkSortAuthorDesc = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_sortauthordesc");

    $mngSortAuthorDescLink = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>$action,
    'sortOptions' => "sortAuthorsDescendingOrder",
     'idSubjectMatter'=> $idSubjectMatter,
        'pageNumber' => 0,
        'noOfMessages'=> $noOfMessages,
        'searchBoolean' => false
   )));
     $mngSortAuthorDescLink = $this->objLink->embedLinkToObject($lnkSortAuthorDesc) ;
    return $link2Manage = $mngSortAuthorDescLink = $this->objLink->showLink();
}

protected function setSortSubjectMatterAscLink($action = 'sortnview',$idSubjectMatter=NULL, $noOfMessages = 1)
{
       $lnkSortSubjectMatterAsc = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_sortsubjectmatterasc");

    $mngSortSubjectMatterAscLink = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>$action,
    'sortOptions' => "sortSubjectMatterAscendingOrder",
     'idSubjectMatter'=> $idSubjectMatter,
        'pageNumber' => 0,
        'noOfMessages'=> $noOfMessages,
        'searchBoolean' => false
   )));
     $mngSortSubjectMatterAscLink = $this->objLink->embedLinkToObject($lnkSortSubjectMatterAsc) ;
 return    $link3Manage = $mngSortSubjectMatterAscLink = $this->objLink->showLink();
}

protected function setSortSubjectMatterDescLink($action = 'sortnview',$idSubjectMatter=NULL, $noOfMessages = 1)
{
       $lnkSortSubjectMatterDesc = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_sortsubjectmatterdesc");

    $mngSortSubjectMatterDescLink = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>$action,
    'sortOptions' => "sortSubjectMatterDescendingOrder",
     'idSubjectMatter'=> $idSubjectMatter,
        'pageNumber' => 0,
        'noOfMessages'=> $noOfMessages,
        'searchBoolean' => false
   )));
     $mngSortSubjectMatterDescLink = $this->objLink->embedLinkToObject($lnkSortSubjectMatterDesc) ;
   return  $link4Manage = $mngSortSubjectMatterDescLink = $this->objLink->showLink();
}

protected function setSortLatestModifiedLink($action = 'sortnview',$idSubjectMatter=NULL,$noOfMessages = 1)
{
            $lnkSortLatestModified = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_sortlatestmodified");

    $mngSortLatestModified = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>$action,
    'sortOptions' => "sortByLatestModifiedMessages",
     'idSubjectMatter'=> $idSubjectMatter,
        'pageNumber' => 0,
        'noOfMessages'=> $noOfMessages,
        'searchBoolean' => false

   )));
     $mngSortLatestModified = $this->objLink->embedLinkToObject($lnkSortLatestModified) ;
    return $link5Manage = $mngSortLatestModified = $this->objLink->showLink();
}


protected function setSortOldestModifiedLink($action = 'sortnview',$idSubjectMatter=NULL,$noOfMessages=1)
{
                $lnkSortOldestModified = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_sortoldestmodified");

    $mngSortOldestModified = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>$action,
    'sortOptions' => "sortByOldestModifiedMessages",
     'idSubjectMatter'=> $idSubjectMatter,
        'pageNumber' => 0,
        'noOfMessages'=> $noOfMessages,
        'searchBoolean' => false
   )));
     $mngSortOldestModified = $this->objLink->embedLinkToObject($lnkSortOldestModified) ;
  return   $link6Manage = $mngSortOldestModified = $this->objLink->showLink();
}

protected function setSortMostRepliesLink($action = 'sortnview',$idSubjectMatter=NULL, $noOfMessages = 1)
{
                $lnkSortMostReplies = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_sortmostreplies");

    $mngSortMostReplies = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>$action,
    'sortOptions' => "sortByMostReplies",
     'idSubjectMatter'=> $idSubjectMatter,
        'pageNumber' => 0,
        'noOfMessages'=> $noOfMessages,
        'searchBoolean' => false
   )));
     $mngSortMostReplies = $this->objLink->embedLinkToObject($lnkSortMostReplies) ;
   return  $link7Manage = $mngSortMostReplies = $this->objLink->showLink();
}

protected function setSortLeastRepliesLink($action = 'sortnview',$idSubjectMatter=NULL, $noOfMessages = 1)
{
                    $lnkSortLeastReplies = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_sortleastreplies");

    $mngSortLeastReplies = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>$action,
    'sortOptions' => "sortByLeastReplies",
     'idSubjectMatter'=> $idSubjectMatter,
        'pageNumber' => 0,
        'noOfMessages'=> $noOfMessages,
        'searchBoolean' => false
   )));
     $mngSortLeastReplies = $this->objLink->embedLinkToObject($lnkSortLeastReplies) ;
 return    $link8Manage = $mngSortLeastReplies = $this->objLink->showLink();
}

}
?>