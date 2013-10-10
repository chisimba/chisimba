<?php

/**
 * Class to provier reusable view logic to the podcaster module
 *
 * This class takes functionality for viewing and creates reusable methods
 * based on it so that the code can be reused in different templates
 *
 * PHP version 5
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the
 * Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @category  Chisimba
 * @package   podcaster
 * @author    Derek Keats <dkeats[AT]uwc[DOT]ac[DOT]za>
 * @copyright 2007 UWC and AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: viewer_class_inc.php 14266 2009-08-09 16:00:00Z davidwaf $
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
        /**
         * Description for $GLOBALS
         * @global string $GLOBALS['kewl_entry_point_run']
         * @name   $kewl_entry_point_run
         */
        $GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * Class for rendering email messages into the
 * podcaster template
 *
 * @author Derek Keats
 * @category Chisimba
 * @package podcaster
 * @copyright AVOIR
 * @licence GNU/GPL
 *
 */
class viewer extends object {

    /**
     *
     * @var $objLanguage String object property for holding the
     * language object
     * @access private
     *
     */
    public $objLanguage;
    /**
     *
     * @var $objUser String object property for holding the
     * user object
     * @access private
     *
     */
    public $objUser;
    /**
     *
     * @var $objUser String object property for holding the
     * cobnfiguration object
     * @access private
     *
     */
    public $objConfig;
    public $objMediaFileData;
    public $objFolderPermissions;
    public $objEventUtils;
    public $objGroupAdmin;

    /**
     *
     * Standard init method
     *
     */
    public function init() {
        // Instantiate the language object.
        $this->objLanguage = $this->getObject('language', 'language');
        // Instantiate the user object.
        $this->objUser = $this->getObject("user", "security");
        // Instantiate the config object
        $this->objConfig = $this->getObject('altconfig', 'config');

        $this->objFile = $this->getObject('dbpodcasterfiles');

        $this->objMediaFileData = $this->getObject('dbmediafiledata');

        $this->objFolderPermissions = $this->getObject('dbfolderpermissions');

        $this->objEventUtils = $this->getObject('eventutils');
        
        $this->_objGroupAdmin = $this->newObject('groupadminmodel', 'groupadmin');
    }

    /**
     * Display results as table
     * @param <type> $files
     * @return <type>
     */
    public function displayAsTable($files) {
        $table = $this->newObject('htmltable', 'htmlelements');
        if (count($files) == 0 || empty($files)) {
            $table->startRow();
            $table->addCell($this->objLanguage->languageText("mod_podcaster_norecords", "podcaster", 'No records found'));
            $table->endRow();
            return $table->show();
        } else {
            

            $divider = '';

            $objDateTime = $this->getObject('dateandtime', 'utilities');
            $objDisplayLicense = $this->getObject('displaylicense', 'creativecommons');
            $objDisplayLicense->icontype = 'small';

            $counter = 0;
            $inRow = FALSE;

            //$objTrim = $this->getObject('trimstr', 'strings');

            foreach ($files as $file) {
                $counter++;

                if (($counter % 2) == 1) {
                    $table->startRow();
                }

                if (trim($file['title']) == '') {
                    $filename = $file['filename'];
                } else {
                    $filename = htmlentities($file['title']);
                }
                /*
                  //Return title instead of nopreview message
                  $thumbnail = $this->objFile->getPodcastThumbnail($file['id']);
                  if ($thumbnail == 'No preview available') {
                  //$thumbnail = $filename;
                  }

                  $link = new link($this->uri(array('action' => 'view', 'id' => $file['id'])));

                  $link->link = $thumbnail;

                  $table->addCell($link->show());
                  $table->addCell('&nbsp;');
                 */
                $rightContent = '';
                $link = new link($this->uri(array('action' => 'view', 'id' => $file['id'])));
                $link->link = $filename;
                $rightContent .= '<p><strong>' . $link->show() . '</strong><br />';

                if (trim($file['description']) == '') {
                    $description = '<em>' . $this->objLanguage->languageText("mod_podcaster_filehasnodesc", "podcaster") . '</em>';
                } else {
                    //$description = nl2br(htmlentities($objTrim->strTrim($file['description'], 200)));
                    $description = $file['description'];
                }

                $rightContent .= $description . '</p>';

                // Set License to copyright if none is set
                if ($file['cclicense'] == '') {
                    $file['cclicense'] = 'copyright';
                }

                $rightContent .= '<p><strong>' . $this->objLanguage->languageText("mod_podcaster_licence", "podcaster") . ':</strong> ' . $objDisplayLicense->show($file['cclicense']) . '<br />';

                $altText = $file['artist'] . ' - ' . $this->objLanguage->languageText("mod_podcaster_latestpodcasts", "podcaster", 'Latest podcasts');
                //Add RSS Link to follow author
                $objIcon = $this->newObject('geticon', 'htmlelements');
                $objIcon->setIcon('rss');
                $objIcon->alt = $altText;


                $rssLink = new link($this->uri(array('action' => 'addauthorfeed', 'author' => $file['artist'])));
                $rssLink->link = $objIcon->show();
                $rssLink = ' ' . $rssLink->show();

                $rightContent .= '<strong>' . $this->objLanguage->languageText("mod_podcaster_author", "podcaster") . ':</strong> ' . $file['artist'] . '<br />';
                $rightContent .= '<strong>' . $file['artist'] . '</strong> ' . $rssLink . '<br />';
                $rightContent .= '<strong>' . $this->objLanguage->languageText("mod_podcaster_dateuploaded", "podcaster") . ':</strong> ' . $objDateTime->formatDateOnly($file['datecreated']) . " - " . $objDateTime->formatTime($file['timecreated']) . '</p>';

                $table->addCell($rightContent);


                if (($counter % 2) == 0) {
                    $table->endRow();
                } else {
                    $table->addCell('&nbsp;');
                }

                $divider = 'addrow';
            }

            if (($counter % 2) == 1) {
                $table->addCell('&nbsp;');
                $table->addCell('&nbsp;');
                $table->addCell('&nbsp;');
                $table->endRow();
            }

            return $table->show();
        }
    }

