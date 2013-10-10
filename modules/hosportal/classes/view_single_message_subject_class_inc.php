<?php
// include_once 'chisimba_modules_handler_class_inc.php';
 // include_once 'language_module_class_inc.php';
 //  include_once 'object_core_module_class_inc.php';

class view_single_message_subject extends object
{
 //public $objLanguage;
    private $AllReplies;
    private $objAllReplies;
 private $objouputtext;
 private $objDBComment;
private $objbuildform;
private $objTopForm;
private $objMiddleForm;
private $objBottomForm;
private $objTitle;
private $objLabel;
private $objTextArea;
private $objLink;
private $objIcon;
 private $messagesAuthorTable;
 private $messagesSubjectTable;
 private $objHTMLAuthorTable;
 private $objHTMLSubjectTable;
 private $messagesCommentsTable;
 private $objHTMLCommentsTable;
         private $author;
        private $title;
       private $comment;
       private $modified;
       private $noOfReplies;
       private $id;

private $no_of_elements;
private $no_of_desired_messages_per_page;
private $number_of_pages;
private $page_number_array;
private $last_page_boolean_value;
public $page_number;

 public function init()
 {
//    $this->objObject = new object_core_module('object_core_module','hosportal');
//     $n = new A();

  //Instantiate the language object
 // $this->objLanguage = $this->getObject('language','language');
  $this->objouputtext = $this->getObject('language_module','hosportal');
  //Load the DB object
  $this->objDBComment = $this->getObject('dbhosportal_messages','hosportal');
  //Load form object
  $this->objbuildform = $this->getObject('form_module','hosportal');
  //Load text input object
  $this->objTitle = $this->getObject('textinput_module','hosportal');
  //Load label object
  $this->objLabel = $this->getObject('label_module','hosportal');
  //load text area object
  $this->objTextArea = $this->getObject('textarea_module','hosportal');
  //load button object
  $this->objBuildButton = $this->getObject('button_module','hosportal');
       //instatiate HTML author table module object
    $this->objHTMLAuthorTable = $this->getObject('htmltable_module','hosportal');
           //instatiate HTML subject table module object
    $this->objHTMLSubjectTable = $this->getObject('htmltable_module','hosportal');
           //instatiate HTML comments table module object
    $this->objHTMLCommentsTable = $this->getObject('htmltable_module','hosportal');
      //instatiate link object
  $this->objLink = $this->getObject('link_module','hosportal');
    //instatiate a icon object
  $this->objIcon = $this->getObject('icon_module','hosportal');
  $this->objAllReplies = $this->getObject('dbhosportal_replies','hosportal');
  $this->objDBComments = $this->getObject('dbhosportal_messages','hosportal');
   //instatiate form module object
  $this->objbuildform = $this->getObject('form_module','hosportal');
     //instatiate HTML table module object
  $this->objHTMLTable = $this->getObject('htmltable_module','hosportal');
  //instatiate confirm object
  $this->objConfirm = $this->getObject('confirm_module','hosportal');

  //instatiate a user object
 $this->objUser = $this->getObject('user_module','hosportal');
 // $this->no_of_desired_messages_per_page = 2;
 $this->no_of_elements = 0;
 $this->last_page_boolean_value = 0;
 $this->page_number = 0;
 }

public function setNoOfDesiredMessagesPerPage($no_of_desired_messages_per_page)
{
    if ($no_of_desired_messages_per_page >=1 && $no_of_desired_messages_per_page <=50)
        {
    $this->no_of_desired_messages_per_page = $no_of_desired_messages_per_page;
        }
}

 public function setIdForSingleOriginalMessage($id)
 {
     $this->id = $id;

 }

 public function getPageNumber()
{

    return $this->page_number;
}
public function setPageNumber($page_number)
{
    $this->page_number = $page_number;

}

