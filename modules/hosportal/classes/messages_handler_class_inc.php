<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
die("you cannot view this page directly");
}

class messages_handler extends controller
{



public $objlanguage;
public $objDBComments;
public $objDBreplies;
public $objDBOriginalMessages;
private $objReplyOptions;
private $objViewReplies;
//private $noOfMessagesPerPage;
//private $objMessageOptions;
private $objOriginalMessageOptions;
public function init()
{

  $this->objLanguage = $this->getObject('language_module','hosportal');

  $this->objDBComments = $this->getObject('dbhosportal_messages','hosportal');
  
  $this->objDBreplies = $this->getObject('dbhosportal_replies','hosportal');
  $this->objDBOriginalMessages = $this->getObject('dbhosportal_original_messages','hosportal');
  $this->objReplyOptions = $this->getObject('set_reply_options','hosportal');
  $this->objOriginalMessageOptions = $this->getObject('set_original_message_options','hosportal');

  $this->objViewReplies = $this->getObject('view_single_message_subject','hosportal');

}

 public function messagesCurrentAction($action ='viewForum')
 {

   $action=$action;

   $method = $this->__getMethod($action);


   return $this->$method();

 }
 private function __validAction(& $action)
 {
   if (method_exists($this, "__".$action)) {
     return TRUE;
   } else {
     return FALSE;
   }
 }
 private function __getMethod( $action)
 {
     if ($this->__validAction($action)) {
         return "__" . $action;
     } else {
         return "__actionError";
     }
 }
 private function __actionError()
 {
     //Get action from query string
     $action=$this->getParam('action');
     $this->setVar('str', "<h3>"
       . $this->objLanguage->insertTextFromConfigFile("mod_hosportal_unrecognized")
       ." : ". $action . "</h3>");
     return 'dump_tpl.php';
 }
 private function __add()
 {
     return 'editadd_tpl.php';
 }
 private function __viewForum()
 {
     $this->objOriginalMessageOptions->setNoOfDesiredMessagesPerPage(4);
    $this->setVar('sortOptions', 'sortByLatestModifiedMessages');
         $this->setVar('noOfMessages', '4');

     return 'listall_tpl.php';
 }
// private function __forwardPagination()
// {
//
//   $sortOptions = $this->getParam('sortOptions');
// }
 private function __searchForReplies()
 {
$id = $this->getParam('idSubjectMatter');
$comment = $this->getParam('comment');
      $search_boolean = $this->getParam('searchBoolean');
     $no_of_desired_messages_per_page = $this->getParam('noOfMessagesDropDown');
      return   $this->nextAction("viewSortedReplies",array('id'=>$id,'searchValue' => $comment,'sortOptions'=> $sortOptions,'pageNumber' => $page_number,'noOfMessages' => $no_of_desired_messages_per_page,'searchBoolean'  => $search_boolean ));
 }
 private function __searchForComment()
 {

     $comment = $this->getParam('comment');
      $search_boolean = $this->getParam('searchBoolean');
     $no_of_desired_messages_per_page = $this->getParam('noOfMessagesDropDown');
    // $this->objOriginalMessageOptions->setNoOfDesiredMessagesPerPage( $no_of_desired_messages_per_page);
      return   $this->nextAction("viewSortedMessages",array('searchValue' => $comment, 'sortOptions'=> $sortOptions,'pageNumber'=> $page_number,'noOfMessages' => $no_of_desired_messages_per_page,'searchBoolean' => $search_boolean ));
 }
private function __setNoOfMessagesPerPage()
 {
     $sortOptions = $this->getParam('sortOptions');
      $page_number = $this->getParam('pageNumber');
     $no_of_desired_messages_per_page = $this->getParam('noOfMessagesDropDown');

      return   $this->nextAction("viewSortedMessages",array('sortOptions'=> $sortOptions,'pageNumber'=> $page_number,'noOfMessages' => $no_of_desired_messages_per_page,'searchBoolean' => FALSE ));
 }
 private function __setNoOfRepliesPerPage()
 {
      $id = $this->getParam('idSubjectMatter');
       $sortOptions = $this->getParam('sortOptions');
      $page_number = $this->getParam('pageNumber');
     $no_of_desired_messages_per_page = $this->getParam('noOfMessagesDropDown');
     $this->objReplyOptions->setNoOfDesiredMessagesPerPage( $no_of_desired_messages_per_page);

     $this->objViewReplies->setIdForSingleOriginalMessage($id);

$this->objViewReplies->setPageNumber($page_number);
$this->objReplyOptions->setSubjectMatterId($id);
//     $this->setVar('sortOptions', $sortOptions);

       //$page_number = $this->getParam('pageNumber');
      //$objListSortedMessages = $this->getObject('view_all_messages', 'hosportal');
//$this->setVar('sortOptions', $sortOptions);
       //$this->setVar('id', $id);
//$a=0;
       return   $this->nextAction("viewSortedReplies",array('id'=>$id,'sortOptions'=> $sortOptions,'pageNumber' => $page_number,'noOfMessages' => $no_of_desired_messages_per_page,'searchBoolean' => FALSE ));


//         $sortOptions = $this->getParam('sortOptions');
//      $page_number = $this->getParam('pageNumber');
//     $no_of_desired_messages_per_page = $this->getParam('noOfMessagesDropDown');
//    // $this->objReplyOptions->setNoOfDesiredMessagesPerPage( $no_of_desired_messages_per_page);
//     $this->objReplyOptions->setNoOfDesiredMessagesPerPage($no_of_desired_messages_per_page);
//     //$this->noOfMessagesPerPage =$no_of_desired_messages_per_page;
//    //  $this->objViewOriginalMessages->setNoOfDesiredMessagesPerPage($no_of_desired_messages_per_page);
//      //$objListSortedMessages = $this->getObject('view_all_messages', 'hosportal');
////$this->setVar('sortOptions', $sortOptions);
////$this->objViewOriginalMessages->setPageNumber($page_number);
//        //return 'listsortedreplies_tpl.php';
//        return   $this->nextAction("viewSortedReplies",array('noOfMessages' => $no_of_desired_messages_per_page));
 }
 private function __sortnview()
 {
      $sortOptions = $this->getParam('sortOptions');
      $searchValue = $this->getParam('searchValue');
      $page_number = $this->getParam('pageNumber');
      $search_boolean = $this->getParam('searchBoolean');
     $no_of_desired_messages_per_page = $this->getParam('noOfMessages');
     $this->objOriginalMessageOptions->setNoOfDesiredMessagesPerPage( $no_of_desired_messages_per_page);
     // $this->objViewOriginalMessages->setNoOfDesiredMessagesPerPage($no_of_desired_messages_per_page);
      //$objListSortedMessages = $this->getObject('view_all_messages', 'hosportal');
//$this->setVar('sortOptions', $sortOptions);
//$this->objViewOriginalMessages->setPageNumber($page_number);
        //return 'listsortedreplies_tpl.php';
        return   $this->nextAction("viewSortedMessages",array('sortOptions'=>$sortOptions, 'pageNumber'=>$page_number,'noOfMessages' => $no_of_desired_messages_per_page, 'searchBoolean'=> $search_boolean, 'searchValue' => $searchValue));
      //echo $sortOptions;
 }
  private function __viewSortedMessages()
 {
    $search_value = $this->getParam('searchValue');
     $sortOptions = $this->getParam('sortOptions');
     $search_boolean = $this->getParam('searchBoolean');
     $page_number = $this->getParam('pageNumber');
     $no_of_desired_messages_per_page = $this->getParam('noOfMessages');
    $this->objOriginalMessageOptions->setNoOfDesiredMessagesPerPage( $no_of_desired_messages_per_page);
    $this->setVar('searchBoolean', $search_boolean);
    $this->setVar('searchValue', $search_value);
     $this->setVar('noOfMessages', $no_of_desired_messages_per_page);
          $this->setVar('sortOptions', $sortOptions);
     $this->setVar('pageNumber', $page_number);
     return 'listall_tpl.php';
 }
 private function __sortnviewreplies()
 {
      $id = $this->getParam('idSubjectMatter');
            $searchValue = $this->getParam('searchValue');
       $sortOptions = $this->getParam('sortOptions');
      $page_number = $this->getParam('pageNumber');
     $no_of_desired_messages_per_page = $this->getParam('noOfMessages');
           $search_boolean = $this->getParam('searchBoolean');
     $this->objReplyOptions->setNoOfDesiredMessagesPerPage( $no_of_desired_messages_per_page);
    // $no_of_desired_messages_per_page = $this->objReplyOptions->getNoOfDesiredMessagesPerPage();
     $this->objViewReplies->setIdForSingleOriginalMessage($id);

   //   $this->objViewReplies->setNoOfDesiredMessagesPerPage($no_of_desired_messages_per_page);
      //$objEditForm->setIdForSingleOriginalMessage($id);
      //$objListSortedMessages = $this->getObject('view_all_messages', 'hosportal');
//$this->setVar('sortOptions', $sortOptions);
$this->objViewReplies->setPageNumber($page_number);
$this->objReplyOptions->setSubjectMatterId($id);
//     $this->setVar('sortOptions', $sortOptions);

       //$page_number = $this->getParam('pageNumber');
      //$objListSortedMessages = $this->getObject('view_all_messages', 'hosportal');
//$this->setVar('sortOptions', $sortOptions);
       //$this->setVar('id', $id);
       return   $this->nextAction("viewSortedReplies",array('id'=>$id,'sortOptions'=> $sortOptions,'pageNumber'=> $page_number,'noOfMessages' => $no_of_desired_messages_per_page, 'searchBoolean'=> $search_boolean,'searchValue'=> $searchValue ));
   // return   'listsingle_tpl.php';
 }
 private function __viewSortedReplies()
 {
   $id = $this->getParam('id');
       $search_value = $this->getParam('searchValue');
   // $this->objReplyOptions->setSubjectMatterId($id);
    $sortOptions = $this->getParam('sortOptions');
          $page_number = $this->getParam('pageNumber');
     $no_of_desired_messages_per_page = $this->getParam('noOfMessages');
$search_boolean = $this->getParam('searchBoolean');
    $this->objViewReplies->setIdForSingleOriginalMessage($id);
      $this->objViewReplies->setPageNumber($page_number);
      //$objEditForm->setIdForSingleOriginalMessage($id);
      //$objListSortedMessages = $this->getObject('view_all_messages', 'hosportal');
//$this->setVar('sortOptions', $sortOptions);
$this->objViewReplies->setPageNumber($page_number);
$this->objReplyOptions->setSubjectMatterId($id);
$this->objReplyOptions->setNoOfDesiredMessagesPerPage( $no_of_desired_messages_per_page);
   // $title = $this->getParam('title');
   // $comments = $this->getParam('commenttxt');
    $this->setVar('id', $id);
   // $this->objReplyOptions->setSubjectMatterId($id);
    $this->setVar('sortOptions', $sortOptions);
         $this->setVar('noOfMessages', $no_of_desired_messages_per_page);
      $this->setVar('pageNumber', $page_number);
               $this->setVar('searchBoolean', $search_boolean);
    $this->setVar('searchValue', $search_value);
    //$this->setVar('title', $title);
    //$this->setVar('commenttxt', $comments);
     //   $modified = $this->objDBComments->recordEntryTime();
    //Update the comment
    //$id = $this->objDBComments->updateSingle($id,$title,$comments);

    return "listsingle_tpl.php";
 }
 private function __listsinglesubject()
 {
            $id = $this->getParam('id');
//                 $title = $this->getParam('title');
//    $comments = $this->getParam('commenttxt');
   // $title = $this->getParam('title');
   // $comments = $this->getParam('commenttxt');
            $this->objReplyOptions->setSubjectMatterId($id);
    $this->setVar('id', $id);

    return   'listsingle_tpl.php';

 }

