<?php
/**
* Template to display submitted/marked essays.
* @package essayadmin
*/

// Avoid undefined variable crap
if (!isset($topic)) {
    $topic=NULL;
}


$this->objDateformat =  $this->newObject('dateandtime', 'utilities');
$this->objFile= $this->newObject('upload','filemanager');

$this->loadClass('htmltable','htmlelements');
$this->loadClass('link','htmlelements');
$this->loadClass('layer','htmlelements');

// set up html elements
$objTable= new htmltable();
$objTable2= new htmltable();
$objLink = new link();
$objLayer = new layer();
$objPop = $this->newObject('windowpop','htmlelements');

// Set up language items
$studentno = ucfirst($this->objLanguage->code2Txt('mod_essayadmin_studentno','essayadmin'));
$studenthead=ucfirst($this->objLanguage->code2Txt('mod_essayadmin_student','essayadmin'));
$topichead=$this->objLanguage->languageText('mod_essayadmin_topic','essayadmin');
$essayhead=$this->objLanguage->languageText('mod_essayadmin_essay','essayadmin');
//$essayhead.=' '.$this->objLanguage->languageText('mod_essayadmin_title','essayadmin');
$submithead=$this->objLanguage->languageText('mod_essayadmin_datesubmitted','essayadmin');
$markhead=$this->objLanguage->languageText('mod_essayadmin_mark','essayadmin').' (%)';
$btnexit=$this->objLanguage->languageText('word_exit');
$head=$this->objLanguage->languageText('mod_essayadmin_submitted','essayadmin')
          .' '.$this->objLanguage->languageText('mod_essayadmin_essays', 'essayadmin')
          .' '.$this->objLanguage->languageText('mod_essayadmin_in', 'essayadmin')
          .' '.$topic;
$titledownload=$this->objLanguage->languageText('mod_essayadmin_downloadessay','essayadmin'); //.' '.$this->objLanguage->languageText('mod_essayadmin_essay', 'essayadmin');
$titleupload=$this->objLanguage->languageText('mod_essayadmin_upload','essayadmin').' '.$this->objLanguage->languageText('mod_essayadmin_marks', 'essayadmin').' '.$this->objLanguage->languageText('mod_essayadmin_and','essayadmin').' '.$this->objLanguage->languageText('mod_essayadmin_marked','essayadmin').' '.$this->objLanguage->languageText('mod_essayadmin_essay','essayadmin');
$topiclist=$this->objLanguage->languageText('word_back').' '.strtolower($this->objLanguage->languageText('word_to')).' '.$topichead;
$topichome=$this->objLanguage->languageText('mod_essayadmin_name', 'essayadmin').' '.$this->objLanguage->languageText('word_home');
$noessays=$this->objLanguage->languageText('mod_essayadmin_nosubmittedessays', 'essayadmin');
$rubricLabel = $this->objLanguage->languageText('mod_rubric_name', 'rubric');

/**
* new language items added 20/mar/06
* @author: otim samuel, sotim@dicts.mak.ac.ug
*/

//$unmarked=0;
$unmarked=$this->objLanguage->languageText('mod_essayadmin_unmarked','essayadmin');
//$markrow=0;
$markrow=$this->objLanguage->languageText('mod_essayadmin_mark','essayadmin');
//$closingdate=0;
$closingdate=$this->objLanguage->languageText('mod_essayadmin_closedate','essayadmin');
//$downloadEssays=0;
$downloadEssays=$this->objLanguage->languageText('mod_essayadmin_downloadessays', 'essayadmin');

/****************** set up table headers ************************/

$tableHd=array();
$tableHd[]=$studentno;
$tableHd[]=$studenthead;
$tableHd[]=$essayhead;
$tableHd[]=$submithead;
$tableHd[]=$markhead;
$tableHd[]='';

$objTable->cellspacing=2;
$objTable->cellpadding=2;

