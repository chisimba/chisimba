<?php
/*
* Template to view list of essays for a student.
* @package essay
*/
$ret = "";
$this->loadclass('htmltable','htmlelements');
$this->loadClass('windowpop','htmlelements');
$this->objDateformat = $this->newObject('dateandtime','utilities');

$objLink = new link();
$objIcon = $this->getObject('geticon','htmlelements');

$this->setVar('heading', $this->objLanguage->code2Txt('mod_essay_listofessaysfor', 'essay', array('STUDENT'=>$this->user)));

$objTable = new htmltable();
$objTable->cellspacing=2;
$objTable->cellpadding=5;

$tableHeader=array();
$tableHeader[] = $this->objLanguage->languageText('mod_essay_topicarea','essay');
$tableHeader[] = $this->objLanguage->languageText('mod_essay_essay','essay');
$tableHeader[] = $this->objLanguage->languageText('mod_essay_closedate','essay');
$tableHeader[] = $this->objLanguage->languageText('mod_essay_bypass', 'essay');
$tableHeader[] = $this->objLanguage->languageText('mod_essay_datesubmitted','essay');
$tableHeader[] = $this->objLanguage->languageText('mod_essay_mark','essay');
$tableHeader[] = $this->objLanguage->languageText('mod_essay_upload','essay').' / '.$this->objLanguage->languageText('mod_essay_download','essay');
$objTable->addHeader($tableHeader, 'heading');

if (!empty($data)) {
    $i=0;
    foreach ($data as $item) {
        $class = ($i++%2) ? 'even':'odd';
        if ($item['mark']=='submit') {
            // if essay hasn't been submitted, display submit icon
            // check if closing date has passed
            //echo "[{$item['date']}]";
            //echo "[".date('Y-m-d H:i:s')."]";
            if (date('Y-m-d H:i:s') > $item['date'] && $item['bypass'] == 'NO') {
    	            $mark = '';
    	            $multiLink = $this->objLanguage->languageText('mod_essay_closed', 'essay');
            } else {
                    $objIcon->setIcon('submit2');
                    $objIcon->title = $this->objLanguage->languageText('mod_essay_uploadessay','essay');
    	            $objIcon->extra = '';
    	            $objLink->link($this->uri(array('action'=>'uploadessay', 'bookid'=>$item['id'])));
                    $objLink->link = $objIcon->show();
    	            $mark = '';
    	            $multiLink = $objLink->show();
            }
        } else if (!is_null($item['mark'])) {
            // if mark exists, display mark, download icon and view comments icon
    	    if (!is_null($item['lecturerfileid'])) {
    	        $objIcon->setIcon('download');
    	        $objIcon->title = $this->objLanguage->languageText('mod_essay_downloadessay','essay');
    	        $objIcon->extra = '';
    	        $objLink->link($this->uri(array('action'=>'download','fileid'=>$item['lecturerfileid'])));
    	        $objLink->link = $objIcon->show();
    	        $multiLink = $objLink->show();
            } else {
    	        $multiLink = $this->objLanguage->languageText('mod_essay_nomarkedessayavailable', 'essay');
    		}
            $objIcon->setIcon('comment_view');
            $objIcon->title = $this->objLanguage->languageText('mod_essay_viewcomment','essay');
       	 	$viewCommentIcon = $objIcon->show();
            $objPopup = new windowpop();
        	$objPopup->set('location', $this->uri(array('action'=>'showcomment', 'book'=>$item['id'], 'essay'=>$item['essay'])));
        	$objPopup->set('linktext',$viewCommentIcon);
        	$objPopup->set('width', '600');
        	$objPopup->set('height', '350');
        	$objPopup->set('left', '200');
        	$objPopup->set('top', '200');
            $objPopup->putJs();
            /*
        	//$objPopup=$objPopup->show();
            //$this->objIcon->extra="onclick=\"javascript:window.open('" .$this->uri(array('action'=>'showcomment','book'=>$item['id'],'essay'=>$item['essay']))."', "essaycomment", "width=400", "height=200", "scrollbars=1")\" ";
            //$this->objIcon->title=$commenthead;
            //$this->objLink->link('#');
            //$this->objLink->link=$this->objIcon->show();
            */
            $mark = $item['mark'].'&nbsp;%<br />'.$objPopup->show();
        } else {
            $mark = '';
            $multiLink = $this->objLanguage->languageText('mod_essay_submitted','essay');
        }
        $objTable->startRow();
        $objTable->addCell($item['name'],'','','',$class);
        //$objTable->addCell($item['essayid'],'','','',$class);
        $objTable->addCell($item['essay'],'','','',$class);
        $objTable->addCell($this->objDateformat->formatDate($item['date']),'','','',$class);
    	$objTable->addCell($item['bypass'],'','','',$class);
        if (empty($item['submitdate'])) {
            $objTable->addCell('','','','',$class);
        } else {
            $objTable->addCell($this->objDateformat->formatDate($item['submitdate']),'','','',$class);
        }
        $objTable->addCell($mark,'','','',$class);
        $objTable->addCell($multiLink,'','','center',$class);
        $objTable->endRow();
    }
}
$ret .= $objTable->show();

$objLink->link($this->uri(''));
$objLink->link=$this->objLanguage->languageText('mod_essay_essayhome','essay');
$ret .= $objLink->show();
echo "<div class='essay_main'>$ret</div>";
?>