 private function __addnew()
 {
    //Use getParam to fetch form data

    $title = $this->getParam('title');
    $comments = $this->getParam('commenttxt');
   // $noofreplies = $this->getParam('replies');
    $noofreplies = 0;
    $unreplied = TRue;
    $allComments = $this->objDBComments->listAll();
    foreach($allComments as $thisComment){
   //Store the values of the array in variables
  // $id = $thisComment["id"];
   //$userid = $thisComment["userid"];
   $existngTitle = $thisComment["title"];
   //$commenttxtshort = $thisComment["commenttxtshort"];
  // $modified = $thisComment["modified"];
   //$unreplied = $thisComment["unreplied"];
   //$no_of_replies = $thisComment["replies"];
   if ($existngTitle == $title)
       {
           $this->setErrorMessage("Subject of Message is already chosen. Please insert a new unique title for your message!");
 //$this->putMessages();
//


   $id = $this->getParam('id');
   $this->setVar('id', $id);
 return "editadd_tpl.php";
       }

    }
        if($title == NULL)
        {
    $this->setErrorMessage("Please enter a title for your message!");
 //$this->putMessages();
//


   $id = $this->getParam('id');
   $this->setVar('id', $id);
 return "editadd_tpl.php";
  // return$this->nextAction("errormessage");

        }
       if($comments == NULL)
        {
    $this->setErrorMessage("Please enter your message!");
 //$this->putMessages();
//


   $id = $this->getParam('id');
   $this->setVar('id', $id);
 return "editadd_tpl.php";
  // return$this->nextAction("errormessage");

        }
        else
            {
//     $original = $this->getParam('original');
//     $noofreplies =$this->getParam('replies');

  //  $modified = $this->objDBComments->recordEntryTime();
    //Insert the data to DB
      $id = $this->objDBComments->insertSingleOriginalMessage($id,$title,$comments,$unreplied,$noofreplies);
//            $id= $this->objDBOriginalMessages->insertSingle($title,$comments,$unreplied,$noofreplies);
//    $id = $this->objDBComments->insertSingle($title,$comments,$unreplied,$noofreplies);
    
   // return 'listall_tpl.php';
  return   $this->nextAction("viewForum");
            }
 }
 private function __edit()
 {
    $id = $this->getParam('id');
    $this->setVar('id', $id);
    return "editadd_tpl.php";
 }
 private function __editreply()
 {
     $id = $this->getParam('id');
    $this->setVar('id', $id);
    return "editadd_tpl.php";

 }
 private function __updatereply()
 {
   $idSubjectMatter = $this->getParam('id');
   $id = $idSubjectMatter;
      // $title = $this->getParam('title');
       $comments = $this->getParam('commenttxt');

           //$commentData = $this->objDBreplies->listSingle($id);

       $commentData = $this->objDBComments->listSingle($id);
       $title = $commentData[0]["title"];
           $noofreplies = 0;
    $unreplied = FALSE;
//       $id = $commentData[0]["id"];
//        $title = $commentData[0]["title"];
//        $comments = $commentData[0]["commenttxt"];
     // $noofreplies = $commentData[0]["replies"];
     // $unreplied = $commentData[0]["unreplied"];
     // $noofreplies=$noofreplies+1;
     //   $original = FALSE;

    //Get the form data

   // $title = $this->getParam('title');
    
    //$noofreplies = $this->getParam('replies');
   // $unreplied = $this->getParam('unreplied');
     //   $modified = $this->objDBComments->recordEntryTime();
    //Update the comment
      if($comments == NULL)
        {
    $this->setErrorMessage("Please enter your message!");
 //$this->putMessages();
//


   $id = $this->getParam('id');
   $this->setVar('id', $id);
 return "editadd_tpl.php";
  // return$this->nextAction("errormessage");

        }
        else
       {
  

  //  $id = $this->objDBreplies->updateSingle($id, $title, $comments,$unreplied,$noofreplies);
$id = $this->objDBComments->updateSingleReply($id, $title, $comments,$unreplied,$noofreplies);
  return   $this->nextAction("viewSingleMessages",array('idSubjectMatter'=>$idSubjectMatter));
//$id = $this->objDBreplies->updateSingle($id, $title, $comments,$unreplied,$noofreplies);
//            $id = $this->objDBComments->updateSingle($id, $title, $comments,$unreplied,$noofreplies);
      //return   $this->nextAction("viewSingleMessage");
    //$id = $this->getParam('id');
        //$this->setVar('id', $id);
       // $comment = array("id"=> $id);
     // $comment = array( "id"=>$id);
//       $comment = array("id"=> $id,
//            'userid' => $userid,
//            'title' => $title,
//            'commenttxt' => $comments,
//            'modified'=> $this->now(),
//            'commenttxtshort'=> $comments,
//            'unreplied'=> $unreplied,
//            'replies' => $noofreplies
//        );
   //     $comment = array();
// / $tableHeader[] = $this->objLanguage->languageText("mod_hosportal_title", 'hosportal');
// $comment[] =  $id;
    //    return   $this->nextAction("listsinglesubject",$comment);
//return   $this->nextAction("view");
     //return   $this->uri(array("action" => "viewSingleMessage"), "hosportal");
       // return   $this->nextAction("viewSingleMessage");
    //    listsinglesubject
// return "listsingle_tpl.php";
        }
        //return "listall_tpl.php";
 }
 private function __addreply()
 {
     $idSubjectMatter = $this->getParam('id');
     $id = $idSubjectMatter;
     //$id = $this->getParam('id');

     $commentData = $this->objDBComments->listSingle($id);
       $id = $commentData[0]["id"];
        $title = $commentData[0]["title"];
        $comments = $commentData[0]["commenttxt"];
        $noofreplies = $commentData[0]["replies"];
        $unreplied = $commentData[0]["unreplied"];
        $noofreplies=$noofreplies+1;
        $unreplied = FALSE;
         //$original = FALSE;

    $this->objDBComments->updateSingleOriginalMessage($id, $title, $comments,$unreplied,$noofreplies);

     $id = $this->getParam('id');

     $title = $this->getParam('title');
    $comments = $this->getParam('commenttxt');
      $noofreplies = 0;
    $unreplied = FALSE;

   // $commentData = $this->objDBreplies->listSingle($id);
             $commentData = $this->objDBComments->listSingle($id);
                 $title = $commentData[0]["title"];
//    $noofreplies = $this->getParam('replies');
//    $unreplied = $this->getParam('unreplied');
//     $original = $this->getParam('original');
//     $noofreplies =$this->getParam('replies');

  //  $modified = $this->objDBComments->recordEntryTime();
    //Insert the data to DB
                       if($comments == NULL)
        {
    $this->setErrorMessage("Please enter your message!");
 //$this->putMessages();
//


   $id = $this->getParam('id');
   $this->setVar('id', $id);
 return "editadd_tpl.php";
  // return$this->nextAction("errormessage");

        }
        else
            {
            $this->objDBComments->insertSingleReply($id,$title,$comments,$unreplied,$noofreplies);

   // $id = $this->objDBComments->insertSingle($title,$comments,$unreplied,$noofreplies);
//$id = $this->objDBreplies->insertSingle($title,$comments,$unreplied,$noofreplies);
   // $id = $this->objDBreplies->insertSingle($title,$comments,$unreplied,$noofreplies);
 // return   $this->messagesCurrentAction();
//return $this->__view();
   // $this->setVar('id', $id);
   //    $id = $this->getParam('id');
   // $title = $this->getParam('title');
   // $comments = $this->getParam('commenttxt');
   // $this->setVar('id', $id);
   // $commentData = array("id"=>$id);
     //     return   $this->nextAction("listsinglesubject",$commentData);
            //$this->setVar('id', $id);

            }
//                        $SingleId = array();
//                       $SingelId  array(
//    'module'=>'hosportal',
//    'action'=>'sortnviewreplies',
            
             return   $this->nextAction("viewSingleMessages",array('idSubjectMatter'=>$idSubjectMatter));
   // return "listsingle_tpl.php";
   // return 'listall_tpl.php';
 }
  private function __reply()
 {
    //Get the form data
//    $id = $this->getParam('id');
//    $title = $this->getParam('title');
//    $comments = $this->getParam('commenttxt');
//    $noofreplies = $this->getParam('replies')+1;
//  //  $no_of_repliesa = 1;
//    $original = $this->getParam('original');
//   // $original = FALSE;
//     //   $modified = $this->objDBComments->recordEntryTime();
    //Update the comment
      //=================================================================================
$id = $this->getParam('id');

     $commentData = $this->objDBComments->listSingle($id);
       $id = $commentData[0]["id"];
        $title = $commentData[0]["title"];
        $comments = $commentData[0]["commenttxt"];
        $noofreplies = $commentData[0]["replies"];
        $unreplied = $commentData[0]["unreplied"];
        //$noofreplies=$noofreplies+1;
       // $unreplied = FALSE;
         //$original = FALSE;
    $this->objDBComments->updateSingleOriginalMessage($id, $title, $comments,$unreplied,$noofreplies);
   // $id = $this->objDBComments->updateSingle($id, $title, $comments,$unreplied,$noofreplies);
   //==============================================================================================
    //$id = $this->objDBComments->updateSingle($id, $title, $comments,$unreplied,$noofreplies);
    //$this->setVar('id', $id);
   // $id = $this->objDBComments->insertSingle($title,$comments,$original,$no_of_replies);
      $this->setVar('id', $id);
      //      return   $this->nextAction("add");
    return "editadd_tpl.php";
//return 'listall_tpl.php';
   // return "editadd_tpl.php";
    //$this->__update($no_of_repliesa);
 }
//private function __errormessage()
// {
//    $id = $this->getParam('id');
//     $this->setErrorMessage("please enter title of your comment");
//     $this->putMessages();
//     //  $id = $this->getParam('id');
//  //  $this->setVar('id', $id);
//     //
//     $id = $this->getParam('id');
//    $this->setVar('id', $id);
//// return "editadderror_tpl.php";
//     //      return   $this->nextAction("view");
//    return "editadd_tpl.php";
// }