 private function buildForm()
 {

       //Create the form
       $this->objTopForm=$this->objbuildform->createNewObjectFromModule('Author',$this->getFormAction());
     

      $id = $this->id;

        $commentData = $this->objDBComment->listSingle($id);
        $this->id = $commentData[0]["id"];
       $this->author = $commentData[0]["userid"];
        $this->title = $commentData[0]["title"];
        $this->comment = $commentData[0]["commenttxt"];
        $this->modified = $commentData[0]["modified"];
        $this->noOfReplies = $commentData[0]["replies"];


        //Next we add the text box for the title of the comment to the form with;

        //...........TEXT INPUT.......................
        //Create a new textinput for the title of the comment
      $objTitleField = $this->objTitle->createNewObjectFromModule('title',$title);

      $temp_array = array('DATE' => $this->modified);
      $templ_array_reply = array('REPLIES'=> $this->noOfReplies);

            $titlelabel = $this->objLabel->createNewObjectFromModule($this->objouputtext->insertTextFromVariables("mod_hosportal_dateposted","hosportal",$temp_array),"title");
             $replylabel = $this->objLabel->createNewObjectFromModule($this->objouputtext->insertTextFromVariables("mod_hosportal_noofreplies","hosportal",$templ_array_reply),"replies");

   $this->objform= $this->objbuildform->addObjectToForm($titlelabel->show(). "<br />");
   $this->objform= $this->objbuildform->addObjectToForm($replylabel->show(). "<br />");



$this->messagesAuthorTable = $this->objHTMLAuthorTable->createNewObjectFromModule("htmltable", "htmlelements");

  //Define the table border
   $this->messagesAuthorTable = $this->objHTMLAuthorTable->setBorderThickness(0);

  //Set the table spacing
     $this->messagesAuthorTable = $this->objHTMLAuthorTable->setCellPadding(5);

  //Set the table width
       $this->messagesAuthorTable = $this->objHTMLAuthorTable->setCellWidth("30%");

 $this->messagesAuthorTable = $this->objHTMLAuthorTable->beginHeaderTableRow();
 $this->messagesAuthorTable = $this->objHTMLAuthorTable->addHeaderCellWithObject($this->objouputtext->insertTextFromConfigFile("mod_hosportal_author"));
 $this->messagesAuthorTable = $this->objHTMLAuthorTable->endHeaderTableRow();
        $this->messagesAuthorTable = $this->objHTMLAuthorTable->beginTableRow();
   $this->messagesAuthorTable = $this->objHTMLAuthorTable->addCellwithObject($this->author);
   $this->messagesAuthorTable = $this->objHTMLAuthorTable->endTableRow();
$this->objTopForm= $this->objbuildform->addObjectToForm( $this->messagesAuthorTable = $this->objHTMLAuthorTable->showBuiltTable());

        return $this->objTopForm = $this->objbuildform->showBuiltForm();
 }
 private function buildMiddleForm()
 {
       $this-> objMiddleForm = $this->objbuildform->createNewObjectFromModule('Subject',$this->getFormAction());
       $this->messagesSubjectTable = $this->objHTMLSubjectTable->createNewObjectFromModule("htmltable", "htmlelements");

  //Define the table border
   $this->messagesSubjectTable = $this->objHTMLSubjectTable->setBorderThickness(0);

  //Set the table spacing
     $this->messagesSubjectTable = $this->objHTMLSubjectTable->setCellPadding(5);

  //Set the table width
       $this->messagesSubjectTable = $this->objHTMLSubjectTable->setCellWidth("50%");

   $this->messagesSubjectTable = $this->objHTMLSubjectTable->beginHeaderTableRow();
 $this->messagesSubjectTable = $this->objHTMLSubjectTable->addHeaderCellWithObject($this->objouputtext->insertTextFromConfigFile("mod_hosportal_title"));
 $this->messagesSubjectTable = $this->objHTMLSubjectTable->endHeaderTableRow();

      $this->messagesAuthorTable = $this->objHTMLAuthorTable->beginTableRow();
   $this->messagesAuthorTable = $this->objHTMLAuthorTable->addCellwithObject($this->title);

   $this->messagesAuthorTable = $this->objHTMLAuthorTable->endTableRow();

 $this->objMiddleForm= $this->objbuildform->addObjectToForm( $this->messagesSubjectTable = $this->objHTMLSubjectTable->showBuiltTable());
        return $this->objMiddleForm = $this->objbuildform->showBuiltForm();
 }

