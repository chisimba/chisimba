<?php
class view_all_messages extends object
{
 //public $objLanguage;
private $objbuildform;
 private $objDBComments;
 private $objouputtext;
 private $objForm;
 private $messagesTable;
 private $objHTMLTable;
 private $objIcon;
 private $objConfirm;
 private $objLink;
 private $objUser;
// private $objComments;
 private $allComments;
 private $objOriginalComments;
private  $objDBOriginalComments;

private $no_of_elements;
private $no_of_desired_messages_per_page;
private $number_of_pages;
private $page_number_array;
private $last_page_boolean_value;
public $page_number;

public function init()
{
 //Instantiate the language object
 //$this->objLanguage = $this->getObject('language','language');
 //instatiate a language mudole object
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

 $this->objDBOriginalComments = $this->getObject('dbhosportal_original_messages','hosportal');
 $this->no_of_desired_messages_per_page = 4;
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
private function loadElements()
{


    $this->allComments = $this->objDBComments->listAll();
    $this->objOriginalComments =  $this->objDBOriginalComments->listAll();
}

private function getNumberofOriginalComments()
{
   foreach($this->objOriginalComments as $thisComment)
           {
   $id = $thisComment["id"];
   $userid = $thisComment["userid"];
   $title = $thisComment["title"];
   $commenttxtshort = $thisComment["commenttxtshort"];
   $comments = $thisComment["commenttxt"];
   $modified = $thisComment["modified"];
   $unreplied = $thisComment["unreplied"];
   $no_of_replies = $thisComment["replies"];

if (($no_of_replies ==0 & $unreplied == true)||($no_of_replies>0 & $unreplied == FALSE))
    {
$this->no_of_elements++;


    }
  }

 return  $this->no_of_elements ;
}
  private function getNumberofSearchedComments($searchValue)
  {
   return $this->no_of_elements = $this->objDBOriginalComments->getNoOfSearchedOriginalMessages($searchValue);
  }
public function getPageNumber()
{

    return $this->page_number;
}
public function setPageNumber($page_number)
{
    $this->page_number = $page_number;

}
private function searchMessages($search_value)
{
  return $this->objOriginalComments =  $this->objDBOriginalComments->searchOriginalMessages($search_value,$this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
}
public function sortMessages($type_of_sort)
{

switch ($type_of_sort)
        {

            case 'sortAuthorsAscendingOrder': return $this->objOriginalComments =  $this->objDBOriginalComments->sortByAuthorAtoZ($this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
            break;
            case 'sortAuthorsDescendingOrder': return $this->objOriginalComments =  $this->objDBOriginalComments->sortByAuthorZtoA($this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
            break;
            case 'sortSubjectMatterAscendingOrder': return $this->objOriginalComments =  $this->objDBOriginalComments->sortBySubjectMatterAtoZ($this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page );
            break;
            case 'sortSubjectMatterDescendingOrder': return $this->objOriginalComments =  $this->objDBOriginalComments->sortBySubjectMatterZtoA($this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
            break;
            case 'sortByLatestModifiedMessages': return $this->objOriginalComments =  $this->objDBOriginalComments->sortByLatestModified($this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
            break;
            case 'sortByOldestModifiedMessages': return $this->objOriginalComments =  $this->objDBOriginalComments->sortByOldestModified($this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
            break;
            case 'sortByMostReplies': return $this->objOriginalComments =  $this->objDBOriginalComments->sortByMostReplies($this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
            break;
            case 'sortByLeastReplies': return $this->objOriginalComments =  $this->objDBOriginalComments->sortByLeastReplies($this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
            break;

            default:
                return $this->objOriginalComments = $this->objDBOriginalComments->sortByLatestModified($this->no_of_desired_messages_per_page,$this->page_number_array[$this->page_number]*$this->no_of_desired_messages_per_page);
            break;
               // $this->clickedAdd=$this->getParam('clickedadd');

                //return "home_tpl.php";
        }
}
private function getPaginationParameters()
{
    $searchBoolean = $this->getParam('searchBoolean');

  if ($searchBoolean==TRUE)
      {
$searchValue = $this->getParam('searchValue');
$this->getNumberofSearchedComments($searchValue);
  }
  else
  {


  $this->getNumberofOriginalComments();
  }

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
private function buildForm()
{
  $this->loadElements();

$this->getPaginationParameters();
  


//  $searchBoolean = $this->getParam('searchBoolean');
//
//  if ($searchBoolean==TRUE)
//      {
//$searchValue = $this->getParam('searchValue');
//$this->getNumberofSearchedComments($searchValue);
//  }
//  else
//  {
//
//
//  $this->getNumberofOriginalComments();
//  }
//
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

  //Create the form
  $this->objForm=$this->objbuildform->createNewObjectFromModule('comments',$this->getFormAction());
   $this->messagesTable = $this->objHTMLTable->createNewObjectFromModule("htmltable", "htmlelements");
//$yahoolink ="sdfdfdfsdfsdfdsfsdfdsfsdfsdfsdfsdfsd  ".'<a href="http://www.yahoo.com" target="_blank">Go to Yahoo</a>';
// $this->objForm= $this->objbuildform->addObjectToForm($yahoolink);
  //Define the table border
   $this->messagesTable = $this->objHTMLTable->setBorderThickness(0);

  //Set the table spacing
     $this->messagesTable = $this->objHTMLTable->setCellPadding(12);

       $this->messagesTable = $this->objHTMLTable->setCellWidth("100%");
       
$searchBoolean = $this->getParam('searchBoolean');
//searchMessages($search_value)
if ($searchBoolean==TRUE)
    {
    $searchValue = $this->getParam('searchValue');
  $this->objOriginalComments=  $this->searchMessages($searchValue);
}
else
    {
  $type_of_sort = $this->getParam('sortOptions');
$this->objOriginalComments=$this->sortMessages($type_of_sort);
    }
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
//($this->page_number > 0)
$this->objForm= $this->objbuildform->addObjectToForm('<P ALIGN = "right">'.$view_messages_limit);

   $backwardPage = $this->objIcon->setIconType('prev');


  $backwardPage = $this->objIcon->setAltTextForIcon($this->objouputtext->insertTextFromConfigFile("mod_hosportal_editcomment"));

    $mngBackwardLink = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>'sortnview',
   'noOfMessages' => $this->no_of_desired_messages_per_page,
    'sortOptions'=> $type_of_sort,
    'pageNumber' => $this->page_number - 1,
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
    'action'=>'sortnview',
   'noOfMessages' => $this->no_of_desired_messages_per_page,
    'sortOptions'=> $type_of_sort,
    'pageNumber' => $this->page_number + 1,
       'searchBoolean' => $searchBoolean,
      'searchValue'=> $searchValue
   )));
       $mngForwardLink = $this->objLink->embedLinkToObject($forwardPage = $this->objIcon->showIcon()) ;

if ($this->page_number+1 < ceil($this->number_of_pages))
        {
   $mngForwardLink = $this->objLink->showLink();
    $this->objForm= $this->objbuildform->addObjectToForm($mngForwardLink.'</P>');
        }


//$this->objOriginalComments=  $this->objDBOriginalComments->paginateAll(1,5);


  //Create the array for the table header
  $tableHeader = array();

 $tableHeader[] =  $this->objouputtext->insertTextFromConfigFile("mod_hosportal_author");
  $tableHeader[] =  $this->objouputtext->insertTextFromConfigFile("mod_hosportal_title");


  $tableHeader[] = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_comment");


  $tableHeader[] = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_edit");



  $tableHeader[] = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_delete");

  $tableHeader[] = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_lastmodified");
   $tableHeader[] = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_viewmore");
      $tableHeader[] = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_numberofreplies");
   $tableHeader[] = $this->objouputtext->insertTextFromConfigFile("mod_hosportal_reply");


  $this->messagesTable = $this->objHTMLTable->addLabelsToHeader($tableHeader, "heading");


foreach($this->objOriginalComments as $thisComment){

   $id = $thisComment["id"];
   $userid = $thisComment["userid"];
   $title = $thisComment["title"];
   $commenttxtshort = $thisComment["commenttxtshort"];
   $modified = $thisComment["modified"];
   $unreplied = $thisComment["unreplied"];
   $no_of_replies = $thisComment["replies"];

   //Edit Row
if (($no_of_replies ==0 & $unreplied == true)||($no_of_replies>0 & $unreplied == FALSE))
    {
    $iconEdSelect = $this->objIcon->setIconType('edit');


  $iconEdSelect = $this->objIcon->setAltTextForIcon($this->objouputtext->insertTextFromConfigFile("mod_hosportal_editcomment"));

    $mngedlink = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>'edit',
    'id' => $id
   )));

         if (($this->objUser->getUserFullName() == $userid) && ($no_of_replies < 1 & $unreplied = TRUE))
           {
   $mngedlink = $this->objLink->embedLinkToObject($iconEdSelect = $this->objIcon->showIcon()) ;

           }
   $linkEdManage = $mngedlink = $this->objLink->showLink();
           

   $iconDelete = $this->objIcon->setIconType('delete');

$iconDelete = $this->objIcon->setAltTextForIcon($this->objouputtext->insertTextFromConfigFile("mod_hosportal_delete"));
 

 $iconDelete = $this->objIcon->setIconAlignment(false);

   $objConfirm = &$this->objConfirm->createNewObjectFromModule('confirm', 'utilities');

      $unreplied = $thisComment["unreplied"];
   $no_of_replies = $thisComment["replies"];
         if (($this->objUser->getUserFullName() == $userid) && ($no_of_replies < 1 & $unreplied = TRUE))
           {
   $objConfirm = $this->objConfirm->setConfirmOptions($iconDelete = $this->objIcon->showIcon() , $this->uri(array(
    'module' => 'hosportal',
    'action' => 'delete',
    'id' => $id
    )) , $this->objouputtext->insertTextFromConfigFile("mod_hosportal_suredelete"));
           }


    //-------------------------------------------------------------

   $iconViewSelect = $this->objIcon->setIconType('view');


  $iconViewSelect = $this->objIcon->setAltTextForIcon($this->objouputtext->insertTextFromConfigFile("mod_hosportal_viewmore"));

    $mngViewlink = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>'viewSingleMessage',
    'id' => $id
   )));


   $mngViewlink = $this->objLink->embedLinkToObject($iconViewSelect = $this->objIcon->showIcon()) ;


   $linkViewManage = $mngViewlink = $this->objLink->showLink();



   //---------------------------------------------------------------------
      $iconReplySelect = $this->objIcon->setIconType('reply');

  $iconReplySelect = $this->objIcon->setAltTextForIcon($this->objouputtext->insertTextFromConfigFile("mod_hosportal_reply"));

    $mngReplylink = $this->objLink->createNewObjectFromModule($this->uri(array(
    'module'=>'hosportal',
    'action'=>'reply',
    'id' => $id
   )));

   $mngReplylink = $this->objLink->embedLinkToObject($iconReplySelect = $this->objIcon->showIcon()) ;

   $linkReplyManage = $mngReplylink = $this->objLink->showLink();


   // Add the table rows.
   $this->messagesTable = $this->objHTMLTable->beginTableRow();
   $this->messagesTable = $this->objHTMLTable->addCellwithObject($userid);
   $this->messagesTable = $this->objHTMLTable->addCellwithObject($title);
     // $this->messagesTable = $this->objHTMLTable->addCellwithObject($no_of_replies);
   $this->messagesTable = $this->objHTMLTable->addCellwithObject($commenttxtshort);
   $this->messagesTable = $this->objHTMLTable->addCellwithObject($linkEdManage);

   $this->messagesTable = $this->objHTMLTable->addCellwithObject( $objConfirm = $this->objConfirm->showConfirmMessage());


   $this->messagesTable = $this->objHTMLTable->addCellwithObject($modified);
   $this->messagesTable = $this->objHTMLTable->addCellwithObject($linkViewManage);
   $this->messagesTable = $this->objHTMLTable->addCellwithObject($no_of_replies);
   
   $this->messagesTable = $this->objHTMLTable->addCellwithObject($linkReplyManage);
  // $this->messagesTable = $this->objHTMLTable->addCellwithObject("no of ele  ".$this->no_of_elements);
 //  $this->messagesTable = $this->objHTMLTable->addCellwithObject("no of pages required   ".$this->number_of_pages);
 //  $this->messagesTable = $this->objHTMLTable->addCellwithObject("last page  ".$this->last_page_boolean_value);
 //   $this->messagesTable = $this->objHTMLTable->addCellwithObject(print_r($this->page_number_array));

   $this->messagesTable = $this->objHTMLTable->endTableRow();

  }
  }

   $iconSelect = $this->objIcon->setIconType('add');

  //Set the alternative text of the icon
   $iconSelect = $this->objIcon->setAltTextForIcon($this->objouputtext->insertTextFromConfigFile("mod_hosportal_addnewcomment"));

  //Create a new link for the add link
  $mnglink =  $this->objLink->createNewObjectFromModule($this->uri(array(
   'module'=>'hosportal',
   'action'=>'add'
  )));

  //Set the link text/image
   $mnglink = $this->objLink->embedLinkToObject($iconSelect = $this->objIcon->showIcon()) ;

  //Build the link

  $linkManage =  $mngedlink = $this->objLink->showLink();

   // Add the table rows.
   $this->messagesTable = $this->objHTMLTable->beginTableRow();
  // $commentsTable->startRow();
   //Note we are using column span. The other four parameters are set to default
 $this->messagesTable = $this->objHTMLTable->addCellwithObject($linkManage,'','','','','colspan="2"');
    $this->messagesTable = $this->objHTMLTable->endTableRow();

  $this->objForm= $this->objbuildform->addObjectToForm( $this->messagesTable = $this->objHTMLTable->showBuiltTable());

        return $this->objform = $this->objbuildform->showBuiltForm();
 }

 private function getFormAction()
 {

       $action = $this->getParam("action", "add");
  if ($action == "edit")
      {
   //Get the comment id and pass to uri
   $id = $this->getParam("id");
   //$replies = $this->getParam("replies");
   $formAction = $this->uri(array("action" => "update", "id"=>$id), "hosportal" );
  }

  if ($action == "reply")
      {
         $id = $this->getParam("id");
   $formAction = $this->uri(array("action" => "reply","id"=>$id), "hosportal");
  }
  if ($action == "add")
      {


  $formAction = $this->uri(array("action" => "add"), "hosportal");


      }


  return $formAction;
 
 }
 public function show()
 {
  return $this->buildForm();
 }
}
?>