 private function __update()
 { $id = $this->getParam('id');
       $commentData = $this->objDBComments->listSingle($id);
//       $id = $commentData[0]["id"];
//        $title = $commentData[0]["title"];
//        $comments = $commentData[0]["commenttxt"];
      $noofreplies = $commentData[0]["replies"];
      $unreplied = $commentData[0]["unreplied"];
     // $noofreplies=$noofreplies+1;
     //   $original = FALSE;
        
    //Get the form data
   
    $title = $this->getParam('title');
    $comments = $this->getParam('commenttxt');




//        $allComments = $this->objDBComments->listAll();
//    foreach($allComments as $thisComment){
//   //Store the values of the array in variables
//  // $id = $thisComment["id"];
//   //$userid = $thisComment["userid"];
//   $existngTitle = $thisComment["title"];
//   //$commenttxtshort = $thisComment["commenttxtshort"];
//  // $modified = $thisComment["modified"];
//   //$unreplied = $thisComment["unreplied"];
//   //$no_of_replies = $thisComment["replies"];
//   if ($existngTitle == $title)
//       {
//           $this->setErrorMessage("Subject of Message is already chosen. Please insert a new unique title for your message!");
// //$this->putMessages();
////
//
//
//   $id = $this->getParam('id');
//   $this->setVar('id', $id);
// return "editadd_tpl.php";
//       }
//    }



    if($title == NULL)
        {
    $this->setErrorMessage("Please enter a title for your message!");
 //$this->putMessages();
// 
     
     
   $id = $this->getParam('id');
   $this->setVar('id', $id);
 return "editadd_tpl.php";
  // return$this->nextAction("errormessage");

        }
       if($comments == NULL)
        {
    $this->setErrorMessage("Please enter your message!");
 //$this->putMessages();
//


   $id = $this->getParam('id');
   $this->setVar('id', $id);
 return "editadd_tpl.php";
  // return$this->nextAction("errormessage");

        }
else
{
    //$noofreplies = $this->getParam('replies');
   // $unreplied = $this->getParam('unreplied');
     //   $modified = $this->objDBComments->recordEntryTime();
    //Update the comment
   
   // return "listsingle_tpl.php";
   // $this->setVar('id', $id);

//    $this->setVar('id', $id);
//    return "editadd_tpl.php";

//$a= $this->uri(array("action" => "view"), "hosportal" );
 $id=   $this->objDBComments->updateSingleOriginalMessage($id, $title, $comments,$unreplied,$noofreplies);
    // $this->objDBComments->updateSingle($id, $title, $comments,$unreplied,$noofreplies);
   //  $id = $this->objDBOriginalMessages->updateSingle($id, $title, $comments,$unreplied,$noofreplies);
      return   $this->nextAction("viewForum");
  //  return "listall_tpl.php";
//return  $this->__getMethod('view');
//return $a;
}
   // 
 }
  private function __viewSingleMessages()
 {
    //Get the form data
    $id = $this->getParam('idSubjectMatter');
   
$this->objReplyOptions->setSubjectMatterId($id);
//$this->objViewReplies->setPageNumber(2);
   // $title = $this->getParam('title');
   // $comments = $this->getParam('commenttxt');
$this->objReplyOptions->setNoOfDesiredMessagesPerPage(4);
$this->setVar('id', $id);
    $this->setVar('sortOptions', 'sortByLatestModifiedMessages');
         $this->setVar('noOfMessages', '4');
    
    //$this->setVar('title', $title);
    //$this->setVar('commenttxt', $comments);
     //   $modified = $this->objDBComments->recordEntryTime();
    //Update the comment
    //$id = $this->objDBComments->updateSingle($id,$title,$comments);

    return "listsingle_tpl.php";
 }
  private function __viewSingleMessage()
 {
    //Get the form data
    $id = $this->getParam('id');
    $this->objReplyOptions->setSubjectMatterId($id);
    //$sortOptions = $this->getParam('sortOptions');
    //$this->objViewReplies->setPageNumber($page_number);
//$this->objReplyOptions->setSubjectMatterId($id);
   // $title = $this->getParam('title');
   // $comments = $this->getParam('commenttxt');
    $this->setVar('id', $id);
   // $this->objReplyOptions->setSubjectMatterId($id);
    $this->objReplyOptions->setNoOfDesiredMessagesPerPage(4);
    $this->setVar('sortOptions', 'sortByLatestModifiedMessages');
         $this->setVar('noOfMessages', '4');
  // $this->setVar('pageNumber', 0);
   // $title = $this->getParam('title');
   // $comments = $this->getParam('commenttxt');
    //$this->setVar('id', $id);
   // $this->setVar('sortOptions', $sortOptions);
    //$this->setVar('title', $title);
    //$this->setVar('commenttxt', $comments);
     //   $modified = $this->objDBComments->recordEntryTime();
    //Update the comment
    //$id = $this->objDBComments->updateSingle($id,$title,$comments);
              
    return "listsingle_tpl.php";
 }
 private function __delete()
 {
    //Get the form data
    $id = $this->getParam('id');
    //Delete the comment
$this->objDBComments->deleteSingleOriginalMessage($id);
   // $this->objDBComments->deleteSingle($id);
   // $this->objDBOriginalMessages->deleteSingle($id);
      return   $this->nextAction("viewForum");
    //return "listall_tpl.php";
 }
 private function __deletereply()
 {
    // deleteSingleReply($id);
     $idSubjectMatter = $this->getParam('idSubjectMatter');
       $id_for_delete = $this->getParam('id');
       $commentDataForDelete = $this->objDBComments->listSingle($id_for_delete);
       $title_for_delete = $commentDataForDelete[0]["title"];
       $this->objDBComments->deleteSingleReply($id_for_delete);
       //$this->objDBreplies->deleteSingle($id_for_delete);
   // $this->objDBComments->deleteSingle($id_for_delete);
  $allComments = $this->objDBComments->listAll();
       foreach($allComments as $thisComment)
           {

   //Store the values of the array in variables
   $id = $thisComment["id"];
   //$userid = $thisComment["userid"];
   $replytitle = $thisComment["title"];
   $comments = $thisComment["commenttxt"];
   //$modified = $thisComment["modified"];
   $unreplied = $thisComment["unreplied"];
   $noofreplies = $thisComment["replies"];

   if (($replytitle == $title_for_delete) && ($noofreplies>0))
       {
       $noofreplies = $noofreplies - 1;
//if (($no_of_replies ==0 & $unreplied == true)||($no_of_replies>0 & $unreplied == FALSE))

         $this->objDBComments->updateSingleOriginalMessage($id, $replytitle, $comments,$unreplied,$noofreplies);
               // $this->setVar('id', $id);
    //return "editadd_tpl.php";
                if (($replytitle == $title_for_delete) && ($noofreplies<1) && ($unreplied == FALSE))
           {
           $unreplied = true;
           $this->objDBComments->updateSingleOriginalMessage($id, $replytitle, $comments,$unreplied,$noofreplies);
           }
       }
//       else if (($replytitle == $title_for_delete) && ($noofreplies<1) && ($unreplied == FALSE))
//           {
//           $unreplied = true;
//           $this->objDBComments->updateSingle($id, $replytitle, $comments,$unreplied,$noofreplies);
//           }

           }
    //Delete the comment
//return "listsingle_tpl.php";
           return   $this->nextAction("viewSingleMessages",array('idSubjectMatter'=>$idSubjectMatter));
             //return   $this->nextAction("view");
   // return "listall_tpl.php";

 }

}
?>