    /**
     * Display podcasts  in a table for adding to an event
     * @param <type> $files
     * @return <type>
     */
    public function addUserPodcastsToEvent($files, $groupId) {
        $this->loadClass('checkbox', 'htmlelements');
        if (count($files) == 0) {
            return '';
        } else {
            $table = $this->newObject('htmltable', 'htmlelements');

            $divider = '';

            $objDateTime = $this->getObject('dateandtime', 'utilities');
            $objDisplayLicense = $this->getObject('displaylicense', 'creativecommons');
            $objDisplayLicense->icontype = 'small';

            $counter = 0;
            $inRow = FALSE;

            //$objTrim = $this->getObject('trimstr', 'strings');
            //Add table heading
            $table->startRow();
            $table->addCell('<strong>' . $this->objLanguage->languageText("mod_podcaster_title", "podcaster", "Title") . '</strong> ');
            $table->addCell('<strong>' . $this->objLanguage->languageText("mod_podcaster_author", "podcaster", "Author") . '</strong> ');
            $table->addCell('<strong>' . $this->objLanguage->languageText("mod_podcaster_dateuploaded", "podcaster", "Date uploaded") . '</strong> ');
            $table->addCell('<strong>' . $this->objLanguage->languageText("mod_podcaster_select", "podcaster", "Select") . '</strong> ');
            $table->endRow();

            foreach ($files as $file) {
                $counter++;

                if (trim($file['title']) == '') {
                    $filename = $file['filename'];
                } else {
                    $filename = htmlentities($file['title']);
                }

                //Return title instead of nopreview message
                $thumbnail = $this->objFile->getPodcastThumbnail($file['id']);
                if ($thumbnail == 'No preview available') {
                    //$thumbnail = $filename;
                }

                $link = new link($this->uri(array('action' => 'view', 'id' => $file['id'])));
                $link->link = $filename;
                //Add checkbox to help in identifying podcasts to add to event
                $objCheck = new checkbox('fileid[]');
                $objCheck->value = $file['id'];
                //If already part of event, set as checked
                $isMember = $this->objEventUtils->checkIfExists($file['id'], $groupId);
                if ($isMember) {
                    $objCheck->ischecked = true;
                } else {
                    $objCheck->ischecked = false;
                }

                $table->startRow();
                $table->addCell('<strong>' . $link->show() . '</strong>');
                $table->addCell($file['artist']);
                $table->addCell($objDateTime->formatDateOnly($file['datecreated']));
                $table->addCell($objCheck->show());
                $table->endRow();
                $divider = 'addrow';
            }

            if (($counter % 2) == 1) {
                $table->addCell('&nbsp;');
                $table->addCell('&nbsp;');
                $table->addCell('&nbsp;');
                $table->endRow();
            }

            return $table->show();
        }
    }

