<?php
/**
* Class to provier reusable view logic to the webpresent module
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
* @package   webpresent
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
    * webpresent template
    *
    * @author Derek Keats
    * @category Chisimba
    * @package webpresent
    * @copyright AVOIR
    * @licence GNU/GPL
    *
    */
class viewer extends object
{
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

        /**
        *
        * Standard init method
        *
        */
    public function init()
    {
        // Instantiate the language object.
        $this->objLanguage = $this->getObject('language', 'language');
        // Instantiate the user object.
        $this->objUser = $this->getObject("user", "security");
        // Instantiate the config object
        $this->objConfig = $this->getObject('altconfig', 'config');

        $this->objFile = $this->getObject('dbwebpresentfiles');
    }

        /**
         *
         * A method to return the flash presentation for rendering in the page
         * @param string $id The file id of the flash file to show
         * @return string the flash file rendered for viewing within a div
         * @access public
         *
         */
    public function showFlash($id)
    {
        $flashFile = $this->objConfig->getcontentBasePath().'webpresent/'. $id .'/' . $id.'.swf';
        if (file_exists($flashFile)) {
            $flashFile = $this->uri(array('action'=>'getflash', 'id'=>$id));
            //$this->objConfig->getcontentPath().'webpresent/' .$id .'/'. $id.'.swf';
            $flashContent = '
             <div style="border: 1px solid #000; width: 534px; height: 402px; text-align: center;"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="540" height="400">
             <param name="movie" value="'.$flashFile.'">
             <param name="quality" value="high">
             <embed src="'.$flashFile.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="534" height="402"></embed>
            </object></div>';
        } else {
            $flashContent = '<div class="noRecordsMessage" style="border: 1px solid #000; width: 540px; height: 302px; text-align: center;">Flash Version of Presentation being converted</div>';
        }
        return $flashContent;
    }
    public function showFeaturedFlash($id)
    {
        $flashFile = $this->objConfig->getcontentBasePath().'webpresent/'. $id .'/' . $id.'.swf';
        if (file_exists($flashFile)) {
            $flashFile = $this->uri(array('action'=>'getflash', 'id'=>$id));
            //$this->objConfig->getcontentPath().'webpresent/' .$id .'/'. $id.'.swf';
            $flashContent = '
             <div style="border: 1px solid #000; width: 270px; height: 270px; text-align: center;"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="280" height="280">
             <param name="movie" value="'.$flashFile.'">
             <param name="quality" value="high">
             <embed src="'.$flashFile.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="270" height="270"></embed>
            </object></div>';
        } else {
            $flashContent = '<div class="noRecordsMessage" style="border: 1px solid #000; width: 270px; height: 270px; text-align: center;">Flash Version of Presentation being converted</div>';
        }
        return $flashContent;
    }
        /**
         *
         * A method to return the flash presentation for rendering in the page
         * @param string $uri The URL of the flash file to show
         * @return string the flash file rendered for viewing within a div
         * @access public
         *
         */
    public function showFlashUrl($uri)
    {
        $flashFile = $uri;
        $flashContent = '
           <div style="border: 1px solid #000; width: 534px; height: 402px; text-align: center;"><object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0" width="540" height="400">
           <param name="movie" value="'.$flashFile.'">
           <param name="quality" value="high">
           <embed src="'.$flashFile.'" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="534" height="402"></embed>
          </object></div>';
        return $flashContent;
    }
        /**
         * Display results as table
         * @param <type> $files
         * @return <type>
         */
    public function displayAsTable($files)
    {
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

            $objTrim = $this->getObject('trimstr', 'strings');

            foreach ($files as $file)
            {
                $counter++;

                if (($counter%2) == 1)
                {
                    $table->startRow();
                }


                $link = new link ($this->uri(array('action'=>'view', 'id'=>$file['id'])));
                $link->link = $this->objFile->getPresentationThumbnail($file['id']);

                $table->addCell($link->show(), 120);
                $table->addCell('&nbsp;', 10);

                $rightContent = '';

                if (trim($file['title']) == '') {
                    $filename = $file['filename'];
                } else {
                    $filename = htmlentities($file['title']);
                }

                $link->link = $filename;
                $rightContent .= '<p><strong>'.$link->show().'</strong><br />';

                if (trim($file['description']) == '') {
                    $description = '<em>'.$this->objLanguage->languageText("mod_webpresent_filehasnodesc", "webpresent").'</em>';
                } else {
                    $description = nl2br(htmlentities($objTrim->strTrim($file['description'], 200)));
                }

                $rightContent .= $description.'</p>';

                // Set License to copyright if none is set
                if ($file['cclicense'] == '')
                {
                    $file['cclicense'] = 'copyright';
                }

                $rightContent .= '<p><strong>'.$this->objLanguage->languageText("mod_webpresent_licence", "webpresent").':</strong> '.$objDisplayLicense->show($file['cclicense']).'<br />';

                $userLink = new link ($this->uri(array('action'=>'byuser', 'userid'=>$file['creatorid'])));
                $userLink->link = $this->objUser->fullname($file['creatorid']);

                $rightContent .= '<strong>'.$this->objLanguage->languageText("mod_webpresent_uploadedby", "webpresent").':</strong> '.$userLink->show().'<br />';
                $rightContent .= '<strong>'.$this->objLanguage->languageText("mod_webpresent_dateuploaded", "webpresent").':</strong> '.$objDateTime->formatDate($file['dateuploaded']).'</p>';

                $table->addCell($rightContent, '40%');


                if (($counter%2) == 0)
                {
                    $table->endRow();
                } else {
                    $table->addCell('&nbsp;', '20');
                }

                $divider = 'addrow';
            }

            if (($counter%2) == 1)
            {
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
    public function getLatestFeed()
    {
        $title = $this->objConfig->getSiteName().' - 10 Newest Uploads';
        $description =$this->objLanguage->languageText("mod_webpresent_listphrase", "webpresent").' '.$this->objConfig->getSiteName().' '.$this->objLanguage->languageText("mod_webpresent_site", "webpresent");
        $url = $this->uri(array('action'=>'latestrssfeed'));

        $files = $this->objFile->getLatestPresentations();

        return $this->generateFeed($title, $description, $url, $files);
    }

/**
 * Get user feed
 * @param <type> $userId
 * @return <type>
 */
    public function getUserFeed($userId)
    {
        $fullName = $this->objUser->fullName($userId);
        $title = $fullName.'\'s Files';
        $description =$this->objLanguage->languageText("mod_webpresent_phraselistuploadedby", "webpresent"). ' '.$fullName;
        $url = $this->uri(array('action'=>'userrss', 'userid'=>$userId));

        $files = $this->objFile->getByUser($userId);

        return $this->generateFeed($title, $description, $url, $files);
    }

/**
 * Get Tag Feed
 * @param <type> $tag
 * @return <type>
 */
    public function getTagFeed($tag)
    {
        $title = $this->objConfig->getSiteName().' - Tag: '.$tag;
        $description = 'A List of Presentations with tag - '.$tag;
        $url = $this->uri(array('action'=>'tagrss', 'tag'=>$tag));

        $objTags = $this->getObject('dbwebpresenttags');
        $files = $objTags->getFilesWithTag($tag);

        return $this->generateFeed($title, $description, $url, $files);
    }

/**
 * Generate Feed
 * @param <type> $title
 * @param <type> $description
 * @param <type> $url
 * @param <type> $files
 * @return <type>
 */
    public function generateFeed($title, $description, $url, $files)
    {
        $objFeedCreator = $this->getObject('feeder', 'feed');
        $objFeedCreator->setupFeed(TRUE, $title, $description, $this->objConfig->getsiteRoot(), $url);

        if (count($files) > 0)
        {
            $this->loadClass('link', 'htmlelements');
            $objDate = $this->getObject('dateandtime', 'utilities');

            foreach ($files as $file)
            {

                if (trim($file['title']) == '') {
                    $filename = $file['filename'];
                } else {
                    $filename = htmlentities($file['title']);
                }

                $link = str_replace('&amp;', '&', $this->uri(array('action'=>'view', 'id'=>$file['id'])));

                $imgLink = new link($link);
                $imgLink->link = $this->objFile->getPresentationThumbnail($file['id'], $filename);

                $date = $objDate->sqlToUnixTime($file['dateuploaded']);


                $objFeedCreator->addItem($filename, $link, $imgLink->show().'<br />'.$file['description'], 'here', $this->objUser->fullName($file['creatorid']), $date);
            }


        }

        return $objFeedCreator->output();
    }

/**
 * Generate presentation thumb nail
 * @param <type> $id
 * @param <type> $title
 * @return <type>
 */
    public function getPresentationThumbnail($id, $title='')
    {
        $source = $this->objConfig->getcontentBasePath().'webpresent_thumbnails/'.$id.'.jpg';
        $relLink = $this->objConfig->getsiteRoot().$this->objConfig->getcontentPath().'webpresent_thumbnails/'.$id.'.jpg';

        if (trim($title) == '')
        {
            $title = '';
        } else {
            $title = ' title="'.htmlentities($title).'" alt="'.htmlentities($title).'"';
        }

        if (file_exists($source)) {

            return '<img src="'.$relLink.'" '.$title.' class="thumbnail" />';
        } else {
            $source = $this->objConfig->getcontentBasePath().'webpresent/'.$id.'/img0.jpg';
            $relLink = $this->objConfig->getcontentPath().'webpresent/'.$id.'/img0.jpg';

            if (file_exists($source)) {
                $objMkDir = $this->getObject('mkdir', 'files');
                $destinationDir = $this->objConfig->getcontentBasePath().'/webpresent_thumbnails';
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
                    $img = $this->objConfig->getcontentBasePath().'/webpresent_thumbnails/'.$id.'.jpg';
                    $imgRel = $this->objConfig->getcontentPath().'/webpresent_thumbnails/'.$id.'.jpg';
                    $this->objImageResize->store($img);

                    return '<img src="'.$imgRel.'" '.$title.' style="border:1px solid #000;" />';
                } else {
                    return $this->objLanguage->languageText("mod_webpresent_unabletogeneratethumbnail", "webpresent");// '';
                }
            } else {
                return $this->objLanguage->languageText("mod_webpresent_nopreview", "webpresent");
            }
        }
    }

        /**
 * Generate presentation thumb nail
 * @param <type> $id
 * @param <type> $title
 * @return <type>
 */
    public function getPresentationFirstSlide($id, $title='')
    {

        $source = $this->objConfig->getcontentBasePath().'webpresent/'.$id.'/img0.jpg';
        $relLink = $this->objConfig->getcontentPath().'webpresent/'.$id.'/img0.jpg';

        if (file_exists($source)) {
            $objMkDir = $this->getObject('mkdir', 'files');
            $destinationDir = $this->objConfig->getcontentBasePath().'/webpresent_thumbnails';
            $objMkDir->mkdirs($destinationDir);

            $this->objImageResize = $this->getObject('imageresize', 'files');

            $this->objImageResize->setImg($source);


            $this->objImageResize->resize(300, 300, TRUE);

            //$this->objImageResize->show(); // Uncomment for testing purposes

            // Determine filename for file
            // If thumbnail can be created, give it a unique file name
            // Else resort to [ext].jpg - prevents clutter, other files with same type can reference this one file
            if ($this->objImageResize->canCreateFromSouce) {
                $img = $this->objConfig->getcontentBasePath().'/webpresent_thumbnails/'.$id.'.jpg';
                $imgRel = $this->objConfig->getcontentPath().'/webpresent_thumbnails/'.$id.'.jpg';
                $this->objImageResize->store($img);

                return '<img src="'.$imgRel.'" '.$title.' style="border:1px solid #000;" />';
            } else {
                return $this->objLanguage->languageText("mod_webpresent_unabletogeneratethumbnail", "webpresent");// '';
            }
        } else {
            return $this->objLanguage->languageText("mod_webpresent_nopreview", "webpresent");
        }
    }

}

?>