 private function buildBottomForm()
 {


        $this-> objBottomForm = $this->objbuildform->createNewObjectFromModule('Comments',$this->getFormAction());
       $this->messagesCommentsTable = $this->objHTMLCommentsTable->createNewObjectFromModule("htmltable", "htmlelements");

  //Define the table border
   $this->messagesCommentsTable = $this->objHTMLCommentsTable->setBorderThickness(0);

  //Set the table spacing
     $this->messagesCommentsTable = $this->objHTMLCommentsTable->setCellPadding(5);

  //Set the table width
       $this->messagesCommentsTable = $this->objHTMLCommentsTable->setCellWidth("70%");

   $this->messagesCommentsTable = $this->objHTMLCommentsTable->beginHeaderTableRow();
 $this->messagesCommentsTable = $this->objHTMLCommentsTable->addHeaderCellWithObject($this->objouputtext->insertTextFromConfigFile("mod_hosportal_commenttitle"));
 $this->messagesCommentsTable = $this->objHTMLCommentsTable->endHeaderTableRow();

      $this->messagesCommentsTable = $this->objHTMLCommentsTable->beginTableRow();
   $this->messagesCommentsTable = $this->objHTMLCommentsTable->addCellwithObject($this->comment);

   $this->messagesCommentsTable = $this->objHTMLCommentsTable->endTableRow();



  $iconBackSelect = $this->objIcon->setIconType('prev');


  $iconBackSelect = $this->objIcon->setAltTextForIcon($this->objouputtext->insertTextFromConfigFile("mod_hosportal_goback"));

    $mngBacklink = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>'viewForum'
   )));

   $mngBacklink = $this->objLink->embedLinkToObject($iconBackSelect = $this->objIcon->showIcon()) ;

   $linkBackManage = $mngBacklink = $this->objLink->showLink();
      $iconReplySelect = $this->objIcon->setIconType('reply');

  $iconReplySelect = $this->objIcon->setAltTextForIcon($this->objouputtext->insertTextFromConfigFile("mod_hosportal_reply"));

    $mngReplylink = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>'reply',
    'id' => $this->id
   )));

   $mngReplylink = $this->objLink->embedLinkToObject($iconReplySelect = $this->objIcon->showIcon()) ;


   $linkReplyManage = $mngReplylink = $this->objLink->showLink();
      $this->messagesCommentsTable = $this->objHTMLCommentsTable->beginTableRow();

   //Note we are using column span. The other four parameters are set to default
       $this->messagesCommentsTable = $this->objHTMLCommentsTable->addCellwithObject($linkReplyManage.'  '.$linkBackManage);

    $this->messagesCommentsTable = $this->objHTMLCommentsTable->endTableRow();
    $this->objBottomForm= $this->objbuildform->addObjectToForm( $this->messagesCommentsTable = $this->objHTMLCommentsTable->showBuiltTable());
        return $this->objBottomForm = $this->objbuildform->showBuiltForm();
 }

  private function getNumberofSearchedReplies($searchValue)
  {
   return $this->no_of_elements = $this->objAllReplies->getNoOfSearchedReplies($this->title,$searchValue);
  }
  public function searchReplies($search_value)
{
  return $this->AllReplies =  $this->objAllReplies->searchReplies($this->title,$search_value,$this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
}
public function sortReplies($type_of_sort)
{

switch ($type_of_sort)
        {

            case 'sortAuthorsAscendingOrder': return $this->AllReplies =  $this->objAllReplies->sortByAuthorAtoZ($this->title,$this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
            break;
            case 'sortAuthorsDescendingOrder': return $this->AllReplies =  $this->objAllReplies->sortByAuthorZtoA($this->title,$this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
            break;
            case 'sortByLatestModifiedMessages': return $this->AllReplies = $this->objAllReplies->sortByLatestModified($this->title,$this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
            break;
//            case 'sortByOldestModifiedMessages': return $this->AllReplies = $this->objAllReplies->sortByLatestModified($this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
//            break;
            case 'sortByOldestModifiedMessages': return $this->AllReplies =  $this->objAllReplies->sortByOldestModified($this->title, $this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
            break;
            default:
                return $this->AllReplies = $this->objAllReplies->sortByLatestModified($this->title,$this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
            break;
               // $this->clickedAdd=$this->getParam('clickedadd');

                //return "home_tpl.php";
        }
}
private function getNumberofReplies()
{
   foreach($this->AllReplies as $thisComment)
           {
    $id = $thisComment["id"];
   $userid = $thisComment["userid"];
   $replytitle = $thisComment["title"];
   $commenttxt = $thisComment["commenttxt"];
   $modified = $thisComment["modified"];
   $unreplied = $thisComment["unreplied"];
   $no_of_replies = $thisComment["replies"];
if (($this->title == $replytitle) && ($no_of_replies < 1) && ($unreplied == FALSE))
    {
$this->no_of_elements++;


    }
  }
  return $this->no_of_elements;
}
private function getPaginationParameters()
{
  $this->AllReplies = $this->objAllReplies->listAll();
        $searchBoolean = $this->getParam('searchBoolean');
        if ($searchBoolean==TRUE)
      {
$searchValue = $this->getParam('searchValue');
$this->no_of_elements = $this->getNumberofSearchedReplies($searchValue);
  }
  else
  {


    $this->no_of_elements = $this->getNumberofReplies();
  }
//  $this->no_of_elements = $this->getNumberofReplies();
$this->last_page_boolean_value = $this->no_of_elements%$this->no_of_desired_messages_per_page;
$this->number_of_pages = $this->no_of_elements/$this->no_of_desired_messages_per_page;
//$this->number_of_pages = ceil($this->number_of_pages);
     $this->page_number_array = array();
// $this->page_number_array[]= $this->number_of_pages;
     for ($x=0;$x<=$this->number_of_pages;$x++)
     {
if ($x == $this->number_of_pages || ($x==0 & $this->number_of_pages== 0))
    {
    if ($this->last_page_boolean_value > 0)
        {
    $this->page_number_array[]= $x;
    break;
        }
    break;
    }

    else
        {
     $this->page_number_array[]=$x;
        }
     }

}
 private function buildReplyForm()
 {
   //     $this->AllReplies = $this->objAllReplies->listAll();
   $this->getPaginationParameters();
//        $searchBoolean = $this->getParam('searchBoolean');
//        if ($searchBoolean==TRUE)
//      {
//$searchValue = $this->getParam('searchValue');
//$this->no_of_elements = $this->getNumberofSearchedReplies($searchValue);
//  }
//  else
//  {
//
//
//    $this->no_of_elements = $this->getNumberofReplies();
//  }
////  $this->no_of_elements = $this->getNumberofReplies();
//$this->last_page_boolean_value = $this->no_of_elements%$this->no_of_desired_messages_per_page;
//$this->number_of_pages = $this->no_of_elements/$this->no_of_desired_messages_per_page;
////$this->number_of_pages = ceil($this->number_of_pages);
//     $this->page_number_array = array();
//// $this->page_number_array[]= $this->number_of_pages;
//     for ($x=0;$x<=$this->number_of_pages;$x++)
//     {
//if ($x == $this->number_of_pages || ($x==0 & $this->number_of_pages== 0))
//    {
//    if ($this->last_page_boolean_value > 0)
//        {
//    $this->page_number_array[]= $x;
//    break;
//        }
//    break;
//    }
//
//    else
//        {
//     $this->page_number_array[]=$x;
//        }
//     }



     $this->objForm=$this->objbuildform->createNewObjectFromModule('comments',$this->getFormAction());
$searchBoolean = $this->getParam('searchBoolean');
  if ($searchBoolean==TRUE)
    {
    $searchValue = $this->getParam('searchValue');
  $this->AllReplies=  $this->searchReplies($searchValue);
}
else
    {
      $type_of_sort = $this->getParam('sortOptions');
   $this->AllReplies=  $this->sortReplies($type_of_sort);
    }

//      $type_of_sort = $this->getParam('sortOptions');
//   $this->AllReplies=  $this->sortReplies($type_of_sort);


    $initial = (($this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page)+1);
 $final = (($this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page)) +($this->no_of_desired_messages_per_page);
//$fiinal = ($initial+($this->no_of_desired_messages_per_page-1))-10;

if ($this->page_number == floor($this->number_of_pages))
        {
 $view_messages_limit = "Messages $initial to $this->no_of_elements of $this->no_of_elements";
     //  $view_messages_limit = "messages .$initial. - . $this->no_of_elements.   ";
        }
else
        {
       $view_messages_limit = "Messages $initial - $final of $this->no_of_elements";
   // $view_messages_limit = "messages .$initial. - . $fiinal.   ";
        }

$this->objForm= $this->objbuildform->addObjectToForm('<P ALIGN = "right">'.$view_messages_limit);

   $backwardPage = $this->objIcon->setIconType('prev');


  $backwardPage = $this->objIcon->setAltTextForIcon($this->objouputtext->insertTextFromConfigFile("mod_hosportal_editcomment"));

    $mngBackwardLink = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>'sortnviewreplies',
        'noOfMessages' => $this->no_of_desired_messages_per_page,
    'sortOptions'=> $type_of_sort,
    'pageNumber' => $this->page_number - 1,
             'idSubjectMatter'=> $this->id,
       'searchBoolean' => $searchBoolean,
      'searchValue'=> $searchValue
   )));
       $mngBackwardLink = $this->objLink->embedLinkToObject($backwardPage = $this->objIcon->showIcon()) ;

if ($this->page_number > 0)
        {
   $mngBackwardLink = $this->objLink->showLink();
    $this->objForm= $this->objbuildform->addObjectToForm($mngBackwardLink);
        }


         $forwardPage = $this->objIcon->setIconType('next');


  $forwardPage = $this->objIcon->setAltTextForIcon($this->objouputtext->insertTextFromConfigFile("mod_hosportal_editcomment"));

    $mngForwardLink = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>'sortnviewreplies',
        'noOfMessages' => $this->no_of_desired_messages_per_page,
    'sortOptions'=> $type_of_sort,
    'pageNumber' => $this->page_number + 1,
     'idSubjectMatter'=> $this->id,
       'searchBoolean' => $searchBoolean,
      'searchValue'=> $searchValue
   )));
       $mngForwardLink = $this->objLink->embedLinkToObject($forwardPage = $this->objIcon->showIcon()) ;

if ($this->page_number+1 < ceil($this->number_of_pages))
        {
   $mngForwardLink = $this->objLink->showLink();
    $this->objForm= $this->objbuildform->addObjectToForm($mngForwardLink."</P>");
        }

  // Create a table object

 $this->messagesTable = $this->objHTMLTable->createNewObjectFromModule("htmltable", "htmlelements");
  //Define the table border
   $this->messagesTable = $this->objHTMLTable->setBorderThickness(0);
  //Set the table spacing
     $this->messagesTable = $this->objHTMLTable->setCellPadding(5);
  //Set the table width
       $this->messagesTable = $this->objHTMLTable->setCellWidth("100%");
  //Create the array for the table header
  $tableHeader = array();

        $this->messagesTable = $this->objHTMLTable->beginHeaderTableRow();
        $this->messagesTable = $this->objHTMLTable->addHeaderCellWithObject($this->objouputtext->insertTextFromConfigFile("mod_hosportal_reply"),'','','','','colspan="5"');

     $this->messagesTable = $this->objHTMLTable->beginHeaderTableRow();
        $this->messagesTable = $this->objHTMLTable->addHeaderCellWithObject($this->objouputtext->insertTextFromConfigFile("mod_hosportal_author"));
        $this->messagesTable = $this->objHTMLTable->addHeaderCellWithObject($this->objouputtext->insertTextFromConfigFile("mod_hosportal_comment"));
            $this->messagesTable = $this->objHTMLTable->addHeaderCellWithObject($this->objouputtext->insertTextFromConfigFile("mod_hosportal_edit"));
        $this->messagesTable = $this->objHTMLTable->addHeaderCellWithObject($this->objouputtext->insertTextFromConfigFile("mod_hosportal_delete"));
          $this->messagesTable = $this->objHTMLTable->addHeaderCellWithObject($this->objouputtext->insertTextFromConfigFile("mod_hosportal_lastmodified"));
        $this->messagesTable = $this->objHTMLTable->endHeaderTableRow();


foreach($this->AllReplies as $thisComment){
   //Store the values of the array in variables
   $id = $thisComment["id"];
   $userid = $thisComment["userid"];
   $replytitle = $thisComment["title"];
   $commenttxt = $thisComment["commenttxt"];
   $modified = $thisComment["modified"];
   $unreplied = $thisComment["unreplied"];
   $no_of_replies = $thisComment["replies"];


   //Edit Row
if (($this->title == $replytitle) && ($no_of_replies < 1) && ($unreplied == FALSE))
        {
    $iconEdSelect = $this->objIcon->setIconType('edit');
  $iconEdSelect = $this->objIcon->setAltTextForIcon($this->objouputtext->insertTextFromConfigFile("mod_hosportal_editcomment"));
 
    $mngedlink = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>'editreply',
    'id' => $id
   )));

         if ($this->objUser->getUserFullName() == $userid)
           {
   $mngedlink = $this->objLink->embedLinkToObject($iconEdSelect = $this->objIcon->showIcon()) ;
 
           }
   $linkEdManage = $mngedlink = $this->objLink->showLink();
    //Get the icon object
   //Set the icon name
   $iconDelete = $this->objIcon->setIconType('delete');