    /**
     * Get latest feeds
     * @return <type>
     */
    public function getLatestFeed() {
        $title = $this->objLanguage->languageText("mod_podcaster_latestpodcasts", "podcaster", 'Latest podcasts');
        $description = $this->objConfig->getSiteName() . ' ' . $this->objLanguage->languageText("mod_podcaster_latestpodcasts", "podcaster", 'Latest podcasts');
        ;
        $url = $this->uri(array('action' => 'latestrssfeed'));
        $files = $this->objMediaFileData->getLatestPodcasts();
        return $this->generateLatestFeed($title, $description, $url, $files);
    }

    /**
     * Get user feed
     * @param string $author
     * @return object
     */
    public function getUserFeed($author) {
        $title = $author . ' - ' . $this->objLanguage->languageText("mod_podcaster_latestpodcasts", "podcaster", 'Latest podcasts');
        $description = $this->objLanguage->languageText("mod_podcaster_phraselistby", "podcaster", "A List of the Latest podcasts by") . ' ' . $author;
        $url = $this->uri(array('action' => 'addauthorfeed', 'author' => $author));

        $files = $this->objMediaFileData->getLatestAuthorPodcasts($author);
        $author = stripslashes($author);
        return $this->generateAuthorLatestFeed($title, $description, $url, $files, $author);
    }

    /**
     * Get event feed
     * @param string $groupId
     * @return object
     */
    public function getEventFeed($groupId) {
        $groupName = $this->_objGroupAdmin->getName($groupId);
        $groupName = explode("^^", $groupName);
        $groupName = $groupName[1];
        
        //Get uploader name        
        $wordPodcasts = $this->objLanguage->languageText("mod_podcaster_podcasts", "podcaster", 'Podcasts');
        $title = $groupName . ' - ' . $wordPodcasts;
        
        $description = $groupName." - ".$this->objLanguage->languageText("mod_podcaster_latestpodcasts", "podcaster", "Latest podcasts");
        $url = $this->uri(array('action' => 'eventrss', 'groupId' => $groupId));

        $files = $this->objEventUtils->getEventPodcasts($groupId,'datecreated_desc');
        
        return $this->generateAuthorLatestFeed($title, $description, $url, $files, $groupName);
    }

    /**
     * Get user feed
     * @param string $userId
     * @return object
     */
    public function getOwnFeed($userId) {
        $ownerName = $this->objUser->getUserDetails($userId);
        $title = $ownerName['surname'] . ' - ' . $this->objLanguage->languageText("mod_podcaster_latestpodcasts", "podcaster", 'Latest podcasts');
        $description = $this->objLanguage->languageText("mod_podcaster_phraselistby", "podcaster", "A List of the Latest podcasts by") . ' ' . $author;
        $url = $this->uri(array('action' => 'addownfeed', 'userId' => $userId));

        $files = $this->objMediaFileData->getAllAuthorPodcasts($userId);
        return $this->generateAuthorLatestFeed($title, $description, $url, $files, $userId);
    }

    /**
     * Get folder feeds
     * @param string $folderId
     * @return object
     */
    public function getFolderFeed($folderId) {
        //Get folder name
        $folderData = $this->objFolderPermissions->getById($folderId);
        $path = $folderData[0]['folderpath'];
        $directPath = explode("/", $path);
        //Get the last item in array
        $directFolder = end($directPath);
        $title = $directFolder . ' - ' . $this->objLanguage->languageText("mod_podcaster_latestpodcasts", "podcaster", 'Latest podcasts');
        $description = $this->objLanguage->languageText("mod_podcaster_podcastinfolder", "podcaster", "List of podcasts within folder") . ' ' . $directFolder;
        $url = $this->uri(array('action' => 'viewfolderfeed', 'id' => $folderId));

        $files = $this->objMediaFileData->getLatestFolderPodcasts($folderId);
        return $this->generateFolderLatestFeed($title, $description, $url, $files, $directFolder);
    }

    /**
     * Get Tag Feed
     * @param <type> $tag
     * @return <type>
     */
    public function getTagFeed($tag) {
        $title = $this->objLanguage->languageText("mod_podcaster_podcast", "podcaster", 'Podcast') . ' ' . $this->objLanguage->languageText("mod_podcaster_tag", "podcaster", 'Tag') . ': ' . $tag;
        $description = $this->objLanguage->languageText("mod_podcaster_tag", "podcaster", 'List of podcasts with the tag') . ' - ' . $tag;
        $url = $this->uri(array('action' => 'tagrss', 'tag' => $tag));

        $objTags = $this->getObject('dbpodcastertags');
        $files = $objTags->getFilesWithTag($tag);

        return $this->generateTagsFeed($title, $description, $url, $files);
    }