$objTable->addHeader($tableHd,'heading');

/******************** set up table data ***********************/

if(!empty($data)){
    $i=0;
    foreach($data as $item){
        $class = ($i++%2) ? 'even':'odd';

        // if essay submitted: allow download
        if($item['studentfileid']){
            if (!is_null($item['submitdate'])) {
                $submitdate = $this->objDateformat->formatDate($item['submitdate']);
            } else {
                $submitdate = '*internal error*';
            }
            if($item['mark']){
                $mark=$item['mark'].'%';
            }else{
    			$uriMark = 0;
    			$uriMark = $this->uri(array('action'=>'upload','book'=>$item['id'],'id'=>$item['topicid']));
    			$this->objLink = new link($uriMark);
    			$this->objLink->link=$markrow;
                $mark=$unmarked.'<br>'.$this->objLink->show();
            }
            $this->objIcon->setIcon('download');
        	$this->objIcon->extra='';
            $this->objIcon->title=$titledownload;
        	$this->objLink = new link($this->uri(array('action'=>'download','fileid'=>$item['studentfileid'])));
        	$this->objLink->link=$this->objIcon->show();
            $loadicons=$this->objLink->show();
            /*
        	$this->objIcon->setIcon('submit2');
        	$this->objIcon->title=$titleupload;
        	$uriUp = $this->uri(array('action'=>'upload','book'=>$item['id'],'id'=>$item['topicid']));
        	$objLink = new link($uriUp);
        	$objLink->link=$this->objIcon->show();
        	//$loadicons.='&nbsp;&nbsp;&nbsp;&nbsp;'.$this->objLink->show();
            */
        } else {
            $submitdate = $this->objLanguage->languageText('mod_essayadmin_bookedbutnotyetsubmitted','essayadmin');
            $mark = $this->objLanguage->languageText('mod_essayadmin_bookedbutnotyetsubmitted','essayadmin');
            $loadicons=$this->objLanguage->languageText('mod_essayadmin_bookedbutnotyetsubmitted','essayadmin');
        }

        /*
        $uriUp = $this->uri(array('action'=>'upload','book'=>$item['id'],'id'=>$item['topicid']));
        $objLink = new link($uriUp);
        $objLink->link = $item['student'];
        $objLink->title = $titleupload;
        $studentLink = $objLink->show();
        */

        $objTable->startRow();
        /*
        $objTable->addCell($studentLink,'','','',$class);
		$objTable->addCell($item['essay'],'','','',$class);
        $objTable->addCell($this->objDateformat->formatDate($item['submitdate']),'','','',$class);
        $objTable->addCell($mark,'','','center',$class);
        $objTable->addCell($loadicons,'','','center',$class,' colspan=2');
        */
        $objTable->addCell($item['studentNo'],'','','',$class);
        $objTable->addCell($item['student'],'','','',$class);
        $objTable->addCell($item['essay']/*$topicdata[0]['name']*/,'','','',$class);
        $objTable->addCell($submitdate,'','','',$class);
        $objTable->addCell($mark,'','','',$class);
        $objTable->addCell($loadicons,'','','',$class);
        $objTable->endRow();
		//add the binary data for the zipped file
		//$zippedStudent = 0;
		//$zippedStudent = eregi_replace("'","",$item['student']);
		//$zippedStudent = eregi_replace(" ","",$zippedStudent);
		/**
		* using the same algorithms as are found within download_page_tpl.php
		* populate the variable $filedata which contains the binary data for the essay
		*/
        /*
		$fId=0;
		$fId=$item['studentfileid'];
		$fdata=0;
		//$fdata=$this->objFile->getArray("select * from tbl_essay_filestore where fileId='$fId'");
		if (count($fdata)==0){ // if the file has been deleted
			$filedata = "No data found!";
		} else {
			$fname=0;
			$fname=$fdata[0]['filename'];
			//get the extension
			$fext=0;
			$farray=array();
			$farray=explode(".",$fname);
			$fext=$farray[count($farray)-1];
			$fsize=0;
			$fsize=$fdata[0]['size'];
			$ftype=0;
			$ftype=$fdata[0]['filetype'];
			$fId2=0;
			$fId2=$fdata[0]['fileId'];
			//$flist=$this->objFile->getArray("select id from tbl_essay_blob where fileId='$fId2' order by segment");

			$line=array();
			foreach ($flist as $line)
			{
				$id=0;
				$id=$line['id'];
				$ffiledata=array();
				$ffiledata=$this->objFile->getArray("select * from tbl_essay_blob where id='$id'");
				$filedata = $ffiledata[0]['filedata'];
			}
		}
        */
    }
}else{
    $objTable->startRow();
    $objTable->addCell($noessays,'','','','noRecordsMessage','colspan="6"');
    $objTable->endRow();
}