$iconDelete = $this->objIcon->setAltTextForIcon($this->objouputtext->insertTextFromConfigFile("mod_hosportal_delete"));


 $iconDelete = $this->objIcon->setIconAlignment(false);
 
   $objConfirm = &$this->objConfirm->createNewObjectFromModule('confirm', 'utilities');

         if ($this->objUser->getUserFullName() == $userid)
           {
   $objConfirm = $this->objConfirm->setConfirmOptions($iconDelete = $this->objIcon->showIcon() , $this->uri(array(
    'module' => 'hosportal',
    'action' => 'deletereply',
    'id' => $id,
    'idSubjectMatter' =>$this->id
    )) , $this->objouputtext->insertTextFromConfigFile("mod_hosportal_suredelete"));
           }

   // Add the table rows.
   $this->messagesTable = $this->objHTMLTable->beginTableRow();
   $this->messagesTable = $this->objHTMLTable->addCellwithObject($userid);
  
   $this->messagesTable = $this->objHTMLTable->addCellwithObject($commenttxt);
   $this->messagesTable = $this->objHTMLTable->addCellwithObject($linkEdManage);

   $this->messagesTable = $this->objHTMLTable->addCellwithObject( $objConfirm = $this->objConfirm->showConfirmMessage());


   $this->messagesTable = $this->objHTMLTable->addCellwithObject($modified);
      $this->messagesTable = $this->objHTMLTable->addCellwithObject($this->no_of_elements);
 //           $this->messagesTable = $this->objHTMLTable->addCellwithObject($this->);