    /**
     * Generate Feed
     * @param <type> $title
     * @param <type> $description
     * @param <type> $url
     * @param <type> $files
     * @return <type>
     */
    public function generateLatestFeed($title, $description, $url, $files) {
        $objFeedCreator = $this->getObject('feeder');
        $objFeedCreator->setupFeed(TRUE, $title, $description, $url, $url);

        if (count($files) > 0) {
            $this->loadClass('link', 'htmlelements');
            $objDate = $this->getObject('dateandtime', 'utilities');
            foreach ($files as $file) {

                if (trim($file['title']) == '') {
                    $filename = $file['filename'];
                } else {
                    $filename = htmlentities($file['title']);
                }

                $link = str_replace('&amp;', '&', $this->uri(array('action' => 'view', 'id' => $file['id'])));

                $imgLink = new link($link);
                $imgLink->link = $this->objFile->getPodcastThumbnail($file['id'], $filename);

                $date = $file['datecreated'] . " " . $file['timecreated'];

                $date = $objDate->sqlToUnixTime($date);

                $objFeedCreator->addItem($filename, $link, $imgLink->show() . '<br />' . nl2br($file['description']), 'here', $this->objUser->fullName($file['creatorid']), $date);
            }
        }

        return $objFeedCreator->output("RSS2.0", "latestfeed.xml");
    }

    /**
     * Generate author latest feeds
     * @param string $title
     * @param string $description
     * @param string $url
     * @param string $files
     * @param string $author
     * @return object
     */
    public function generateAuthorLatestFeed($title, $description, $url, $files, $author) {
        $objFeedCreator = $this->getObject('feeder');
        $objFeedCreator->setupFeed(TRUE, $title, $description, $url, $url);

        if (count($files) > 0) {
            $this->loadClass('link', 'htmlelements');
            $objDate = $this->getObject('dateandtime', 'utilities');
            foreach ($files as $file) {

                if (trim($file['title']) == '') {
                    $filename = $file['filename'];
                } else {
                    $filename = htmlentities($file['title']);
                }

                $link = str_replace('&amp;', '&', $this->uri(array('action' => 'view', 'id' => $file['id'])));

                $imgLink = new link($link);
                $imgLink->link = $this->objFile->getPodcastThumbnail($file['id'], $filename);

                $date = $file['datecreated'] . " " . $file['timecreated'];

                $date = $objDate->sqlToUnixTime($date);

                $objFeedCreator->addItem($filename, $link, $imgLink->show() . '<br />' . nl2br($file['description']), 'here', $this->objUser->fullName($file['creatorid']), $date);
            }
        }

        $author = str_replace("/", " ", $author);
        $fileName = $author . "-" . "latestfeed.xml";

        return $objFeedCreator->output("RSS2.0", $fileName);
    }

    /**
     * Generate folder latest feeds
     * @param string $title
     * @param string $description
     * @param string $url
     * @param string $files
     * @param string $author
     * @return object
     */
    public function generateFolderLatestFeed($title, $description, $url, $files, $folder) {
        $objFeedCreator = $this->getObject('feeder');
        $objFeedCreator->setupFeed(TRUE, $title, $description, $url, $url);

        if (count($files) > 0) {
            $this->loadClass('link', 'htmlelements');
            $objDate = $this->getObject('dateandtime', 'utilities');
            foreach ($files as $file) {

                if (trim($file['title']) == '') {
                    $filename = $file['filename'];
                } else {
                    $filename = htmlentities($file['title']);
                }

                $link = str_replace('&amp;', '&', $this->uri(array('action' => 'view', 'id' => $file['id'])));

                $imgLink = new link($link);
                $imgLink->link = $this->objFile->getPodcastThumbnail($file['id'], $filename);

                $date = $file['datecreated'] . " " . $file['timecreated'];

                $date = $objDate->sqlToUnixTime($date);

                $objFeedCreator->addItem($filename, $link, $imgLink->show() . '<br />' . nl2br($file['description']), 'here', $this->objUser->fullName($file['creatorid']), $date);
            }
        }

        $fileName = $folder . "-" . "folderlatestfeed.xml";

        return $objFeedCreator->output("RSS2.0", $fileName);
    }

