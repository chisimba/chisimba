<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
if (!
        /**
        * Description for $GLOBALS
        * @global string $GLOBALS['kewl_entry_point_run']
        * @name   $kewl_entry_point_run
        */
    $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
class viewerutils extends object
{

 
    public function getLatestUpload(){
        $objFiles = $this->getObject('dbwebpresentfiles');
        $objView = $this->getObject("viewer", "webpresent");

        $filename='';
        $latestFile = $objFiles->getLatestPresentation();
        $preview='';
        $fileStr='';
        if (count($latestFile) == 0) {
            $latestFileContent = '';
        } else {
            $latestFileContent = '';
            $objTrim = $this->getObject('trimstr', 'strings');

            $objTrim = $this->getObject('trimstr', 'strings');

            $counter = 0;

            foreach ($latestFile as $file)
            {

                if (trim($file['title']) == '') {
                    $filename = $file['filename'];
                } else {
                    $filename = htmlentities($file['title']);
                }

                $link = new link ($this->uri(array('action'=>'view', 'id'=>$file['id'])));
                $link->link = $objView->getPresentationFirstSlide($file['id'],$filename);
                $preview=$link->show();
                $linkname = $objTrim->strTrim($filename, 45);
                $fileLink = new link ($this->uri(array('action'=>'view', 'id'=>$file['id'])));
                $fileLink->link =$linkname;
                $fileLink->title = $filename;
                $fileStr=$fileLink->show();
            }
        }
          $objLanguage = $this->getObject('language', 'language');
          $featuredPresentationsStr=$objLanguage->languageText("mod_webpresent_featuredpresentation", "webpresent");

          $str='<div id="sidebar" class="c41r">
                   <div class="statstabs">
                   <div class="statslistcontainer">

                   <ul class="paneltabs">
                   <li><a href="javascript:void(0);" class="selected">'.$featuredPresentationsStr.'</a></li>
                   </ul>

                   <ul class="statslist">
                     <li>'.$fileStr.'</li>
                    '.$preview.'
                   </ul>

                   </div>
                   </div>
                   </div>';
        return $str;
    }

public function getMostViewed(){
$objStats = $this->getObject('dbwebpresentviewcounter');
$list = $objStats->getMostViewedList();
$objLanguage = $this->getObject('language', 'language');
$statisticStr=$objLanguage->languageText("mod_webpresent_statistics", "webpresent");
$mostViewedStr=$objLanguage->languageText("mod_webpresent_mostviewed", "webpresent");
    $str='<div class="c15r">
           <div class="subcr">
           <div class="tower">
           <font style="font-size:13pt;color:#5e6eb5;">

          '.$mostViewedStr.'

           </font>
           <p>
           <ul class="statslist">
           '.$list.'
           </ul>
          </p>
          </div>

          </div>
         </div>

';
return $str;
}

public function getMostDownloaded(){
$objStats = $this->getObject('dbwebpresentdownloadcounter');
$list = $objStats->getMostDownloadedList();
$objLanguage = $this->getObject('language', 'language');
$mostDownloadedStr=$objLanguage->languageText("mod_webpresent_mostdownloaded", "webpresent");
    $str='<div class="c15r">
           <div class="subcr">

           <div class="tower">
           <font style="font-size:13pt;color:#5e6eb5;">
            '.$mostDownloadedStr.'
           </font>
           <p>
           <ul class="statslist">
           '.$list.'
           </ul>
          </p>
          </div>

          </div>
         </div>

';
return $str;
}
public function getMostUploaded(){
$objStats = $this->getObject('dbwebpresentuploadscounter');
$list = $objStats->getMostUploadedList();
$objLanguage = $this->getObject('language', 'language');
$mostUploadsStr=$objLanguage->languageText("mod_webpresent_mostuploaded", "webpresent");

    $str='<div class="c15r">
           <div class="subcr">

           <div class="tower">
           <font style="font-size:13pt;color:#0091B9;">
            '.$mostUploadsStr.'
           </font>
           <p>
           <ul class="statslist">
           <li>'.$list.'</li>
           </ul>
          </p>
          </div>

          </div>
         </div>

';
return $str;
}
public function getTagCloudContent($tagCloud){
   $objLanguage = $this->getObject('language', 'language');
   $aboutStr=$objLanguage->languageText("mod_webpresent_aboutstr", "webpresent");
   $aboutWord=$objLanguage->languageText("mod_webpresent_aboutword", "webpresent");
   $cloud= '<div id="sidebar" class="c41r">
                   <div class="statstabs">
                   <div class="statslistcontainer">

                   <ul class="paneltabs">
                  <font style="font-size:13pt;color:#5e6eb5;">'.$aboutWord.'</font>
                   </ul>
                   <br/>
                   <p>
                   '.$aboutStr.'
                   </p>
                   <ul class="paneltabs">
                  <font style="font-size:13pt;color:#5e6eb5;">Tags</font>
                   </ul>
                   <ul class="statslist">
                   <li>'.$tagCloud.'</li>

                   </ul>
                   </div>
                   </div>
                   </div>';
    return $cloud;
}
public function getFeatured(){
        $objFiles = $this->getObject('dbwebpresentfiles');
        $objView = $this->getObject("viewer", "webpresent");
        $this->loadClass('link', 'htmlelements');
        $filename='';
        $latestFile = $objFiles->getLatestPresentation();
        $preview='';
        $fileStr='';
        if (count($latestFile) == 0) {
            $latestFileContent = '';
        } else {
            $latestFileContent = '';
            $objTrim = $this->getObject('trimstr', 'strings');

            $objTrim = $this->getObject('trimstr', 'strings');

            $counter = 0;

            foreach ($latestFile as $file)
            {

                if (trim($file['title']) == '') {
                    $filename = $file['filename'];
                } else {
                    $filename = htmlentities($file['title']);
                }

                $link = new link ($this->uri(array('action'=>'view', 'id'=>$file['id'])));
                $link->link = $objView->getPresentationFirstSlide($file['id'],$filename);
                $preview=$link->show();
                $linkname = $objTrim->strTrim($filename, 45);
                $fileLink = new link ($this->uri(array('action'=>'view', 'id'=>$file['id'])));
                $fileLink->link =$linkname;
                $fileLink->title = $filename;
                $fileStr=$fileLink->show();
            }
        }
    return $preview;
}

    private function createCell($colType,$filename,$thumbNail,$desc,$tags,
        $uploader,$licence,$id){
        $objTrim = $this->getObject('trimstr', 'strings');
        $desc=$objTrim->strTrim($desc,30);
        $descLink = new link ($this->uri(array('action'=>'view', 'id'=>$id)));
        $descLink->link = $desc;
        $str='<div class="'.$colType.'">
              <div class="subcl">
              <div class="sectionstats_content">

              <div class="statslistcontainer">

              <ul class="statslist">

              <li class="sectionstats_first">

              '.$thumbNail->show().'
              <h3>
             '.$descLink->show().'
              </h3>
   <br/><br/></br><br/><br/><br/><br/><br/><br/>
              </li>

              <li><strong>Tags: </strong><a  href="#">'.$tags.'</a></li>
              <li><strong>By: </strong>'.$uploader.'</li>
              '.$licence.'
              </ul>
 <div class="clear"></div>

              </div>
              </div>
              </div>
              </div>';
        return $str;
    }
    
    public function getLatestUploads(){
        $objLanguage = $this->getObject('language', 'language');
        $this->loadClass('link', 'htmlelements');
        $objFiles = $this->getObject('dbwebpresentfiles');
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objUser = $this->getObject('user', 'security');
        $objTags = $this->getObject('dbwebpresenttags');
        $latestFiles = $objFiles->getLatestPresentations();
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $latest10Desc=$objLanguage->languageText("mod_webpresent_latest10desc", "webpresent");
        $latest10Str=$objLanguage->languageText("mod_webpresent_latest10str", "webpresent");
        $fCount = count($latestFiles);
        //die($fCount);
        if ($fCount == 0) {
            $latestFilesContent = '';
        } else {
            $latestFilesContent = '';

            $objTrim = $this->getObject('trimstr', 'strings');
            $content='';
            $counter = 0;
            $homepagetitle=$objSysConfig->getValue('HOME_PAGE_TITLE', 'webpresent');
            
            $title='
           
           <h1>'.$homepagetitle.'</h1>
           
            <ul class="paneltabs">             

          <font style="font-size:13pt;color:#5e6eb5;">

          ' . $latest10Str . '

           </font>
                  </ul>
                   <br/>
                   <p>
                ' . $latest10Desc . '
                   </p>
              ';
            $row='<div class="sectionstats">';
            $row.='<div class="subcolumns">';
            $column=0;
            

            foreach ($latestFiles as $file)
            {
                if (trim($file['title']) == '') {
                    $filename = $file['filename'];
                } else {
                    $filename = htmlentities($file['title']);
                }
                $linkname = $objTrim->strTrim($filename, 45);
                $extra='';
                $columnDiv='';

                if($column == 0){
                    $columnDiv='c50l';
                }else{
                    $columnDiv='c50r';

                }
                $fileLink = new link ($this->uri(array('action'=>'view', 'id'=>$file['id'])));
                $fileLink->link = $objFiles->getPresentationThumbnail($file['id']);
                $fileLink->title = $filename;
                $desc='No description available';
                if ($file['description'] != '') {
                    $desc = ''
                    .nl2br(htmlentities($file['description']));

                }
                $tags = $objTags->getTags($file['id']);
                $tagsStr='';
                if (count($tags) == 0) {
                    $tagsStr .=  '<em>'
                    . $objLanguage->languageText("mod_webpresent_notags", "webpresent")
                    . ' </em>';
                } else {
                    $divider = '';
                    foreach ($tags as $tag) {
                        $tagLink = new link ($this->uri(array('action'=>'tag', 'tag'=>$tag['tag'])));
                        $tagLink->link = $tag['tag'];
                        $tagsStr .=  $divider.$tagLink->show();
                        $divider = ', ';
                    }
                }

                $fileTypes = array('odp'=>'OpenOffice Impress Presentation', 'ppt'=>'PowerPoint Presentation', 'pdf'=>'PDF Document');
                $objFileIcons = $this->getObject('fileicons', 'files');
                $uploaderLink = new link ($this->uri(array('action'=>'byuser', 'userid'=>$file['creatorid'])));
                $uploaderLink->link = $objUser->fullname($file['creatorid']);


                $objDisplayLicense = $this->getObject('displaylicense', 'creativecommons');
                $objDisplayLicense->icontype = 'small';
                $license = ($file['cclicense'] == '' ? 'copyright' : $file['cclicense']);
                '<p>'.$objDisplayLicense->show($license).'</p>';

                $row.=$this->createCell(
                    $columnDiv,
                    $filename,
                    $fileLink,
                    $desc,
                    $tagsStr,
                    $uploaderLink->show(),
                   '<p>'.$objDisplayLicense->show($license).'</p>',
                   $file['id']
                );

                $column++;
                $counter++;
                if($column >1 || count($latestFiles) ==1){
                    $row.='</div>';
                    $row.='</div>';
                    $content .=$row;
                    $row='<div class="sectionstats">';
                    $row.='<div class="subcolumns">';
                    $column=0;
                }
            }

            return $title.$content;
        }
    }

}

?>
