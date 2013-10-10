<?php

/**
 *
 * Show thumbnails of the latest photos by a user
 *
 * Show thumbnails of the latest photos by a user who is identified using the
 * best guess method. This is a wide block that shows the photos as thumbnails
 * horizontally.
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
 * @package   photogallery
 * @author    Wesley Nitsckie <wnitsckie@uwc.ac.za>
 * @copyright 2007 Wesley Nitsckie
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
 * @see       References to other sections (if any)...
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 *
 * Show thumbnails of the latest photos by a user
 *
 * Show thumbnails of the latest photos by a user who is identified using the
 * best guess method. This is a wide block that shows the photos as thumbnails
 * horizontally.
 *
 * @author Derek Keats
 *
 *
 */
class block_latestphotos extends object
{
    /**
     * @var string $title The title of the block
     */
    public $title;


    /**
     * Description for public
     * @var    object
     * @access public
     */
    public $objLanguage;

    /**
     * Standard init function to instantiate language and user objects
     * and create title
     */
    public function init()
    {
        $this->objLanguage = &$this->getObject('language', 'language');
        $this->_objFileMan = & $this->getObject('dbfile','filemanager');
        $this->title = $this->objLanguage->languageText("mod_photogallery_block_latest", "photogallery");
    }
    /**
     * Standard block show method. It builds the output based
     * on data obtained via the getlast class
     */
    public function oldshow()
    {
        $objPhoto = $this->getObject('dbimages', 'photogallery');
        $link = $this->getObject('link','htmlelements');
        $objThumbnail = $this->getObject('thumbnails','filemanager');

        $image = $objPhoto->getRandomPhoto();

        if ($image == FALSE) {
            return '';
        } else {
        $css = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('style/default.css','photogallery').'" />';

        $this->appendArrayVar('headerParams',$css);

        $str='<div class="image"><div class="imagethumb">';
        $filename = $this->_objFileMan->getFileName($image['file_id']);
         $path = $objThumbnail->getThumbnail($image['file_id'],$filename);
         $bigPath = $this->_objFileMan->getFilePath($image['file_id']);
         $link->href = $this->uri(array('action' => 'viewimage', 'albumid' => $image['album_id'],'imageid' => $image['id']));
         $link->link = '<img title="'.$image['title'].'" src="'.$path.'" alt="'.$image['title'].'"  />';
         $link->extra = ' rel="lightbox" ';
        $str.=$link->show().'</div></div>';

            return '<center>'.$str.'</center>';
        }

    }

    public function show()
    {
        $objThumbnail = $this->getObject('thumbnails','filemanager');
        $objDbGal = $this->getObject('dbimages', 'photogallery');


        // Figure out whose profile it is.
        $objGuessUser = $this->getObject('bestguess', 'utilities');
        $userId = $objGuessUser->guessUserId();
        $photos = $objDbGal->getRecentByUser($userId);

        $imgStrip = "<div class='gallerystrip'>";
        foreach ($photos as $photo) {
            $link = $this->newObject('link','htmlelements');
            $link->href = $this->uri(array('action' => 'viewimage', 'albumid' => $photo['album_id'],'imageid' => $photo['imagecode']));
            $fileId = $photo['file_id'];
            $filename = $photo['filename'];
            $thumbNail = $objThumbnail->getThumbnail($fileId,$filename);
            $link->link = '<img class="pgthumbimage" title="' . $photo['title'] . '" src="'
              . $thumbNail .'" alt="' . $photo['title'].'"  />';
            $imgStrip .= "<div class='pgthumbnail'>" . $link->show() . "</div>";
            unset($link);
        }
        $imgStrip = $imgStrip . "</div>";
        return $imgStrip;
    }
}
?>