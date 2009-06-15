<?php

/**
*
* Parse string for filter for photogallery
*
* Class to parse a string (e.g. page content) that contains a filter
* code for including the all photos in an album.
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
* @package   filters
* @author    David Wafuala <Wanyonyi.Wafula@wits.ac.za>
* @copyright 2009 David Wafula
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
* @link      http://avoir.uwc.ac.za
*/
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
    $GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check


/**
 *
 * Parse string for filter for directory contents
 *
 * Class to parse a string (e.g. page content) that contains a filter
 * code for including the all files in a user directory as links with descriptions
 * where descriptions exist.
 *
 * @author Derek Keats
 *
 */
class parse4photogallery extends object
{

    /**
    *
    * @var string $objLanguage String object property for holding the
    * language object
    * @access public
    *
    */
    public $objLanguage;

    /**
    *
    * String object $objExpar is a string to hold the parameter extractor object
    * @access public
    *
    */
    public $objExpar;

    /**
     *

     * @access public
     *
     */
    public $type;

    public $filemanager;

    public $dbimages;
    /**
     * @return void
     * @access public
     *
     */
    public function init()
    {
        // Get an instance of the language object
        $this->objLanguage = $this->getObject('language', 'language');
        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
        $this->objUser = $this->getObject('user', 'security');
        $this->filemanager =  $this->getObject('dbfile','filemanager');
        $this->_objConfig = $this->getObject('altconfig', 'config');

        $objModule = $this->getObject('modules','modulecatalogue');
        //See if the mathml module is registered and set a param
        $isRegistered = $objModule->checkIfRegistered('photogallery');
        if ($isRegistered){
        $this->dbimages =  $this->getObject('dbimages', 'photogallery');
        $this->_objUtils =  $this->getObject('utils',"photogallery");
        $scripts = '<script type="text/javascript" src="'.$this->_objConfig->getModuleURI().'photogallery/resources/lightbox/js/prototype.js"></script>
<script type="text/javascript" src="'.$this->_objConfig->getModuleURI().'photogallery/resources/lightbox/js/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="'.$this->_objConfig->getModuleURI().'photogallery/resources/lightbox/js/lightbox.js"></script>
<link rel="stylesheet" href="'.$this->_objConfig->getModuleURI().'photogallery/resources/lightbox/css/lightbox.css" type="text/css" media="screen" />';
        $this->appendArrayVar('headerParams',$scripts);
        }
    }

    /**
    *
    * Method to parse the string
    * @param  string $str The string to parse
    * @return string The parsed string
    *
    */
    public function parse($txt)
    {
        $objModule = $this->getObject('modules','modulecatalogue');
        //See if the mathml module is registered and set a param
        $isRegistered = $objModule->checkIfRegistered('photogallery');
        if ($isRegistered){

        //Match filters based on a wordpress style
        preg_match_all('/\[PHOTOGALLERY\](.*)\[\/PHOTOGALLERY\]/U', $txt, $results, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[0] as $item) {
            $str = $results[1][$counter];
            $replacement = $this->getAlbum($str);
            $txt = str_replace($item, $replacement, $txt);
            $counter++;
        }
        }
        return $txt;
    }

    private function getAlbum($title){

        $dbalbum =  $this->getObject('dbalbum', 'photogallery');
        $sql="WHERE title='".$title."' ORDER BY position";
        $albums= $dbalbum->getAll($sql);
        return $this->renderAlbum($albums);
    }

    private function renderAlbum($albums){
        $str = '';
        $link = $this->getObject('link','htmlelements');
        $objThumbnail = & $this->getObject('thumbnails','filemanager');
        $dbimages =  $this->getObject('dbimages', 'photogallery');

        foreach($albums as $album)
        {
            $str .= '<div class="image">';



            $images=$dbimages->getAlbumImages($album['id']);
            $image=$images[0];


            $nav = $this->_objUtils->getImageNav($image['id']);

            $head = '<div id="main2">'.$nav.'<div id="gallerytitle">
        <h2>'.$image['title'].'</h2></div></div>

    ';
            


            $info=getimagesize($this->filemanager->getFullFilePath($image['file_id']));
            if (isset($info[0])){
                $width=$info[0];
            } else {
                $width=500;
            }
            if ($width>500){
                $width=500;
            }

            $filename = $this->filemanager->getFileName($image['file_id']);
            $path = $objThumbnail->getThumbnail($image['file_id'],$filename);
            $bigPath = $this->filemanager->getFilePath($image['file_id']);

            $link->href = $bigPath;
            $link->link = '<img title="'.$image['title'].'" src="'.$bigPath.'" alt="'.$image['title'].'" width="'.$width.'" />';
            $link->extra = ' rel="lightbox" ';
            $str.=$link->show().'</div>';

            $str.=$head;


        }

        return '<div id="albums">'. $str .'</div></div>';
    }
}
?>