    /**
     * Generate Feed
     * @param <type> $title
     * @param <type> $description
     * @param <type> $url
     * @param <type> $files
     * @return <type>
     */
    public function generateTagsFeed($title, $description, $url, $files) {
        $objFeedCreator = $this->getObject('feeder');
        $objFeedCreator->setupFeed(TRUE, $title, $description, $url, $url);

        if (count($files) > 0) {
            $this->loadClass('link', 'htmlelements');
            $objDate = $this->getObject('dateandtime', 'utilities');
            foreach ($files as $file) {

                if (trim($file['title']) == '') {
                    $filename = $file['filename'];
                } else {
                    $filename = htmlentities($file['title']);
                }

                $link = str_replace('&amp;', '&', $this->uri(array('action' => 'view', 'id' => $file['id'])));

                $imgLink = new link($link);
                $imgLink->link = $this->objFile->getPodcastThumbnail($file['id'], $filename);

                $date = $file['datecreated'] . " " . $file['timecreated'];

                $date = $objDate->sqlToUnixTime($date);

                $objFeedCreator->addItem($filename, $link, $imgLink->show() . '<br />' . nl2br($file['description']), 'here', $this->objUser->fullName($file['creatorid']), $date);
            }
        }

        return $objFeedCreator->output("RSS2.0", "latesttagsfeed.xml");
    }

    /**
     * Generate Feed
     * @param <type> $title
     * @param <type> $description
     * @param <type> $url
     * @param <type> $files
     * @return <type>
     */
    public function generatePodcastFeed($title, $description, $url, $file) {
        $objFeedCreator = $this->getObject('feeder');
        $objFeedCreator->setupFeed(TRUE, $title, strip_tags($description), $url, $url);

        if (count($file) > 0) {
            $this->loadClass('link', 'htmlelements');
            $objDate = $this->getObject('dateandtime', 'utilities');

            if (trim($file['title']) == '') {
                $filename = $file['filename'];
            } else {
                $filename = htmlentities($file['title']);
            }

            $link = str_replace('&amp;', '&', $this->uri(array('action' => 'view', 'id' => $file['id'])));

            $imgLink = new link($link);
            $imgLink->link = $this->objFile->getPodcastThumbnail($file['id'], $filename);

            $date = $file['datecreated'] . " " . $file['timecreated'];

            $date = $objDate->sqlToUnixTime($date);

            $objFeedCreator->addItem($filename, $link, $imgLink->show() . '<br />' . nl2br($file['description']), 'here', $this->objUser->fullName($file['creatorid']), $date);
        }

        return $objFeedCreator->output("RSS2.0", 'podcastfeed.xml');
    }

    /**
     * Generate presentation thumb nail
     * @param <type> $id
     * @param <type> $title
     * @return <type>
     */
    public function getPodcastThumbnail($id, $title='') {
        $source = $this->objConfig->getcontentBasePath() . 'podcaster_thumbnails/' . $id . '.jpg';
        $relLink = $this->objConfig->getsiteRoot() . $this->objConfig->getcontentPath() . 'podcaster_thumbnails/' . $id . '.jpg';

        if (trim($title) == '') {
            $title = '';
        } else {
            $title = ' title="' . htmlentities($title) . '" alt="' . htmlentities($title) . '"';
        }

        if (file_exists($source)) {

            return '<img src="' . $relLink . '" ' . $title . ' class="thumbnail" />';
        } else {
            $source = $this->objConfig->getcontentBasePath() . 'podcaster/' . $id . '/img0.jpg';
            $relLink = $this->objConfig->getcontentPath() . 'podcaster/' . $id . '/img0.jpg';

            if (file_exists($source)) {
                $objMkDir = $this->getObject('mkdir', 'files');
                $destinationDir = $this->objConfig->getcontentBasePath() . '/podcaster_thumbnails';
                $objMkDir->mkdirs($destinationDir);

                $this->objImageResize = $this->getObject('imageresize', 'files');

                $this->objImageResize->setImg($source);

                // Resize to 100x100 Maintaining Aspect Ratio
                $this->objImageResize->resize(120, 120, TRUE);

                //$this->objImageResize->show(); // Uncomment for testing purposes
                // Determine filename for file
                // If thumbnail can be created, give it a unique file name
                // Else resort to [ext].jpg - prevents clutter, other files with same type can reference this one file
                if ($this->objImageResize->canCreateFromSouce) {
                    $img = $this->objConfig->getcontentBasePath() . '/podcaster_thumbnails/' . $id . '.jpg';
                    $imgRel = $this->objConfig->getcontentPath() . '/podcaster_thumbnails/' . $id . '.jpg';
                    $this->objImageResize->store($img);

                    return '<img src="' . $imgRel . '" ' . $title . ' style="border:1px solid #000;" />';
                } else {
                    return $this->objLanguage->languageText("mod_podcaster_unabletogeneratethumbnail", "podcaster");
                }
            } else {
                return $this->objLanguage->languageText("mod_podcaster_nopreview", "podcaster");
            }
        }
    }

}

?>