/*
$objTable->row_attributes='height="10"';
$objTable->startRow();
$objTable->addCell('');
$objTable->endRow();
*/

/******************* Display table *******************/

//show the due date for this essay
//echo '<br><strong>'.$closingdate.':</strong> '.$this->formatDate($duedate);

echo $objTable->show();

// back to topic area
$strBackToTopicArea = $this->objLanguage->languageText('mod_essayadmin_backtotopicarea','essayadmin');
$objLink->link($this->uri(array('action'=>'view','id'=>$topicdata[0]['id'])));
$objLink->link = $strBackToTopicArea;
$objLink->title = $strBackToTopicArea;
$link1 = $objLink->show();

// essay home
$strHome = $this->objLanguage->languageText('mod_essayadmin_home', 'essayadmin');
$objLink->link($this->uri(array()));
$objLink->link = $strHome;
$objLink->title = $strHome;
$link2 = $objLink->show();

/*
//download submitted essays
$filename = 0;
//$filename = $essayadminpath.$zippedTopic.date("Y-m-d-Hms").".zip";

$fileUploader = $this->getObject('fileuploader', 'files');

// Set the Upload Restriction
$fileUploader->allowedCategories = array('documents', 'images');

// Set folder/path in usrfiles to save file
// If the path does not exist, the class will create it for you
$fileUploader->savePath = '/etd/essayadmin/'; // This will then be saved in usrfiles/etd/december

// Set whether to overwrite file
$fileUploader->overwriteExistingFile = TRUE;

// Upload. Returns result as an array
$results = $fileUploader->uploadFile('fileupload1'); // This corresponds with the name of the input -
//<input type="file"  name="fileupload1" />;

//$fd = fopen($filename, "wb");
//$out = fwrite ($fd, $this->objZip->file());
//fclose ($fd);

//make a record of this file
//$this->objDbZip->insertData($zippedTopic.date("Y-m-d-Hms").".zip",$essayadminpath.$zippedTopic."/".$zippedTopic.date("Y-m-d-Hms").".zip",$essayadminDownloadLink.$zippedTopic."/".$zippedTopic.date("Y-m-d-Hms").".zip");

//$this->objLink->link("$essayadminDownloadLink$zippedTopic".date("Y-m-d-Hms").".zip");
*/

/*
$objLink->link =  $downloadEssays;
$link3 = 0;
$link3 = $objLink->show();
*/

/*
$objLayer->align = 'center';
$objLayer->str = $link3.'&nbsp;&nbsp;&nbsp;&nbsp;'.$link2.'&nbsp;&nbsp;&nbsp;&nbsp;'.$link1;
echo $objLayer->show();
*/

echo $link1
    .'<br />'.$link2;

if($this->rubric){
    $objPop->resizable = 'yes';
    $objPop->scrollbars = 'yes';
    $objPop->set('location', $this->uri(array(),'rubric'));
    $objPop->set('linktext',$rubricLabel);
    echo '<br />'.$objPop->show();
}

?>