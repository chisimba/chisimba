<?php
/* ----------- viewassessments_Eportfolio class extends object------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
/**
 * Model class for viewing assessments
 * @author Paul Mungai
 * @copyright 2009 University of the Western Cape
 */
class viewassessments_Eportfolio extends object
{
    /**
     *
     * Intialiser for the viewassessments_Eportfolio controller
     * @access public
     *
     */
    public function init() 
    {
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $objPopup = &$this->loadClass('windowpop', 'htmlelements');
        $this->objForm = $this->newObject('form', 'htmlelements');
        $this->user = $this->objUser->fullname();
        // Get the DB object.
        $this->dbbook = $this->getObject('dbessay_book', 'essay');
    }
    public function viewEssays($data) 
    {
        $this->objDateformat = $this->newObject('dateandtime', 'utilities');
        $this->loadclass('htmltable', 'htmlelements');
        $objPopup = &$this->loadClass('windowpop', 'htmlelements');
        // set up html elements
        //$objTable=$this->objTable;
        $objTable = new htmltable();
        $objLayer = $this->objLayer;
        // set up language items
        $list = $this->objLanguage->languageText('word_list');
        $head = $list . ' ' . $this->objLanguage->languageText('mod_essay_of', 'essay') . ' ' . $this->objLanguage->languageText('mod_essay_essay', 'essay') . ' ' . $this->objLanguage->languageText('word_for') . ' ' . $this->user;
        $topichead = $this->objLanguage->languageText('mod_essay_topic', 'essay');
        $essayhead = $this->objLanguage->languageText('mod_essay_essay', 'essay');
        $datehead = $this->objLanguage->languageText('mod_essay_closedate', 'essay');
        $bypasshead = $this->objLanguage->languageText('mod_essay_bypass', 'essay');
        $submithead = $this->objLanguage->languageText('mod_essay_datesubmitted', 'essay');
        $lblSubmitted = $this->objLanguage->languageText('mod_essay_submitted', 'essay');
        $markhead = $this->objLanguage->languageText('mod_essay_mark', 'essay');
        $submittitle = $this->objLanguage->languageText('mod_essay_upload', 'essay');
        $downloadhead = $this->objLanguage->languageText('mod_essay_download', 'essay');
        $loadhead = $submittitle . ' / ' . $downloadhead;
        $submittitle.= ' ' . $this->objLanguage->languageText('mod_essay_essay', 'essay');
        $downloadhead.= ' ' . $this->objLanguage->languageText('mod_essay_marked', 'essay') . ' ' . $this->objLanguage->languageText('mod_essay_essay', 'essay');
        $commenthead = $this->objLanguage->languageText('word_view') . ' ' . $this->objLanguage->languageText('mod_essay_comment', 'essay');
        $topiclist = $this->objLanguage->languageText('word_back') . ' ' . strtolower($this->objLanguage->languageText('word_to')) . ' ' . $topichead;
        $topichome = $this->objLanguage->languageText('mod_essay_name', 'essay') . ' ' . $this->objLanguage->languageText('word_home');
        $lbClosed = $this->objLanguage->languageText('mod_essay_closed', 'essay');
        /********************* set up table ************************/
        //$this->setVarByRef('heading',$head);
        $tableHd = array();
        $tableHd[] = $topichead;
        $tableHd[] = $essayhead;
        $tableHd[] = $datehead;
        $tableHd[] = $bypasshead;
        $tableHd[] = $submithead;
        $tableHd[] = $markhead;
        $tableHd[] = $loadhead;
        $objTable->row_attributes = 'height="5"';
        $objTable->startRow();
        $objTable->addCell('');
        $objTable->endRow();
        $objTable->cellspacing = 2;
        $objTable->cellpadding = 5;
        $objTable->addHeader($tableHd, 'Heading');
        $objTable->row_attributes = 'height="5"';
        $objTable->startRow();
        $objTable->addCell('');
        $objTable->endRow();
        /********************* display data *************************/
        $i = 0;
        foreach($data as $item) {
            $class = ($i++%2) ? 'even' : 'odd';
            if ($item['mark'] == 'submit') {
                // if essay hasn't been submitted: display submit icon
                // check if closing date has passed
                //        echo "[{$item['date']}]";
                //        echo "[".date('Y-m-d H:i:s')."]";
                if (date('Y-m-d H:i:s') > $item['date'] && $item['bypass'] == 'NO') {
                    $mark = '';
                    $load = $lbClosed;
                } else {
                    $this->objLink->link($this->uri(array(
                        'action' => 'uploadessay',
                        'bookid' => $item['id']
                    )));
                    $this->objIcon->setIcon('submit2');
                    $this->objIcon->extra = '';
                    $this->objIcon->title = $submittitle;
                    $this->objLink->link = $this->objIcon->show();
                    $mark = '';
                    $load = $this->objLink->show();
                }
            } else if ($item['mark']) {
                // if mark exists: display mark and download icon and view comments icon
                if (!is_null($item['lecturerfileid'])) {
                    $this->objLink->link($this->uri(array(
                        'action' => 'download',
                        'fileid' => $item['lecturerfileid']
                    )));
                    $this->objIcon->setIcon('download');
                    $this->objIcon->extra = '';
                    $this->objIcon->title = $downloadhead;
                    $this->objLink->link = $this->objIcon->show();
                    $downlink = $this->objLink->show();
                } else {
                    $downlink = $this->objLanguage->languageText('mod_essay_nomarkedessayavailable', 'essay');
                }
                //$this->objLink->link('#');
                //$this->objIcon->setIcon('comment_view');
                $this->objIcon->title = $commenthead;
                $this->objIcon->setIcon('comment_view');
                $commentIcon = $this->objIcon->show();
                $objPopup = new windowpop();
                $objPopup->set('location', $this->uri(array(
                    'action' => 'showcomment',
                    'book' => $item['id'],
                    'essay' => $item['essay']
                ) , 'essay'));
                $objPopup->set('linktext', $commentIcon);
                $objPopup->set('width', '600');
                $objPopup->set('height', '350');
                $objPopup->set('left', '200');
                $objPopup->set('top', '200');
                $objPopup->putJs(); // you only need to do this once per page
                //$observersEmailPopup=$objPopup->show();
                //       $this->objIcon->extra="onclick=\"javascript:window.open('" .$this->uri(array('action'=>'showcomment','book'=>$item['id'],'essay'=>$item['essay']))."', "essaycomment", "width=400", "height=200", "scrollbars=1")\" ";
                //$this->objIcon->title=$commenthead;
                //$this->objLink->link=$this->objIcon->show();
                $mark = $item['mark'] . '&nbsp;%<br />' . $objPopup->show();
                $load = $downlink;
            } else {
                // if no mark
                $mark = '';
                $load = $lblSubmitted;
            }
            $objTable->startRow();
            $objTable->addCell($item['name'], '', '', '', $class);
            //$objTable->addCell($item['essayid'],'','','',$class);
            $objTable->addCell($item['essay'], '', '', '', $class);
            $objTable->addCell($this->objDateformat->formatDate($item['date']) , '', '', '', $class);
            $objTable->addCell($item['bypass'], '', '', '', $class);
            if (!empty($item['submitdate'])) {
                $objTable->addCell($this->objDateformat->formatDate($item['submitdate']) , '', '', '', $class);
            } else {
                $objTable->addCell('', '', '', '', $class);
            }
            $objTable->addCell($mark, '', '', '', $class);
            $objTable->addCell($load, '', '', 'center', $class);
            $objTable->endRow();
        }
        $objTable->row_attributes = 'height="10"';
        $objTable->startRow();
        $objTable->addCell('');
        $objTable->endRow();
        /********************* display table ************************/
        $essayLabel = '<br></br>' . $objTable->show() . '<br></br>';
        return $essayLabel;
    }
    public function viewEssaysFull($data) 
    {
        $this->objDateformat = $this->newObject('dateandtime', 'utilities');
        $this->loadclass('htmltable', 'htmlelements');
        // set up html elements
        //$objTable=$this->objTable;
        $objTable = new htmltable();
        $objTable->border = 1;
        $objTable->cellspacing = '1';
        $objTable->width = "100%";
        $objLayer = $this->objLayer;
        // set up language items
        $list = $this->objLanguage->languageText('word_list');
        $head = $list . ' ' . $this->objLanguage->languageText('mod_essay_of', 'essay') . ' ' . $this->objLanguage->languageText('mod_essay_essay', 'essay') . ' ' . $this->objLanguage->languageText('word_for') . ' ' . $this->user;
        $topichead = $this->objLanguage->languageText('mod_essay_topic', 'essay');
        $essayhead = $this->objLanguage->languageText('mod_essay_essay', 'essay');
        $datehead = $this->objLanguage->languageText('mod_essay_closedate', 'essay');
        $bypasshead = $this->objLanguage->languageText('mod_essay_bypass', 'essay');
        $submithead = $this->objLanguage->languageText('mod_essay_datesubmitted', 'essay');
        $lblSubmitted = $this->objLanguage->languageText('mod_essay_submitted', 'essay');
        $markhead = $this->objLanguage->languageText('mod_essay_mark', 'essay');
        $submittitle = $this->objLanguage->languageText('mod_essay_upload', 'essay');
        $downloadhead = $this->objLanguage->languageText('mod_essay_download', 'essay');
        $loadhead = $submittitle . ' / ' . $downloadhead;
        $submittitle.= ' ' . $this->objLanguage->languageText('mod_essay_essay', 'essay');
        $essayComment = $this->objLanguage->languageText('mod_essay_comment', 'essay');
        $downloadhead.= ' ' . $this->objLanguage->languageText('mod_essay_marked', 'essay') . ' ' . $this->objLanguage->languageText('mod_essay_essay', 'essay');
        $commenthead = $this->objLanguage->languageText('word_view') . ' ' . $this->objLanguage->languageText('mod_essay_comment', 'essay');
        $topiclist = $this->objLanguage->languageText('word_back') . ' ' . strtolower($this->objLanguage->languageText('word_to')) . ' ' . $topichead;
        $topichome = $this->objLanguage->languageText('mod_essay_name', 'essay') . ' ' . $this->objLanguage->languageText('word_home');
        $lbClosed = $this->objLanguage->languageText('mod_essay_closed', 'essay');
        /********************* set up table ************************/
        //$this->setVarByRef('heading',$head);
        $tableHd = array();
        $tableHd[] = "<b>" . $topichead . "</b>";
        $tableHd[] = "<b>" . $essayhead . "</b>";
        $tableHd[] = "<b>" . $datehead . "</b>";
        $tableHd[] = "<b>" . $bypasshead . "</b>";
        $tableHd[] = "<b>" . $submithead . "</b>";
        $tableHd[] = "<b>" . $markhead . "</b>";
        $tableHd[] = "<b>" . $essayComment . "</b>";
        /*
        $objTable->row_attributes = 'height="5"';
        $objTable->cellspacing = 1;
        $objTable->cellpadding = 1;
        */
        $objTable->addHeader($tableHd, 'Heading', $row_attributes = 'bgcolor="#D3D3D3"');
        $objTable->row_attributes = 'height="5"';
        /********************* display data *************************/
        $i = 0;
        $bgcolor = "#FFFFFF";
        foreach($data as $item) {
            $class = ($i++%2) ? 'even' : 'odd';
            $bgcolor = ($i++%2) ? "#D3D3D3" : "#FFFFFF";
            $i = $i+1;
            if ($item['mark'] == 'submit') {
                // if essay hasn't been submitted: display submit icon
                // check if closing date has passed
                //        echo "[{$item['date']}]";
                //        echo "[".date('Y-m-d H:i:s')."]";
                if (date('Y-m-d H:i:s') > $item['date'] && $item['bypass'] == 'NO') {
                    $mark = '';
                    $load = $lbClosed;
                } else {
                    $this->objLink->link($this->uri(array(
                        'action' => 'uploadessay',
                        'bookid' => $item['id']
                    )));
                    $this->objIcon->setIcon('submit2');
                    $this->objIcon->extra = '';
                    $this->objIcon->title = $submittitle;
                    $this->objLink->link = $this->objIcon->show();
                    $mark = '';
                    $load = $this->objLink->show();
                }
            } else if ($item['mark']) {
                // if mark exists: display mark and download icon and view comments icon
                if (!is_null($item['lecturerfileid'])) {
                    $this->objLink->link($this->uri(array(
                        'action' => 'download',
                        'fileid' => $item['lecturerfileid']
                    )));
                    $this->objIcon->setIcon('download');
                    $this->objIcon->extra = '';
                    $this->objIcon->title = $downloadhead;
                    $this->objLink->link = $this->objIcon->show();
                    $downlink = $this->objLink->show();
                } else {
                    $downlink = $this->objLanguage->languageText('mod_essay_nomarkedessayavailable', 'essay');
                }
                // get comment form booking details
                $comment = $this->dbbook->getBooking("where id='" . $item['id'] . "'", 'comment');
                $notes = $comment[0]['comment'];
                //$observersEmailPopup=$objPopup->show();
                //       $this->objIcon->extra="onclick=\"javascript:window.open('" .$this->uri(array('action'=>'showcomment','book'=>$item['id'],'essay'=>$item['essay']))."', "essaycomment", "width=400", "height=200", "scrollbars=1")\" ";
                //$this->objIcon->title=$commenthead;
                //$this->objLink->link=$this->objIcon->show();
                $mark = $item['mark'] . "%";
                $load = $downlink;
            } else {
                // if no mark
                $mark = '';
                $load = $lblSubmitted;
            }
            $objTable->startRow();
            $objTable->addCell($item['name'], '', '', '', $class, "bgcolor='" . $bgcolor . "'");
            //$objTable->addCell($item['essayid'],'','','',$class);
            $objTable->addCell($item['essay'], '', '', '', $class, "bgcolor='" . $bgcolor . "'");
            $objTable->addCell($this->objDateformat->formatDate($item['date']) , '', '', '', $class, "bgcolor='" . $bgcolor . "'");
            $objTable->addCell($item['bypass'], '', '', '', $class, "bgcolor='" . $bgcolor . "'");
            if (!empty($item['submitdate'])) {
                $objTable->addCell($this->objDateformat->formatDate($item['submitdate']) , '', '', '', $class, "bgcolor='" . $bgcolor . "'");
            } else {
                $objTable->addCell('', '', '', '', $class, "bgcolor='" . $bgcolor . "'");
            }
            $objTable->addCell($mark, '', '', '', $class, "bgcolor='" . $bgcolor . "'");
            $objTable->addCell($notes, '', '', '', $class, "bgcolor='" . $bgcolor . "'");
            $objTable->endRow();
            /*
            $objTable->startRow();
            $objTable->addCell("<b>".$essayComment.": </b>".$notes, '', '', '', $class,'colspan="6"');
            //$objTable->addCell("<b>".$essayComment.": </b></br>".$notes,'', '', '', $class);
            //          $objTable->row_attributes = 'colspan="6"';
            $objTable->endRow();
            */
        }
        //$objTable->row_attributes = 'height="10"';
        /********************* display table ************************/
        $essayLabel = '<br></br>' . $objTable->show() . '<br></br>';
        return $essayLabel;
    }
}
?>
