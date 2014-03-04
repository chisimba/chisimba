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

class viewerutils extends object {

    /**
     * Constructor
     */
    public function init() {
        $this->objUser = $this->getObject('user', 'security');
        $this->objConfig = $this->getObject('altconfig', 'config');
        $this->objMediaFileData = $this->getObject('dbmediafiledata');
        $this->objFolderPerms = $this->getObject('dbfolderpermissions');
        $this->objLanguage = & $this->getObject('language', 'language');
        $this->objDateTime = & $this->getObject('dateandtime', 'utilities');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->userId = $this->objUser->userId();
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $this->baseDir = $this->objSysConfig->getValue('FILES_DIR', 'podcaster');
        $this->siteBase = $this->objAltConfig->getitem('KEWL_SITEROOT_PATH');
        $this->siteUrl = $this->objAltConfig->getSitePath();
        $this->objFileIcons = $this->getObject('fileicons', 'files');
    }

    /**
     * Function that validates emails
     * @param string $email
     * @return boolean
     */
    public function isValidEmail($email) {
        $result = ereg("^[^@ ]+@[^@ ]+\.[^@ \.]+$", $email);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * checks for user agent whether this is firefox or not
     * @param void
     * @return bool
     * @author svetoslavm##gmail.com
     * @link http://devquickref.com/
     */
    public function is_firefox() {
        $agent = '';
        // old php user agent can be found here
        if (!empty($HTTP_USER_AGENT))
            $agent = $HTTP_USER_AGENT;
        // newer versions of php do have useragent here.
        if (empty($agent) && !empty($_SERVER["HTTP_USER_AGENT"]))
            $agent = $_SERVER["HTTP_USER_AGENT"];
        if (!empty($agent) && preg_match("/firefox/si", $agent))
            return true;
        return false;
    }

    /**
     * Function that validates emails
     * @param array $emails
     * @return string
     */
    public function validateEmails($emails) {
        $delimiter = "";
        $newemailstring = "";
        foreach ($emails as $email) {
            if ($this->isValidEmail($email)) {
                $newemailstring .= $delimiter . $email;
                $delimiter = ",";
                $email = "";
            }
        }
        return $newemailstring;
    }

    /**
     * Function to format file file-size
     * @param string $size
     * @param string $display_bytes
     * @return string 
     */
    public function format_file_fize_size($size, $display_bytes=false) {
        if ($size < 1024)
            $filesize = $size . ' bytes';
        elseif ($size >= 1024 && $size < 1048576)
            $filesize = round($size / 1024, 2) . ' KB';

        elseif ($size >= 1048576)
            $filesize = round($size / 1048576, 2) . ' MB';

        if ($size >= 1024 && $display_bytes)
            $filesize = $filesize . ' (' . $size . ' bytes)';

        return $filesize;
    }

    public function getPodcastView($id) {
        $objTrim = $this->getObject('trimstr', 'strings');

        $objFile = $this->getObject('dbfile', 'filemanager');

        $objSoundPlayer = $this->newObject('buildsoundplayer', 'files');

        $result = $this->objMediaFileData->getFile($id);
        //$pathdata = $result;

        $filename = $result['filename'];
        //Get the path
        $pathdata = $this->objFolderPerms->getById($result['uploadpathid']);
        $pathdata = $pathdata[0];
        $newpodpath = str_replace($this->siteBase, "/", $this->baseDir);
        $newpodpath = $newpodpath . "/" . $result['creatorid'] . "/" . $pathdata['folderpath'] . '/' . $filename;
        $newpodpath = str_replace("//", "/", $newpodpath);

        $podpath = $this->baseDir . "/" . $result['creatorid'] . "/" . $pathdata['folderpath'] . '/' . $filename;
        $podpath = str_replace("//", "/", $podpath);

        $filepath = $podpath;
        $filesize = filesize($filepath);
        $filesize = $this->format_file_fize_size($filesize);
        $newpodpath = ltrim($newpodpath, '/');
        $soundFile = str_replace(' ', '%20', $newpodpath);

        $objSoundPlayer->setSoundFile($soundFile);
        $firefoxCheck = $this->is_firefox();
        //Create podcast url
        $podURL = $this->siteUrl . $newpodpath;
        
        //If using firefox use soundplayer, else just embed
        if ($firefoxCheck) {
            $podInfo = $objSoundPlayer->show();
        } else {
            $podInfo = '<embed src="' . $podURL . '" autostart="true" loop="false" width="400" controller="true" bgcolor="#FFFFFF"></embed>';
        }
        $content = "";

        $table = $this->newObject('htmltable', 'htmlelements');
        $table->startRow();
        $fullName = $this->objUser->fullname($result['creatorid']);
        if ($result['artist'] == '') {
            $artist = $fullName;
        } else {
            $artist = $result['artist'];
        }
        $altText = $artist . ' - ' . $this->objLanguage->languageText("mod_podcaster_latestpodcasts", "podcaster", 'Latest podcasts');
        //Add RSS Link to follow author
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objIcon->setIcon('rss');
        $objIcon->alt = $altText;


        $rssLink = new link($this->uri(array('action' => 'addauthorfeed', 'author' => $artist)));
        $rssLink->link = $objIcon->show();
        $rssLink = ' ' . $rssLink->show();
        if (isset($id)) {
            $table->addCell('<strong>' . $this->objLanguage->languageText("mod_podcaster_author", "podcaster", 'Author') . ':</strong> ' . $artist, '50%');
        } else {
            $authorLink = new link($this->uri(array('action' => 'byuser', 'id' => $this->objUser->userName($result['creatorid']))));
            $authorLink->link = $artist;
            $table->addCell('<strong>' . $this->objLanguage->languageText("mod_podcaster_uploadedby", "podcaster", 'Uploaded by') . ':</strong> ' . $authorLink->show(), '50%');
        }
        $table->addCell('<strong>' . $this->objLanguage->languageText('mod_podcaster_date', 'podcaster', 'Date') . ':</strong> ' . $this->objDateTime->formatDate($result['datecreated']), '50%');
        $table->endRow();
        $table->startRow();
        $table->addCell('<strong>' . $this->objLanguage->languageText('phrase_filesize', 'system') . ':</strong> ' . $filesize, '50%');
        $playtime = $this->objDateTime->secondsToTime($result['playtime']);
        $playtime = ($playtime == '0:0') ? '<em>Unknown</em>' : $playtime;
        $table->addCell('<strong>' . $this->objLanguage->languageText('mod_podcaster_playtime', 'podcaster', 'Play time') . ':</strong> ' . $playtime, '50%');
        $table->endRow();
        $table->startRow();
        $table->addCell($rssLink . " " . $artist, '50%');
        $table->addCell(" ", '50%');
        $table->endRow();

        //Get the license
        $objDisplayLicense = $this->getObject('displaylicense', 'creativecommons');
        $objDisplayLicense->icontype = 'big';

        $license = ($result['cclicense'] == '' ? 'copyright' : $result['cclicense']);

        $license = $objDisplayLicense->show($license);

        $content .= $table->show();
        $fileurl = $this->siteUrl . $newpodpath;

        $fileurl = str_replace("//", "/", $fileurl);

        $downloadLink = new link($fileurl);
        $downloadLink->link = htmlentities($filename);

        $link = new link($this->uri(array('action' => 'download', 'id' => $id)));
        $link->link = $this->objFileIcons->getExtensionIcon($result['format']) . ' ' . $result['title'];
        $linkEmbed = new link($this->uri(array('action' => 'viewembed', 'id' => $id)));
        $embedCode = $this->objLanguage->languageText("mod_podcaster_embedcode", "podcaster", 'Embed code');
        $content .= '<br /><p>' . $podInfo . " " . $license . '</p><p><strong>' . $this->objLanguage->languageText('mod_podcaster_downloadpodcast', 'podcaster', 'Download podcast') . ': ' . $link->show() .
                '</strong> (' . $this->objLanguage->languageText('mod_podcast_rightclickandchoose', 'podcast', 'Right Click, and choose Save As') . ') ' . '</p>'.
                '<br /><p><b>'.$embedCode.': </b></p><p><textarea name="snippet" id="input_snippet" rows="6" cols="60"><embed src="' . $linkEmbed->show() . '" autostart="true" loop="false" width="500" height="42" controller="true" bgcolor="#FFFFFF"></embed></textarea></p>';

        return array('podinfo' => $content, 'filename' => $filename, 'filedata' => $result, 'id' => $id, 'podpath' => $newpodpath);
    }

    public function getLatestUpload() {
        $objFiles = $this->getObject('dbpodcasterfiles');
        $objView = $this->getObject("viewer", "podcaster");

        $filename = '';
        $latestFile = $objFiles->getLatestPresentation();
        $preview = '';
        $fileStr = '';
        if (count($latestFile) == 0) {
            $latestFileContent = '';
        } else {
            $latestFileContent = '';
            $objTrim = $this->getObject('trimstr', 'strings');

            $objTrim = $this->getObject('trimstr', 'strings');

            $counter = 0;

            foreach ($latestFile as $file) {

                if (trim($file['title']) == '') {
                    $filename = $file['filename'];
                } else {
                    $filename = htmlentities($file['title']);
                }

                $link = new link($this->uri(array('action' => 'view', 'id' => $file['id'])));
                $link->link = $objView->getPresentationFirstSlide($file['id'], $filename);
                $preview = $link->show();
                $linkname = $objTrim->strTrim($filename, 45);
                $fileLink = new link($this->uri(array('action' => 'view', 'id' => $file['id'])));
                $fileLink->link = $linkname;
                $fileLink->title = $filename;
                $fileStr = $fileLink->show();
            }
        }
        $objLanguage = $this->getObject('language', 'language');
        $featuredPresentationsStr = $objLanguage->languageText("mod_podcaster_featuredpresentation", "podcaster");

        $str = '<div id="sidebar" class="c41r">
                   <div class="statstabs">
                   <div class="statslistcontainer">

                   <ul class="paneltabs">
                   <li><a href="javascript:void(0);" class="selected">' . $featuredPresentationsStr . '</a></li>
                   </ul>

                   <ul class="statslist">
                     <li>' . $fileStr . '</li>
                    ' . $preview . '
                   </ul>

                   </div>
                   </div>
                   </div>';
        return $str;
    }

    public function getMostViewed() {
        $objStats = $this->getObject('dbpodcasterviewcounter');
        $list = $objStats->getMostViewedList();

        $objLanguage = $this->getObject('language', 'language');
        $statisticStr = $objLanguage->languageText("mod_podcaster_statistics", "podcaster");
        $mostViewedStr = $objLanguage->languageText("mod_podcaster_mostviewed", "podcaster");
        $str = '<div class="c15r">
           <div class="subcr">
           <div class="tower">
           <font style="font-size:13pt;color:#5e6eb5;">

          ' . $mostViewedStr . '

           </font>
           <p>
           <ul class="statslist">
           ' . $list . '
           </ul>
          </p>
          </div>

          </div>
         </div>

';
        return $str;
    }

    public function getMostDownloaded() {
        $objStats = $this->getObject('dbpodcasterdownloadcounter');
        $list = $objStats->getMostDownloadedList();
        $objLanguage = $this->getObject('language', 'language');
        $mostDownloadedStr = $objLanguage->languageText("mod_podcaster_mostdownloaded", "podcaster");
        $str = '<div class="c15r">
           <div class="subcr">

           <div class="tower">
           <font style="font-size:13pt;color:#5e6eb5;">
            ' . $mostDownloadedStr . '
           </font>
           <p>
           <ul class="statslist">
           ' . $list . '
           </ul>
          </p>
          </div>

          </div>
         </div>

';
        return $str;
    }

    public function getMostUploaded() {
        $objStats = $this->getObject('dbpodcasteruploadscounter');
        $list = $objStats->getMostUploadedList();
        $objLanguage = $this->getObject('language', 'language');
        $mostUploadsStr = $objLanguage->languageText("mod_podcaster_mostuploaded", "podcaster");

        $str = '<div class="c15r">
           <div class="subcr">

           <div class="tower">
           <font style="font-size:13pt;color:#0091B9;">
            ' . $mostUploadsStr . '
           </font>
           <p>
           <ul class="statslist">
           <li>' . $list . '</li>
           </ul>
          </p>
          </div>

          </div>
         </div>

';
        return $str;
    }

    public function getTagCloudContent($tagCloud) {
        $objLanguage = $this->getObject('language', 'language');
        $aboutStr = $objLanguage->languageText("mod_podcaster_aboutstr", "podcaster");
        $aboutWord = $objLanguage->languageText("mod_podcaster_aboutword", "podcaster");
        $cloud = '<div id="sidebar" class="c41r">
                   <div class="statstabs">
                   <div class="statslistcontainer">

                   <ul class="paneltabs">
                  <font style="font-size:13pt;color:#5e6eb5;">' . $aboutWord . '</font>
                   </ul>
                   <br/>
                   <p>
                   ' . $aboutStr . '
                   </p>
                   <ul class="paneltabs">
                  <font style="font-size:13pt;color:#5e6eb5;">Tags</font>
                   </ul>
                   <ul class="statslist">
                   <li>' . $tagCloud . '</li>

                   </ul>
                   </div>
                   </div>
                   </div>';
        return $cloud;
    }

    public function getFeatured() {
        $objFiles = $this->getObject('dbpodcasterfiles');

        $objView = $this->getObject("viewer", "podcaster");
        $this->loadClass('link', 'htmlelements');
        $filename = '';

        $latestFile = $objFiles->getLatestPodcasts();

        $preview = '';
        $fileStr = '';
        if (count($latestFile) == 0) {
            $latestFileContent = '';
        } else {
            $latestFileContent = '';
            $objTrim = $this->getObject('trimstr', 'strings');

            $objTrim = $this->getObject('trimstr', 'strings');

            $counter = 0;

            foreach ($latestFile as $filedata) {

                $file = $this->objMediaFileData->getFileByFileId($filedata['id']);
                if (trim($file['title']) == '') {
                    $filename = $file['filename'];
                } else {
                    $filename = htmlentities($file['title']);
                }

                $link = new link($this->uri(array('action' => 'view', 'id' => $filedata['id'])));

                //Return title instead of nopreview message
                $thumbnail = $objView->getPodcastThumbnail($file['id'], $filename);
                if ($thumbnail == 'No preview available') {
                    $thumbnail = $filename;
                }

                $link->link = $thumbnail;
                $preview = $link->show();
                $linkname = $objTrim->strTrim($filename, 45);
                $fileLink = new link($this->uri(array('action' => 'view', 'id' => $filedata['id'])));
                $fileLink->link = $linkname;
                $fileLink->title = $filename;
                $fileStr = $fileLink->show();
            }
        }
        return $preview;
    }

    private function createCell($colType, $filename, $thumbNail, $tags, $uploader, $licence, $id) {
        $objTrim = $this->getObject('trimstr', 'strings');
        $str = '<div class="' . $colType . '">
              <div class="subcl">
              <div class="sectionstats_content">

              <div class="statslistcontainer">

              <ul class="statslist">

              <li class="sectionstats_first">

              ' . $thumbNail->show() . '
   <br/><br/><br/><br/>
              </li>

              <li><strong>Tags: </strong><a  href="#">' . $tags . '</a></li>
              <li><strong>' . $this->objLanguage->languageText("mod_podcaster_author", "podcaster", 'Author') . ': </strong>' . $uploader . '</li>
              ' . $licence . '
              </ul>
 <div class="clear"></div>

              </div>
              </div>
              </div>
              </div>';
        return $str;
    }

    public function getLatestUploads() {
        $objLanguage = $this->getObject('language', 'language');
        $this->loadClass('link', 'htmlelements');
        $objFiles = $this->getObject('dbpodcasterfiles');
        $objIcon = $this->newObject('geticon', 'htmlelements');
        $objUser = $this->getObject('user', 'security');
        $objTags = $this->getObject('dbpodcastertags');
        $latestFiles = $this->objMediaFileData->getLatestAccessiblePodcasts();

        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $latest10Desc = $objLanguage->languageText("mod_podcaster_latest10desc", "podcaster");
        $latest10Str = $objLanguage->languageText("mod_podcaster_latest10str", "podcaster");
        $homepagetitle = $objSysConfig->getValue('HOME_PAGE_TITLE', 'podcaster');

        if (count($latestFiles) == 0) {
            $latestFilesContent = '';
            if ($this->userId == Null) {
                $msg = "<h1>" . $homepagetitle . "</h1><h3>" . $objLanguage->languageText("mod_podcaster_nopublic", "podcaster", "No public podcasts have been published yet") . "</h3>";
            } else {
                $msg = "<h1>" . $homepagetitle . "</h1><h3>" . $objLanguage->languageText("mod_podcaster_noopenorpublic", "podcaster", "No open or public podcasts have been published yet") . "</h3>";
            }
            return $msg;
        } else {
            $latestFilesContent = '';

            $objTrim = $this->getObject('trimstr', 'strings');
            $content = '';
            $counter = 0;
            $altText = $this->objLanguage->languageText("mod_podcaster_latestpodcasts", "podcaster", 'Latest podcasts');
            //Add RSS Link
            $objIcon = $this->newObject('geticon', 'htmlelements');
            $objIcon->setIcon('rss');
            $objIcon->alt = $altText;


            $rssLink = new link($this->uri(array('action' => 'getlatestfeeds')));
            $rssLink->link = $objIcon->show();
            $rssLink = ' ' . $rssLink->show();
            //Append RSS icon to the heading
            $homepagetitle = $homepagetitle . $rssLink;
            $title = '
           
           <h1>' . $homepagetitle . '</h1>
           
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
            $row = '<div class="sectionstats">';
            $row.='<div class="subcolumns">';
            $column = 0;


            foreach ($latestFiles as $filedata) {

                if (trim($filedata['title']) == '') {
                    $filename = $filedata['filename'];
                } else {
                    $filename = htmlentities($filedata['title']);
                }
                $linkname = $objTrim->strTrim($filename, 45);
                $extra = '';
                $columnDiv = '';

                if ($column == 0) {
                    $columnDiv = 'c50l';
                } else {
                    $columnDiv = 'c50r';
                }
                $fileLink = new link($this->uri(array('action' => 'view', 'id' => $filedata['id'])));
                //Return title instead of nopreview message
                $thumbnail = $objFiles->getPodcastThumbnail($filedata['id']);
                if ($thumbnail == 'No preview available') {
                    $thumbnail = $filename;
                }
                $fileLink->link = $thumbnail;
                $fileLink->title = $filename;

                $tags = $objTags->getTags($filedata['id']);
                $tagsStr = '';
                if (count($tags) == 0) {
                    $tagsStr .= '<em>'
                            . $objLanguage->languageText("mod_podcaster_notags", "podcaster")
                            . ' </em>';
                } else {
                    $divider = '';
                    foreach ($tags as $tag) {
                        $tagLink = new link($this->uri(array('action' => 'tag', 'tag' => $tag['tag'])));
                        $tagLink->link = $tag['tag'];
                        $tagsStr .= $divider . $tagLink->show();
                        $divider = ', ';
                    }
                }

                $fileTypes = array('mp3' => 'mp3');
                $objFileIcons = $this->getObject('fileicons', 'files');
                $uploaderLink = new link($this->uri(array('action' => 'byuser', 'userid' => $filedata['creatorid'])));
                $uploaderLink->link = $objUser->fullname($filedata['creatorid']);
                $theAuthor = $filedata['artist'];

                $objDisplayLicense = $this->getObject('displaylicense', 'creativecommons');
                $objDisplayLicense->icontype = 'small';
                $license = ($filedata['cclicense'] == '' ? 'copyright' : $filedata['cclicense']);
                '<p>' . $objDisplayLicense->show($license) . '</p>';

                $row.=$this->createCell(
                                $columnDiv,
                                $filename,
                                $fileLink,
                                $tagsStr,
                                $theAuthor,
                                '<p>' . $objDisplayLicense->show($license) . '</p>',
                                $filedata['id']
                );

                $column++;
                $counter++;
                if ($column > 1 || count($latestFiles) == 1) {
                    $row.='</div>';
                    $row.='</div>';
                    $content .=$row;
                    $row = '<div class="sectionstats">';
                    $row.='<div class="subcolumns">';
                    $column = 0;
                }
            }

            return $title . $content;
        }
    }

}

?>
