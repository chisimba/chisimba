<?php
/**
* repository class extends object
* @package hivaids
* @filesource
*/

// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* repository class
* @author Megan Watson
* @copyright (c) 2007 UWC
* @version 0.1
*/

class repository extends object
{
    /**
    * Constructor method
    */
    public function init()
    {        
        $this->objUser = $this->getObject('user', 'security');
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objFilePreview = $this->getObject('filepreview', 'filemanager');
        $this->objDate = $this->getObject('dateandtime', 'utilities');
        
        $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
        $this->objIcon = $this->newObject('geticon', 'htmlelements');
        $this->loadClass('htmlheading', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textarea', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('windowpop', 'htmlelements');
    }
    
    /**
    * Method to display a form for uploading a video to the repository
    *
    * @access public
    * @return string html
    */
    public function upload($id = NULL, $fileId = NULL, $description = NULL)
    {
        $hdUpload = $this->objLanguage->languageText('mod_hivaids_uploadvideo', 'hivaids');
        $lbDescription = $this->objLanguage->languageText('mod_hivaids_enterdescription', 'hivaids');
        $btnSave = $this->objLanguage->languageText('mod_hivaids_savetorepository', 'hivaids');
        $btnCancel = $this->objLanguage->languageText('word_cancel');
        
        $objHead = new htmlheading();
        $objHead->type = 1;
        $objHead->str = $hdUpload;
        $str = $objHead->show();
        
        $objSelectFile = $this->newObject('selectfile', 'filemanager');
        $objSelectFile->name = 'video';
        $objSelectFile->restrictFileList = array('avi', 'mpg', 'mpg4', 'mp3', 'flv', 'mpeg');
        
        if(!empty($fileId)){
            $objSelectFile->setDefaultFile($fileId);
        }
        $formStr = '<p>'.$objSelectFile->show().'</p>';
        
        $objLabel = new label($lbDescription, 'input_description');
        $objText = new textarea('description', $description);
        $formStr .= '<p>'.$objLabel->show().': <br />'.$objText->show().'</p>';
        
        $objButton = new button('save', $btnSave);
        $objButton->setToSubmit();
        $formStr .= '<p>'.$objButton->show().'&nbsp;&nbsp;&nbsp;';
        
        $objButton = new button('cancel', $btnCancel);
        $objButton->setToSubmit();
        $formStr .= $objButton->show().'</p>';
        
        if(!empty($id)){
            $objInput = new textinput('id', $id, 'hidden');
            $formStr .= $objInput->show();
        }
        
        $objForm = new form('upload', $this->uri(array('action' => 'savevideo')));
        $objForm->addToForm($formStr);
        
        $str .= $objForm->show();
        
        return $str;
    }
    
    /**
    * Method to display a preview of the video
    *
    * @access public
    * @return string html
    */
    public function preview($fileId)
    {
        $lnClose = $this->objLanguage->languageText('word_close');
        
        $str = $this->objFilePreview->previewFile($fileId);
        
        $objLink = new link('#');
        $objLink->extra = 'onclick="javascript: window.close();"';
        $objLink->link = $lnClose;
        $str .= '<p><center>'.$objLink->show().'</center></p>';
        
        return $str;
    }
    
    /**
    * Method to display a preview of the video
    *
    * @access public
    * @param array $data The list of videos - names and descriptions
    * @return string html
    */
    public function listVideos($data)
    {
        $objRound = $this->newObject('roundcorners', 'htmlelements');
        
        $head = $this->objLanguage->languageText('word_videos');
        $lbDate = $this->objLanguage->languageText('phrase_dateloaded');
        $lbSize = $this->objLanguage->languageText('phrase_filesize');
        $lbDownload = $this->objLanguage->languageText('word_download');
        $lbPreview = $this->objLanguage->languageText('phrase_viewpreview');
        $lbNoVideos = $this->objLanguage->languageText('mod_hivaids_novideosavailable', 'hivaids');
        $lbKb = $this->objLanguage->languageText('mod_hivaids_kb', 'hivaids');
        
        $objHead = new htmlheading();
        $objHead->type = 1;
        $objHead->str = $head;
        $str = $objHead->show();
        
//        echo '<pre>'; print_r($data);
        
        if(!empty($data)){
            foreach($data as $item){
                $title = $item['file_name'];
                $filesize = round($item['filesize']/1000);
                $date = $this->objDate->formatDate($item['datecreated']);
                $path = $this->objConfig->getcontentPath().$item['path'];
                
                $vidStr = '<p>'.$item['descript'].'</p>';
                $vidStr .= '<p><b>'.$lbDate.':</b>&nbsp;&nbsp;'.$date.'<br />';
                $vidStr .= '<b>'.$lbSize.':</b>&nbsp;&nbsp;'.$filesize.' '.$lbKb.'</p>';
                
                // links to download / preview
                $objLink = new link($path);
                $objLink->link = $lbDownload;
                $vidStr .= '<p>'.$objLink->show().'&nbsp;&nbsp;|&nbsp;&nbsp;';
                
                $objPop = new windowpop();
                $objPop->set('resizable', 'yes');
                $objPop->set('scrollbars', 'yes');
                $objPop->set('width', '700');
                $objPop->set('height', '400');
                $objPop->set('title', $item['file_name']);
                $objPop->set('window_name', $item['file_name']);
                $objPop->set('linktext', $lbPreview);
                $url = $this->uri(array('action' => 'preview', 'fileid' => $item['file_id']));
                $objPop->set('location', $url);
                
                $vidStr .= $objPop->show().'</p>';
                
                $str .= $this->objFeatureBox->show($title, $vidStr);
            }
        }else{
            $str .= '<p class="noRecordsMessage">'.$lbNoVideos.'</p>';
        }
        
        return $str;
    }
    
    /**
    * Method to display the list of videos in the repository
    *
    * @access public
    * @param array $data The list of videos - names and descriptions
    * @return string html
    */
    public function show($data)
    {
        $head = $this->objLanguage->languageText('mod_hivaids_managevideorepository', 'hivaids');
        $lnUpload = $this->objLanguage->languageText('mod_hivaids_uploadvideo', 'hivaids');
        $lbNoVideos = $this->objLanguage->languageText('mod_hivaids_novideosavailable', 'hivaids');
        $hdName = $this->objLanguage->languageText('phrase_filename');
        $hdDescription = $this->objLanguage->languageText('word_description');
        $hdType = $this->objLanguage->languageText('phrase_filetype');
        $hdSize = $this->objLanguage->languageText('phrase_filesize');
        $lbKb = $this->objLanguage->languageText('mod_hivaids_kb', 'hivaids');
        $lbConfirm = $this->objLanguage->languageText('mod_hivaids_confirmdeletevideo', 'hivaids');
        $lnView = $this->objLanguage->languageText('word_view');

        $objHead = new htmlheading();
        $objHead->type = 1;
        $objHead->str = $head;
        $str = $objHead->show();
        
        if(!empty($data)){
            $objTable = new htmltable();
            $objTable->cellspacing = '2';
            $objTable->cellpadding = '5';
            
            $hdArr = array();
            $hdArr[] = $hdName;
            $hdArr[] = $hdDescription;
            $hdArr[] = $hdType;
            $hdArr[] = $hdSize;
            $hdArr[] = '';
            
            $objTable->addHeader($hdArr);
            
            $class = 'even';
            foreach($data as $item){
                $class = ($class == 'odd') ? 'even' : 'odd';
                
                // edit and delete
                $icons = $this->objIcon->getEditIcon($this->uri(array('action' => 'addvideo', 'id' => $item['vid_id'], 'fileid' => $item['file_id'])));
                $url = array('action' => 'deletevideo', 'id' => $item['vid_id']);
                $icons .= $this->objIcon->getDeleteIconWithConfirm('', $url, 'hivaids', $lbConfirm);
                
                // format size
                $size = $item['filesize'];
                $filesize = round($size/1000).'&nbsp;'.$lbKb; // kilobytes
                
                // generate preview in a pop up window
                $objPop = new windowpop();
                $objPop->set('resizable', 'yes');
                $objPop->set('scrollbars', 'yes');
                $objPop->set('width', '700');
                $objPop->set('height', '400');
                $objPop->set('title', $item['file_name']);
                $objPop->set('window_name', $item['file_name']);
                $objPop->set('linktext', $item['file_name']);
                $url = $this->uri(array('action' => 'preview', 'fileid' => $item['file_id']));
                $objPop->set('location', $url);
                
                $row = array();
                $row[] = $objPop->show(); //$item['file_name'];
                $row[] = $item['descript'];
                $row[] = $item['datatype'];
                $row[] = $filesize;
                $row[] = $icons;
                
                $objTable->addRow($row, $class);
            }
            $str .= $objTable->show();
        }else{
            $str .= '<p class="noRecordsMessage">'.$lbNoVideos.'</p>';
        }
        
        $objLink = new link($this->uri(array('action' => 'addvideo')));
        $objLink->link = $lnUpload;
        $str .= '<p style="padding-top:5px;">'.$objLink->show().'&nbsp;&nbsp;|&nbsp;&nbsp;';
        
        $objLink = new link($this->uri(array('action' => 'videolist')));
        $objLink->link = $lnView;
        $str .= $objLink->show().'<p>';
        return $str;
    }
}
?>