$this->messagesTable = $this->objHTMLTable->addCellwithObject(print_r($this->page_number_array));
   $this->messagesTable = $this->objHTMLTable->endTableRow();


  }
}
  
  $this->objForm= $this->objbuildform->addObjectToForm( $this->messagesTable = $this->objHTMLTable->showBuiltTable());

        return $this->objform = $this->objbuildform->showBuiltForm();


 }
  private function getFormAction()
 {
//  //Get the action to determine if its add or edit

         $action = $this->getParam("action", "editreply");
  if ($action == "editreply")
      {
   //Get the comment id and pass to uri
   $id = $this->getParam("id");
   //$replies = $this->getParam("replies");
   $formAction = $this->uri(array("action" => "updatereply", "id"=>$id), "hosportal" );
  }
    if ($action == "reply")
        {
     $id = $this->getParam("id");
   //$replies = $this->getParam("replies");
   $formAction = $this->uri(array("action" => "addreply", "id"=>$id), "hosportal" );
    }
//    if ($action == "sortnviewreplies")
//        {
//        //$id = $this->getParam("id");
//   //$replies = $this->getParam("replies");
//   $formAction = $this->uri(array("action" => "sortnviewreplies", "id"=>$this->id), "hosportal" );
//
//    }





  return $formAction;


 }



 public function showTopForm()
 {
     
  return $this->buildForm();

 }
 public function showMiddleForm()
 {
     return $this->buildMiddleForm();
 }
 public function showBottomForm()
 {
     return $this->buildBottomForm();
 }
 public function show()
 {
return $this->buildReplyForm();
 }

}

?>
