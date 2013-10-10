<?php
/* -------------------- googleApi render class ----------------*/


/**
* Class to provide a means to renser the results of google 
* API search results.
* 
* @Author Derek Keats 
*/
class renderSearchResults extends object 
{
    /**
    * @var object $objLanguage: string to hold the language object
    */
    public $objLanguage;
    
    /**
    * @var object $objUser: string to hold the user object
    */
    public $objUser;

    /**
    * Standard init function
    */
    public function init()
    {
        $this->objLanguage = &$this->getObject('language', 'language');
        $this->objUser = &$this->getObject('user', 'security');
    }
    
    /**
    * Method to take an array and output the formatted
    * results
    * 
    * @param array $ar An array or results from the google
    * API search
    * @param integer $next The minimum for the next set to 
    * display
    */
    public function render($ar, $pages, $count)
    {
    
        //Create the page heading
        $searchterm = $this->getParam('searchterm', NULL);
        $objHd = $this->newObject('htmlheading', 'htmlelements'); 
        $str = 'mod_websearch_searchheading';
        $rep = array('searchterm' => "<i>" . $searchterm . "</i>");
        $objHd->str = $this->objLanguage->code2Txt($str, $rep);
        $ret = "<div align=\"center\">" . $objHd->show() . "</div>";
        
        //Put in the search refinement box
        $objSearch = & $this->getObject('search');
        $objSearch->interfaceType = "HORIZONTAL_SIMPLE";
        $ret .= "<div align=\"center\">" . $objSearch->renderGoogleApiForm($searchterm) . "</div>";
    
        //If there are data in the array
        if ( count($ar) > 0 ) {
            //Create an instance of the table object
            $objTable = $this->newObject('htmltable', 'htmlelements');
            $objTable->border = "0";
            $objTable->cellpadding = "4";
            $objTable->cellspacing = "0";
            $start = (int) $this->getParam('start', 0);
            $firstRec = $this->getParam('start', 1);
            $page = $start/10+1;
            $next = (int) $start + 10;
            $prev = (int) $start - 10;
            $last = (int) $count - 10; #doesn't work
            
            
            //Set up the next icon
            $nextIcon = $this->newObject('geticon', 'htmlelements'); 
            $nextIcon->setIcon("next");
            $str='mod_websearch_next';
            $rep=array('searchterm' => $searchterm);
            $nextIcon->alt=$this->objLanguage->code2Txt($str, $rep);
            
            //Set up the prev icon
            $prevIcon = $this->newObject('geticon', 'htmlelements'); 
            $prevIcon->setIcon("prev");
            $str='mod_websearch_prev';
            $rep=array('searchterm' => $searchterm);
            $prevIcon->alt=$this->objLanguage->code2Txt($str, $rep);
            
            //Set up the first icon
            $firstIcon = $this->newObject('geticon', 'htmlelements'); 
            $firstIcon->setIcon("first");
            $first='mod_websearch_first';
            $rep=array('searchterm' => $searchterm);
            $firstIcon->alt=$this->objLanguage->code2Txt($str, $rep);

            //Put in the header row
            $objTable->startHeaderRow();
            $str="mod_websearch_found";
            $arrOfRep= array(
              'count'=> "<b>" . $count . "</b>",
              'pages'=> "<b>" . $pages . "</b>",
              'page'=>"<b>" . $page . "</b>",
              'searchterm'=>"<b>" . $searchterm . "</b>");
            $objTable->addCell($this->objLanguage->code2Txt($str, $arrOfRep ) . ".", NULL, "top", "center");
            //End the table row
            $objTable->endHeaderRow();
            
            //Get the context and set default to lobby
            $objContext =& $this->newObject('dbcontext','context');
            $contextCode=$objContext->getContextCode();
            if ($contextCode==NULL || $contextCode="") {
                $contextCode="lobby";
            }
            
            //Set up the icon for use in making buddy link
            $budIcon = $this->newObject('geticon', 'htmlelements'); 
            $budIcon->setIcon("recommend_buddy");
            $budIcon->alt=$this->objLanguage->languageText("mod_websearch_recbud");
            $budIconGr = $this->newObject('geticon', 'htmlelements'); 
            $budIconGr->setIcon("recommend_buddy_grey");
            $budIconGr->alt=$this->objLanguage->languageText("mod_websearch_recbud_nobuds");
            
            //Set up the icon for use in making lecturer link
            $lecIcon = $this->newObject('geticon', 'htmlelements'); 
            $lecIcon->setIcon("recommend_lec");
            $rep = array('COURSE' => $objContext->getContextCode());
            $lecIcon->alt=$this->objLanguage->code2Txt("mod_websearch_reclec", $rep);
            $lecIconGr = $this->newObject('geticon', 'htmlelements'); 
            $lecIconGr->setIcon("recommend_lec_grey");
            $lecIconGr->alt=$this->objLanguage->languageText("mod_websearch_reclec_notincourse");
            
            //Set up the icon for use in making classmates link
            $stuIcon = $this->newObject('geticon', 'htmlelements'); 
            $stuIcon->setIcon("recommend_stu");
            $rep = array('COURSE' => $objContext->getContextCode());
            $stuIcon->alt=$this->objLanguage->code2Txt("mod_websearch_reccm", $rep);
            $stuIconGr = $this->newObject('geticon', 'htmlelements'); 
            $stuIconGr->setIcon("recommend_stu_grey");
            $stuIconGr->alt=$this->objLanguage->languageText("mod_websearch_reclec_notincourse");
            
            //Set up the icon for making send to chatroom
            $chaIcon = $this->newObject('geticon', 'htmlelements'); 
            $chaIcon->setIcon("recommend_chat");
            $chaIcon->alt=$this->objLanguage->languageText("mod_websearch_recchat");
            $chaIconGr = $this->newObject('geticon', 'htmlelements'); 
            $chaIconGr->setIcon("recommend_chat_grey");
            $chaIconGr->alt=$this->objLanguage->languageText("mod_websearch_recchat_notinchat");
            //Get the popup window HTML elements used for bookmark
            $objPop = $this->newObject('windowpop', 'htmlelements');
            $objPop2 = $this->newObject('windowpop', 'htmlelements');
            $objPop3 = $this->newObject('windowpop', 'htmlelements');
            $objPop4 = $this->newObject('windowpop', 'htmlelements');
            $objPop5 = $this->newObject('windowpop', 'htmlelements');
            
            //Instantiate the highlighter class
            $objHi = $this->getObject('highlight', 'strings');
            
            //Initialize the odd/even row counter
            $rowcount=0;
            $itemCount=0;
            //Loop over the array
            foreach ($ar as $line) {
                //Check if the row is odd or even
                $oddOrEven=($rowcount==0) ? "odd" : "even";
                $itemCount++;
                $curRec = $firstRec + $itemCount;

                //Make a link like google does with numbering
                $url = $line['URL'];
                $link = "<br /><b>" . $curRec 
                  . "</b>.&nbsp;&nbsp;<a href=\"" . $url 
                  . "\" target=\"_blank\">" . $line['title'] . "</a>";
                //Add a row for the link
                $objTable->startRow();
                $objTable->addCell($link, NULL, "top", "left", $oddOrEven);
                //End the table row
                $objTable->endRow();
                
                //Add a row for the snippet
                $snippet = $objHi->show($line['snippet'], $searchterm);
                $objTable->startRow();
                $objTable->addCell($snippet, NULL, "top", "left", $oddOrEven);
                //End the table row
                $objTable->endRow();
                               
                //Add a row for the directoryTitle
                $objTable->startRow();
                $objTable->addCell($line['directoryTitle'], NULL, "top", "left", $oddOrEven);
                //End the table row
                $objTable->endRow();
                
                //Add a row for the summary
                if ( $line['summary'] !== "" ) {
                    $objTable->startRow();
                    $objTable->addCell($line['summary'], NULL, "top", "left", $oddOrEven);
                    //End the table row
                    $objTable->endRow();
                }
                
                //Add a row for the smart links
                $objTable->startRow();
                $objTable->addCell($url . " - " . $line['cached-size'], NULL, "top", "left", $oddOrEven);
                $objTable->endRow();
              
                //-------- The bookmark link
                $bookMark = $this->uri(array(
                  'action' => 'bookmark',
                  'url' => $url,
                  'searchterm' => $searchterm,
                  'title' => urlencode($line['title']),
                  'snippet' => urlencode($line['snippet'])), 'websearch');
                //Make the link a popup
                $objPop->set('location', $bookMark);
                $objPop->set('linktext', $this->objLanguage->languageText("mod_websearch_bookmark"));
                $objPop->set('width','550'); 
                $objPop->set('height','160');
                $objPop->set('left','200');
                $objPop->set('top','200');
                $objPop->putJs();
                
                //-------- The recommend to buddy link
                //Show link only if there are buddies
                $objDbBuddies =& $this->getObject('dbbuddies','buddies');
                $ar = $objDbBuddies->getBuddies($this->objUser->userId());
                if ( count($ar) > 0 ) {
                    $recToBuddy = $this->uri(array(
                      'type' => 'buddy',
                      'url' => $url,
                      'searchterm' => $searchterm,
                      'title' => urlencode($line['title']),
                      'snippet' => urlencode($line['snippet'])), 'recommend');
                    //Make the link a popup
                    $objPop2->set('location', $recToBuddy);
                    $objPop2->set('linktext', $budIcon->show());
                    $objPop2->set('width','700'); 
                    $objPop2->set('height','400');
                    $objPop2->set('left','100');
                    $objPop2->set('top','100');
                    $objPop2->putJs();
                    $recToBuddyLn = $objPop2->show();
                } else {
                    $recToBuddyLn = "<span class=\"dim\">" 
                      . $budIconGr->show() . "</span>";
                }


                //Put a link to send to lecturers and classmates only if in course
                if ($contextCode!=='lobby') {
                    //Show the link to lecturers if they are in a course
                    $recToLecturer = $this->uri(array(
                      'type' => 'lecturer',
                      'url' => $url,
                      'searchterm' => $searchterm,
                      'title' => urlencode($line['title']),
                      'snippet' => urlencode($line['snippet'])), 'recommend');
                    //Make the link a popup
                    $objPop3->set('location', $recToLecturer);
                    $objPop3->set('linktext', $lecIcon->show());
                    $objPop3->set('width','700'); 
                    $objPop3->set('height','400');
                    $objPop3->set('left','100');
                    $objPop3->set('top','100');
                    $objPop3->putJs();
                    $recToLecturerLn = $objPop3->show();
                      
                    //Show the link to classmates if they are in a course
                    $recToClassMates = $this->uri(array(
                      'type' => 'classmate',
                      'url' => $url,
                      'searchterm' => $searchterm,
                      'title' => urlencode($line['title']),
                      'snippet' => urlencode($line['snippet'])), 'recommend');
                    //Make the link a popup
                    $objPop4->set('location', $recToClassMates);
                    $objPop4->set('linktext', $stuIcon->show());
                    $objPop4->set('width','700'); 
                    $objPop4->set('height','400');
                    $objPop4->set('left','100');
                    $objPop4->set('top','100');
                    $objPop4->putJs();
                    $recToClassMatesLn  = $objPop4->show();
                } else {
                    $recToLecturerLn = $lecIconGr->show();
                    $recToClassMatesLn = $stuIconGr->show();
                }
                
                //Put a link to send to chat only if in chat
                $objModelChat =& $this->getObject('model','chat');
                $ary = $objModelChat->getRoomsUserIsIn($this->objUser->userId());
                //If there is at least one element in array then s/he is in chatroom
                if (isset($ary)) {
                    if (count($ary) >= 1) {
                        //Show the link to to chat if they are in a chatroom
                        $recToChat = $this->uri(array(
                          'type' => 'tochat',
                          'url' => $url,
                          'searchterm' => $searchterm,
                          'title' => urlencode($line['title']),
                          'snippet' => urlencode($line['snippet'])), 'recommend');
                    //Make the link a popup
                    $objPop5->set('location', $recToChat);
                    $objPop5->set('linktext', $chaIcon->show());
                    $objPop5->set('width','700'); 
                    $objPop5->set('height','400');
                    $objPop5->set('left','100');
                    $objPop5->set('top','100');
                    $objPop5->putJs();
                    $recToChatLn = $objPop5->show();
                    } else {
                        $recToChatLn = $chaIconGr->show();
                    }
                } else {
                    $recToChatLn =  $chaIconGr->show();
                }
                
                //Put the row with all the links
                $objTable->startRow();
                $objTable->addCell("&nbsp;"
                  . $objPop->show() . "&nbsp;&nbsp;&nbsp;&nbsp;" 
                  . $recToChatLn . "&nbsp;&nbsp;&nbsp;&nbsp;"
                  . $recToLecturerLn . "&nbsp;&nbsp;&nbsp;&nbsp;"
                  . $recToClassMatesLn . "&nbsp;&nbsp;&nbsp;&nbsp;"
                  . $recToBuddyLn . "<br /><br />", NULL, "top", "left", $oddOrEven);
                //End the table row
                $objTable->endRow();
                
                //Set rowcount for bitwise determination of odd or even
                $rowcount=($rowcount==0) ? 1 : 0;
            } #foreach ($ar as $line)
            
            //Set the row colour for the bottom row
            if ($oddOrEven == "odd") {
                $oddOrEven = "even";
            } else { 
                $oddOrEven = "odd";
            }
            
            //Link to get next set
            if ($next < $pages) {
                $nextUrl = $this->uri(
                  array('action' => 'gapi',
                  'callingModule' => $this->getParam('callingModule', NULL),
                  'params' => $this->getParam('params',NULL),
                  'searchterm' => $this->getParam('searchterm',NULL),
                  'start' => $next,
                  'searchengine' => 'googleapi'), 'websearch');
                $nextLink = "&nbsp;<a href=\"" . $nextUrl . "\">" . $nextIcon->show() . "</a>";
             } else {
                $nextLink = NULL;
             }

            //build the previous link
            if ($prev >= 0) {
                //Link to get prev set
                $prevUrl = $this->uri(
                  array('action' => 'gapi',
                  'callingModule' => $this->getParam('callingModule', NULL),
                  'params' => $this->getParam('params',NULL),
                  'searchterm' => $this->getParam('searchterm',NULL),
                  'start' => $prev,
                  'searchengine' => 'googleapi'), 'websearch');
                $prevLink = "&nbsp;<a href=\"" . $prevUrl 
                  . "\">" . $prevIcon->show() . "</a>";
            } else {
                $prevLink=NULL;
            }
            
            //Build the first link
            if ($start == 0) {
                $firstLink=NULL;
            } else {
                //Link to get prev set
                $firstUrl = $this->uri(
                  array('action' => 'gapi',
                  'start' => 0,
                  'callingModule' => $this->getParam('callingModule', NULL),
                  'params' => $this->getParam('params',NULL),
                  'searchterm' => $this->getParam('searchterm',NULL),
                  'searchengine' => 'googleapi'), 'websearch');
                $firstLink = "&nbsp;<a href=\"" . $firstUrl 
                  . "\">" . $firstIcon->show() . "</a>";
            }
            
            //Render output to table row
            $objTable->startRow();
            $str='mod_websearch_pgofpgs';
            $rep=array('page' => "<b>" . $page . "</b>",
              'pages' => "<b>" . $pages . "</b>");
            $objTable->addCell($this->objLanguage->code2Txt($str, $rep) 
              . "&nbsp;&nbsp;&nbsp;&nbsp;"
              . $firstLink . $prevLink . $nextLink, 
              NULL, "top", "left");
            //End the table row
            $objTable->endRow();
            //Show the table
            return $ret . $objTable->show() . "<br />";
        } #if ( count($ar)>0 )
    } # function render

} #end of class
?>